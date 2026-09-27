<?php
// view/admin/banners/list.php
$success = $_GET['success'] ?? '';
?>
<?php include_once __DIR__ . '/../Layouts/header.php'; ?>

<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- HEADER DANH SÁCH BANNER -->
    <div
        style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; border-bottom: 1px solid #1f2937; padding-bottom: 1.25rem; gap: 1rem;">
        <div>
            <h1
                style="font-size: 1.5rem; font-weight: 800; color: #ffffff; letter-spacing: -0.025em; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                <i class="fa-solid fa-images" style="color: #06b6d4;"></i> Quản Lý Banner & Slider
            </h1>
            <p style="font-size: 0.875rem; color: #9ca3af; margin-top: 0.25rem;">Thêm, sửa, xóa hoặc thay đổi trạng thái
                hiển thị của các banner trên trang chủ.</p>
        </div>
        <a href="index.php?action=admin-banner-add" class="btn-form-submit"
            style="background-color: #06b6d4; text-decoration: none; gap: 0.5rem; padding: 0.625rem 1.25rem;">
            <i class="fa-solid fa-plus" style="font-size: 0.75rem;"></i> Thêm Banner Mới
        </a>
    </div>

    <!-- CÁC THÔNG BÁO THÀNH CÔNG -->
    <?php if ($success === 'add'): ?>
        <div
            style="padding: 1rem; background-color: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; border-radius: 0.75rem; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-circle-check"></i> Thêm mới banner thành công!
        </div>
    <?php elseif ($success === 'edit'): ?>
        <div
            style="padding: 1rem; background-color: rgba(6, 182, 212, 0.1); border: 1px solid rgba(6, 182, 212, 0.3); color: #22d3ee; border-radius: 0.75rem; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-circle-check"></i> Cập nhật thông tin banner thành công!
        </div>
    <?php elseif ($success === 'delete'): ?>
        <div
            style="padding: 1rem; background-color: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.3); color: #fb7185; border-radius: 0.75rem; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-circle-check"></i> Đã xóa banner thành công!
        </div>
    <?php endif; ?>

    <!-- BẢNG DỮ LIỆU BANNER -->
    <div class="admin-card-section" style="box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);">
        <div class="admin-table-container">
            <table class="admin-data-table">
                <thead>
                    <tr
                        style="background-color: rgba(17, 24, 39, 0.8); border-bottom: 1px solid #1f2937; color: #9ca3af; text-transform: uppercase; font-size: 0.75rem;">
                        <th style="width: 4rem; text-align: center;">ID</th>
                        <th style="width: 12rem;">Hình Ảnh</th>
                        <th>Tiêu Đề / Liên Kết</th>
                        <th style="width: 9rem; text-align: center;">Trạng Thái</th>
                        <th style="width: 8rem; text-align: right;">Hành Động</th>
                    </tr>
                </thead>
                <tbody style="color: #d1d5db;">
                    <?php if (!empty($banners)): ?>
                        <?php foreach ($banners as $b): ?>
                            <tr class="admin-table-row">
                                <td style="text-align: center; font-family: monospace; color: #6b7280; font-weight: 700;">
                                    <?= $b['id'] ?>
                                </td>
                                <td>
                                    <div
                                        style="position: relative; width: 10rem; height: 5rem; border-radius: 0.5rem; overflow: hidden; border: 1px solid #1f2937; background-color: #0d111a; display: flex; align-items: center; justify-content: center;">
                                        <img src="<?= htmlspecialchars($b['image_url']) ?>"
                                            alt="<?= htmlspecialchars($b['title']) ?>"
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                </td>
                                <td>
                                    <div
                                        style="font-weight: 700; color: #ffffff; font-size: 0.9375rem; margin-bottom: 0.25rem;">
                                        <?= !empty($b['title']) ? htmlspecialchars($b['title']) : '<span style="color: #6b7280; font-style: italic; font-size: 0.75rem;">Không có tiêu đề</span>' ?>
                                    </div>
                                    <div
                                        style="font-size: 0.75rem; color: #9ca3af; display: flex; align-items: center; gap: 0.375rem; max-width: 28rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <i class="fa-solid fa-link" style="color: #4b5563; font-size: 10px;"></i>
                                        <?php if (!empty($b['link_url'])): ?>
                                            <a href="<?= htmlspecialchars($b['link_url']) ?>" target="_blank"
                                                style="color: #22d3ee; text-decoration: underline;"><?= htmlspecialchars($b['link_url']) ?></a>
                                        <?php else: ?>
                                            <span style="color: #4b5563; font-style: italic;">Không chuyển hướng liên kết</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($b['status'] == 1): ?>
                                        <span class="badge-status-pill active">
                                            <span class="status-dot"></span> Hiển thị
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-status-pill inactive"
                                            style="background-color: #1f2937; color: #9ca3af; border-color: #374151;">
                                            <span class="status-dot" style="background-color: #6b7280;"></span> Đang Ẩn
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem;">
                                        <a href="index.php?action=admin-banner-edit&id=<?= $b['id'] ?>" class="btn-action-icon"
                                            style="background-color: #1f2937; color: #22d3ee; border: 1px solid rgba(55, 65, 81, 0.6);"
                                            title="Sửa banner">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="index.php?action=admin-banner-delete&id=<?= $b['id'] ?>"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn banner này? Không thể hoàn tác hành động này!');"
                                            class="btn-action-icon red"
                                            style="background-color: #1f2937; border: 1px solid rgba(55, 65, 81, 0.6);"
                                            title="Xóa banner">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5"
                                style="padding: 3.5rem 0; text-align: center; color: #6b7280; font-style: italic;">
                                <i class="fa-regular fa-folder-open"
                                    style="font-size: 2rem; margin-bottom: 0.5rem; display: block; color: #4b5563;"></i>
                                Không tìm thấy banner nào trong hệ thống dữ liệu.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include_once __DIR__ . '/../Layouts/footer.php'; ?>