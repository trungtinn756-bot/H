<?php
require_once __DIR__ . '/../libs/pdo.php';

function comic_get_all($keyword, $status, $orderBy, $limit = null, $offset = null)
{
    // CẬP NHẬT: Gom thêm UNIX_TIMESTAMP(sub_ch.created_at) vào chuỗi kết quả
    $sql = "SELECT c.*, 
            (SELECT MAX(ch.chapter_number) FROM chapters ch WHERE ch.comic_id = c.id) as latest_chapter,
            (SELECT IFNULL(SUM(ch2.view_count), 0) FROM chapters ch2 WHERE ch2.comic_id = c.id) as total_views,
            (
                SELECT GROUP_CONCAT(CONCAT(sub_ch.id, '||', sub_ch.chapter_number, '||', sub_ch.chapter_title, '||', UNIX_TIMESTAMP(sub_ch.created_at)) ORDER BY sub_ch.chapter_number DESC SEPARATOR '[split_chap]')
                FROM (
                    SELECT id, comic_id, chapter_number, chapter_title, created_at 
                    FROM chapters 
                    ORDER BY chapter_number DESC
                ) sub_ch 
                WHERE sub_ch.comic_id = c.id
            ) as top_three_chapters
            FROM comics c 
            LEFT JOIN comics_genres cg ON c.id = cg.comic_id
            LEFT JOIN genres g ON cg.genre_id = g.id
            WHERE 1=1";

    $params = [];

    if (!empty($keyword)) {
        $sql .= " AND (c.title LIKE ? OR c.other_title LIKE ? OR c.slug LIKE ? OR c.author LIKE ? OR g.genre_name LIKE ? OR g.slug LIKE ?)";
        $params[] = "%" . $keyword . "%";
        $params[] = "%" . $keyword . "%";
        $params[] = "%" . $keyword . "%";
        $params[] = "%" . $keyword . "%";
        $params[] = "%" . $keyword . "%";
        $params[] = "%" . $keyword . "%";
    }

    if ($status === '0' || $status === '1' || $status === '2') {
        $sql .= " AND c.status = ?";
        $params[] = $status;
    }

    $sql .= " GROUP BY c.id";

    if (!empty($orderBy)) {
        $sql .= " ORDER BY " . $orderBy;
    }

    if ($limit !== null && $offset !== null) {
        $sql .= " LIMIT " . (int) $limit . " OFFSET " . (int) $offset;
    }

    return pdo_getAll($sql, ...$params);
}

function comic_get_latest($limit = 12)
{
    $sql = "SELECT * FROM comics ORDER BY created_at DESC LIMIT ?";
    return pdo_getAll($sql, (int) $limit);
}

function comic_get_by_id($id)
{
    $sql = "SELECT * FROM comics WHERE id = ?";
    return pdo_getOne($sql, (int) $id);
}

function comic_get_by_slug($slug)
{
    $sql = "SELECT * FROM comics WHERE slug = ?";
    return pdo_getOne($sql, $slug);
}

function comic_add($data)
{
    $sql = "INSERT INTO comics (title, other_title, slug, author, summary, thumbnail, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
    return pdo_execute($sql, $data['title'], $data['other_title'], $data['slug'], $data['author'], $data['summary'], $data['thumbnail'], $data['status']);
}
function comic_update($id, $data)
{
    $sql = "UPDATE comics SET title = ?, other_title = ?, slug = ?, author = ?, summary = ?, thumbnail = ?, status = ? WHERE id = ?";
    return pdo_execute($sql, $data['title'], $data['other_title'], $data['slug'], $data['author'], $data['summary'], $data['thumbnail'], $data['status'], (int) $id);
}
function comic_delete($id)
{
    $sql = "DELETE FROM comics WHERE id = ?";
    return pdo_execute($sql, (int) $id);
}

function comic_count($keyword = '', $status = '')
{
    $keyword = trim((string) $keyword);
    $status = trim((string) $status);

    $sql = "SELECT COUNT(DISTINCT c.id) as total 
            FROM comics c
            LEFT JOIN comics_genres cg ON c.id = cg.comic_id
            LEFT JOIN genres g ON cg.genre_id = g.id
            WHERE 1=1";
    $params = [];

    if (!empty($keyword)) {
        $sql .= " AND (c.title LIKE ? OR c.other_title LIKE ? OR c.slug LIKE ? OR c.author LIKE ? OR g.genre_name LIKE ? OR g.slug LIKE ?)";
        $params[] = "%" . $keyword . "%";
        $params[] = "%" . $keyword . "%";
        $params[] = "%" . $keyword . "%";
        $params[] = "%" . $keyword . "%";
        $params[] = "%" . $keyword . "%";
        $params[] = "%" . $keyword . "%";
    }

    if ($status === '0' || $status === '1' || $status === '2') {
        $sql .= " AND c.status = ?";
        $params[] = $status;
    }

    return pdo_getOne($sql, ...$params)['total'] ?? 0;
}
function count_views($comic_id)
{
    $sql = "SELECT SUM(view_count) as total_views 
            FROM chapters 
            WHERE comic_id = ?";
    return pdo_getOne($sql, (int) $comic_id)['total_views'] ?? 0;
}
/**
 * Lấy toàn bộ danh sách thể loại có trong hệ thống (A -> Z)
 */
