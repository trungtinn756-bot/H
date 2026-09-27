<?php
$scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$projectRoot = dirname($scriptPath);
if ($projectRoot === '/' || $projectRoot === '\\') {
    $projectRoot = '';
}

$user_avatar = empty($_SESSION['user']['avatar'])
    ? $projectRoot . '/uploads/avatars/default.png'
    : $projectRoot . '/uploads/avatars/' . $_SESSION['user']['avatar'];

require_once __DIR__ . '/../../model/HistoryModel.php';
$fullHistory = isset($_SESSION['user']['id']) ? history_get_list_by_user($_SESSION['user']['id']) : [];
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
                    <?php echo htmlspecialchars($_SESSION['user']['display_name'] ?? 'Độc Giả', ENT_QUOTES, 'UTF-8'); ?>
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
            <a href="index.php?action=favorites" class="user-nav-item">
                <i class="fa-solid fa-heart user-nav-icon"></i> Truyện yêu thích
            </a>
            <a href="index.php?action=history" class="user-nav-item active">
                <i class="fa-solid fa-clock-rotate-left user-nav-icon"></i> Lịch sử xem truyện
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <section class="user-main-col user-content-card">
        <div style="width: 100%; flex: 1; display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="user-card-header">
                <div style="display: flex; items-center; gap: 0.75rem;">
                    <div
                        style="width: 2.5rem; height: 2.5rem; border-radius: 0.75rem; background-color: rgba(79, 70, 229, 0.1); display: flex; align-items: center; justify-content: center; color: #818cf8;">
                        <i class="fa-solid fa-clock-rotate-left" style="font-size: 1.125rem;"></i>
                    </div>
                    <div>
                        <h3 class="user-card-title">Lịch sử xem truyện</h3>
                        <p class="user-card-subtitle">Nơi lưu giữ các chương truyện bạn đang đọc dở</p>
                    </div>
                </div>

                <?php if (!empty($fullHistory)): ?>
                    <a href="index.php?action=clear-all-history" id="btn-clear-history"
                        onclick="return confirm('Bạn có chắc chắn muốn dọn sạch toàn bộ lịch sử xem truyện không?')"
                        class="btn-clear-all-danger">
                        <i class="fa-solid fa-trash-can"></i> Xóa toàn bộ
                    </a>
                <?php endif; ?>
            </div>

            <?php if (empty($fullHistory)): ?>
                <div id="history-empty"
                    style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 5rem 0; text-align: center; gap: 1rem;">
                    <div
                        style="width: 4rem; height: 4rem; border-radius: 9999px; background-color: rgba(31, 41, 55, 0.4); display: flex; align-items: center; justify-content: center; color: #6b7280; border: 1px solid rgba(31, 41, 55, 0.6);">
                        <i class="fa-solid fa-folder-open" style="font-size: 1.5rem;"></i>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                        <p style="color: #d1d5db; font-weight: 500; font-size: 0.875rem;">Bạn chưa đọc bộ truyện nào gần đây
                        </p>
                        <p style="font-size: 0.75rem; color: #6b7280; max-width: 20rem; line-height: 1.5;">Lịch sử đọc sẽ
                            giúp bạn dễ dàng tìm lại chương truyện đang đọc dở khi quay lại.</p>
                    </div>
                    <a href="index.php" class="btn-save-submit"
                        style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-compass"></i> Khám phá truyện ngay
                    </a>
                </div>
            <?php else: ?>
                <div id="history-list" style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <?php foreach ($fullHistory as $item):
                        $comicUrl = "index.php?action=detail&id=" . $item['comic_id'];
                        $readerUrl = "index.php?action=reader&id=" . $item['chapterId'];
                        $thumbUrl = "uploads/comics/" . $item['comicThumbnail'];
                        ?>
                        <div class="history-row-item">
                            <a href="<?php echo $comicUrl; ?>" style="flex-shrink: 0;">
                                <img src="<?php echo $thumbUrl; ?>"
                                    alt="<?php echo htmlspecialchars($item['comicTitle'], ENT_QUOTES, 'UTF-8'); ?>"
                                    class="history-thumb-img"
                                    onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=80&h=100&q=80';" />
                            </a>
                            <div style="flex: 1; min-width: 0;">
                                <h4
                                    style="font-weight: 700; font-size: 0.875rem; color: #ffffff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <a href="<?php echo $comicUrl; ?>"
                                        style="transition: color 0.2s;"><?php echo htmlspecialchars($item['comicTitle'], ENT_QUOTES, 'UTF-8'); ?></a>
                                </h4>
                                <p style="font-size: 0.75rem; color: #818cf8; font-weight: 600; margin-top: 0.25rem;">
                                    Đã đọc đến chương <?php echo (float) $item['chapterNumber']; ?>
                                </p>
                                <p
                                    style="font-size: 0.6875rem; color: #6b7280; margin-top: 0.375rem; display: flex; align-items: center; gap: 0.25rem;">
                                    <i class="fa-regular fa-clock"></i> Thời gian:
                                    <?php echo date('d/m/Y H:i', strtotime($item['updated_at'])); ?>
                                </p>
                            </div>

                            <a href="index.php?action=delete-history-item&comic_id=<?php echo $item['comic_id']; ?>"
                                title="Xóa khỏi lịch sử"
                                style="color: #4b5563; padding: 0.5rem; font-size: 0.875rem; transition: color 0.2s;"
                                onmouseover="this.style.color='#fb7185'" onmouseout="this.style.color='#4b5563'">
                                <i class="fa-solid fa-xmark"></i>
                            </a>

                            <a href="<?php echo $readerUrl; ?>" class="btn-save-submit"
                                style="text-decoration: none; padding: 0.5rem 1rem;">
                                Đọc tiếp
                            </a>
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
        if (!avatarInput) return;

        avatarInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
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
                        const pRoot = '<?php echo $projectRoot; ?>';
                        document.getElementById('avatar-preview').src = pRoot + '/' + data.url;
                    } else {
                        alert('Lỗi: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Lỗi:', error);
                    alert('Có lỗi xảy ra trong quá trình tải ảnh lên.');
                });
        });
    });
</script>

<?php include_once __DIR__ . '/../client/Layouts/footer.php'; ?>