<?php
mb_internal_encoding('UTF-8');
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION

// 封装统一的错误输出+终止函数
function outputError($code) {
    echo $code;
    exit;
}

// 魔法引号还原
function stripMagicQuotes($data) {
    if (get_magic_quotes_gpc() && is_string($data)) {
        return stripslashes($data);
    }
    return $data;
}

// 校验登录状态
if (empty($ppzusername) || $_SERVER['REQUEST_METHOD'] !== 'POST' || !is_string($ppzusername)){
    outputError(500);
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

// 获取客户端IP
function getConnectIp() {
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        return $ip;
    }                            
    return '0.0.0.0';
}

// 安全过滤HTML内容（防XSS）
function safeFilterHtml($content, $allowedTags) {
    // 1. 还原转义字符
    $content = htmlspecialchars_decode($content, ENT_QUOTES);
    // 2. 过滤危险属性：on*事件、javascript伪协议、expression等
    $content = preg_replace('/<[^>]*on\w+=[^>]*>/i', '', $content);
    $content = preg_replace('/<a[^>]*href\s*=\s*["\']?javascript:[^>]*>/i', '<a>', $content);
    $content = preg_replace('/<[^>]*style\s*=\s*["\']?[^"\']*expression[^"\']*["\']?[^>]*>/i', '', $content);
    // 3. 仅保留允许的标签
    $content = strip_tags($content, implode('', array_map(function($tag) {
        return "<$tag>";
    }, $allowedTags)));
    return $content;
}

// 链接数据库
include $_SERVER['DOCUMENT_ROOT'] . '/inc/conn.php';

// ========== 查询用户信息 ==========
$user_sql = "select * from ppz_newusername where binary uusername = ?";
$user_stmt = $conn->prepare($user_sql);
if (!$user_stmt) {
    outputError(500);
}
$user_stmt->bind_param("s", $ppzusername);
$user_stmt->execute();
$retval = $user_stmt->get_result();

if (mysqli_num_rows($retval) !== 1) {
    $user_stmt->close();
    $retval->free();
    outputError(500);
}

// 获取用户信息（仅一次查询）
$row = $retval->fetch_array();
$vip = (int)$row['ustatus']; // 身份，1普通会员，2为管理员，3为副站长，4为站长
$id = $row['uid']; // 会员ID
$uban = (int)$row['uban']; // 封禁状态，1为正常

// 释放用户查询资源
$user_stmt->close();
$retval->free();

// 校验用户权限
$allowed_vip_levels = array(1,2,3,4);
define('USER_STATUS_NORMAL', 1);
if (!in_array($vip, $allowed_vip_levels, true) || $uban !== USER_STATUS_NORMAL) {
    outputError(500);
}

// ========== 使用预处理查询配置信息（仅一次查询） ==========
$set_sql = "select set_off,set_mun from ppz_subset where binary set_id = 1";
$set_stmt = $conn->prepare($set_sql);
if (!$set_stmt) {
    outputError(500);
}
$set_stmt->execute();
$set_retval = $set_stmt->get_result();

if (mysqli_num_rows($set_retval) !== 1) {
    $set_stmt->close();
    $set_retval->free();
    outputError(500);
}

$set_row = $set_retval->fetch_array();
$set_off = $set_row['set_off']; // 开关：0：关闭，1：开启
$set_mun = $set_row['set_mun']; // 单个会员每天最多可发表的篇数，0为不限制

// 释放配置查询资源
$set_stmt->close();
$set_retval->free();

if ($set_off != 1) {
    outputError(500);
}

// ========== 查询标签配置（仅一次查询） ==========
$subtype_sql = "select sub_id,sub_name from ppz_subtype order by sub_id asc";
$subtype_stmt = $conn->prepare($subtype_sql);
if (!$subtype_stmt) {
    outputError(500);
}
$subtype_stmt->execute();
$subtype_retval = $subtype_stmt->get_result();

if (mysqli_num_rows($subtype_retval) <= 0) {
    $subtype_stmt->close();
    $subtype_retval->free();
    outputError(500);
}

