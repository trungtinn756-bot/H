<?php
require_once __DIR__ . '/../libs/auth.php';
require_once __DIR__ . '/../libs/pdo.php';
require_once __DIR__ . '/../model/ComicModel.php';
require_once __DIR__ . '/../model/ChapterModel.php';
require_once __DIR__ . '/../model/CommentModel.php';
require_once __DIR__ . '/../model/HistoryModel.php';
require_once __DIR__ . '/../model/BannerModel.php';
class HomeController
{
    private function getProjectRoot(): string
    {
        $scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $projectRoot = dirname($scriptPath);

        if ($projectRoot === '/' || $projectRoot === '\\') {
            return '';
        }

        return $projectRoot;
    }

    public function index()
    {
        // 1. Lấy từ khóa tìm kiếm và bộ lọc (Đồng bộ 'q' với giao diện)
        $search = trim((string) ($_GET['q'] ?? ''));
        $statusFilter = trim((string) ($_GET['status'] ?? ''));

        // 2. Cấu hình phân trang
        $page = max(1, (int) ($_GET['page'] ?? 1)); // Trang hiện tại, tối thiểu là 1
        $perPage = 48; // Số lượng truyện hiển thị trên mỗi trang

        // 3. Lấy tổng số truyện thỏa mãn điều kiện lọc để tính tổng số trang
        $totalComics = comic_count($search, $statusFilter);
        $totalPages = max(1, (int) ceil($totalComics / $perPage));

        // Đảm bảo trang hiện tại không vượt quá tổng số trang hợp lệ
        if ($page > $totalPages) {
            $page = $totalPages;
        }

        // Tính vị trí bắt đầu lấy dữ liệu (Offset)
        $offset = ($page - 1) * $perPage;

        // 4. Lấy danh sách truyện theo phân trang
        $comics = comic_get_all($search, $statusFilter, "c.id DESC", $perPage, $offset);

        // 🌟 BỔ SUNG: Lấy 5 bình luận mới nhất toàn cục phục vụ hiển thị trang chủ
        if (function_exists('comment_get_newest_global')) {
            $globalNewestComments = comment_get_newest_global(5);
        } else {
            $globalNewestComments = [];
        }

        // 🌟 THAY ĐỔI TẠI ĐÂY: Lấy lịch sử đọc từ Database thay vì chờ JS gọi LocalStorage
        $dbReadingHistory = [];
        if (isset($_SESSION['user']['id'])) {
            $dbReadingHistory = history_get_list_by_user($_SESSION['user']['id'], 4); // Lấy 4 truyện mới đọc dở
        }

        // ĐỌC DỮ LIỆU BANNER ĐỘNG
        $activeBanners = banner_get_active();

        $pageTitle = "MANGATIN - Website Đọc Truyện Tranh Online Miễn Phí";
        // 5. Đẩy dữ liệu ra view
        include_once __DIR__ . '/../view/client/home.php';
    }

