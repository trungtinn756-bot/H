<?php
include_once __DIR__ . '/../Layouts/header.php';
?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Danh Sách Người Dùng</h1>
</div>

<!-- Khối Tìm kiếm & Bộ Lọc -->
<div class="admin-card-section">
    <form method="GET" action="index.php" id="filterForm">
        <input type="hidden" name="action" value="admin-user-list">

        <div class="admin-filter-bar">
            <div class="admin-filter-left">
                <div class="admin-search-input-box">
                    <i class="fa-solid fa-search" style="color: #9ca3af; margin-right: 0.5rem;"></i>
                    <input type="text" name="search" placeholder="Tìm kiếm theo tên, email..."
                        value="<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        class="admin-search-input" />
                </div>

                <select name="role" id="role" onchange="this.form.submit()" class="admin-filter-select">
                    <option value="">Tất cả vai trò</option>
                    <option value="1" <?php echo (isset($_GET['role']) && $_GET['role'] === '1') ? 'selected' : ''; ?>>
                        Admin
                    </option>
                    <option value="0" <?php echo (isset($_GET['role']) && $_GET['role'] === '0') ? 'selected' : ''; ?>>
                        Người Dùng
                    </option>
                </select>

                <select name="status" id="status" onchange="this.form.submit()" class="admin-filter-select"
                    style="min-width: 160px;">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1" <?php echo (isset($_GET['status']) && $_GET['status'] === '1') ? 'selected' : ''; ?>>Hoạt động
                    </option>
                    <option value="0" <?php echo (isset($_GET['status']) && $_GET['status'] === '0') ? 'selected' : ''; ?>>Không hoạt
                        động</option>
                </select>

                <button type="submit" class="btn-filter-submit">
                    Lọc dữ liệu
                </button>
            </div>

            <div class="admin-filter-right">
                <span style="font-size: 0.875rem; color: #6b7280; white-space: nowrap;">Sắp xếp theo:</span>
                <select name="sort" onchange="this.form.submit()" class="admin-filter-select"
                    style="background-color: #ffffff; border-color: #d1d5db;">
                    <option value="newest" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'newest') ? 'selected' : ''; ?>>Mới nhất
                    </option>
                    <option value="oldest" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'oldest') ? 'selected' : ''; ?>>Cũ nhất
                    </option>
                    <option value="username_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'username_asc') ? 'selected' : ''; ?>>Tên
                        A-Z</option>
                    <option value="username_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'username_desc') ? 'selected' : ''; ?>>Tên
                        Z-A</option>
                </select>
            </div>
        </div>
    </form>
</div>

<!-- Khối Bảng Danh Sách Dữ Liệu -->
<div class="admin-card-section" style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
    <div class="admin-table-container">
        <table class="admin-data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên Người Dùng</th>
                    <th>Trạng Thái</th>
                    <th>Ngày tham gia</th>
                    <th style="text-align: center;">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #6b7280;">
                            Không tìm thấy người dùng nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr class="admin-table-row">
                            <td style="font-weight: 500; color: #4b5563;">
                                #<?php echo htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8'); ?>
                            </td>
                            <td>
                                <div class="user-avatar-cell">
                                    <?php
                                    $avatarPath = empty($user['avatar'])
                                        ? $projectRoot . '/uploads/avatars/default.png'
                                        : $projectRoot . '/uploads/avatars/' . htmlspecialchars($user['avatar'], ENT_QUOTES, 'UTF-8');
                                    ?>
                                    <img src="<?php echo $avatarPath; ?>" alt="Avatar" class="user-table-avatar"
                                        onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=User&background=38bdf8&color=fff';" />
                                    <div>
                                        <p style="font-weight: 600; color: #1f2937; margin: 0;">
                                            <?php echo htmlspecialchars($user['display_name'] ?? $user['username'], ENT_QUOTES, 'UTF-8'); ?>
                                        </p>
                                        <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">
                                            <?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if ($user['status'] === 1): ?>
                                    <span class="badge-status-pill active">
                                        <span class="status-dot"></span>
                                        Hoạt động
                                    </span>
                                <?php else: ?>
                                    <span class="badge-status-pill inactive">
                                        <span class="status-dot"></span>
                                        Không hoạt động
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="color: #4b5563;">
                                <?php echo date("d/m/Y", strtotime($user['created_at'])); ?>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; items-center; justify-content: center; gap: 0.75rem;">
                                    <?php if ($user['id'] === $_SESSION['user']['id']): ?>
                                        <button style="color: #d1d5db; cursor: not-allowed;"
                                            title="Bạn không thể tự khóa chính mình" disabled>
                                            <i class="fa-solid fa-user-lock" style="font-size: 1.125rem;"></i>
                                        </button>
                                    <?php else: ?>
                                        <?php if ($user['status'] === 1): ?>
                                            <a href="index.php?id=<?php echo $user['id']; ?>&action=admin-lock-user"
                                                style="color: #ef4444; transition: color 0.2s;" title="Khóa tài khoản"
                                                onclick="return confirm('Bạn có chắc chắn muốn KHÓA tài khoản này?')">
                                                <i class="fa-solid fa-lock" style="font-size: 1.125rem;"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="index.php?id=<?php echo $user['id']; ?>&action=admin-unlock-user"
                                                style="color: #22c55e; transition: color 0.2s;" title="Mở khóa tài khoản"
                                                onclick="return confirm('Bạn có chắc chắn muốn MỞ KHÓA tài khoản này?')">
                                                <i class="fa-solid fa-lock-open" style="font-size: 1.125rem;"></i>
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Khối Chân Bảng Phân Trang -->
    <div class="admin-table-footer">
        <p style="font-size: 0.875rem; color: #4b5563; margin: 0;">
            Hiển thị trang <span style="font-weight: 700; color: #1f2937;"><?php echo $page; ?></span> /
            <?php echo $totalPages; ?> trang (Tổng số <?php echo $totalUsers; ?> tài khoản)
        </p>

        <?php if ($totalPages > 1): ?>
            <div class="admin-pagination">
                <a href="index.php?action=admin-user-list&page=<?= max(1, $page - 1) ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role) ?>&status=<?= urlencode($status) ?>&sort=<?= urlencode($sort) ?>"
                    class="page-link-admin <?= ($page <= 1) ? 'disabled' : '' ?>">
                    Trước
                </a>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="index.php?action=admin-user-list&page=<?= $i ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role) ?>&status=<?= urlencode($status) ?>&sort=<?= urlencode($sort) ?>"
                        class="page-link-admin <?= ($page == $i) ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <a href="index.php?action=admin-user-list&page=<?= min($totalPages, $page + 1) ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role) ?>&status=<?= urlencode($status) ?>&sort=<?= urlencode($sort) ?>"
                    class="page-link-admin <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                    Sau
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/../Layouts/footer.php'; ?>