<?php
ob_start();
@include $_SERVER['DOCUMENT_ROOT'].'/inc/inc.php';//链接数据库
if (!isset($_GET["id"])||empty($_GET["id"])||!isset($ppzusername)||empty($ppzusername)||!is_numeric($_GET["id"])||$_GET["id"]<1){ 
    if (!headers_sent()) {
        ob_clean();
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
        header("Location: /");
    } else {
        echo "<script>window.location.href = '/';</script>";
    }
    die();
}
$com_id=intval(trim($_GET["id"]));
$commrep_sql="SELECT * FROM `ppz_commentary` WHERE `plid` = '$com_id'";
$commrep_res=mysqli_query($conn,$commrep_sql);
if (!$commrep_res||mysqli_num_rows($commrep_res) !== 1) {
    if (!headers_sent()) {
        ob_clean();
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
        header("Location: /");
    } else {
        echo "<script>window.location.href = '/';</script>";
    }
    die();
}
while ($commrep_row = mysqli_fetch_array($commrep_res)) {
    $commrep_text=$commrep_row['plbigtext'];//评论内容
    $commrep_time=$commrep_row['pltime'];//评论时间
    $commrep_rowid=$commrep_row['plrowid'];//评论文章id
    $commrep_admin=$commrep_row['pladmin'];//评论会员id
    $commrep_top=$commrep_row['pltop'];//点赞数组，按|分隔
}
if(empty($commrep_text)||empty($commrep_time)||empty($commrep_rowid)||empty($commrep_admin)||!is_numeric($commrep_rowid)||!is_numeric($commrep_admin)||$commrep_admin<1||$commrep_rowid<1){
    if (!headers_sent()) {
        ob_clean();
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
        header("Location: /");
    } else {
        echo "<script>window.location.href = '/';</script>";
    }
    die();
}
$commrep_top_anime = $commrep_top;
if(empty($commrep_top)){
    $commrep_top=0;
    $commrep_toparr = []; // 初始化为空数组
}else{
    $commrep_toparr=explode("|",$commrep_top);
    $commrep_top=count($commrep_toparr);
}
//获取文章标题
$comrow_sql="SELECT rowtexe,rowyes FROM `ppz_row` WHERE `rowid` = '$commrep_rowid'";
$comrow_res=mysqli_query($conn,$comrow_sql);
if($comrow_res&&mysqli_num_rows($comrow_res)>0){
    while($comrow_row=mysqli_fetch_array($comrow_res)){
        if($comrow_row['rowyes']==4){
            $comrow_row_rowyes="";
        }else{
            $comrow_row_rowyes='<span class="notrow">[文章不可访问]</span>';
        }
        $comrow_texe=$comrow_row_rowyes.$comrow_row['rowtexe'];
    }
}else{
    $comrow_texe="未知内容";
}

//获取评论作者信息
$comuser_sql="SELECT uname FROM `ppz_newusername` WHERE `uid` = '$commrep_admin'";
$comuser_res=mysqli_query($conn,$comuser_sql);
if($comuser_res&&mysqli_num_rows($comuser_res)>0){
    while($comuser_row=mysqli_fetch_array($comuser_res)){
        $comuser_name=$comuser_row['uname'];
    }
}else{
    $comuser_name="未知会员";
}

if (!isset($_GET["p"])){ 
    $p=1;
}else{
    $p=intval(trim($_GET["p"]));
    if($p<1){$p = 1;}
}
$num_rec_per_page=10;//每页显示的回复数量
if (empty($p) || !is_numeric($p) || $p < 1) {
    $p = 1;
} else {
    $p = $p;
}

