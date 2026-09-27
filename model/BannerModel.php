<?php
// model/BannerModel.php
require_once __DIR__ . '/../libs/pdo.php';

/**
 * Lấy danh sách banner đang hoạt động (status = 1) hiển thị ngoài Client
 * @return array
 */
function banner_get_active()
{
    $sql = "SELECT * FROM `banners` WHERE `status` = 1 ORDER BY `id` DESC";
    return pdo_getAll($sql);
}

/**
 * Lấy toàn bộ danh sách banner (cả ẩn và hiện) cho trang quản trị Admin
 * @return array
 */
function banner_get_all()
{
    $sql = "SELECT * FROM `banners` ORDER BY `id` DESC";
    return pdo_getAll($sql);
}

/**
 * Lấy thông tin chi tiết của một banner theo ID
 * @param int $id
 * @return array|bool
 */
function banner_get_by_id($id)
{
    $sql = "SELECT * FROM `banners` WHERE `id` = ?";
    return pdo_getOne($sql, $id);
}

/**
 * Thêm mới một banner vào cơ sở dữ liệu
 * @param string $title
 * @param string $image_url
 * @param string $link_url
 * @param int $status
 * @return string|int ID của banner vừa tạo
 */
function banner_insert($title, $image_url, $link_url, $status)
{
    $sql = "INSERT INTO `banners` (`title`, `image_url`, `link_url`, `status`) VALUES (?, ?, ?, ?)";
    return pdo_insert($sql, $title, $image_url, $link_url, $status);
}

/**
 * Cập nhật thông tin banner theo ID
 * @param int $id
 * @param string $title
 * @param string $image_url
 * @param string $link_url
 * @param int $status
 * @return PDOStatement
 */
function banner_update($id, $title, $image_url, $link_url, $status)
{
    $sql = "UPDATE `banners` SET `title` = ?, `image_url` = ?, `link_url` = ?, `status` = ? WHERE `id` = ?";
    return pdo_execute($sql, $title, $image_url, $link_url, $status, $id);
}

/**
 * Xóa một banner khỏi cơ sở dữ liệu
 * @param int $id
 * @return PDOStatement
 */
function banner_delete($id)
{
    $sql = "DELETE FROM `banners` WHERE `id` = ?";
    return pdo_execute($sql, $id);
}