    public function detail()
    {
        $comic_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $comicDetail = comic_get_by_id($comic_id);
        $comic = $comicDetail;

        // 🌟 BƯỚC CHUẨN HÓA: Kiểm tra truyện có tồn tại hay không TRƯỚC khi xử lý các dữ liệu khác
        if (!$comic) {
            header("HTTP/1.0 404 Not Found");
            echo "Truyện tranh không tồn tại.";
            exit;
        }

        // --- ĐOẠN CODE ĐÁNH GIÁ (Đã được chuyển xuống vị trí an toàn) ---
        require_once __DIR__ . '/../model/RatingModel.php';
        $comicIdForRating = $comicDetail['id'];

        // 1. Lấy điểm trung bình và tổng số lượt bầu chọn
        $ratingStats = rating_get_average($comicIdForRating);
        $avgRating = round($ratingStats['avg_rating'] ?? 0, 1);
        $totalVotes = $ratingStats['total_votes'] ?? 0;

        // 2. Kiểm tra xem user đăng nhập đã đánh giá chưa
        $userRating = 0;
        if (isset($_SESSION['user'])) {
            $current_user_id = $_SESSION['user']['id'];
            $checkUserRating = pdo_getOne("SELECT rating_value FROM ratings WHERE user_id = ? AND comic_id = ?", $current_user_id, $comicIdForRating);
            if ($checkUserRating) {
                $userRating = (int) $checkUserRating['rating_value'];
            }
        }

        // Tăng lượt xem cho truyện
        if (function_exists('comic_increment_view_count')) {
            comic_increment_view_count($comic_id);
        }

        // --- KIỂM TRA TRẠNG THÁI THEO DÕI ---
        $is_following = false;
        if (isset($_SESSION['user']['id'])) {
            $is_following = follow_check($_SESSION['user']['id'], $comic_id);
        }

        // --- BỔ SUNG DỮ LIỆU ĐỘNG CHO DETAIL ---
        // 1. Lấy tổng số lượt xem thực tế của truyện
        $totalViews = count_views($comic_id);

        // 2. Lấy danh sách tất cả các chương của bộ truyện (Mới nhất xếp trên cùng)
        $chapters = chapter_get_all_by_comic_id($comic_id);

        // 3. Lấy danh sách ID các thể loại thuộc về bộ truyện này
        $current_genre_ids = comic_get_genre_ids_by_comic_id($comic_id);

        // 4. Lấy thông tin chi tiết (tên, slug) của các thể loại truyện này để hiện Tag
        $comicGenres = [];
        if (!empty($current_genre_ids)) {
            $placeholders = implode(',', array_fill(0, count($current_genre_ids), '?'));
            $sqlGenres = "SELECT * FROM genres WHERE id IN ($placeholders)";
            $comicGenres = pdo_getAll($sqlGenres, ...$current_genre_ids);
        }

        // 5. Lấy danh sách truyện cùng thể loại (Sidebar) - Lấy tối đa 5 truyện
        $relatedComics = [];
        if (!empty($current_genre_ids)) {
            $placeholders = implode(',', array_fill(0, count($current_genre_ids), '?'));
            // BỔ SUNG: Tính AVG(r.rating_value) kèm làm tròn 1 chữ số thập phân
            $sqlRelated = "SELECT c.*, 
                   (SELECT MAX(ch.chapter_number) FROM chapters ch WHERE ch.comic_id = c.id) as latest_chapter,
                   ROUND(IFNULL(AVG(r.rating_value), 0), 1) as avg_rating
                   FROM comics c
                   JOIN comics_genres cg ON c.id = cg.comic_id
                   LEFT JOIN ratings r ON c.id = r.comic_id
                   WHERE cg.genre_id IN ($placeholders) AND c.id != ?
                   GROUP BY c.id
                   LIMIT 5";
            $relatedParams = array_merge($current_genre_ids, [$comic_id]);
            $relatedComics = pdo_getAll($sqlRelated, ...$relatedParams);
        }

        include_once __DIR__ . '/../view/client/detail.php';
    }

