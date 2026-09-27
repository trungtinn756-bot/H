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

$filterKeyword = isset($keyword) ? (string) $keyword : '';
$filterStatus = isset($status) ? (string) $status : 'all';
$filterSort = isset($sort) ? (string) $sort : 'update_desc';
$filterChapterCount = isset($chapter_count) ? (string) $chapter_count : 'all';
$selectedGenres = isset($selected_genres) && is_array($selected_genres) ? $selected_genres : [];
$availableGenres = isset($allGenres) && is_array($allGenres) ? $allGenres : [];

$filterBaseParams = [
    'action' => 'filter',
    'keyword' => $filterKeyword,
    'status' => $filterStatus,
    'sort' => $filterSort,
    'chapter_count' => $filterChapterCount,
];

if (!empty($selectedGenres)) {
    $filterBaseParams['genres'] = $selectedGenres;
}

$buildFilterUrl = function (int $pageNumber) use ($filterBaseParams): string {
    $params = $filterBaseParams;
    $params['page'] = max(1, $pageNumber);
    return 'index.php?' . http_build_query($params);
};
?>
<?php include_once __DIR__ . '/../client/Layouts/header.php'; ?>

<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <nav class="breadcrumb-nav">
        <a href="<?php echo $projectRoot; ?>/" class="breadcrumb-link"><i class="fa-solid fa-house"
                style="margin-right: 0.25rem;"></i>Trang Chủ</a>
        <span>/</span>
        <span class="breadcrumb-current">Tìm kiếm nâng cao</span>
    </nav>

    <div
        style="display: flex; justify-content: space-between; align-items: center; background-color: #111827; border: 1px solid rgba(31, 41, 55, 0.6); padding: 1rem; border-radius: 0.75rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="width: 0.5rem; height: 0.5rem; border-radius: 9999px; background-color: #22d3ee;"></div>
            <span style="font-size: 0.875rem; font-weight: 500; color: #d1d5db;">Bạn muốn tìm truyện theo tiêu chí
                riêng?</span>
        </div>
        <button type="button" id="toggle-filter-btn" class="btn-read-latest"
            style="font-size: 0.875rem; padding: 0.5rem 1rem; cursor: pointer;">
            <i class="fa-solid fa-sliders" id="filter-icon" style="transition: transform 0.3s;"></i>
            <span id="filter-btn-text">Hiện bảng bộ lọc</span>
        </button>
    </div>

    <div id="filter-search-box" class="filter-search-box hidden">
        <h1 class="section-title">Tìm kiếm nâng cao</h1>

        <form action="index.php" method="GET" style="display: flex; flex-direction: column; gap: 1.5rem;">
            <input type="hidden" name="action" value="filter" />
            <div>
                <div class="search-form" style="max-width: 28rem;">
                    <input type="text" name="keyword" placeholder="Nhập tên truyện, tác giả..."
                        value="<?php echo htmlspecialchars($filterKeyword, ENT_QUOTES, 'UTF-8'); ?>"
                        class="search-input" />
                    <i class="fa-solid fa-magnifying-glass search-btn"></i>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div>
                    <div
                        style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <label class="form-label" style="margin: 0;">Thể loại</label>
                        <button type="button" id="clear-genres-btn"
                            style="font-size: 0.75rem; color: #22d3ee; background: none; border: none; cursor: pointer;">Bỏ
                            chọn tất cả</button>
                    </div>
                    <div class="checkbox-genre-grid">
                        <?php if (!empty($availableGenres)): ?>
                        <?php foreach ($availableGenres as $genre): ?>
                        <?php
                                $genreId = (int) ($genre['id'] ?? 0);
                                if ($genreId <= 0) continue;
                                $isGenreChecked = in_array($genreId, $selectedGenres, true);
                                ?>
                        <label class="checkbox-label-item">
                            <input type="checkbox" name="genres[]" value="<?php echo $genreId; ?>"
                                <?php echo $isGenreChecked ? 'checked' : ''; ?> class="genre-filter-checkbox"
                                style="accent-color: #06b6d4;">
                            <span
                                style="font-size: 0.875rem; color: <?php echo $isGenreChecked ? '#e5e7eb' : '#9ca3af'; ?>;"><?php echo htmlspecialchars((string) ($genre['genre_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                        </label>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <p style="font-size: 0.875rem; color: #6b7280; grid-column: 1 / -1;">Chưa có dữ liệu thể loại.
                        </p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-grid-2col" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
                    <div>
                        <label class="form-label">Tình trạng</label>
                        <div class="chapter-select-wrapper">
                            <select name="status" class="chapter-select">
                                <option value="all" <?php echo $filterStatus === 'all' ? 'selected' : ''; ?>>Tất cả
                                </option>
                                <option value="ongoing" <?php echo $filterStatus === 'ongoing' ? 'selected' : ''; ?>>
                                    Đang tiến hành</option>
                                <option value="pause" <?php echo $filterStatus === 'pause' ? 'selected' : ''; ?>>Tạm
                                    dừng</option>
                                <option value="completed"
                                    <?php echo $filterStatus === 'completed' ? 'selected' : ''; ?>>Đã hoàn thành
                                </option>
                            </select>
                            <i class="fa-solid fa-chevron-down chapter-select-icon"></i>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Sắp xếp theo</label>
                        <div class="chapter-select-wrapper">
                            <select name="sort" class="chapter-select">
                                <option value="update_desc"
                                    <?php echo $filterSort === 'update_desc' ? 'selected' : ''; ?>>Mới cập nhật</option>
                                <option value="new_desc" <?php echo $filterSort === 'new_desc' ? 'selected' : ''; ?>>
                                    Truyện mới nhất</option>
                                <option value="view_desc" <?php echo $filterSort === 'view_desc' ? 'selected' : ''; ?>>
                                    Lượt xem nhiều nhất</option>
                                <option value="name_asc" <?php echo $filterSort === 'name_asc' ? 'selected' : ''; ?>>A -
                                    Z</option>
                            </select>
                            <i class="fa-solid fa-chevron-down chapter-select-icon"></i>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Số lượng chương</label>
                        <div class="chapter-select-wrapper">
                            <select name="chapter_count" class="chapter-select">
                                <option value="all" <?php echo $filterChapterCount === 'all' ? 'selected' : ''; ?>>Tất
                                    cả</option>
                                <option value="1" <?php echo $filterChapterCount === '1' ? 'selected' : ''; ?>>1 - 10
                                    chương</option>
                                <option value="2" <?php echo $filterChapterCount === '2' ? 'selected' : ''; ?>>11 - 50
                                    chương</option>
                                <option value="3" <?php echo $filterChapterCount === '3' ? 'selected' : ''; ?>>51 - 100
                                    chương</option>
                                <option value="4" <?php echo $filterChapterCount === '4' ? 'selected' : ''; ?>>Trên 100
                                    chương</option>
                            </select>
                            <i class="fa-solid fa-chevron-down chapter-select-icon"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div
                style="display: flex; justify-content: center; padding-top: 1rem; border-top: 1px solid rgba(31, 41, 55, 0.6);">
                <button type="submit" class="btn-save-submit"
                    style="display: flex; align-items: center; gap: 0.5rem; padding: 0.625rem 2rem;">
                    <i class="fa-solid fa-filter"></i> Lọc kết quả
                </button>
            </div>
        </form>
    </div>

    <section style="display: flex; flex-direction: column; gap: 1.5rem; padding-top: 1rem;">
        <div class="section-title-wrapper">
            <h2 class="section-title">Kết quả tìm kiếm</h2>
            <span
                style="font-size: 0.875rem; color: #22d3ee; background-color: rgba(6, 182, 212, 0.1); padding: 0.25rem 0.75rem; border-radius: 9999px;">
                Tìm thấy <?php echo (int) ($totalComics ?? 0); ?> truyện
            </span>
        </div>

        <?php if (!empty($comics)): ?>
        <div class="comic-grid">
            <?php foreach ($comics as $comic): ?>
            <div class="comic-card">
                <a href="index.php?action=detail&id=<?php echo $comic['id']; ?>" class="comic-poster-link">
                    <?php
                            $comicImg = empty($comic['thumbnail'])
                                ? $projectRoot . '/uploads/comics/default-comic.png'
                                : $projectRoot . '/uploads/comics/' . htmlspecialchars($comic['thumbnail'], ENT_QUOTES, 'UTF-8');
                            ?>
                    <img src="<?php echo $comicImg; ?>" alt="Cover" class="comic-poster-img"
                        onerror="this.onerror=null; this.src='https://via.placeholder.com/300x400';" />

                    <?php if (isset($comic['latest_chapter']) && $comic['latest_chapter'] !== null): ?>
                    <span class="badge-chapter">Ch. <?php echo $comic['latest_chapter']; ?></span>
                    <?php endif; ?>
                </a>
                <div class="comic-card-body">
                    <a href="index.php?action=detail&id=<?php echo $comic['id']; ?>" class="comic-card-title">
                        <?php echo htmlspecialchars($comic['title'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                    <div class="comic-card-footer" style="justify-content: space-between; margin-top: auto;">
                        <span><i class="fa-regular fa-eye"></i>
                            <?php echo number_format((float) ($comic['total_views'] ?? 0)); ?></span>
                        <span><i class="fa-regular fa-clock"></i>
                            <?php
                                    $comicStatus = (string) ($comic['status'] ?? '');
                                    echo $comicStatus === '1' ? 'Đã HT' : ($comicStatus === '2' ? 'Tạm ngưng' : 'Đang ra');
                                    ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="pagination-wrapper">
            <?php if ($page > 1): ?>
            <a href="<?php echo htmlspecialchars($buildFilterUrl($page - 1), ENT_QUOTES, 'UTF-8'); ?>"
                class="page-btn"><i class="fa-solid fa-angle-left"></i></a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php if ($i === $page): ?>
            <span class="page-btn active"><?php echo $i; ?></span>
            <?php else: ?>
            <a href="<?php echo htmlspecialchars($buildFilterUrl($i), ENT_QUOTES, 'UTF-8'); ?>"
                class="page-btn"><?php echo $i; ?></a>
            <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
            <a href="<?php echo htmlspecialchars($buildFilterUrl($page + 1), ENT_QUOTES, 'UTF-8'); ?>"
                class="page-btn"><i class="fa-solid fa-angle-right"></i></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <div
            style="text-align: center; padding: 3rem 0; background-color: #111827; border: 1px solid rgba(31, 41, 55, 0.6); border-radius: 0.75rem;">
            <i class="fa-solid fa-box-open"
                style="font-size: 2.25rem; color: #4b5563; margin-bottom: 0.75rem; display: block;"></i>
            <p style="color: #9ca3af; font-size: 0.875rem;">Không tìm thấy kết quả phù hợp với bộ lọc nâng cao.</p>
        </div>
        <?php endif; ?>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleFilterBtn = document.getElementById('toggle-filter-btn');
    const filterSearchBox = document.getElementById('filter-search-box');
    const filterIcon = document.getElementById('filter-icon');
    const filterBtnText = document.getElementById('filter-btn-text');

    if (toggleFilterBtn && filterSearchBox) {
        const urlParams = new URLSearchParams(window.location.search);

        let hasGenreActive = false;
        for (let key of urlParams.keys()) {
            if (key.startsWith('genres')) {
                hasGenreActive = true;
                break;
            }
        }

        const hasFilterActive =
            (urlParams.get('keyword') && urlParams.get('keyword').trim() !== '') ||
            hasGenreActive ||
            (urlParams.get('status') && urlParams.get('status') !== 'all') ||
            (urlParams.get('chapter_count') && urlParams.get('chapter_count') !== 'all') ||
            (urlParams.get('sort') && urlParams.get('sort') !== 'update_desc');

        if (hasFilterActive) {
            filterSearchBox.classList.remove('hidden');
            if (filterIcon) filterIcon.classList.add('rotate-180');
            if (filterBtnText) filterBtnText.textContent = 'Ẩn bảng bộ lọc';
        }

        toggleFilterBtn.addEventListener('click', function() {
            const isHidden = filterSearchBox.classList.contains('hidden');
            if (isHidden) {
                filterSearchBox.classList.remove('hidden');
                if (filterIcon) filterIcon.classList.add('rotate-180');
                if (filterBtnText) filterBtnText.textContent = 'Ẩn bảng bộ lọc';
            } else {
                filterSearchBox.classList.add('hidden');
                if (filterIcon) filterIcon.classList.remove('rotate-180');
                if (filterBtnText) filterBtnText.textContent = 'Hiện bảng bộ lọc';
            }
        });
    }

    const clearGenresBtn = document.getElementById('clear-genres-btn');
    if (clearGenresBtn) {
        clearGenresBtn.addEventListener('click', function() {
            document.querySelectorAll('.genre-filter-checkbox').forEach(function(checkbox) {
                checkbox.checked = false;
            });
        });
    }
});
</script>

<?php include_once __DIR__ . '/../client/Layouts/footer.php'; ?>