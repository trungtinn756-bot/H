<?php
require_once __DIR__ . '/../libs/pdo.php';

/**
 * Ghi nhận hoặc cập nhật lịch sử đọc truyện của User
 */
function history_save_location($user_id, $comic_id, $chapter_id)
{
    // Sử dụng tính năng ON DUPLICATE KEY UPDATE của MySQL cực kỳ tối ưu
    $sql = "INSERT INTO reading_history (user_id, comic_id, chapter_id, updated_at) 
            VALUES (?, ?, ?, NOW()) 
            ON DUPLICATE KEY UPDATE chapter_id = ?, updated_at = NOW()";
    return pdo_execute($sql, $user_id, $comic_id, $chapter_id, $chapter_id);
}

/**
 * Lấy danh sách lịch sử đọc của một User cụ thể (Giới hạn số lượng nếu cần)
 */
function history_get_list_by_user($user_id, $limit = null)
{
    $sql = "SELECT h.*, 
                   c.title AS comicTitle, c.thumbnail AS comicThumbnail,
                   ch.chapter_number AS chapterNumber, ch.id AS chapterId
            FROM reading_history h
            JOIN comics c ON h.comic_id = c.id
            JOIN chapters ch ON h.chapter_id = ch.id
            WHERE h.user_id = ?
            ORDER BY h.updated_at DESC";

    if ($limit !== null) {
        $sql .= " LIMIT " . (int) $limit;
    }
    return pdo_getAll($sql, $user_id);
}

/**
 * Xóa một truyện khỏi lịch sử đọc
 */
function history_delete_single($user_id, $comic_id)
{
    $sql = "DELETE FROM reading_history WHERE user_id = ? AND comic_id = ?";
    return pdo_execute($sql, $user_id, $comic_id);
}

/**
 * Xóa toàn bộ lịch sử đọc của một User
 */
function history_clear_all($user_id)
{
    $sql = "DELETE FROM reading_history WHERE user_id = ?";
    return pdo_execute($sql, $user_id);
}