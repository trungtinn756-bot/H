<?php
include_once __DIR__ . '/../Layouts/header.php';

$currentPage = $_REQUEST['page'] ?? 1;
$search = $_REQUEST['search'] ?? '';
$statusFilter = $_REQUEST['status_filter'] ?? $_REQUEST['status'] ?? '';
$sort = $_REQUEST['sort'] ?? '';

$queryString = "&page={$currentPage}&search=" . urlencode($search) . "&status=" . urlencode($statusFilter) . "&sort=" . urlencode($sort);
?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Chỉnh sửa: <?php echo htmlspecialchars($comic['title']); ?></h1>
    <a href="index.php?action=admin-comic-list<?php echo $queryString; ?>" class="btn-form-cancel"
        style="background-color: #4b5563; color: #ffffff; text-decoration: none; display: inline-flex; align-items: center;">
        <i class="fa-solid fa-arrow-left" style="margin-right: 0.375rem;"></i> Quay lại danh sách
    </a>
</div>

<?php if (!empty($error)): ?>
    <div
        style="margin-bottom: 1rem; padding: 1rem; background-color: #fef2f2; border-left: 4px solid #ef4444; color: #b91c1c; border-radius: 0.375rem;">
        <i class="fa-solid fa-triangle-exclamation" style="margin-right: 0.5rem;"></i> <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<form action="index.php?action=admin-edit-comic&id=<?= $comic['id'] ?>" method="post" enctype="multipart/form-data"
    id="comicForm" class="admin-form-grid">
    <input type="hidden" name="page" value="<?= $currentPage ?>">
    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
    <input type="hidden" name="status_filter" value="<?= htmlspecialchars($statusFilter) ?>">
    <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
    <input type="hidden" name="thumbnail_existing" value="<?= htmlspecialchars($comic['thumbnail']) ?>">

    <div class="admin-form-main-col" style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="admin-card-section" style="padding: 1.5rem;">
            <h3 class="form-card-title">Thông tin truyện</h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div>
                    <label for="title" class="form-label" style="margin-bottom: 0.25rem;">Tên truyện <span
                            style="color: #ef4444;">*</span></label>
                    <input type="text" name="title" id="title" required value="<?= htmlspecialchars($comic['title']) ?>"
                        class="form-control-std" />
                </div>
                <div>
                    <label for="other_title" class="form-label" style="margin-bottom: 0.25rem;">Tên khác</label>
                    <input type="text" name="other_title" id="other_title"
                        value="<?= htmlspecialchars($comic['other_title']) ?>" class="form-control-std" />
                </div>
                <div>
                    <label for="slug" class="form-label" style="margin-bottom: 0.25rem;">Slug <span
                            style="color: #ef4444;">*</span></label>
                    <input type="text" name="slug" id="slug" required value="<?= htmlspecialchars($comic['slug']) ?>"
                        class="form-control-std" />
                </div>
                <div>
                    <label for="author" class="form-label" style="margin-bottom: 0.25rem;">Tác giả <span
                            style="color: #ef4444;">*</span></label>
                    <input type="text" name="author" id="author" required
                        value="<?= htmlspecialchars($comic['author']) ?>" class="form-control-std" />
                </div>
                <div>
                    <label for="summary" class="form-label" style="margin-bottom: 0.25rem;">Mô tả nội dung</label>
                    <textarea name="summary" id="summary" rows="6"
                        class="form-control-std"><?= htmlspecialchars($comic['summary']) ?></textarea>
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
        <?php
        $title = "Ảnh bìa hiện tại";
        $current_cover_url = (!empty($projectRoot) ? rtrim($projectRoot, '/') . '/' : '') . 'uploads/comics/' . htmlspecialchars($comic['thumbnail'], ENT_QUOTES, 'UTF-8');
        $alt_text = "Current Cover";
        $button_text = "Đổi ảnh mới";
        $note_text = "Nhấp vào ảnh hoặc nút để thay đổi. Kích thước đề xuất: 600x800px";
        ?>

        <div class="admin-card-section" style="padding: 1.5rem;">
            <h3 class="form-card-title"><?= htmlspecialchars($title) ?></h3>

            <div id="coverWrapper" class="cover-preview-wrapper">
                <img id="coverPreview" src="<?= htmlspecialchars($current_cover_url) ?>"
                    alt="<?= htmlspecialchars($alt_text) ?>" style="width: 100%; height: 100%; object-fit: cover;" />

                <div class="cover-overlay-hover">
                    <button type="button" class="btn-form-cancel"
                        style="background-color: #ffffff; color: #1f2937; display: inline-flex; align-items: center;">
                        <i class="fa-solid fa-cloud-arrow-up" style="margin-right: 0.5rem; color: #4f46e5;"></i>
                        <?= htmlspecialchars($button_text) ?>
                    </button>
                </div>
                <input type="file" name="thumbnail" id="thumbnail" accept=".jpg,.jpeg,.png,.webp,.avif" class="hidden">
            </div>

            <p style="font-size: 0.75rem; text-align: center; color: #6b7280; margin-top: 0.75rem;">
                <?= htmlspecialchars($note_text) ?>
            </p>
        </div>

        <div class="admin-card-section" style="padding: 1.5rem;">
            <h3 class="form-card-title">Phân loại</h3>
            <div style="margin-bottom: 1.25rem;">
                <label for="status" class="form-label" style="margin-bottom: 0.25rem;">Trạng thái</label>
                <div class="chapter-select-wrapper">
                    <select name="status" id="status" class="form-control-std">
                        <option value="0" <?= $comic['status'] == '0' ? 'selected' : '' ?>>Đang tiến hành</option>
                        <option value="1" <?= $comic['status'] == '1' ? 'selected' : '' ?>>Đã hoàn thành</option>
                        <option value="2" <?= $comic['status'] == '2' ? 'selected' : '' ?>>Tạm ngưng</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label"
                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <span>Thể loại</span>
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
                                <?php $isChecked = in_array($genre['id'], $current_genre_ids) ? 'checked' : ''; ?>
                                <label class="genre-item genre-checkbox-card">
                                    <input type="checkbox" name="genres[]" value="<?= (int) $genre['id'] ?>" <?= $isChecked ?>
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
    document.addEventListener("DOMContentLoaded", function () {
        const coverWrapper = document.getElementById('coverWrapper');
        const fileInput = document.getElementById('thumbnail');
        const coverPreview = document.getElementById('coverPreview');

        if (coverWrapper && fileInput && coverPreview) {
            coverWrapper.addEventListener('click', () => {
                fileInput.click();
            });

            fileInput.addEventListener('change', function () {
                const file = this.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        coverPreview.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });
        }

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