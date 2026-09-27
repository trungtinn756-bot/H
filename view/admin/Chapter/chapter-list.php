<?php
include_once __DIR__ . '/../Layouts/header.php';

// Lấy thông tin trang và bộ lọc để giữ vị trí cũ
$currentPage = $_REQUEST['page'] ?? 1;
$search = $_REQUEST['search'] ?? '';
$statusFilter = $_REQUEST['status_filter'] ?? $_REQUEST['status'] ?? '';
$sort = $_REQUEST['sort'] ?? '';

$queryString = "&page={$currentPage}&search=" . urlencode($search) . "&status=" . urlencode($statusFilter) . "&sort=" . urlencode($sort);
?>

<!-- HEADER TRANG: TIÊU ĐỀ BÊN TRÁI - CỤM NÚT BẤM BÊN PHẢI -->
<div class="admin-page-header"
    style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
    <div>
        <h2 class="admin-page-title" style="margin: 0; font-size: 1.5rem; font-weight: 700; color: #1f2937;">
            Quản lý chương: <?= htmlspecialchars($comic['title']) ?>
        </h2>
        <p style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem;">
            Tổng số: <span style="font-weight: 600; color: #1f2937;"><?= $totalChapters ?></span> chương
        </p>
    </div>

    <!-- Cụm nút chuyển hướng đẩy hẳn sang bên phải -->
    <div style="display: flex; align-items: center; gap: 0.75rem; flex-shrink: 0;">
        <a href="index.php?action=admin-comic-list<?php echo $queryString; ?>" class="btn-form-cancel"
            style="background-color: #ffffff; border: 1px solid #d1d5db; color: #374151; text-decoration: none; display: inline-flex; align-items: center; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
            <i class="fa-solid fa-arrow-left" style="margin-right: 0.5rem;"></i>Quay lại danh sách truyện
        </a>
        <a href="index.php?action=admin-add-chapter&comic_id=<?= $comic['id'] ?><?php echo $queryString; ?>"
            class="btn-form-submit"
            style="background-color: #4f46e5; color: #ffffff; text-decoration: none; display: inline-flex; align-items: center; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
            <i class="fa-solid fa-plus" style="margin-right: 0.5rem;"></i>Thêm chương mới
        </a>
    </div>
</div>

<div class="admin-card-section">
    <form method="GET" action="index.php" class="admin-filter-bar">
        <input type="hidden" name="page" value="<?= $currentPage ?>">
        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
        <input type="hidden" name="status_filter" value="<?= htmlspecialchars($statusFilter) ?>">
        <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
        <input type="hidden" name="action" value="admin-chapter-list">
        <input type="hidden" name="comic_id" value="<?= $comic['id'] ?>">

        <div class="admin-search-input-box">
            <i class="fa-solid fa-search" style="color: #9ca3af; margin-right: 0.5rem;"></i>
            <input type="text" name="chapter_search" value="<?= htmlspecialchars($search) ?>"
                placeholder="Tìm kiếm theo tên chương..." class="admin-search-input" />
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <select name="sort" onchange="this.form.submit()" class="admin-filter-select">
                <option value="desc" <?= $sort === 'desc' ? 'selected' : '' ?>>Chương mới nhất trước</option>
                <option value="asc" <?= $sort === 'asc' ? 'selected' : '' ?>>Chương cũ nhất trước</option>
            </select>
            <button type="submit" class="hidden">Lọc</button>
        </div>
    </form>

    <div class="admin-table-container">
        <table class="admin-data-table">
            <thead>
                <tr>
                    <th>Số / Tên chương</th>
                    <th>Lượt xem</th>
                    <th>Ngày đăng</th>
                    <th style="text-align: right;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($chapters)): ?>
                    <tr>
                        <td colspan="4" style="padding: 2rem 0; text-align: center; color: #9ca3af; font-style: italic;">
                            Truyện hiện chưa có chương nào hoặc không tìm thấy kết quả phù hợp.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($chapters as $chapter): ?>
                        <tr class="admin-table-row">
                            <td>
                                <p style="font-weight: 600; color: #1f2937; font-size: 1rem; margin: 0;">
                                    Chương <?= htmlspecialchars((float) $chapter['chapter_number']) ?>
                                </p>
                                <?php if (!empty($chapter['chapter_title'])): ?>
                                    <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.125rem;">
                                        <?= htmlspecialchars($chapter['chapter_title']) ?>
                                    </p>
                                <?php endif; ?>
                            </td>
                            <td style="color: #4b5563;">
                                <div style="display: flex; align-items: center;">
                                    <i class="fa-regular fa-eye" style="margin-right: 0.5rem; color: #9ca3af;"></i>
                                    <?= number_format($chapter['view_count'] ?? 0) ?>
                                </div>
                            </td>
                            <td style="color: #4b5563;">
                                <p style="color: #1f2937; margin: 0;">
                                    <?= date('d/m/Y', strtotime($chapter['created_at'])) ?>
                                </p>
                                <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.125rem;">
                                    <?= date('H:i', strtotime($chapter['created_at'])) ?>
                                </p>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                                    <a href="index.php?action=admin-edit-chapter&id=<?= $chapter['id'] ?>"
                                        class="btn-action-icon indigo" title="Chỉnh sửa chương">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="index.php?action=admin-delete-chapter&id=<?= $chapter['id'] ?>"
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa chương này cùng toàn bộ hình ảnh thuộc về nó? Hành động này không thể hoàn tác!')"
                                        class="btn-action-icon red" title="Xóa chương">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="admin-table-footer" style="flex-wrap: wrap; gap: 1rem;">
            <span style="font-size: 0.875rem; color: #6b7280;">
                Hiển thị từ chương thứ <?= $offset + 1 ?> đến <?= min($offset + $perPage, $totalChapters) ?> trong tổng số
                <?= $totalChapters ?> chương
            </span>
            <div class="admin-pagination">
                <?php if ($page > 1): ?>
                    <a href="index.php?action=admin-chapter-list&comic_id=<?= $comic['id'] ?>&chapter_page=<?= $page - 1 ?>&page=<?= $currentPage ?>&chapter_search=<?= urlencode($search) ?>&sort=<?= $sort ?>"
                        class="page-link-admin">
                        Trước
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="index.php?action=admin-chapter-list&comic_id=<?= $comic['id'] ?>&chapter_page=<?= $i ?>&page=<?= $currentPage ?>&chapter_search=<?= urlencode($search) ?>&sort=<?= $sort ?>"
                        class="page-link-admin <?= $i === $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="index.php?action=admin-chapter-list&comic_id=<?= $comic['id'] ?>&chapter_page=<?= $page + 1 ?>&page=<?= $currentPage ?>&chapter_search=<?= urlencode($search) ?>&sort=<?= $sort ?>"
                        class="page-link-admin">
                        Sau
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../Layouts/footer.php'; ?>