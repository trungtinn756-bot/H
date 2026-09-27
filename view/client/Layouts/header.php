<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$layoutCurrentUser = function_exists('auth_get_current_user') ? auth_get_current_user() : null;
$scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$projectRoot = dirname($scriptPath);
if ($projectRoot === '/' || $projectRoot === '\\') {
    $projectRoot = '';
}

if (function_exists('genre_get_for_menu')) {
    $layoutGenres = genre_get_for_menu();
} else {
    $layoutGenres = [];
}

$current_user_id = $_SESSION['user']['id'] ?? 0;
$list_notifications = [];
$unread_count = 0;

if ($current_user_id <= 0) {
    $sqlNoti = "SELECT * FROM notifications WHERE user_id IS NULL AND is_read = 0 ORDER BY created_at DESC LIMIT 10";
    $list_notifications = pdo_getAll($sqlNoti);
} else {
    $sqlNoti = "SELECT * FROM notifications WHERE (user_id = ? OR user_id IS NULL) AND is_read = 0 ORDER BY created_at DESC LIMIT 10";
    $list_notifications = pdo_getAll($sqlNoti, $current_user_id);
}

if (function_exists('notification_count_unread')) {
    $unread_count = notification_count_unread($current_user_id);
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <script>
        window.projectRoot = "<?php echo $projectRoot; ?>";
        window.totalUnreadNotis = <?php echo (int) $unread_count; ?>;
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MANGATIN - Website Đọc Truyện Tranh Online Miễn Phí</title>

    <!-- CSS Thuần độc lập (Không cần CDN) -->
    <link rel="stylesheet" href="<?php echo $projectRoot; ?>/public/client/css/styles.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script defer src="<?php echo $projectRoot; ?>/public/client/js/main.js"></script>
</head>

<body id="top">
    <header class="site-header">
        <div class="header-container">
            <div class="logo-group">
                <button id="menu-toggle" aria-label="Toggle Menu" class="btn-menu-toggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <a href="<?php echo $projectRoot; ?>/" class="site-logo">
                    MANGA<span>TIN</span>
                </a>
            </div>

            <div class="search-desktop-wrapper" data-search-bar>
                <form action="index.php" method="GET" class="search-form">
                    <input type="hidden" name="action" value="search" />
                    <input type="text" name="q" id="search-desktop" autocomplete="off"
                        value="<?php echo htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        placeholder="Tìm truyện, tác giả, tên khác..." class="search-input" />
                    <button type="submit" class="search-btn">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>
                <div id="search-results-desktop" class="search-results hidden scrollbar-none"></div>
            </div>

            <div class="header-actions">
                <button type="button" id="mobile-search-toggle" aria-label="Toggle Search" class="btn-mobile-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>

                <div class="noti-wrapper">
                    <button type="button" id="noti-btn" aria-label="Notifications" class="btn-noti">
                        <i class="fa-regular fa-bell pointer-events-none"></i>
                        <span id="noti-badge" class="noti-badge <?php echo $unread_count > 0 ? '' : 'hidden'; ?>">
                            <?php echo $unread_count > 999 ? '999+' : $unread_count; ?>
                        </span>
                    </button>

                    <div id="noti-dropdown" class="noti-dropdown hidden">
                        <div class="noti-header">
                            <span>Thông báo mới</span>
                        </div>
                        <div id="noti-list-container" class="noti-list-container scrollbar-none">
                            <?php if (!empty($list_notifications)): ?>
                                <?php foreach ($list_notifications as $noti): ?>
                                    <a href="<?php echo $projectRoot; ?>/<?php echo $noti['link'] ?? '#'; ?>"
                                        data-noti-id="<?php echo $noti['id']; ?>"
                                        class="noti-item <?php echo isset($noti['is_read']) && !$noti['is_read'] ? 'unread' : ''; ?>">
                                        <div class="noti-item-flex">
                                            <div
                                                class="noti-icon <?php echo $noti['type'] === 'new_chapter' ? 'chapter' : 'system'; ?>">
                                                <?php echo $noti['type'] === 'new_chapter' ? '<i class="fa-solid fa-bolt"></i>' : '<i class="fa-solid fa-circle-info"></i>'; ?>
                                            </div>
                                            <div class="noti-text-box">
                                                <h4 class="noti-text-title">
                                                    <?php echo htmlspecialchars($noti['title'], ENT_QUOTES, 'UTF-8'); ?>
                                                </h4>
                                                <p class="noti-text-desc">
                                                    <?php echo htmlspecialchars($noti['content'], ENT_QUOTES, 'UTF-8'); ?>
                                                </p>
                                                <span
                                                    class="noti-text-time"><?php echo date('d/m H:i', strtotime($noti['created_at'])); ?></span>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div style="padding: 2rem 0; text-align: center; color: #6b7280; user-select: none;">
                                    <i class="fa-regular fa-bell-slashed"
                                        style="font-size: 1.25rem; display: block; color: #4b5563; margin-bottom: 0.5rem;"></i>
                                    <p style="font-size: 0.75rem; font-style: italic;">Không có thông báo chưa đọc nào.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="noti-footer">
                            <a href="#" id="page-mark-all" class="noti-mark-all">Đọc tất cả</a>
                            <button type="button" id="btn-load-more-noti" class="noti-load-more">
                                Xem thêm <i class="fa-solid fa-angles-down text-[9px] ml-0.5"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="button" id="theme-toggle" data-theme-toggle aria-label="Toggle Theme"
                    class="theme-toggle-chip">
                    <i class="fa-regular fa-moon text-base" data-theme-icon></i>
                    <span class="theme-label" data-theme-label>Tối</span>
                </button>

                <div class="user-action-area">
                    <?php if (isset($_SESSION['user'])): ?>
                        <div class="user-menu-wrapper">
                            <button type="button" id="user-menu-toggle" aria-haspopup="true" aria-expanded="false"
                                class="btn-user-menu">
                                Xin chào,
                                <?php
                                $avatarPath = empty($_SESSION['user']['avatar']) ? $projectRoot . '/uploads/avatars/default.png' : $projectRoot . '/uploads/avatars/' . htmlspecialchars($_SESSION['user']['avatar'], ENT_QUOTES, 'UTF-8');
                                $displayName = empty($_SESSION['user']['display_name']) ? 'Độc giả' : htmlspecialchars($_SESSION['user']['display_name'], ENT_QUOTES, 'UTF-8');
                                ?>
                                <img src="<?php echo $avatarPath; ?>" alt="Avatar" class="user-avatar"
                                    onerror="this.onerror = null; this.src ='https://ui-avatars.com/api/?name=User&background=0d8abc&color=fff';" />
                                <span class="user-name"><?php echo $displayName; ?></span>
                                <svg class="user-arrow" id="user-menu-arrow" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="user-menu-dropdown" class="user-dropdown hidden">
                                <?php if ($_SESSION['user']['role'] === '1'): ?>
                                    <a href="index.php?action=admin-dashboard" class="user-dropdown-item">Trang quản trị</a>
                                <?php endif; ?>
                                <a href="index.php?action=favorites" class="user-dropdown-item">Tủ truyện</a>
                                <a href="index.php?action=history" class="user-dropdown-item">Lịch sử đọc</a>
                                <a href="index.php?action=profile" class="user-dropdown-item">Hồ sơ cá nhân</a>
                                <hr class="dropdown-divider" />
                                <a href="index.php?action=logout" class="user-dropdown-item text-red">Đăng xuất</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="guest-auth-links">
                            <a href="index.php?action=login" class="auth-link">Đăng nhập</a>
                            <span class="auth-divider">/</span>
                            <a href="index.php?action=register" class="auth-btn-register">Đăng ký</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div id="mobile-search-bar" class="mobile-search-bar hidden">
                <form action="index.php" method="GET" class="search-form">
                    <input type="hidden" name="action" value="search" />
                    <input type="text" name="q" id="search-mobile" autocomplete="off"
                        value="<?php echo htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        placeholder="Tìm truyện, tác giả..." class="search-input" />
                    <button type="submit" class="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div id="search-results-mobile" class="search-results hidden scrollbar-none"></div>
            </div>
        </div>
    </header>

    <nav id="main-nav" class="main-nav hidden-mobile">
        <div class="nav-container">
            <ul class="nav-list">
                <li>
                    <a href="<?php echo $projectRoot; ?>/" class="nav-link">
                        <i class="fa-solid fa-house nav-icon-mobile"></i>Trang Chủ
                    </a>
                </li>
                <li>
                    <a href="index.php?action=list" class="nav-link">
                        <i class="fa-solid fa-list nav-icon-mobile"></i>Danh Sách Truyện
                    </a>
                </li>

                <!-- Dropdown Thể Loại -->
                <li class="dropdown-wrapper" id="genre-dropdown-wrapper">
                    <a href="index.php?action=genre-list" id="genre-dropdown-btn" class="nav-link nav-dropdown-btn">
                        <span><i class="fa-solid fa-tags nav-icon-mobile"></i>Thể Loại</span>
                        <i class="fa-solid fa-chevron-down id-arrow"></i>
                    </a>
                    <div id="genre-dropdown-box" class="genre-dropdown-box hidden">
                        <?php if (!empty($layoutGenres)): ?>
                            <?php foreach ($layoutGenres as $genre): ?>
                                <a href="index.php?action=genre&slug=<?php echo $genre['slug']; ?>" class="genre-item"
                                    title="<?php echo htmlspecialchars($genre['genre_name'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($genre['genre_name'], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="genre-item" style="grid-column: 1 / -1;">Đang cập nhật...</span>
                        <?php endif; ?>
                    </div>
                </li>

                <!-- Dropdown Danh Mục -->
                <li class="dropdown-wrapper" id="catalog-dropdown-wrapper">
                    <button type="button" id="catalog-dropdown-btn" class="nav-link nav-dropdown-btn">
                        <span><i class="fa-solid fa-layer-group nav-icon-mobile"></i>Danh Mục</span>
                        <i class="fa-solid fa-chevron-down catalog-arrow"></i>
                    </button>
                    <div id="catalog-dropdown-box" class="catalog-dropdown-box hidden">
                        <a href="index.php?action=genre&slug=comic-18" class="catalog-item text-adult">
                            <span>🔞 Người lớn (18+)</span>
                        </a>
                        <a href="index.php?action=genre&slug=full-color" class="catalog-item">
                            <i class="fa-solid fa-palette icon-amber"></i>Full Color
                        </a>
                        <a href="index.php?action=genre&slug=uncensored" class="catalog-item">
                            <i class="fa-solid fa-eye-slash icon-cyan"></i>Uncensored
                        </a>
                        <hr class="dropdown-divider" />
                        <a href="index.php?action=genre&slug=manga" class="catalog-item">
                            <i class="fa-solid fa-book-open icon-muted"></i>Manga
                        </a>
                        <a href="index.php?action=genre&slug=manhwa" class="catalog-item">
                            <i class="fa-solid fa-book-open icon-muted"></i>Manhwa
                        </a>
                        <a href="index.php?action=genre&slug=manhua" class="catalog-item">
                            <i class="fa-solid fa-book-open icon-muted"></i>Manhua
                        </a>
                        <hr class="dropdown-divider" />
                        <a href="index.php?action=genre&slug=adult" class="catalog-item">
                            <i class="fa-solid fa-user-shield icon-muted"></i>Adult
                        </a>
                        <a href="index.php?action=genre&slug=webtoon" class="catalog-item">
                            <i class="fa-solid fa-mobile-screen icon-muted"></i>Webtoon
                        </a>
                        <a href="index.php?action=genre&slug=hentai" class="catalog-item text-pink">
                            <i class="fa-solid fa-heart icon-pink animate-pulse"></i>Hentai
                        </a>
                    </div>
                </li>

                <li>
                    <a href="index.php?action=filter" class="nav-link">
                        <i class="fa-solid fa-filter nav-icon-mobile"></i>Tìm kiếm nâng cao
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <main>