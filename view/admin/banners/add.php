<?php
// view/admin/banners/add.php
?>
<?php include_once __DIR__ . '/../Layouts/header.php'; ?>

<div style="max-width: 52rem; margin: 0 auto; display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- HEADER TRANG -->
    <div
        style="display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 1px solid #1f2937; padding-bottom: 1.25rem;">
        <div>
            <h1
                style="font-size: 1.5rem; font-weight: 800; color: #ffffff; letter-spacing: -0.025em; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-plus-circle" style="color: #06b6d4;"></i> Thêm Mới Banner Quảng Cáo
            </h1>
            <p style="font-size: 0.875rem; color: #9ca3af; margin-top: 0.375rem;">
                Tải lên hình ảnh banner chất lượng cao để hiển thị trên slider trang chủ khách hàng.
            </p>
        </div>
        <a href="index.php?action=admin-banner-list" class="btn-form-cancel"
            style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.375rem;">
            <i class="fa-solid fa-arrow-left"></i> Trở lại danh sách
        </a>
    </div>

    <!-- THÔNG BÁO LỖI -->
    <?php if (isset($error)): ?>
        <div
            style="padding: 1rem 1.25rem; background-color: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.3); color: #fb7185; border-radius: 0.75rem; font-size: 0.875rem; display: flex; align-items: center; gap: 0.625rem;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.125rem;"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- FORM THÊM BANNER -->
    <form action="index.php?action=admin-banner-add" method="POST" enctype="multipart/form-data"
        class="banner-form-card">

        <!-- KÉO THẢ TẢI ẢNH BANNER -->
        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
            <label class="form-label" style="margin: 0; font-size: 0.875rem; color: #e5e7eb;">
                Hình ảnh banner <span style="color: #ef4444;">*</span>
            </label>

            <label class="banner-dropzone">
                <div
                    style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.375rem;">
                    <div
                        style="width: 3rem; height: 3rem; background-color: rgba(6, 182, 212, 0.1); color: #22d3ee; border-radius: 9999px; display: flex; align-items: center; justify-content: center; margin-bottom: 0.25rem;">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size: 1.25rem;"></i>
                    </div>
                    <p style="margin: 0; font-size: 0.875rem; color: #e5e7eb; font-weight: 600;">Nhấp vào đây để chọn
                        tệp tải lên</p>
                    <p style="margin: 0; font-size: 0.75rem; color: #6b7280;">Khuyến nghị kích thước: 1200x400px (Hỗ trợ
                        JPG, PNG, WEBP)</p>
                </div>
                <input type="file" name="image" required style="display: none;" accept="image/*"
                    onchange="previewImage(event)" />
            </label>

            <!-- Khung xem trước ảnh chọn -->
            <div id="image-preview-container" class="hidden banner-preview-box">
                <p style="font-size: 0.75rem; color: #9ca3af; margin-bottom: 0.375rem;">Xem trước ảnh vừa chọn:</p>
                <img id="image-preview-target" src="#" class="banner-preview-img" alt="Preview">
            </div>
        </div>

        <!-- TIÊU ĐỀ BANNER -->
        <div style="display: flex; flex-direction: column; gap: 0.375rem;">
            <label for="title" class="form-label" style="margin: 0; color: #e5e7eb;">Tiêu đề Banner (Không bắt
                buộc)</label>
            <input type="text" name="title" id="title"
                placeholder="Ví dụ: Siêu phẩm Truyện Tranh bản quyền hot nhất năm" class="form-control-std"
                style="background-color: #161f30; border-color: #1f2937; color: #ffffff; padding: 0.625rem 1rem;">
        </div>

        <!-- LINK LIÊN KẾT -->
        <div style="display: flex; flex-direction: column; gap: 0.375rem;">
            <label for="link_url" class="form-label" style="margin: 0; color: #e5e7eb;">Đường dẫn liên kết (Khi nhấp vào
                ảnh)</label>
            <input type="text" name="link_url" id="link_url" placeholder="Ví dụ: index.php?action=detail&id=5"
                class="form-control-std"
                style="background-color: #161f30; border-color: #1f2937; color: #ffffff; padding: 0.625rem 1rem;">
        </div>

        <!-- TRẠNG THÁI -->
        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
            <label class="form-label" style="margin: 0; color: #e5e7eb;">Trạng thái phát hành</label>
            <div style="display: flex; align-items: center; gap: 1rem; padding-top: 0.25rem;">
                <label class="radio-choice-label">
                    <input type="radio" name="status" value="1" checked style="accent-color: #06b6d4;">
                    <span>Hiển thị ngay lập tức</span>
                </label>
                <label class="radio-choice-label">
                    <input type="radio" name="status" value="0" style="accent-color: #06b6d4;">
                    <span style="color: #9ca3af;">Tạm ẩn dữ liệu</span>
                </label>
            </div>
        </div>

        <!-- CỤM NÚT BẤM -->
        <div
            style="display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem; padding-top: 1.25rem; border-top: 1px solid rgba(31, 41, 55, 0.6); margin-top: 0.5rem;">
            <a href="index.php?action=admin-banner-list" class="btn-form-cancel" style="text-decoration: none;">Hủy
                Bỏ</a>
            <button type="submit" class="btn-form-submit" style="background-color: #06b6d4; padding: 0.625rem 1.5rem;">
                <i class="fa-solid fa-floppy-disk" style="margin-right: 0.375rem;"></i> Lưu Banner
            </button>
        </div>
    </form>
</div>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function () {
            const output = document.getElementById('image-preview-target');
            output.src = reader.result;
            document.getElementById('image-preview-container').classList.remove('hidden');
        }
        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>
<?php include_once __DIR__ . '/../Layouts/footer.php'; ?>