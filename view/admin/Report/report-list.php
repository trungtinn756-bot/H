<?php include_once __DIR__ . '/../Layouts/header.php'; ?>

<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- HEADER TRANG VÀ BỘ LỌC TRẠNG THÁI -->
    <div
        style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; border-bottom: 1px solid #e5e7eb; padding-bottom: 1rem; gap: 1rem;">
        <div>
            <h1 class="admin-page-title"
                style="margin: 0; font-size: 1.25rem; font-weight: 700; color: #1f2937; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-triangle-exclamation" style="color: #ef4444;"></i> Quản Lý Báo Lỗi Chương Truyện
            </h1>
            <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">Nơi xử lý các phản hồi lỗi từ độc giả
                gửi về hệ thống.</p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.5rem; user-select: none;">
            <a href="index.php?action=admin-report-list&status=all"
                class="btn-sort-item <?php echo $status === 'all' ? 'active' : ''; ?>">Tất cả</a>
            <a href="index.php?action=admin-report-list&status=0"
                class="btn-sort-item <?php echo $status === '0' ? 'active' : ''; ?>"
                style="<?php echo $status === '0' ? 'background-color: #f59e0b; border-color: #f59e0b;' : ''; ?>">Chưa
                xử lý</a>
            <a href="index.php?action=admin-report-list&status=1"
                class="btn-sort-item <?php echo $status === '1' ? 'active' : ''; ?>"
                style="<?php echo $status === '1' ? 'background-color: #059669; border-color: #059669;' : ''; ?>">Đã
                sửa</a>
        </div>
    </div>

    <!-- KHỐI BẢNG BÁO LỖI -->
    <div class="admin-card-section" style="box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);">
        <div class="admin-table-container">
            <table class="admin-data-table">
                <thead>
                    <tr
                        style="background-color: #f9fafb; color: #374151; font-weight: 700; border-bottom: 1px solid #e5e7eb;">
                        <th style="width: 4rem; text-align: center;">STT</th>
                        <th style="width: 13rem;">Truyện / Chương</th>
                        <th>Nội dung báo lỗi</th>
                        <th style="width: 10rem;">Người báo</th>
                        <th style="width: 8rem; text-align: center;">Trạng thái</th>
                        <th style="width: 10rem; text-align: center;">Hành động</th>
                    </tr>
                </thead>
                <tbody style="color: #4b5563;">
                    <?php if (!empty($reports)): ?>
                        <?php foreach ($reports as $index => $rep): ?>
                            <tr class="admin-table-row">
                                <td style="text-align: center; color: #9ca3af;"><?php echo $offset + $index + 1; ?></td>
                                <td>
                                    <div style="font-weight: 700; color: #1f2937; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;"
                                        title="<?php echo htmlspecialchars($rep['comic_title']); ?>">
                                        <?php echo htmlspecialchars($rep['comic_title']); ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: #4f46e5; font-weight: 600; margin-top: 0.125rem;">
                                        Chương <?php echo $rep['chapter_number']; ?>
                                    </div>
                                </td>
                                <td>
                                    <p
                                        style="color: #374151; background-color: #f9fafb; border: 1px solid #f3f4f6; border-radius: 0.5rem; padding: 0.625rem; white-space: pre-line; line-height: 1.5; font-size: 0.75rem; margin: 0;">
                                        <?php echo htmlspecialchars($rep['content']); ?>
                                    </p>
                                    <span style="font-size: 10px; color: #9ca3af; display: block; margin-top: 0.25rem;">
                                        Gửi lúc: <?php echo date('d/m/Y H:i', strtotime($rep['created_at'])); ?>
                                    </span>
                                </td>
                                <td style="font-size: 0.75rem;">
                                    <span style="font-weight: 500; color: #374151;">
                                        <?php echo $rep['display_name'] ? htmlspecialchars($rep['display_name']) : 'Khách vãng lai'; ?>
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ((int) $rep['status'] === 1): ?>
                                        <span class="badge-status-pill active">
                                            Đã sửa xong
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-status-pill inactive"
                                            style="background-color: #fffbeb; color: #b45309; border-color: #fde68a;">
                                            Đang chờ
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: flex; justify-content: center; align-items: center; gap: 0.5rem;">
                                        <a href="index.php?action=reader&id=<?php echo $rep['chapter_id']; ?>" target="_blank"
                                            class="btn-action-icon"
                                            style="background-color: #ffffff; border: 1px solid #e5e7eb; color: #6b7280;"
                                            title="Xem chương bị lỗi">
                                            <i class="fa-solid fa-eye" style="font-size: 0.75rem;"></i>
                                        </a>

                                        <?php if ((int) $rep['status'] === 0): ?>
                                            <a href="index.php?action=admin-resolve-report&id=<?php echo $rep['id']; ?>&status_filter=<?php echo $status; ?>&page=<?php echo $page; ?>"
                                                onclick="return confirm('Xác nhận bạn đã sửa xong chương này?')"
                                                class="btn-action-icon green" style="border: 1px solid #a7f3d0;"
                                                title="Đánh dấu đã sửa">
                                                <i class="fa-solid fa-check" style="font-size: 0.75rem;"></i>
                                            </a>
                                        <?php endif; ?>

                                        <a href="index.php?action=admin-delete-report&id=<?php echo $rep['id']; ?>&status_filter=<?php echo $status; ?>&page=<?php echo $page; ?>"
                                            onclick="return confirm('Bạn chắc chắn muốn xóa bản báo cáo lỗi này?')"
                                            class="btn-action-icon red" style="border: 1px solid #fecaca;" title="Xóa báo cáo">
                                            <i class="fa-solid fa-trash-can" style="font-size: 0.75rem;"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6"
                                style="padding: 2rem 0; text-align: center; color: #9ca3af; font-style: italic;">
                                Không tìm thấy báo cáo lỗi chương truyện nào.
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
                <a href="index.php?action=admin-report-list&status=<?php echo $status; ?>&page=<?php echo $i; ?>"
                    class="page-link-admin <?php echo $page == $i ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../Layouts/footer.php'; ?>