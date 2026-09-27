<?php
// model/DashboardModel.php
require_once __DIR__ . '/../libs/pdo.php';

/**
 * 6.1.1 Thống kê lượt đọc (Tổng toàn bộ lượt xem của các chương truyện)
 */
function dashboard_get_total_views()
{
    // Lấy tổng view_count từ tất cả các chương thuộc bảng chapters
    $sql = "SELECT SUM(view_count) AS total_views FROM chapters";
    $result = pdo_getOne($sql);
    return $result && isset($result['total_views']) ? (int) $result['total_views'] : 0;
}

/**
 * 6.1.2 Thống kê truyện phổ biến (Top truyện có lượt xem nhiều nhất)
 */
function dashboard_get_popular_comics($limit = 5)
{
    // Tính tổng lượt xem từ các chương của mỗi truyện, đồng thời đếm số lượng theo dõi
    $sql = "SELECT c.id, c.title, c.thumbnail,
            (SELECT IFNULL(SUM(ch.view_count), 0) FROM chapters ch WHERE ch.comic_id = c.id) AS total_views,
            (SELECT COUNT(*) FROM follows f WHERE f.comic_id = c.id) AS follow_count
            FROM comics c
            ORDER BY total_views DESC, follow_count DESC
            LIMIT " . (int) $limit;

    return pdo_getAll($sql);
}

/**
 * 6.1.3 Thống kê người dùng (Tổng số, đang hoạt động, bị khóa)
 */
function dashboard_get_user_stats()
{
    $sqlTotal = "SELECT COUNT(*) AS total FROM users";
    $sqlActive = "SELECT COUNT(*) AS active FROM users WHERE status = 1";
    $sqlLocked = "SELECT COUNT(*) AS locked FROM users WHERE status = 0";

    $total = pdo_getOne($sqlTotal);
    $active = pdo_getOne($sqlActive);
    $locked = pdo_getOne($sqlLocked);

    return [
        'total' => $total ? (int) current($total) : 0,
        'active' => $active ? (int) current($active) : 0,
        'locked' => $locked ? (int) current($locked) : 0
    ];
}