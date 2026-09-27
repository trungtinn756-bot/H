<?php include_once __DIR__ . '/../Layouts/header.php'; ?>

<?php
// Bổ sung các biến hỗ trợ từ request/session nếu chưa có từ controller
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = max(1, (int) ($_GET['page'] ?? 1));
$totalPages = $totalPages ?? 1;
$genres = $genres ?? [];
$totalGenres = $totalGenres ?? count($genres);
$editGenre = $editGenre ?? null;
$error = $_SESSION['genre_error'] ?? '';
unset($_SESSION['genre_error']);
?>

<!-- THÔNG BÁO LỖI -->
<?php if (!empty($error)): ?>
    <div
        style="margin-bottom: 1.5rem; padding: 1rem; background-color: #fef2f2; border-left: 4px solid #ef4444; border-radius: 0 0.75rem 0.75rem 0; color: #b91c1c; display: flex; align-items: center; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
        <i class="fa-solid fa-circle-exclamation" style="margin-right: 0.75rem; font-size: 1.25rem;"></i>
        <span style="font-weight: 500;"><?= htmlspecialchars($error) ?></span>
    </div>
<?php endif; ?>

<!-- THÔNG BÁO THÀNH CÔNG -->
<?php if (isset($_GET['success'])): ?>
    <div
        style="margin-bottom: 1.5rem; padding: 1rem; background-color: #f0fdf4; border-left: 4px solid #22c55e; border-radius: 0 0.75rem 0.75rem 0; color: #15803d; display: flex; align-items: center; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
        <i class="fa-solid fa-circle-check" style="margin-right: 0.75rem; font-size: 1.25rem;"></i>
        <span style="font-weight: 500;">
            <?php
            if ($_GET['success'] === 'add')
                echo "Thêm thể loại mới thành công!";
            if ($_GET['success'] === 'edit')
                echo "Cập nhật dữ liệu thể loại thành công!";
            if ($_GET['success'] === 'delete')
                echo "Đã xóa thể loại khỏi hệ thống thành công!";
            ?>
        </span>
    </div>
<?php endif; ?>

