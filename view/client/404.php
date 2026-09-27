<?php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang không tìm thấy - 404 Not Found</title>
    <link rel="stylesheet" href="/public/css/styles.css" />
</head>

<body>

    <div class="page-404-container">
        <h1 class="page-404-title">404</h1>
        <h2 style="font-size: 1.5rem; margin-bottom: 1.25rem; color: #e5e7eb;">Úp! Trang bạn tìm kiếm không tồn tại.
        </h2>
        <p style="font-size: 1rem; color: #9ca3af; line-height: 1.6; margin-bottom: 2rem;">
            Có vẻ như liên kết đã bị hỏng, trang web đã bị xóa hoặc bạn đã nhập sai địa chỉ URL.
            Hãy thử quay lại trang chủ hoặc tìm kiếm nội dung khác bên dưới.
        </p>

        <a href="/" class="btn-404-home">Quay lại Trang Chủ</a>

        <div class="search-box-404">
            <form action="index.php" method="GET" style="width: 100%; display: flex; justify-content: center;">
                <input type="hidden" name="action" value="search" />
                <input type="text" name="q" placeholder="Nhập từ khóa tìm kiếm..." required>
                <button type="submit">Tìm</button>
            </form>
        </div>
    </div>

</body>

</html>