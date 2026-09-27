<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../model/FollowModel.php';

class FollowController
{
    public function toggle_follow()
    {
        // Thiết lập header trả về JSON
        header('Content-Type: application/json');

        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user']['id'])) {
            echo json_encode(['status' => 'unauthenticated', 'message' => 'Vui lòng đăng nhập để thực hiện chức năng này!']);
            exit;
        }

        $user_id = $_SESSION['user']['id'];
        $comic_id = isset($_POST['comic_id']) ? (int) $_POST['comic_id'] : 0;

        if ($comic_id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Dữ liệu truyện không hợp lệ!']);
            exit;
        }

        // Kiểm tra tình trạng hiện tại
        $is_following = follow_check($user_id, $comic_id);

        if ($is_following) {
            // Nếu đã theo dõi thì tiến hành HỦY
            follow_delete($user_id, $comic_id);
            echo json_encode([
                'status' => 'success',
                'action' => 'unfollowed',
                'message' => 'Đã hủy theo dõi truyện thành công.'
            ]);
        } else {
            // Nếu chưa theo dõi thì tiến hành THÊM
            follow_add($user_id, $comic_id);
            echo json_encode([
                'status' => 'success',
                'action' => 'followed',
                'message' => 'Đã thêm truyện vào danh sách theo dõi.'
            ]);
        }
        exit;
    }
}