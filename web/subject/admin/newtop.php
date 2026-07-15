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
function outputError($code, $conn = null, $stmt = null) {
    // 释放资源
    if ($stmt && method_exists($stmt, 'close')) {
        $stmt->close();
    }
    if ($conn && method_exists($conn, 'close')) {
        $conn->close();
    }
    header('Content-Type: text/plain; charset=UTF-8');
    echo (string)$code;
    exit;
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
    outputError(500, $conn);
}
$user_stmt->bind_param("s", $ppzusername);
$user_stmt->execute();
$retval = $user_stmt->get_result();
if (mysqli_num_rows($retval) !== 1) {
    $user_stmt->close();
    $retval->free();
    outputError(500, $conn);
}
$row = $retval->fetch_array(MYSQLI_ASSOC);
$vip = (int)$row['ustatus'];
$login_uid = (int)$row['uid'];
$uban = (int)$row['uban'];
$user_stmt->close();
$retval->free();

// ========== 权限校验 ==========
define('USER_STATUS_NORMAL', 1);
$allowed_vip_levels = array(2,3,4);
if (!in_array($vip, $allowed_vip_levels, true) || $uban !== USER_STATUS_NORMAL) {
    outputError(500, $conn);
}

// ========== 参数处理 ==========
$ids = isset($_POST['ids']) ? stripMagicQuotes(trim($_POST['ids'])) : '';
$if = isset($_POST['if']) ? stripMagicQuotes(trim($_POST['if'])) : '';
$if = (int)$if;
if (empty($ids) || !in_array($if, [1,2,3], true)) {
    outputError(500, $conn);
}
$ids = explode(',', $ids);
$ids = array_unique($ids);
$ids = array_filter($ids, function($id) {
    return isPositiveInteger($id);
});
$ids = array_values($ids);
$ids = array_map('intval', $ids);
if (empty($ids)) {
    outputError(500, $conn);
}

// ========== 验证ID是否存在 【修复动态绑定引用警告】 ==========
$placeholder = rtrim(str_repeat('?,', count($ids)), ',');
$sub_sql = "SELECT id FROM ppz_subject WHERE id IN ($placeholder)";
$sub_stmt = $conn->prepare($sub_sql);
if (!$sub_stmt) {
    outputError(500, $conn);
}
$types = str_repeat('i', count($ids));
// 构造引用数组（标准无警告写法）
$bindRefs = [$types];
foreach ($ids as &$val) {
    $bindRefs[] = &$val;
}
unset($val); // 销毁残留引用
// 反射调用绑定
$reflection = new ReflectionMethod('mysqli_stmt', 'bind_param');
$reflection->invokeArgs($sub_stmt, $bindRefs);

$sub_stmt->execute();
$retval_sub = $sub_stmt->get_result();
$exist_ids_count = mysqli_num_rows($retval_sub);
if ($exist_ids_count !== count($ids)) {
    $sub_stmt->close();
    $retval_sub->free();
    outputError(500, $conn);
}
$sub_stmt->close();
$retval_sub->free();

// ========== 批量更新状态【修复动态绑定引用警告】 ==========
$update_sql = "UPDATE ppz_subject SET top = ? WHERE id IN ($placeholder)";
$update_stmt = $conn->prepare($update_sql);
if (!$update_stmt) {
    outputError(600, $conn);
}
$update_types = 'i' . str_repeat('i', count($ids));
// 构造引用数组
$updateRefs = [$update_types, &$if];
foreach ($ids as &$idVal) {
    $updateRefs[] = &$idVal;
}
unset($idVal);
$reflection->invokeArgs($update_stmt, $updateRefs);

$update_stmt->execute();

// ========== 释放资源 + 响应 ==========
$update_stmt->close();
$conn->close();
header('Content-Type: text/plain; charset=UTF-8');
echo 200;
?>