<?php include_once __DIR__ . '/../client/Layouts/header.php'; ?>

<main class="reader-main-container">

    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav">
        <a href="<?php echo $projectRoot; ?>/" class="breadcrumb-link">Trang Chủ</a>
        <span>/</span>
        <a href="<?php echo $projectRoot; ?>/index.php?action=detail&id=<?php echo $comic['id']; ?>"
            class="breadcrumb-link breadcrumb-current"><?php echo htmlspecialchars($comic['title'], ENT_QUOTES, 'UTF-8'); ?></a>
        <span>/</span>
        <span style="color: #e5e7eb; font-weight: 500;">Chương <?php echo $chapter['chapter_number']; ?></span>
    </nav>

    <!-- Top Navigation Controls -->
    <div class="reader-control-box">
        <h1 class="reader-chapter-title">
            <?php echo htmlspecialchars($comic['title'], ENT_QUOTES, 'UTF-8'); ?> - <span class="text-cyan">Chương
                <?php echo $chapter['chapter_number']; ?></span>
        </h1>

        <div class="reader-nav-buttons">
            <?php if ($prevChapterId): ?>
                <a href="<?php echo $projectRoot; ?>/index.php?action=reader&id=<?php echo $prevChapterId; ?>"
                    id="btn-prev-top" class="btn-nav-nav" title="Chương trước">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            <?php else: ?>
                <button disabled class="btn-nav-nav disabled">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
            <?php endif; ?>

            <div class="chapter-select-wrapper">
                <select class="chapter-select"
                    onchange="window.location.href='<?php echo $projectRoot; ?>/index.php?action=reader&id=' + this.value;">
                    <?php foreach ($dropdownChapters as $dc): ?>
                        <option value="<?php echo $dc['id']; ?>" <?php echo $dc['id'] == $chapter['id'] ? 'selected' : ''; ?>>
                            Chương <?php echo $dc['chapter_number']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <i class="fa-solid fa-chevron-down chapter-select-icon"></i>
            </div>

            <?php if ($nextChapterId): ?>
                <a href="<?php echo $projectRoot; ?>/index.php?action=reader&id=<?php echo $nextChapterId; ?>"
                    id="btn-next-top" class="btn-nav-nav" title="Chương sau">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            <?php else: ?>
                <button disabled class="btn-nav-nav disabled">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Key Navigation Tip -->
    <div class="reader-tip-box">
        <i class="fa-solid fa-circle-info" style="margin-right: 0.25rem;"></i> Sử dụng mũi tên trái (←) hoặc phải (→)
        trên bàn phím để chuyển nhanh chương
    </div>

    <!-- Comic Images Reader View -->
    <div class="reading-container">
        <?php if (!empty($chapterImages)): ?>
            <?php foreach ($chapterImages as $index => $img): ?>
                <img src="<?php echo $projectRoot; ?>/<?php echo htmlspecialchars($img['image_url'], ENT_QUOTES, 'UTF-8'); ?>"
                    alt="Trang <?php echo $img['order_number']; ?>"
                    class="reading-image <?php echo $index > 0 ? 'img-gap' : ''; ?>" loading="lazy"
                    onerror="this.onerror=null; this.src='https://placehold.co/800x1200/111827/64748b?text=L%E1%BB%97i+T%E1%BA%A3i+Trang+' + <?php echo $img['order_number']; ?>;" />
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 5rem 0; color: #6b7280;">
                <i class="fa-regular fa-images"
                    style="font-size: 2.25rem; color: #374151; display: block; margin-bottom: 0.5rem;"></i>
                <p style="font-size: 0.875rem; font-style: italic;">Chương truyện này hiện tại chưa được tải hình ảnh lên hệ
                    thống.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bottom Controls -->
    <div class="reader-bottom-actions">
        <div class="reader-bottom-flex">

            <?php if ($prevChapterId): ?>
                <a href="<?php echo $projectRoot; ?>/index.php?action=reader&id=<?php echo $prevChapterId; ?>"
                    id="btn-prev-bottom" class="btn-bottom-nav">
                    <i class="fa-solid fa-chevron-left"></i> Chap trước
                </a>
            <?php else: ?>
                <button disabled class="btn-bottom-nav disabled">
                    <i class="fa-solid fa-chevron-left"></i> Chap trước
                </button>
            <?php endif; ?>

            <div class="chapter-select-wrapper" style="flex: 1;">
                <div class="catalog-dropdown-btn-custom">
                    <i class="fa-solid fa-list-ul"></i> Mục lục
                    <i class="fa-solid fa-chevron-down" style="font-size: 10px; opacity: 0.6;"></i>
                </div>

                <select
                    onchange="window.location.href='<?php echo $projectRoot; ?>/index.php?action=reader&id=' + this.value"
                    style="position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
                    <option value="" disabled selected hidden>-- Chọn chương --</option>
                    <?php foreach ($dropdownChapters as $dc): ?>
                        <option value="<?php echo $dc['id']; ?>" style="background-color: #111827; color: #e5e7eb;">
                            Chương <?php echo $dc['chapter_number']; ?>
                            <?php echo !empty($dc['chapter_title']) ? ' - ' . htmlspecialchars($dc['chapter_title'], ENT_QUOTES, 'UTF-8') : ''; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if ($nextChapterId): ?>
                <a href="<?php echo $projectRoot; ?>/index.php?action=reader&id=<?php echo $nextChapterId; ?>"
                    id="btn-next-bottom" class="btn-nav-next-large">
                    Chap sau <i class="fa-solid fa-chevron-right"></i>
                </a>
            <?php else: ?>
                <button disabled class="btn-bottom-nav disabled">
                    Chap sau <i class="fa-solid fa-chevron-right"></i>
                </button>
            <?php endif; ?>
        </div>

        <button type="button" onclick="openReportModal()" class="btn-report-trigger">
            <i class="fa-solid fa-triangle-exclamation"></i> Báo lỗi chương
        </button>
    </div>

    <!-- Report Modal Popup -->
    <div id="report-modal" class="modal-overlay hidden">
        <div class="modal-content">
            <button onclick="closeReportModal()" class="modal-close-btn">
                <i class="fa-solid fa-xmark" style="font-size: 1.125rem;"></i>
            </button>

            <h3 class="modal-title">
                <i class="fa-solid fa-circle-exclamation" style="color: #ef4444;"></i> Báo lỗi chương truyện
            </h3>
            <p class="modal-desc">Ý kiến đóng góp của bạn giúp hệ thống hoàn thiện hơn. Vui lòng chọn hoặc nhập lỗi bạn
                gặp phải.</p>

            <form id="report-form" style="display: flex; flex-direction: column; gap: 1rem;">
                <input type="hidden" name="comic_id" value="<?php echo $comic['id']; ?>">
                <input type="hidden" name="chapter_id" value="<?php echo $chapter['id']; ?>">

                <div>
                    <label class="form-label">Loại lỗi phổ biến</label>
                    <select id="report-suggest" onchange="document.getElementById('report-content').value = this.value"
                        class="form-control-input" style="cursor: pointer;">
                        <option value="">-- Chọn lỗi có sẵn --</option>
                        <option value="Ảnh bị lỗi hiển thị, không tải được hình ảnh.">Ảnh bị lỗi hiển thị, không tải
                            được</option>
                        <option value="Chương bị trùng lặp nội dung với chương khác.">Chương bị trùng lặp nội dung
                        </option>
                        <option value="Nội dung chương bị lộn xộn, sai thứ tự các trang truyện.">Sai thứ tự trang truyện
                        </option>
                        <option value="Truyện dịch sai từ ngữ, lỗi chính tả nhiều.">Lỗi dịch thuật, chính tả</option>
                    </select>
                </div>

                <div>
                    <label class="form-label">Chi tiết lỗi bổ sung</label>
                    <textarea name="content" id="report-content" required rows="3"
                        placeholder="Mô tả cụ thể lỗi bạn đang gặp phải tại chương này..." class="form-control-input"
                        style="resize: none;"></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 0.5rem;">
                    <button type="button" onclick="closeReportModal()" class="btn-modal-cancel">Hủy</button>
                    <button type="submit" class="btn-modal-submit">Gửi báo lỗi</button>
                </div>
            </form>
        </div>
    </div>

    <?php
    $listComments = comment_get_by_context($comic['id'], $chapter['id'], 10, 0);
    ?>

    <!-- Comment Section -->
    <div class="detail-section-box" style="margin-top: 2rem;">
        <h2 class="section-title"
            style="border-left: 4px solid #22d3ee; padding-left: 0.75rem; font-size: 1.125rem; margin-bottom: 1rem;">
            Bình luận</h2>

        <?php if (isset($_SESSION['user'])): ?>
            <form id="comment-form" class="comment-form-box" style="margin-bottom: 1.5rem;">
                <input type="hidden" name="comic_id" value="<?php echo $comic['id']; ?>">
                <input type="hidden" name="chapter_id" value="<?php echo $chapter['id']; ?>">
                <?php $readerUserAvatar = empty($_SESSION['user']['avatar'])
                    ? $projectRoot . '/uploads/avatars/default.png'
                    : $projectRoot . '/uploads/avatars/' . $_SESSION['user']['avatar']; ?>
                <img src="<?php echo htmlspecialchars($readerUserAvatar, ENT_QUOTES, 'UTF-8'); ?>"
                    style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; border: 1px solid #1f2937; object-fit: cover;" />
                <div style="flex: 1;">
                    <textarea name="content" class="comment-input-area" rows="3"
                        placeholder="Nhập bình luận của bạn..."></textarea>
                    <div style="display: flex; justify-content: flex-end; margin-top: 0.5rem;">
                        <button type="submit" class="btn-nav-next-large">Gửi bình luận</button>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <div
                style="padding: 1rem; background-color: rgba(31, 41, 55, 0.4); border: 1px solid #1f2937; border-radius: 0.5rem; font-size: 0.875rem; text-align: center; margin-bottom: 1.5rem; color: #9ca3af;">
                Vui lòng <a href="index.php?action=login"
                    style="color: #22d3ee; font-weight: 700; text-decoration: underline;">Đăng nhập</a> để tham gia bình
                luận truyện.
            </div>
        <?php endif; ?>

        <!-- List Comments -->
        <div style="display: flex; flex-direction: column; gap: 1rem;" id="comments-container">
            <?php if (!empty($listComments)): ?>
                <?php foreach ($listComments as $cmt): ?>
                    <?php
                    $commentAvatar = !empty($cmt['avatar'])
                        ? $projectRoot . '/uploads/avatars/' . $cmt['avatar']
                        : 'https://ui-avatars.com/api/?name=' . urlencode($cmt['display_name']) . '&background=random';
                    ?>
                    <div style="display: flex; gap: 0.75rem;">
                        <img src="<?php echo htmlspecialchars($commentAvatar, ENT_QUOTES, 'UTF-8'); ?>"
                            style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; object-fit: cover;"
                            onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($cmt['display_name']); ?>&background=random';" />
                        <div>
                            <div
                                style="background-color: #161f30; border: 1px solid #1f2937; border-radius: 0.5rem; padding: 0.75rem;">
                                <h4 style="font-size: 0.875rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.25rem;">
                                    <?php echo htmlspecialchars($cmt['display_name'], ENT_QUOTES, 'UTF-8'); ?>
                                </h4>
                                <p style="font-size: 0.875rem; color: #d1d5db;">
                                    <?php echo htmlspecialchars($cmt['content'], ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                            </div>
                            <div
                                style="display: flex; gap: 0.75rem; margin-top: 0.25rem; font-size: 0.75rem; color: #6b7280; margin-left: 0.25rem;">
                                <span><?php echo date('d/m/Y H:i', strtotime($cmt['created_at'])); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: #6b7280; font-style: italic; font-size: 0.875rem; text-align: center; padding: 1rem 0;">
                    Chưa có bình luận nào. Hãy là người đầu tiên để lại ý kiến!</p>
            <?php endif; ?>
        </div>
    </div>

</main>

<script>
    function openReportModal() {
        document.getElementById('report-modal').classList.remove('hidden');
    }

    function closeReportModal() {
        document.getElementById('report-modal').classList.add('hidden');
        document.getElementById('report-form').reset();
    }

    document.addEventListener('DOMContentLoaded', function () {
        // --- 1. SỰ KIỆN GỬI BÁO CÁO BÁO LỖI ---
        const reportForm = document.getElementById('report-form');
        if (reportForm) {
            reportForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch('index.php?action=post-report', {
                    method: 'POST',
                    body: formData
                })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.message);
                        if (data.status === 'success') {
                            closeReportModal();
                        }
                    })
                    .catch(err => {
                        console.error('Lỗi gửi báo cáo:', err);
                        alert('Có lỗi xảy ra trong quá trình gửi. Vui lòng thử lại sau!');
                    });
            });
        }

        // --- 2. SỰ KIỆN GỬI BÌNH LUẬN ---
        const cmtForm = document.getElementById('comment-form');
        if (cmtForm) {
            cmtForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch('index.php?action=post-comment', {
                    method: 'POST',
                    body: formData
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(err => console.error('Lỗi gửi bình luận:', err));
            });
        }

        // --- 3. LƯU LỊCH SỬ ĐỌC TRUYỆN LOCALSTORAGE ---
        const comicId = "<?php echo $comic['id']; ?>";
        const comicTitle = "<?php echo htmlspecialchars($comic['title'], ENT_QUOTES, 'UTF-8'); ?>";
        const comicThumbnail = "<?php echo htmlspecialchars($comic['thumbnail'], ENT_QUOTES, 'UTF-8'); ?>";
        const chapterId = "<?php echo $chapter['id']; ?>";
        const chapterNumber = "<?php echo $chapter['chapter_number']; ?>";
        const projectRoot = "<?php echo $projectRoot; ?>";

        let readingHistory = JSON.parse(localStorage.getItem('reading_history')) || [];
        readingHistory = readingHistory.filter(item => item.comicId !== comicId);

        readingHistory.unshift({
            comicId: comicId,
            comicTitle: comicTitle,
            comicThumbnail: comicThumbnail,
            chapterId: chapterId,
            chapterNumber: chapterNumber,
            projectRoot: projectRoot,
            updatedAt: new Date().getTime()
        });

        if (readingHistory.length > 12) {
            readingHistory.pop();
        }

        localStorage.setItem('reading_history', JSON.stringify(readingHistory));

        // --- 4. CHUYỂN CHƯƠNG BẰNG PHÍM MŨI TÊN (LEFT/RIGHT) ---
        document.addEventListener('keydown', function (e) {
            if (e.target.tagName.toLowerCase() === 'textarea' || e.target.tagName.toLowerCase() ===
                'input') {
                return;
            }

            if (e.key === 'ArrowLeft') {
                const prevBtn = document.getElementById('btn-prev-top');
                if (prevBtn) {
                    prevBtn.click();
                }
            } else if (e.key === 'ArrowRight') {
                const nextBtn = document.getElementById('btn-next-top');
                if (nextBtn) {
                    nextBtn.click();
                }
            }
        });
    });
</script>

<?php include_once __DIR__ . '/../client/Layouts/footer.php'; ?>