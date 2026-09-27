<?php
require_once __DIR__ . '/../libs/auth.php';
require_once __DIR__ . '/../model/GenreModel.php';
require_once __DIR__ . '/../libs/helper.php';

class GenreController
{
    public function __construct()
    {
        // Đảm bảo chỉ Admin mới có thể thao tác với nghiệp vụ này
        auth_require_admin();
    }

    /**
     * Hiển thị danh sách thể loại & Form xử lý Thêm/Sửa
     */
    public function admin_list_genres()
    {
        $search = trim($_GET['search'] ?? '');
        $perPage = 10;
        $page = max(1, (int) ($_GET['page'] ?? 1));

        // Tính toán phân trang
        $totalGenres = genre_count($search);
        $totalPages = max(1, (int) ceil($totalGenres / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $genres = genre_get_all($perPage, $offset, $search);

        // Biến dùng để nạp dữ liệu lên form sửa nếu có tham số edit_id
        $editGenre = null;
        $edit_id = (int) ($_GET['edit_id'] ?? 0);
        if ($edit_id > 0) {
            $editGenre = genre_get_by_id($edit_id);
        }

        $pageTitle = "Quản Lý Thể Loại Truyện";
        $error = $_SESSION['genre_error'] ?? '';
        unset($_SESSION['genre_error']); // Xóa sau khi lấy thông báo

        require_once __DIR__ . '/../view/admin/Genre/genre-list.php';
    }

    /**
     * Xử lý thêm thể loại
     */
    public function admin_add_genre()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $genre_name = trim($_POST['genre_name'] ?? '');
            $inputSlug = trim($_POST['slug'] ?? '');
            $slug = str_slug($inputSlug !== '' ? $inputSlug : $genre_name);
            $description = trim($_POST['description'] ?? '');
            $is_menu = isset($_POST['is_menu']) ? 1 : 0;

            if ($genre_name === '') {
                $_SESSION['genre_error'] = "Vui lòng nhập tên thể loại!";
                header("Location: index.php?action=admin-genre-list");
                exit;
            }

            if (genre_exists_slug($slug)) {
                $_SESSION['genre_error'] = "Đường dẫn (Slug) hoặc tên thể loại này đã tồn tại!";
                header("Location: index.php?action=admin-genre-list");
                exit;
            }

            if (genre_add($genre_name, $slug, $description, $is_menu)) {
                header("Location: index.php?action=admin-genre-list&success=add");
            } else {
                $_SESSION['genre_error'] = "Thêm thể loại thất bại. Vui lòng kiểm tra lại!";
                header("Location: index.php?action=admin-genre-list");
            }
            exit;
        }
    }

    /**
     * Xử lý cập nhật thể loại
     */
    public function admin_edit_genre()
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            header("Location: index.php?action=admin-genre-list");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $genre_name = trim($_POST['genre_name'] ?? '');
            $inputSlug = trim($_POST['slug'] ?? '');
            $slug = str_slug($inputSlug !== '' ? $inputSlug : $genre_name);
            $description = trim($_POST['description'] ?? '');
            $is_menu = isset($_POST['is_menu']) ? 1 : 0;

            if ($genre_name === '') {
                $_SESSION['genre_error'] = "Tên thể loại không được để trống!";
                header("Location: index.php?action=admin-genre-list&edit_id=$id");
                exit;
            }

            if (genre_exists_slug($slug, $id)) {
                $_SESSION['genre_error'] = "Đường dẫn (Slug) này đã bị trùng với thể loại khác!";
                header("Location: index.php?action=admin-genre-list&edit_id=$id");
                exit;
            }

            if (genre_update($id, $genre_name, $slug, $description, $is_menu)) {
                header("Location: index.php?action=admin-genre-list&success=edit");
            } else {
                $_SESSION['genre_error'] = "Cập nhật thất bại hoặc dữ liệu không thay đổi!";
                header("Location: index.php?action=admin-genre-list&edit_id=$id");
            }
            exit;
        }
    }

    /**
     * Xử lý xóa thể loại
     */
    public function admin_delete_genre()
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id > 0) {
            genre_delete($id);
            header("Location: index.php?action=admin-genre-list&success=delete");
        } else {
            header("Location: index.php?action=admin-genre-list&error=notfound");
        }
        exit;
    }
}