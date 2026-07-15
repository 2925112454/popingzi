<?php
include $_SERVER['DOCUMENT_ROOT'].'/inc/inc.php';//通用

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
// 阅览量和评论数格式化函数
function format_views($views) {
    $units = [
        100000000 => '亿',
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

// 获取标签筛选参数
$sub_new_tag="";
if(isset($_GET['t']) && !empty($_GET['t']) && is_number($_GET['t'])){
    $sub_new_tag = intval(trim($_GET['t']));
}

// 获取搜索关键词
$search_key = "";
if(isset($_GET['sub']) && !empty($_GET['sub'])){
    $search_key = $conn ? mysqli_real_escape_string($conn, trim($_GET['sub'])) : addslashes(trim($_GET['sub']));
}

// 获取"我的发布"筛选参数
$is_mine = 0;
if(isset($_GET['mine']) && !empty($_GET['mine']) && is_number($_GET['mine'])){
    $is_mine = intval(trim($_GET['mine']));
}

// 获取"关注"筛选参数
$is_follow = 0;
if(isset($_GET['follow']) && !empty($_GET['follow']) && is_number($_GET['follow'])){
    $is_follow = intval(trim($_GET['follow']));
}

// 获取分页参数
$page = 1;
if(isset($_GET['p']) && !empty($_GET['p']) && is_number($_GET['p'])){
    $page = intval(trim($_GET['p']));
}
$page_size = 10; // 每页显示10条
$offset = ($page - 1) * $page_size;

// 获取当前标签名称
$tag_title = "最新".$set_tag;
if($sub_new_tag > 0){
    $sub_new_tag_esc = mysqli_real_escape_string($conn, $sub_new_tag);
    $tagname_sql = "select sub_id,sub_name from ppz_subtype where sub_id = {$sub_new_tag_esc} limit 1";
    $tagname_retval = mysqli_query($conn,$tagname_sql);
    if ($tagname_retval && mysqli_num_rows($tagname_retval) > 0){
        $tagname = mysqli_fetch_array($tagname_retval);
        $tag_titlex = $tagname['sub_name'];
        $tag_title = empty($tag_titlex) ? "未知".$set_tag : $tag_titlex.$set_tag;
    }else{
        $tag_title = "未知标签";
        $sub_new_tag = "";
    }
}

// 处理"我的发布"标题显示
if($is_mine == 1 && !empty($ppzusername)){
    $tag_title = "我的".$set_tag;
}

// 处理"关注"筛选标题显示
if($is_follow == 1 && !empty($ppzusername)){
    $tag_title = "我关注的".$set_tag;
}

// ========== 审核状态查询条件 ==========
// 1. 先获取当前登录用户UID（全局复用）
$current_user_uid = 0;
if (!empty($ppzusername)) {
    $user_uid_sql = "SELECT uid FROM ppz_newusername WHERE uusername = '".mysqli_real_escape_string($conn, $ppzusername)."' LIMIT 1";
    $user_uid_ret = mysqli_query($conn, $user_uid_sql);
    if ($user_uid_ret && mysqli_num_rows($user_uid_ret) > 0) {
        $user_uid_row = mysqli_fetch_array($user_uid_ret);
        $current_user_uid = intval($user_uid_row['uid']);
    }
}

// 2. 构建基础查询条件
$where = "1=1";
// 审核状态条件（区分作者/非作者）
if ($current_user_uid > 0 && $is_mine == 1) {
    // 登录用户：自己的文章（不限审核状态） + 他人的审核通过文章
    $where .= " AND (yes = 3 OR admin = {$current_user_uid})";
} else {
    // 游客：仅能看审核通过的文章
    $where .= " AND yes = 3";
}

// 标签筛选
if($sub_new_tag > 0){
    $sub_new_tag_esc = mysqli_real_escape_string($conn, $sub_new_tag);
    $where .= " and type = {$sub_new_tag_esc}";
}

// 搜索关键词筛选
if(!empty($search_key)){
    $where .= " and (title like '%$search_key%' or text like '%$search_key%')";
}

// "我的发布"筛选（仅自己的文章）
if($is_mine == 1 && !empty($ppzusername) && $current_user_uid > 0){
    $where .= " and admin = {$current_user_uid}";
    // 我的发布：显示自己所有文章（无论审核状态），无需额外限制yes
}

// "关注"筛选（仅关注用户的审核通过文章）
if($is_follow == 1 && !empty($ppzusername) && $current_user_uid > 0){
    // 查询当前用户关注的所有用户ID
    $follow_sql = "SELECT usuename FROM ppz_folus WHERE usvip = {$current_user_uid}";
    $follow_ret = mysqli_query($conn, $follow_sql);
    $follow_uids = array();
    if($follow_ret && mysqli_num_rows($follow_ret) > 0){
        while($follow_row = mysqli_fetch_array($follow_ret)){
            $follow_uids[] = intval($follow_row['usuename']);
        }
    }
    // 如果有关注的用户，添加IN条件 + 审核通过；否则设置无结果
    if(!empty($follow_uids)){
        $follow_uids_str = implode(',', $follow_uids);
        $where .= " and admin IN ({$follow_uids_str}) AND yes = 3"; // 关注用户仅显示审核通过的
    }else{
        $where .= " and 1=2"; // 无关注用户时返回空结果
    }
}

// 查询文章总数（用于分页）
$total_sql = "select count(id) as total from ppz_subject where $where";
$total_retval = mysqli_query($conn,$total_sql);
$total = 0; // 默认值
if ($total_retval) {
    $total_row = mysqli_fetch_array($total_retval);
    $total = intval($total_row['total']);
}
$total_page = ceil($total / $page_size); // 总页数

// 查询当前页文章列表
$article_sql = "SELECT s.id, s.title, s.text, s.admin, s.type, s.time, s.top, s.eyes, s.yes,s.no,s.quote,
                u.uname AS author_name, u.uimg AS author_avatar, u.uviptime,
                t.sub_name AS type_name
                FROM ppz_subject s
                LEFT JOIN ppz_newusername u ON s.admin = u.uid
                LEFT JOIN ppz_subtype t ON s.type = t.sub_id
                WHERE $where
                ORDER BY 
                    CASE 
                        WHEN s.top = 3 THEN 0  -- 置顶文章优先
                        ELSE 1                 -- 其他文章排在后面
                    END,
                    s.id DESC                  -- 所有非置顶文章按ID倒序排列
                LIMIT $offset, $page_size";
$article_retval = mysqli_query($conn,$article_sql);
// 初始化空结果集（防止后续循环报错）
if (!$article_retval) $article_retval = false;

// 查询评论数（批量查询，减少数据库请求）
$comment_count = array();
if($total > 0){
    $comment_sql = "select comm_subid, count(comm_id) as count from ppz_subcomm group by comm_subid";
    $comment_retval = mysqli_query($conn,$comment_sql);
    if ($comment_retval) {
        while($comm_row = mysqli_fetch_array($comment_retval)){
            $comment_count[$comm_row['comm_subid']] = $comm_row['count'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN" translate="no">
<head>
    <meta charset="utf-8">
    <title><?php echo $set_title;?> - <?php echo $webtext;?>丨<?php echo $webby;?></title>
    <meta name="keywords" content="<?php echo $webpass;?>" />
    <meta name="description" content="<?php echo $webvar;?>" />
    <link rel="icon" href="/favicon.ico"/>
    <?php @include $_SERVER['DOCUMENT_ROOT'].'/inc/style.php';?>
    <link type="text/css" rel="stylesheet" href="/style/css/font-awesome-4.7.0/css/font-awesome.min.css">
    <link type="text/css" rel="stylesheet" href="style.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="/style/js/jquery-3.5.1.min.js" type="text/javascript"></script>
    <script src="/style/js/input.js" type="text/javascript"></script>
    <script src="/style/js/alert.js" type="text/javascript"></script>
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
                            // 显示"全部"选项
                            $active_class = ($sub_new_tag <= 0 && empty($search_key) && $is_mine == 0 && $is_follow == 0) ? 'activex' : '';
                            echo '<a href="/subject/" class="'.$active_class.'"><li>全部</li></a>';
                            if(!empty($ppzusername)){
                                // 显示"关注"选项（仅登录用户可见）
                                $active_class = ($is_follow == 1 && empty($search_key) && $is_mine == 0) ? 'activex' : '';
                                echo '<a href="/subject/?follow=1" class="'.$active_class.'"><li>关注</li></a>';
                            }
                            if ($alltag_retval && mysqli_num_rows($alltag_retval) > 0){
                                while($alltag = mysqli_fetch_array($alltag_retval)){
                                    $tag_id=$alltag['sub_id'];
                                    $tag_name=$alltag['sub_name'];
                                    $active_class = ($tag_id == $sub_new_tag && empty($search_key) && $is_mine == 0 && $is_follow == 0) ? 'activex' : '';
                                    echo '<a href="/subject/?t='.$tag_id.'" class="'.$active_class.'"><li>'.$tag_name.'</li></a>';
                                }
                            }
                        ?>
                    </ul>
<?php
if (!empty($ppzusername)){
    echo ' 
                    <div class="sub-but-div"><div class="sub-but-post"><a href="/subject/post.php"><i class="fa fa-paper-plane" aria-hidden="true"></i>发表'.$set_tag.'</a></div>
                    <div class="sub-but-set">
                    <a href="?mine=1"><i class="fa fa-bars" aria-hidden="true"></i>我的'.$set_tag.'</a>
';
// 显示未读回复提示
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
                        <h2><?php echo $tag_title;?></h2>
                        <form method="GET" class="sub-search-form">
                            <?php if($sub_new_tag > 0): ?>
                                <input type="hidden" name="t" value="<?php echo $sub_new_tag;?>">
                            <?php endif; ?>
                            <?php if($is_mine == 1): ?>
                                <input type="hidden" name="mine" value="1">
                            <?php endif; ?>
                            <?php if($is_follow == 1): ?>
                                <input type="hidden" name="follow" value="1">
                            <?php endif; ?>
                            <input type="text" name="sub" placeholder="搜索<?php echo $set_title;?>..." value="<?php echo htmlspecialchars($search_key);?>">
                            <button type="submit" class="btn-secondary">搜索</button>
                        </form>
                    </div>
                    <div class="sub-content">

                        <?php if($article_retval && mysqli_num_rows($article_retval) > 0): ?>
                            <?php while($article = mysqli_fetch_array($article_retval)): ?>
                                <?php
                                    // 处理作者头像
                                    $avatar = !empty($article['author_avatar']) ? $article['author_avatar'] : '/images/web/default.jpg';
                                    // 处理作者id
                                    $author_id = !empty($article['admin']) ? $article['admin'] : 0;

                                    // 处理内容摘要（纯文字、截取前180字符）
                                    $content = $article['text'];
                                    // 1. 先把 HTML 实体（&ldquo; &rdquo; 等）还原成正常符号
                                    $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
                                    // 2. 去除 HTML 标签
                                    $content = strip_tags($content);
                                    // 3. 截取 180 个中文字符（UTF-8安全）
                                    $content = mb_substr(trim($content), 0, 180, 'UTF-8');
                                    
                                    // 格式化发布时间
                                    $publish_time = format_time(strtotime($article['time']));
                                    // 评论数
                                    $comm_count = isset($comment_count[$article['id']]) ? $comment_count[$article['id']] : 0;
                                    // 分类名称
                                    $type_name = empty($article['type_name']) ? '未知标签' : $article['type_name'];
                                    //分类id
                                    $type_id = !empty($article['type']) ? $article['type'] : 0;

                                    $author_name = !empty($article['author_name']) ? $article['author_name'] : '未知用户';

                                    // 原IP判断逻辑：
                                    $author_is_vip_expired = true; // 默认VIP会员已过期
                                    $vip_icon = '';// 会员图标(默认为空)
                                    if (!empty($article['uviptime'])) {
                                        $vip_expire_time = strtotime($article['uviptime']);
                                        if ($vip_expire_time !== false && $vip_expire_time > time()) {
                                            $author_is_vip_expired = false;
                                        }
                                    }
                                    if(!$author_is_vip_expired){
                                        $vip_icon = '<div class="vipimg vip7"></div>';
                                    }
                                    
                                    
                                    // 判断当前登录用户是否是文章作者
                                    $is_article_author = false;
                                    if ($current_user_uid > 0 && $current_user_uid == intval($article['admin'])) {
                                        $is_article_author = true;
                                    }
                                    
                                    // 判断 top 值并添加对应的标签
                                    $top_label = '';
                                    if ($article['top'] == 3) {
                                        $top_label = '<div class="top-label">置顶</div>';
                                    } elseif ($article['top'] == 2) {
                                        $top_label = '<div class="hot-label">精选</div>';
                                    }

                                    // ========== 未审核文章的标识 ==========
                                    $review_label = '';
                                    $review_labelyes=true;
                                    if ($is_article_author) {
                                        if($article['yes'] == 1){
                                            $review_label = '<div class="unreviewed-label wait nocopy">等待审核</div>';
                                            $review_labelyes=false;
                                        }elseif($article['yes'] == 2){
                                            $review_labelyes=false;
                                            if(empty($article['no'])){
                                                $subno= '很遗憾，你的'.$set_tag.'没有通过审核，具体原因请联系管理员进行了解。';
                                            }else{
                                                $subno = trim(preg_replace("/\s(?=\s)/", "", $article['no']));
                                            }
                                            $review_label = '<div class="unreviewed-label violate nocopy" data-text="'.$subno.'">内容违规<i class="fa fa-exclamation-circle" aria-hidden="true"></i></div>';
                                        }else{
                                            $review_label = '<div class="unreviewed-label adopt nocopy">审核通过</div>';
                                        }
                                    }

                                    if($review_labelyes){
                                        $openclick="openclick nocopy";
                                    }else{
                                        $openclick="nocopy";
                                    }
                                ?>
                                <div class="post-card">
                                    <?php echo $top_label; ?>
                                    <div class="post-card-header">
                                        <div class="post-card-img newopenclick" data-href="/user.php?id=<?php echo $author_id;?>" style="background-image: url(<?php echo $avatar;?>);"><?php echo $vip_icon;?></div>
                                        <div class="post-card-text">
                                            <div class="post-card-name">
                                                <div class="newopenclick" data-href="/user.php?id=<?php echo $author_id;?>"><?php echo htmlspecialchars($author_name);?></div>
                                                <?php echo $review_label;?>
                                                <?php if (!$is_article_author && !empty($ppzusername)):?>
                                                    <?php
                                                        //判断是否已经关注
                                                        $is_followed = false;
                                                        if ($current_user_uid > 0 && $author_id > 0) {
                                                            $follow_uid_esc = mysqli_real_escape_string($conn, $current_user_uid);
                                                            $author_id_esc = mysqli_real_escape_string($conn, $author_id);
                                                            $check_follow_sql = "SELECT 1 FROM ppz_folus WHERE usvip = {$follow_uid_esc} AND usuename = {$author_id_esc} LIMIT 1";
                                                            $check_follow_ret = mysqli_query($conn, $check_follow_sql);
                                                            if ($check_follow_ret && mysqli_num_rows($check_follow_ret) > 0) {
                                                                $is_followed = true;
                                                            }
                                                            if ($check_follow_ret) {
                                                                mysqli_free_result($check_follow_ret);
                                                            }
                                                        }
                                                        echo $is_followed ? '<span class="subuser_post nocopy subfollowed" data-uuid="'.$author_id.'"><i class="fa fa-check" aria-hidden="true"></i>已关注</span>' : '<span class="subuser_post nocopy" data-uuid="'.$author_id.'"><i class="fa fa-plus" aria-hidden="true"></i>关注</span>';
                                                    ?>
                                                <?php endif;?>
                                            </div>
                                            <div class="post-card-time"><?php echo $publish_time;?></div>
                                        </div>
                                    </div>
                                    <h3 class="post-card-title <?php echo $openclick;?>" data-href="/subject/detail.php?id=<?php echo $article['id'];?>"><?php echo htmlspecialchars($article['title']);?></h3>
                                    <div class="post-card-content <?php echo $openclick;?>" data-href="/subject/detail.php?id=<?php echo $article['id'];?>"><?php echo htmlspecialchars($content);?></div>
                                    <?php
                                        // 初始化DOM解析器
                                        $dom = new DOMDocument();
                                        libxml_use_internal_errors(true);
                                        $dom->loadHTML(mb_convert_encoding($article['text'], 'HTML-ENTITIES', 'UTF-8'));
                                        libxml_clear_errors();

                                        // 获取所有img标签
                                        $imgTags = $dom->getElementsByTagName('img');
                                        $images = [];
                                        foreach ($imgTags as $img) {
                                            $src = $img->getAttribute('src');
                                            if (!empty($src)) {
                                                $images[] = $src;
                                            }
                                        }
                                        // 输出最多3张图片
                                        if (!empty($images)) {
                                            $displayImages = array_slice($images, 0, 3);
                                            echo '<div class="sub_image '.$openclick.'" data-href="/subject/detail.php?id='.$article['id'].'">';
                                            foreach ($displayImages as $imgSrc) {
                                                echo '<img src="' . htmlspecialchars($imgSrc) . '"  alt="'.htmlspecialchars($article['title']).'" />';
                                            }
                                            echo '</div>';
                                        }
                                    ?>
<?php
// ===== 解析引用内容开始 =====
$quote_text = ""; // 默认文本置空
$quote_count = 0; // 有效引用数量
$first_quote_title = ""; // 第一个有效引用标题

// 1. 获取当前话题的引用字段并处理
$quote_str = !empty($article['quote']) ? trim($article['quote']) : "";
if (!empty($quote_str)) {
    $quote_items = explode(',', $quote_str); // 分割引用ID
    $valid_quotes = array(); // 存储有效引用（文章/话题）
    
    // 2. 遍历所有引用项，验证有效性
    foreach ($quote_items as $item) {
        $item = trim($item);
        if (empty($item)) continue;
        
        // 区分话题ID（带{}）和文章ID（纯数字）
        if (strpos($item, '{') === 0 && strpos($item, '}') === strlen($item)-1) {
            // 处理话题ID
            $topic_id = intval(str_replace(array('{', '}'), '', $item));
            if ($topic_id <= 0) continue;
            
            // 查询话题是否存在且审核通过
            $topic_sql = "SELECT title FROM ppz_subject WHERE id = {$topic_id} AND yes = 3 LIMIT 1";
            $topic_ret = mysqli_query($conn, $topic_sql);
            if ($topic_ret && mysqli_num_rows($topic_ret) > 0) {
                $topic_row = mysqli_fetch_array($topic_ret);
                $valid_quotes[] = $topic_row['title'];
                mysqli_free_result($topic_ret);
            }
        } else {
            // 处理文章ID
            $article_id = intval($item);
            if ($article_id <= 0) continue;
            
            // 查询文章是否存在且审核通过
            $article_sql = "SELECT rowtexe FROM ppz_row WHERE rowid = {$article_id} AND rowyes = 4 LIMIT 1";
            $article_ret = mysqli_query($conn, $article_sql);
            if ($article_ret && mysqli_num_rows($article_ret) > 0) {
                $article_row = mysqli_fetch_array($article_ret);
                $valid_quotes[] = $article_row['rowtexe'];
                mysqli_free_result($article_ret);
            }
        }
    }
    
    // 3. 格式化引用文本（仅当有有效引用时赋值）
    $quote_count = count($valid_quotes);
    if ($quote_count > 0) {
        $first_quote_title = htmlspecialchars($valid_quotes[0]); // 第一个有效引用标题
        if (mb_strlen($first_quote_title, 'utf-8') > 24) {
            $first_quote_title = mb_substr($first_quote_title, 0, 24, 'utf-8')."...";
        }        
        if ($quote_count == 1) {
            $quote_text = "该".$set_tag."引用了《{$first_quote_title}》共1个内容";
        } else {
            $quote_text = "该".$set_tag."引用了《{$first_quote_title}》等{$quote_count}个有效内容";
        }
    }
}
// ===== 解析引用内容结束 =====

// 仅当有有效引用时，才输出div结构
if (!empty($quote_text)) {
?>
<div class="post-card-quote-div"><?php echo $quote_text;?></div>
<?php } ?>
                                    <div class="post-card-tag-div">
                                        <div class="post-card-tag openclick nocopy" data-href="/subject/?t=<?php echo $type_id;?>"><?php echo $type_name;?></div>
                                        <div class="post-card-tag-r">
                                            <div class="post-card-tag-eye"><i class="fa fa-eye" aria-hidden="true"></i><?php echo format_views($article['eyes']);?></div>
                                            <div class="post-card-tag-eye"><i class="fa fa-commenting-o" aria-hidden="true"></i><?php echo format_views($comm_count);?></div>
                                        </div>
                                    </div>
                                    
                                    <?php if ($is_article_author):?>
                                        <div class="post-card-edit">
                                            <a class="sub-edit" href="/subject/edit.php?id=<?php echo $article['id'];?>">编辑</a>
                                            <a class="sub-del" sub-id="<?php echo $article['id'];?>">删除</a>
                                        </div>
                                    <?php endif;?>
                                </div>
                                <script>
                                    //当前窗口打开
                                    document.querySelectorAll('.openclick[data-href]').forEach(function(card) {
                                        card.style.cursor = 'pointer';
                                        card.addEventListener('click', function(e) {
                                           window.location.href = this.getAttribute('data-href');
                                        });
                                    });
                                    //新窗口打开
                                    document.querySelectorAll('.newopenclick[data-href]').forEach(function(ncard) {
                                        ncard.style.cursor = 'pointer';
                                        ncard.addEventListener('click', function(e) {
                                           window.open(this.getAttribute('data-href'));
                                        });
                                    });
                                </script>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="nullsub">暂无<?php echo $set_tag;?>内容</div>
                        <?php endif; ?>

                    </div>
                    <div class="sub-page page">
                        <div class="page-left">
                            第<?php echo $page;?>页 / 共<?php echo $total_page;?>页
                            <div class="tpage">
                                <form name="page" onsubmit="return checkformpage()" method="get">
                                    <?php if($sub_new_tag > 0): ?>
                                        <input type="hidden" name="t" value="<?php echo $sub_new_tag;?>">
                                    <?php endif; ?>
                                    <?php if(!empty($search_key)): ?>
                                        <input type="hidden" name="sub" value="<?php echo htmlspecialchars($search_key);?>">
                                    <?php endif; ?>
                                    <?php if($is_mine == 1): ?>
                                        <input type="hidden" name="mine" value="1">
                                    <?php endif; ?>
                                    <?php if($is_follow == 1): ?>
                                        <input type="hidden" name="follow" value="1">
                                    <?php endif; ?>
                                    <input name="p" placeholder="跳页">
                                </form>
                            </div>
                        </div>
                        <div class="page-right">
                            <?php if($page == 1 || $total_page <= 1): ?>
                                <a class="page-no-button nocopy">首页</a>
                            <?php else: ?>
                                <a href="?p=1<?php echo $sub_new_tag > 0 ? '&t='.$sub_new_tag : '';?><?php echo !empty($search_key) ? '&sub='.urlencode($search_key) : '';?><?php echo $is_mine == 1 ? '&mine=1' : '';?><?php echo $is_follow == 1 ? '&follow=1' : '';?>" class="page-button nocopy">首页</a>
                            <?php endif; ?>
                            <?php if($page > 1 && $total_page > 1): ?>
                                <a href="?p=<?php echo $page-1;?><?php echo $sub_new_tag > 0 ? '&t='.$sub_new_tag : '';?><?php echo !empty($search_key) ? '&sub='.urlencode($search_key) : '';?><?php echo $is_mine == 1 ? '&mine=1' : '';?><?php echo $is_follow == 1 ? '&follow=1' : '';?>" class="page-button nocopy">上一页</a>
                            <?php else: ?>
                                <a class="page-no-button nocopy">上一页</a>
                            <?php endif; ?>
                            <?php if($page < $total_page && $total_page > 1): ?>
                                <a href="?p=<?php echo $page+1;?><?php echo $sub_new_tag > 0 ? '&t='.$sub_new_tag : '';?><?php echo !empty($search_key) ? '&sub='.urlencode($search_key) : '';?><?php echo $is_mine == 1 ? '&mine=1' : '';?><?php echo $is_follow == 1 ? '&follow=1' : '';?>" class="page-button nocopy">下一页</a>
                            <?php else: ?>
                                <a class="page-no-button nocopy">下一页</a>
                            <?php endif; ?>
                            <?php if($page == $total_page || $total_page <= 1): ?>
                                <a class="page-no-button nocopy">尾页</a>
                            <?php else: ?>
                                <a href="?p=<?php echo $total_page;?><?php echo $sub_new_tag > 0 ? '&t='.$sub_new_tag : '';?><?php echo !empty($search_key) ? '&sub='.urlencode($search_key) : '';?><?php echo $is_mine == 1 ? '&mine=1' : '';?><?php echo $is_follow == 1 ? '&follow=1' : '';?>" class="page-button nocopy">尾页</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php }?>
    </div>
    <?php @include $_SERVER['DOCUMENT_ROOT'].'/inc/footer.php';?>
    <?php if (empty($ppzusername)){ echo '<script src="/style/js/login.js" type="text/javascript"></script>';}?>
    <?php @include $_SERVER['DOCUMENT_ROOT'].'/wap/index.php';?>
    <script>
        function checkformpage() {
            var p = document.forms['page']['p'].value;
            if(!/^[1-9]\d*$/.test(p)){
                alert('<font>(ô‿ô)</font> 请输入有效的页码');
                return false;
            }
            if(parseInt(p) > <?php echo $total_page;?>){
                alert('<font>(ô‿ô)</font> 页码不能超过总页数');
                return false;
            }
            return true;
        }
    </script>
<?php if (!empty($ppzusername)):?><script src="followed.js" type="text/javascript"></script><script src="subdel.js" type="text/javascript"></script><dialog id="subdialog"></dialog><?php endif;?>
</body>
</html>