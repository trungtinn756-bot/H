<?php
require_once __DIR__ . '/../libs/pdo.php';

/**
 * Kiểm tra xem người dùng đã theo dõi bộ truyện này chưa
 */
function follow_check($user_id, $comic_id)
{
    $sql = "SELECT COUNT(*) as total FROM follows WHERE user_id = ? AND comic_id = ?";
    $result = pdo_getOne($sql, (int) $user_id, (int) $comic_id);
    return ($result['total'] ?? 0) > 0;
}

/**
 * Thêm truyện vào danh sách theo dõi
 */
function follow_add($user_id, $comic_id)
{
    if (follow_check($user_id, $comic_id)) {
        return false; // Đã theo dõi rồi
    }
    $sql = "INSERT INTO follows (user_id, comic_id) VALUES (?, ?)";
    return pdo_execute($sql, (int) $user_id, (int) $comic_id);
}

/**
 * Hủy theo dõi truyện
 */
function follow_delete($user_id, $comic_id)
{
    $sql = "DELETE FROM follows WHERE user_id = ? AND comic_id = ?";
    return pdo_execute($sql, (int) $user_id, (int) $comic_id);
}

/**
 * Lấy danh sách tất cả các truyện mà user đang theo dõi
 */
function follow_get_all_by_user($user_id)
{
    $sql = "SELECT c.*, 
            (SELECT MAX(ch.chapter_number) FROM chapters ch WHERE ch.comic_id = c.id) as latest_chapter
            FROM comics c
            JOIN follows f ON c.id = f.comic_id
            WHERE f.user_id = ?
            ORDER BY f.created_at DESC";
    return pdo_getAll($sql, (int) $user_id);
}
/**
 * Lấy danh sách truyện mà người dùng đang theo dõi/yêu thích kèm chương mới nhất
 */
function follow_get_list_comics_by_user($user_id)
{
    $sql = "SELECT c.*, 
            (SELECT MAX(ch.chapter_number) FROM chapters ch WHERE ch.comic_id = c.id) as latest_chapter
            FROM comics c
            JOIN follows f ON c.id = f.comic_id
            WHERE f.user_id = ?
            ORDER BY f.created_at DESC";
    return pdo_getAll($sql, (int) $user_id);
}