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
$allowed_vip_levels = array(2, 3, 4);
if (!in_array($vip, $allowed_vip_levels, true) || $uban !== USER_STATUS_NORMAL) {
    outputError(500);
}
// ========== 评论ID校验 ==========
$idsStr = isset($_POST['ids']) ? stripMagicQuotes(trim($_POST['ids'])) : '';
if (empty($idsStr)) {
    outputError(500);
}
// 拆分ID字符串为数组
$idArray = explode(',', $idsStr);
$validIds = array();
// 过滤并验证每个ID
foreach ($idArray as $idItem) {
    $idItem = trim($idItem);
    if (isPositiveInteger($idItem)) {
        $validIds[] = (int)$idItem;
    }
}
// 如果过滤后没有有效ID，返回500
if (empty($validIds)) {
    outputError(500);
}
// ========== 递归删除所有层级的回复 ==========
function deleteAllReplies($conn, $parentId) {
    // 1. 查询当前父ID下的所有直接回复ID
    $sql = "SELECT comm_id FROM ppz_subcomm WHERE comm_type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $parentId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // 2. 递归删除每个直接回复的子回复
    while ($row = $result->fetch_assoc()) {
        $childId = $row['comm_id'];
        deleteAllReplies($conn, $childId); // 递归删除子回复
    }
    
    // 3. 删除当前父ID下的所有直接回复
    $delSql = "DELETE FROM ppz_subcomm WHERE comm_type = ?";
    $delStmt = $conn->prepare($delSql);
    $delStmt->bind_param("i", $parentId);
    $delStmt->execute();
    
    // 释放资源
    $stmt->close();
    $result->free();
    $delStmt->close();
}

// ========== 批量处理评论删除 ==========
foreach ($validIds as $id) {
    // 判断评论是否存在
    $comm_sql = "SELECT comm_id,comm_admin FROM ppz_subcomm WHERE comm_id = ?";
    $comm_stmt = $conn->prepare($comm_sql);
    if (!$comm_stmt) {
        continue; // 准备语句失败则跳过当前ID
    }
    $comm_stmt->bind_param("i", $id);
    $comm_stmt->execute();
    $comm_retval = $comm_stmt->get_result();
    
    // 评论不存在则跳过
    if (mysqli_num_rows($comm_retval) <= 0) {
        $comm_stmt->close();
        $comm_retval->free();
        continue;
    }
    
    // 释放查询资源
    $comm_stmt->close();
    $comm_retval->free();
    
    // 执行递归删除：先删所有回复，再删主评论
    deleteAllReplies($conn, $id);
    
    // 删除主评论
    $del_sqlx = "DELETE FROM ppz_subcomm WHERE comm_id = ?";
    $del_stmtx = $conn->prepare($del_sqlx);
    if ($del_stmtx) {
        $del_stmtx->bind_param("i", $id);
        $del_stmtx->execute();
        $del_stmtx->close();
    }
}

$conn->close();
header('Content-Type: text/plain; charset=UTF-8');
echo 200;
?>