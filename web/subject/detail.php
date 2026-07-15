<?php
include $_SERVER['DOCUMENT_ROOT'].'/inc/inc.php';//通用
header("Content-type: text/html; charset=utf-8");
// 跳转函数
function redirectWithMsg($url) {
    header("Location: {$url}");
    exit;
}

// 时间格式化函数：转换为"十分钟前、1小时前、1天前"等格式
function format_time($timestamp) {
    $now = time();
    $diff = $now - $timestamp;
    
    if ($diff < 60) {
        return '刚刚';
    } elseif ($diff < 3600) {
        $min = floor($diff / 60);
        return $min.'分钟前';
    } elseif ($diff < 86400) {
        $hour = floor($diff / 3600);
        return $hour.'小时前';
    } elseif ($diff < 2592000) {
        $day = floor($diff / 86400);
        return $day.'天前';
    } elseif ($diff < 31536000) {
        $month = floor($diff / 2592000);
        return $month.'月前';
    } else {
        $year = floor($diff / 31536000);
        return $year.'年前';
    }
}

// 统计点赞数：解析逗号分隔的用户ID字符串，返回有效ID数量
function countLikes($like_str) {
    if (empty($like_str) || !is_string($like_str)) {
        return 0;
    }
    $like_ids = explode(',', trim($like_str));
    $valid_ids = array_filter($like_ids, function($id) {
        return is_numeric(trim($id)) && trim($id) > 0;
    });
    return count($valid_ids);
}

// 校验文章ID参数
function is_number($str) {
    if (is_null($str) || is_bool($str) || is_array($str) || is_object($str)|| !is_numeric($str)) {
        return false;
    }
    $str = trim((string)$str);
    if (preg_match('/^[1-9][0-9]*$/', $str)) {
        return true;
    }
    return false;
}

// 获取用户唯一标识（优先登录UID，无则生成设备指纹）
function getUserUniqueId($conn, $ppzusername) {
    // 登录用户用UID
    if (!empty($ppzusername)) {
        $user_uid_sql = "SELECT uid FROM ppz_newusername WHERE uusername = '".mysqli_real_escape_string($conn, $ppzusername)."' LIMIT 1";
        $user_uid_ret = mysqli_query($conn, $user_uid_sql);
        if ($user_uid_ret && mysqli_num_rows($user_uid_ret) > 0) {
            $user_uid_row = mysqli_fetch_array($user_uid_ret);
            return 'uid_'.intval($user_uid_row['uid']);
        }
    }
    // 未登录用户生成设备指纹（基于IP+UA）
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'unknown';
    return 'device_'.md5($ip.$ua);
}

