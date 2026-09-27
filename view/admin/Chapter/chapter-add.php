<?php
include_once __DIR__ . '/../Layouts/header.php';

// Lấy thông tin trang và bộ lọc để giữ vị trí cũ
$currentPage = $_REQUEST['page'] ?? 1;
$search = $_REQUEST['search'] ?? '';
$statusFilter = $_REQUEST['status_filter'] ?? $_REQUEST['status'] ?? '';
$sort = $_REQUEST['sort'] ?? '';

$queryString = "&page={$currentPage}&search=" . urlencode($search) . "&status=" . urlencode($statusFilter) . "&sort=" . urlencode($sort);
?>

<!-- HEADER TRANG: TIÊU ĐỀ BÊN TRÁI - NÚT QUAY LẠI BÊN PHẢI -->
<div class="admin-page-header"
    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h2 class="admin-page-title" style="margin: 0; font-size: 1.5rem; font-weight: 700; color: #1f2937;">
        Thêm chương mới: <?php echo htmlspecialchars($comic['title']); ?>
    </h2>
    <a href="index.php?action=admin-chapter-list&comic_id=<?php echo $comic['id']; ?><?= $queryString ?>"
        class="btn-form-cancel"
        style="background-color: #374151; color: #ffffff; text-decoration: none; display: inline-flex; align-items: center; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
        <i class="fa-solid fa-arrow-left" style="margin-right: 0.375rem;"></i> Quay lại danh sách
    </a>
</div>