// Thêm hàm này vào ComicModel.php
function comic_get_all_by_genre($genre_id, $limit = null, $offset = null)
{
    $sql = "SELECT c.*, 
            (SELECT MAX(ch.chapter_number) FROM chapters ch WHERE ch.comic_id = c.id) as latest_chapter,
            (SELECT IFNULL(SUM(ch2.view_count), 0) FROM chapters ch2 WHERE ch2.comic_id = c.id) as total_views,
            (
                SELECT GROUP_CONCAT(CONCAT(sub_ch.id, '||', sub_ch.chapter_number, '||', sub_ch.chapter_title, '||', UNIX_TIMESTAMP(sub_ch.created_at)) ORDER BY sub_ch.chapter_number DESC SEPARATOR '[split_chap]')
                FROM (
                    SELECT id, comic_id, chapter_number, chapter_title, created_at 
                    FROM chapters 
                    ORDER BY chapter_number DESC
                ) sub_ch 
                WHERE sub_ch.comic_id = c.id
            ) as top_three_chapters
            FROM comics c 
            JOIN comics_genres cg ON c.id = cg.comic_id
            WHERE cg.genre_id = ?
            GROUP BY c.id
            ORDER BY c.id DESC";

    // Thay vì nối chuỗi, ta đẩy biến vào hệ thống thực thi an toàn hơn nếu pdo hỗ trợ binding limit
    if ($limit !== null && $offset !== null) {
        $sql .= " LIMIT " . (int) $limit . " OFFSET " . (int) $offset;
    }

    return pdo_getAll($sql, $genre_id);
}
// Bổ sung hàm này vào cuối file ComicModel.php nếu chưa có
function comic_get_all_genres()
{
    $sql = "SELECT id, genre_name, slug FROM genres ORDER BY genre_name ASC";
    return pdo_getAll($sql);
}

/**
 * Lấy danh sách ID các thể loại thuộc về một bộ truyện cụ thể
 */
function comic_get_genre_ids_by_comic_id(int $comic_id): array
{
    $sql = "SELECT genre_id FROM comics_genres WHERE comic_id = ?";
    $rows = pdo_getAll($sql, $comic_id);
    return array_column($rows, 'genre_id');
}

/**
 * Cập nhật mối quan hệ Thể loại cho Truyện tranh (Xóa cũ - Thêm mới)
 */
function comic_update_genres(int $comic_id, array $genre_ids): void
{
    // 1. Xóa toàn bộ liên kết thể loại cũ của bộ truyện này
    $sqlDelete = "DELETE FROM comics_genres WHERE comic_id = ?";
    pdo_execute($sqlDelete, $comic_id);

    // 2. Thêm lại các liên kết thể loại mới được chọn
    if (!empty($genre_ids)) {
        $sqlInsert = "INSERT INTO comics_genres (comic_id, genre_id) VALUES (?, ?)";
        foreach ($genre_ids as $genre_id) {
            pdo_execute($sqlInsert, $comic_id, (int) $genre_id);
        }
    }
}
function comic_advanced_filter_count($keyword = '', $status = 'all', $chapter_count = 'all', $genre_ids = [])
{
    $sql = "SELECT COUNT(*) as total FROM comics c WHERE 1=1";
    $params = [];

    $keyword = trim((string) $keyword);
    if ($keyword !== '') {
        $likeKeyword = '%' . $keyword . '%';
        $sql .= " AND (c.title LIKE ? OR c.other_title LIKE ? OR c.slug LIKE ? OR c.author LIKE ?)";
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
    }

    if ($status === 'ongoing') {
        $sql .= " AND c.status = ?";
        $params[] = '0';
    } elseif ($status === 'completed') {
        $sql .= " AND c.status = ?";
        $params[] = '1';
    } elseif ($status === 'pause') { // 🌟 Đã bổ sung lọc Tạm ngưng
        $sql .= " AND c.status = ?";
        $params[] = '2';
    }

    $genre_ids = array_values(array_unique(array_filter(array_map('intval', (array) $genre_ids), function ($id) {
        return $id > 0;
    })));
    if (!empty($genre_ids)) {
        $placeholders = implode(',', array_fill(0, count($genre_ids), '?'));
        $sql .= " AND c.id IN (
            SELECT cg2.comic_id
            FROM comics_genres cg2
            WHERE cg2.genre_id IN ($placeholders)
            GROUP BY cg2.comic_id
            HAVING COUNT(DISTINCT cg2.genre_id) = ?
        )";

        foreach ($genre_ids as $genre_id) {
            $params[] = $genre_id;
        }
        $params[] = count($genre_ids);
    }

    // 🌟 Đã đồng bộ chính xác khoảng số lượng chương theo đúng View & hàm get_all
    if ($chapter_count !== 'all') {
        $sql .= " AND (SELECT COUNT(*) FROM chapters ch WHERE ch.comic_id = c.id) ";
        if ($chapter_count === '1') {
            $sql .= " BETWEEN 1 AND 10";
        } elseif ($chapter_count === '2') {
            $sql .= " BETWEEN 11 AND 50";
        } elseif ($chapter_count === '3') {
            $sql .= " BETWEEN 51 AND 100";
        } elseif ($chapter_count === '4') {
            $sql .= " > 100";
        }
    }

    return (int) (pdo_getOne($sql, ...$params)['total'] ?? 0);
}

