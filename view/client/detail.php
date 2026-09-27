<?php
include_once __DIR__ . '/../client/Layouts/header.php';

if (function_exists('comment_get_by_context')) {
    $listComments = comment_get_by_context($comic['id'], null, 10, 0);
} else {
    $listComments = [];
}

function comic_get_all_tags_of_comic($comic_id)
{
    $sql = "SELECT g.* FROM genres g 
            INNER JOIN comics_genres cg ON g.id = cg.genre_id 
            WHERE cg.comic_id = ?";
    return pdo_getAll($sql, (int) $comic_id);
}
?>

<main style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav">
        <a href="<?php echo $projectRoot; ?>/" class="breadcrumb-link">Trang Chủ</a>
        <span>/</span>
        <span class="breadcrumb-current">
            <?php echo htmlspecialchars($comic['title'], ENT_QUOTES, 'UTF-8'); ?>
        </span>
    </nav>

    <!-- Comic Header Info Card -->
    <section class="detail-hero-card">
        <div class="detail-sidebar-left">
            <div class="detail-poster-box">
                <img src="<?php echo $projectRoot; ?>/uploads/comics/<?php echo htmlspecialchars($comic['thumbnail'], ENT_QUOTES, 'UTF-8'); ?>"
                    alt="<?php echo htmlspecialchars($comic['title'], ENT_QUOTES, 'UTF-8'); ?>"
                    class="detail-poster-img"
                    onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=300&h=400&q=80';" />
            </div>

            <div class="detail-stats-bar">
                <span>
                    <i class="fa-solid fa-eye" style="color: #22d3ee; margin-right: 0.25rem;"></i>
                    <?php echo number_format((int) $totalViews); ?>
                </span>
                <span id="header-votes-count">
                    <i class="fa-solid fa-star" style="color: #facc15; margin-right: 0.25rem;"></i>
                    <span id="total-votes-text-top"><?php echo $totalVotes; ?></span> lượt
                </span>
            </div>

            <div class="detail-rating-box">
                <div class="rating-stars" id="rating-stars-container" data-comic-id="<?php echo $comic['id']; ?>"
                    data-user-logged="<?php echo isset($_SESSION['user']) ? 'true' : 'false'; ?>">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fa-star <?php echo ($i <= $userRating) ? 'fa-solid' : (($i <= round($avgRating)) ? 'fa-solid' : 'fa-regular'); ?>"
                            style="<?php echo ($i <= $userRating || $i <= round($avgRating)) ? 'color: #facc15;' : 'color: #4b5563;'; ?>"
                            data-value="<?php echo $i; ?>"></i>
                    <?php endfor; ?>
                </div>
                <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">
                    Điểm: <span style="font-weight: 700; color: #facc15;"
                        id="avg-rating-text"><?php echo $avgRating; ?></span>/5
                </div>
                <p id="rating-message"
                    style="font-size: 0.6875rem; color: #4ade80; min-height: 14px; font-style: italic; margin-top: 0.125rem;">
                </p>
            </div>
        </div>

        <div class="detail-info-main">
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <h1 class="detail-title">
                    <?php echo htmlspecialchars($comic['title'], ENT_QUOTES, 'UTF-8'); ?>
                </h1>
                <?php if (!empty($comic['other_title'])): ?>
                    <p class="detail-other-title">
                        <span style="font-weight: 600; color: #d1d5db;">Tên khác:</span>
                        <?php echo htmlspecialchars($comic['other_title'], ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                <?php endif; ?>

                <div class="detail-meta-grid">
                    <p>
                        <i class="fa-solid fa-user" style="color: #6b7280; width: 1.25rem;"></i> Tác giả:
                        <span style="color: #22d3ee; font-weight: 500;">
                            <?php echo htmlspecialchars($comic['author'] ?? 'Đang cập nhật', ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    </p>
                    <p>
                        <i class="fa-solid fa-signal" style="color: #6b7280; width: 1.25rem;"></i> Tình trạng:
                        <?php if ($comic['status'] === '1'): ?>
                            <span class="badge-status ongoing">Đang tiến hành</span>
                        <?php elseif ($comic['status'] === '2'): ?>
                            <span class="badge-status completed">Hoàn thành</span>
                        <?php else: ?>
                            <span class="badge-status paused">Tạm ngưng</span>
                        <?php endif; ?>
                    </p>
                </div>

                <div class="detail-genre-tags">
                    <?php if (!empty($comicGenres)): ?>
                        <?php foreach ($comicGenres as $g): ?>
                            <a href="<?php echo $projectRoot; ?>/index.php?action=genre&slug=<?php echo urlencode($g['slug']); ?>"
                                class="genre-tag-item">
                                <?php echo htmlspecialchars($g['genre_name'], ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span style="font-size: 0.75rem; color: #6b7280; font-style: italic;">Chưa cập nhật thể loại</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="detail-action-buttons">
                <?php if (!empty($chapters)):
                    $firstChapter = end($chapters);
                    $latestChapter = reset($chapters);
                    ?>
                    <a href="<?php echo $projectRoot; ?>/index.php?action=reader&id=<?php echo $firstChapter['id']; ?>"
                        class="btn-read-first">
                        <i class="fa-solid fa-book-open"></i> Đọc Từ Đầu
                    </a>
                    <a href="<?php echo $projectRoot; ?>/index.php?action=reader&id=<?php echo $latestChapter['id']; ?>"
                        class="btn-read-latest">
                        Đọc Mới Nhất <span style="font-size: 0.75rem; color: #22d3ee;">(Ch.
                            <?php echo $latestChapter['chapter_number']; ?>)</span>
                    </a>
                <?php else: ?>
                    <button disabled
                        style="background-color: #1f2937; color: #6b7280; font-weight: 700; padding: 0.75rem 1.5rem; border-radius: 0.75rem; font-size: 0.875rem; border: 1px solid #1f2937; cursor: not-allowed;">
                        Chưa có chương dữ liệu
                    </button>
                <?php endif; ?>

                <button type="button" id="btn-follow" data-comic-id="<?php echo $comic['id']; ?>"
                    class="btn-follow <?php echo $is_following ? 'following' : 'not-following'; ?>">
                    <i class="<?php echo $is_following ? 'fa-solid fa-heart' : 'fa-regular fa-heart'; ?>"
                        id="icon-follow"></i>
                    <span id="text-follow"><?php echo $is_following ? 'Đang Theo Dõi' : 'Theo Dõi'; ?></span>
                </button>
            </div>
        </div>
    </section>

    <!-- Main Content Grid -->
    <div class="detail-layout-grid">
        <div class="detail-main-col" style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Summary -->
            <section class="detail-section-box">
                <h2 class="detail-box-title">
                    <i class="fa-solid fa-file-lines" style="margin-right: 0.5rem;"></i>Tóm tắt nội dung
                </h2>
                <p style="font-size: 0.875rem; color: #d1d5db; line-height: 1.625; white-space: pre-line;">
                    <?php echo !empty($comic['summary']) ? htmlspecialchars($comic['summary'], ENT_QUOTES, 'UTF-8') : 'Nội dung truyện đang được cập nhật...'; ?>
                </p>
            </section>

            <!-- Chapters List -->
            <section class="detail-section-box">
                <div
                    style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(31, 41, 55, 0.8); padding-bottom: 0.75rem; margin-bottom: 1rem;">
                    <h2 class="detail-box-title" style="border: none; padding: 0; margin: 0;">
                        <i class="fa-solid fa-list" style="margin-right: 0.5rem;"></i>Danh sách chương
                    </h2>
                    <span style="font-size: 0.75rem; color: #9ca3af; font-weight: 500;">Tổng số:
                        <?php echo count($chapters); ?> chương</span>
                </div>

                <div class="chapter-scroll-container scrollbar-none">
                    <?php if (!empty($chapters)): ?>
                        <?php foreach ($chapters as $index => $chap): ?>
                            <a href="<?php echo $projectRoot; ?>/index.php?action=reader&id=<?php echo $chap['id']; ?>"
                                class="chapter-row-item <?php echo $index === 0 ? 'first-chap' : ''; ?>">
                                <span class="chapter-item-name">
                                    Chương <?php echo htmlspecialchars($chap['chapter_title'], ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                                <span class="chapter-item-date">
                                    <?php echo !empty($chap['created_at']) ? date('d/m/Y', strtotime($chap['created_at'])) : 'Vừa xong'; ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div
                            style="text-align: center; padding: 2rem 0; color: #6b7280; font-style: italic; font-size: 0.875rem;">
                            Hệ thống chưa tải chương nào cho bộ truyện này.
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <!-- Sidebar Related Comics -->
        <div>
            <div class="detail-section-box">
                <h2 class="detail-box-title" style="margin-bottom: 1rem;">
                    <i class="fa-solid fa-fire" style="color: #f59e0b; margin-right: 0.5rem;"></i> Truyện cùng thể loại
                </h2>

                <div style="display: flex; flex-direction: column; gap: 0.875rem;">
                    <?php if (!empty($relatedComics)): ?>
                        <?php foreach ($relatedComics as $rc): ?>
                            <div onclick="window.location.href='<?php echo $projectRoot; ?>/index.php?action=detail&id=<?php echo $rc['id']; ?>'"
                                class="related-item-card">

                                <div class="related-thumb">
                                    <img src="<?php echo $projectRoot; ?>/uploads/comics/<?php echo htmlspecialchars($rc['thumbnail'], ENT_QUOTES, 'UTF-8'); ?>"
                                        alt="<?php echo htmlspecialchars($rc['title'], ENT_QUOTES, 'UTF-8'); ?>"
                                        style="width: 100%; height: 100%; object-fit: cover;"
                                        onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=100&h=150&q=80';" />
                                </div>

                                <div
                                    style="flex: 1; min-width: 0; display: flex; flex-direction: column; justify-content: space-between; height: 4rem; padding: 0.125rem 0;">
                                    <h3
                                        style="font-size: 0.875rem; font-weight: 600; color: #e5e7eb; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?php echo htmlspecialchars($rc['title'], ENT_QUOTES, 'UTF-8'); ?>
                                    </h3>

                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <span
                                            style="font-size: 0.75rem; color: #22d3ee; font-weight: 500; background-color: rgba(6, 182, 212, 0.1); padding: 0.125rem 0.5rem; border-radius: 0.25rem;">
                                            <?php echo !empty($rc['latest_chapter']) ? 'Ch. ' . (int) $rc['latest_chapter'] : 'Full'; ?>
                                        </span>

                                        <div
                                            style="display: flex; align-items: center; font-size: 0.75rem; color: #facc15; font-weight: 700; background-color: rgba(250, 204, 21, 0.05); padding: 0.125rem 0.5rem; border-radius: 0.25rem;">
                                            <i class="fa-solid fa-star" style="font-size: 10px; margin-right: 0.25rem;"></i>
                                            <span><?php echo number_format((float) ($rc['avg_rating'] ?? 0), 1); ?></span>
                                        </div>
                                    </div>

                                    <div style="font-size: 10px; color: #6b7280; display: flex; align-items: center;">
                                        <i class="fa-solid fa-user" style="margin-right: 0.25rem;"></i>
                                        <span
                                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($rc['author'] ?? 'Ẩn danh', ENT_QUOTES, 'UTF-8'); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div
                            style="text-align: center; padding: 2rem 0; color: #6b7280; font-style: italic; font-size: 0.75rem;">
                            Không có truyện liên quan khác.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Comments Section -->
    <section class="detail-section-box">
        <h2 class="detail-box-title">
            <i class="fa-solid fa-comments" style="margin-right: 0.5rem;"></i>Thảo luận truyện
        </h2>

        <?php if (isset($_SESSION['user'])): ?>
            <form id="detail-comment-form" class="comment-form-box">
                <input type="hidden" name="comic_id" value="<?php echo $comic['id']; ?>">
                <input type="hidden" name="chapter_id" value="0">

                <div
                    style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; border: 1px solid #374151; overflow: hidden; flex-shrink: 0; background-color: #1f2937;">
                    <img src="<?php echo $projectRoot; ?>/uploads/avatars/<?php echo !empty($_SESSION['user']['avatar']) ? htmlspecialchars($_SESSION['user']['avatar'], ENT_QUOTES, 'UTF-8') : 'default.png'; ?>"
                        style="width: 100%; height: 100%; object-fit: cover;"
                        onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=User';" />
                </div>

                <div style="flex: 1;">
                    <textarea name="content" class="comment-input-area" rows="3"
                        placeholder="Góp ý hoặc chia sẻ cảm nghĩ về bộ truyện này..."></textarea>
                    <div style="display: flex; justify-content: flex-end; margin-top: 0.5rem;">
                        <button type="submit" class="btn-submit-comment">
                            Gửi bình luận
                        </button>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <div
                style="padding: 1.25rem; background-color: rgba(31, 41, 55, 0.3); border: 1px solid rgba(31, 41, 55, 0.6); border-radius: 0.75rem; font-size: 0.875rem; text-align: center; color: #9ca3af;">
                Vui lòng <a href="index.php?action=login"
                    style="color: #22d3ee; font-weight: 700; text-decoration: underline;">Đăng nhập</a> để tham gia thảo
                luận truyện.
            </div>
        <?php endif; ?>

        <div style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 1rem;"
            id="detail-comments-container">
            <?php if (!empty($listComments)): ?>
                <?php foreach ($listComments as $cmt): ?>
                    <?php
                    $commentAvatar = !empty($cmt['avatar'])
                        ? $projectRoot . '/uploads/avatars/' . htmlspecialchars($cmt['avatar'], ENT_QUOTES, 'UTF-8')
                        : 'https://ui-avatars.com/api/?name=' . urlencode($cmt['display_name']) . '&background=random&color=fff';
                    ?>
                    <div style="display: flex; gap: 0.75rem; padding-top: 1rem; border-top: 1px solid rgba(31, 41, 55, 0.4);">
                        <div
                            style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; overflow: hidden; border: 1px solid rgba(31, 41, 55, 0.6); flex-shrink: 0; background-color: #1f2937;">
                            <img src="<?php echo htmlspecialchars($commentAvatar, ENT_QUOTES, 'UTF-8'); ?>"
                                style="width: 100%; height: 100%; object-fit: cover;"
                                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($cmt['display_name']); ?>&background=random&color=fff';" />
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div
                                style="background-color: rgba(31, 41, 55, 0.4); border: 1px solid rgba(31, 41, 55, 0.6); border-radius: 0.75rem; padding: 0.75rem;">
                                <div
                                    style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.25rem;">
                                    <h4
                                        style="font-size: 0.875rem; font-weight: 700; color: #22d3ee; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?php echo htmlspecialchars($cmt['display_name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </h4>
                                    <span style="font-size: 10px; color: #6b7280; white-space: nowrap;">
                                        <i class="fa-regular fa-clock"
                                            style="margin-right: 0.25rem;"></i><?php echo date('d/m/Y H:i', strtotime($cmt['created_at'])); ?>
                                    </span>
                                </div>
                                <p style="font-size: 0.875rem; color: #d1d5db; line-height: 1.5; word-break: break-word;">
                                    <?php echo htmlspecialchars($cmt['content'], ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 1.5rem 0; color: #6b7280; font-style: italic; font-size: 0.875rem;"
                    id="no-comment-placeholder">
                    Chưa có bình luận nào về truyện. Hãy chia sẻ cảm nghĩ của bạn đầu tiên!
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- 1. XỬ LÝ THEO DÕI TRUYỆN ---
        const btnFollow = document.getElementById('btn-follow');
        const iconFollow = document.getElementById('icon-follow');
        const textFollow = document.getElementById('text-follow');

        if (btnFollow) {
            btnFollow.addEventListener('click', function () {
                const comicId = this.getAttribute('data-comic-id');
                const formData = new FormData();
                formData.append('comic_id', comicId);

                fetch('index.php?action=toggle-follow', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'unauthenticated') {
                            alert(data.message);
                            window.location.href = 'index.php?action=login';
                            return;
                        }

                        if (data.status === 'success') {
                            if (data.action === 'followed') {
                                btnFollow.className = "btn-follow following";
                                iconFollow.className = "fa-solid fa-heart";
                                textFollow.innerText = "Đang Theo Dõi";
                            } else if (data.action === 'unfollowed') {
                                btnFollow.className = "btn-follow not-following";
                                iconFollow.className = "fa-regular fa-heart";
                                textFollow.innerText = "Theo Dõi";
                            }
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Đã xảy ra lỗi hệ thống! Vui lòng thử lại sau.');
                    });
            });
        }

        // --- 2. XỬ LÝ GỬI BÌNH LUẬN ---
        const detailCmtForm = document.getElementById('detail-comment-form');
        if (detailCmtForm) {
            detailCmtForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const contentInput = this.querySelector('textarea[name="content"]');
                if (!contentInput.value.trim()) {
                    alert('Nội dung bình luận không được để trống!');
                    return;
                }

                const formData = new FormData(this);
                fetch('index.php?action=post-comment', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            alert(data.message);
                            contentInput.value = '';
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(err => {
                        console.error('Lỗi gửi bình luận:', err);
                        alert('Đã xảy ra lỗi hệ thống!');
                    });
            });
        }

        // --- 3. XỬ LÝ ĐÁNH GIÁ SAO ---
        const starsContainer = document.getElementById('rating-stars-container');
        if (starsContainer) {
            const stars = starsContainer.querySelectorAll('i');
            const comicId = starsContainer.getAttribute('data-comic-id');
            const isLogged = starsContainer.getAttribute('data-user-logged') === 'true';
            const msgPlace = document.getElementById('rating-message');

            stars.forEach(star => {
                star.addEventListener('mouseover', function () {
                    const currentVal = parseInt(this.getAttribute('data-value'));
                    stars.forEach(s => {
                        const sVal = parseInt(s.getAttribute('data-value'));
                        if (sVal <= currentVal) {
                            s.className = 'fa-solid fa-star';
                            s.style.color = '#facc15';
                        } else {
                            s.className = 'fa-regular fa-star';
                            s.style.color = '#4b5563';
                        }
                    });
                });

                star.addEventListener('click', function () {
                    if (!isLogged) {
                        alert('Vui lòng đăng nhập để đánh giá truyện!');
                        window.location.href = 'index.php?action=login';
                        return;
                    }

                    const ratingValue = this.getAttribute('data-value');
                    const formData = new FormData();
                    formData.append('comic_id', comicId);
                    formData.append('rating', ratingValue);

                    fetch('index.php?action=submit-rating', {
                        method: 'POST',
                        body: formData
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                msgPlace.innerText = data.message;
                                document.getElementById('avg-rating-text').innerText = data
                                    .avg_rating;

                                const topVotesText = document.getElementById(
                                    'total-votes-text-top');
                                if (topVotesText) topVotesText.innerText = data.total_votes;

                                stars.forEach(s => {
                                    const sVal = parseInt(s.getAttribute('data-value'));
                                    if (sVal <= ratingValue) {
                                        s.className = 'fa-solid fa-star';
                                        s.style.color = '#facc15';
                                    } else {
                                        s.className = 'fa-regular fa-star';
                                        s.style.color = '#4b5563';
                                    }
                                });
                            } else {
                                alert(data.message);
                            }
                        })
                        .catch(err => {
                            console.error('Lỗi hệ thống khi đánh giá:', err);
                        });
                });
            });
        }
    });
</script>

<?php include_once __DIR__ . '/../client/Layouts/footer.php'; ?>