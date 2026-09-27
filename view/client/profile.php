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
            <a href="index.php?action=profile" class="user-nav-item active">
                <i class="fa-solid fa-user-gear user-nav-icon"></i> Thông tin cá nhân
            </a>
            <a href="index.php?action=change-password" class="user-nav-item">
                <i class="fa-solid fa-shield-halved user-nav-icon"></i> Bảo mật tài khoản
            </a>
            <a href="index.php?action=favorites" class="user-nav-item">
                <i class="fa-solid fa-heart user-nav-icon"></i> Truyện yêu thích
            </a>
            <a href="index.php?action=history" class="user-nav-item">
                <i class="fa-solid fa-clock-rotate-left user-nav-icon"></i> Lịch sử xem truyện
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <section class="user-main-col user-content-card">
        <div>
            <div class="user-card-header">
                <div>
                    <h3 class="user-card-title">Thông tin cá nhân</h3>
                    <p class="user-card-subtitle">Quản lý và cập nhật thông tin hiển thị cơ bản của bạn trên hệ thống
                    </p>
                </div>
            </div>

            <form action="index.php?action=profile" method="post" class="form-grid-2col">
                <div class="form-group-item">
                    <label for="username" class="form-label">Tên tài khoản (Username) *</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon-left"><i class="fa-solid fa-at" style="font-size: 0.75rem;"></i></span>
                        <input type="text" id="username" name="username"
                            value="<?php echo htmlspecialchars($_SESSION['user']['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            class="form-input-text disabled" disabled />
                    </div>
                </div>

                <div class="form-group-item">
                    <label for="email" class="form-label">Địa chỉ Email liên kết *</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon-left"><i class="fa-solid fa-envelope"
                                style="font-size: 0.75rem;"></i></span>
                        <input type="email" id="email" name="email" required
                            value="<?php echo htmlspecialchars($_SESSION['user']['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            class="form-input-text" />
                    </div>
                </div>

                <div class="form-group-item">
                    <label for="display_name" class="form-label">Tên hiển thị *</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon-left"><i class="fa-solid fa-user"
                                style="font-size: 0.75rem;"></i></span>
                        <input type="text" id="display_name" name="display_name"
                            value="<?php echo htmlspecialchars($_SESSION['user']['display_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            class="form-input-text" />
                    </div>
                </div>

                <div class="form-group-item">
                    <label for="updated_at" class="form-label">Ngày cập nhật tài khoản *</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon-left"><i class="fa-solid fa-calendar"
                                style="font-size: 0.75rem;"></i></span>
                        <input type="text" id="updated_at" name="updated_at"
                            value="<?php echo htmlspecialchars($_SESSION['user']['updated_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            class="form-input-text disabled" disabled />
                    </div>
                </div>

                <div class="form-group-item">
                    <label for="created_at" class="form-label">Ngày tạo tài khoản *</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon-left"><i class="fa-solid fa-calendar"
                                style="font-size: 0.75rem;"></i></span>
                        <input type="text" id="created_at" name="created_at"
                            value="<?php echo htmlspecialchars($_SESSION['user']['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            class="form-input-text disabled" disabled />
                    </div>
                </div>

                <div
                    style="grid-column: 1 / -1; display: flex; justify-content: flex-end; padding-top: 1rem; border-top: 1px solid rgba(31, 41, 55, 0.4); margin-top: 0.5rem;">
                    <button type="submit" class="btn-save-submit">
                        <i class="fa-solid fa-floppy-disk" style="margin-right: 0.375rem;"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>

<script>
    document.getElementById('avatar-input').addEventListener('change', function (event) {
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
                    document.getElementById('avatar-preview').src = projectRoot + '/' + data.url;
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Lỗi tải ảnh:', error);
                alert('Có lỗi xảy ra trong quá trình kết nối tải ảnh lên.');
            });
    });
</script>

<?php include_once __DIR__ . '/../client/Layouts/footer.php'; ?>