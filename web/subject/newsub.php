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
// ========== 文章ID校验 ==========
$id = isset($_POST['subid']) ? stripMagicQuotes(trim($_POST['subid'])) : '';
if (empty($id) || !isPositiveInteger($id)) {
    outputError(500);
}
$id = (int)$id;
// ========== 评论类型校验 ==========
$type = isset($_POST['type']) ? stripMagicQuotes(trim($_POST['type'])) : 0;
if (is_null($type) || $type < 0) {
    outputError(500);
}
if ($type != 0) {
    if(!isPositiveInteger($type)){
        outputError(500);
    }
}
$type = (int)$type;
// ========== 查询文章信息==========
$set_sql = "SELECT id FROM ppz_subject WHERE BINARY id = ?";
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
$set_stmt->close();
$set_retval->free();

$content= isset($_POST['content']) ? stripMagicQuotes(trim($_POST['content'])) : '';
if (empty($content)) {
    outputError(500);//判断内容是否为空
}
if (mb_strlen($content, 'utf-8') > 320) {
    outputError(500);//判断内容是否超过320字符(utf-8编码)
}
if (preg_match('/^[\s\r\n]*$/', $content)) {
    outputError(500);//判断内容是否全为空格、回车、换行符
}
// ========== 违禁词校验逻辑 ==========
$fuck_words = '';// 初始化违禁词为空，默认不限制
if (isset($conn) && $conn instanceof mysqli && !$conn->connect_error) {
    try {
        // 获取违禁词设置参数，多个违禁字用|分隔
        $fuck_sql = "SELECT fuck FROM ppz_fuck WHERE id = 1";
        $fuck_stmt = $conn->prepare($fuck_sql);
        if ($fuck_stmt) {
            $fuck_stmt->execute();
            $fuck_retval = $fuck_stmt->get_result();
            if (mysqli_num_rows($fuck_retval) === 1) {
                $fuck_row = $fuck_retval->fetch_array(MYSQLI_ASSOC);
                if (isset($fuck_row['fuck']) && trim($fuck_row['fuck']) !== '') {
                    $fuck_words = trim($fuck_row['fuck']);
                }
            }
            if (isset($fuck_retval)) $fuck_retval->free();
            $fuck_stmt->close();
        }
    } catch (Exception $e) {
        $fuck_words = '';
    }
}
if (!empty($fuck_words)) {
    $words_array = explode('|', $fuck_words);
    $escaped_words = array_map(function ($word) {
        return preg_quote(trim($word), '/');
    }, $words_array);
    $fuck_pattern = '/(' . implode('|', $escaped_words) . ')/i';
    if (isset($content) && preg_match($fuck_pattern, $content)) {
        outputError(800); // 包含违禁词，返回错误
    }
}

// ========== 防频繁评论校验 ==========
define('COMMENT_INTERVAL', 60);// 限制时间：60秒（可根据需求调整）
$check_sql = "SELECT UNIX_TIMESTAMP(comm_time) as comm_time_ts FROM ppz_subcomm WHERE comm_admin = ? AND comm_subid = ? ORDER BY comm_time DESC LIMIT 1";
$check_stmt = $conn->prepare($check_sql);
if (!$check_stmt) {
    outputError(500);
}
$check_stmt->bind_param("ii", $login_uid, $id);
$check_stmt->execute();
$check_retval = $check_stmt->get_result();
// 检查是否存在最近评论，且间隔小于限制时间
if (mysqli_num_rows($check_retval) === 1) {
    $check_row = $check_retval->fetch_array(MYSQLI_ASSOC);
    $last_comment_time = (int)$check_row['comm_time_ts'];
    $current_time = time();
    $time_diff = $current_time - $last_comment_time;
    if ($time_diff < COMMENT_INTERVAL) {
        $check_stmt->close();
        $check_retval->free();
        outputError(900);
    }
}
$check_stmt->close();
$check_retval->free();

$content = htmlspecialchars($content);//转义
$ip= $_SERVER['REMOTE_ADDR'];
$comment_time = date('Y-m-d H:i:s'); // 当前时间

// ========== 提交评论 ==========
$newcom_sql = "INSERT INTO ppz_subcomm (comm_subid, comm_text, comm_ip, comm_admin, comm_type, comm_time) VALUES (?, ?, ?, ?, ?, ?)";
$newcom_stmt = $conn->prepare($newcom_sql);
if (!$newcom_stmt) {
    outputError(600);
}
$newcom_stmt->bind_param("issiis", $id, $content, $ip, $login_uid, $type, $comment_time);
$execute_result = $newcom_stmt->execute();
if (!$execute_result) {
    $newcom_stmt->close();
    outputError(600); // 执行插入失败
}
if ($newcom_stmt->affected_rows !== 1) {
    $newcom_stmt->close();
    outputError(600);
}
$newcom_stmt->close();
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
// 评论提交成功，返回成功标识（如200）
header('Content-Type: text/plain; charset=UTF-8');
echo '200';
exit;
?>