// 检查是否已统计过阅读量（24小时内）
function hasCountedRead($conn, $article_id, $unique_id) {
    $safe_article_id = mysqli_real_escape_string($conn, $article_id);
    $safe_unique_id = mysqli_real_escape_string($conn, $unique_id);
    // 检查临时表（无则创建）
    $create_table_sql = "CREATE TABLE IF NOT EXISTS ppz_read_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        article_id INT NOT NULL,
        unique_id VARCHAR(64) NOT NULL,
        create_time INT NOT NULL,
        UNIQUE KEY uk_article_unique (article_id, unique_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    mysqli_query($conn, $create_table_sql);
    
    // 检查24小时内是否已有记录
    $now = time();
    $expire_time = $now - 86400;
    $check_sql = "SELECT id FROM ppz_read_log WHERE article_id = {$safe_article_id} AND unique_id = '{$safe_unique_id}' AND create_time > {$expire_time} LIMIT 1";
    $check_ret = mysqli_query($conn, $check_sql);
    return mysqli_num_rows($check_ret) > 0;
}

// 记录阅读日志并更新阅读量
function recordReadLogAndUpdateView($conn, $article_id, $unique_id) {
    $safe_article_id = mysqli_real_escape_string($conn, $article_id);
    $safe_unique_id = mysqli_real_escape_string($conn, $unique_id);
    $now = time();
    
    // 插入阅读日志（利用唯一索引防重复）
    $insert_log_sql = "INSERT IGNORE INTO ppz_read_log (article_id, unique_id, create_time) VALUES ({$safe_article_id}, '{$safe_unique_id}', {$now})";
    mysqli_query($conn, $insert_log_sql);
    
    // 只有插入成功才更新阅读量
    if (mysqli_affected_rows($conn) > 0) {
        $update_views_sql = "UPDATE ppz_subject SET eyes = eyes + 1 WHERE id = {$safe_article_id} AND yes = 3";
        mysqli_query($conn, $update_views_sql);
    }
}

// 获取文章ID
$article_id = 0;
if(isset($_GET['id']) && !empty($_GET['id']) && is_number($_GET['id'])){
    $article_id = intval(trim($_GET['id']));
} else {
    redirectWithMsg('/subject/');
}

$unread_user_ustatus=1;
if (!empty($ppzusername)){
    // 获取登录用户UID（仅登录时执行）
    $user_uid_sql = "SELECT uid,ustatus FROM ppz_newusername WHERE uusername = '".mysqli_real_escape_string($conn, $ppzusername)."' LIMIT 1";
    $user_uid_ret = mysqli_query($conn, $user_uid_sql);
    $user_uid = 0;
    if ($user_uid_ret && mysqli_num_rows($user_uid_ret) > 0) {
        $user_uid_row = mysqli_fetch_array($user_uid_ret);
        $user_uid = intval($user_uid_row['uid']);
        $unread_user_ustatus = intval($user_uid_row['ustatus']);
    }
}

// 获取文章基础信息
if($unread_user_ustatus==4||$unread_user_ustatus==3||$unread_user_ustatus==2){
    $article_sql = "SELECT * FROM ppz_subject WHERE id = {$article_id} LIMIT 1";
}else{
    $article_sql = "SELECT * FROM ppz_subject WHERE id = {$article_id} AND yes = 3 LIMIT 1";
}
$article_ret = mysqli_query($conn, $article_sql);
if (!$article_ret || mysqli_num_rows($article_ret) == 0) {
    redirectWithMsg('/subject/');
}
$article = mysqli_fetch_array($article_ret); 

// ========== 阅读量统计 ==========
$cookie_name = "ppz_read_articles";
$read_articles = isset($_COOKIE[$cookie_name]) ? explode(',', $_COOKIE[$cookie_name]) : [];
$read_articles = array_filter($read_articles, function($id) { // 过滤空值和非数字
    return is_numeric($id) && $id > 0;
});

// 获取用户唯一标识
$unique_id = getUserUniqueId($conn, $ppzusername);

// 双重校验：Cookie + 数据库日志（防止Cookie被清空导致重复统计）
if (!in_array($article_id, $read_articles) && !hasCountedRead($conn, $article_id, $unique_id)) {
    // 1. 更新Cookie（保留最多300个）
    $read_articles[] = $article_id;
    $read_articles = array_unique($read_articles); // 去重
    if (count($read_articles) > 300) {
        $read_articles = array_slice($read_articles, -300); // 保留最后300个
    }
    // 设置 Cookie
    setcookie(
        $cookie_name, 
        implode(',', $read_articles), 
        time() + 86400 * 1,
        '/',
        '',
        false,
        true
    );
    
    // 2. 记录日志并更新阅读量（原子操作，防重复）
    recordReadLogAndUpdateView($conn, $article_id, $unique_id);
}

// 获取文章标签名称
$article_tag_name = "未知标签"; // 文章专属标签名称变量
$article_tag_id = 0; // 文章专属标签ID变量
if (is_number($article['type'])) {
    $article_tag_id = intval($article['type']);
    // 增加SQL防注入处理
    $safe_tag_id = mysqli_real_escape_string($conn, $article_tag_id);
    $tag_sql = "SELECT sub_name FROM ppz_subtype WHERE sub_id = {$safe_tag_id} LIMIT 1";
    $tag_ret = mysqli_query($conn, $tag_sql);
    // 标签不存在/已删除则重置为未知标签、ID=0
    if (!$tag_ret || mysqli_num_rows($tag_ret) == 0) {
        $article_tag_name = "未知标签";
        $article_tag_id = 0;
    } else {
        $tag_row = mysqli_fetch_array($tag_ret);
        $article_tag_name = htmlspecialchars($tag_row['sub_name'], ENT_QUOTES, 'UTF-8');
    }
}

// 获取文章作者信息
$author_name = "未知作者";
$author_uid = 0; // 作者UID，用于跳转
$author_avatar = "/images/web/default.jpg"; // 默认头像
if (is_number($article['admin'])) {
    $author_uid = intval($article['admin']);
    $safe_author_uid = mysqli_real_escape_string($conn, $author_uid);
    $author_sql = "SELECT uname, uimg FROM ppz_newusername WHERE uid = {$safe_author_uid} LIMIT 1";
    $author_ret = mysqli_query($conn, $author_sql);
    if ($author_ret && mysqli_num_rows($author_ret) > 0) {
        $author_row = mysqli_fetch_array($author_ret);
        // 显示作者昵称（uname字段）
        $author_name = !empty($author_row['uname']) ? htmlspecialchars($author_row['uname'], ENT_QUOTES, 'UTF-8') : "未知作者";
        // 处理作者头像，为空则用默认头像
        $author_avatar = !empty($author_row['uimg']) ? htmlspecialchars($author_row['uimg'], ENT_QUOTES, 'UTF-8') : "/images/web/default.jpg";
    }
}

// 阅览量和评论数格式化函数
function format_views($views) {
    $units = [
        100000000 => '亿+',
        10000000  => '千万',
        1000000   => '百万',
        10000     => '万'
    ];
    foreach ($units as $threshold => $unit) {
        if ($views >= $threshold) {
            $formatted_value = $views / $threshold;
            return rtrim(rtrim(sprintf("%.1f", $formatted_value), '0'), '.') . $unit;
        }
    }
    return $views;
}

// 获取回复最大层级限制
function getMaxReplyLevel($conn) {
    $max_level = 0; // 默认0表示不限制
    $sql = "SELECT set_maxrep FROM ppz_subset LIMIT 1";
    $ret = mysqli_query($conn, $sql);
    if ($ret && mysqli_num_rows($ret) > 0) {
        $row = mysqli_fetch_array($ret);
        $max_level = intval($row['set_maxrep']);
    }
    return $max_level;
}

// 将递归函数 - 新增层级参数限制
if (!function_exists('renderReplies')) {
    function renderReplies($conn, $parent_id, $article_id, $ppzusername, $current_level = 1) {
        $max_level = getMaxReplyLevel($conn);
        // 如果达到最大层级且最大层级不为0，不再查询子回复
        if ($max_level > 0 && $current_level > $max_level) {
            return;
        }
        
        $safe_article_id = mysqli_real_escape_string($conn, $article_id);
        $safe_parent_id = mysqli_real_escape_string($conn, $parent_id);
        $reply_sql = "SELECT comm_id, comm_admin, comm_text, comm_time, comm_top,
               LENGTH(comm_top) - LENGTH(REPLACE(comm_top, ',', '')) + 1 AS like_count
              FROM ppz_subcomm 
              WHERE comm_subid = '{$safe_article_id}' AND comm_type = {$safe_parent_id} 
              ORDER BY IF(like_count IS NULL OR comm_top = '', 0, like_count) DESC, comm_id DESC";
        $reply_ret = mysqli_query($conn, $reply_sql);
        
        if (mysqli_num_rows($reply_ret) > 0) {
            echo '<div class="comment-replies">';
            while ($reply = mysqli_fetch_array($reply_ret)) {
                $reply_id = intval($reply['comm_id']);
                $reply_admin_uid = intval($reply['comm_admin']);
                $reply_text = htmlspecialchars($reply['comm_text'], ENT_QUOTES, 'UTF-8');
                $reply_time = format_time(strtotime($reply['comm_time']));
                $reply_top = countLikes($reply['comm_top']);
                $reply_author_name = "未知作者";
                $reply_author_avatar = "/images/web/default.jpg";
                $safe_reply_uid = mysqli_real_escape_string($conn, $reply_admin_uid);
                $reply_author_sql = "SELECT uname, uimg ,uid FROM ppz_newusername WHERE uid = {$safe_reply_uid} LIMIT 1";
                $reply_author_ret = mysqli_query($conn, $reply_author_sql);
                if ($reply_author_ret && mysqli_num_rows($reply_author_ret) > 0) {
                    $reply_author_row = mysqli_fetch_array($reply_author_ret);
                    $reply_author_name = !empty($reply_author_row['uname']) ? htmlspecialchars($reply_author_row['uname'], ENT_QUOTES, 'UTF-8') : "未知作者";
                    $reply_author_avatar = !empty($reply_author_row['uimg']) ? htmlspecialchars($reply_author_row['uimg'], ENT_QUOTES, 'UTF-8') : "/images/web/default.jpg";
                    $reply_author_id = !empty($reply_author_row['uid']) ? intval($reply_author_row['uid']) : "0";
                }
                // 判断是否显示回复按钮：最大层级为0（不限制） 或 当前层级小于最大层级
                $show_reply_btn = ($max_level == 0) || ($current_level < $max_level);
                ?>
                <div class="reply-item" data-reply-id="<?php echo $reply_id; ?>">
                    <div class="reply-author">
                        <a href="/user.php?id=<?php echo $reply_author_id; ?>" target="_blank"><img src="<?php echo $reply_author_avatar; ?>" class="reply-avatar" alt="<?php echo $reply_author_name; ?>">
                        <span class="reply-username"><?php echo $reply_author_name; ?></span></a>
                        <span class="reply-time"><?php echo $reply_time; ?></span>
                    </div>
                    <div class="reply-content"><?php echo nl2br($reply_text); ?></div>
                    <div class="reply-actions nocopy">
                        <span class="reply-like like" date-id="<?php echo $reply_id; ?>"><i class="fa fa-thumbs-up"></i>点赞(<div class="likemun"><?php echo $reply_top; ?></div>)</span>
                        <?php if ($show_reply_btn): ?>
                        <span class="reply-reply-btn" data-reply-to="<?php echo $reply_id; ?>"><i class="fa fa-reply"></i>回复</span>
                        <?php endif; ?>
                    </div>
                    <div class="reply-form-wrap" id="reply-form-<?php echo $reply_id; ?>" style="display: none;">
                        <?php if (!empty($ppzusername)): ?>
                        <form class="reply-form position-r" data-parent-id="<?php echo $reply_id; ?>">
                            <textarea name="reply_text" class="reply-textarea" charset="UTF-8" autocomplete="off" spellcheck="false" class="reply-textarea" data-max-length="150" placeholder="请输入回复内容..."></textarea>
                            <input type="hidden" name="subid" value="<?php echo $article_id; ?>" />
                            <input type="hidden" name="parent_id" value="<?php echo $reply_id; ?>" />
                            <div>
                                <span class="reply-msg">剩余字数：150</span>
                                <button type="submit" class="reply-submit">回复</button>
                            </div>
                        </form>
                        <?php else: ?>
                        <div class="reply-login-tip">请登录后回复</div>
                        <?php endif; ?>
                    </div>
                    <?php 
                    // 递归调用时层级+1
                    renderReplies($conn, $reply_id, $article_id, $ppzusername, $current_level + 1); 
                    ?>
                </div>
                <?php
            }
            echo '</div>';
        }
    }
}

// 应用格式化函数
$article_views = "0";
if (!empty($article['eyes'])) {
    $article_views = format_views(intval($article['eyes']));
}

// 文章时间
$article_time = "未知时间";
if (!empty($article['time'])) {
    if (is_numeric($article['time'])) {
        $article_time = format_time(intval($article['time']));
    } else {
        $time_stamp = strtotime($article['time']);
        $article_time = $time_stamp ? format_time($time_stamp) : htmlspecialchars($article['time'], ENT_QUOTES, 'UTF-8');
    }
}

// 文章加精置顶字段（1=无，2=精选，3=置顶）
$top_label = '';
if (is_number($article['top'])) {
    $top_val = intval($article['top']);
    if ($top_val == 2) {
        $top_label = '<div class="top-label-row hot-row">精选</div>';
    } elseif ($top_val == 3) {
        $top_label = '<div class="top-label-row top-row">置顶</div>';
    }
}

// 编辑页的标签筛选参数逻辑
$sub_new_tag="";
if(isset($_GET['t']) && !empty($_GET['t']) && is_number($_GET['t'])){
    $sub_new_tag = intval(trim($_GET['t']));
}
$is_mine = 0;
if(isset($_GET['mine']) && !empty($_GET['mine']) && is_number($_GET['mine'])){
    $is_mine = intval(trim($_GET['mine']));
}
$search_key = '';
?>
<?php
    function get_ogscheme(){
        // 代理转发优先，兼容大小写
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            $proto = strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']);
            if ($proto === 'https') {
                return 'https';
            }
        }
        // 原生HTTPS兜底
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            return 'https';
        }
        return 'http';
}
    $og_scheme = get_ogscheme();//获取协议
    $og_host = $_SERVER['HTTP_HOST'];//获取主机名
    $og_uri  = $_SERVER['REQUEST_URI'];//获取请求URI
    $og_fullUrl = $og_scheme . '://' . $og_host . $og_uri;//获取完整URL

    function html_to_plain_substr($htmlx, $lengthx){
        // 移除script、style区块
        $htmlx = preg_replace('#<(script|style).*?>.*?</\\1>#is', '', $htmlx);
        $textx = strip_tags($htmlx);
        $textx = html_entity_decode($textx, ENT_QUOTES, 'UTF-8');
        // ========== 敏感内容过滤 ==========
        // 1. 密码相关内容过滤
        $pwdPattern = '/(解压码|提取码|密码|password).{0,8}/iu';
        $textx = preg_replace($pwdPattern, '******', $textx);
        // 2. 邮箱地址过滤（标准邮箱正则，支持中英文前后字符）
        $emailPattern = '/[a-zA-Z0-9_\-\.\+]+@[a-zA-Z0-9_\-\.]+\.[a-zA-Z]{2,}/iu';
        $textx = preg_replace($emailPattern, '******', $textx);
        // 3. URL拦截正则
        $pattern = '/(?:(?:https?|ftps?):\/\/|\/\/|www\.)[^\s，。！？；：""\'()（）、]+|[a-zA-Z0-9_-]+\.[a-zA-Z0-9_-]{1,}[^\s，。！？；：""\'()（）、]*/iu';
        $textx = preg_replace($pattern, '******', $textx);
        // 合并空白 + 首尾修剪
        $textx = preg_replace('/\s+/u', ' ', $textx);
        $textx = trim($textx);
        // 优先使用mb截取UTF8字符
        if (function_exists('mb_substr')) {
            $textx = mb_substr($textx, 0, $lengthx, 'UTF-8');
        } else {
            $textx = substr($textx, 0, $lengthx * 1);
        }
        // 截取完成后再次去除末尾空格
        $textx = trim($textx);
        return $textx . "……";
    }
    $og_image = '';
    $og_imgtype = 'image/jpeg';

    if (!empty($article['text'])) {
        if (preg_match('/<img[^>]+src=[\'"]?([^\'">]+)[\'"]?/i', $article['text'], $matches)) {
            $src = trim($matches[1]);
            if (substr($src, 0, 5) !== 'data:') {
                $og_image = $src;
            }
        }
    }

    if (!empty($og_image)) {
        if (!preg_match('/^https?:\/\//i', $og_image)) {
            $og_image = str_replace(['../', './', '\\'], '/', $og_image);
            $og_image = ltrim($og_image, '/');
            $og_image = $og_scheme . '://' . $og_host . '/' . $og_image;
        }
        $og_img_ext = strtolower(pathinfo($og_image, PATHINFO_EXTENSION));
        switch ($og_img_ext) {
            case 'webp':
                $og_imgtype = 'image/webp';
                break;
            case 'png':
                $og_imgtype = 'image/png';
                break;
            case 'avif':
                $og_imgtype = 'image/avif';
                break;
            case 'gif':
                $og_imgtype = 'image/gif';
                break;
            default:
                $og_imgtype = 'image/jpeg';
                break;
        }
    }

