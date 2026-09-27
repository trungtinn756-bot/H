<?php
require_once __DIR__ . '/../libs/pdo.php';

/**
 * Đếm tổng số thể loại dựa trên từ khóa tìm kiếm
 */
function genre_count(string $search = ''): int
{
    if ($search !== '') {
        $sql = "SELECT COUNT(*) FROM genres WHERE genre_name LIKE ? OR description LIKE ?";
        return (int) pdo_getValue($sql, "%$search%", "%$search%");
    }
    $sql = "SELECT COUNT(*) FROM genres";
    return (int) pdo_getValue($sql);
}

/**
 * Lấy danh sách thể loại có phân trang, tìm kiếm, sắp xếp từ A -> Z và đếm số lượng truyện
 */
function genre_get_all(int $limit, int $offset, string $search = ''): array
{
    if ($search !== '') {
        // Sử dụng LEFT JOIN và GROUP BY id để đếm số truyện (comic_count) của mỗi thể loại
        $sql = "SELECT g.*, COUNT(cg.comic_id) AS comic_count 
                FROM genres g
                LEFT JOIN comics_genres cg ON g.id = cg.genre_id
                WHERE g.genre_name LIKE ? OR g.description LIKE ? 
                GROUP BY g.id
                ORDER BY g.genre_name ASC 
                LIMIT $limit OFFSET $offset";
        return pdo_getAll($sql, "%$search%", "%$search%");
    }

    $sql = "SELECT g.*, COUNT(cg.comic_id) AS comic_count 
            FROM genres g
            LEFT JOIN comics_genres cg ON g.id = cg.genre_id
            GROUP BY g.id
            ORDER BY g.genre_name ASC 
            LIMIT $limit OFFSET $offset";
    return pdo_getAll($sql);
}

/**
 * Lấy thông tin một thể loại qua ID
 */
function genre_get_by_id(int $id): ?array
{
    $sql = "SELECT * FROM genres WHERE id = ?";
    $result = pdo_getOne($sql, $id);
    return $result ?: null;
}

/**
 * Kiểm tra trùng lặp Slug (tránh trùng UNIQUE KEY)
 */
function genre_exists_slug(string $slug, int $exclude_id = 0): bool
{
    if ($exclude_id > 0) {
        $sql = "SELECT COUNT(*) FROM genres WHERE slug = ? AND id != ?";
        return (int) pdo_getValue($sql, $slug, $exclude_id) > 0;
    }
    $sql = "SELECT COUNT(*) FROM genres WHERE slug = ?";
    return (int) pdo_getValue($sql, $slug) > 0;
}

/**
 * Thêm thể loại mới
 */
function genre_add(string $genre_name, string $slug, string $description, int $is_menu = 0): bool
{
    $sql = "INSERT INTO genres (genre_name, slug, description, is_menu) VALUES (?, ?, ?, ?)";
    try {
        pdo_execute($sql, $genre_name, $slug, $description, $is_menu);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Cập nhật thể loại
 */
function genre_update(int $id, string $genre_name, string $slug, string $description, int $is_menu = 0): bool
{
    $sql = "UPDATE genres SET genre_name = ?, slug = ?, description = ?, is_menu = ? WHERE id = ?";
    try {
        pdo_execute($sql, $genre_name, $slug, $description, $is_menu, $id);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Xóa thể loại (Hệ thống tự động xóa liên kết ở bảng comics_genres nhờ ON DELETE CASCADE)
 */
function genre_delete(int $id): void
{
    $sql = "DELETE FROM genres WHERE id = ?";
    pdo_execute($sql, $id);
}
function genre_get_for_menu()
{
    // Chỉ lấy các thể loại cha/chính do lập trình viên thiết lập hiển thị ở menu
    $sql = "SELECT g.*, COUNT(cg.comic_id) AS comic_count 
            FROM genres g
            LEFT JOIN comics_genres cg ON g.id = cg.genre_id
            WHERE g.is_menu = 1 
            GROUP BY g.id
            ORDER BY g.genre_name ASC";
    return pdo_getAll($sql);
}