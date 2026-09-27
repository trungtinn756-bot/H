<?php include_once __DIR__ . '/../client/Layouts/header.php'; ?>

<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <div style="border-bottom: 1px solid #1f2937; padding-bottom: 1rem;">
        <nav class="breadcrumb-nav">
            <a href="<?php echo $projectRoot; ?>/" class="breadcrumb-link">Trang Chủ</a>
            <span>/</span>
            <span class="breadcrumb-current">Tìm kiếm</span>
        </nav>
        <h2
            style="font-size: 1.25rem; font-weight: 900; color: #ffffff; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-magnifying-glass" style="color: #22d3ee; font-size: 0.875rem;"></i>
            Kết Quả Tìm Kiếm:
            <span
                style="color: #22d3ee; font-weight: 500; text-transform: none;">"<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"</span>
        </h2>
        <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">
            Tìm thấy <span style="color: #d1d5db; font-weight: 700;"><?php echo $totalComics; ?></span> bộ truyện phù
            hợp với từ khóa của bạn
        </p>
    </div>

    <?php if (!empty($comics)): ?>
        <div class="comic-grid">
            <?php foreach ($comics as $comic): ?>
                <div class="comic-card">
                    <a href="<?php echo $projectRoot; ?>/index.php?action=detail&id=<?php echo $comic['id']; ?>"
                        class="comic-poster-link">
                        <img src="<?php echo $projectRoot; ?>/uploads/comics/<?php echo htmlspecialchars($comic['thumbnail'], ENT_QUOTES, 'UTF-8'); ?>"
                            alt="<?php echo htmlspecialchars($comic['title'], ENT_QUOTES, 'UTF-8'); ?>" class="comic-poster-img"
                            onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=300&h=400&q=80';" />

                        <?php if (!empty($comic['latest_chapter'])): ?>
                            <span class="badge-chapter">
                                Th. <?php echo (float) $comic['latest_chapter']; ?>
                            </span>
                        <?php endif; ?>
                    </a>

                    <div class="comic-card-body">
                        <div>
                            <h3 class="comic-card-title">
                                <a href="<?php echo $projectRoot; ?>/index.php?action=detail&id=<?php echo $comic['id']; ?>">
                                    <?php echo htmlspecialchars($comic['title'], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </h3>
                            <?php if (!empty($comic['other_title'])): ?>
                                <p style="font-size: 10px; color: #6b7280; font-style: italic; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 0.25rem;"
                                    title="<?php echo htmlspecialchars($comic['other_title'], ENT_QUOTES, 'UTF-8'); ?>">
                                    Tên khác: <?php echo htmlspecialchars($comic['other_title'], ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <div class="comic-card-footer" style="justify-content: space-between;">
                            <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <i class="fa-solid fa-user-pen"
                                    style="margin-right: 0.25rem;"></i><?php echo htmlspecialchars($comic['author'] ?? 'Ẩn danh', ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                            <span style="flex-shrink: 0;">
                                <i class="fa-regular fa-eye"
                                    style="margin-right: 0.25rem;"></i><?php echo number_format($comic['total_views'] ?? 0); ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination-wrapper">
                <?php if ($page > 1): ?>
                    <a href="index.php?action=search&q=<?php echo urlencode($search); ?>&page=<?php echo $page - 1; ?>"
                        class="page-btn">
                        <i class="fa-solid fa-angle-left"></i>
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="page-btn active"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="index.php?action=search&q=<?php echo urlencode($search); ?>&page=<?php echo $i; ?>"
                            class="page-btn"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="index.php?action=search&q=<?php echo urlencode($search); ?>&page=<?php echo $page + 1; ?>"
                        class="page-btn">
                        <i class="fa-solid fa-angle-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div
            style="text-align: center; padding: 6rem 0; background-color: rgba(17, 24, 39, 0.4); border: 1px solid rgba(31, 41, 55, 0.6); border-radius: 1rem; display: flex; flex-direction: column; align-items: center; gap: 0.75rem;">
            <div
                style="width: 3rem; height: 3rem; background-color: rgba(3, 7, 18, 0.6); border: 1px solid #1f2937; border-radius: 9999px; display: flex; align-items: center; justify-content: center; color: #6b7280;">
                <i class="fa-solid fa-magnifying-glass-minus"></i>
            </div>
            <p style="color: #9ca3af; font-size: 0.875rem; font-style: italic;">Không tìm thấy bộ truyện nào phù hợp với từ
                khóa của bạn.</p>
            <p style="font-size: 0.75rem; color: #6b7280; max-width: 28rem; line-height: 1.5;">Mẹo: Hãy thử kiểm tra lại
                chính tả hoặc tìm kiếm bằng từ khóa ngắn hơn, tên tiếng Anh hoặc tên gọi khác của truyện.</p>
            <a href="<?php echo $projectRoot; ?>/" class="btn-save-submit"
                style="text-decoration: none; margin-top: 0.5rem;">
                Quay lại Trang Chủ
            </a>
        </div>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../client/Layouts/footer.php'; ?>