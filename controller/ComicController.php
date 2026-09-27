<?php
require_once __DIR__ . '/../libs/auth.php';
require_once __DIR__ . '/../libs/pdo.php';
require_once __DIR__ . '/../model/ComicModel.php';
require_once __DIR__ . '/../libs/helper.php';

class ComicController
{
    public function admin_list_comics()
    {
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $sort = $_GET['sort'] ?? 'newest'; // Giá trị mặc định
        $perPage = 12;
        $page = (int) ($_GET['page'] ?? 1);

        $totalComics = comic_count($search, $status);
        $totalPages = max(1, (int) ceil($totalComics / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        // Thiết lập chuỗi ORDER BY dựa vào bộ lọc bộ chọn $sort
        $orderBy = "c.id DESC";

        switch ($sort) {
            case 'oldest':
                $orderBy = "c.id ASC";
                break;
            case 'title_asc':
                $orderBy = "c.title ASC";
                break;
            case 'title_desc':
                $orderBy = "c.title DESC";
                break;
            case 'views_desc':
                $orderBy = "views_desc"; // Từ khóa để Model nhận diện sắp xếp theo tổng views
                break;
            case 'newest':
            default:
                $orderBy = "c.id DESC";
                break;
        }

        // GỌI HÀM THEO THỨ TỰ MỚI: $search, $status, $orderBy, $perPage, $offset
        $comics = comic_get_all($search, $status, $orderBy, $perPage, $offset);

        $from = ($page - 1) * $perPage + 1;
        $to = min($page * $perPage, $totalComics);

        $pageTitle = "Danh Sách Truyện Tranh";
        require_once __DIR__ . '/../view/admin/Comic/comic-list.php';
    }
    public function admin_add_comic()
    {
        $currentAction = $_GET['action'] ?? '';
        $error = ''; // Khởi tạo biến thông báo lỗi

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentPage = (int) ($_POST['page'] ?? 1);
            $search = $_POST['search'] ?? '';
            $statusFilter = $_POST['status_filter'] ?? '';
            $sort = $_POST['sort'] ?? 'newest';

            $id = (int) ($_POST['id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $other_title = trim($_POST['other_title'] ?? '');
            $inputSlug = trim($_POST['slug'] ?? '');
            $slug = str_slug($inputSlug !== '' ? $inputSlug : $title);
            $authorInput = trim($_POST['author'] ?? '');
            $author = ($authorInput !== '') ? $authorInput : 'Đang cập nhật';
            $description = trim($_POST['summary'] ?? '');
            $status = trim($_POST['status'] ?? '0');
            $existingImage = trim((string) ($_POST['thumbnail_existing'] ?? ''));

            // --- BỔ SUNG: Nhận danh sách các ID thể loại được tick từ form ---
            $genre_ids = $_POST['genres'] ?? [];

            $uploadError = '';
            // Thực hiện upload ảnh
            $uploadedImage = comic_handle_image_upload('thumbnail', $uploadError);

            // Kiểm tra nếu hàm upload trả về false
            if ($uploadedImage === false) {
                $error = $uploadError;
            } else {
                $thumbnail = $uploadedImage ?: $existingImage;

                if ($currentAction === 'admin-add-comic' && $thumbnail === '') {
                    $thumbnail = 'default-comic.png';
                }

                if (empty($title) || empty($slug)) {
                    $error = "Vui lòng điền đầy đủ các trường bắt buộc!";
                } else {
                    $comicData = [
                        'id' => $id,
                        'title' => $title,
                        'other_title' => $other_title,
                        'slug' => $slug,
                        'author' => $author,
                        'summary' => $description,
                        'thumbnail' => $thumbnail,
                        'status' => $status
                    ];

                    $result = comic_add($comicData);
                    if ($result) {
                        // --- BỔ SUNG: Thêm liên kết thể loại cho truyện vừa tạo ---
                        // Truy vấn lấy lại ID truyện vừa tạo dựa trên slug duy nhất
                        $new_comic_id = pdo_getValue("SELECT id FROM comics WHERE slug = ?", $slug);
                        if ($new_comic_id) {
                            comic_update_genres((int) $new_comic_id, $genre_ids);
                        }

                        header("Location: index.php?action=admin-comic-list&success=add&page={$currentPage}&search=" . urlencode($search) . "&status=" . urlencode($statusFilter) . "&sort=" . urlencode($sort));
                        exit;
                    } else {
                        $error = "Thêm truyện thất bại! Vui lòng thử lại.";
                    }
                }
            }
        }

        // --- BỔ SUNG: Lấy toàn bộ danh sách thể loại từ DB để lặp ra view ---
        $all_genres = comic_get_all_genres();

        $pageTitle = "Thêm Truyện Tranh Mới";
        require_once __DIR__ . '/../view/admin/Comic/comic-add.php';
    }
    public function admin_edit_comic()
    {
        $comic_id = (int) ($_GET['id'] ?? 0);
        $comic = comic_get_by_id($comic_id);

        if (!$comic) {
            header("Location: index.php?action=admin-comic-list&error=notfound");
            exit;
        }

        $currentAction = $_GET['action'] ?? '';
        $error = ''; // Khởi tạo biến thông báo lỗi

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $currentPage = (int) ($_POST['page'] ?? 1);
            $search = $_POST['search'] ?? '';
            $statusFilter = $_POST['status_filter'] ?? '';
            $sort = $_POST['sort'] ?? 'newest';

            $title = trim($_POST['title'] ?? '');
            $other_title = trim($_POST['other_title'] ?? '');
            $inputSlug = trim($_POST['slug'] ?? '');
            $slug = str_slug($inputSlug !== '' ? $inputSlug : $title);
            $author = trim($_POST['author'] ?? '');
            $description = trim($_POST['summary'] ?? '');
            $status = trim($_POST['status'] ?? '0');
            $existingImage = trim((string) ($_POST['thumbnail_existing'] ?? ''));

            // --- BỔ SUNG: Nhận mảng ID thể loại mới chỉnh sửa từ form ---
            $genre_ids = $_POST['genres'] ?? [];

            $uploadError = '';
            // Thực hiện upload ảnh
            $uploadedImage = comic_handle_image_upload('thumbnail', $uploadError);

            // Kiểm tra nếu hàm upload trả về false
            if ($uploadedImage === false) {
                $error = $uploadError;
            } else {
                $thumbnail = $uploadedImage ?: $existingImage;

                if (empty($title) || empty($slug) || empty($author)) {
                    $error = "Vui lòng điền đầy đủ các trường bắt buộc!";
                } else {
                    $comicData = [
                        'id' => $comic_id,
                        'title' => $title,
                        'other_title' => $other_title,
                        'slug' => $slug,
                        'author' => $author,
                        'summary' => $description,
                        'thumbnail' => $thumbnail,
                        'status' => $status
                    ];
                    $result = comic_update($comic_id, $comicData);

                    // --- BỔ SUNG: Đồng bộ lại danh sách thể loại (Xóa cũ, ghi mới) ---
                    comic_update_genres($comic_id, $genre_ids);

                    header("Location: index.php?action=admin-comic-list&page={$currentPage}&search=" . urlencode($search) . "&status=" . urlencode($statusFilter) . "&sort=" . urlencode($sort) . "&success=edit");
                    exit;
                }
            }
        }

        // --- BỔ SUNG: Lấy dữ liệu thể loại để hiển thị trạng thái check trên form ---
        $all_genres = comic_get_all_genres(); // Lấy tất cả thể loại từ DB
        $current_genre_ids = comic_get_genre_ids_by_comic_id($comic_id); // Lấy các ID thể loại truyện đang có

        $pageTitle = "Chỉnh Sửa Truyện Tranh";
        require_once __DIR__ . '/../view/admin/Comic/comic-edit.php';
    }
    public function admin_delete_comic()
    {
        $comic_id = (int) ($_GET['id'] ?? 0);
        $comic = comic_get_by_id($comic_id);

        if (!$comic) {
            header("Location: index.php?action=admin-comic-list&error=notfound");
            exit;
        }
        comic_delete($comic_id);
        header("Location: index.php?action=admin-comic-list&success=delete");
        exit;
    }
    public function admin_change_comic_status()
    {
        $comic_id = (int) ($_GET['id'] ?? 0);
        $comic = comic_get_by_id($comic_id);

        if (!$comic) {
            header("Location: index.php?action=admin-comic-list&error=notfound");
            exit;
        }

        $newStatus = isset($_GET['status']) ? trim($_GET['status']) : '0';

        if (!in_array($newStatus, ['0', '1', '2'], true)) {
            header("Location: index.php?action=admin-comic-list&error=invalidstatus");
            exit;
        }

        // Đọc lại các tham số phân trang và bộ lọc từ Form gửi lên
        $currentPage = (int) ($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';
        $statusFilter = $_GET['status_filter'] ?? '';
        $sort = $_GET['sort'] ?? 'newest';

        $comicData = [
            'title' => $comic['title'],
            'other_title' => $comic['other_title'],
            'slug' => $comic['slug'],
            'author' => $comic['author'],
            'summary' => $comic['summary'],
            'thumbnail' => $comic['thumbnail'],
            'status' => $newStatus
        ];

        comic_update($comic_id, $comicData);

        // CHUYỂN HƯỚNG QUAY LẠI ĐÚNG TRANG VÀ GIỮ NGUYÊN BỘ LỌC CŨ
        header("Location: index.php?action=admin-comic-list&page={$currentPage}&search=" . urlencode($search) . "&status=" . urlencode($statusFilter) . "&sort=" . urlencode($sort) . "&success=status");
        exit;
    }
    public function admin_list_chapters()
    {
        $comic_id = (int) ($_GET['comic_id'] ?? 0);
        $comic = comic_get_by_id($comic_id);

        if (!$comic) {
            header("Location: index.php?action=admin-comic-list&error=notfound");
            exit;
        }

        // --- SỬA TẠI ĐÂY: Phân tách rõ ràng giữa tìm kiếm chương và lưu vết tìm kiếm truyện ---
        // Kiểm tra xem request gửi lên là từ form tìm kiếm CHƯƠNG hay chuyển hướng từ danh sách TRUYỆN
        // Nếu URL có tham số 'chapter_search' thì đó mới là từ khóa tìm chương
        $search = trim($_GET['chapter_search'] ?? '');

        // Lưu lại các biến lọc của danh sách TRUYỆN để làm chuỗi quay lại (Query String)
        $comicSearch = $_GET['search'] ?? '';
        $statusFilter = $_GET['status'] ?? '';
        $sort = $_GET['sort'] ?? 'desc';
        $orderBy = ($sort === 'asc') ? 'chapter_number ASC' : 'chapter_number DESC';

        // Phân trang
        $perPage = 10;
        $totalChapters = chapter_count_by_comic_id($comic_id, $search); // $search lúc này mặc định trống nên sẽ lấy hết chương
        $totalPages = max(1, (int) ceil($totalChapters / $perPage));
        $page = max(1, (int) ($_GET['chapter_page'] ?? 1)); // Dùng biến riêng cho trang của chương để tránh đè lên trang của truyện
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        // Lấy dữ liệu chương thỏa mãn điều kiện
        $chapters = chapter_get_all_with_filter($comic_id, $search, $orderBy, $perPage, $offset);

        $pageTitle = "Quản Lý Chương Truyện - " . htmlspecialchars($comic['title']);

        // Tạo lại chuỗi truy vấn quay về danh sách truyện chuẩn xác
        $queryString = "&page=" . ($_GET['page'] ?? 1) . "&search=" . urlencode($comicSearch) . "&status=" . urlencode($statusFilter) . "&sort=" . urlencode($_GET['sort'] ?? 'newest');

        require_once __DIR__ . '/../view/admin/Chapter/chapter-list.php';
    }

    public function admin_add_chapter()
    {
        $comic_id = isset($_GET['comic_id']) ? (int) $_GET['comic_id'] : 0;

        // Kiểm tra truyện tranh có tồn tại không
        $comic = comic_get_by_id($comic_id);
        if (!$comic) {
            header("Location: index.php?action=admin-comic-list&error=comicnotfound");
            exit;
        }

        $error = ''; // Khởi tạo biến thông báo lỗi

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $currentPage = (int) ($_POST['page'] ?? 1);
            $search = $_POST['search'] ?? '';
            $statusFilter = $_POST['status_filter'] ?? '';
            $sort = $_POST['sort'] ?? 'newest';

            $chapter_number = trim($_POST['chapter_number'] ?? '');
            $chapter_title = trim($_POST['chapter_title'] ?? '');

            // 1. Kiểm tra tính hợp lệ của dữ liệu form đầu vào
            if ($chapter_number === '') {
                $error = "Vui lòng nhập số chương (Ví dụ: 1 hoặc 1.5).";
            } else {
                if ($chapter_number <= 0) {
                    $error = "Số chương phải là một số lớn hơn 0.";
                } else {
                    if (check_duplicate_chapter($chapter_number, $comic_id)) {
                        $error = "Số chương này đã tồn tại trong hệ thống. Vui lòng nhập số khác.";
                    }
                }
            }

            // 2. Nếu thông tin form hợp lệ, tiến hành thêm chương và xử lý ảnh
            if (empty($error)) {
                // Tạo bản ghi chương mới trong cơ sở dữ liệu
                $chapter_id = chapter_add($comic_id, $chapter_number, $chapter_title);

                if ($chapter_id > 0) {

                    if (function_exists('notification_add_new_chapter')) {
                        notification_add_new_chapter($comic['title'], $chapter_number, $chapter_id);
                    }

                    // Xử lý upload nhiều ảnh chương (nếu Admin có chọn file)
                    if (isset($_FILES['chapter_images']) && !empty($_FILES['chapter_images']['name'][0])) {
                        $files = $_FILES['chapter_images'];
                        $totalFiles = count($files['name']);

                        $baseUploadDir = comic_upload_dir(); // Đường dẫn thư mục upload gốc vật lý
                        $chapterFolder = "{$comic_id}/{$chapter_id}/"; // Cấu trúc thư mục id_truyen/id_chuong
                        $fullTargetDir = $baseUploadDir . $chapterFolder;

                        // Tạo thư mục nếu chưa tồn tại
                        if (!is_dir($fullTargetDir)) {
                            mkdir($fullTargetDir, 0775, true);
                        }

                        $order = 1; // Số thứ tự trang truyện bắt đầu từ 1
                        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];
                        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/avif'];
                        $maxSize = 3 * 1024 * 1024; // Giới hạn kích thước tối đa 3MB mỗi ảnh

                        $uploadSuccess = true;
                        $uploadedFilesList = []; // Mảng lưu vết các file đã upload vật lý thành công

                        for ($i = 0; $i < $totalFiles; $i++) {
                            // Bỏ qua nếu cấu trúc file upload bị lỗi hệ thống cơ bản
                            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                                continue;
                            }

                            $tmpName = $files['tmp_name'][$i];
                            $originalName = $files['name'][$i];

                            // --- BẢO MẬT 1: Kiểm tra dung lượng file ---
                            if ($files['size'][$i] > $maxSize) {
                                $error = "Tệp tin thứ " . ($i + 1) . " ({$originalName}) vượt quá kích thước cho phép (Tối đa 3MB).";
                                $uploadSuccess = false;
                                break;
                            }

                            // --- BẢO MẬT 2: Kiểm tra đuôi mở rộng của tệp ---
                            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                            if (!in_array($ext, $allowedExtensions, true)) {
                                $error = "Tệp tin thứ " . ($i + 1) . " không đúng định dạng ảnh được phép upload.";
                                $uploadSuccess = false;
                                break;
                            }

                            // --- BẢO MẬT 3: Kiểm tra cấu trúc nội dung (MIME type) thực tế chống mã độc ẩn danh ---
                            if (function_exists('mime_content_type')) {
                                $mimeType = mime_content_type($tmpName);

                                if (!in_array($mimeType, $allowedMimeTypes, true)) {
                                    // Cơ chế dự phòng (Fallback): Nếu đuôi file là avif nhưng server cũ trả về octet-stream hoặc trống, vẫn cho qua
                                    if ($ext === 'avif' && ($mimeType === 'application/octet-stream' || $mimeType === '')) {
                                        // Hợp lệ, châm chước cho qua vì server chưa cập nhật thư viện magic
                                    } else {
                                        $error = "Nội dung tệp tin thứ " . ($i + 1) . " không phải là ảnh hợp lệ!";
                                        $uploadSuccess = false;
                                        break;
                                    }
                                }
                            }

                            // Tiến hành đặt tên file an toàn: Định dạng 001-randomSuffix.ext
                            $randomSuffix = substr(md5(uniqid(rand(), true)), 0, 6);
                            $fileName = sprintf("%03d", $order) . '-' . $randomSuffix . '.' . $ext;
                            $targetFilePath = $fullTargetDir . $fileName;

                            // Di chuyển tệp tin từ thư mục tạm sang thư mục lưu trữ
                            if (move_uploaded_file($tmpName, $targetFilePath)) {
                                // Đường dẫn lưu trữ tương đối trong database
                                $dbPath = "uploads/comics/" . $chapterFolder . $fileName;

                                // Lưu thông tin trang truyện vào bảng `chapter_images`
                                chapter_image_add($chapter_id, $dbPath, $order);

                                // Lưu lại đường dẫn vật lý để phòng trường hợp lỗi cần dọn dẹp
                                $uploadedFilesList[] = $targetFilePath;
                                $order++;
                            } else {
                                $error = "Không thể lưu tệp tin ảnh thứ " . ($i + 1) . " lên máy chủ máy chủ.";
                                $uploadSuccess = false;
                                break;
                            }
                        }

                        // --- CƠ CHẾ DỌN DẸP TỰ ĐỘNG (ROLLBACK) KHI CÓ LỖI XẢY RA GIỮA CHỪNG ---
                        if (!$uploadSuccess) {
                            // 1. Xóa các file ảnh vật lý đã lỡ di chuyển thành công trước đó để tránh rác ổ cứng
                            foreach ($uploadedFilesList as $fileToDel) {
                                if (is_file($fileToDel)) {
                                    @unlink($fileToDel);
                                }
                            }
                            // Thử xóa thư mục chương vừa tạo nếu nó trống không
                            if (is_dir($fullTargetDir)) {
                                @rmdir($fullTargetDir);
                            }

                            // 2. Xóa bản ghi dữ liệu chương vừa được tạo lỗi trong cơ sở dữ liệu
                            // Bạn cần đảm bảo đã viết hàm `chapter_delete($id)` trong ChapterModel.php
                            if (function_exists('chapter_delete')) {
                                chapter_delete($chapter_id);
                            } else {
                                // Phương án dự phòng nếu chưa viết hàm xóa: Chạy trực tiếp qua pdo
                                $sqlDeleteChapter = "DELETE FROM chapters WHERE id = ?";
                                pdo_execute($sqlDeleteChapter, $chapter_id);
                            }

                            // Hiển thị lại view form kèm thông báo lỗi cụ thể để admin biết và upload lại
                            $pageTitle = "Thêm Chương Mới - " . htmlspecialchars($comic['title']);
                            require_once __DIR__ . '/../view/admin/Chapter/chapter-add.php';
                            exit;
                        }
                    }

                    // Toàn bộ quá trình thêm chương và upload ảnh thành công, chuyển hướng về danh sách chương
                    header("Location: index.php?action=admin-chapter-list" .
                        "&comic_id=" . (int) $comic_id .
                        "&success=addchapter" .
                        "&chapter_page=1" .
                        "&chapter_search=" .
                        "&sort=" . urlencode($sort) .
                        "&page=" . (int) $currentPage .
                        "&search=" . urlencode($search) .
                        "&status=" . urlencode($statusFilter));
                    exit;
                } else {
                    $error = "Thêm chương thất bại! Số chương này có thể đã tồn tại trong hệ thống truyện của bạn.";
                }
            }
        }

        // Hiển thị giao diện Form thêm chương mới khi gọi bằng phương thức GET hoặc khi dính lỗi nhập liệu
        $pageTitle = "Thêm Chương Mới - " . htmlspecialchars($comic['title']);
        require_once __DIR__ . '/../view/admin/Chapter/chapter-add.php';
    }
    public function admin_edit_chapter()
    {
        $chapter_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        // 1. Kiểm tra chương truyện có tồn tại không
        $chapter = chapter_get_by_id($chapter_id);
        if (!$chapter) {
            header("Location: index.php?action=admin-comic-list&error=chapternotfound");
            exit;
        }

        // Lấy thông tin truyện để hiển thị ở tiêu đề tiêu đề
        $comic_id = $chapter['comic_id'];
        $comic = comic_get_by_id($comic_id);

        // Lấy danh sách ảnh hiện tại của chương để hiển thị ở giao diện
        $currentImages = chapter_images_get_by_chapter_id($chapter_id);

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $chapter_number = trim($_POST['chapter_number'] ?? '');
            $chapter_title = trim($_POST['chapter_title'] ?? '');

            // Kiểm tra tính hợp lệ dữ liệu
            if ($chapter_number === '') {
                $error = "Vui lòng nhập số chương.";
            } else {
                $chapter_number = (float) $chapter_number;
                if ($chapter_number <= 0) {
                    $error = "Số chương phải lớn hơn 0.";
                }
            }

            if (empty($error)) {
                // Cập nhật thông tin cơ bản của chương trước
                chapter_update($chapter_id, $chapter_number, $chapter_title);

                // Kiểm tra xem Admin có tải lên loạt ảnh mới để thay thế không
                if (isset($_FILES['chapter_images']) && !empty($_FILES['chapter_images']['name'][0])) {
                    $files = $_FILES['chapter_images'];
                    $totalFiles = count($files['name']);

                    $baseUploadDir = comic_upload_dir(); //
                    $chapterFolder = "{$comic_id}/{$chapter_id}/"; //
                    $fullTargetDir = $baseUploadDir . $chapterFolder; //

                    // Tạo thư mục nếu chưa có
                    if (!is_dir($fullTargetDir)) {
                        mkdir($fullTargetDir, 0775, true); //
                    }

                    $order = 1; //
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif']; //
                    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/avif']; //
                    $maxSize = 3 * 1024 * 1024; //

                    $uploadSuccess = true;
                    $uploadedFilesList = []; //

                    // Vòng lặp xử lý và kiểm tra file mới
                    for ($i = 0; $i < $totalFiles; $i++) {
                        if ($files['error'][$i] !== UPLOAD_ERR_OK) { //
                            continue;
                        }

                        $tmpName = $files['tmp_name'][$i];
                        $originalName = $files['name'][$i];

                        // Kiểm tra dung lượng
                        if ($files['size'][$i] > $maxSize) { //
                            $error = "Tệp tin thứ " . ($i + 1) . " ({$originalName}) vượt quá kích thước 3MB."; //
                            $uploadSuccess = false;
                            break;
                        }

                        // Kiểm tra đuôi file
                        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION)); //
                        if (!in_array($ext, $allowedExtensions, true)) { //
                            $error = "Tệp tin thứ " . ($i + 1) . " không đúng định dạng ảnh cho phép."; //
                            $uploadSuccess = false;
                            break;
                        }

                        // Kiểm tra MIME type thực tế (Hỗ trợ dự phòng AVIF cho server cũ)
                        if (function_exists('mime_content_type')) {
                            $mimeType = mime_content_type($tmpName);
                            if (!in_array($mimeType, $allowedMimeTypes, true)) {
                                if ($ext === 'avif' && ($mimeType === 'application/octet-stream' || $mimeType === '')) {
                                    // Chấp nhận cấu hình dự phòng cho file .avif
                                } else {
                                    $error = "Nội dung tệp tin thứ " . ($i + 1) . " không phải là ảnh hợp lệ!"; //
                                    $uploadSuccess = false;
                                    break;
                                }
                            }
                        }

                        // Đặt tên file
                        $randomSuffix = substr(md5(uniqid(rand(), true)), 0, 6); //
                        $fileName = sprintf("%03d", $order) . '-' . $randomSuffix . '.' . $ext; //
                        $targetFilePath = $fullTargetDir . $fileName; //

                        if (move_uploaded_file($tmpName, $targetFilePath)) { //
                            $uploadedFilesList[] = [
                                'physical_path' => $targetFilePath,
                                'db_path' => "uploads/comics/" . $chapterFolder . $fileName,
                                'order' => $order
                            ];
                            $order++; //
                        } else {
                            $error = "Không thể lưu tệp tin ảnh thứ " . ($i + 1) . " lên hệ thống.";
                            $uploadSuccess = false;
                            break;
                        }
                    }

                    if ($uploadSuccess) {
                        // --- ĐĂNG ẢNH MỚI THÀNH CÔNG -> TIẾN HÀNH DỌN DẸP ẢNH CŨ ---
                        // 1. Xóa toàn bộ file ảnh cũ trên ổ cứng
                        foreach ($currentImages as $oldImg) {
                            $oldPhysicalPath = __DIR__ . '/../' . $oldImg['image_url'];
                            if (is_file($oldPhysicalPath)) {
                                @unlink($oldPhysicalPath);
                            }
                        }

                        // 2. Xóa dữ liệu ảnh cũ trong bảng chapter_images
                        chapter_images_delete_by_chapter_id($chapter_id);

                        // 3. Thêm danh sách ảnh mới vào database
                        foreach ($uploadedFilesList as $newImg) {
                            chapter_image_add($chapter_id, $newImg['db_path'], $newImg['order']);
                        }
                    } else {
                        // --- UPLOAD BỊ LỖI GIỮA CHỪNG -> ROLLBACK HỦY LOẠT ẢNH MỚI VỪA TẢI TẠM LÊN ---
                        foreach ($uploadedFilesList as $tempImg) {
                            if (is_file($tempImg['physical_path'])) {
                                @unlink($tempImg['physical_path']);
                            }
                        }

                        // Nạp lại dữ liệu cũ để hiển thị lại view sửa đổi dữ liệu lỗi
                        $currentImages = chapter_images_get_by_chapter_id($chapter_id);
                        $pageTitle = "Chỉnh Sửa Chương - " . htmlspecialchars($comic['title']);
                        require_once __DIR__ . '/../view/admin/Chapter/chapter-edit.php';
                        exit;
                    }
                }

