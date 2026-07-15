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

// 获取标签筛选参数
$sub_new_tag="";
if(isset($_GET['t']) && !empty($_GET['t']) && is_number($_GET['t'])){
    $sub_new_tag = intval(trim($_GET['t']));
}

// 获取"我的发布"筛选参数
$is_mine = 0;
if(isset($_GET['mine']) && !empty($_GET['mine']) && is_number($_GET['mine'])){
    $is_mine = intval(trim($_GET['mine']));
}

// 管理我的评论开关
$manage_my_comm = isset($_GET['manage']) && is_number($_GET['manage']) ? intval($_GET['manage']) : 0;

// 分页参数
$page = isset($_GET['page']) && is_number($_GET['page']) ? intval($_GET['page']) : 1;
$page_size = 10;//每页显示的条数
$offset = ($page - 1) * $page_size;

// 1. 获取当前登录用户的UID
$user_uid = 0;
if (!empty($ppzusername)) {
    $user_uid_sql = "SELECT uid FROM ppz_newusername WHERE uusername = '".mysqli_real_escape_string($conn, $ppzusername)."' LIMIT 1";
    $user_uid_ret = mysqli_query($conn, $user_uid_sql);
    if ($user_uid_ret && mysqli_num_rows($user_uid_ret) > 0) {
        $user_uid_row = mysqli_fetch_array($user_uid_ret);
        $user_uid = intval($user_uid_row['uid']);
    }
}

// 精准追溯任意层级回复的根评论（你的评论）
function get_root_comment_id($conn, $comm_id) {
    $current_id = intval($comm_id);
    // 循环向上追溯，直到找到根评论
    while (true) {
        $sql = "SELECT comm_type, comm_admin FROM ppz_subcomm WHERE comm_id = {$current_id} LIMIT 1";
        $ret = mysqli_query($conn, $sql);
        if (!$ret || mysqli_num_rows($ret) == 0) {
            break;
        }
        $row = mysqli_fetch_array($ret);
        $parent_id = intval($row['comm_type']);
        $comm_admin = intval($row['comm_admin']);
        
        // 如果父ID为0（根评论） 或者 该评论是当前用户发布的，停止追溯
        if ($parent_id == 0 || $comm_admin == $GLOBALS['user_uid']) {
            break;
        }
        $current_id = $parent_id;
        mysqli_free_result($ret);
    }
    return $current_id;
}

// 根据评论ID获取评论内容
function get_comment_content($conn, $comm_id) {
    $comm_id = intval($comm_id);
    $sql = "SELECT comm_text FROM ppz_subcomm WHERE comm_id = {$comm_id} LIMIT 1";
    $ret = mysqli_query($conn, $sql);
    if ($ret && mysqli_num_rows($ret) > 0) {
        $row = mysqli_fetch_array($ret);
        $content = trim($row['comm_text']);
        mysqli_free_result($ret);
        return $content;
    }
    return '';
}
// 根据评论ID获取回复数量
function get_comment_reply_count($conn, $comm_id) {
    $comm_id = intval($comm_id);
    $sql = "SELECT COUNT(comm_id) AS count FROM ppz_subcomm WHERE comm_type = {$comm_id}";
    $ret = mysqli_query($conn, $sql);
    if ($ret && mysqli_num_rows($ret) > 0) {
        $row = mysqli_fetch_array($ret);
        $count = intval($row['count']);
        mysqli_free_result($ret);
        return $count;
    }
    return 0;
}

//计算点赞数量（处理逗号分割的数组）
function get_like_count($like_str) {
    if (empty($like_str) || $like_str == ',') {
        return 0;
    }
    $like_arr = explode(',', trim($like_str, ','));
    $like_arr = array_filter($like_arr, 'is_numeric'); // 过滤非数字项
    return count($like_arr);
}

