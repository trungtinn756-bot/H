<?php
$scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$projectRoot = dirname($scriptPath);
if ($projectRoot === '/' || $projectRoot === '\\') {
    $projectRoot = '';
}

$user_avatar = empty($_SESSION['user']['avatar'])
    ? $projectRoot . '/uploads/avatars/default.png'
    : $projectRoot . '/uploads/avatars/' . $_SESSION['user']['avatar'];
?>
<?php include_once __DIR__ . '/../client/Layouts/header.php'; ?>

<div class="user-layout-grid">
    <!-- Sidebar Left -->
    <aside class="user-sidebar-col" style="display: flex; flex-direction: column; gap: 1rem;">
        <div class="user-card-widget">
            <div class="avatar-upload-wrapper">
                <img src="<?php echo htmlspecialchars($user_avatar, ENT_QUOTES, 'UTF-8'); ?>"
                    onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=User&background=0d8abc&color=fff';"
                    alt="User Avatar" id="avatar-preview" class="user-avatar-img" />
                <label for="avatar-input" class="avatar-upload-label">
                    <i class="fa-solid fa-camera" style="font-size: 1rem; margin-bottom: 0.25rem; color: #818cf8;"></i>
                    Thay ảnh
                    <input id="avatar-input" type="file" accept="image/*" class="hidden" />
                </label>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                <h2 class="user-display-name">
                    <?php echo htmlspecialchars($_SESSION['user']['display_name'] ?? 'User', ENT_QUOTES, 'UTF-8'); ?>
                </h2>
                <p class="user-email-text">
                    Email: <span
                        class="user-email-badge"><?php echo htmlspecialchars($_SESSION['user']['email'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></span>
                </p>
            </div>
        </div>

        <nav class="user-nav-menu scrollbar-none">
            <a href="index.php?action=profile" class="user-nav-item">
                <i class="fa-solid fa-user-gear user-nav-icon"></i> Thông tin cá nhân
            </a>
            <a href="index.php?action=change-password" class="user-nav-item">
                <i class="fa-solid fa-shield-halved user-nav-icon"></i> Bảo mật tài khoản
            </a>
            <a href="index.php?action=favorites" class="user-nav-item active">
                <i class="fa-solid fa-heart user-nav-icon"></i> Truyện yêu thích
            </a>
            <a href="index.php?action=history" class="user-nav-item">
                <i class="fa-solid fa-clock-rotate-left user-nav-icon"></i> Lịch sử xem truyện
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <section class="user-main-col user-content-card">
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="user-card-header">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div
                        style="width: 2.5rem; height: 2.5rem; border-radius: 0.75rem; background-color: rgba(79, 70, 229, 0.1); display: flex; align-items: center; justify-content: center; color: #818cf8;">
                        <i class="fa-solid fa-heart" style="font-size: 1.125rem;"></i>
                    </div>
                    <div>
                        <h2 class="user-card-title">Truyện đang theo dõi</h2>
                        <p class="user-card-subtitle">Danh sách những bộ truyện bạn đã đánh dấu yêu thích</p>
                    </div>
                </div>
                <span
                    style="font-size: 0.75rem; background-color: #1f2937; color: #d1d5db; padding: 0.375rem 0.75rem; border-radius: 0.5rem; font-weight: 500; border: 1px solid rgba(55, 65, 81, 0.5);">
                    Tổng số: <span id="total-count"
                        style="color: #818cf8; font-weight: 700;"><?php echo count($favoriteComics); ?></span> bộ
                </span>
            </div>

            <?php if (empty($favoriteComics)): ?>
                <div id="empty-alert"
                    style="text-align: center; padding: 4rem 0; display: flex; flex-direction: column; gap: 1rem; align-items: center;">
                    <div
                        style="width: 4rem; height: 4rem; background-color: rgba(31, 41, 55, 0.4); border-radius: 9999px; display: flex; align-items: center; justify-content: center; color: #6b7280;">
                        <i class="fa-solid fa-heart-crack" style="font-size: 1.5rem;"></i>
                    </div>
                    <p style="color: #9ca3af; font-size: 0.875rem;">Bạn chưa theo dõi bộ truyện nào.</p>
                    <a href="index.php" class="btn-save-submit" style="text-decoration: none;">
                        Khám phá truyện ngay
                    </a>
                </div>
            <?php else: ?>
                <div id="favorites-grid" class="favorites-grid">
                    <?php foreach ($favoriteComics as $comic): ?>
                        <div id="comic-item-<?php echo $comic['id']; ?>" class="favorite-card">

                            <a href="index.php?action=detail&id=<?php echo $comic['id']; ?>"
                                style="display: block; position: relative; border-radius: 0.375rem; overflow: hidden;">
                                <img src="<?php echo $projectRoot; ?>/uploads/comics/<?php echo htmlspecialchars($comic['thumbnail']); ?>"
                                    alt="<?php echo htmlspecialchars($comic['title']); ?>"
                                    style="width: 100%; height: 12rem; object-fit: cover; border-radius: 0.375rem;"
                                    onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=300&h=400&q=80';" />

                                <h4
                                    style="font-weight: 700; color: #ffffff; font-size: 0.875rem; margin-top: 0.75rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?php echo htmlspecialchars($comic['title']); ?>
                                </h4>
                                <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">
                                    <?php echo !empty($comic['latest_chapter']) ? 'Chương ' . (int) $comic['latest_chapter'] : 'Đang cập nhật'; ?>
                                </p>
                            </a>

                            <div
                                style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid rgba(31, 41, 55, 0.6); flex: 1; display: flex; align-items: flex-end;">
                                <a href="index.php?action=detail&id=<?php echo $comic['id']; ?>" class="btn-save-submit"
                                    style="width: 100%; text-align: center; text-decoration: none; padding: 0.5rem;">
                                    Xem truyện
                                </a>
                            </div>

                            <button type="button" class="btn-unfollow btn-unfollow-floating"
                                data-comic-id="<?php echo $comic['id']; ?>" title="Bỏ yêu thích">
                                <i class="fa-solid fa-heart-crack" style="font-size: 0.75rem;"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const avatarInput = document.getElementById('avatar-input');
        if (avatarInput) {
            avatarInput.addEventListener('change', function (event) {
                const file = event.target.files[0];
                if (!file) return;

                if (!file.type.startsWith('image/')) {
                    alert('Vui lòng chọn file hình ảnh hợp lệ!');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                };
                reader.readAsDataURL(file);

                const formData = new FormData();
                formData.append('avatar', file);

                fetch('index.php?action=upload-avatar', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.ok ? response.json() : Promise.reject('Mạng lỗi!'))
                    .then(data => {
                        if (data.status === 'success') {
                            alert(data.message);
                            const projectRoot = '<?php echo $projectRoot; ?>';
                            document.getElementById('avatar-preview').src = projectRoot + '/' + data
                                .url;
                        } else {
                            alert('Lỗi: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Lỗi:', error);
                        alert('Có lỗi xảy ra trong quá trình tải ảnh.');
                    });
            });
        }

        const unfollowButtons = document.querySelectorAll('.btn-unfollow');
        const totalCountSpan = document.getElementById('total-count');

        unfollowButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                if (!confirm('Bạn có chắc chắn muốn bỏ theo dõi bộ truyện này không?')) {
                    return;
                }

                const comicId = this.getAttribute('data-comic-id');
                const targetCard = document.getElementById(`comic-item-${comicId}`);

                const formData = new FormData();
                formData.append('comic_id', comicId);

                fetch('index.php?action=toggle-follow', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success' && data.action === 'unfollowed') {
                            if (targetCard) {
                                targetCard.style.opacity = '0';
                                targetCard.style.transform = 'scale(0.9)';
                                targetCard.style.transition = 'all 0.3s ease';

                                setTimeout(() => {
                                    targetCard.remove();
                                    let currentCount = parseInt(totalCountSpan
                                        .innerText) - 1;
                                    totalCountSpan.innerText = currentCount;

                                    if (currentCount <= 0) {
                                        window.location.reload();
                                    }
                                }, 300);
                            }
                        } else {
                            alert(data.message || 'Có lỗi xảy ra, không thể hủy yêu thích.');
                        }
                    })
                    .catch(error => {
                        console.error('Lỗi:', error);
                        alert('Mạng lỗi, vui lòng kiểm tra lại đường truyền!');
                    });
            });
        });
    });
</script>

<?php include_once __DIR__ . '/../client/Layouts/footer.php'; ?>