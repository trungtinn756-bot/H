<?php include_once __DIR__ . '/../client/Layouts/header.php'; ?>

<!-- BANNER TRUYỆN HOT -->
<?php if (!empty($activeBanners) && !empty($activeBanners[0]['image_url'])):
    $currentBanner = $activeBanners[0];
    ?>
    <section class="hero-banner">
        <img src="<?php echo htmlspecialchars($projectRoot . '/' . $currentBanner['image_url']); ?>"
            class="hero-banner-img" />
        <div class="hero-banner-content">
            <span class="hero-badge">
                🔥 Nổi Bật
            </span>
            <h1 class="hero-title">
                <?php echo htmlspecialchars($currentBanner['title']); ?>
            </h1>
            <?php if (!empty($currentBanner['link_url'])): ?>
                <a href="<?php echo htmlspecialchars($currentBanner['link_url']); ?>" class="btn-hero-action">
                    Xem chi tiết ngay
                </a>
            <?php endif; ?>
        </div>
    </section>
<?php else: ?>
    <!-- Khung khi không có banner -->
    <section class="hero-banner-empty">
        <p>Hệ thống chưa cấu hình banner quảng cáo hoặc ảnh không hợp lệ.</p>
    </section>
<?php endif; ?>

<!-- SECTION: TRUYỆN ĐANG ĐỌC DỞ -->
<?php if (!empty($dbReadingHistory)): ?>
    <section id="section-history" class="history-section">
        <div class="section-title-wrapper">
            <h2 class="section-title">Truyện đang đọc dở</h2>
            <a href="#" class="section-link-more">
                Xem tất cả <i class="fa-solid fa-angle-right"></i>
            </a>
        </div>

        <div id="history-list" class="history-grid">
            <?php foreach ($dbReadingHistory as $item):
                $comicUrl = "index.php?action=detail&id=" . $item['comic_id'];
                $readerUrl = "index.php?action=reader&id=" . $item['chapterId'];
                $thumbUrl = "uploads/comics/" . $item['comicThumbnail'];
                ?>
                <div class="history-card">
                    <a href="<?php echo $comicUrl; ?>" style="flex-shrink: 0;">
                        <img src="<?php echo $thumbUrl; ?>"
                            alt="<?php echo htmlspecialchars($item['comicTitle'], ENT_QUOTES, 'UTF-8'); ?>"
                            class="history-thumb"
                            onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=150&h=200&q=80';" />
                    </a>
                    <div class="history-info">
                        <div>
                            <h3 class="history-title">
                                <a
                                    href="<?php echo $comicUrl; ?>"><?php echo htmlspecialchars($item['comicTitle'], ENT_QUOTES, 'UTF-8'); ?></a>
                            </h3>
                            <a href="<?php echo $readerUrl; ?>" class="history-continue-link">
                                Đọc tiếp: Chương <?php echo (float) $item['chapterNumber']; ?>
                            </a>
                        </div>
                        <div style="width: 100%; padding-top: 0.5rem;">
                            <a href="<?php echo $readerUrl; ?>" class="btn-history-read">
                                <i class="fa-solid fa-play" style="margin-right: 0.25rem;"></i> Đọc tiếp
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<!-- SECTION: TRUYỆN MỚI CẬP NHẬT -->
<section>
    <div class="section-title-wrapper">
        <h2 class="section-title">
            Truyện mới cập nhật
        </h2>
    </div>

    <div class="comic-grid">
        <?php if (!empty($comics)): ?>
            <?php foreach ($comics as $c): ?>
                <div class="comic-card">
                    <a href="index.php?action=detail&id=<?php echo $c['id']; ?>" class="comic-poster-link">
                        <img src="<?php echo $projectRoot; ?>/uploads/comics/<?php echo htmlspecialchars($c['thumbnail'], ENT_QUOTES, 'UTF-8'); ?>"
                            alt="<?php echo htmlspecialchars($c['title'], ENT_QUOTES, 'UTF-8'); ?>" class="comic-poster-img"
                            onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=300&h=400&q=80';" />

                        <?php if (isset($c['total_views']) && $c['total_views'] > 1000): ?>
                            <span class="badge-hot">
                                HOT
                            </span>
                        <?php endif; ?>

                        <?php if (!empty($c['latest_chapter'])): ?>
                            <span class="badge-chapter">
                                Ch. <?php echo !empty($c['latest_chapter']) ? (int) $c['latest_chapter'] : 'Chưa rõ'; ?>
                            </span>
                        <?php else: ?>
                            <span class="badge-chapter full">
                                Full
                            </span>
                        <?php endif; ?>
                    </a>

                    <div class="comic-card-body">
                        <div>
                            <h3 class="comic-card-title">
                                <a href="<?php echo $projectRoot; ?>/index.php?action=detail&id=<?php echo $c['id']; ?>">
                                    <?php echo htmlspecialchars($c['title'], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </h3>

                            <div class="comic-chapter-list">
                                <?php
                                if (!empty($c['top_three_chapters'])):
                                    $chaptersArr = explode('[split_chap]', $c['top_three_chapters']);
                                    $chaptersArr = array_slice($chaptersArr, 0, 3);

                                    foreach ($chaptersArr as $chapData):
                                        $parts = explode('||', $chapData);

                                        if (count($parts) === 4):
                                            $chapId = $parts[0];
                                            $chapNum = $parts[1];
                                            $chapTitle = trim($parts[2]);
                                            $createdAt = (int) $parts[3];

                                            $secondsIn30Days = 3 * 24 * 60 * 60;
                                            $isNew = (time() - $createdAt) < $secondsIn30Days;

                                            $displayTitle = "";
                                            if (strtolower($chapTitle) === 'oneshot') {
                                                $displayTitle = "Oneshot";
                                            } elseif (is_numeric($chapTitle)) {
                                                $displayTitle = "Chapter " . ($chapTitle + 0);
                                            } else {
                                                $displayTitle = "Ch." . ($chapNum + 0) . ": " . $chapTitle;
                                            }
                                            ?>
                                            <a href="<?php echo $projectRoot; ?>/index.php?action=reader&id=<?php echo $chapId; ?>"
                                                class="comic-chapter-item"
                                                title="<?php echo htmlspecialchars($displayTitle, ENT_QUOTES, 'UTF-8'); ?>">

                                                <span class="chapter-title-text">
                                                    <?php echo htmlspecialchars($displayTitle, ENT_QUOTES, 'UTF-8'); ?>
                                                </span>

                                                <?php if ($isNew): ?>
                                                    <span class="badge-new">Mới</span>
                                                <?php endif; ?>
                                            </a>
                                            <?php
                                        endif;
                                    endforeach;
                                else:
                                    ?>
                                    <span
                                        style="font-size: 0.6875rem; color: #6b7280; font-style: italic; display: block; padding: 0 0.5rem;">Đang
                                        cập nhật...</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <p class="comic-card-footer">
                            <i class="fa-regular fa-clock" style="font-size: 10px;"></i>
                            <?php echo !empty($c['updated_at']) ? date('d/m/Y', strtotime($c['updated_at'])) : 'Vừa xong'; ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; padding: 2.5rem 0; text-align: center; color: #9ca3af;">
                <i class="fa-solid fa-magnifying-glass"
                    style="font-size: 1.875rem; margin-bottom: 0.75rem; display: block; color: #4b5563;"></i>
                Không tìm thấy bộ truyện tranh nào phù hợp với bộ lọc hiện tại.
            </div>
        <?php endif; ?>
    </div>

    <!-- PHÂN TRANG -->
    <?php if ($totalPages > 1): ?>
        <div class="pagination-wrapper">

            <?php if ($page > 1): ?>
                <a href="<?php echo $projectRoot; ?>/index.php?action=home&page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&q=' . urlencode($search) : ''; ?><?php echo $statusFilter !== '' ? '&status=' . urlencode($statusFilter) : ''; ?>"
                    class="page-btn">
                    <i class="fa-solid fa-angle-left"></i>
                </a>
            <?php else: ?>
                <span class="page-btn disabled">
                    <i class="fa-solid fa-angle-left"></i>
                </span>
            <?php endif; ?>

            <?php
            $startPage = max(1, $page - 2);
            $endPage = min($totalPages, $page + 2);

            if ($startPage > 1): ?>
                <a href="<?php echo $projectRoot; ?>/index.php?action=home&page=1<?php echo !empty($search) ? '&q=' . urlencode($search) : ''; ?><?php echo $statusFilter !== '' ? '&status=' . urlencode($statusFilter) : ''; ?>"
                    class="page-btn">1</a>
                <?php if ($startPage > 2): ?>
                    <span class="page-dots">...</span>
                <?php endif; ?>
            <?php endif; ?>

            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="page-btn active">
                        <?php echo $i; ?>
                    </span>
                <?php else: ?>
                    <a href="<?php echo $projectRoot; ?>/index.php?action=home&page=<?php echo $i; ?><?php echo !empty($search) ? '&q=' . urlencode($search) : ''; ?><?php echo $statusFilter !== '' ? '&status=' . urlencode($statusFilter) : ''; ?>"
                        class="page-btn">
                        <?php echo $i; ?>
                    </a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($endPage < $totalPages): ?>
                <?php if ($endPage < $totalPages - 1): ?>
                    <span class="page-dots">...</span>
                <?php endif; ?>
                <a href="<?php echo $projectRoot; ?>/index.php?action=home&page=<?php echo $totalPages; ?><?php echo !empty($search) ? '&q=' . urlencode($search) : ''; ?><?php echo $statusFilter !== '' ? '&status=' . urlencode($statusFilter) : ''; ?>"
                    class="page-btn"><?php echo $totalPages; ?></a>
            <?php endif; ?>

            <?php if ($page < $totalPages): ?>
                <a href="<?php echo $projectRoot; ?>/index.php?action=home&page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&q=' . urlencode($search) : ''; ?><?php echo $statusFilter !== '' ? '&status=' . urlencode($statusFilter) : ''; ?>"
                    class="page-btn">
                    <i class="fa-solid fa-angle-right"></i>
                </a>
            <?php else: ?>
                <span class="page-btn disabled">
                    <i class="fa-solid fa-angle-right"></i>
                </span>
            <?php endif; ?>

        </div>
    <?php endif; ?>
</section>

<!-- SECTION BỔ SUNG: BOX BÌNH LUẬN MỚI NHẤT -->
<div class="global-comments-wrapper">
    <div class="section-title-wrapper" style="margin-bottom: 1rem;">
        <h2 class="section-title" style="font-size: 1.125rem;">Bình luận mới từ độc giả</h2>
    </div>
    <div class="comments-grid">
        <?php if (!empty($globalNewestComments)): ?>
            <?php foreach ($globalNewestComments as $gc): ?>
                <div onclick="window.location.href='index.php?action=detail&id=<?php echo $gc['comic_id']; ?>'"
                    class="comment-card">
                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                        <?php
                        $commentAvatar = !empty($gc['avatar'])
                            ? $projectRoot . '/uploads/avatars/' . $gc['avatar']
                            : 'https://ui-avatars.com/api/?name=' . urlencode($gc['display_name']) . '&background=random&color=fff';
                        ?>
                        <div class="comment-user-area">
                            <img src="<?php echo htmlspecialchars($commentAvatar, ENT_QUOTES, 'UTF-8'); ?>"
                                class="comment-avatar"
                                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($gc['display_name']); ?>&background=random&color=fff';" />
                            <div class="comment-meta">
                                <span class="comment-author">
                                    <i class="fa-solid fa-user"
                                        style="margin-right: 0.25rem; font-size: 10px;"></i><?php echo htmlspecialchars($gc['display_name'], ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                                <span
                                    style="flex-shrink: 0;"><?php echo date('d/m H:i', strtotime($gc['created_at'])); ?></span>
                            </div>
                        </div>
                        <p class="comment-content">
                            "<?php echo htmlspecialchars($gc['content'], ENT_QUOTES, 'UTF-8'); ?>"
                        </p>
                    </div>
                    <div class="comment-comic-info">
                        Tại truyện: <span
                            class="comment-comic-title"><?php echo htmlspecialchars($gc['comic_title'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p
                style="font-size: 0.75rem; color: #6b7280; font-style: italic; grid-column: 1 / -1; padding: 1rem 0; text-align: center;">
                Hệ thống chưa ghi nhận tương tác nào hôm nay.
            </p>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/../client/Layouts/footer.php'; ?>