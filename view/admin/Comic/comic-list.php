<?php
$page = max(1, (int) ($_GET['page'] ?? 1));
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$totalPages = $totalPages ?? 1;
$comics = $comics ?? [];

include_once __DIR__ . '/../Layouts/header.php';
?>

<div class="admin-page-header">
    <h2 class="admin-page-title">Danh Sách Truyện Tranh</h2>
    <a href="index.php?action=admin-add-comic&page=<?= $page ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&sort=<?= urlencode($sort) ?>"
        class="btn-form-submit" style="text-decoration: none;">
        <i class="fa-solid fa-plus" style="margin-right: 0.5rem;"></i> Thêm Truyện Mới
    </a>
</div>

<div class="admin-card-section">
    <form method="GET" action="index.php" id="filterForm">
        <input type="hidden" name="action" value="admin-comic-list" />
        <input type="hidden" name="page" value="1" />

        <div class="admin-filter-bar">
            <div class="admin-filter-left">
                <div class="admin-search-input-box">
                    <i class="fa-solid fa-search" style="color: #9ca3af; margin-right: 0.5rem;"></i>
                    <input type="text" name="search" placeholder="Tìm kiếm theo tên truyện, tác giả..."
                        value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
                        class="admin-search-input" />
                </div>
                <select name="status" id="status" onchange="this.form.submit()" class="admin-filter-select">
                    <option value="">Tất cả trạng thái</option>
                    <option value="2" <?php echo ($status === '2') ? 'selected' : ''; ?>>Tạm ngừng</option>
                    <option value="1" <?php echo ($status === '1') ? 'selected' : ''; ?>>Hoàn thành</option>
                    <option value="0" <?php echo ($status === '0') ? 'selected' : ''; ?>>Đang tiến hành</option>
                </select>
            </div>
            <div class="admin-filter-right">
                <span style="font-size: 0.875rem; color: #6b7280; white-space: nowrap;">Sắp xếp theo:</span>
                <select name="sort" onchange="this.form.submit()" class="admin-filter-select"
                    style="background-color: #ffffff; border-color: #d1d5db;">
                    <option value="newest" <?php echo ($sort === 'newest') ? 'selected' : ''; ?>>Mới nhất</option>
                    <option value="oldest" <?php echo ($sort === 'oldest') ? 'selected' : ''; ?>>Cũ nhất</option>
                    <option value="title_asc" <?php echo ($sort === 'title_asc') ? 'selected' : ''; ?>>Tên A-Z</option>
                    <option value="title_desc" <?php echo ($sort === 'title_desc') ? 'selected' : ''; ?>>Tên Z-A
                    </option>
                    <option value="views_desc" <?php echo ($sort === 'views_desc') ? 'selected' : ''; ?>>Lượt xem nhiều
                        nhất</option>
                </select>
            </div>
        </div>
    </form>
</div>

