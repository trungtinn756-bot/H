<?php include_once __DIR__ . '/Layouts/header.php'; ?>

<div style="display: flex; flex-direction: column; gap: 2rem;">
    <!-- HEADER TRANG -->
    <div
        style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e5e7eb; padding-bottom: 1rem;">
        <h1 style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin: 0;">Hệ Thống Thống Kê & Báo Cáo</h1>
        <span
            style="font-size: 0.875rem; background-color: #f3f4f6; color: #4b5563; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 500; display: inline-flex; align-items: center;">
            <i class="far fa-clock" style="margin-right: 0.375rem;"></i> Hôm nay: <?= date('d/m/Y') ?>
        </span>
    </div>

    <!-- KHỐI 3 THẺ TỔNG QUAN (NẰM NGANG 3 CỘT chuẩn responsive) -->
    <div class="stat-cards-grid">

        <!-- Thẻ 1: Tổng lượt đọc -->
        <div class="stat-card-widget blue">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <p
                        style="color: #dbeafe; font-size: 0.875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">
                        6.1.1 Tổng Lượt Đọc</p>
                    <h3
                        style="font-size: 2.25rem; font-weight: 700; margin-top: 0.5rem; margin-bottom: 0; color: #ffffff;">
                        <?= number_format($totalViews) ?>
                    </h3>
                </div>
                <div class="stat-icon-badge">
                    <i class="fas fa-eye" style="font-size: 1.5rem; color: #ffffff;"></i>
                </div>
            </div>
            <p
                style="color: #bfdbfe; font-size: 0.75rem; margin-top: 1rem; margin-bottom: 0; display: flex; align-items: center;">
                <i class="fas fa-chart-line" style="margin-right: 0.375rem;"></i> Tính trên tổng lượt đọc tất cả các
                chương
            </p>
        </div>

        <!-- Thẻ 2: Tổng thành viên -->
        <div class="stat-card-widget emerald">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <p
                        style="color: #d1fae5; font-size: 0.875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">
                        6.1.3 Tổng Thành Viên</p>
                    <h3
                        style="font-size: 2.25rem; font-weight: 700; margin-top: 0.5rem; margin-bottom: 0; color: #ffffff;">
                        <?= number_format($userStats['total']) ?>
                    </h3>
                </div>
                <div class="stat-icon-badge">
                    <i class="fas fa-users" style="font-size: 1.5rem; color: #ffffff;"></i>
                </div>
            </div>
            <p
                style="color: #a7f3d0; font-size: 0.75rem; margin-top: 1rem; margin-bottom: 0; display: flex; align-items: center;">
                <i class="fas fa-check-circle" style="margin-right: 0.375rem;"></i> Đang hoạt động: <span
                    style="font-weight: 700; margin-left: 0.25rem;"><?= $userStats['active'] ?></span>
            </p>
        </div>

        <!-- Thẻ 3: Tài khoản bị khóa -->
        <div class="stat-card-widget rose">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <p
                        style="color: #ffe4e6; font-size: 0.875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">
                        Tài Khoản Bị Khóa</p>
                    <h3
                        style="font-size: 2.25rem; font-weight: 700; margin-top: 0.5rem; margin-bottom: 0; color: #ffffff;">
                        <?= number_format($userStats['locked']) ?>
                    </h3>
                </div>
                <div class="stat-icon-badge">
                    <i class="fas fa-user-slash" style="font-size: 1.5rem; color: #ffffff;"></i>
                </div>
            </div>
            <p
                style="color: #fecdd3; font-size: 0.75rem; margin-top: 1rem; margin-bottom: 0; display: flex; align-items: center;">
                <i class="fas fa-exclamation-triangle" style="margin-right: 0.375rem;"></i> Chiếm
                <?= $userStats['total'] > 0 ? round(($userStats['locked'] / $userStats['total']) * 100, 1) : 0 ?>% trên
                tổng quy mô thành viên
            </p>
        </div>
    </div>

    <!-- KHỐI BẢNG THỐNG KÊ VÀ BIỂU ĐỒ BÊN DƯỚI -->
    <div class="admin-form-grid" style="align-items: stretch;">

        <!-- BẢNG TOP 5 TRUYỆN PHỔ BIẾN NHẤT (BÊN TRÁI 2 CỘT) -->
        <div class="admin-form-main-col admin-card-section" style="padding: 1.5rem; margin-bottom: 0;">
            <div
                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid #f3f4f6; padding-bottom: 0.5rem;">
                <h2
                    style="font-size: 1.125rem; font-weight: 700; color: #1f2937; margin: 0; display: flex; align-items: center;">
                    <i class="fas fa-fire" style="color: #f97316; margin-right: 0.5rem;"></i> 6.1.2 Top 5 Truyện Phổ
                    Biến Nhất
                </h2>
                <a href="index.php?action=admin-comic-list"
                    style="font-size: 0.75rem; color: #4f46e5; text-decoration: none; font-weight: 600;">Xem tất cả</a>
            </div>

            <div class="admin-table-container">
                <table class="admin-data-table">
                    <thead>
                        <tr
                            style="background-color: #f9fafb; color: #6b7280; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                            <th style="padding: 0.75rem 1rem;">Ảnh Bìa</th>
                            <th style="padding: 0.75rem 1rem;">Tên Bộ Truyện</th>
                            <th style="padding: 0.75rem 1rem; text-align: center;">Tổng Lượt Đọc</th>
                            <th style="padding: 0.75rem 1rem; text-align: center;">Lượt Theo Dõi</th>
                        </tr>
                    </thead>
                    <tbody style="color: #374151;">
                        <?php if (!empty($popularComics)): ?>
                            <?php foreach ($popularComics as $comic): ?>
                                <tr class="admin-table-row">
                                    <td style="padding: 0.75rem 1rem;">
                                        <img src="uploads/comics/<?= htmlspecialchars($comic['thumbnail'] ?: 'default.png') ?>"
                                            alt="Thumbnail"
                                            style="width: 2.5rem; height: 3.5rem; object-fit: cover; border-radius: 0.25rem; border: 1px solid #f3f4f6; box-shadow: 0 1px 2px rgba(0,0,0,0.05);"
                                            onerror="this.src='assets/images/default-comic.png'">
                                    </td>
                                    <td style="padding: 0.75rem 1rem; font-weight: 500; color: #374151;">
                                        <a href="index.php?action=detail&id=<?= $comic['id'] ?>" target="_blank"
                                            style="color: #374151; text-decoration: none; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; transition: color 0.2s;"
                                            onmouseover="this.style.color='#4f46e5'" onmouseout="this.style.color='#374151'">
                                            <?= htmlspecialchars($comic['title']) ?>
                                        </a>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center; font-weight: 700; color: #111827;">
                                        <?= number_format($comic['total_views']) ?>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center; color: #f43f5e; font-weight: 500;">
                                        <i class="fas fa-heart" style="margin-right: 0.25rem; font-size: 0.75rem;"></i>
                                        <?= number_format($comic['follow_count']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4"
                                    style="padding: 2rem 0; text-align: center; color: #9ca3af; font-style: italic;">Chưa có
                                    dữ liệu thống kê truyện.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- BẢNG TỈ LỆ TRẠNG THÁI TÀI KHOẢN (BÊN PHẢI 1 CỘT) -->
        <div class="admin-card-section"
            style="padding: 1.5rem; margin-bottom: 0; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <h2
                    style="font-size: 1.125rem; font-weight: 700; color: #1f2937; margin: 0 0 1.5rem 0; display: flex; align-items: center;">
                    <i class="fas fa-chart-pie" style="color: #6366f1; margin-right: 0.5rem;"></i> Trạng Thái Tài Khoản
                </h2>

                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <!-- Tiến trình Đang hoạt động -->
                    <div>
                        <div
                            style="display: flex; justify-content: space-between; font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.375rem;">
                            <span>ĐANG HOẠT ĐỘNG</span>
                            <span style="color: #059669; font-weight: 700;"><?= $userStats['active'] ?> /
                                <?= $userStats['total'] ?></span>
                        </div>
                        <div class="progress-track-bg">
                            <?php $activeBar = $userStats['total'] > 0 ? ($userStats['active'] / $userStats['total']) * 100 : 0; ?>
                            <div class="progress-track-fill emerald" style="width: <?= $activeBar ?>%"></div>
                        </div>
                    </div>

                    <!-- Tiến trình Đang bị khóa -->
                    <div>
                        <div
                            style="display: flex; justify-content: space-between; font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.375rem;">
                            <span>ĐANG BỊ KHÓA / VI PHẠM</span>
                            <span style="color: #e11d48; font-weight: 700;"><?= $userStats['locked'] ?> /
                                <?= $userStats['total'] ?></span>
                        </div>
                        <div class="progress-track-bg">
                            <?php $lockedBar = $userStats['total'] > 0 ? ($userStats['locked'] / $userStats['total']) * 100 : 0; ?>
                            <div class="progress-track-fill rose" style="width: <?= $lockedBar ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nút điều hướng chân thẻ -->
            <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #f9fafb; text-align: center;">
                <a href="index.php?action=admin-user-list" class="btn-form-cancel"
                    style="display: inline-flex; width: 100%; justify-content: center; align-items: center; text-decoration: none;">
                    <i class="fas fa-cog" style="margin-right: 0.5rem;"></i> Đi đến Quản lý người dùng
                </a>
            </div>
        </div>

    </div>
</div>

<?php include_once __DIR__ . '/Layouts/footer.php'; ?>