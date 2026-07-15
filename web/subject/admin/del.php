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

/**
 * 统一错误输出函数
 * @param int $code 错误码
 */
function outputError($code) {
    header('Content-Type: text/plain; charset=UTF-8');
    echo (string)$code;
    exit;
}

/**
 * 魔法引号还原
 * @param mixed $data 要处理的数据
 * @return mixed
 */
function stripMagicQuotes($data) {
    if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() && is_string($data)) {
        return stripslashes($data);
    }
    return $data;
}

/**
 * 正整数校验函数
 * @param string $str 要校验的字符串
 * @return bool
 */
function isPositiveInteger($str) {
    if (!is_string($str) || trim($str) === '') {
        return false;
    }
    // 兼容ctype_digit对空字符串/特殊字符的处理
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
$allowed_vip_levels = array(2, 3, 4);
if (!in_array($vip, $allowed_vip_levels, true) || $uban !== USER_STATUS_NORMAL) {
    outputError(500);
}

// ========== 文章ID校验 ==========
$id = isset($_POST['id']) ? stripMagicQuotes(trim($_POST['id'])) : '';
if (empty($id) || !isPositiveInteger($id)) {
    outputError(500);
}
$id = (int)$id; // 强制转为整数，防止注入

// ========== 查询文章信息==========
$set_sql = "SELECT id, admin, text FROM ppz_subject WHERE BINARY id = ?";
$set_stmt = $conn->prepare($set_sql);
if (!$set_stmt) {
    outputError(500);
}

// 绑定参数（ID和UID均为整数）
$set_stmt->bind_param("i", $id);
$set_stmt->execute();
$set_retval = $set_stmt->get_result();

// 校验文章是否存在且归属当前用户
if (mysqli_num_rows($set_retval) !== 1) {
    $set_stmt->close();
    $set_retval->free();
    outputError(500);
}

$set_row = $set_retval->fetch_array(MYSQLI_ASSOC);
$text = $set_row['text']; // 文章内容

// 释放资源
$set_stmt->close();
$set_retval->free();
function extractNumberBetweenHyphens($str) {
    $pattern = '/-(\d+)-/';
    if (preg_match($pattern, $str, $matches)) {
        return $matches[1];
    }
    return false;
}
if (!empty($text)) {
    // 若内容里存在本地图片，则删除
    $img_pattern = '/<img[^>]+src="\.\.\/upload\/[^"]+\.(png|gif|jpg|jpeg|bmp|avif|webp|jfif)"[^>]*>/i';
    if (preg_match_all($img_pattern, $text, $img_matches)) {
        foreach ($img_matches[0] as $img_tag) {
            preg_match('/src="([^"]+)"/', $img_tag, $src_match);
            $img_path = isset($src_match[1]) ? $src_match[1] : '';
            $img_id = extractNumberBetweenHyphens($img_path);
            if ($img_id==$login_uid) {
                if (file_exists($img_path)) {
                    unlink($img_path);
                }
            }
        }
    }
}

// ========== 查询文章下的评论并删除 ==========
$del_comm_sql = "DELETE FROM ppz_subcomm WHERE comm_subid = ?";
$del_comm_stmt = $conn->prepare($del_comm_sql);
if (!$del_comm_stmt) {
    outputError(500);
}
$del_comm_stmt->bind_param("i", $id);
if (!$del_comm_stmt->execute()) {
    $del_comm_stmt->close();
    outputError(500);
}
$del_comm_stmt->close();

// ========== 删除文章 ==========
$del_art_sql = "DELETE FROM ppz_subject WHERE id = ?";
$del_art_stmt = $conn->prepare($del_art_sql);
if (!$del_art_stmt) {
    outputError(500);
}
$del_art_stmt->bind_param("i", $id);
if (!$del_art_stmt->execute()) {
    $del_art_stmt->close();
    outputError(500);
}
if ($del_art_stmt->affected_rows !== 1) {
    $del_art_stmt->close();
    outputError(500);
}
$del_art_stmt->close();

// ========== 操作成功响应 ==========
header('Content-Type: text/plain; charset=UTF-8');
echo 200;

// 关闭数据库连接
if (isset($conn) && is_object($conn)) {
    $conn->close();
}
?>