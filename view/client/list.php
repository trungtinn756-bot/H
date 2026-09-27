<?php include_once __DIR__ . '/../client/Layouts/header.php'; ?>

<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- TIÊU ĐỀ TRANG VÀ BỘ LỌC SẮP XẾP -->
    <div class="list-header-bar">
        <div>
            <h1
                style="font-size: 1.25rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; color: #ffffff;">
                Danh Sách Tất Cả Truyện Tranh
            </h1>
            <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">Hiển thị một dòng một bộ truyện trực
                quan</p>
        </div>

        <div class="sort-btn-group">
            <span style="color: #9ca3af; margin-right: 0.25rem;"><i class="fa-solid fa-arrow-down-wide-short"
                    style="margin-right: 0.25rem;"></i>Sắp xếp theo:</span>

            <a href="index.php?action=list&sort=newest"
                class="btn-sort-item <?php echo (!isset($_GET['sort']) || $_GET['sort'] === 'newest') ? 'active' : ''; ?>">
                Mới nhất
            </a>

            <a href="index.php?action=list&sort=oldest"
                class="btn-sort-item <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'oldest') ? 'active' : ''; ?>">
                Cũ nhất
            </a>

            <a href="index.php?action=list&sort=most_viewed"
                class="btn-sort-item <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'most_viewed') ? 'active' : ''; ?>">
                Xem nhiều nhất
            </a>

            <a href="index.php?action=list&sort=least_viewed"
                class="btn-sort-item <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'least_viewed') ? 'active' : ''; ?>">
                Xem ít nhất
            </a>
        </div>
    </div>

    <!-- DANH SÁCH TRUYỆN TRANH DẠNG 1 HÀNG 1 TRUYỆN -->
    <div style="display: grid; grid-template-columns: repeat(1, minmax(0, 1fr)); gap: 1rem;">
        <?php if (!empty($comics)): ?>
            <?php foreach ($comics as $c):
                $detailUrl = "index.php?action=detail&id=" . $c['id'];
                $genreIds = function_exists('comic_get_genre_ids_by_comic_id') ? comic_get_genre_ids_by_comic_id($c['id']) : [];
                ?>

                <div class="comic-row-card">
                    <a href="<?php echo $detailUrl; ?>" class="comic-row-thumb">
                        <img src="<?php echo htmlspecialchars($projectRoot . '/uploads/comics/' . $c['thumbnail'], ENT_QUOTES, 'UTF-8'); ?>"
                            alt="<?php echo htmlspecialchars($c['title'], ENT_QUOTES, 'UTF-8'); ?>"
                            style="width: 100%; height: 100%; object-fit: cover;"
                            onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=200&h=300&q=80';" />

                        <?php if (!empty($c['latest_chapter'])): ?>
                            <span class="badge-chapter" style="bottom: 0.5rem; top: auto;">
                                Ch. <?php echo (float) $c['latest_chapter']; ?>
                            </span>
                        <?php else: ?>
                            <span class="badge-chapter full" style="bottom: 0.5rem; top: auto;">
                                Full
                            </span>
                        <?php endif; ?>
                    </a>

                    <div class="comic-row-info">
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <h2
                                style="font-size: 1rem; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #e5e7eb;">
                                <a href="<?php echo $detailUrl; ?>">
                                    <?php echo htmlspecialchars($c['title'], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </h2>

                            <div
                                style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem; font-size: 0.75rem; color: #6b7280;">
                                <?php if (!empty($c['author'])): ?>
                                    <span><i class="fa-solid fa-pen-nib"
                                            style="margin-right: 0.25rem; font-size: 10px;"></i><?php echo htmlspecialchars($c['author'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php endif; ?>
                                <span><i class="fa-regular fa-eye"
                                        style="margin-right: 0.25rem; color: #22d3ee;"></i><?php echo number_format($c['total_views'] ?? 0); ?>
                                    lượt xem</span>
                            </div>

                            <div style="display: flex; flex-wrap: wrap; gap: 0.375rem; padding-top: 0.25rem;">
                                <?php
                                $hasMenuGenre = false;
                                if (!empty($layoutGenres) && !empty($genreIds)):
                                    foreach ($layoutGenres as $gMenu):
                                        if (in_array($gMenu['id'], $genreIds)):
                                            $hasMenuGenre = true;
                                            ?>
                                            <a href="index.php?action=genre&slug=<?php echo $gMenu['slug']; ?>"
                                                style="font-size: 10px; background-color: rgba(6, 182, 212, 0.1); color: #22d3ee; border: 1px solid rgba(6, 182, 212, 0.2); padding: 0.125rem 0.5rem; border-radius: 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 120px;"
                                                title="<?php echo htmlspecialchars($gMenu['genre_name'], ENT_QUOTES, 'UTF-8'); ?>">
                                                <?php echo htmlspecialchars($gMenu['genre_name'], ENT_QUOTES, 'UTF-8'); ?>
                                            </a>
                                            <?php
                                        endif;
                                    endforeach;
                                endif;
                                if (!$hasMenuGenre): ?>
                                    <span style="font-size: 10px; color: #6b7280; font-style: italic;">Mạng xã hội truyện</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div
                            style="display: flex; align-items: center; justify-content: space-between; padding-top: 0.5rem; border-top: 1px solid rgba(31, 41, 55, 0.6); font-size: 0.75rem;">
                            <span style="font-weight: 600; color: #9ca3af; display: flex; align-items: center; gap: 0.375rem;">
                                <i class="fa-regular fa-eye" style="color: #22d3ee;"></i>
                                Tổng số: <span
                                    style="color: #e5e7eb;"><?php echo number_format($c['total_views'] ?? 0); ?></span> lượt xem
                            </span>

                            <a href="<?php echo $detailUrl; ?>" class="btn-detail-row">
                                Chi tiết <i class="fa-solid fa-bolt" style="font-size: 10px;"></i>
                            </a>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php else: ?>
            <div
                style="padding: 4rem 0; text-align: center; color: #9ca3af; background-color: #111827; border-radius: 0.75rem; border: 1px solid rgba(31, 41, 55, 0.6);">
                <i class="fa-solid fa-folder-open"
                    style="font-size: 2.25rem; margin-bottom: 0.75rem; display: block; color: #4b5563;"></i>
                Không tìm thấy dữ liệu truyện tranh nào phù hợp.
            </div>
        <?php endif; ?>
    </div>

    <!-- PHÂN TRANG -->
    <?php if (isset($totalPages) && $totalPages > 1):
        $currentSort = $_GET['sort'] ?? 'newest';
        ?>
        <div class="pagination-wrapper">
            <?php if ($page > 1): ?>
                <a href="index.php?action=list&sort=<?php echo $currentSort; ?>&page=<?php echo $page - 1; ?>" class="page-btn">
                    <i class="fa-solid fa-angle-left"></i>
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="page-btn active"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="index.php?action=list&sort=<?php echo $currentSort; ?>&page=<?php echo $i; ?>"
                        class="page-btn"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="index.php?action=list&sort=<?php echo $currentSort; ?>&page=<?php echo $page + 1; ?>" class="page-btn">
                    <i class="fa-solid fa-angle-right"></i>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../client/Layouts/footer.php'; ?>