function comic_advanced_filter_get_all($keyword = '', $status = 'all', $chapter_count = 'all', $genre_ids = [], $sort = 'update_desc', $limit = 12, $offset = 0)
{
    $sql = "SELECT c.*, 
            (SELECT MAX(ch.chapter_number) FROM chapters ch WHERE ch.comic_id = c.id) as latest_chapter,
            (SELECT IFNULL(SUM(ch2.view_count), 0) FROM chapters ch2 WHERE ch2.comic_id = c.id) as total_views,
            (SELECT MAX(ch3.created_at) FROM chapters ch3 WHERE ch3.comic_id = c.id) as last_update,
            (SELECT COUNT(*) FROM chapters ch4 WHERE ch4.comic_id = c.id) as total_chapters
            FROM comics c
            WHERE 1=1";
    $params = [];

    $keyword = trim((string) $keyword);
    if ($keyword !== '') {
        $likeKeyword = '%' . $keyword . '%';
        $sql .= " AND (c.title LIKE ? OR c.other_title LIKE ? OR c.slug LIKE ? OR c.author LIKE ?)";
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
        $params[] = $likeKeyword;
    }

    if ($status === 'ongoing') {
        $sql .= " AND c.status = ?";
        $params[] = '0';
    } elseif ($status === 'completed') {
        $sql .= " AND c.status = ?";
        $params[] = '1';
    } elseif ($status === 'pause') { // 🌟 BỔ SUNG THÊM TRẠNG THÁI TẠM NGỪNG
        $sql .= " AND c.status = ?";
        $params[] = '2';
    }

    $genre_ids = array_values(array_unique(array_filter(array_map('intval', (array) $genre_ids), function ($id) {
        return $id > 0;
    })));
    if (!empty($genre_ids)) {
        $placeholders = implode(',', array_fill(0, count($genre_ids), '?'));
        $sql .= " AND c.id IN (
            SELECT cg2.comic_id
            FROM comics_genres cg2
            WHERE cg2.genre_id IN ($placeholders)
            GROUP BY cg2.comic_id
            HAVING COUNT(DISTINCT cg2.genre_id) = ?
        )";

        foreach ($genre_ids as $genre_id) {
            $params[] = $genre_id;
        }
        $params[] = count($genre_ids);
    }

    if ($chapter_count !== 'all') {
        $sql .= " AND (SELECT COUNT(*) FROM chapters ch WHERE ch.comic_id = c.id) ";
        if ($chapter_count === '1') {
            $sql .= " BETWEEN 1 AND 10";
        } elseif ($chapter_count === '2') {
            $sql .= " BETWEEN 11 AND 50";
        } elseif ($chapter_count === '3') {
            $sql .= " BETWEEN 50 AND 100";
        } elseif ($chapter_count === '4') {
            $sql .= " > 100";
        }
    }

    if ($sort === 'new_desc') {
        $sql .= " ORDER BY c.id DESC";
    } elseif ($sort === 'view_desc') {
        $sql .= " ORDER BY total_views DESC, c.id DESC";
    } elseif ($sort === 'name_asc') {
        $sql .= " ORDER BY c.title ASC, c.id DESC";
    } else {
        $sql .= " ORDER BY last_update DESC, c.id DESC";
    }

    $sql .= " LIMIT " . (int) $limit . " OFFSET " . (int) $offset;
    return pdo_getAll($sql, ...$params);
}