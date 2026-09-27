<?php include_once __DIR__ . '/Layouts/header.php'; ?>

<div style="display: flex; flex-direction: column; gap: 2rem; padding: 1.5rem 0;">
    <!-- Tiêu đề trang -->
    <div style="border-bottom: 1px solid #1f2937; padding-bottom: 1rem;">
        <h1
            style="font-size: 1.5rem; font-weight: 900; letter-spacing: 0.05em; color: #22d3ee; display: flex; align-items: center; gap: 0.5rem; user-select: none;">
            <i class="fa-solid fa-tags" style="color: #06b6d4;"></i> BẢNG DANH SÁCH THỂ LOẠI TRUYỆN
        </h1>
        <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">
            Khám phá thế giới truyện tranh đa dạng được phân loại chi tiết theo từng chuyên mục.
        </p>
    </div>

    <!-- Danh sách Grid Thể Loại -->
    <div class="genres-grid">
        <?php if (!empty($allGenres)): ?>
            <?php foreach ($allGenres as $g): ?>
                <a href="<?php echo (!empty($projectRoot) ? rtrim($projectRoot, '/') . '/' : '') . 'index.php?action=genre&slug=' . $g['slug']; ?>"
                    class="genre-card-item">

                    <div style="display: flex; flex-direction: column; gap: 0.375rem; z-index: 10;">
                        <h3 style="font-size: 0.875rem; font-weight: 700; color: #e5e7eb; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                            title="<?php echo htmlspecialchars($g['genre_name'], ENT_QUOTES, 'UTF-8'); ?>">
                            # <?php echo htmlspecialchars($g['genre_name'], ENT_QUOTES, 'UTF-8'); ?>
                        </h3>
                        <p
                            style="font-size: 0.6875rem; color: #6b7280; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 32px; line-height: 1.5;">
                            <?php echo !empty($g['description']) ? htmlspecialchars($g['description'], ENT_QUOTES, 'UTF-8') : 'Chưa có mô tả chi tiết cho thể loại này.'; ?>
                        </p>
                    </div>

                    <div
                        style="margin-top: 1rem; padding-top: 0.5rem; border-top: 1px solid rgba(31, 41, 55, 0.6); display: flex; align-items: center; justify-content: space-between; font-size: 10px; font-weight: 500; z-index: 10;">
                        <span
                            style="color: #9ca3af; background-color: rgba(31, 41, 55, 0.5); padding: 0.125rem 0.5rem; border-radius: 9999px;">
                            <i class="fa-solid fa-book-open"
                                style="margin-right: 0.25rem; color: #6b7280;"></i><?php echo (int) ($g['comic_count'] ?? 0); ?>
                            truyện
                        </span>
                        <span style="color: #06b6d4;">
                            Xem ngay <i class="fa-solid fa-arrow-right" style="font-size: 8px; margin-left: 0.125rem;"></i>
                        </span>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div
                style="grid-column: 1 / -1; padding: 4rem 0; text-align: center; color: #6b7280; background-color: #111827; border: 1px solid #1f2937; border-radius: 0.75rem;">
                <i class="fa-solid fa-inbox"
                    style="font-size: 1.875rem; color: #374151; margin-bottom: 0.5rem; display: block;"></i>
                <p style="font-size: 0.875rem; font-style: italic;">Dữ liệu thể loại đang được cập nhật...</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/Layouts/footer.php'; ?>