<?php
require_once __DIR__ . '/../libs/pdo.php';

function chapter_get_all_by_comic_id($comic_id)
{
    $sql = "SELECT * FROM chapters WHERE comic_id = ? ORDER BY chapter_number DESC";
    return pdo_getAll($sql, (int) $comic_id);
}
function chapter_add($comic_id, $chapter_number, $chapter_title)
{
    $sql = "INSERT INTO chapters (comic_id, chapter_number, chapter_title) VALUES (?, ?, ?)";

    return pdo_insert($sql, (int) $comic_id, (float) $chapter_number, $chapter_title);
}
// Lấy chi tiết thông tin của 1 chương dựa vào ID
function chapter_get_by_id($id)
{
    $sql = "SELECT * FROM chapters WHERE id = ?";
    return pdo_getOne($sql, (int) $id);
}

// Lấy tất cả danh sách ảnh (trang truyện) thuộc về 1 chương, sắp xếp theo thứ tự trang
function chapter_images_get_by_chapter_id($chapter_id)
{
    $sql = "SELECT * FROM chapter_images WHERE chapter_id = ? ORDER BY order_number ASC";
    return pdo_getAll($sql, (int) $chapter_id);
}

// Cập nhật thông tin cơ bản của chương (số chương, tên chương)
function chapter_update($id, $chapter_number, $chapter_title)
{
    $sql = "UPDATE chapters SET chapter_number = ?, chapter_title = ? WHERE id = ?";
    return pdo_execute($sql, (float) $chapter_number, $chapter_title, (int) $id);
}

// Xóa toàn bộ ảnh của một chương trong cơ sở dữ liệu để chuẩn bị ghi đè (nếu Admin upload bộ ảnh mới)
function chapter_images_delete_by_chapter_id($chapter_id)
{
    $sql = "DELETE FROM chapter_images WHERE chapter_id = ?";
    return pdo_execute($sql, (int) $chapter_id);
}
function chapter_delete($id)
{
    // Trước khi xóa chương, cần xóa tất cả ảnh thuộc về chương đó để tránh rác dữ liệu
    chapter_images_delete_by_chapter_id($id);

    $sql = "DELETE FROM chapters WHERE id = ?";
    return pdo_execute($sql, (int) $id);
}
function chapter_image_add($chapter_id, $image_url, $order_number)
{
    $sql = "INSERT INTO chapter_images (chapter_id, image_url, order_number) VALUES (?, ?, ?)";
    return pdo_execute($sql, (int) $chapter_id, $image_url, (int) $order_number);
}
function chapter_count_by_comic_id($comic_id, $search = '')
{
    $sql = "SELECT COUNT(*) FROM chapters WHERE comic_id = ?";
    $params = [(int) $comic_id];

    if (!empty($search)) {
        $sql .= " AND chapter_title LIKE ?";
        $params[] = "%$search%";
    }

    $result = pdo_getOne($sql, ...$params);

    // LẤY LOGIC PHÒNG NGỰ: Nếu $result là mảng, lấy giá trị đầu tiên. Nếu không, ép kiểu int thẳng.
    if (is_array($result)) {
        return (int) reset($result);
    }

    return (int) $result;
}

function chapter_get_all_with_filter($comic_id, $search = '', $orderBy = 'chapter_number DESC', $limit = 10, $offset = 0)
{
    $sql = "SELECT * FROM chapters WHERE comic_id = ?";
    $params = [(int) $comic_id];

    if (!empty($search)) {
        $sql .= " AND chapter_title LIKE ?";
        $params[] = "%$search%";
    }

    // Đảm bảo dữ liệu sắp xếp an toàn
    $allowedSort = ['chapter_number DESC', 'chapter_number ASC', 'created_at DESC', 'created_at ASC'];
    if (!in_array($orderBy, $allowedSort)) {
        $orderBy = 'chapter_number DESC';
    }

    $sql .= " ORDER BY {$orderBy} LIMIT " . (int) $limit . " OFFSET " . (int) $offset;

    return pdo_getAll($sql, ...$params);
}

function check_duplicate_chapter($chapter_number, $comic_id)
{
    $sql = "SELECT COUNT(*) FROM chapters WHERE comic_id = ? AND chapter_number = ?";
    return pdo_getValue($sql, (int) $comic_id, (float) $chapter_number) > 0;
}
/**
 * Tăng số lượt xem của một chương truyện dựa vào ID
 * @param int $chapter_id
 * @return bool
 */
function chapter_increment_view_count_secure($chapter_id)
{
    $sql = "UPDATE chapters SET view_count = view_count + 1 WHERE id = ?";
    return pdo_execute($sql, (int) $chapter_id);
}