//获取评论下的回复
$comrep_sql="SELECT * FROM `ppz_reply` WHERE `repplid` = '$com_id'";
$comrep_res=mysqli_query($conn,$comrep_sql);
if($comrep_res&&mysqli_num_rows($comrep_res)>0){
    $comrep_mun=mysqli_num_rows($comrep_res);
}else{
    $comrep_mun=0;
}
$total_pages = ceil($comrep_mun / $num_rec_per_page);//计算总页数
if($p>$total_pages&&$total_pages>0){$p = $total_pages;}//页码大于总页数时，设为总页数
$start_from = ($p-1) * $num_rec_per_page;//计算从第几条数据开始显示
/* 设置分页按钮 */
if ($p==1||empty($p)){
    $pageindex='<a class="page-no-button nocopy">首页</a>';//首页按钮
}else{
    $pageindex='<a class="page-button nocopy" href="?id='.$com_id.'">首页</a>';//首页按钮
}

if ($p==$total_pages||$total_pages<1){
    $pagebody='<a class="page-no-button nocopy" >尾页</a>';
}else{
    $pagebody='<a class="page-button nocopy" href="?id='.$com_id.'&p='.$total_pages.'">尾页</a>';
}

if ($total_pages>1&&$p<$total_pages){
    $exit=$p+1;
    $pageexit='<a class="page-button nocopy" href="?id='.$com_id.'&p='.$exit.'">下一页</a>';
}else{
    $pageexit='<a class="page-no-button nocopy" >下一页</a>';
}

if ($p<=$total_pages&&$p>1){
    $exitup=$p-1;
    $pageup='<a class="page-button nocopy" href="?id='.$com_id.'&p='.$exitup.'">上一页</a>';
}else{
    $pageup='<a class="page-no-button nocopy" >上一页</a>';
}
?>
<!DOCTYPE html>
<html lang="zh-CN" translate="no">
<head>
<meta charset="utf-8">
<title>评论详情 - <?php echo $webtext;?>丨<?php echo $webby;?></title>
<meta name="keywords" content="<?php echo $webpass;?>" />
<meta name="description" content="<?php echo $webvar;?>" />
<link rel="icon" href="/favicon.ico"/>
<?php @include $_SERVER['DOCUMENT_ROOT'].'/inc/style.php';?>
<link type="text/css" rel="stylesheet" href="/style/css/font-awesome-4.7.0/css/font-awesome.min.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link type="text/css" rel="stylesheet" href="/style/css/rep.css">
<script src="/style/js/jquery-3.5.1.min.js" type="text/javascript"></script>
<script src="/style/js/input.js" type="text/javascript"></script>
<script src="/style/js/alert.js" type="text/javascript"></script>
</head>
<body>
<?php
@include $_SERVER['DOCUMENT_ROOT'].'/inc/header.php';//头部
?>
<div class="body-divx">
    <div class="user padding_15px common-title">
        <b>评论详情<a href="/show.php?id=<?php echo $commrep_rowid;?>">来源：<?php echo $comrow_texe;?></a></b>
        <div class="usercomm-info  padding_15px"><?php echo $commrep_text;?></div>
        <div class="usercomm-mun  padding_15px"><span>本条评论由<a href="/user.php?id=<?php echo $commrep_admin;?>" target="_blank"><?php echo $comuser_name;?></a>发表于<?php echo $commrep_time;?></span><span>点赞：<?php echo $commrep_top;?></span></div>
    </div>
    
    <div class="user padding_15px common-title margin-top-15px">
        <b>回复(<?php echo $comrep_mun;?>)</b>
        <?php
            /* 查询数据表 */
            $rcomm_sql = "$comrep_sql ORDER BY repid desc LIMIT $start_from, $num_rec_per_page";//获取数据库表
            $rcomm_retval=mysqli_query($conn,$rcomm_sql);
            if(!$rcomm_retval||mysqli_num_rows($rcomm_retval) < 1){
                echo '<div class="usercomm-text-null  padding_15px">暂无回复</div>';
            }else{
                while($row = mysqli_fetch_array($rcomm_retval)){
                    $rep_admin = $row['repadmin'];//回复者
                    $rep_text = $row['reptext'];//回复内容
                    $rep_time = $row['reptime'];//回复时间
                    //获取回复者昵称和头像
                    $rep_nickname_sql = "select uname,uimg from ppz_newusername where uid = '$rep_admin'";
                    $rep_nickname_retval = mysqli_query($conn,$rep_nickname_sql);
                    if($rep_nickname_retval&&mysqli_num_rows($rep_nickname_retval) > 0){
                        $rep_nickname_row = mysqli_fetch_array($rep_nickname_retval);
                        $rep_nickname = $rep_nickname_row['uname'];
                        $rep_img = $rep_nickname_row['uimg'];
                        if (empty($rep_img)){
                            $rep_img = '/images/web/default.jpg';
                        }
                    }else{
                        $rep_nickname = '未知会员';
                        $rep_img = '/images/web/default.jpg';
                    }

                    //判断该会员是否在点赞数组内
                    if (in_array($rep_admin, $commrep_toparr)){
                        $like_class_top = '<i class="fa fa-thumbs-o-up" aria-hidden="true"></i>';
                    }else{
                        $like_class_top = '';
                    }
                    
                    echo '
                    <div class="usercomm-text  padding_15px">
                        <div class="usercomm-text-user"><div class="usercomm-text-box"><div style="background-image: url('.$rep_img.');" class="usercomm-text-img"><a href="/user.php?id='.$rep_admin.'" target="_blank"></a></div><a href="/user.php?id='.$rep_admin.'" target="_blank">'.$rep_nickname.'</a>'.$like_class_top.'</div><span><i class="fa fa-clock-o"></i>'.$rep_time.'</span></div>
                        <div class="usercomm-text-con">'.$rep_text.'</div>
                    </div>
                    ';
                }

            }
        ?>
        <div class="usercomm-text-page  padding_15px">
            <span class="page-left">第<?php echo $p;?>页（共<?php echo $total_pages;?>页）- 共计：<?php echo $comrep_mun;?>条记录</span>
            <span class="page-right"><?php echo $pageindex.$pageup.$pageexit.$pagebody;?></span>
        </div>

    </div>

