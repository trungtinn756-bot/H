<?php
require_once __DIR__ . '/../model/UserModel.php';
require_once __DIR__ . '/../libs/auth.php';
require_once __DIR__ . '/../libs/config.php';
require_once __DIR__ . '/../libs/pdo.php';
require_once __DIR__ . '/../model/HistoryModel.php';
class UserController
{
    public function register()
    {
        if (auth_is_logged_in()) {
            header("Location: index.php?action=home");
            exit;
        }

        $error = '';
        $success = '';
        $login_success_message = $_SESSION['login_success_message'] ?? '';
        unset($_SESSION['login_success_message']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $confirm_password = trim($_POST['confirm_password'] ?? '');
            $existing_user = user_get_by_username($username);
            $existing_email = user_get_by_email($email);

            if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
                $error = "Vui lòng điền đầy đủ tất cả các trường thông tin!";
            } else if ($existing_user) {
                $error = "Tên đăng nhập đã tồn tại! Vui lòng chọn tên khác.";
            } else if ($existing_email) {
                $error = "Email đã được sử dụng! Vui lòng sử dụng email khác.";
            } else if ($password !== $confirm_password) {
                $error = "Mật khẩu và xác nhận mật khẩu không khớp!";
            } else if (strlen($password) < 8) {
                $error = "Mật khẩu phải có ít nhất 8 ký tự!";
            } else {
                $result = user_register($username, $email, $password);
                if ($result) {
                    $success = "Đăng ký tài khoản thành công! Bạn có thể đăng nhập ngay bây giờ.";
                    header("Location: index.php?action=login");
                    exit;
                } else {
                    $error = "Đăng ký thất bại! Tên đăng nhập hoặc Email có thể đã tồn tại.";
                }
            }
        }

