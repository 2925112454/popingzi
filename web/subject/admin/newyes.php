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
// 生成引用数组，解决bind_param传值警告
function getRefArray($arr) {
    $refs = [];
    foreach ($arr as &$val) {
        $refs[] = &$val;
    }
    unset($val);
    return $refs;
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
// 绑定参数
$user_stmt->bind_param("s", $ppzusername);
$user_stmt->execute();
$retval = $user_stmt->get_result();
// 校验用户是否存在
if (mysqli_num_rows($retval) !== 1) {
    $user_stmt->close();
    $retval->free();
    outputError(500, $conn);
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
$allowed_vip_levels = array(2,3,4);
if (!in_array($vip, $allowed_vip_levels, true) || $uban !== USER_STATUS_NORMAL) {
    outputError(500, $conn);
}
// ========== 参数处理 ==========
$ids = isset($_POST['ids']) ? stripMagicQuotes(trim($_POST['ids'])) : '';//批量处理的ID，用英文逗号分割
$if = isset($_POST['if']) ? stripMagicQuotes(trim($_POST['if'])) : '';//状态：1待审核，2驳回，3通过
$if = (int)$if;
$reason = isset($_POST['reason']) ? stripMagicQuotes(trim($_POST['reason'])) : '';//驳回理由，状态为2时有效(可留空)
// 基础参数校验
if (empty($ids) || !in_array($if, [1,2,3], true)) {
    outputError(500, $conn);
}
if ($if == 2) {
   $reason = htmlspecialchars($reason, ENT_QUOTES, 'UTF-8'); // 指定字符集，防止XSS
}else{
   $reason = ''; // 非驳回时，驳回理由置空
}
// ID数组处理
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
// ========== 验证ID是否存在 ==========
$placeholder = rtrim(str_repeat('?,', count($ids)), ',');
$sub_sql = "SELECT id FROM ppz_subject WHERE id IN ($placeholder)";
$sub_stmt = $conn->prepare($sub_sql);
if (!$sub_stmt) {
    outputError(500, $conn);
}
// 预处理参数绑定
$types = str_repeat('i', count($ids));
$bindParams = array_merge([$types], $ids);
// 转为引用数组再调用
$reflection = new ReflectionMethod('mysqli_stmt', 'bind_param');
$refParams = getRefArray($bindParams);
$reflection->invokeArgs($sub_stmt, $refParams);
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
// ========== 批量更新状态==========
$update_sql = "UPDATE ppz_subject SET yes = ?, no = ? WHERE id IN ($placeholder)";
$update_stmt = $conn->prepare($update_sql);
if (!$update_stmt) {
    outputError(600, $conn);
}
$update_types = 'is' . str_repeat('i', count($ids)); 
$update_params = array_merge(
    [$update_types],  // 类型字符串
    [$if],            // yes字段：状态值（1/2/3）
    [$reason],        // no字段：驳回理由（字符串）
    $ids              // 批量ID
);
// 转为引用数组再调用
$refParamsUpdate = getRefArray($update_params);
$reflection->invokeArgs($update_stmt, $refParamsUpdate);
// 执行更新
$update_stmt->execute();
// ========== 释放资源 + 响应 ==========
$update_stmt->close();
$conn->close();
header('Content-Type: text/plain; charset=UTF-8');
echo 200;
?>