<?php
include_once __DIR__ . '/../Layouts/header.php';

// Lấy thông tin trang và bộ lọc để giữ vị trí cũ
$currentPage = $_REQUEST['page'] ?? 1;
$search = $_REQUEST['search'] ?? '';
$statusFilter = $_REQUEST['status_filter'] ?? $_REQUEST['status'] ?? '';
$sort = $_REQUEST['sort'] ?? '';

$queryString = "&page={$currentPage}&search=" . urlencode($search) . "&status=" . urlencode($statusFilter) . "&sort=" . urlencode($sort);
?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Thêm Truyện Tranh Mới</h1>
    <a href="index.php?action=admin-comic-list<?= $queryString ?>" class="btn-form-cancel"
        style="background-color: #4b5563; color: #ffffff; text-decoration: none; display: inline-flex; align-items: center;">
        <i class="fa-solid fa-arrow-left" style="margin-right: 0.375rem;"></i> Quay lại danh sách
    </a>
</div>

<!-- THÔNG BÁO LỖI NẾU CÓ -->
<?php if (!empty($error)): ?>
    <div
        style="margin-bottom: 1rem; padding: 1rem; background-color: #fef2f2; border-left: 4px solid #ef4444; color: #b91c1c; border-radius: 0.375rem;">
        <i class="fa-solid fa-triangle-exclamation" style="margin-right: 0.5rem;"></i> <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<form action="index.php?action=admin-add-comic" method="post" enctype="multipart/form-data" id="comicForm"
    class="admin-form-grid">
    <input type="hidden" name="page" value="<?= $currentPage ?>">
    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
    <input type="hidden" name="status_filter" value="<?= htmlspecialchars($statusFilter) ?>">
    <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">

    <div class="admin-form-main-col" style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="admin-card-section" style="padding: 1.5rem;">
            <h3 class="form-card-title">Thông tin truyện</h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div>
                    <label for="title" class="form-label" style="margin-bottom: 0.25rem;">Tên truyện <span
                            style="color: #ef4444;">*</span></label>
                    <input type="text" name="title" id="title" required
                        value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" class="form-control-std" />
                </div>
                <div>
                    <label for="other_title" class="form-label" style="margin-bottom: 0.25rem;">Tên khác</label>
                    <input type="text" name="other_title" id="other_title"
                        value="<?= htmlspecialchars($_POST['other_title'] ?? '') ?>" class="form-control-std" />
                </div>
                <div>
                    <label for="slug" class="form-label" style="margin-bottom: 0.25rem;">Slug <span
                            style="color: #ef4444;">*</span></label>
                    <input type="text" name="slug" id="slug" required
                        value="<?= htmlspecialchars($_POST['slug'] ?? '') ?>" class="form-control-std" />
                </div>
                <div>
                    <label for="author" class="form-label" style="margin-bottom: 0.25rem;">Tác giả</label>
                    <input type="text" name="author" id="author" placeholder="Nếu bỏ trống sẽ để: Đang cập nhật"
                        value="<?= htmlspecialchars($_POST['author'] ?? '') ?>" class="form-control-std" />
                </div>
                <div>
                    <label for="summary" class="form-label" style="margin-bottom: 0.25rem;">Mô tả nội dung</label>
                    <textarea name="summary" id="summary" rows="6"
                        class="form-control-std"><?= htmlspecialchars($_POST['summary'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
            <button type="reset" class="btn-form-cancel">
                Xóa nhập liệu
            </button>
            <button type="submit" class="btn-form-submit">
                <i class="fa-solid fa-cloud-arrow-up" style="margin-right: 0.5rem;"></i> Lưu & Đăng chương
            </button>
        </div>
    </div>

    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Kéo thả ảnh đại diện -->
        <div class="admin-card-section" style="padding: 1.5rem;">
            <h3 class="form-card-title">Ảnh bìa</h3>

            <input type="file" name="thumbnail" id="thumbnail" accept=".jpg,.jpeg,.png,.webp,.avif" class="hidden">

            <div id="dropZone" class="dropzone-box">
                <div id="uploadPrompt"
                    style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    <div
                        style="width: 3rem; height: 3rem; background-color: #eef2ff; color: #4f46e5; border-radius: 9999px; display: flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size: 1.25rem;"></i>
                    </div>
                    <p style="font-size: 0.875rem; color: #4b5563; text-align: center;">Kéo thả ảnh vào đây hoặc <span
                            style="color: #4f46e5; font-weight: 500;">chọn ảnh</span></p>
                    <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.5rem;">Hỗ trợ: JPG, PNG, WEBP, AVIF. Tối
                        đa: 3MB.</p>
                </div>

                <div id="previewContainer" class="hidden"
                    style="position: absolute; inset: 0; padding: 0.5rem; background-color: #ffffff; border-radius: 0.75rem;">
                    <img id="imagePreview" src=""
                        style="width: 100%; height: 100%; object-fit: contain; border-radius: 0.5rem;" alt="Preview">
                    <button type="button" id="removeImage"
                        style="position: absolute; top: 1rem; right: 1rem; background-color: #ef4444; color: #ffffff; padding: 0.5rem; border-radius: 9999px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border: none; cursor: pointer; z-index: 10;">
                        <i class="fa-solid fa-trash-can" style="font-size: 0.875rem;"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Phân loại & Thể loại -->
        <div class="admin-card-section" style="padding: 1.5rem;">
            <h3 class="form-card-title">Phân loại</h3>
            <div style="margin-bottom: 1.25rem;">
                <label for="status" class="form-label" style="margin-bottom: 0.25rem;">Trạng thái</label>
                <div class="chapter-select-wrapper">
                    <select name="status" id="status" class="form-control-std">
                        <option value="0">Đang tiến hành</option>
                        <option value="1">Đã hoàn thành</option>
                        <option value="2">Tạm ngưng</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label"
                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <span>Thể loại <span style="font-size: 0.75rem; font-weight: 400; color: #6b7280;">(Tích chọn thể
                            loại phù hợp)</span></span>
                    <button type="button" id="uncheckAllGenres"
                        style="font-size: 0.75rem; color: #ef4444; border: none; background: none; cursor: pointer; font-weight: 500;">
                        <i class="fa-solid fa-square-minus" style="margin-right: 0.25rem;"></i>Bỏ chọn tất cả
                    </button>
                </label>

                <div class="input-icon-wrapper" style="margin-bottom: 0.5rem;">
                    <span class="input-icon-left"><i class="fa-solid fa-magnifying-glass"
                            style="font-size: 0.75rem;"></i></span>
                    <input type="text" id="searchGenre" placeholder="Tìm nhanh thể loại..." class="form-control-std"
                        style="padding-left: 2rem; font-size: 0.75rem;" />
                </div>

                <div class="genre-scroll-selector">
                    <?php if (!empty($all_genres)): ?>
                        <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.5rem;"
                            id="genreContainer">
                            <?php foreach ($all_genres as $genre): ?>
                                <label class="genre-item genre-checkbox-card">
                                    <input type="checkbox" name="genres[]" value="<?= (int) $genre['id'] ?>"
                                        style="accent-color: #4f46e5; cursor: pointer;" />
                                    <span style="font-size: 0.875rem; font-weight: 500; color: #374151; user-select: none;"
                                        class="genre-name"><?= htmlspecialchars($genre['genre_name']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p
                            style="font-size: 0.75rem; color: #9ca3af; font-style: italic; text-align: center; padding: 1rem 0;">
                            Chưa có thể loại nào trên hệ thống.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('thumbnail');
    const uploadPrompt = document.getElementById('uploadPrompt');
    const previewContainer = document.getElementById('previewContainer');
    const imagePreview = document.getElementById('imagePreview');
    const removeImage = document.getElementById('removeImage');

    function handleFile(file) {
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                imagePreview.src = e.target.result;
                uploadPrompt.classList.add('hidden');
                previewContainer.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    }

    if (dropZone) {
        dropZone.addEventListener('click', (e) => {
            if (!removeImage.contains(e.target)) {
                fileInput.click();
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                handleFile(fileInput.files[0]);
            }
        });

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
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFile(files[0]);
            }
        });

        removeImage.addEventListener('click', (e) => {
            e.stopPropagation();
            fileInput.value = '';
            imagePreview.src = '';
            previewContainer.classList.add('hidden');
            uploadPrompt.classList.remove('hidden');
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        (function () {
            const titleInput = document.getElementById('title');
            const slugInput = document.getElementById('slug');

            if (!titleInput || !slugInput) return;

            let slugTouched = slugInput.value.trim() !== '';

            function toSlug(value) {
                return value
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/[đĐ]/g, 'd')
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/(^-|-$)/g, '');
            }

            slugInput.addEventListener('input', function () {
                slugTouched = slugInput.value.trim() !== '';
            });

            titleInput.addEventListener('input', function () {
                if (!slugTouched) {
                    slugInput.value = toSlug(titleInput.value || '');
                }
            });
        })();

        (function () {
            const searchInput = document.getElementById('searchGenre');
            const genreItems = document.querySelectorAll('.genre-item');

            if (!searchInput || genreItems.length === 0) return;

            searchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                    .trim();

                genreItems.forEach(item => {
                    const genreName = item.querySelector('.genre-name').textContent
                        .toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
                    if (genreName.includes(query)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        })();

        (function () {
            const uncheckBtn = document.getElementById('uncheckAllGenres');
            if (!uncheckBtn) return;

            uncheckBtn.addEventListener('click', function () {
                const checkboxes = document.querySelectorAll('input[name="genres[]"]');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
            });
        })();
    });
</script>

<?php include_once __DIR__ . '/../Layouts/footer.php'; ?>