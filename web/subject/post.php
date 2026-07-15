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

// 验证引用话题是否有效（状态为3-通过）
function check_quote_topic($conn, $topic_id) {
    if (!is_number($topic_id)) return false;
    $sql = "SELECT id, title FROM ppz_subject WHERE id = ".intval($topic_id)." AND yes = 3 LIMIT 1";
    $ret = mysqli_query($conn, $sql);
    return $ret && mysqli_num_rows($ret) > 0 ? mysqli_fetch_assoc($ret) : false;
}

// 验证引用文章是否有效，并处理封面逻辑
function check_quote_article($conn, $article_id) {
    if (!is_number($article_id)) return false;
    $sql = "SELECT rowid, rowtexe, rowif, rowimg FROM ppz_row WHERE rowid = ".intval($article_id)." LIMIT 1";
    $ret = mysqli_query($conn, $sql);
    if (!$ret || mysqli_num_rows($ret) == 0) return false;
    
    $article = mysqli_fetch_assoc($ret);
    $default_cover = '/images/web/null.jpg';
    $article['cover'] = $default_cover; // 默认封面
    
    // 处理不同类型文章的封面
    switch (intval($article['rowif'])) {
        case 1: // 图文：优先rowimg，无则取内容第一张图（示例逻辑，需根据实际内容字段调整）
            if (!empty($article['rowimg'])) {
                $article['cover'] = $article['rowimg'];
            } else {
                $content_sql = "SELECT rowbigtext FROM ppz_row WHERE rowid = ".intval($article_id)." LIMIT 1";
                $content_ret = mysqli_query($conn, $content_sql);
                if ($content_ret && $row = mysqli_fetch_assoc($content_ret)) {
                    if (preg_match('/<img[^>]+src="([^"]+)"/i', $row['rowbigtext'], $match)) {
                        $article['cover'] = $match[1];
                    }
                }
            }
            break;
        case 2: // 相册：rowimg为空则取内容中|分割的第一个
            if (!empty($article['rowimg'])) {
                $article['cover'] = $article['rowimg'];
            } else {
                $content_sql = "SELECT rowbigtext FROM ppz_row WHERE rowid = ".intval($article_id)." LIMIT 1";
                $content_ret = mysqli_query($conn, $content_sql);
                if ($content_ret && $row = mysqli_fetch_assoc($content_ret)) {
                    $imgs = explode('|', $row['rowbigtext']);
                    if (!empty($imgs[0])) $article['cover'] = $imgs[0];
                }
            }
            break;
        case 3: // 视频：直接用rowimg
            if (!empty($article['rowimg'])) {
                $article['cover'] = $article['rowimg'];
            }
            break;
    }
    return $article;
}

