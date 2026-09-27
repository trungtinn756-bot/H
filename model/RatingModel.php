<?php
require_once __DIR__ . '/../libs/pdo.php';

// Thực hiện lưu hoặc cập nhật số sao đánh giá
function rating_submit($user_id, $comic_id, $rating_value)
{
    // Sử dụng tính năng ON DUPLICATE KEY UPDATE của MySQL vì cặp khóa primary là (user_id, comic_id)
    $sql = "INSERT INTO ratings (user_id, comic_id, rating_value) VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE rating_value = ?";
    return pdo_execute($sql, (int) $user_id, (int) $comic_id, (int) $rating_value, (int) $rating_value);
}

// Lấy thông tin thống kê đánh giá trung bình của truyện
function rating_get_average($comic_id)
{
    $sql = "SELECT COUNT(*) as total_votes, IFNULL(AVG(rating_value), 0) as avg_rating 
            FROM ratings WHERE comic_id = ?";
    return pdo_getOne($sql, (int) $comic_id);
}