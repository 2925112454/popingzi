<?php
include $_SERVER['DOCUMENT_ROOT'].'/inc/inc.php';//通用
// 跳转函数
function redirectWithMsg($url) {
    header("Location: {$url}");
    exit;
}
// 1. 登录校验：未登录直接拦截
if (empty($ppzusername)) {
    redirectWithMsg('/');  
}

// 2. 校验文章ID参数
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

// 获取文章ID
$article_id = 0;
if(isset($_GET['id']) && !empty($_GET['id']) && is_number($_GET['id'])){
    $article_id = intval(trim($_GET['id']));
} else {
    redirectWithMsg('/subject/?mine=1');
}

// 3. 获取登录用户UID
$unread_user_ustatus=1;
$user_uid_sql = "SELECT uid,ustatus FROM ppz_newusername WHERE uusername = '".mysqli_real_escape_string($conn, $ppzusername)."' LIMIT 1";
$user_uid_ret = mysqli_query($conn, $user_uid_sql);
$user_uid = 0;
if ($user_uid_ret && mysqli_num_rows($user_uid_ret) > 0) {
    $user_uid_row = mysqli_fetch_array($user_uid_ret);
    $user_uid = intval($user_uid_row['uid']);
    $unread_user_ustatus = intval($user_uid_row['ustatus']);
}
if ($user_uid == 0) {
    redirectWithMsg('/subject/?mine=1');
}

// 4. 校验文章归属：必须是作者本人或者管理者
if($unread_user_ustatus==4||$unread_user_ustatus==3||$unread_user_ustatus==2){
    $article_sql = "SELECT * FROM ppz_subject WHERE id = {$article_id} LIMIT 1";
}else{
    $article_sql = "SELECT * FROM ppz_subject WHERE id = {$article_id} AND admin = {$user_uid} LIMIT 1";
}

$article_ret = mysqli_query($conn, $article_sql);
if (!$article_ret || mysqli_num_rows($article_ret) == 0) {
    redirectWithMsg('/subject/?mine=1');
}
$article = mysqli_fetch_array($article_ret); // 文章原始数据

$sub_new_tag="";
if(isset($_GET['t']) && !empty($_GET['t']) && is_number($_GET['t'])){
    $sub_new_tag = intval(trim($_GET['t']));
}
$is_mine = 0;
if(isset($_GET['mine']) && !empty($_GET['mine']) && is_number($_GET['mine'])){
    $is_mine = intval(trim($_GET['mine']));
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
    <script src="core.js" type="text/javascript"></script>
    <script src="md5.js" type="text/javascript"></script>
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
                </div></div>

                <div class="sub-right">
                    <div class="sub-header"><h2>编辑<?php echo $set_tag;?></h2><a class="subgoback" data-url="/subject/?mine=1"><i class="fa fa-reply-all" aria-hidden="true"></i>返回</a></div>
                        <div class="sub-content">
                                <script src="/style/tinymce/tinymce.min.js"></script>
                                    <form id="subpost" method="post" enctype="multipart/form-data">
                                        <div class="tougao-item">
                                            <label class="tougao-title-label">标签 *</label>
                                            <div>
                                                <select name="sub_tag" class="postform">
                                                <?php
                                                    $tag_sql = "select sub_id, sub_name from ppz_subtype order by sub_id asc";
                                                    $tag_retval = mysqli_query($conn, $tag_sql);
                                                    $tag_count = $tag_retval ? mysqli_num_rows($tag_retval) : 0;
                                                    if ($tag_count == 0) {
                                                        echo '<option value="0">错误标签</option>';
                                                    } else {
                                                        while($tag = mysqli_fetch_array($tag_retval)){
                                                            $tag_id = intval($tag['sub_id']);
                                                            $tag_name = htmlspecialchars($tag['sub_name'], ENT_QUOTES, 'UTF-8');
                                                            $selected = ($tag_id == $article['type']) ? 'selected' : '';
                                                            echo '<option value="'.$tag_id.'" '.$selected.'>'.$tag_name.'</option>';
                                                        }
                                                    }
                                                ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="tougao-item">
                                            <label class="tougao-title-label">标题 *</label>
                                            <input type="text" name="sub_title" class="tougao-input" value="<?php echo htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8'); ?>" required="" placeholder="请输入标题" />
                                        </div>
                                        <div class="tougao-item">
                                            <label class="tougao-title-label">内容 *</label>
                                            <textarea name="sub_textarea" id="sub_textarea" class="tougao-textarea"><?php echo htmlspecialchars($article['text'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                                        </div>
                                        <div class="tougao-item">
                                            <label class="tougao-title-label">引用文章或<?php echo $set_tag;?></label>
                                            <input type="text" name="quote_ids" class="tougao-input" placeholder="示例：9,{1},10（纯数字=文章ID，{数字}=<?php echo $set_tag;?>ID，多个内容用逗号分隔）" value="<?php echo htmlspecialchars($article['quote'], ENT_QUOTES, 'UTF-8'); ?>" />
                                            <div id="quote_tips">
                                                提示：不引用可留空，输入文章ID或<?php echo $set_tag;?>ID，多个内容用逗号分隔(对应ID在文章/<?php echo $set_tag;?>详情页URL可见)；一个<?php echo $set_tag;?>最多可引用10个。
                                            </div>
                                        </div>
                                        <input name="sub_id" type="hidden" value="<?php echo htmlspecialchars($article['id'], ENT_QUOTES, 'UTF-8'); ?>" />
                                        <button type="submit" id="subbtn" class="btn">保存修改</button>
                                        <div class="sub-msg" id="submsg"></div>
                                    </form>
                                <?php
                                    $post_sql="SELECT * FROM `ppz_upfile` where id=1";
                                    $post_res=mysqli_query($conn,$post_sql);
                                    if ($post_res&&mysqli_num_rows($post_res)>0) {
                                        while ($post_row=mysqli_fetch_assoc($post_res)) {
                                            $postimg_off=$post_row['upifimg'];
                                            $post_size=$post_row['upsize'];
                                        }
                                    }else{
                                        $postimg_off=0;
                                        $post_size=0;
                                    }
                                    if($postimg_off==1){
                                        $upimg="on";
                                    }else{
                                        $upimg="off";
                                    }
                                    echo '<script type="text/javascript">let post_max_size='.$post_size.';let upimgoff="'.$upimg.'";let submun="'.$set_mun.'"</script>';
                                ?>                                
                                <script src="/style/tinymce/sub.js"></script>
                                <script src="editsub.js" type="text/javascript"></script>
                                <script src="goback.js" type="text/javascript"></script>
                        </div>
                </div>

            </div>
            
        <?php }?>
    </div>
    <?php @include $_SERVER['DOCUMENT_ROOT'].'/inc/footer.php';?>
    <?php if (empty($ppzusername)){ echo '<script src="/style/js/login.js" type="text/javascript"></script>';}?>
    <?php @include $_SERVER['DOCUMENT_ROOT'].'/wap/index.php';?>
</body>
</html>