// 生成JSON格式的引用数据（兼容PHP5.5的json_encode参数）
function build_quote_json($quote_data) {
    $json_options = 0;
    if (PHP_VERSION >= 5.4) {
        $json_options = JSON_UNESCAPED_UNICODE; // 5.4+支持，避免中文转义
    }
    return json_encode($quote_data, $json_options);
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
?>
<!DOCTYPE html>
<html lang="zh-CN" translate="no">
<head>
    <meta charset="utf-8">
    <title>发表<?php echo $set_title;?> - <?php echo $webtext;?>丨<?php echo $webby;?></title>
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
// 获取当前登录用户的UID（需确保$ppzusername关联的uid可通过ppz_newusername表查询）
$user_uid_sql = "SELECT uid FROM ppz_newusername WHERE uusername = '".mysqli_real_escape_string($conn, $ppzusername)."' LIMIT 1";
$user_uid_ret = mysqli_query($conn, $user_uid_sql);
$user_uid = 0;
if ($user_uid_ret && mysqli_num_rows($user_uid_ret) > 0) {
    $user_uid_row = mysqli_fetch_array($user_uid_ret);
    $user_uid = intval($user_uid_row['uid']);
}
// 仅当未读数量>0时显示提示
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
                    <div class="sub-header"><h2>发表<?php echo $set_tag;?></h2></div>
                        <div class="sub-content">
                                <script src="/style/tinymce/tinymce.min.js"></script>
                                    <form id="subpost" method="post" enctype="multipart/form-data">
                                        <div class="tougao-item">
                                            <label class="tougao-title-label">标签 *</label>
                                            <div>
                                                <select name="sub_tag" class="postform">
                                                <?php
                                                    //获取所有标签，输出为下拉框；若无标签则自动创建默认标签
                                                    $tag_sql = "select sub_id, sub_name from ppz_subtype order by sub_id asc";
                                                    $tag_retval = mysqli_query($conn, $tag_sql);
                                                    
                                                    // 检查查询结果是否有数据
                                                    $tag_count = $tag_retval ? mysqli_num_rows($tag_retval) : 0;
                                                    
                                                    // 如果没有标签，创建默认标签
                                                    if ($tag_count == 0) {
                                                        // 定义默认标签信息（兼容PHP5.5的字符串拼接方式）
                                                        $default_tag_name = "示例标签";
                                                        $default_tag_sql = "INSERT INTO ppz_subtype (sub_name) VALUES ('" . mysqli_real_escape_string($conn, $default_tag_name) . "')";
                                                        
                                                        // 执行插入操作（PHP5.5兼容的mysqli语法）
                                                        if (mysqli_query($conn, $default_tag_sql)) {
                                                            // 获取刚插入的默认标签ID
                                                            $default_tag_id = mysqli_insert_id($conn);
                                                            // 输出默认标签的option
                                                            echo '<option value="' . intval($default_tag_id) . '">' . htmlspecialchars($default_tag_name, ENT_QUOTES, 'UTF-8') . '</option>';
                                                        } else {
                                                            // 插入失败时的容错处理（可选，避免页面报错）
                                                            echo '<option value="0">暂无标签</option>';
                                                        }
                                                    } else {
                                                        // 有标签时正常循环输出
                                                        while($tag = mysqli_fetch_array($tag_retval)){
                                                            $tag_id = intval($tag['sub_id']);
                                                            $tag_name = htmlspecialchars($tag['sub_name'], ENT_QUOTES, 'UTF-8');
                                                            echo '<option value="'.$tag_id.'">'.$tag_name.'</option>';
                                                        }
                                                    }
                                                ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="tougao-item">
                                            <label class="tougao-title-label">标题 *</label>
                                            <input type="text" name="sub_title" class="tougao-input" value="" required="" placeholder="请输入标题" />
                                        </div>
                                        <div class="tougao-item">
                                            <label class="tougao-title-label">内容 *</label>
                                            <textarea id="sub_textarea" class="tougao-textarea"></textarea>
                                        </div>

                                        <div class="tougao-item">
                                            <label class="tougao-title-label">引用文章或<?php echo $set_tag;?></label>
                                            <input type="text" name="quote_ids" class="tougao-input" placeholder="示例：9,{1},10（纯数字=文章ID，{数字}=<?php echo $set_tag;?>ID，多个内容用逗号分隔）" />
                                            <div id="quote_tips">
                                                提示：不引用可留空，输入文章ID或<?php echo $set_tag;?>ID，多个内容用逗号分隔(对应ID在文章/<?php echo $set_tag;?>详情页URL可见)；一个<?php echo $set_tag;?>最多可引用10个。
                                            </div>
                                        </div>

                                        <button type="submit" id="subbtn" class="btn">发表</button>
                                        <div class="sub-msg" id="submsg"></div>
                                    </form>
                                <?php
                                    $post_sql="SELECT * FROM `ppz_upfile` where id=1";
                                    $post_res=mysqli_query($conn,$post_sql);
                                    if ($post_res&&mysqli_num_rows($post_res)>0) {
                                        while ($post_row=mysqli_fetch_assoc($post_res)) {
                                            $postimg_off=$post_row['upifimg'];//上传开关，0关闭，1开启
                                            $post_size=$post_row['upsize'];//投稿文件大小限制(KB)
                                        }
                                    }else{
                                        $postimg_off=0;//上传开关，0关闭，1开启
                                        $post_size=0;//投稿文件大小限制(KB)
                                    }
                                    if($postimg_off==1){
                                        $upimg="on";
                                    }else{
                                        $upimg="off";
                                    }
                                    echo '<script type="text/javascript">let post_max_size='.$post_size.';let upimgoff="'.$upimg.'";let submun="'.$set_mun.'"</script>';
                                ?>
                                <script src="/style/tinymce/sub.js"></script>
                                <script src="postsub.js" type="text/javascript"></script>
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