                // Thành công, chuyển hướng về lại danh sách chương của bộ truyện đó
                header("Location: index.php?action=admin-chapter-list&comic_id={$comic_id}&success=editchapter");
                exit;
            }
        }

        $pageTitle = "Chỉnh Sửa Chương - " . htmlspecialchars($comic['title']);
        require_once __DIR__ . '/../view/admin/Chapter/chapter-edit.php';
    }
    public function admin_delete_chapter()
    {
        $chapter_id = (int) ($_GET['id'] ?? 0);
        $chapter = chapter_get_by_id($chapter_id);

        if (!$chapter) {
            header("Location: index.php?action=admin-comic-list&error=chapternotfound");
            exit;
        }

        // Lấy thông tin truyện để chuyển hướng về lại danh sách chương sau khi xóa
        $comic_id = $chapter['comic_id'];
        $comic = comic_get_by_id($comic_id);

        // Xóa chương và toàn bộ ảnh liên quan
        chapter_delete($chapter_id);

        header("Location: index.php?action=admin-chapter-list&comic_id={$comic_id}&success=deletechapter");
        exit;
    }
    // Thêm hàm này vào một Controller xử lý dữ liệu (ví dụ HomeController hoặc UserController)
    public function submit_rating()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập để đánh giá!']);
            exit;
        }

        $user_id = $_SESSION['user']['id'];
        $comic_id = (int) ($_POST['comic_id'] ?? 0);
        $rating_value = (int) ($_POST['rating'] ?? 0);

        if ($comic_id <= 0 || $rating_value < 1 || $rating_value > 5) {
            echo json_encode(['status' => 'error', 'message' => 'Dữ liệu đánh giá không hợp lệ!']);
            exit;
        }

        require_once __DIR__ . '/../model/RatingModel.php';
        $result = rating_submit($user_id, $comic_id, $rating_value);

        if ($result) {
            $stats = rating_get_average($comic_id);
            echo json_encode([
                'status' => 'success',
                'message' => 'Cảm ơn bạn đã đánh giá bộ truyện này!',
                'avg_rating' => round($stats['avg_rating'], 1),
                'total_votes' => $stats['total_votes']
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Có lỗi xảy ra, thử lại sau.']);
        }
        exit;
    }
    public function admin_mark_notification_read()
    {
        $noti_id = (int) ($_POST['id'] ?? 0);

        if ($noti_id > 0) {
            require_once __DIR__ . '/../model/NotificationModel.php';
            $result = notification_mark_as_read_by_id($noti_id);

            header('Content-Type: application/json');
            if ($result) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Không thể cập nhật']);
            }
            exit;
        }
    }
}