    public function reader()
    {

        $chapter_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $current_user_id = $_SESSION['user']['id'] ?? 0;
        $current_link = "index.php?action=reader&id=" . $chapter_id;

        if (function_exists('notification_mark_as_read_by_link')) {
            notification_mark_as_read_by_link($current_user_id, $current_link);
        }

        $chapter = chapter_get_by_id($chapter_id);
        if (!$chapter) {
            header("HTTP/1.0 404 Not Found");
            echo "Chương truyện không tồn tại.";
            exit;
        }

        $comic_id = $chapter['comic_id'];
        // Lấy thông tin bộ truyện của chương này để hiển thị tiêu đề và sidebar
        $comic = comic_get_by_id($comic_id);
        if (!$comic) {
            header("HTTP/1.0 404 Not Found");
            echo "Bộ truyện không tồn tại.";
            exit;
        }

        // 🌟 THAY ĐỔI TẠI ĐÂY: Nếu có user đăng nhập, lưu ngay lịch sử vào DB
        if ($current_user_id > 0) {
            history_save_location($current_user_id, $comic_id, $chapter_id);
        }

        // Tăng lượt xem cho chương
        // --- XỬ LÝ TÍNH VIEW CHƯƠNG & CHỐNG TĂNG VIEW ẢO TRONG 10 PHÚT ---
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Khởi tạo mảng lưu lịch sử các chương đã tính view trong phiên làm việc
        if (!isset($_SESSION['viewed_chapters'])) {
            $_SESSION['viewed_chapters'] = [];
        }

        $currentTime = time();
        $cooldownTime = 10 * 60; // 10 phút = 600 giây
        $should_increase_view = true;

        // Kiểm tra xem chương này đã được người dùng này xem trong vòng 10 phút qua chưa
        if (isset($_SESSION['viewed_chapters'][$chapter_id])) {
            $lastViewTime = (int) $_SESSION['viewed_chapters'][$chapter_id];
            if (($currentTime - $lastViewTime) < $cooldownTime) {
                $should_increase_view = false; // Đang trong thời gian cooldown, không tính view
            }
        }

        // Nếu hợp lệ, tiến hành cập nhật mốc thời gian mới và tăng view trong Database
        if ($should_increase_view) {
            $_SESSION['viewed_chapters'][$chapter_id] = $currentTime;
            chapter_increment_view_count_secure($chapter_id);
        }

        // --- BỔ SUNG DỮ LIỆU CHO TRANG READER ---
        // 1. Lấy tất cả ảnh trang truyện của chương này (Sắp xếp theo số thứ tự trang)
        $chapterImages = chapter_images_get_by_chapter_id($chapter_id);

        // 2. Lấy danh sách tất cả các chương (Sắp xếp từ Cũ đến Mới phục vụ việc tìm chương Kế/Trước)
        // Chúng ta lật ngược lại ASC để dễ tính toán logic tuyến tính
        $sqlAllChaps = "SELECT id, chapter_number, chapter_title FROM chapters WHERE comic_id = ? ORDER BY chapter_number ASC";
        $allChapters = pdo_getAll($sqlAllChaps, $comic_id);

        $prevChapterId = null;
        $nextChapterId = null;

        // Tìm vị trí của chương hiện tại trong danh sách để xác định chương trước/sau
        foreach ($allChapters as $index => $c) {
            if ($c['id'] == $chapter_id) {
                if (isset($allChapters[$index - 1])) {
                    $prevChapterId = $allChapters[$index - 1]['id'];
                }
                if (isset($allChapters[$index + 1])) {
                    $nextChapterId = $allChapters[$index + 1]['id'];
                }
                break;
            }
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Đảo ngược lại danh sách chương phục vụ Select Dropdown
        $dropdownChapters = array_reverse($allChapters);

        include_once __DIR__ . '/../view/client/reader.php';
    }

    public function genre()
    {
        // 1. Lấy slug của thể loại từ URL
        $slug = trim((string) ($_GET['slug'] ?? ''));
        if (empty($slug)) {
            header("Location: index.php");
            exit;
        }

        // 2. Lấy thông tin chi tiết của thể loại dựa vào slug
        $genreInfo = pdo_getOne("SELECT * FROM genres WHERE slug = ?", $slug);
        if (!$genreInfo) {
            header("HTTP/1.0 404 Not Found");
            echo "Thể loại không tồn tại.";
            exit;
        }

        // 3. Cấu hình phân trang
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 24; // Số lượng truyện mỗi trang

        // Đếm tổng số truyện thuộc thể loại này
        $totalComics = (int) pdo_getValue("SELECT COUNT(*) FROM comics_genres WHERE genre_id = ?", $genreInfo['id']);
        $totalPages = max(1, (int) ceil($totalComics / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;

        // 4. Gọi hàm Model tối ưu (Đã có sẵn đủ sub-query tính views và top 3 chaps)
        $comics = comic_get_all_by_genre((int) $genreInfo['id'], $perPage, $offset);

        // 5. Đẩy ra view hiển thị
        $pageTitle = "Thể loại: " . $genreInfo['genre_name'];
        include_once __DIR__ . '/../view/client/genre-comics.php';
    }
    public function api_search_suggestions()
    {
        $keyword = trim((string) ($_GET['q'] ?? ''));
        $suggestions = [];
        $projectRoot = $this->getProjectRoot();

        if (!empty($keyword) && strlen($keyword) >= 2) { // Chỉ tìm khi gõ từ 2 ký tự trở lên để tránh nặng Server
            // Sử dụng lại hàm chuẩn có sẵn của bạn để tìm kiếm, giới hạn lấy 6 truyện gợi ý cho nhẹ giao diện
            $suggestions = comic_get_all($keyword, '', 'c.id DESC', 6, 0);
        }

        // Thiết lập Header trả về dạng dữ liệu JSON
        header('Content-Type: application/json; charset=utf-8');

        // Chỉ map lại các trường cần thiết để chuyển cho JS xử lý nhẹ hơn
        $result = array_map(function ($comic) use ($projectRoot) {
            $thumbnail = !empty($comic['thumbnail']) ? $comic['thumbnail'] : 'default-comic.png';

            return [
                'id' => $comic['id'],
                'title' => $comic['title'],
                'other_title' => $comic['other_title'],
                'thumbnail' => $projectRoot . '/uploads/comics/' . $thumbnail,
                'link' => 'index.php?action=detail&id=' . $comic['id']
            ];
        }, $suggestions);

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
    public function search()
    {
        // 1. Lấy từ khóa tìm kiếm từ thanh URL
        $search = trim((string) ($_GET['q'] ?? ''));

        // 2. Cấu hình phân trang (Gợi ý: 24 truyện mỗi trang cho cân đối Grid)
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 24;

        // 3. Đếm tổng số truyện thỏa mãn từ khóa quét đồng bộ cả Tên chính và Tên khác
        $totalComics = comic_count($search, '');
        $totalPages = max(1, (int) ceil($totalComics / $perPage));

        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;

        // 4. Gọi hàm lấy danh sách truyện tìm được từ Model
        $comics = comic_get_all($search, '', "c.id DESC", $perPage, $offset);

        // 5. include file giao diện kết quả tìm kiếm
        include_once __DIR__ . '/../view/client/search.php';
    }
    public function filter()
    {
        $keyword = trim((string) ($_GET['keyword'] ?? ''));

        $status = trim((string) ($_GET['status'] ?? 'all'));
        $allowedStatuses = ['all', 'pause', 'ongoing', 'completed'];
        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'all';
        }

        $chapter_count = trim((string) ($_GET['chapter_count'] ?? 'all'));
        $allowedChapterCounts = ['all', '1', '2', '3', '4']; // 1: 1-10, 2: 11-50, 3: 51-100, 4: >100
        if (!in_array($chapter_count, $allowedChapterCounts, true)) {
            $chapter_count = 'all';
        }

        $sort = trim((string) ($_GET['sort'] ?? 'update_desc'));
        $allowedSorts = ['update_desc', 'new_desc', 'view_desc', 'name_asc'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'update_desc';
        }

        $selected_genres = isset($_GET['genres']) && is_array($_GET['genres']) ? array_map('intval', $_GET['genres']) : [];
        $selected_genres = array_values(array_unique(array_filter($selected_genres, function ($id) {
            return $id > 0;
        })));

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 30;

        $totalComics = comic_advanced_filter_count($keyword, $status, $chapter_count, $selected_genres);
        $totalPages = max(1, (int) ceil($totalComics / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;

        $comics = comic_advanced_filter_get_all($keyword, $status, $chapter_count, $selected_genres, $sort, $perPage, $offset);
        //  Mới: Chỉ lấy các chuyên mục cha lớn, chính thức có is_menu = 1
        $allGenres = function_exists('genre_get_for_menu') ? genre_get_for_menu() : [];

        include_once __DIR__ . '/../view/client/filter.php';
    }

    public function history()
    {
        include_once __DIR__ . '/../view/client/history.php';
    }

    public function genre_list()
    {
        // Lấy toàn bộ danh sách thể loại từ DB (sử dụng hàm có sẵn của hệ thống)
        $sqlAllGenres = "SELECT g.*, COUNT(cg.comic_id) AS comic_count 
                     FROM genres g
                     LEFT JOIN comics_genres cg ON g.id = cg.genre_id
                     GROUP BY g.id
                     ORDER BY g.genre_name ASC";
        $allGenres = pdo_getAll($sqlAllGenres);

        $pageTitle = "Tất Cả Thể Loại Truyện Tranh";

        include_once __DIR__ . '/../view/client/genre-list.php';
    }

    public function list_comics()
    {
        // 1. Nhận tiêu chí sắp xếp từ URL (Mặc định: mới nhất)
        $sort = trim((string) ($_GET['sort'] ?? 'newest'));

        // Ánh xạ tham số sang câu lệnh ORDER BY tương ứng của MySQL
        switch ($sort) {
            case 'oldest':
                $orderBy = "c.id ASC";
                break;
            case 'most_viewed':
                $orderBy = "total_views DESC, c.id DESC";
                break;
            case 'least_viewed':
                $orderBy = "total_views ASC, c.id ASC";
                break;
            case 'newest':
            default:
                $orderBy = "c.id DESC";
                break;
        }

        // 2. Cấu hình phân trang
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 24; // Hiển thị 24 truyện trên một trang để cân đối Grid giao diện

        // 3. Đếm tổng số lượng truyện và tính số trang
        $totalComics = comic_count('', ''); // Không lọc từ khóa hay trạng thái
        $totalPages = max(1, (int) ceil($totalComics / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;

        // 4. Lấy danh sách truyện từ Model dựa trên cơ chế sắp xếp được yêu cầu
        $comics = comic_get_all('', '', $orderBy, $perPage, $offset);

        // 5. Khai báo tiêu đề trang và nhúng giao diện view danh sách
        $pageTitle = "Danh Sách Tất Cả Truyện Tranh - MANGATIN";
        include_once __DIR__ . '/../view/client/list.php';
    }

    public function admin_dashboard()
    {
        // Yêu cầu quyền admin (Đã được định nghĩa trong auth.php)
        auth_require_admin();

        // Nạp file model thống kê
        require_once __DIR__ . '/../model/DashboardModel.php';

        // Lấy dữ liệu xử lý nghiệp vụ cho cụm 6.1
        $totalViews = dashboard_get_total_views();     // 6.1.1
        $popularComics = dashboard_get_popular_comics(5);  // 6.1.2
        $userStats = dashboard_get_user_stats();      // 6.1.3

        $pageTitle = "Bảng Điều Khiển Admin - MANGATIN";

        // Khai báo file view hiển thị giao diện dashboard
        include_once __DIR__ . '/../view/admin/dashboard.php';
    }
}