// 获取并过滤POST数据
$type = stripMagicQuotes(trim($_POST['tag']));//分类标签
$title = htmlspecialchars(stripMagicQuotes(trim($_POST['title'])), ENT_QUOTES, 'UTF-8');//标题
$content = stripMagicQuotes(trim($_POST['content']));//内容
$quote = isset($_POST['quote']) ? stripMagicQuotes(trim($_POST['quote'])) : '';//引用的文章或话题ID，为了避免混淆，话题ID用{}包裹
$htmltag = ["p","br","span","img","b","em","strong","a","blockquote","h2","h3","h4"];//允许的HTML标签
$userip = getConnectIp();//用户IP

if (!empty($quote)) {
    // 统一分隔符：中文逗号替换为英文逗号
    $quote = str_replace('，', ',', $quote);
    // 拆分、去重、过滤空元素
    $quote_arr = array_filter(array_unique(explode(',', $quote)));
    // 限制最多10个引用ID
    if (count($quote_arr) > 10) {
        outputError(500);
    }
    // 分离文章ID（纯数字）和话题ID（{数字}）
    $article_ids = []; // 存储纯数字文章ID
    $topic_ids = [];   // 存储{数字}里的话题ID
    $validated_ids = [];
    
    foreach ($quote_arr as $item) {
        // 正则验证格式：要么是纯数字，要么是{数字}
        if (preg_match('/^(\d+|\{\d+\})$/', $item)) {
            // 提取纯数字后调用正整数校验函数
            $pure_id = str_replace(['{', '}'], '', $item);
            if (!isPositiveInteger($pure_id)) {
                outputError(500);
            }
            
            // 分类存储ID（用于后续数据库校验）
            if (strpos($item, '{') !== false) {
                $topic_ids[] = $pure_id;
            } else {
                $article_ids[] = $pure_id;
            }
            
            $validated_ids[] = $item; // 保留原始格式（带{}或不带）
        } else {
            outputError(500);
        }
    }

    // ========== 校验文章ID是否存在且审核通过（状态4） ==========
    if (!empty($article_ids)) {
        // 拼接占位符
        $article_placeholders = rtrim(str_repeat('?,', count($article_ids)), ',');
        $article_sql = "SELECT rowid FROM ppz_row WHERE rowid IN ($article_placeholders) AND rowyes = 4";
        
        $article_stmt = $conn->prepare($article_sql);
        if (!$article_stmt) {
            outputError(500);
        }
        
        // 绑定参数
        $article_types = str_repeat('i', count($article_ids));
        // 创建引用数组
        $article_bind_params = [$article_types];
        for ($i = 0; $i < count($article_ids); $i++) {
            $article_bind_params[] = &$article_ids[$i];
        }
        call_user_func_array([$article_stmt, 'bind_param'], $article_bind_params);
        
        $article_stmt->execute();
        $article_result = $article_stmt->get_result();
        $found_article_ids = [];
        while ($row = $article_result->fetch_assoc()) {
            $found_article_ids[] = $row['rowid'];
        }
        // 校验：查询到的ID数量必须和传入的一致
        if (count($found_article_ids) !== count($article_ids)) {
            $article_stmt->close();
            $article_result->free();
            outputError(650);
        }
        // 释放资源
        $article_stmt->close();
        $article_result->free();
    }

    // ========== 校验话题ID是否存在且审核通过 ==========
    if (!empty($topic_ids)) {
        // 拼接占位符
        $topic_placeholders = rtrim(str_repeat('?,', count($topic_ids)), ',');
        $topic_sql = "SELECT id FROM ppz_subject WHERE id IN ($topic_placeholders) AND yes = 3";
        
        $topic_stmt = $conn->prepare($topic_sql);
        if (!$topic_stmt) {
            outputError(500);
        }
        
        // 绑定参数
        $topic_types = str_repeat('i', count($topic_ids));
        // 创建引用数组
        $topic_bind_params = [$topic_types];
        for ($i = 0; $i < count($topic_ids); $i++) {
            $topic_bind_params[] = &$topic_ids[$i];
        }
        call_user_func_array([$topic_stmt, 'bind_param'], $topic_bind_params);
        
        $topic_stmt->execute();
        $topic_result = $topic_stmt->get_result();
        $found_topic_ids = [];
        while ($row = $topic_result->fetch_assoc()) {
            $found_topic_ids[] = $row['id'];
        }
        // 校验：查询到的ID数量必须和传入的一致
        if (count($found_topic_ids) !== count($topic_ids)) {
            $topic_stmt->close();
            $topic_result->free();
            outputError(650);
        }
        // 释放资源
        $topic_stmt->close();
        $topic_result->free();
    }
    // 重新拼接为规范字符串
    $quote = implode(',', $validated_ids);
}

