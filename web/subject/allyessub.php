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
// 绑定参数（变量引用）
$bind_ppzusername = $ppzusername;
$user_stmt->bind_param("s", $bind_ppzusername);
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
// ========== ID校验 ==========
$id = isset($_POST['id']) ? stripMagicQuotes(trim($_POST['id'])) : '';
if (empty($id) || !isPositiveInteger($id) || $id != 200) {
    outputError(500);
}
// ========== 批量标记已读 ==========
function get_user_unread_comment_ids($conn, $user_uid) {
    $user_uid = (int)$user_uid;
    $comment_ids = array();
    
    // 步骤1：获取
    $sql1 = "SELECT sc.comm_id 
             FROM ppz_subcomm sc
             LEFT JOIN ppz_subject s ON sc.comm_subid = s.id
             WHERE sc.comm_yes = 0 
               AND sc.comm_type = 0 
               AND s.admin = {$user_uid} 
               AND sc.comm_admin != {$user_uid}";
    $ret1 = mysqli_query($conn, $sql1);
    if ($ret1 && mysqli_num_rows($ret1) > 0) {
        while ($row = mysqli_fetch_array($ret1)) {
            $comment_ids[] = (int)$row['comm_id'];
        }
    }
    mysqli_free_result($ret1);
    // 步骤2：获取「直接回复我的评论（任意层级）」的一级回复ID
    $my_comments_sql = "SELECT comm_id FROM ppz_subcomm WHERE comm_admin = {$user_uid}";
    $my_comments_ret = mysqli_query($conn, $my_comments_sql);
    $my_comment_ids = array();
    if ($my_comments_ret && mysqli_num_rows($my_comments_ret) > 0) {
        while ($row = mysqli_fetch_array($my_comments_ret)) {
            $my_comment_ids[] = (int)$row['comm_id'];
        }
    }
    mysqli_free_result($my_comments_ret);
    // 仅获取一级直接回复
    if (!empty($my_comment_ids)) {
        $my_comment_ids_str = implode(',', $my_comment_ids);
        $sql2 = "SELECT comm_id FROM ppz_subcomm 
                 WHERE comm_yes = 0 
                   AND comm_admin != {$user_uid}
                   AND comm_type IN ({$my_comment_ids_str})";
        $ret2 = mysqli_query($conn, $sql2);
        if ($ret2 && mysqli_num_rows($ret2) > 0) {
            while ($row = mysqli_fetch_array($ret2)) {
                $target_comm_id = (int)$row['comm_id'];
                $check_sql = "SELECT comm_admin FROM ppz_subcomm WHERE comm_id = {$target_comm_id} LIMIT 1";
                $check_ret = mysqli_query($conn, $check_sql);
                $check_row = mysqli_fetch_array($check_ret);
                mysqli_free_result($check_ret);
                if ($check_row && (int)$check_row['comm_admin'] !== $user_uid) {
                    $comment_ids[] = $target_comm_id;
                }
            }
        }
        mysqli_free_result($ret2);
    }
    return array_unique($comment_ids);
}
// ========== 执行批量标记已读操作 ==========
$allowed_comment_ids = get_user_unread_comment_ids($conn, $login_uid);
// 无未读评论直接返回成功
if (empty($allowed_comment_ids)) {
    outputError(200);
}
// 批量更新已读状态
$comment_ids_str = implode(',', $allowed_comment_ids);
$update_sql = "UPDATE ppz_subcomm SET comm_yes = 1 WHERE comm_id IN ({$comment_ids_str}) AND comm_admin != {$login_uid}";
if (!mysqli_query($conn, $update_sql)) {
    //error_log('批量标记已读失败：UID='.$login_uid.' | SQL='.$update_sql.' | 错误='.mysqli_error($conn));// 可选：记录错误日志，方便排查
    outputError(500);
}
// 标记成功
header('Content-Type: text/plain; charset=UTF-8');
echo 200;
?>