?>
<!DOCTYPE html>
<html lang="zh-CN" translate="no">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8');?> - <?php echo $webtext;?></title>
    <meta name="keywords" content="<?php echo $article_tag_name;?>" />
    <meta name="description" content="<?php echo html_to_plain_substr($article['text'], 120);?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta property="og:site_name" content="<?php echo $webtext;?>" />
    <meta property="og:title" content="<?php echo htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8');?>" />
    <meta property="og:description" content="<?php echo html_to_plain_substr($article['text'], 120);?>" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="<?php echo $og_fullUrl;?>" />
    <?php if (!empty($og_image)) { ?><meta property="og:image" content="<?php echo $og_image;?>" /><meta property="og:image:type" content="<?php echo $og_imgtype;?>" /><?php } ?>
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?php echo htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8');?>" />
    <meta name="twitter:description" content="<?php echo html_to_plain_substr($article['text'], 120);?>" />
    <?php if (!empty($og_image)) { ?><meta name="twitter:image" content="<?php echo $og_image;?>" /><?php } ?>
    <link rel="icon" href="/favicon.ico" />
    <link rel="canonical" href="<?php echo $og_fullUrl;?>" />
    <?php @include $_SERVER['DOCUMENT_ROOT'].'/inc/style.php';?>
    <link type="text/css" rel="stylesheet" href="/style/css/font-awesome-4.7.0/css/font-awesome.min.css" />
    <link type="text/css" rel="stylesheet" href="style.css" />
    <script src="/style/js/jquery-3.5.1.min.js" type="text/javascript"></script>
    <script src="/style/js/input.js" type="text/javascript"></script>
    <script src="/style/js/alert.js" type="text/javascript"></script>
    <script src="core.js" type="text/javascript"></script>
    <script src="md5.js" type="text/javascript"></script>
    <script type="text/javascript" src="emoji.js"></script>
