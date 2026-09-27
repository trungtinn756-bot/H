<?php
// controller/ReportController.php
require_once __DIR__ . '/../model/ReportModel.php';

class ReportController
{
    public function post_report()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json; charset=UTF-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Phương thức request không hợp lệ!']);
            exit;
        }

        // Người dùng không bắt buộc đăng nhập để báo lỗi (0 nếu chưa đăng nhập)
        $user_id = $_SESSION['user']['id'] ?? 0;
        $comic_id = (int) ($_POST['comic_id'] ?? 0);
        $chapter_id = (int) ($_POST['chapter_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');

        if ($comic_id <= 0 || $chapter_id <= 0 || empty($content)) {
            echo json_encode(['status' => 'error', 'message' => 'Dữ liệu hoặc nội dung báo lỗi không được để trống!']);
            exit;
        }

        $result = report_add($user_id, $comic_id, $chapter_id, $content);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Gửi báo lỗi thành công! Ban quản trị sẽ sớm kiểm tra và khắc phục.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Hệ thống bận, không thể lưu báo lỗi. Vui lòng thử lại!']);
        }
        exit;
    }
    /**
     * Hiển thị danh sách các báo cáo lỗi truyện bên trang Admin
     */
    public function admin_list_reports()
    {
        // Kiểm tra quyền hạn Admin tại đây nếu cần (Ví dụ: if($_SESSION['user']['role'] !== '1') exit;)

        $status = $_GET['status'] ?? 'all'; // Bộ lọc: all, 0 (Chưa xử lý), 1 (Đã sửa)
        $perPage = 15;
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $filterStatus = ($status === 'all') ? null : $status;
        $totalReports = report_count($filterStatus);
        $totalPages = max(1, (int) ceil($totalReports / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $reports = report_get_all($filterStatus, $perPage, $offset);
        $pageTitle = "Quản Lý Báo Lỗi Chương";

        // Gọi file view (Sẽ tạo ở bước 3)
        require_once __DIR__ . '/../view/admin/Report/report-list.php';
    }

    /**
     * Xử lý đánh dấu báo cáo lỗi đã được khắc phục xong
     */
    public function admin_resolve_report()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $statusRedirect = $_GET['status_filter'] ?? 'all';
        $pageRedirect = $_GET['page'] ?? 1;

        if ($id > 0) {
            report_update_status($id, 1); // Cập nhật status = 1 (Đã sửa)
        }
        header("Location: index.php?action=admin-report-list&status={$statusRedirect}&page={$pageRedirect}&success=resolved");
        exit;
    }

    /**
     * Xóa bỏ báo cáo lỗi nếu kiểm tra thấy chương vẫn bình thường
     */
    public function admin_delete_report()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $statusRedirect = $_GET['status_filter'] ?? 'all';
        $pageRedirect = $_GET['page'] ?? 1;

        if ($id > 0) {
            report_delete($id);
        }
        header("Location: index.php?action=admin-report-list&status={$statusRedirect}&page={$pageRedirect}&success=deleted");
        exit;
    }
}