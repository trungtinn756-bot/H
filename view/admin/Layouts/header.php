<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$projectRoot = rtrim(dirname($scriptPath), '/') . '/';

$currentAction = $_GET['action'] ?? '';

$navLinks = [
    'dashboard' => $projectRoot . 'index.php?action=admin-dashboard',
    'users' => $projectRoot . 'index.php?action=admin-user-list',
    'comics' => $projectRoot . 'index.php?action=admin-comic-list',
    'genres' => $projectRoot . 'index.php?action=admin-genre-list',
    'banners' => $projectRoot . 'index.php?action=admin-banner-list',
    'home' => $projectRoot . 'index.php?action=home',
];

$isDashboard = ($currentAction === 'admin-dashboard' || $currentAction === '');
$isUsers = (strpos($currentAction, 'admin-user-list') === 0);
$isComics = (strpos($currentAction, 'admin-comic-list') === 0);
$isGenres = (strpos($currentAction, 'admin-genre-list') === 0);
$isHome = ($currentAction === 'home' || $currentAction === '');
$isReports = (strpos($currentAction, 'admin-report-list') === 0);
$isComments = (strpos($currentAction, 'admin-comment-list') === 0);
$isBanners = (strpos($currentAction, 'admin-banner') === 0);

$admin_user_id = $_SESSION['user']['id'] ?? 0;

$adminNotifications = [];
$unreadCount = 0;