</head>
<body>
    <?php @include $_SERVER['DOCUMENT_ROOT'].'/inc/header.php';//头部?>
    <div class="body-div">
        <?php if($set_off<1||empty($set_off)){ ?><div class="nullsub"><?php echo $set_title;?>功能暂未开启</div><?php }else{?>
            <div class="sub-div">

                <div class="sub-left">
                    <h3>筛选标签</h3>
                    <ul class="sub-list">
                        <?php
                            //获取全部标签
                            $alltag_sql="select sub_id,sub_name from ppz_subtype order by sub_id asc";
                            $alltag_retval=mysqli_query($conn,$alltag_sql);
                            $active_class = ($sub_new_tag <= 0 && empty($search_key)) ? 'activex' : '';
                            echo '<a href="/subject/" class="'.$active_class.'"><li>全部</li></a>';
                            if(!empty($ppzusername)){ // 仅登录用户显示关注
                                echo '<a href="/subject/?follow=1"><li>关注</li></a>';
                            }
                            if ($alltag_retval && mysqli_num_rows($alltag_retval) > 0){
                                while($alltag = mysqli_fetch_array($alltag_retval)){
                                    $list_tag_id=$alltag['sub_id']; // 列表标签ID
                                    $list_tag_name=$alltag['sub_name']; // 列表标签名称
                                    $active_class = ($list_tag_id == $sub_new_tag && empty($search_key)) ? 'activex' : '';
                                    echo '<a href="/subject/?t='.$list_tag_id.'" class="'.$active_class.'"><li>'.$list_tag_name.'</li></a>';
                                }
                            }
                        ?>
                    </ul>
<?php

// 仅登录用户显示发表/我的标签/评论按钮
if (!empty($ppzusername)){
    echo ' 
                    <div class="sub-but-div"><div class="sub-but-post"><a href="/subject/post.php"><i class="fa fa-paper-plane" aria-hidden="true"></i>发表'.$set_tag.'</a></div>
                    <div class="sub-but-set">
                    <a href="/subject/?mine=1"><i class="fa fa-bars" aria-hidden="true"></i>我的'.$set_tag.'</a>
';
if ($unread_count > 0) {
    if($unread_count>99){
        $unread_count='99+';
    }
?>
<a href="comment.php"><i class="fa fa-comments-o" aria-hidden="true"></i><?php echo $set_tag;?>评论<div class="sub-but-comm-new"><i class="fa fa-envelope-o" aria-hidden="true"></i><?php echo $unread_count; ?></div></a></div>
<?php } else { ?>
<a href="comment.php"><i class="fa fa-comments-o" aria-hidden="true"></i><?php echo $set_tag;?>评论</a></div>
<?php } ?>
<?php } ?>
                </div><?php if (!empty($ppzusername)){echo '</div>';}?>

                <div class="sub-right">
                    <div class="sub-header">
                        <h2><?php echo htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8');?></h2>
                        <a class="subgoback" data-url="/subject/"><i class="fa fa-reply-all" aria-hidden="true"></i>返回</a>
                    </div>
                    <div class="sub-content header-sub">
                        <?php echo $top_label;?>
                        <div class="article-info">
                            <span>
                                <i class="fa fa-tags" aria-hidden="true"></i>
                                标签：<a class="tagsa" href="/subject/?t=<?php echo $article_tag_id;?>"><?php echo $article_tag_name;?></a>
                            </span>
                            <span>
                                <i class="fa fa-clock-o" aria-hidden="true"></i>发表时间：<?php echo $article_time;?>
                            </span>
                            <span>
                                <i class="fa fa-user" aria-hidden="true"></i>作者：
                                <a class="sub-user-name" href="/user.php?id=<?php echo $author_uid;?>" target="_blank">
                                    <img src="<?php echo $author_avatar;?>" class="author-avatar" alt="<?php echo $author_name;?>">
                                    <?php echo $author_name;?>
                                </a>
                            </span>
                            <?php
                            // 查询文章下的评论数
                                $comment_count = 0;
                                if($article_id > 0){
                                    $safe_article_id = mysqli_real_escape_string($conn, $article_id);
                                    $sqlc = "SELECT COUNT(comm_subid) FROM ppz_subcomm WHERE comm_subid = '{$safe_article_id}'";
                                    $queryc = mysqli_query($conn, $sqlc);
                                    $comment_count = mysqli_fetch_array($queryc)[0];
                                }
                            ?>
                            <span>
                                <i class="fa fa-eye" aria-hidden="true"></i>阅览：<?php echo $article_views;?>
                            </span>
                            <span>
                                <i class="fa fa-commenting-o" aria-hidden="true"></i>评论：<?php echo format_views($comment_count);?>
                            </span>
                        </div>
                        <div id="imgrow" class="rowdiv subrow">
                            <?php echo htmlspecialchars_decode($article['text'], ENT_QUOTES);?>
<?php
// 处理引用功能：解析quote字段，区分话题({ID})和文章(ID)
$quote_list = [];
if (!empty($article['quote'])) {
    // 分割引用字符串（英文逗号分隔）
    $quote_ids = explode(',', trim($article['quote']));
    foreach ($quote_ids as $quote_id) {
        $quote_id = trim($quote_id);
        if (empty($quote_id)) continue;
        
        // 区分话题（带{}）和文章（纯数字）
        if (preg_match('/^\{(\d+)\}$/', $quote_id, $matches)) {
            // 话题引用：提取ID
            $topic_id = intval($matches[1]);
            if ($topic_id > 0) {
                $safe_topic_id = mysqli_real_escape_string($conn, $topic_id);
                // 查询话题信息
                if($unread_user_ustatus==4||$unread_user_ustatus==3||$unread_user_ustatus==2){
                    $topic_sql = "SELECT id, title, text FROM ppz_subject WHERE id = {$safe_topic_id} LIMIT 1";
                }else{
                    $topic_sql = "SELECT id, title, text FROM ppz_subject WHERE id = {$safe_topic_id} AND yes = 3 LIMIT 1";
                }                
                $topic_ret = mysqli_query($conn, $topic_sql);
                if ($topic_ret && mysqli_num_rows($topic_ret) > 0) {
                    $topic = mysqli_fetch_array($topic_ret);
                    // 截取纯文本简介（前40字）
                    $topic_text = strip_tags(isset($topic['text']) ? $topic['text'] : '');
                    $topic_desc = mb_substr(trim($topic_text), 0, 40, 'UTF-8');
                    $topic_desc = $topic_desc ?: '暂无话题简介';
                    
                    // 话题封面（优先取内容第一张图片，无则用默认）
                    $topic_cover = '/images/web/null.jpg';
                    $topic_text_content = isset($topic['text']) ? $topic['text'] : '';
                    if (preg_match('/<img.*?src=["\'](.*?)["\']/i', $topic_text_content, $img_matches)) {
                        $topic_cover = $img_matches[1];
                    }
                    
                    $quote_list[] = [
                        'type' => 'topic',
                        'id' => $topic_id,
                        'title' => htmlspecialchars(isset($topic['title']) ? $topic['title'] : '', ENT_QUOTES, 'UTF-8'),
                        'desc' => htmlspecialchars($topic_desc, ENT_QUOTES, 'UTF-8').'...',
                        'cover' => htmlspecialchars($topic_cover, ENT_QUOTES, 'UTF-8'),
                        'url' => "/subject/detail.php?id={$topic_id}"
                    ];
                }
            }
        } elseif (is_numeric($quote_id) && intval($quote_id) > 0) {
            // 文章引用：纯数字ID
            $article_row_id = intval($quote_id);
            $safe_article_id = mysqli_real_escape_string($conn, $article_row_id);
            // 查询文章基础信息
            $article_sql = "SELECT rowid, rowtexe, rowif, rowimg, rowyes, videotext FROM ppz_row WHERE rowid = {$safe_article_id} AND rowyes = 4 LIMIT 1";
            $article_ret = mysqli_query($conn, $article_sql);
            if ($article_ret && mysqli_num_rows($article_ret) > 0) {
                $row = mysqli_fetch_array($article_ret);
                $row_title = htmlspecialchars(isset($row['rowtexe']) ? $row['rowtexe'] : '', ENT_QUOTES, 'UTF-8');
                
                // 处理文章简介
                $row_desc = '';
                if (isset($row['rowif']) && $row['rowif'] == 1) {
                    // 1-图文：截取rowbigtext字段纯文本前40字
                    $content_sql = "SELECT rowbigtext FROM ppz_row WHERE rowid = {$safe_article_id} LIMIT 1";
                    $content_ret = mysqli_query($conn, $content_sql);
                    $content_row = mysqli_fetch_array($content_ret);
                    $row_text = strip_tags(isset($content_row['rowbigtext']) ? $content_row['rowbigtext'] : '');
                    $row_desc = mb_substr(trim($row_text), 0, 40, 'UTF-8');
                } else {
                    // 2-相册/3-视频：截取videotext纯文本前40字
                    $row_text = strip_tags(isset($row['videotext']) ? $row['videotext'] : '');
                    $row_desc = mb_substr(trim($row_text), 0, 40, 'UTF-8');
                }
                $row_desc = $row_desc ?: '暂无文章简介';
                $row_desc = htmlspecialchars($row_desc, ENT_QUOTES, 'UTF-8');
                
                // 处理文章封面
                $row_cover = '/images/web/null.jpg';
                if (!empty($row['rowimg'])) {
                    $row_cover = $row['rowimg'];
                } else {
                    if (isset($row['rowif']) && $row['rowif'] == 1) {
                        // 图文：从rowbigtext取内容第一张图片
                        $content_sql = "SELECT rowbigtext FROM ppz_row WHERE rowid = {$safe_article_id} LIMIT 1";
                        $content_ret = mysqli_query($conn, $content_sql);
                        $content_row = mysqli_fetch_array($content_ret);
                        $row_content = isset($content_row['rowbigtext']) ? $content_row['rowbigtext'] : '';
                        if (preg_match('/<img.*?src=["\'](.*?)["\']/i', $row_content, $img_matches)) {
                            $row_cover = $img_matches[1];
                        }
                    } elseif (isset($row['rowif']) && $row['rowif'] == 2) {
                        // 相册：rowbigtext是|分割的数组，取第一个作为封面
                        $content_sql = "SELECT rowbigtext FROM ppz_row WHERE rowid = {$safe_article_id} LIMIT 1";
                        $content_ret = mysqli_query($conn, $content_sql);
                        $content_row = mysqli_fetch_array($content_ret);
                        $album_content = isset($content_row['rowbigtext']) ? $content_row['rowbigtext'] : '';
                        $album_imgs = explode('|', $album_content);
                        if (!empty($album_imgs[0])) {
                            $row_cover = $album_imgs[0];
                        }
                    }
                }
                $row_cover = htmlspecialchars($row_cover, ENT_QUOTES, 'UTF-8');
                
                $quote_list[] = [
                    'type' => 'article',
                    'id' => $article_row_id,
                    'title' => $row_title,
                    'desc' => $row_desc.'...',
                    'cover' => $row_cover,
                    'url' => "/show.php?id={$article_row_id}"
                ];
            }
        }
    }
}

// 渲染引用列表
if (!empty($quote_list)) {
    echo '<div class="sub-quote">';
    foreach ($quote_list as $item) {
        echo '<a href="'.$item['url'].'" target="_blank">';
        echo '<img src="'.$item['cover'].'" alt="'.$item['title'].'">';
        echo '<div class="text">';
        echo '<div class="text-content">'.$item['title'].'</div>';
        echo '<span>'.$item['desc'].'</span>';
        echo '</div>';
        echo '</a>';
    }
    echo '</div>';
}
?>
                        </div>
                    </div>
                    <div class="header-sub sub-comment">
                        <div class="sub-header-comment"><?php echo format_views($comment_count);?>条评论</div>
                        <?php if(empty($ppzusername)):?>
                        <div class="sub-comment-login">
                            <a id="showModaladl2">登录</a>后发表评论
                        </div>
                        <?php else:?>
                        <form id="sub_comment" class="sub-comment-form">
                            <textarea name="comment" class="sub-comment-textarea" data-max-length="320" charset="UTF-8" autocomplete="off" spellcheck="false" placeholder="请输入评论..."></textarea>
                            <input type="hidden" name="subid" value="<?php echo $article_id;?>" />
                            <div class="sub-submit"><span id="sub_comment_msg">剩余字数：320</span><button type="submit" class="sub-comment-submit">评论</button></div>
                        </form>
                        <?php endif;?>


<div class="sub-comment-list"> 
    <?php
    $page = isset($_GET['cpage']) && is_number($_GET['cpage']) ? intval($_GET['cpage']) : 1;
    $page_size = 10; // 每页显示10条父评论
    $offset = ($page - 1) * $page_size;

    $safe_article_id = mysqli_real_escape_string($conn, $article_id);
    $total_sql = "SELECT COUNT(comm_id) AS total FROM ppz_subcomm WHERE comm_subid = '{$safe_article_id}' AND comm_type = 0";
    $total_ret = mysqli_query($conn, $total_sql);
    $total_row = mysqli_fetch_array($total_ret);
    $total_comments = intval($total_row['total']);
    $total_pages = ceil($total_comments / $page_size);

    $parent_sql = "SELECT comm_id, comm_admin, comm_text, comm_time, comm_top,
               LENGTH(comm_top) - LENGTH(REPLACE(comm_top, ',', '')) + 1 AS like_count
               FROM ppz_subcomm 
               WHERE comm_subid = '{$safe_article_id}' AND comm_type = 0
               ORDER BY IF(like_count IS NULL OR comm_top = '', 0, like_count) DESC, comm_id DESC 
               LIMIT {$offset}, {$page_size}";
    $parent_ret = mysqli_query($conn, $parent_sql);

    if (mysqli_num_rows($parent_ret) > 0) {
        while ($parent_comm = mysqli_fetch_array($parent_ret)) {
            $parent_comm_id = intval($parent_comm['comm_id']);
            $parent_admin_uid = intval($parent_comm['comm_admin']);
            $parent_comm_text = htmlspecialchars($parent_comm['comm_text'], ENT_QUOTES, 'UTF-8');
            $parent_comm_time = format_time(strtotime($parent_comm['comm_time']));
            $parent_comm_top = countLikes($parent_comm['comm_top']);
            $parent_author_name = "未知作者";
            $parent_author_avatar = "/images/web/default.jpg";
            $safe_parent_uid = mysqli_real_escape_string($conn, $parent_admin_uid);
            $parent_author_sql = "SELECT uname,uimg,uid FROM ppz_newusername WHERE uid = {$safe_parent_uid} LIMIT 1";
            $parent_author_ret = mysqli_query($conn, $parent_author_sql);
            if ($parent_author_ret && mysqli_num_rows($parent_author_ret) > 0) {
                $parent_author_row = mysqli_fetch_array($parent_author_ret);
                $parent_author_name = !empty($parent_author_row['uname']) ? htmlspecialchars($parent_author_row['uname'], ENT_QUOTES, 'UTF-8') : "未知作者";
                $parent_author_avatar = !empty($parent_author_row['uimg']) ? htmlspecialchars($parent_author_row['uimg'], ENT_QUOTES, 'UTF-8') : "/images/web/default.jpg";
                $parent_author_uid = !empty($parent_author_row['uid']) ? intval($parent_author_row['uid']) : "0";
            }
            // 父评论默认是第0层，回复从第1层开始计算
            $max_level = getMaxReplyLevel($conn);
            $parent_show_reply = ($max_level == 0) || ($max_level > 0); // 父评论始终显示回复按钮（只要有层级限制或无限制）
            ?>
            <div class="comment-item" data-comm-id="<?php echo $parent_comm_id; ?>">
                <div class="comment-author">
                    <a href="/user.php?id=<?php echo $parent_author_uid; ?>" target="_blank"><img src="<?php echo $parent_author_avatar; ?>" class="comment-avatar" alt="<?php echo $parent_author_name; ?>">
                    <span class="comment-username"><?php echo $parent_author_name; ?></span></a>
                    <span class="comment-time"><?php echo $parent_comm_time; ?></span>
                </div>
                <div class="comment-content"><?php echo nl2br($parent_comm_text); ?></div>
                <div class="comment-actions nocopy">
                    <span class="comment-like like" date-id="<?php echo $parent_comm_id; ?>"><i class="fa fa-thumbs-up"></i>点赞(<div class="likemun"><?php echo $parent_comm_top; ?></div>)</span>
                    <?php if ($parent_show_reply): ?>
                    <span class="comment-reply-btn" data-reply-to="<?php echo $parent_comm_id; ?>"><i class="fa fa-reply"></i>回复</span>
                    <?php endif; ?>
                </div>
                <div class="reply-form-wrap" id="reply-form-<?php echo $parent_comm_id; ?>" style="display: none;">
                    <?php if (!empty($ppzusername)): ?>
                    <form class="reply-form position-r" data-parent-id="<?php echo $parent_comm_id; ?>">
                        <textarea name="reply_text" charset="UTF-8" autocomplete="off" spellcheck="false" class="reply-textarea" data-max-length="150" placeholder="请输入回复内容..."></textarea>
                        <input type="hidden" name="subid" value="<?php echo $article_id; ?>" />
                        <input type="hidden" name="parent_id" value="<?php echo $parent_comm_id; ?>" />
                        <div>
                            <span class="reply-msg">剩余字数：150</span>
                            <button type="submit" class="reply-submit">回复</button>
                        </div>
                    </form>
                    <?php else: ?>
                    <div class="reply-login-tip">请登录后回复</div>
                    <?php endif; ?>
                </div>

                <?php
                    // 调用递归函数，初始层级为1（父评论是0层，第一层回复）
                    renderReplies($conn, $parent_comm_id, $article_id, $ppzusername, 1);
                ?>
            </div>
            <?php
        }
        // 分页控件
        if ($total_pages > 1) {
            echo '<div class="comment-pagination">';
            // 上一页
            if ($page > 1) {
                $prev_page = $page - 1;
                echo '<a href="?id='.$article_id.'&cpage='.$prev_page.(!empty($sub_new_tag) ? '&t='.$sub_new_tag : '').(!empty($is_mine) ? '&mine='.$is_mine : '').'" class="page-prev">上一页</a>';
            } else {
                echo '<span class="page-prev disabled">上一页</span>';
            }

            // 页码
            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i == $page) {
                    echo '<span class="page-num active">'.$i.'</span>';
                } else {
                    echo '<a href="?id='.$article_id.'&cpage='.$i.(!empty($sub_new_tag) ? '&t='.$sub_new_tag : '').(!empty($is_mine) ? '&mine='.$is_mine : '').'" class="page-num">'.$i.'</a>';
                }
            }

            // 下一页
            if ($page < $total_pages) {
                $next_page = $page + 1;
                echo '<a href="?id='.$article_id.'&cpage='.$next_page.(!empty($sub_new_tag) ? '&t='.$sub_new_tag : '').(!empty($is_mine) ? '&mine='.$is_mine : '').'" class="page-next">下一页</a>';
            } else {
                echo '<span class="page-next disabled">下一页</span>';
            }
            echo '</div>';
        }
    } else {
        // 无评论提示
        echo '<div class="no-comment">暂无评论，快来抢沙发吧！</div>';
    }
    ?>
</div>

<script type="text/javascript">

</script>

                    </div>                    
                </div>

            </div>
            <script src="goback.js" type="text/javascript"></script>
        <?php }?>
    </div>
    
    <link rel="stylesheet" href="/style/PhotoSwipe/viewer.min.css">
    <script src="/style/PhotoSwipe/viewer.min.js"></script>
    <script src="/style/js/lazy/jquery.lazyload.js"></script>
    <script src="/style/js/viewswitching.js"></script>
    <script type="text/javascript">var viewer = new Viewer(document.getElementById("imgrow"));</script>
    <?php @include $_SERVER['DOCUMENT_ROOT'].'/inc/footer.php';?>
    <?php if (empty($ppzusername)){
            echo '<script src="/style/js/login.js" type="text/javascript"></script>';
        }else{
            echo '<script src="comment.js" type="text/javascript"></script>';
        }
    ?>
    <?php @include $_SERVER['DOCUMENT_ROOT'].'/wap/index.php';?>
</body>
</html>