        $pageTitle = "Đăng Ký Thành Viên";
        require_once __DIR__ . '/../view/client/register.php';
    }

    public function login()
    {
        if (auth_is_logged_in()) {
            header("Location: index.php?action=home");
            exit;
        }

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['identity'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($username) || empty($password)) {
                $error = "Vui lòng điền đầy đủ tất cả các trường thông tin!";
            } else {
                $user = user_get_by_username_or_email($username);

                if ($user && password_verify($password, $user['password'])) {
                    if ((int) $user['status'] === 0) {
                        $error = "Tài khoản của bạn đã bị khóa bởi Ban Quản Trị!";
                    } else {
                        $_SESSION['user'] = [
                            'id' => $user['id'],
                            'username' => $user['username'],
                            'email' => $user['email'],
                            'display_name' => $user['display_name'],
                            'role' => (string) ((int) $user['role']),
                            'avatar' => $user['avatar'],
                            'created_at' => $user['created_at'] ?? '',
                            'updated_at' => $user['updated_at'] ?? 'Chưa cập nhật',
                        ];

                        if (isset($_POST['remember'])) {
                            $cookie_expire = time() + (30 * 24 * 60 * 60);
                            setcookie('remember_user', $user['username'], $cookie_expire, '/', '', false, true);
                        }

                        if ((int) $user['role'] === 1) {
                            header("Location: index.php?action=admin-dashboard");
                        } else {
                            header("Location: index.php?action=home");
                        }
                        exit;
                    }
                } else {
                    $error = "Tên đăng nhập hoặc mật khẩu không đúng!";
                }
            }
        }

        $pageTitle = "Đăng Nhập Độc Giả MANGATIN";
        require_once __DIR__ . '/../view/client/login.php';
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        unset($_SESSION['user']);

        if (isset($_COOKIE['remember_user'])) {
            setcookie('remember_user', '', time() - 3600, "/");
        }

        session_destroy();

        header("Location: index.php?action=home");
        exit;
    }

    public function profile()
    {
        if (!auth_is_logged_in()) {
            header("Location: index.php?action=login");
            exit;
        }

        $current_user = auth_get_current_user();

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $display_name = trim($_POST['display_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $current_time = date("Y-m-d H:i:s");

            if (empty($display_name) || empty($email)) {
                $error = "Vui lòng điền đầy đủ tất cả các trường thông tin!";
            } else if (mb_strlen($display_name) > 100) {
                $error = "Tên hiển thị không được quá 100 ký tự!";
            } else {
                $existing_email_user = user_get_by_email($email);
                if ($existing_email_user && $existing_email_user['id'] != $current_user['id']) {
                    $error = "Email đã được sử dụng bởi người khác! Vui lòng sử dụng email khác.";
                } else {
                    $result = user_update_profile($current_user['id'], $display_name, $email);
                    if ($result) {
                        $_SESSION['user']['display_name'] = $display_name;
                        $_SESSION['user']['email'] = $email;
                        $_SESSION['user']['updated_at'] = $current_time;
                        $success = "Cập nhật thông tin cá nhân thành công!";
                    } else {
                        $error = "Cập nhật thất bại! Vui lòng thử lại.";
                    }
                }
            }
        }

        $pageTitle = "Tài Khoản MANGATIN";
        require_once __DIR__ . '/../view/client/profile.php';
    }

    public function upload_avatar()
    {
        // 1. Kiểm tra quyền đăng nhập
        if (!auth_is_logged_in()) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Bạn chưa đăng nhập!']);
            exit;
        }

        header('Content-Type: application/json; charset=UTF-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Phương thức không hợp lệ.']);
            exit;
        }

        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy file ảnh.']);
            exit;
        }

        $file = $_FILES['avatar'];
        $current_user = auth_get_current_user();

        // Cấu hình các điều kiện validate ảnh
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxFileSize = 2 * 1024 * 1024; // Thư mục giới hạn dung lượng: 2MB
        $targetDir = __DIR__ . '/../uploads/avatars/';

        // Kiểm tra dung lượng file
        if ($file['size'] > $maxFileSize) {
            echo json_encode(['status' => 'error', 'message' => 'Kích thước ảnh không được vượt quá 2MB.']);
            exit;
        }

        // Tạo thư mục lưu trữ nếu chưa có sẵn
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Kiểm tra đuôi file mở rộng và định dạng MIME thực tế
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $realMimeType = mime_content_type($file['tmp_name']);

        if (!in_array($fileExtension, $allowedExtensions) || !in_array($realMimeType, $allowedMimeTypes)) {
            echo json_encode(['status' => 'error', 'message' => 'File không đúng định dạng ảnh hợp lệ!']);
            exit;
        }

        // Tạo tên file ngẫu nhiên bảo mật dựa trên ID user
        $newFileName = 'avatar_' . $current_user['id'] . '_' . uniqid() . '.' . $fileExtension;
        $targetFilePath = $targetDir . $newFileName;

        // Tiến hành di chuyển file từ thư mục tạm sang thư mục upload chính thức
        if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {

            // Chỉ lưu tên file trần để đồng bộ cấu hình nối chuỗi hiển thị của header.php
            $dbPath = $newFileName;

            if (function_exists('user_update_avatar')) {
                $updateResult = user_update_avatar($current_user['id'], $dbPath);

                if ($updateResult) {
                    // Cập nhật lại session hiển thị nhanh cho website
                    $_SESSION['user']['avatar'] = $dbPath;

                    // Đường dẫn trả về để Front-end JS thay src xem trước chính xác nhất
                    $browserUrl = 'uploads/avatars/' . $newFileName;

                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Cập nhật ảnh đại diện thành công!',
                        'url' => $browserUrl
                    ]);
                    exit;
                }
            }
            echo json_encode(['status' => 'error', 'message' => 'Đã lưu file vật lý nhưng không cập nhật được Database.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi hệ thống! Không thể ghi nhớ file lên Server.']);
        }
        exit;
    }

    public function change_password()
    {
        if (!auth_is_logged_in()) {
            header("Location: index.php?action=login");
            exit;
        }

        $current_user = auth_get_current_user();

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $current_password = trim($_POST['current_password'] ?? '');
            $new_password = trim($_POST['new_password'] ?? '');
            $confirm_new_password = trim($_POST['confirm_password'] ?? '');

            if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
                $error = "Vui lòng điền đầy đủ tất cả các trường thông tin!";
            } else if ($new_password !== $confirm_new_password) {
                $error = "Mật khẩu mới và xác nhận mật khẩu mới không khớp!";
            } else if (strlen($new_password) < 8) {
                $error = "Mật khẩu mới phải có ít nhất 8 ký tự!";
            } else {
                $user = user_get_by_id($current_user['id']);
                if ($user && password_verify($current_password, $user['password'])) {
                    $result = user_update_password($current_user['id'], $new_password);
                    if ($result) {
                        $success = "Đổi mật khẩu thành công!";
                    } else {
                        $error = "Đổi mật khẩu thất bại! Vui lòng thử lại.";
                    }
                } else {
                    $error = "Mật khẩu hiện tại không đúng!";
                }
            }
        }

        $pageTitle = "Đổi Mật Khẩu";
        require_once __DIR__ . '/../view/client/change-password.php';
    }
    public function admin_list_users()
    {
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $role = isset($_GET['role']) ? $_GET['role'] : '';
        $status = isset($_GET['status']) ? $_GET['status'] : '';

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;

        $totalUsers = user_count($search, $role, $status);
        $totalPages = max(1, (int) ceil($totalUsers / $perPage));

        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;
        $from = ($page - 1) * $perPage + 1;
        $to = min($page * $perPage, $totalUsers);

        $orderBy = "id DESC";
        switch ($sort) {
            case 'oldest':
                $orderBy = "id ASC";
                break;
            case 'username_asc':
                $orderBy = "username ASC";
                break;
            case 'username_desc':
                $orderBy = "username DESC";
                break;
            case 'newest':
            default:
                $orderBy = "id DESC";
                break;
        }
        $users = user_get_all($search, $role, $status, $orderBy);
        $pageTitle = "Quản Lý Người Dùng";
        require_once __DIR__ . '/../view/admin/User/user-list.php';
    }
    public function admin_lock_user()
    {
        if (!isset($_GET['id'])) {
            header("Location: index.php?action=admin-user-list");
            exit;
        }

        $user_id = (int) $_GET['id'];
        user_update_status($user_id, 0);
        header("Location: index.php?action=admin-user-list");
        exit;
    }
    public function admin_unlock_user()
    {
        if (!isset($_GET['id'])) {
            header("Location: index.php?action=admin-user-list");
            exit;
        }

        $user_id = (int) $_GET['id'];
        user_update_status($user_id, 1);
        header("Location: index.php?action=admin-user-list");
        exit;
    }
    public function favorites()
    {
        // Bắt buộc đăng nhập để xem trang này
        if (!isset($_SESSION['user']['id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        $user_id = $_SESSION['user']['id'];

        // Gọi Model lấy danh sách truyện đã yêu thích
        $favoriteComics = follow_get_list_comics_by_user($user_id);

        $pageTitle = "Truyện yêu thích - MANGATIN";

        // Khai báo file view hiển thị giao diện bên dưới
        include_once __DIR__ . '/../view/client/favorites.php';
    }
    public function delete_history_item()
    {
        if (!isset($_SESSION['user']['id'])) {
            header("Location: index.php?action=login");
            exit;
        }
        $comic_id = (int) ($_GET['comic_id'] ?? 0);
        history_delete_single($_SESSION['user']['id'], $comic_id);
        header("Location: index.php?action=history");
        exit;
    }

    public function clear_all_history()
    {
        if (!isset($_SESSION['user']['id'])) {
            header("Location: index.php?action=login");
            exit;
        }
        history_clear_all($_SESSION['user']['id']);
        header("Location: index.php?action=history");
        exit;
    }
}