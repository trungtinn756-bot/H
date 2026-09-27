<?php
require_once __DIR__ . '/../libs/pdo.php';

/**
 * Lấy danh sách thông báo (Hệ thống + Cá nhân của user)
 */
function notification_get_all_by_user($user_id, $limit = 5)
{
    // Nếu user chưa đăng nhập ($user_id = 0), chỉ lấy thông báo hệ thống chung (user_id IS NULL)
    if ($user_id <= 0) {
        $sql = "SELECT * FROM notifications WHERE user_id IS NULL ORDER BY created_at DESC LIMIT ?";
        return pdo_getAll($sql, (int) $limit);
    }

    $sql = "SELECT * FROM notifications 
            WHERE user_id = ? OR user_id IS NULL 
            ORDER BY created_at DESC LIMIT ?";
    return pdo_getAll($sql, (int) $user_id, (int) $limit);
}

/**
 * Đếm số lượng thông báo chưa đọc
 */
function notification_count_unread($user_id)
{
    if ($user_id <= 0) {
        return 0;
    }
    // Sử dụng subquery LIMIT 1000 để MySQL dừng quét ngay khi đạt đủ 1000 dòng chưa đọc
    $sql = "SELECT COUNT(*) as unread FROM (
                SELECT 1 FROM notifications 
                WHERE (user_id = ? OR user_id IS NULL) AND is_read = 0 
                LIMIT 1000
            ) as temp";
    return pdo_getOne($sql, (int) $user_id)['unread'] ?? 0;
}

/**
 * Thêm thông báo tự động khi đăng chương mới
 */
function notification_add_new_chapter($comic_title, $chapter_number, $chapter_id)
{
    $title = "🔥 Chương mới cập nhật!";
    $content = "Bộ truyện \"" . $comic_title . "\" vừa ra mắt Chương " . $chapter_number . ". Đọc ngay!";
    $link = "index.php?action=reader&id=" . $chapter_id;
    $type = "new_chapter";

    $sql = "INSERT INTO notifications (user_id, title, content, link, type) VALUES (NULL, ?, ?, ?, ?)";
    return pdo_execute($sql, $title, $content, $link, $type);
}
/**
 * Đánh dấu một thông báo cụ thể là đã đọc dựa trên link hoặc ID chương truyện
 */
function notification_mark_as_read_by_link($user_id, $link)
{
    if ($user_id > 0) {
        // Chỉ update nếu là thông báo đích danh của user đó
        $sql = "UPDATE notifications SET is_read = 1 WHERE link = ? AND user_id = ?";
        return pdo_execute($sql, $link, (int) $user_id);
    }
    return false;
}
/**
 * Đánh dấu một thông báo cụ thể là đã đọc bằng ID thông báo
 */
function notification_mark_as_read_by_id($noti_id)
{
    $sql = "UPDATE notifications SET is_read = 1 WHERE id = ?";
    return pdo_execute($sql, (int) $noti_id);
}