<?php if (!empty($error)): ?>
    <div
        style="margin-bottom: 1rem; padding: 1rem; background-color: #fef2f2; border-left: 4px solid #ef4444; color: #b91c1c; border-radius: 0.375rem;">
        <i class="fa-solid fa-circle-exclamation" style="margin-right: 0.25rem;"></i> <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<form action="index.php?action=admin-add-chapter&comic_id=<?php echo $comic['id']; ?>" method="post"
    enctype="multipart/form-data" class="admin-form-grid">

    <input type="hidden" name="page" value="<?= $currentPage ?>">
    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
    <input type="hidden" name="status_filter" value="<?= htmlspecialchars($statusFilter) ?>">
    <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
    <input type="hidden" name="comic_id" value="<?php echo $comic['id']; ?>">

    <!-- CỘT BÊN TRÁI: THÔNG TIN CHƯƠNG -->
    <div class="admin-form-main-col" style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="admin-card-section"
            style="padding: 1.5rem; background-color: #ffffff; border-radius: 0.75rem; border: 1px solid #f3f4f6; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h3 class="form-card-title"
                style="font-size: 1.125rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem; border-bottom: 1px solid #f3f4f6; padding-bottom: 0.5rem;">
                Thông tin chương mới
            </h3>

            <!-- XẾP NGANG 2 Ô INPUT BẰNG GRID 2 CỘT -->
            <div class="form-grid-2col"
                style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem;">
                <div>
                    <label for="chapter_number" class="form-label"
                        style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                        Số chương <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="number" step="0.01" name="chapter_number" id="chapter_number" required
                        value="<?= htmlspecialchars($_POST['chapter_number'] ?? '') ?>" class="form-control-std"
                        style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; outline: none; font-size: 0.875rem;" />
                </div>
                <div>
                    <label for="chapter_title" class="form-label"
                        style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                        Tên chương
                    </label>
                    <input type="text" name="chapter_title" id="chapter_title"
                        value="<?= htmlspecialchars($_POST['chapter_title'] ?? '') ?>" class="form-control-std"
                        style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; outline: none; font-size: 0.875rem;" />
                </div>
            </div>
        </div>

        <!-- CỤM NÚT BẤM DƯỚI BÊN TRÁI -->
        <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
            <button type="reset" class="btn-form-cancel"
                style="padding: 0.625rem 1.25rem; background-color: #f3f4f6; color: #374151; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; border: none; cursor: pointer;">
                Xóa nhập liệu
            </button>
            <button type="submit" class="btn-form-submit"
                style="padding: 0.625rem 1.25rem; background-color: #4f46e5; color: #ffffff; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; border: none; cursor: pointer; display: inline-flex; align-items: center; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                <i class="fa-solid fa-cloud-arrow-up" style="margin-right: 0.5rem;"></i> Lưu & Đăng chương
            </button>
        </div>
    </div>

    <!-- CỘT BÊN PHẢI: KÉO THẢ UPLOAD NỘI DUNG CHƯƠNG -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="admin-card-section"
            style="padding: 1.5rem; background-color: #ffffff; border-radius: 0.75rem; border: 1px solid #f3f4f6; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div
                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid #f3f4f6; padding-bottom: 0.5rem;">
                <h3 class="form-card-title"
                    style="margin: 0; border: none; padding: 0; font-size: 1.125rem; font-weight: 600; color: #1f2937;">
                    Nội dung chương
                </h3>
                <button type="button" id="clearAllImages"
                    style="font-size: 0.875rem; color: #dc2626; border: none; background: none; cursor: pointer; font-weight: 500; display: flex; align-items: center; gap: 0.25rem;">
                    <i class="fa-solid fa-trash-can"></i> Xóa tất cả
                </button>
            </div>

            <input type="file" id="chapterImagesInput" name="chapter_images[]" multiple class="hidden"
                accept=".jpg,.jpeg,.png,.webp,.avif" style="display: none;" />

            <!-- KHUNG KÉO THẢ ÁNH NÉT ĐỨT -->
            <div id="dropZone" class="dropzone-box"
                style="border: 2px dashed #d1d5db; border-radius: 0.75rem; padding: 2rem; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; margin-bottom: 1rem; transition: all 0.2s;">
                <div id="uploadPrompt"
                    style="display: flex; flex-direction: column; align-items: center; justify-content: center; pointer-events: none;">
                    <div
                        style="width: 3rem; height: 3rem; background-color: #eef2ff; color: #4f46e5; border-radius: 9999px; display: flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
                        <i class="fa-regular fa-images" style="font-size: 1.25rem;"></i>
                    </div>
                    <p style="font-size: 0.875rem; color: #4b5563; text-align: center; margin: 0;">
                        Kéo thả ảnh vào đây hoặc <span style="color: #4f46e5; font-weight: 500;">chọn ảnh</span>
                    </p>
                    <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.5rem; text-align: center;">
                        Hỗ trợ: JPG, PNG, WEBP, AVIF. Hệ thống tự đổi tên dạng số.
                    </p>
                </div>
            </div>

            <div id="imagesPreviewList" class="chapter-preview-list">
                <p
                    style="text-align: center; font-size: 0.875rem; color: #9ca3af; padding: 1rem 0; font-style: italic; margin: 0;">
                    Chưa có ảnh nào được chọn.
                </p>
            </div>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('chapterImagesInput');
        const previewList = document.getElementById('imagesPreviewList');
        const clearAllBtn = document.getElementById('clearAllImages');

        let selectedFiles = [];
        let cachedObjectUrls = [];

        if (typeof Sortable !== 'undefined' && previewList) {
            new Sortable(previewList, {
                animation: 150,
                handle: '.fa-grip-vertical',
                ghostClass: 'bg-indigo-50',
                onEnd: function () {
                    reorderFilesArray();
                }
            });
        }

        function reorderFilesArray() {
            const currentRows = previewList.querySelectorAll('.preview-row');
            const newSortedFiles = [];

            currentRows.forEach((row) => {
                const originalIndex = parseInt(row.getAttribute('data-file-id'));
                newSortedFiles.push(selectedFiles[originalIndex]);
            });

            selectedFiles = newSortedFiles;
            syncWithFormOnly();
        }

        if (dropZone) {
            dropZone.addEventListener('click', () => fileInput.click());

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropZone.style.borderColor = '#4f46e5';
                    dropZone.style.backgroundColor = '#eef2ff';
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropZone.style.borderColor = '#d1d5db';
                    dropZone.style.backgroundColor = '';
                }, false);
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                handleFiles(e.dataTransfer.files);
            });

            fileInput.addEventListener('change', (e) => {
                handleFiles(e.target.files);
            });
        }

        function handleFiles(files) {
            const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'avif'];

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const ext = file.name.split('.').pop().toLowerCase();

                if (allowedExtensions.includes(ext)) {
                    selectedFiles.push(file);
                }
            }
            renderAllPreviews();
        }

        function clearCacheUrls() {
            cachedObjectUrls.forEach(url => URL.revokeObjectURL(url));
            cachedObjectUrls = [];
        }

        function renderAllPreviews() {
            clearCacheUrls();
            previewList.innerHTML = '';

            if (selectedFiles.length === 0) {
                previewList.innerHTML =
                    '<p style="text-align: center; font-size: 0.875rem; color: #9ca3af; padding: 1rem 0; font-style: italic; margin: 0;">Chưa có ảnh nào được chọn.</p>';
                fileInput.files = new DataTransfer().files;
                return;
            }

            selectedFiles.forEach((file, index) => {
                const objectUrl = URL.createObjectURL(file);
                cachedObjectUrls.push(objectUrl);

                const row = document.createElement('div');
                row.className = "preview-row preview-row-card";
                row.setAttribute('data-file-id', index);

                row.innerHTML = `
                <div class="preview-row-content">
                    <i class="fa-solid fa-grip-vertical" style="color: #9ca3af; cursor: move; pointer-events: auto;" title="Kéo thả để sắp xếp"></i>
                    <span class="number-badge" style="font-size: 0.75rem; font-weight: 600; color: #6b7280; width: 1.25rem;">${index + 1}</span>
                    <div class="preview-thumb-box">
                        <img src="${objectUrl}" alt="preview" />
                    </div>
                    <span style="font-size: 0.875rem; font-weight: 500; color: #374151; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${file.name}">${file.name}</span>
                </div>
                <button type="button" class="remove-btn" style="color: #9ca3af; border: none; background: none; cursor: pointer; padding: 0.25rem;" title="Xóa ảnh này">
                    <i class="fa-solid fa-xmark" style="font-size: 1.125rem;"></i>
                </button>
            `;

                previewList.appendChild(row);
            });

            syncWithFormOnly();
            bindRemoveEvents();
        }

        function syncWithFormOnly() {
            const dataTransfer = new DataTransfer();
            const rows = previewList.querySelectorAll('.preview-row');

            rows.forEach((row, index) => {
                const badge = row.querySelector('.number-badge');
                if (badge) badge.textContent = index + 1;
                row.setAttribute('data-file-id', index);
                dataTransfer.items.add(selectedFiles[index]);
            });

            fileInput.files = dataTransfer.files;
        }

        function bindRemoveEvents() {
            const removeButtons = previewList.querySelectorAll('.remove-btn');
            removeButtons.forEach((btn) => {
                btn.onclick = function (e) {
                    e.stopPropagation();
                    const row = this.closest('.preview-row');
                    const indexToRemove = parseInt(row.getAttribute('data-file-id'));

                    selectedFiles.splice(indexToRemove, 1);
                    renderAllPreviews();
                };
            });
        }

        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (selectedFiles.length > 0 && confirm('Bạn có chắc chắn muốn xóa toàn bộ ảnh đã chọn?')) {
                    selectedFiles = [];
                    renderAllPreviews();
                }
            });
        }
    });
</script>

<?php include_once __DIR__ . '/../Layouts/footer.php'; ?>