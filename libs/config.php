<?php
// Ưu tiên lấy từ biến môi trường (Render), nếu không có thì lấy giá trị mặc định
define('DB_HOST', getenv('DB_HOST') ?: 'gateway01.us-west-2.prod.aws.tidbcloud.com');
define('DB_PORT', getenv('DB_PORT') ?: '4000');
define('DB_USER', getenv('DB_USER') ?: '3ifkmuUQUcceQ4L.root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '5C0XDbWb52A3OzkP');
define('DB_NAME', getenv('DB_NAME') ?: 'comic_online');

date_default_timezone_set('Asia/Ho_Chi_Minh');
?>