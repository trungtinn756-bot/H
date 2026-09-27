<?php
// model/ReportModel.php
require_once __DIR__ . '/../libs/pdo.php';

/**
 * Thêm mới một yêu cầu báo lỗi chương
 */
function report_add($user_id, $comic_id, $chapter_id, $content)
{
    $sql = "INSERT INTO chapter_reports (user_id, comic_id, chapter_id, content) VALUES (?, ?, ?, ?)";
    $uid = ($user_id > 0) ? (int) $user_id : null;
    return pdo_execute($sql, $uid, (int) $comic_id, (int) $chapter_id, trim($content));
}

/**
 * Lấy danh sách báo lỗi dành cho trang quản trị (Admin)
 */
function report_get_all($status = null, $limit = 20, $offset = 0)
{
    $sql = "SELECT r.*, c.title as comic_title, ch.chapter_number, u.display_name 
            FROM chapter_reports r
            JOIN comics c ON r.comic_id = c.id
            JOIN chapters ch ON r.chapter_id = ch.id
            LEFT JOIN users u ON r.user_id = u.id WHERE 1=1";
    $params = [];

    if ($status !== null && $status !== 'all') {
        $sql .= " AND r.status = ?";
        $params[] = (int) $status;
    }

    $sql .= " ORDER BY r.created_at DESC LIMIT " . (int) $limit . " OFFSET " . (int) $offset;
    return pdo_getAll($sql, ...$params);
}
/**
 * Đếm tổng số lượng báo cáo lỗi (hỗ trợ bộ lọc trạng thái)
 */
function report_count($status = null)
{
    $sql = "SELECT COUNT(*) as total FROM chapter_reports WHERE 1=1";
    $params = [];
    if ($status !== null && $status !== 'all') {
        $sql .= " AND status = ?";
        $params[] = (int) $status;
    }
    $result = pdo_getOne($sql, ...$params);
    return (int) ($result['total'] ?? 0);
}

/**
 * Cập nhật trạng thái xử lý của một báo cáo (Ví dụ: Đổi từ 0 thành 1 - Đã sửa lỗi)
 */
function report_update_status($id, $status)
{
    $sql = "UPDATE chapter_reports SET status = ? WHERE id = ?";
    return pdo_execute($sql, (int) $status, (int) $id);
}

/**
 * Xóa báo cáo lỗi ra khỏi hệ thống nếu là báo cáo rác/sai sự thật
 */
function report_delete($id)
{
    $sql = "DELETE FROM chapter_reports WHERE id = ?";
    return pdo_execute($sql, (int) $id);
}