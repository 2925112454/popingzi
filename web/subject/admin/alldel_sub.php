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

// ========== 批量文章ID校验 ==========
$ids_str = isset($_POST['ids']) ? stripMagicQuotes(trim($_POST['ids'])) : '';
if (empty($ids_str)) {
    outputError(500);
}

// 分割ID并校验每个ID是否为正整数
$id_list = explode(',', $ids_str);
$valid_id_list = array();
foreach ($id_list as $id) {
    $id = trim($id);
    if (isPositiveInteger($id)) {
        $valid_id_list[] = (int)$id;
    } else {
        outputError(500);
    }
}

$valid_id_list = array_unique($valid_id_list);//去重
$valid_id_list = array_filter($valid_id_list);//去空
if (empty($valid_id_list)) {
    outputError(500);
}

function extractNumberBetweenHyphens($str) {
    $pattern = '/-(\d+)-/';
    if (preg_match($pattern, $str, $matches)) {
        return $matches[1];
    }
    return false;
}

// ========== 批量处理文章删除逻辑 ==========

// 1. 预编译查询文章信息的SQ
$article_sql = "SELECT id, text FROM ppz_subject WHERE BINARY id = ?";
if (!$article_stmt = $conn->prepare($article_sql)) {
    outputError(500);
}

// 2. 预编译删除评论的SQL
$del_comm_sql = "DELETE FROM ppz_subcomm WHERE comm_subid = ?";
if (!$del_comm_stmt = $conn->prepare($del_comm_sql)) {
    outputError(500);
}

// 3. 预编译删除文章的SQL
$del_art_sql = "DELETE FROM ppz_subject WHERE id = ?";
if (!$del_art_stmt = $conn->prepare($del_art_sql)) {
    outputError(500);
}

// 遍历每个合法ID执行删除逻辑
foreach ($valid_id_list as $article_id) {
    // ========== 查询文章信息 ==========
    $article_stmt->bind_param("i", $article_id);
    $article_stmt->execute();
    $article_retval = $article_stmt->get_result();

    // 校验文章是否存在
    if (mysqli_num_rows($article_retval) !== 1) {
        $article_retval->free();
        continue;//跳过当前ID
    }

    $article_row = $article_retval->fetch_array(MYSQLI_ASSOC);
    $text = $article_row['text']; // 文章内容
    $article_retval->free();

    // ========== 删除文章内关联的本地图片 ==========
    if (!empty($text)) {
        $img_pattern = '/<img[^>]+src="\.\.\/upload\/[^"]+\.(png|gif|jpg|jpeg|bmp|avif|webp|jfif)"[^>]*>/i';
        if (preg_match_all($img_pattern, $text, $img_matches)) {
            foreach ($img_matches[0] as $img_tag) {
                preg_match('/src="([^"]+)"/', $img_tag, $src_match);
                $img_path = isset($src_match[1]) ? $src_match[1] : '';
                // 管理员删除图片无需验证img_id，直接删除
                if (!empty($img_path)) {
                    // 拼接绝对路径（防止相对路径问题）
                    $abs_img_path = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($img_path, './');
                    if (file_exists($abs_img_path)) {
                        @unlink($abs_img_path);
                    }
                }
            }
        }
    }

    // ========== 删除文章下的评论 ==========
    $del_comm_stmt->bind_param("i", $article_id);
    $del_comm_stmt->execute();

    // ========== 删除文章 ==========
    $del_art_stmt->bind_param("i", $article_id);
    $del_art_stmt->execute();
}

// ========== 释放预编译语句资源 ==========
$article_stmt->close();
$del_comm_stmt->close();
$del_art_stmt->close();

// ========== 操作成功响应 ==========
header('Content-Type: text/plain; charset=UTF-8');
echo 200;

// 关闭数据库连接
if (isset($conn) && is_object($conn)) {
    $conn->close();
}
?>