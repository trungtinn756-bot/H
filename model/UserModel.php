<?php
require_once __DIR__ . '/../libs/pdo.php';

function user_register($username, $email, $password)
{
    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    return pdo_execute($sql, $username, $email, $hashed_password);
}

function user_get_by_username($username)
{
    $sql = "SELECT * FROM users WHERE username = ? LIMIT 1";
    return pdo_getOne($sql, $username);
}
function user_get_by_email($email)
{
    $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
    return pdo_getOne($sql, $email);
}
function user_get_by_id($user_id)
{
    $sql = "SELECT * FROM users WHERE id = ? LIMIT 1";
    return pdo_getOne($sql, $user_id);
}
function user_get_by_username_or_email($identifier)
{
    $sql = "SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1";
    return pdo_getOne($sql, $identifier, $identifier);
}
// // Cập nhật token quên mật khẩu và thời gian hết hạn (15 phút sau)
// function user_update_reset_token($email, $token)
// {
//     $expires_at = date("Y-m-d H:i:s", strtotime("+15 minutes"));
//     $sql = "UPDATE users SET reset_token = ?, reset_expires_at = ? WHERE email = ?";
//     return pdo_execute($sql, $token, $expires_at, $email);
// }

// // Tìm user dựa vào token hợp lệ và chưa hết hạn
// function user_get_by_reset_token($token)
// {
//     $current_time = date("Y-m-d H:i:s");
//     $sql = "SELECT * FROM users WHERE reset_token = ? AND reset_expires_at > ? LIMIT 1";
//     return pdo_getOne($sql, $token, $current_time);
// }

// // Cập nhật mật khẩu mới và xóa token đi
// function user_reset_password($user_id, $new_password)
// {
//     $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
//     $sql = "UPDATE users SET password = ?, reset_token = NULL, reset_expires_at = NULL WHERE id = ?";
//     return pdo_execute($sql, $hashed_password, $user_id);
// }
function user_update_profile($user_id, $display_name, $email)
{
    $sql = "UPDATE users SET display_name = ?, email = ? WHERE id = ?";
    return pdo_execute($sql, $display_name, $email, $user_id);
}
function user_update_avatar($user_id, $avatar_path)
{
    $sql = "UPDATE users SET avatar = ? WHERE id = ?";
    return pdo_execute($sql, $avatar_path, $user_id);
}
function user_update_password($user_id, $new_password)
{
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET password = ? WHERE id = ?";
    return pdo_execute($sql, $hashed_password, $user_id);
}
function user_update_status($user_id, $status)
{
    $sql = "UPDATE users SET status = ? WHERE id = ?";
    return pdo_execute($sql, $status, $user_id);
}
function user_get_all($keyword, $role_ID, $status, $orderBy)
{
    $sql = "SELECT * FROM users Where 1=1";
    $params = [];

    if (!empty($keyword)) {
        $sql .= " AND (username LIKE ? OR email LIKE ? OR display_name LIKE ?)";
        $searchTerm = '%' . $keyword . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    if ($role_ID === '0' || $role_ID === '1') {
        $sql .= " AND role = ?";
        $params[] = (int) $role_ID;
    }

    if ($status === '0' || $status === '1') {
        $sql .= " AND status = ?";
        $params[] = (int) $status;
    }

    $orderBy = !empty($orderBy) ? $orderBy : "id DESC";
    $sql .= " ORDER BY " . $orderBy;

    return pdo_getAll($sql, ...$params);
}
function user_count($keyword, $role_ID, $status)
{
    $sql = "SELECT COUNT(*) FROM users WHERE 1=1";
    $params = [];

    if (!empty($keyword)) {
        $sql .= " AND (username LIKE ? OR email LIKE ? OR display_name LIKE ?)";
        $searchTerm = '%' . $keyword . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    if ($role_ID === '0' || $role_ID === '1') {
        $sql .= " AND role = ?";
        $params[] = (int) $role_ID;
    }

    if ($status === '0' || $status === '1') {
        $sql .= " AND status = ?";
        $params[] = (int) $status;
    }

    return pdo_getValue($sql, ...$params);
}