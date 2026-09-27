<?php
require_once __DIR__ . '/../model/CommentModel.php';

class CommentController
{
    public function post_comment()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Bắt buộc đăng nhập mới được bình luận
        if (!isset($_SESSION['user'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập để bình luận!']);
            exit;
        }

        $user_id = $_SESSION['user']['id'];
        $comic_id = (int) ($_POST['comic_id'] ?? 0);
        $chapter_id = isset($_POST['chapter_id']) ? (int) $_POST['chapter_id'] : 0;
        $content = trim($_POST['content'] ?? '');

        if ($comic_id <= 0 || empty($content)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Nội dung bình luận không được để trống!']);
            exit;
        }

        $result = comment_add($user_id, $comic_id, $chapter_id, $content);
        if ($result) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Đăng bình luận thành công!']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Không thể gửi bình luận. Vui lòng thử lại.']);
        }
        exit;
    }
    /**
     * Giao diện quản lý danh sách bình luận phía Admin
     */
    public function admin_list_comments()
    {
        // Kiểm tra quyền hạn Admin
        $search = trim($_GET['search'] ?? '');
        $perPage = 15;
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $totalComments = comment_count_global($search);
        $totalPages = max(1, (int) ceil($totalComments / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $comments = comment_get_all_global($search, $perPage, $offset);
        $pageTitle = "Quản Lý Bình Luận";

        require_once __DIR__ . '/../view/admin/Comment/comment-list.php';
    }

    /**
     * Xử lý xóa bình luận tiêu cực
     */
    public function admin_delete_comment()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $searchRedirect = $_GET['search_filter'] ?? '';
        $pageRedirect = $_GET['page'] ?? 1;

        if ($id > 0) {
            comment_delete_by_id($id);
        }

        header("Location: index.php?action=admin-comment-list&search=" . urlencode($searchRedirect) . "&page={$pageRedirect}&success=deleted");
        exit;
    }
}