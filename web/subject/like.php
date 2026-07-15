<?php
// 统一字符编码
mb_internal_encoding('UTF-8');
// 防止SESSION重复启动
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// 引入SESSION
$sessionNoticeFile = $_SERVER['DOCUMENT_ROOT'] . '/api/sessionnotice.php';
if (file_exists($sessionNoticeFile)) {
    include $sessionNoticeFile;
} else {
    exit('500');
}
// 统一错误输出函数
function outputError($code) {
    header('Content-Type: text/plain; charset=UTF-8');
    echo (string)$code;
    exit;
}
// 字符串处理函数
function stripMagicQuotes($data) {
    if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() && is_string($data)) {
        return stripslashes($data);
    }
    return $data;
}
// 正整数校验函数
function isPositiveInteger($str) {
    if (!is_string($str) || trim($str) === '') {
        return false;
    }
    if (!ctype_digit($str)) {
        return false;
    }
    $num = (int)$str;
    return $num > 0;
}
// ========== 基础校验 ==========
if (empty($ppzusername) || $_SERVER['REQUEST_METHOD'] !== 'POST' || !is_string($ppzusername)) {
    outputError(500);
}
// ========== 数据库连接 ==========
$connFile = $_SERVER['DOCUMENT_ROOT'] . '/inc/conn.php';
if (!file_exists($connFile)) {
    outputError(500);
}
include $connFile;
// 校验数据库连接是否成功
if (!$conn || (isset($conn->connect_errno) && $conn->connect_errno > 0)) {
    outputError(500);
}
// ========== 查询用户信息 ==========
$user_sql = "SELECT * FROM ppz_newusername WHERE BINARY uusername = ?";
$user_stmt = $conn->prepare($user_sql);
if (!$user_stmt) {
    outputError(500);
}
// 绑定参数
$user_stmt->bind_param("s", $ppzusername);
$user_stmt->execute();
$retval = $user_stmt->get_result();
// 校验用户是否存在
if (mysqli_num_rows($retval) !== 1) {
    $user_stmt->close();
    $retval->free();
    outputError(500);
}
// 获取用户信息
$row = $retval->fetch_array(MYSQLI_ASSOC);
$vip = (int)$row['ustatus']; // 1普通会员，2管理员，3副站长，4站长
$login_uid = (int)$row['uid']; // 登录用户ID
$uban = (int)$row['uban']; // 1为正常
// 释放资源
$user_stmt->close();
$retval->free();
// ========== 权限校验 ==========
define('USER_STATUS_NORMAL', 1);
$allowed_vip_levels = array(1, 2, 3, 4);
if (!in_array($vip, $allowed_vip_levels, true) || $uban !== USER_STATUS_NORMAL) {
    outputError(500);
}
// ========== 评论ID校验 ==========
$id = isset($_POST['id']) ? stripMagicQuotes(trim($_POST['id'])) : '';
if (empty($id) || !isPositiveInteger($id)) {
    outputError(500);
}
$id = (int)$id;
// ========== 查询评论信息==========
$set_sql = "SELECT comm_id,comm_top FROM ppz_subcomm WHERE BINARY comm_id = ?";
$set_stmt = $conn->prepare($set_sql);
if (!$set_stmt) {
    outputError(500);
}
$set_stmt->bind_param("i", $id);
$set_stmt->execute();
$set_retval = $set_stmt->get_result();
if (mysqli_num_rows($set_retval) !== 1) {
    $set_stmt->close();
    $set_retval->free();
    outputError(500);
}
// 获取评论的点赞用户ID数据
$comment_row = $set_retval->fetch_array(MYSQLI_ASSOC);
$set_stmt->close();
$set_retval->free();

// ========== 点赞逻辑处理 ==========
// 1. 处理当前点赞用户ID数组
$current_likes = $comment_row['comm_top'];
// 将字符串转为数组（处理空值、单个值、多个值情况）
$like_ids = !empty($current_likes) ? explode(',', $current_likes) : array();
// 确保数组元素都是整数，避免格式问题
$like_ids = array_map('intval', $like_ids);

// 2. 判断当前用户是否已点赞
$is_liked = in_array($login_uid, $like_ids, true);
$new_like_str = '';
if ($is_liked) {
    // 已点赞：移除当前用户ID
    $like_ids = array_filter($like_ids, function($uid) use ($login_uid) {
        return $uid !== $login_uid;
    });
    // 重新构建点赞字符串（保持英文逗号分隔）
    $new_like_str = implode(',', $like_ids);
    $response_code = 202; // 取消点赞返回202
} else {
    // 未点赞：添加当前用户ID
    $like_ids[] = $login_uid;
    // 去重（防止重复添加）
    $like_ids = array_unique($like_ids);
    // 重新构建点赞字符串
    $new_like_str = implode(',', $like_ids);
    $response_code = 200; // 点赞成功返回200
}

// 3. 更新数据库中的点赞数据
$update_sql = "UPDATE ppz_subcomm SET comm_top = ? WHERE comm_id = ?";
$update_stmt = $conn->prepare($update_sql);
if (!$update_stmt) {
    outputError(500);
}
// 绑定参数并执行更新
$update_stmt->bind_param("si", $new_like_str, $id);
if (!$update_stmt->execute()) {
    $update_stmt->close();
    outputError(500);
}
// 释放更新语句资源
$update_stmt->close();
// 4. 返回最终状态码
header('Content-Type: text/plain; charset=UTF-8');
echo (string)$response_code;
exit;
?>