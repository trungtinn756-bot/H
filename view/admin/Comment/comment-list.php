<?php include_once __DIR__ . '/../Layouts/header.php'; ?>

<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- HEADER TRANG VÀ FORM TÌM KIẾM BÌNH LUẬN -->
    <div
        style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; border-bottom: 1px solid #e5e7eb; padding-bottom: 1rem; gap: 1rem;">
        <div>
            <h1 class="admin-page-title"
                style="margin: 0; font-size: 1.25rem; font-weight: 700; color: #1f2937; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-regular fa-comments" style="color: #4f46e5;"></i> Quản Lý Bình Luận Hệ Thống
            </h1>
            <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">Kiểm duyệt và xóa bỏ các bình luận thô
                tục, tiêu cực, mang tính chất phá hoại.</p>
        </div>

        <form action="index.php" method="GET" style="width: 20rem; position: relative; user-select: none;">
            <input type="hidden" name="action" value="admin-comment-list">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                placeholder="Tìm từ khóa thô tục, tiêu cực..." class="form-control-std"
                style="padding-right: 2.5rem; background-color: #ffffff;" />
            <button type="submit"
                style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); border: none; background: none; color: #9ca3af; cursor: pointer;">
                <i class="fa-solid fa-magnifying-glass" style="font-size: 0.75rem;"></i>
            </button>
        </form>
    </div>

    <!-- KHỐI BẢNG DANH SÁCH BÌNH LUẬN -->
    <div class="admin-card-section" style="box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);">
        <div class="admin-table-container">
            <table class="admin-data-table">
                <thead>
                    <tr
                        style="background-color: #f9fafb; color: #374151; font-weight: 700; border-bottom: 1px solid #e5e7eb;">
                        <th style="width: 4rem; text-align: center;">STT</th>
                        <th style="width: 11rem;">Thành viên</th>
                        <th style="width: 13rem;">Vị trí truyện</th>
                        <th>Nội dung bình luận</th>
                        <th style="width: 8rem; text-align: center;">Hành động</th>
                    </tr>
                </thead>
                <tbody style="color: #4b5563;">
                    <?php if (!empty($comments)): ?>
                        <?php foreach ($comments as $index => $cmt): ?>
                            <tr class="admin-table-row">
                                <td style="text-align: center; color: #9ca3af;"><?php echo $offset + $index + 1; ?></td>
                                <td>
                                    <div style="font-weight: 700; color: #1f2937;">
                                        <?php echo htmlspecialchars($cmt['display_name']); ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.125rem;">
                                        @<?php echo htmlspecialchars($cmt['username']); ?></div>
                                </td>
                                <td>
                                    <div style="font-weight: 500; color: #374151; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;"
                                        title="<?php echo htmlspecialchars($cmt['comic_title']); ?>">
                                        <?php echo htmlspecialchars($cmt['comic_title']); ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: #4f46e5; margin-top: 0.125rem; font-weight: 600;">
                                        <?php echo $cmt['chapter_number'] ? 'Chương ' . $cmt['chapter_number'] : 'Trang giới thiệu'; ?>
                                    </div>
                                </td>
                                <td>
                                    <p
                                        style="color: #374151; background-color: #f9fafb; border: 1px solid #f3f4f6; border-radius: 0.5rem; padding: 0.75rem; font-size: 0.75rem; line-height: 1.5; max-width: 40rem; white-space: pre-line; margin: 0;">
                                        <?php echo htmlspecialchars($cmt['content']); ?>
                                    </p>
                                    <span
                                        style="font-size: 10px; color: #9ca3af; display: block; margin-top: 0.25rem; margin-left: 0.25rem;">
                                        Đăng lúc: <?php echo date('d/m/Y H:i', strtotime($cmt['created_at'])); ?>
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <a href="index.php?action=admin-delete-comment&id=<?php echo $cmt['id']; ?>&search_filter=<?php echo urlencode($search); ?>&page=<?php echo $page; ?>"
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn bình luận tiêu cực này không?')"
                                        class="btn-action-icon red"
                                        style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.375rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; text-decoration: none; border: 1px solid #fecaca;"
                                        title="Xóa bình luận">
                                        <i class="fa-solid fa-trash-can" style="font-size: 11px;"></i> Xóa bỏ
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5"
                                style="padding: 2rem 0; text-align: center; color: #9ca3af; font-style: italic;">
                                Không tìm thấy bình luận nào thỏa mãn điều kiện.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- PHÂN TRANG -->
    <?php if ($totalPages > 1): ?>
        <div class="admin-pagination" style="justify-content: center;">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="index.php?action=admin-comment-list&search=<?php echo urlencode($search); ?>&page=<?php echo $i; ?>"
                    class="page-link-admin <?php echo $page == $i ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../Layouts/footer.php'; ?>