// 格式化时间显示
function format_comment_time($time_str) {
    if (empty($time_str)) {
        return '未知时间';
    }
    $time = strtotime($time_str);
    if (!$time) {
        return '未知时间';
    }
    return date('Y-m-d H:i:s', $time);
}
?>
<!DOCTYPE html>
<html lang="zh-CN" translate="no">
<head>
    <meta charset="utf-8">
    <title>我的<?php echo $set_title;?>评论 - <?php echo $webtext;?>丨<?php echo $webby;?></title>
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
        <?php if($set_off<1||empty($set_off)||empty($ppzusername)){ ?><div class="nullsub"><?php echo $set_title;?>功能暂未开启或操作错误</div><?php }else{?>
            <div class="sub-div">
                <div class="sub-left">
                    <h3>筛选标签</h3>
                    <ul class="sub-list">
                        <?php
                            //获取全部标签
                            $alltag_sql="select sub_id,sub_name from ppz_subtype order by sub_id asc";
                            $alltag_retval=mysqli_query($conn,$alltag_sql);
                            // 显示"全部"选项
                            $active_class = ($sub_new_tag <= 0 && empty($search_key)) ? 'activex' : '';
                            echo '<a href="/subject/" class="'.$active_class.'"><li>全部</li></a>';
                            if(!empty($ppzusername)){
                                echo '<a href="/subject/?follow=1"><li>关注</li></a>';
                            }
                            if ($alltag_retval && mysqli_num_rows($alltag_retval) > 0){
                                while($alltag = mysqli_fetch_array($alltag_retval)){
                                    $tag_id=$alltag['sub_id'];
                                    $tag_name=$alltag['sub_name'];
                                    $active_class = ($tag_id == $sub_new_tag && empty($search_key)) ? 'activex' : '';
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
<?php
if ($user_uid > 0) {
    if ($manage_my_comm == 1) {
        // 管理我的评论：仅显示当前用户发布的评论
        $total_sql = "SELECT COUNT(comm_id) AS total FROM ppz_subcomm WHERE comm_admin = {$user_uid}";
        $commsql = "SELECT 
                sc.*,
                s.title AS subject_title,
                s.yes AS subject_status,
                u.uname AS sender_name,
                u.uimg AS sender_avatar
            FROM 
                ppz_subcomm sc
            LEFT JOIN 
                ppz_subject s ON sc.comm_subid = s.id
            LEFT JOIN 
                ppz_newusername u ON sc.comm_admin = u.uid
            WHERE 
                sc.comm_admin = {$user_uid}
            ORDER BY 
                sc.comm_id DESC
            LIMIT {$offset}, {$page_size}";
    } else {
        // 消息列表：仅显示 直接回复你的评论 + 评论你的文章
        // 1. 获取直接回复你评论的回复ID
        $direct_reply_ids = get_direct_reply_to_me_ids($conn, $user_uid);
        $direct_reply_ids_str = !empty($direct_reply_ids) ? implode(',', $direct_reply_ids) : '-1';
        
        // 2. 总数量查询
        $total_sql = "SELECT COUNT(sc.comm_id) AS total 
            FROM ppz_subcomm sc
            LEFT JOIN ppz_subject s ON sc.comm_subid = s.id
            WHERE 
                -- 条件1：直接回复你的评论
                (sc.comm_id IN ({$direct_reply_ids_str}) AND sc.comm_admin != {$user_uid})
                OR 
                -- 条件2：别人评论你的文章
                (sc.comm_type = 0 
                AND sc.comm_subid > 0 
                AND s.id IS NOT NULL 
                AND s.yes = 3 
                AND s.admin = {$user_uid} 
                AND sc.comm_admin != {$user_uid})";

        // 3. 分页查询SQL
        $commsql = "SELECT 
                sc.*,
                s.title AS subject_title,
                s.admin AS subject_admin,
                s.yes AS subject_status,
                u.uname AS sender_name,
                u.uimg AS sender_avatar,
                parent_sc.comm_text AS parent_comm_text,
                parent_sc.comm_admin AS parent_comm_admin
            FROM ppz_subcomm sc
            LEFT JOIN ppz_subject s ON sc.comm_subid = s.id
            LEFT JOIN ppz_newusername u ON sc.comm_admin = u.uid
            LEFT JOIN ppz_subcomm parent_sc ON sc.comm_type = parent_sc.comm_id
            WHERE 
                (sc.comm_id IN ({$direct_reply_ids_str}) AND sc.comm_admin != {$user_uid})
                OR 
                (sc.comm_type = 0 AND s.id IS NOT NULL AND s.yes = 3 AND s.admin = {$user_uid} AND sc.comm_admin != {$user_uid})
            ORDER BY 
                sc.comm_id DESC
            LIMIT {$offset}, {$page_size}";
    }
    // 执行总数量查询
    $total_ret = mysqli_query($conn, $total_sql);
    $total = 0;
    if ($total_ret && mysqli_num_rows($total_ret) > 0) {
        $total_row = mysqli_fetch_array($total_ret);
        $total = intval($total_row['total']);
    }

    // 未读数量
    $unread_total = 0;
    if ($manage_my_comm == 0) {
        $unread_sql = str_replace('COUNT(sc.comm_id) AS total', 'COUNT(sc.comm_id) AS unread_num', $total_sql);
        $unread_sql = str_replace('WHERE', 'WHERE (sc.comm_yes = 0) AND (', $unread_sql);
        if (substr($unread_sql, -1) === ')') {
            $unread_sql = substr($unread_sql, 0, -1) . '))';
        }
        $unread_ret = mysqli_query($conn, $unread_sql);
        if ($unread_ret && mysqli_num_rows($unread_ret) > 0) {
            $unread_row = mysqli_fetch_array($unread_ret);
            $unread_total = intval($unread_row['unread_num']);
        }
    }

    // 执行分页数据查询
    $userComments = array();
    $commresult = mysqli_query($conn, $commsql);
    if ($commresult && mysqli_num_rows($commresult) > 0) {
        while ($commrow = mysqli_fetch_assoc($commresult)) {
            $userComments[] = $commrow;
        }
        mysqli_free_result($commresult);
    }
}
?>
                <div class="sub-right">
                    <div class="sub-header">
                        <h2>
                            <?php if($manage_my_comm == 1): ?>
                                我的评论[<?php echo $total;?>]
                            <?php else: ?>
                                消息列表[<?php echo $total;?>]
                                <?php if($unread_total > 0): ?>
                                    <a class="sub-comm-delc" id="allsubcomm"><i class="fa fa-bell-slash-o" aria-hidden="true"></i>清除未读(<?php echo $unread_total;?>)</a>
                                <?php endif;?>
                            <?php endif; ?>
                        </h2>
                        <a href="comment.php?manage=<?php echo $manage_my_comm == 1 ? 0 : 1;?>">
                            <i class="fa fa-cog" aria-hidden="true"></i>
                            <?php echo $manage_my_comm == 1 ? '返回消息列表' : '管理我的评论';?>
                        </a>
                    </div>
                    
                    <div class="sub-content">
                        <?php if(!empty($userComments)):?>
                            <?php foreach ($userComments as $comment): ?>
                                <?php
                                    $is_valid = false;
                                    if ($manage_my_comm == 0) {
                                        // 消息列表模式：验证是否是直接回复你的评论 或 评论你的文章
                                        if (!empty($comment['parent_comm_admin']) && $comment['parent_comm_admin'] == $user_uid) {
                                            // 直接回复你的评论
                                            $is_valid = true;
                                        } else if ($comment['comm_type'] == 0 && $comment['subject_admin'] == $user_uid) {
                                            // 评论你的文章
                                            $is_valid = true;
                                        }
                                    } else {
                                        // 管理我的评论：评论作者是当前用户
                                        $is_valid = ($comment['comm_admin'] == $user_uid);
                                    }
                                    if (!$is_valid) continue;
                                ?>
                                <?php
                                // 1. 消息类型文案（精准显示你被回复的评论内容）
                                if ($manage_my_comm == 1) {
                                    // 管理我的评论：显示来源+文章标题
                                    $subject_title = !empty($comment['subject_title']) ? "《".htmlspecialchars($comment['subject_title'])."》" : '【'.$set_tag.'不存在或已被删除】';
                                    $comment_type = "源自".$set_tag."：{$subject_title}";
                                    
                                    // 计算点赞数量、回复数量、格式化评论时间
                                    $like_count = get_like_count($comment['comm_top']);
                                    $reply_count = get_comment_reply_count($conn, $comment['comm_id']);
                                    $comment_time = format_comment_time($comment['comm_time']);
                                    
                                    // 管理模式下显示编辑/删除按钮
                                    $operate_btns = '<div class="comm-operate">
                                        <button class="comm-delete" data-comm-id="'.$comment['comm_id'].'">删除</button>
                                    </div>';
                                } else {
                                    // 普通消息模式：区分直接回复你的评论/评论你的文章
                                    if (!empty($comment['parent_comm_admin']) && $comment['parent_comm_admin'] == $user_uid) {
                                        // 直接回复你的评论：获取你被回复的那条评论内容
                                        $your_comment_id = get_root_comment_id($conn, $comment['comm_type']);
                                        $your_comment_content = get_comment_content($conn, $your_comment_id);
                                        
                                        if (empty($your_comment_content)) {
                                            $your_comment_content = "【该评论已删除】";
                                        } else {
                                            // 截取前100个字符，避免文案过长
                                            $your_comment_content = mb_substr($your_comment_content, 0,100, 'UTF-8');
                                            if (mb_strlen($your_comment_content, 'UTF-8') > 100) {
                                                $your_comment_content .= '...';
                                            }
                                            $your_comment_content = htmlspecialchars($your_comment_content);
                                        }
                                        $comment_type = "回复了我的：{$your_comment_content}";
                                    } else {
                                        // 评论你的文章
                                        $subject_title = !empty($comment['subject_title']) ? htmlspecialchars($comment['subject_title']) : '【'.$set_tag.'不存在或已被删除】';
                                        $comment_type = "评论了我的".$set_tag."：《{$subject_title}》";
                                    }
                                    // 普通模式初始化变量（避免报错）
                                    $like_count = 0;
                                    $reply_count = 0;
                                    $comment_time = '';
                                    $operate_btns = ''; // 普通模式不显示按钮
                                }
                                // 2. 未读标识（仅普通模式显示）
                                $comment_yes = ($manage_my_comm == 0 && $comment['comm_yes'] == 0) ? '<div class="notmsg" date-yes-id="'.$comment['comm_id'].'"></div><div class="comm-edit-div"><a class="comm-edit" data-comm-id="'.$comment['comm_id'].'"><i class="fa fa-bell-slash-o" aria-hidden="true"></i>标记为已读</a></div>' : '';
                                // 3. 发送者信息
                                $sender_name = !empty($comment['sender_name']) ? $comment['sender_name'] : '匿名用户';
                                $sender_avatar = !empty($comment['sender_avatar']) ? $comment['sender_avatar'] : '/images/web/default.jpg';
                                // 4. 文章跳转链接（仅文章状态为3时显示）
                                $gotosub = '';
                                if ($comment['subject_status'] == 3) {
                                    $gotosub = '<a href="detail.php?id='.$comment['comm_subid'].'" target="_blank" class="gotosub" title="前往'.$set_tag.'页"><i class="fa fa-share" aria-hidden="true"></i></a>';
                                }
                                ?>
                                <div date-del-id="<?php echo $comment['comm_id'];?>" class="sub-comm-list sub-comm-flex">
                                    <span>
                                        <a href="/user.php?id=<?php echo $comment['comm_admin'];?>" target="_blank">
                                            <img src="<?php echo $sender_avatar;?>"/>
                                            <div class="ellipsis_clamp1"><?php echo $sender_name;?></div>
                                        </a>
                                        <div class="ellipsis_clamp1 max-width-70"><?php echo $comment_type;?></div>
                                    </span>
                                    <?php echo htmlspecialchars($comment['comm_text']);?>
                                    <?php if($manage_my_comm == 1): ?>
                                    <div class="comm-stats-div">
                                        <div class="comm-stats">
                                            <div class="stat-item">
                                                <span>获赞：<?php echo $like_count;?></span>
                                            </div>
                                            <div class="stat-item">
                                                <span>回复：<?php echo $reply_count;?></span>
                                            </div>
                                        </div>
                                        <div class="comm-time">
                                            时间：<?php echo $comment_time;?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php echo $gotosub;?>
                                    <?php echo $comment_yes;?>
                                    <?php echo $operate_btns;?>
                                </div>
                            <?php endforeach;?>
                            <div class="sub-pagination">
                                <?php if ($page > 1): ?>
                                    <a href="comment.php?manage=<?php echo $manage_my_comm;?>&page=<?php echo $page-1;?>">上一页</a>
                                <?php endif;?>
                                <?php if ($page * $page_size < $total): ?>
                                    <a href="comment.php?manage=<?php echo $manage_my_comm;?>&page=<?php echo $page+1;?>">下一页</a>
                                <?php endif;?>
                                <span>第<?php echo $page;?>页 / 共<?php echo ceil($total/$page_size);?>页</span>
                            </div>
                        <?php else:?>
                            <div class="sub-comm-list sub-comm-flex">
                                <div class="sub-comm-empty">
                                    <?php echo $manage_my_comm == 1 ? '你还未发表任何评论/回复' : '暂无新的消息';?>
                                </div>
                            </div>
                        <?php endif;?>
                    </div>
                </div>
            </div>
        <?php }?>
    </div>
    <?php @include $_SERVER['DOCUMENT_ROOT'].'/inc/footer.php';?>
    <?php
    if (empty($ppzusername)){
        echo '<script src="/style/js/login.js" type="text/javascript"></script>';
    }else{
        echo '<script src="editcomment.js" type="text/javascript"></script>';
    }?>
    <?php @include $_SERVER['DOCUMENT_ROOT'].'/wap/index.php';?>
</body>
</html>