</div>
<?php
if (!empty($commrep_top_anime)) {
    $commrep_top_anime = explode('|', $commrep_top_anime);
    $commrep_top_anime = array_unique(array_filter($commrep_top_anime));
    
    // 判断是否都是数字且大于0
    $commrep_top_anime_is_num = true;
    foreach ($commrep_top_anime as $uid) {
        if (!is_numeric($uid) || $uid <= 0) {
            $commrep_top_anime_is_num = false;
            break;
        }
    }

    if ($commrep_top_anime_is_num) {
        echo '<div id="like-container" class="like-container"></div>
<script type="text/javascript">
const likeData = [';

        // 批量查询用户信息
        $uids = implode(',', array_map('intval', $commrep_top_anime));
        $sqls = "SELECT uid, uname, uimg FROM ppz_newusername WHERE uid IN ($uids)";
        $resultss = mysqli_query($conn, $sqls);

        $users = [];
        if ($resultss && mysqli_num_rows($resultss) > 0) {
            while ($row = mysqli_fetch_assoc($resultss)) {
                $unames = !empty($row['uname']) ? addslashes($row['uname']) : '未知会员';
                $uimgs = !empty($row['uimg']) ? $row['uimg'] : '/images/web/default.jpg';
                $users[] = '{ name: "' . $unames . '", avatar: "' . $uimgs . '" }';
            }
        }

        echo implode(',', $users); // 输出 JSON 格式用户列表

        echo '];
        </script>
        <script src="/style/js/rep.js" type="text/javascript"></script>';
    }
}
?>
<?php @include $_SERVER['DOCUMENT_ROOT'].'/inc/footer.php';?>
<?php @include $_SERVER['DOCUMENT_ROOT'].'/wap/index.php';?>
</body>
</html>
<?php
ob_end_flush();
?>