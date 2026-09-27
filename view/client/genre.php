<?php include_once __DIR__ . '/../client/Layouts/header.php'; ?>

<div
    style="background-color: #161f30; padding: 1.5rem; border-radius: 0.75rem; border: 1px solid #1f2937; margin-bottom: 1.5rem;">
    <h1 style="font-size: 1.5rem; font-weight: 700; color: #22d3ee;">Thể loại:
        <?php echo htmlspecialchars($genreInfo['genre_name']); ?>
    </h1>
    <?php if (!empty($genreInfo['description'])): ?>
        <p style="font-size: 0.875rem; color: #9ca3af; margin-top: 0.5rem;">
            <?php echo htmlspecialchars($genreInfo['description']); ?>
        </p>
    <?php endif; ?>
</div>

<div class="comic-grid">
    <?php if (!empty($comics)): ?>
        <?php foreach ($comics as $c): ?>
            <div class="comic-card">

                <a href="index.php?action=detail&id=<?php echo $c['id']; ?>" class="comic-poster-link">
                    <img src="<?php echo $projectRoot; ?>/uploads/comics/<?php echo htmlspecialchars($c['thumbnail'], ENT_QUOTES, 'UTF-8'); ?>"
                        alt="<?php echo htmlspecialchars($c['title'], ENT_QUOTES, 'UTF-8'); ?>" class="comic-poster-img"
                        onerror="this.onerror=null; this.src='<?php echo $projectRoot; ?>/uploads/comics/default-comic.png';" />

                    <?php if (isset($c['total_views']) && $c['total_views'] > 1000): ?>
                        <span class="badge-hot">HOT</span>
                    <?php endif; ?>

                    <?php if (!empty($c['latest_chapter'])): ?>
                        <span class="badge-chapter">Ch. <?php echo (int) $c['latest_chapter']; ?></span>
                    <?php else: ?>
                        <span class="badge-chapter full">Full</span>
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

                    <div class="comic-card-footer" style="justify-content: space-between; margin-top: 0.5rem;">
                        <p style="display: flex; align-items: center; gap: 0.25rem;">
                            <i class="fa-regular fa-clock" style="font-size: 10px;"></i>
                            <?php
                            $displayDate = !empty($c['chapter_updated_at']) ? $c['chapter_updated_at'] : ($c['updated_at'] ?? null);
                            echo !empty($displayDate) ? date('d/m/Y', strtotime($displayDate)) : 'Vừa xong';
                            ?>
                        </p>
                        <p style="display: flex; align-items: center; gap: 0.25rem; font-size: 0.6875rem; color: #6b7280;">
                            <i class="fa-regular fa-eye" style="font-size: 10px;"></i>
                            <?php echo isset($c['total_views']) ? number_format($c['total_views']) : 0; ?>
                        </p>
                    </div>

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

<?php if ($totalPages > 1): ?>
    <div class="pagination-wrapper">
        <?php if ($page > 1): ?>
            <a href="index.php?action=genre&slug=<?php echo urlencode($genreInfo['slug']); ?>&page=<?php echo $page - 1; ?>"
                class="page-btn">
                <i class="fa-solid fa-chevron-left" style="font-size: 12px;"></i>
            </a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php if ($i == $page): ?>
                <span class="page-btn active"><?php echo $i; ?></span>
            <?php else: ?>
                <a href="index.php?action=genre&slug=<?php echo urlencode($genreInfo['slug']); ?>&page=<?php echo $i; ?>"
                    class="page-btn"><?php echo $i; ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="index.php?action=genre&slug=<?php echo urlencode($genreInfo['slug']); ?>&page=<?php echo $page + 1; ?>"
                class="page-btn">
                <i class="fa-solid fa-chevron-right" style="font-size: 12px;"></i>
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php include_once __DIR__ . '/../client/Layouts/footer.php'; ?>