<div class="admin-card-section" style="box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
    <div class="admin-table-container">
        <table class="admin-data-table">
            <thead>
                <tr>
                    <th>Tên truyện</th>
                    <th>Thể loại</th>
                    <th>Chương mới nhất</th>
                    <th>Trạng thái</th>
                    <th style="text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($comics)): ?>
                    <tr>
                        <td colspan="5" style="padding: 2.5rem 0; text-align: center; color: #9ca3af;">
                            <i class="fa-solid fa-folder-open"
                                style="font-size: 1.875rem; margin-bottom: 0.5rem; display: block;"></i> Không tìm thấy
                            truyện nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($comics as $comic): ?>
                        <tr class="admin-table-row">
                            <td style="display: flex; align-items: center; padding: 1rem 1.5rem;">
                                <div
                                    style="width: 2.5rem; height: 3rem; background-color: #e5e7eb; border-radius: 0.25rem; overflow: hidden; margin-right: 0.75rem; flex-shrink: 0;">
                                    <img src="<?php echo (!empty($projectRoot) ? rtrim($projectRoot, '/') . '/' : '') . 'uploads/comics/' . htmlspecialchars($comic['thumbnail'], ENT_QUOTES, 'UTF-8'); ?>"
                                        alt="Cover" style="width: 100%; height: 100%; object-fit: cover;" />
                                </div>
                                <div>
                                    <p style="font-weight: 600; color: #1f2937; font-size: 1rem; margin: 0;"
                                        title="<?php echo htmlspecialchars($comic['title'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php
                                        $fullTitle = htmlspecialchars($comic['title'], ENT_QUOTES, 'UTF-8');
                                        echo mb_strimwidth($fullTitle, 0, 20, "...");
                                        ?>
                                    </p>
                                    <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.125rem;">
                                        Tác giả: <?php echo htmlspecialchars($comic['author'], ENT_QUOTES, 'UTF-8'); ?>
                                    </p>
                                    <div
                                        style="display: flex; align-items: center; font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">
                                        <i class="fa-regular fa-eye" style="margin-right: 0.25rem;"></i>
                                        <?php echo count_views($comic['id']); ?> lượt xem
                                    </div>
                                </div>
                            </td>
                            <td style="max-width: 18rem; white-space: normal;">
                                <div style="display: flex; flex-wrap: wrap; gap: 0.375rem;">
                                    <?php
                                    $sqlG = "SELECT g.genre_name FROM genres g JOIN comics_genres cg ON g.id = cg.genre_id WHERE cg.comic_id = ?";
                                    $comicGenres = pdo_getAll($sqlG, $comic['id']);

                                    if (!empty($comicGenres)):
                                        foreach ($comicGenres as $cg):
                                            ?>
                                            <span
                                                style="background-color: #eef2ff; color: #4f46e5; font-size: 11px; font-weight: 600; padding: 0.125rem 0.5rem; border-radius: 0.25rem; border: 1px solid #e0e7ff;">
                                                <?= htmlspecialchars($cg['genre_name']) ?>
                                            </span>
                                            <?php
                                        endforeach;
                                    else:
                                        ?>
                                        <span style="font-size: 0.75rem; color: #9ca3af; font-style: italic;">Chưa phân loại</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="font-size: 0.875rem; color: #4b5563; font-weight: 500;">
                                <?php if (isset($comic['latest_chapter']) && $comic['latest_chapter'] !== null): ?>
                                    <span
                                        style="background-color: #f0f9ff; color: #0369a1; padding: 0.25rem 0.625rem; border-radius: 0.375rem; font-size: 0.75rem; border: 1px solid #e0f2fe;">
                                        Chương <?php echo $comic['latest_chapter']; ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #9ca3af; font-style: italic; font-size: 0.75rem;">Chưa có chương</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $bgColor = '#f3f4f6';
                                $textColor = '#374151';
                                $borderColor = '#d1d5db';
                                if ($comic['status'] == '1') {
                                    $bgColor = '#dcfce7';
                                    $textColor = '#15803d';
                                    $borderColor = '#86efac';
                                } elseif ($comic['status'] == '2') {
                                    $bgColor = '#fef3c7';
                                    $textColor = '#b45309';
                                    $borderColor = '#fde68a';
                                }
                                ?>

                                <form method="GET" action="index.php">
                                    <input type="hidden" name="action" value="admin-change-comic-status" />
                                    <input type="hidden" name="id" value="<?php echo $comic['id']; ?>" />
                                    <input type="hidden" name="page" value="<?php echo $page; ?>" />
                                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>" />
                                    <input type="hidden" name="status_filter"
                                        value="<?php echo htmlspecialchars($status); ?>" />
                                    <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>" />

                                    <div style="position: relative; display: inline-block; width: 10rem;">
                                        <select name="status" onchange="this.form.submit()" class="status-pill-select"
                                            style="background-color: <?= $bgColor ?>; color: <?= $textColor ?>; border-color: <?= $borderColor ?>;">
                                            <option value="0" <?php echo ($comic['status'] == '0') ? 'selected' : ''; ?>>⏳ Đang
                                                tiến hành</option>
                                            <option value="1" <?php echo ($comic['status'] == '1') ? 'selected' : ''; ?>>✅ Hoàn
                                                thành</option>
                                            <option value="2" <?php echo ($comic['status'] == '2') ? 'selected' : ''; ?>>🚫 Tạm
                                                ngừng</option>
                                        </select>
                                    </div>
                                </form>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; justify-content: center; gap: 0.5rem;">
                                    <a href="index.php?action=admin-chapter-list&comic_id=<?php echo $comic['id']; ?>&page=<?php echo $page; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>&sort=<?php echo urlencode($sort); ?>"
                                        class="btn-action-icon green" title="Quản lý chương">
                                        <i class="fa-solid fa-list-ol"></i>
                                    </a>
                                    <a href="index.php?action=admin-edit-comic&id=<?php echo $comic['id']; ?>&page=<?php echo $page; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>&sort=<?php echo urlencode($sort); ?>"
                                        class="btn-action-icon indigo" title="Chỉnh sửa truyện">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="index.php?action=admin-delete-comic&id=<?php echo $comic['id']; ?>&page=<?php echo $page; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>&sort=<?php echo urlencode($sort); ?>"
                                        class="btn-action-icon red" title="Xóa truyện"
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa truyện này không?');">
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

    <!-- Footer Phân Trang -->
    <div class="admin-table-footer">
        <p style="font-size: 0.875rem; color: #4b5563; margin: 0;">
            Hiển thị trang <span style="font-weight: 700; color: #1f2937;"><?php echo $page; ?></span> /
            <?php echo $totalPages; ?> trang (Tổng số <?php echo $totalComics; ?> truyện)
        </p>
        <?php if ($totalPages > 1): ?>
            <?php
            $pages = [];
            for ($i = 1; $i <= min(3, $totalPages); $i++)
                $pages[] = $i;
            for ($i = max(1, $page - 1); $i <= min($totalPages, $page + 1); $i++)
                $pages[] = $i;
            for ($i = max(1, $totalPages - 2); $i <= $totalPages; $i++)
                $pages[] = $i;
            $pages = array_unique($pages);
            sort($pages);
            ?>

            <div class="admin-pagination">
                <a href="index.php?action=admin-comic-list&page=<?= max(1, $page - 1) ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&sort=<?= urlencode($sort) ?>"
                    class="page-link-admin <?= ($page <= 1) ? 'disabled' : '' ?>">
                    Trước
                </a>

                <?php $lastPage = 0;
                foreach ($pages as $p): ?>
                    <?php if ($lastPage > 0 && $p - $lastPage > 1): ?>
                        <span style="padding: 0 0.5rem; font-size: 0.75rem; color: #9ca3af;">...</span>
                    <?php endif; ?>

                    <a href="index.php?action=admin-comic-list&page=<?= $p ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&sort=<?= urlencode($sort) ?>"
                        class="page-link-admin <?= ($page == $p) ? 'active' : '' ?>">
                        <?= $p ?>
                    </a>
                    <?php $lastPage = $p; endforeach; ?>

                <a href="index.php?action=admin-comic-list&page=<?= min($totalPages, $page + 1) ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&sort=<?= urlencode($sort) ?>"
                    class="page-link-admin <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                    Sau
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/../Layouts/footer.php'; ?>