// 校验标签ID
if (empty($type) || !isPositiveInteger($type)) {
    $subtype_stmt->close();
    $subtype_retval->free();
    outputError(500);
}

// 校验标签是否合法
$sql_type = false;
while($subtype_row = $subtype_retval->fetch_array()){
    if ($subtype_row['sub_id'] == $type) {
        $sql_type = true;
        break;
    }
}
$subtype_stmt->close();
$subtype_retval->free();

if (!$sql_type) {
    outputError(500);
}

// 校验标题
if (empty($title) || mb_strlen($title, 'UTF-8') > 180) {
    outputError(500);
}

// 安全过滤内容+校验非法标签
$content_escaped = safeFilterHtml($content, $htmltag);
// 检查是否包含非法标签
preg_match_all('/<\s*(\/?)\s*([a-zA-Z0-9]+)[^>]*>/i', $content_escaped, $matches);
$illegal_tag = false;
if (!empty($matches[2])) {
    foreach ($matches[2] as $tag) {
        $tag = strtolower(trim($tag));
        if (!in_array($tag, $htmltag)) {
            $illegal_tag = true;
            break;
        }
    }
}
if ($illegal_tag) {
    outputError(500);
}

// 校验内容非空（去除所有标签后）
$pure_text = strip_tags($content_escaped);
$pure_text_trimmed = preg_replace('/\s+/', '', $pure_text);

// 检查是否包含 <img> 标签
if (empty($pure_text_trimmed)) {
    if (!preg_match('/<img[^>]*>/i', $content_escaped)) {
        outputError(500); // 没有文字也没有图片，才算空
    }
}

// 校验每日发帖限制
$today = true;
if ($set_mun > 0) {
    $today_start = strtotime(date('Y-m-d 00:00:00'));
    $today_end = strtotime(date('Y-m-d 23:59:59'));

    // 查询今日发帖数量
    $count_sql = "SELECT COUNT(*) AS total FROM ppz_subject WHERE admin = ? AND time BETWEEN FROM_UNIXTIME(?) AND FROM_UNIXTIME(?)";
    $count_stmt = $conn->prepare($count_sql);
    if (!$count_stmt) {
        outputError(500);
    }
    $count_stmt->bind_param("iss", $id, $today_start, $today_end);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $count_row = $count_result->fetch_assoc();
    $today_post_count = intval($count_row['total']);

    // 释放资源
    $count_stmt->close();
    $count_result->free();

    // 判断是否超出限制
    if ($today_post_count >= $set_mun) {
        $today = false;
    }
}

if ($today) {
    // 安全过滤内容
    $content_final = safeFilterHtml($content, $htmltag);
    // 插入数据
    $insert_sql = "INSERT INTO ppz_subject (title, admin, type, ip, text,quote) VALUES (?, ?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    if (!$insert_stmt) {
        outputError(600);
    }
    $insert_stmt->bind_param("siisss", $title, $id, $type, $userip, $content_final, $quote);
    $insert_result = $insert_stmt->execute();
    
    if ($insert_result) {
        echo 200;
    } else {
        echo 600;
        // $errorInfo = [
        //     'code' => 600,
        //     'msg' => '数据插入失败',
        //     'mysql_errno' => $insert_stmt->errno, // 语句级错误码
        //     'mysql_error' => $insert_stmt->error, // 语句级错误信息
        //     'sql_state' => $insert_stmt->sqlstate // SQL状态码（可选）
        // ];
        // echo json_encode($errorInfo, JSON_UNESCAPED_UNICODE);
    }
    $insert_stmt->close();
} else {
    echo 400;
}

// 关闭数据库连接
mysqli_close($conn);
?>