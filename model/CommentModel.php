<?php
require_once __DIR__ . '/../libs/pdo.php';

// Lấy danh sách bình luận của truyện hoặc chương cụ thể (kèm thông tin User)
function comment_get_by_context($comic_id, $chapter_id = null, $limit = 10, $offset = 0)
{
    $sql = "SELECT c.*, u.display_name, u.avatar 
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.comic_id = ?";
    $params = [(int) $comic_id];

    if ($chapter_id !== null) {
        $sql .= " AND c.chapter_id = ?";
        $params[] = (int) $chapter_id;
    } else {
        $sql .= " AND c.chapter_id IS NULL";
    }

    $sql .= " ORDER BY c.created_at DESC LIMIT " . (int) $limit . " OFFSET " . (int) $offset;
    return pdo_getAll($sql, ...$params);
}

// Thêm một bình luận mới
function comment_add($user_id, $comic_id, $chapter_id, $content)
{
    $sql = "INSERT INTO comments (user_id, comic_id, chapter_id, content) VALUES (?, ?, ?, ?)";
    $chap = ($chapter_id > 0) ? (int) $chapter_id : null;
    return pdo_execute($sql, (int) $user_id, (int) $comic_id, $chap, trim($content));
}
// Lấy danh sách bình luận mới nhất trên toàn bộ website (Hiển thị Widget trang chủ)
function comment_get_newest_global($limit = 5)
{
    $sql = "SELECT c.*, u.display_name, u.avatar, c.comic_id, comic.title as comic_title 
            FROM comments c
            JOIN users u ON c.user_id = u.id
            JOIN comics comic ON c.comic_id = comic.id
            ORDER BY c.created_at DESC 
            LIMIT " . (int) $limit;
    return pdo_getAll($sql);
}
/**
 * Đếm tổng số bình luận toàn hệ thống phục vụ phân trang Admin (Hỗ trợ lọc theo từ khóa tiêu cực)
 */
function comment_count_global($keyword = '')
{
    $sql = "SELECT COUNT(*) as total FROM comments WHERE 1=1";
    $params = [];
    if (!empty($keyword)) {
        $sql .= " AND content LIKE ?";
        $params[] = "%" . $keyword . "%";
    }
    $result = pdo_getOne($sql, ...$params);
    return (int) ($result['total'] ?? 0);
}

/**
 * Lấy danh sách bình luận chi tiết toàn cục hỗ trợ giao diện Admin
 */
function comment_get_all_global($keyword = '', $limit = 20, $offset = 0)
{
    $sql = "SELECT c.*, u.display_name, u.username, comic.title as comic_title, ch.chapter_number
            FROM comments c
            JOIN users u ON c.user_id = u.id
            JOIN comics comic ON c.comic_id = comic.id
            LEFT JOIN chapters ch ON c.chapter_id = ch.id WHERE 1=1";
    $params = [];

    if (!empty($keyword)) {
        $sql .= " AND (c.content LIKE ? OR u.display_name LIKE ? OR comic.title LIKE ?)";
        $params[] = "%" . $keyword . "%";
        $params[] = "%" . $keyword . "%";
        $params[] = "%" . $keyword . "%";
    }

    $sql .= " ORDER BY c.created_at DESC LIMIT " . (int) $limit . " OFFSET " . (int) $offset;
    return pdo_getAll($sql, ...$params);
}

/**
 * Thực thi lệnh xóa vĩnh viễn 1 bình luận tiêu cực
 */
function comment_delete_by_id($id)
{
    $sql = "DELETE FROM comments WHERE id = ?";
    return pdo_execute($sql, (int) $id);
}