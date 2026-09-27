<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/libs/config.php';
require_once __DIR__ . '/libs/database.php';
require_once __DIR__ . '/libs/pdo.php';

require_once __DIR__ . '/model/ComicModel.php';
require_once __DIR__ . '/model/ChapterModel.php';
require_once __DIR__ . '/model/BannerModel.php';
require_once __DIR__ . '/model/NotificationModel.php';
require_once __DIR__ . '/model/UserModel.php';
require_once __DIR__ . '/model/GenreModel.php';
require_once __DIR__ . '/model/FollowModel.php';
require_once __DIR__ . '/model/CommentModel.php';
require_once __DIR__ . '/model/DashboardModel.php';

require_once __DIR__ . '/libs/auth.php';

auth_restore_session_from_remember_cookie();

require_once __DIR__ . '/controller/HomeController.php';
require_once __DIR__ . '/controller/ComicController.php';
require_once __DIR__ . '/controller/GenreController.php';
require_once __DIR__ . '/controller/UserController.php';
require_once __DIR__ . '/controller/FollowController.php';
require_once __DIR__ . '/controller/CommentController.php';
require_once __DIR__ . '/controller/ReportController.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'home';

switch ($action) {
    case 'home':
        $controller = new HomeController();
        $controller->index();
        break;
    case 'detail':
        $controller = new HomeController();
        $controller->detail();
        break;
    case 'reader':
        $controller = new HomeController();
        $controller->reader();
        break;
    case 'genre':
        $controller = new HomeController();
        $controller->genre();
        break;

    case 'history':
        $controller = new HomeController();
        $controller->history();
        break;

    case 'delete-history-item':
        // Đảm bảo an toàn bảo mật, bắt buộc có session đăng nhập trước khi xóa
        if (!isset($_SESSION['user']['id'])) {
            header("Location: index.php?action=login");
            exit;
        }
        $controller = new UserController();
        $controller->delete_history_item(); // Đã sửa từ $userController thành $controller
        break;

    case 'clear-all-history':
        // Đảm bảo an toàn bảo mật, bắt buộc có session đăng nhập trước khi xóa sạch
        if (!isset($_SESSION['user']['id'])) {
            header("Location: index.php?action=login");
            exit;
        }
        $controller = new UserController();
        $controller->clear_all_history(); // Đã sửa từ $userController thành $controller
        break;
    // Gộp tất cả xử lý "Đọc tất cả" thành một khối duy nhất trong switch-case của index.php
    case 'mark-all-notifications-read':
        header('Content-Type: application/json; charset=utf-8');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $current_user_id = $_SESSION['user']['id'] ?? 0;

        if ($current_user_id > 0) {
            // Câu lệnh cập nhật toàn bộ trạng thái chưa đọc về đã đọc (is_read = 1)
            $sql = "UPDATE notifications SET is_read = 1 WHERE (user_id = ? OR user_id IS NULL) AND is_read = 0";
            try {
                pdo_execute($sql, $current_user_id);
                echo json_encode(['success' => true, 'status' => 'success', 'message' => 'Đã đọc tất cả']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'status' => 'error', 'message' => 'Lỗi kết nối CSDL']);
            }
        } else {
            echo json_encode(['success' => false, 'status' => 'error', 'message' => 'Người dùng chưa đăng nhập']);
        }
        exit;

    case 'toggle-follow':
        $controller = new FollowController();
        $controller->toggle_follow();
        break;
    case 'register':
        $controller = new UserController();
        $controller->register();
        break;
    case 'login':
        $controller = new UserController();
        $controller->login();
        break;
    case 'logout':
        $controller = new UserController();
        $controller->logout();
        break;
    case 'profile':
        $controller = new UserController();
        $controller->profile();
        break;
    case 'change-password':
        $controller = new UserController();
        $controller->change_password();
        break;
    case 'favorites':
        $controller = new UserController(); // Hoặc HomeController tùy nơi bạn đặt hàm
        $controller->favorites();
        break;
    case 'upload-avatar':
        $controller = new UserController();
        $controller->upload_avatar();
        break;
    // Thêm vào bên trong cấu trúc switch ($action)
    case 'search':
        $controller = new HomeController();
        $controller->search();
        break;
    case 'genre-list':
        $controller = new HomeController();
        $controller->genre_list(); // Hàm này ta sẽ tạo ở bước 3
        break;
    // Thêm vào bên trong cấu trúc switch ($action)
    case 'filter':
        $controller = new HomeController();
        $controller->filter();
        break;
    case 'list':
        $controller = new HomeController();
        $controller->list_comics();
        break;
    case 'api-search-suggestions':
        $controller = new HomeController();
        $controller->api_search_suggestions();
        exit; // Kết thúc sớm vì đây là API trả về JSON, không phải trang giao diện
    // Thêm vào bên trong switch ($action) trong tệp index.php của bạn
    // ... [Các case khác giữ nguyên] ...

    case 'api-get-notifications':
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $user_id = $_SESSION['user']['id'] ?? 0;
        $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;
        $limit = 10;

        header('Content-Type: application/json; charset=utf-8');

        // Ép kiểu tường minh để tránh lỗi bind tham số chuỗi vào LIMIT/OFFSET của PDO
        if ($user_id <= 0) {
            $sql = "SELECT * FROM notifications WHERE user_id IS NULL AND is_read = 0 ORDER BY created_at DESC LIMIT %d OFFSET %d";
            $sql = sprintf($sql, $limit, $offset);
            $notifications = pdo_getAll($sql);
        } else {
            // Sử dụng tham số hóa an toàn cho user_id và nội hàm chuỗi số cho limit/offset
            $sql = "SELECT * FROM notifications 
                WHERE (user_id = ? OR user_id IS NULL) AND is_read = 0 
                ORDER BY created_at DESC LIMIT %d OFFSET %d";
            $sql = sprintf($sql, $limit, $offset);
            $notifications = pdo_getAll($sql, $user_id);
        }

        $notifications = $notifications ? $notifications : [];

        foreach ($notifications as &$noti) {
            $noti['formatted_time'] = date('d/m H:i', strtotime($noti['created_at']));
            $noti['title'] = htmlspecialchars($noti['title'], ENT_QUOTES, 'UTF-8');
            $noti['content'] = htmlspecialchars($noti['content'], ENT_QUOTES, 'UTF-8');
        }

        echo json_encode($notifications, JSON_UNESCAPED_UNICODE);
        exit;

    case 'api-mark-single-read':
        header('Content-Type: application/json; charset=utf-8');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $current_user_id = $_SESSION['user']['id'] ?? 0;
        $noti_id = intval($_POST['id'] ?? 0);

        // BẢO MẬT: Khách vãng lai không được quyền cấu hình trạng thái đọc lẻ thông báo chung
        if ($current_user_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập để thực hiện chức năng này']);
            exit;
        }

        if ($noti_id > 0) {
            // Chỉ cập nhật thông báo thuộc về chính tài khoản này, hoặc thông báo hệ thống nhưng định danh riêng
            $sql = "UPDATE notifications SET is_read = 1 WHERE id = ? AND (user_id = ? OR user_id IS NULL)";

            try {
                pdo_execute($sql, $noti_id, $current_user_id);
                echo json_encode(['success' => true, 'message' => 'Đã đọc thông báo lẻ']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Lỗi kết nối CSDL']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
        }
        exit;
    case 'post-comment':
        $controller = new CommentController();
        $controller->post_comment();
        break;
    case 'submit-rating':
        // Gọi đến hàm tương ứng trong Controller để xử lý lưu vào DB
        $controller = new ComicController(); // Hoặc HomeController tùy nơi bạn đặt hàm này
        $controller->submit_rating();
        break;
    case 'post-report':

        $controller = new ReportController();
        $controller->post_report();
        break;
    case 'admin-dashboard':
        auth_require_admin();
        $controller = new HomeController();
        $controller->admin_dashboard();
        break;

    case 'admin-user-list':
        auth_require_admin();
        $controller = new UserController();
        $controller->admin_list_users();
        break;

    case 'admin-comic-list':
        auth_require_admin();
        $controller = new ComicController();
        $controller->admin_list_comics();
        break;

    case 'admin-add-comic':
        auth_require_admin();
        $controller = new ComicController();
        $controller->admin_add_comic();
        break;

    case 'admin-edit-comic':
        auth_require_admin();
        $controller = new ComicController();
        $controller->admin_edit_comic();
        break;

    case 'admin-delete-comic':
        auth_require_admin();
        $controller = new ComicController();
        $controller->admin_delete_comic();
        break;

    case 'admin-chapter-list':
        auth_require_admin();
        $controller = new ComicController();
        $controller->admin_list_chapters();
        break;

    case 'admin-add-chapter':
        auth_require_admin();
        $controller = new ComicController();
        $controller->admin_add_chapter();
        break;

    case 'admin-edit-chapter':
        auth_require_admin();
        $controller = new ComicController();
        $controller->admin_edit_chapter();
        break;

    case 'admin-delete-chapter':
        auth_require_admin();
        $controller = new ComicController();
        $controller->admin_delete_chapter();
        break;

    case 'admin-genre-list':
        auth_require_admin();
        $controller = new GenreController();
        $controller->admin_list_genres();
        break;

    case 'admin-add-genre':
        auth_require_admin();
        $controller = new GenreController();
        $controller->admin_add_genre();
        break;

    case 'admin-edit-genre':
        auth_require_admin();
        $controller = new GenreController();
        $controller->admin_edit_genre();
        break;

    case 'admin-delete-genre':
        auth_require_admin();
        $controller = new GenreController();
        $controller->admin_delete_genre();
        break;

    case 'admin-lock-user':
        auth_require_admin();
        $controller = new UserController();
        $controller->admin_lock_user();
        break;

    case 'admin-unlock-user':
        auth_require_admin();
        $controller = new UserController();
        $controller->admin_unlock_user();
        break;

    case 'admin-change-comic-status':
        auth_require_admin();
        $controller = new ComicController();
        $controller->admin_change_comic_status();
        break;

    // ================= ROUTE BÁO LỖI CHƯƠNG =================
    case 'admin-report-list':
        require_once __DIR__ . '/controller/ReportController.php';
        $controller = new ReportController();
        $controller->admin_list_reports();
        break;

    case 'admin-resolve-report':
        require_once __DIR__ . '/controller/ReportController.php';
        $controller = new ReportController();
        $controller->admin_resolve_report();
        break;

    case 'admin-delete-report':
        require_once __DIR__ . '/controller/ReportController.php';
        $controller = new ReportController();
        $controller->admin_delete_report();
        break;

    // ================= ROUTE KIỂM DUYỆT BÌNH LUẬN =================
    case 'admin-comment-list':
        require_once __DIR__ . '/controller/CommentController.php';
        $controller = new CommentController();
        $controller->admin_list_comments();
        break;

    case 'admin-delete-comment':
        require_once __DIR__ . '/controller/CommentController.php';
        $controller = new CommentController();
        $controller->admin_delete_comment();
        break;

    case 'admin-banner-list':
        require_once __DIR__ . '/controller/BannerController.php';
        (new BannerController())->admin_list();
        break;

    case 'admin-banner-add':
        require_once __DIR__ . '/controller/BannerController.php';
        (new BannerController())->admin_add();
        break;

    case 'admin-banner-edit':
        require_once __DIR__ . '/controller/BannerController.php';
        (new BannerController())->admin_edit();
        break;

    case 'admin-banner-delete':
        require_once __DIR__ . '/controller/BannerController.php';
        (new BannerController())->admin_delete();
        break;

    default:
        header("HTTP/1.0 404 Not Found");
        exit;
}
?>