<?php
// view/admin/banners/edit.php
?>
<?php include_once __DIR__ . '/../Layouts/header.php'; ?>

<div style="max-width: 52rem; margin: 0 auto; display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- HEADER TRANG -->
    <div
        style="display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 1px solid #1f2937; padding-bottom: 1.25rem;">
        <div>
            <h1
                style="font-size: 1.5rem; font-weight: 800; color: #ffffff; letter-spacing: -0.025em; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-pen-to-square" style="color: #06b6d4;"></i> Chỉnh Sửa Thông Tin Banner
            </h1>
            <p style="font-size: 0.875rem; color: #9ca3af; margin-top: 0.375rem;">
                Cập nhật lại hình ảnh hiển thị, tiêu đề hoặc cấu hình ẩn/hiện cấu trúc banner mã số <span
                    style="color: #22d3ee; font-weight: 700;">#<?= $banner['id'] ?></span>
            </p>
        </div>
        <a href="index.php?action=admin-banner-list" class="btn-form-cancel"
            style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.375rem;">
            <i class="fa-solid fa-arrow-left"></i> Trở lại danh sách
        </a>
    </div>

    <!-- FORM CHỈNH SỬA BANNER -->
    <form action="index.php?action=admin-banner-edit&id=<?= $banner['id'] ?>" method="POST"
        enctype="multipart/form-data" class="banner-form-card">

        <!-- LƯỚI BẢNG SO SÁNH ẢNH CŨ VÀ ĐỔI ẢNH MỚI -->
        <div class="form-grid-2col" style="grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem;">

            <!-- Cột ảnh đang chạy -->
            <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                <label class="form-label" style="margin: 0; color: #9ca3af; font-size: 0.75rem;">Ảnh hiện tại đang
                    chạy</label>
                <div
                    style="position: relative; height: 8rem; border-radius: 0.75rem; overflow: hidden; border: 1px solid #1f2937; background-color: #0d111a;">
                    <img src="<?= htmlspecialchars($banner['image_url']) ?>"
                        style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            </div>

            <!-- Cột chọn ảnh mới -->
            <div style="grid-column: span 2 / span 2; display: flex; flex-direction: column; gap: 0.375rem;">
                <label class="form-label" style="margin: 0; color: #e5e7eb; font-size: 0.75rem;">Thay đổi ảnh mới (Nếu
                    muốn đổi)</label>
                <label class="banner-dropzone" style="height: 8rem; min-height: initial; padding: 0.5rem;">
                    <div
                        style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.25rem;">
                        <i class="fa-solid fa-file-image"
                            style="color: #06b6d4; font-size: 1.5rem; margin-bottom: 0.25rem;"></i>
                        <p style="font-size: 0.75rem; color: #d1d5db; font-weight: 600; margin: 0;">Chọn file ảnh mới
                            thay thế</p>
                        <p style="font-size: 10px; color: #6b7280; margin: 0;">Định dạng: JPG, PNG, WEBP</p>
                    </div>
                    <input type="file" name="image" style="display: none;" accept="image/*" />
                </label>
            </div>
        </div>

        <!-- TIÊU ĐỀ BANNER -->
        <div style="display: flex; flex-direction: column; gap: 0.375rem;">
            <label for="title" class="form-label" style="margin: 0; color: #e5e7eb;">Tiêu đề Banner</label>
            <input type="text" name="title" id="title" value="<?= htmlspecialchars($banner['title'] ?? '') ?>"
                placeholder="Nhập tiêu đề..." class="form-control-std"
                style="background-color: #161f30; border-color: #1f2937; color: #ffffff; padding: 0.625rem 1rem;">
        </div>

        <!-- LINK LIÊN KẾT -->
        <div style="display: flex; flex-direction: column; gap: 0.375rem;">
            <label for="link_url" class="form-label" style="margin: 0; color: #e5e7eb;">Đường dẫn liên kết điều
                hướng</label>
            <input type="text" name="link_url" id="link_url" value="<?= htmlspecialchars($banner['link_url'] ?? '') ?>"
                placeholder="Nhập link chuyển hướng..." class="form-control-std"
                style="background-color: #161f30; border-color: #1f2937; color: #ffffff; padding: 0.625rem 1rem;">
        </div>

        <!-- TRẠNG THÁI -->
        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
            <label class="form-label" style="margin: 0; color: #e5e7eb;">Trạng thái phát hành</label>
            <div style="display: flex; align-items: center; gap: 1rem; padding-top: 0.25rem;">
                <label class="radio-choice-label">
                    <input type="radio" name="status" value="1" <?= $banner['status'] == 1 ? 'checked' : '' ?>
                        style="accent-color: #06b6d4;">
                    <span>Hiển thị</span>
                </label>
                <label class="radio-choice-label">
                    <input type="radio" name="status" value="0" <?= $banner['status'] == 0 ? 'checked' : '' ?>
                        style="accent-color: #06b6d4;">
                    <span style="color: #9ca3af;">Đang Ẩn</span>
                </label>
            </div>
        </div>

        <!-- CỤM NÚT BẤM -->
        <div
            style="display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem; padding-top: 1.25rem; border-top: 1px solid rgba(31, 41, 55, 0.6); margin-top: 0.5rem;">
            <a href="index.php?action=admin-banner-list" class="btn-form-cancel" style="text-decoration: none;">Hủy
                Bỏ</a>
            <button type="submit" class="btn-form-submit" style="background-color: #06b6d4; padding: 0.625rem 1.5rem;">
                <i class="fa-solid fa-floppy-disk" style="margin-right: 0.375rem;"></i> Cập Nhật
            </button>
        </div>
    </form>
</div>
<?php include_once __DIR__ . '/../Layouts/footer.php'; ?>