if (function_exists('notification_get_all_by_user')) {
    $adminNotifications = notification_get_all_by_user($admin_user_id, 5);
}
if (function_exists('notification_count_unread')) {
    $unreadCount = notification_count_unread($admin_user_id);
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Hệ Thống Quản Trị Cao Cấp - MANGATIN</title>

    <!-- Liên kết CSS Admin Độc Lập -->
    <link rel="stylesheet" href="<?php echo $projectRoot; ?>public/admin/css/styles.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Liên kết JS Admin Độc Lập -->
    <script defer src="<?php echo $projectRoot; ?>public/admin/js/main.js"></script>

    <script>
        window.projectRoot = "<?php echo rtrim($projectRoot, '/'); ?>";
    </script>
</head>

<body class="admin-body">
    <!-- Sidebar Menu -->
    <aside id="sidebar-menu" class="admin-sidebar sidebar-closed">
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div class="admin-logo-box">
                <a href="<?php echo $navLinks['dashboard']; ?>">
                    <img src="<?php echo $projectRoot . 'uploads/logo/logo.png'; ?>" alt="Logo"
                        class="admin-logo-img" />
                </a>
                <div style="line-height: 1;">
                    <h1 class="admin-brand-title">MANGATIN</h1>
                    <h2 class="admin-brand-sub">ADMINCMC</h2>
                    <p class="admin-brand-desc">Hệ Thống Quản Lý Nội Dung</p>
                </div>
            </div>

            <nav class="admin-nav-list">
                <a href="<?php echo htmlspecialchars($navLinks['dashboard'], ENT_QUOTES, 'UTF-8'); ?>"
                    class="admin-nav-link <?php echo $isDashboard ? 'active' : ''; ?>">
                    <i class="fas fa-home admin-nav-icon"></i>
                    <span>Tổng Quan</span>
                </a>
                <a href="<?php echo htmlspecialchars($navLinks['users'], ENT_QUOTES, 'UTF-8'); ?>"
                    class="admin-nav-link <?php echo $isUsers ? 'active' : ''; ?>">
                    <i class="fa-solid fa-users admin-nav-icon"></i>
                    <span>Quản Lý Người Dùng</span>
                </a>
                <a href="<?php echo htmlspecialchars($navLinks['comics'], ENT_QUOTES, 'UTF-8'); ?>"
                    class="admin-nav-link <?php echo $isComics ? 'active' : ''; ?>">
                    <i class="fa-solid fa-book admin-nav-icon"></i>
                    <span>Quản Lý Truyện Tranh</span>
                </a>
                <a href="<?php echo htmlspecialchars($navLinks['genres'], ENT_QUOTES, 'UTF-8'); ?>"
                    class="admin-nav-link <?php echo $isGenres ? 'active' : ''; ?>">
                    <i class="fa-solid fa-list admin-nav-icon"></i>
                    <span>Quản Lý Thể Loại</span>
                </a>
                <a href="<?php echo $projectRoot; ?>index.php?action=admin-report-list"
                    class="admin-nav-link <?php echo $isReports ? 'active' : ''; ?>">
                    <i class="fa-solid fa-triangle-exclamation admin-nav-icon"></i>
                    <span>Quản Lý Báo Lỗi</span>
                </a>
                <a href="<?php echo $projectRoot; ?>index.php?action=admin-comment-list"
                    class="admin-nav-link <?php echo $isComments ? 'active' : ''; ?>">
                    <i class="fa-regular fa-comments admin-nav-icon"></i>
                    <span>Quản Lý Bình Luận</span>
                </a>
                <a href="<?php echo $navLinks['banners']; ?>"
                    class="admin-nav-link <?php echo $isBanners ? 'active' : ''; ?>">
                    <i class="fa-solid fa-images admin-nav-icon"></i>
                    <span>Quản lý Banner</span>
                </a>
                <a href="<?php echo htmlspecialchars($navLinks['home'], ENT_QUOTES, 'UTF-8'); ?>"
                    class="admin-nav-link <?php echo $isHome ? 'active' : ''; ?>">
                    <i class="fa-solid fa-right-to-bracket admin-nav-icon"></i>
                    <span>Quay lại trang chủ</span>
                </a>
            </nav>
        </div>

        <div
            style="padding-top: 1rem; border-top: 1px solid #111827; font-size: 11px; color: #4b5563; text-align: center;">
            <p>&copy; 2026 Mangatin Matrix</p>
        </div>
    </aside>

    <!-- Mobile Backdrop Screen -->
    <div id="sidebar-backdrop" class="sidebar-backdrop hidden"></div>

    <!-- Main Right Content Wrapper -->
    <div class="admin-main-wrapper">
        <header class="admin-top-header">
            <div>
                <button id="sidebar-toggle" class="btn-sidebar-toggle" aria-label="Toggle Navigation">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>

            <div class="admin-header-actions">
                <!-- Notifications Dropdown -->
                <div style="position: relative;">
                    <button id="noti-menu-toggle" class="btn-noti-light" aria-label="Notifications">
                        <i class="fa-regular fa-bell text-xl pointer-events-none"></i>
                        <?php if ($unreadCount > 0): ?>
                            <span id="admin-noti-badge" class="admin-noti-badge">
                                <?php echo $unreadCount > 99 ? '99+' : $unreadCount; ?>
                            </span>
                        <?php endif; ?>
                    </button>

                    <div id="noti-menu-dropdown" class="admin-noti-dropdown hidden">
                        <div class="admin-noti-header">
                            <span style="font-weight: 600; color: #374151; font-size: 0.875rem;">Thông báo gần
                                đây</span>
                            <span id="admin-unread-text"
                                style="font-size: 0.75rem; background-color: #eef2ff; color: #4f46e5; padding: 0.125rem 0.5rem; border-radius: 9999px; font-weight: 500;"
                                class="<?php echo $unreadCount <= 0 ? 'hidden' : ''; ?>">
                                <?php echo $unreadCount; ?> chưa đọc
                            </span>
                        </div>

                        <div class="scrollbar-none" style="max-height: 20rem; overflow-y: auto;">
                            <?php if (!empty($adminNotifications)): ?>
                                <?php foreach ($adminNotifications as $noti): ?>
                                    <a href="<?php echo $projectRoot . htmlspecialchars($noti['link'] ?? 'index.php', ENT_QUOTES, 'UTF-8'); ?>"
                                        onclick="markAsRead(<?php echo $noti['id']; ?>, this, event)"
                                        class="admin-noti-item <?php echo !$noti['is_read'] ? 'unread' : ''; ?>">
                                        <div
                                            style="display: flex; flex-direction: column; gap: 0.125rem; pointer-events: none;">
                                            <p
                                                style="font-size: 0.75rem; color: #1f2937; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin: 0;">
                                                <?php echo htmlspecialchars($noti['title'], ENT_QUOTES, 'UTF-8'); ?>
                                            </p>
                                            <span style="font-size: 10px; color: #9ca3af;">
                                                <?php echo date('d/m/Y H:i', strtotime($noti['created_at'])); ?>
                                            </span>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div style="padding: 1.5rem 1rem; text-align: center; font-size: 0.875rem; color: #9ca3af;">
                                    <i class="fa-regular fa-bell-slash"
                                        style="font-size: 1.125rem; display: block; color: #d1d5db; margin-bottom: 0.5rem;"></i>
                                    Không có thông báo nào
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- User Profile Dropdown -->
                <div style="position: relative;">
                    <?php
                    $avatarPath = empty($_SESSION['user']['avatar'])
                        ? $projectRoot . 'uploads/avatars/default.png'
                        : $projectRoot . 'uploads/avatars/' . htmlspecialchars($_SESSION['user']['avatar'], ENT_QUOTES, 'UTF-8');

                    $displayName = empty($_SESSION['user']['display_name'])
                        ? 'Quản trị viên'
                        : htmlspecialchars($_SESSION['user']['display_name'], ENT_QUOTES, 'UTF-8');
                    ?>

                    <button type="button" id="user-menu-toggle" aria-haspopup="true" aria-expanded="false"
                        class="btn-admin-user">
                        <img src="<?php echo $avatarPath; ?>" alt="Avatar" class="admin-avatar-thumb"
                            onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=Admin&background=4f46e5&color=fff';" />
                        <span class="admin-user-name">
                            <?php echo $displayName; ?>
                        </span>
                        <i class="fa-solid fa-chevron-down" id="user-menu-arrow"
                            style="font-size: 10px; color: #9ca3af; transition: transform 0.2s;"></i>
                    </button>

                    <div id="user-menu-dropdown" class="admin-user-dropdown hidden">
                        <a href="<?php echo $projectRoot . 'index.php?action=home'; ?>"
                            class="admin-user-dropdown-item">
                            Xem trang chủ
                        </a>
                        <hr style="border: 0; border-top: 1px solid #f3f4f6; margin: 0.25rem 0;">
                        <a href="<?php echo $projectRoot . 'index.php?action=logout'; ?>"
                            class="admin-user-dropdown-item text-red">
                            Đăng xuất
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main class="admin-content-body">