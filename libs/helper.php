<?php
function str_slug($text)
{
    $text = trim((string) $text);
    if ($text === '') {
        return '';
    }

    $map = [
        'a' => '/[aAáÁàÀảẢãÃạẠăĂắẮằẰẳẲẵẴặẶâÂấẤầẦẩẨẫẪậẬ]/u',
        'd' => '/[dDđĐ]/u',
        'e' => '/[eEéÉèÈẻẺẽẼẹẸêÊếẾềỀểỂễỄệỆ]/u',
        'i' => '/[iIíÍìÌỉỈĩĨịỊ]/u',
        'o' => '/[oOóÓòÒỏỎõÕọỌôÔốỐồỒổỔỗỖộỘơƠớỚờỜởỞỡỠợỢ]/u',
        'u' => '/[uUúÚùÙủỦũŨụỤưƯứỨừỪửỬữỮựỰ]/u',
        'y' => '/[yYýÝỳỲỷỶỹỸỵỴ]/u',
    ];

    foreach ($map as $replace => $pattern) {
        $text = preg_replace($pattern, $replace, $text);
    }

    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim((string) $text, '-');
}
function comic_upload_dir()
{
    return __DIR__ . '/../uploads/comics/';
}

function comic_normalize_image_name($filename)
{
    $filename = strtolower(trim((string) $filename));
    $filename = preg_replace('/[^a-z0-9]+/', '-', $filename);
    return $filename;
}

function comic_handle_image_upload($fieldName, &$errorMessage = '')
{
    $errorMessage = '';

    if (!isset($_FILES[$fieldName]) || !is_array($_FILES[$fieldName])) {
        return null;
    }

    $file = $_FILES[$fieldName];
    $uploadError = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);

    if ($uploadError === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($uploadError !== UPLOAD_ERR_OK) {
        $errorMessage = 'Tải ảnh lên thất bại. Vui lòng thử lại.';
        return false;
    }

    $tmpName = (string) ($file['tmp_name'] ?? '');
    $originalName = (string) ($file['name'] ?? '');

    if ($tmpName === '' || !is_uploaded_file($tmpName)) {
        $errorMessage = 'Tệp ảnh không hợp lệ.';
        return false;
    }

    // 1. Kiểm tra đuôi mở rộng (Extension) - Đã thêm 'avif'
    $extension = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];

    if (!in_array($extension, $allowedExtensions, true)) {
        $errorMessage = 'Chỉ hỗ trợ ảnh JPG, JPEG, PNG, WEBP, GIF, AVIF.';
        return false;
    }

    // 2. Kiểm tra MIME type thực tế của file để tránh bypass mã độc thông qua đuôi file
    if (function_exists('mime_content_type')) {
        $mimeType = mime_content_type($tmpName);
        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif',
            'image/avif' // MIME type chuẩn của file .avif
        ];

        if (!in_array($mimeType, $allowedMimeTypes, true)) {
            $errorMessage = 'Nội dung tệp tin không phải là ảnh hợp lệ.';
            return false;
        }
    }

    $maxSize = 3 * 1024 * 1024;
    if ((int) ($file['size'] ?? 0) > $maxSize) {
        $errorMessage = 'Kích thước ảnh tối đa là 3MB.';
        return false;
    }

    $dir = comic_upload_dir();
    if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
        $errorMessage = 'Không thể tạo thư mục lưu ảnh.';
        return false;
    }

    $baseName = comic_normalize_image_name((string) pathinfo($originalName, PATHINFO_FILENAME));
    if ($baseName === '') {
        $baseName = 'comic-image';
    }

    try {
        $randomToken = bin2hex(random_bytes(3));
    } catch (Throwable $e) {
        $randomToken = substr(sha1(uniqid((string) mt_rand(), true)), 0, 6);
    }

    $finalName = $baseName . '-' . date('YmdHis') . '-' . $randomToken . '.' . $extension;
    $targetPath = $dir . $finalName;

    if (!move_uploaded_file($tmpName, $targetPath)) {
        $errorMessage = 'Không thể lưu ảnh tải lên.';
        return false;
    }

    return $finalName;
}