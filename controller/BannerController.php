<?php
// controller/BannerController.php
require_once __DIR__ . '/../libs/auth.php';
require_once __DIR__ . '/../model/BannerModel.php';

class BannerController
{
    public function __construct()
    {
        // Kiểm tra quyền Admin trước khi thực hiện các tác vụ quản trị
        auth_require_admin();
    }

    // Hiển thị danh sách banner trong admin
    public function admin_list()
    {
        $banners = banner_get_all();
        include_once __DIR__ . '/../view/admin/banners/list.php';
    }

    // Hiển thị form thêm mới và xử lý lưu dữ liệu
    public function admin_add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $link_url = trim($_POST['link_url'] ?? '');
            $status = (int) ($_POST['status'] ?? 1);
            $image_url = '';

            // Xử lý upload ảnh banner
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/../uploads/banners/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $filename = time() . '_' . basename($_FILES['image']['name']);
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
                    $image_url = 'uploads/banners/' . $filename;
                }
            }

            if (!empty($image_url)) {
                banner_insert($title, $image_url, $link_url, $status);
                header("Location: index.php?action=admin-banner-list&success=add");
                exit;
            } else {
                $error = "Vui lòng chọn một hình ảnh hợp lệ!";
            }
        }
        include_once __DIR__ . '/../view/admin/banners/add.php';
    }

    // Hiển thị form chỉnh sửa và xử lý cập nhật
    public function admin_edit()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $banner = banner_get_by_id($id);
        if (!$banner) {
            header("Location: index.php?action=admin-banner-list");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $link_url = trim($_POST['link_url'] ?? '');
            $status = (int) ($_POST['status'] ?? 1);
            $image_url = $banner['image_url']; // Giữ lại ảnh cũ mặc định

            // Nếu người dùng upload ảnh mới
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/../uploads/banners/';
                $filename = time() . '_' . basename($_FILES['image']['name']);
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
                    // Xóa ảnh cũ nếu tồn tại
                    if (file_exists(__DIR__ . '/../' . $banner['image_url'])) {
                        @unlink(__DIR__ . '/../' . $banner['image_url']);
                    }
                    $image_url = 'uploads/banners/' . $filename;
                }
            }

            banner_update($id, $title, $image_url, $link_url, $status);
            header("Location: index.php?action=admin-banner-list&success=edit");
            exit;
        }
        include_once __DIR__ . '/../view/admin/banners/edit.php';
    }

    // Xóa banner
    public function admin_delete()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $banner = banner_get_by_id($id);
        if ($banner) {
            if (file_exists(__DIR__ . '/../' . $banner['image_url'])) {
                @unlink(__DIR__ . '/../' . $banner['image_url']);
            }
            banner_delete($id);
        }
        header("Location: index.php?action=admin-banner-list&success=delete");
        exit;
    }
}