<!-- GRID 2 CỘT: FORM BÊN TRÁI (4 CỘT), BẢNG BÊN PHẢI (8 CỘT) -->
<div class="admin-form-grid" style="align-items: start;">

    <!-- KHỐI FORM: CHIẾM CỘT BÊN TRÁI -->
    <div class="admin-card-section" style="padding: 1.5rem; position: sticky; top: 1.5rem;">
        <h3 class="form-card-title" style="display: flex; align-items: center; user-select: none;">
            <?php if ($editGenre): ?>
                <i class="fa-solid fa-pen-to-square" style="margin-right: 0.5rem; color: #f59e0b;"></i>Cập nhật thể loại
            <?php else: ?>
                <i class="fa-solid fa-circle-plus" style="margin-right: 0.5rem; color: #4f46e5;"></i>Thêm thể loại mới
            <?php endif; ?>
        </h3>

        <?php
        $formAction = $editGenre
            ? "index.php?action=admin-edit-genre&id=" . (int) $editGenre['id']
            : "index.php?action=admin-add-genre";
        ?>
        <form action="<?= $formAction ?>" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
            <div>
                <label for="genre_name" class="form-label" style="margin-bottom: 0.25rem;">Tên thể loại <span
                        style="color: #ef4444;">*</span></label>
                <input type="text" id="genre_name" name="genre_name" required
                    value="<?= htmlspecialchars($editGenre['genre_name'] ?? '') ?>"
                    placeholder="Ví dụ: Hành động, Hài hước..." class="form-control-std" />
            </div>

            <div>
                <label for="slug" class="form-label" style="margin-bottom: 0.25rem;">Đường dẫn thân thiện (Slug)</label>
                <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($editGenre['slug'] ?? '') ?>"
                    placeholder="Hệ thống tự tạo nếu để trống" class="form-control-std"
                    style="background-color: #f9fafb;" />
            </div>

            <div>
                <label for="description" class="form-label" style="margin-bottom: 0.25rem;">Mô tả chi tiết</label>
                <textarea id="description" name="description" rows="4"
                    placeholder="Mô tả tóm tắt ý nghĩa thể loại để hiển thị cho người đọc..." class="form-control-std"
                    style="resize: none;"><?= htmlspecialchars($editGenre['description'] ?? '') ?></textarea>
            </div>

            <div style="display: flex; align-items: flex-start; padding: 0.25rem 0;">
                <input type="checkbox" id="is_menu" name="is_menu" value="1"
                    style="width: 1rem; height: 1rem; margin-top: 0.125rem; accent-color: #4f46e5; cursor: pointer;"
                    <?= (isset($editGenre) && $editGenre['is_menu'] == 1) ? 'checked' : '' ?>>
                <label for="is_menu"
                    style="margin-left: 0.5rem; font-size: 0.875rem; font-weight: 500; color: #4b5563; cursor: pointer; user-select: none; line-height: 1.25;">
                    Hiển thị thể loại này trên Thanh Menu / Bộ lọc chính thức
                </label>
            </div>

            <div style="padding-top: 0.5rem; display: flex; gap: 0.75rem;">
                <?php if ($editGenre): ?>
                    <a href="index.php?action=admin-genre-list" class="btn-form-cancel"
                        style="width: 50%; text-align: center; text-decoration: none;">Hủy</a>
                    <button type="submit" class="btn-form-submit" style="width: 50%; justify-content: center;">Cập
                        nhật</button>
                <?php else: ?>
                    <button type="submit" class="btn-form-submit" style="width: 100%; justify-content: center;">Thêm thể
                        loại</button>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- KHỐI BẢNG BÊN PHẢI: CHIẾM 2 CỘT CÒN LẠI TRONG GRID -->
    <div class="admin-form-main-col" style="display: flex; flex-direction: column; gap: 1rem;">

        <!-- Thanh tìm kiếm nhanh -->
        <div class="admin-card-section" style="padding: 1rem;">
            <form method="GET" action="index.php"
                style="width: 100%; position: relative; display: flex; align-items: center;">
                <input type="hidden" name="action" value="admin-genre-list" />
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 1rem; color: #9ca3af;"></i>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                    placeholder="Tìm kiếm danh mục, mô tả thể loại..." class="form-control-std"
                    style="padding-left: 2.75rem; padding-right: 6rem; background-color: #f9fafb;" />
                <button type="submit" class="btn-form-submit"
                    style="position: absolute; right: 0.5rem; padding: 0.375rem 1rem; font-size: 0.75rem; background-color: #111827;">
                    Tìm kiếm
                </button>
            </form>
        </div>

        <!-- Bảng danh mục thể loại -->
        <div class="admin-card-section" style="display: flex; flex-direction: column;">
            <div class="admin-table-container">
                <table class="admin-data-table" style="table-layout: fixed; min-width: 650px;">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Tên Thể Loại</th>
                            <th style="width: 15%;">Số Truyện</th>
                            <th style="width: 38%;">Mô Tả</th>
                            <th style="width: 12%;">Hiển thị</th>
                            <th style="width: 10%; text-align: center;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($genres)): ?>
                            <?php foreach ($genres as $item): ?>
                                <tr class="admin-table-row">
                                    <td>
                                        <div style="margin-bottom: 0.25rem;">
                                            <span
                                                style="background-color: #eef2ff; color: #4338ca; padding: 0.25rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; border: 1px solid #e0e7ff; white-space: nowrap;">
                                                <?= htmlspecialchars($item['genre_name']) ?>
                                            </span>
                                        </div>
                                        <div style="color: #9ca3af; font-family: monospace; font-size: 11px; padding-left: 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                            title="<?= htmlspecialchars($item['slug']) ?>">
                                            <i class="fa-solid fa-link"
                                                style="margin-right: 0.25rem; color: #d1d5db;"></i><?= htmlspecialchars($item['slug']) ?>
                                        </div>
                                    </td>

                                    <td style="white-space: nowrap;">
                                        <span
                                            style="display: inline-flex; align-items: center; background-color: #f3f4f6; color: #374151; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; border: 1px solid #e5e7eb;">
                                            <i class="fa-solid fa-book" style="margin-right: 0.25rem; color: #9ca3af;"></i>
                                            <?= (int) $item['comic_count'] ?> truyện
                                        </span>
                                    </td>

                                    <td>
                                        <div style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 0.75rem; color: #6b7280; line-height: 1.5; padding-right: 0.5rem;"
                                            title="<?= htmlspecialchars($item['description'] ?? '') ?>">
                                            <?= htmlspecialchars($item['description'] ?: 'Không có mô tả.') ?>
                                        </div>
                                    </td>

                                    <td style="white-space: nowrap;">
                                        <?php if (isset($item['is_menu']) && $item['is_menu'] == 1): ?>
                                            <span class="badge-status-pill active">
                                                Hiển thị Menu
                                            </span>
                                        <?php else: ?>
                                            <span class="badge-status-pill inactive"
                                                style="background-color: #f3f4f6; color: #6b7280; border-color: #e5e7eb;">
                                                Tag ngách (Ẩn)
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td style="text-align: center; white-space: nowrap;">
                                        <div
                                            style="display: flex; align-items: center; justify-content: center; gap: 0.375rem;">
                                            <a href="index.php?action=admin-genre-list&edit_id=<?= (int) $item['id'] ?>&search=<?= urlencode($search) ?>&page=<?= $page ?>"
                                                class="btn-action-icon indigo" title="Chỉnh sửa">
                                                <i class="fa-solid fa-pen-to-square" style="font-size: 0.75rem;"></i>
                                            </a>
                                            <a href="index.php?action=admin-delete-genre&id=<?= (int) $item['id'] ?>"
                                                onclick="return confirm('Bạn chắc chắn có muốn xóa thể loại [<?= htmlspecialchars($item['genre_name']) ?>] không? Các liên kết truyện tương ứng sẽ tự động bị gỡ bỏ.');"
                                                class="btn-action-icon red" title="Xóa thể loại">
                                                <i class="fa-solid fa-trash" style="font-size: 0.75rem;"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5"
                                    style="padding: 2.5rem 0; text-align: center; color: #9ca3af; font-style: italic;">
                                    <i class="fa-solid fa-folder-open"
                                        style="font-size: 1.875rem; color: #d1d5db; margin-bottom: 0.5rem; display: block;"></i>
                                    Tidak tìm thấy thể loại nào phù hợp.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- CHÂN TRANG PHÂN TRANG -->
            <div class="admin-table-footer">
                <p style="font-size: 0.875rem; color: #4b5563; margin: 0;">
                    Hiển thị trang <span style="font-weight: 700; color: #1f2937;"><?= $page; ?></span> /
                    <?= $totalPages; ?> trang (Tổng số <?= $totalGenres; ?> thể loại)
                </p>

                <?php if ($totalPages > 1): ?>
                    <?php
                    $pages = [];
                    for ($i = 1; $i <= min(1, $totalPages); $i++)
                        $pages[] = $i;
                    for ($i = max(1, $page - 1); $i <= min($totalPages, $page + 1); $i++)
                        $pages[] = $i;
                    for ($i = max(1, $totalPages); $i <= $totalPages; $i++)
                        $pages[] = $i;
                    $pages = array_unique($pages);
                    sort($pages);
                    ?>

                    <div class="admin-pagination">
                        <a href="index.php?action=admin-genre-list&page=<?= max(1, $page - 1) ?>&search=<?= urlencode($search) ?>"
                            class="page-link-admin <?= ($page <= 1) ? 'disabled' : '' ?>">
                            Trước
                        </a>

                        <?php $lastPage = 0;
                        foreach ($pages as $p): ?>
                            <?php if ($lastPage > 0 && $p - $lastPage > 1): ?>
                                <span style="padding: 0 0.5rem; font-size: 0.75rem; color: #9ca3af;">...</span>
                            <?php endif; ?>

                            <a href="index.php?action=admin-genre-list&page=<?= $p ?>&search=<?= urlencode($search) ?>"
                                class="page-link-admin <?= ($page == $p) ? 'active' : '' ?>">
                                <?= $p ?>
                            </a>
                            <?php $lastPage = $p; endforeach; ?>

                        <a href="index.php?action=admin-genre-list&page=<?= min($totalPages, $page + 1) ?>&search=<?= urlencode($search) ?>"
                            class="page-link-admin <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            Sau
                        </a>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

</div>

<?php include_once __DIR__ . '/../Layouts/footer.php'; ?>