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
// 大于或等于0的整数校验函数
function isPositiveInteger($str) {
    if (!is_string($str) || trim($str) === '') {
        return false;
    }
    if (!ctype_digit($str)) {
        return false;
    }
    $num = (int)$str;
    return $num >= 0;
}
// 字符串处理函数
function stripMagicQuotes($data) {
    if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() && is_string($data)) {
        return stripslashes($data);
    }
    return $data;
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
// ========== 登录用户信息 ==========
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
$allowed_vip_levels = array(3,4);
if (!in_array($vip, $allowed_vip_levels, true) || $uban !== USER_STATUS_NORMAL) {
    outputError(500);
}
$off = isset($_POST['off']) ? stripMagicQuotes(trim($_POST['off'])) : '0';// 0关闭，1开启
$title = isset($_POST['title']) ? stripMagicQuotes(trim($_POST['title'])) : '';// 标题
$name = isset($_POST['name']) ? stripMagicQuotes(trim($_POST['name'])) : '';//名称
$mun = isset($_POST['mun']) ? stripMagicQuotes(trim($_POST['mun'])) : 0;
$repmun = isset($_POST['repmun']) ? stripMagicQuotes(trim($_POST['repmun'])) : 2;
$off = (int)$off;
if (!isPositiveInteger($mun)||!isPositiveInteger($repmun)||($off != 0 && $off != 1)) {
    outputError(500);
}
$mun = (int)$mun;
$repmun = (int)$repmun;
$name = htmlspecialchars($name);
$title = htmlspecialchars($title);
$sql = "UPDATE ppz_subset SET set_off = ?,set_title = ?,set_tag = ?,set_mun = ?,set_maxrep = ? WHERE set_id = 1";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    outputError(600);
}
$stmt->bind_param("issii", $off,$title,$name,$mun,$repmun);
$stmt->execute();
$stmt->close();
$conn->close();
header('Content-Type: text/plain; charset=UTF-8');
echo 200;
?>