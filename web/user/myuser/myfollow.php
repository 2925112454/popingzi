<?php
ob_start();
if (empty($allnameid) || !isset($allnameid) || !is_numeric($allnameid) || $allnameid < 1 ||
    !isset($myuser) || empty($myuser) || $myuser != 200 ||
    !isset($ppzusername) || empty($ppzusername) ||
    !isset($typeuser) || empty($typeuser) || $typeuser != 7) {
    if (!headers_sent()) {
        ob_clean();
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
        header("Location: /");
    } else {
        echo "<script>window.location.href = '/';</script>";
    }
    die(); // 确保脚本完全终止
}

if (!isset($_GET["p"])){ 
    $_GET["p"]="";
}
$num_rec_per_page=32;//每页显示数量
$p=trim($_GET["p"]);//页码
if (empty($p)||!is_numeric($p)||$p<1){ 
    $p=1;
}else{ 
    $p=intval($p);
}
echo '<div class="user-h1 myuser">我的关注</div>';
// 获取关注数据库
$fans_arr=[];
$fans_sql = "SELECT usuename FROM ppz_folus WHERE usvip = $allnameid";
$fans_retval = mysqli_query($conn, $fans_sql);
if($fans_retval && mysqli_num_rows($fans_retval) > 0){
    while ($fans_query = mysqli_fetch_assoc($fans_retval)){
        $fans_arr[] = $fans_query['usuename'];
    }
}
$fans_arr = array_unique(array_filter($fans_arr));
if (empty($fans_arr)||count($fans_arr)<1){
    echo '<div class="empty-collection">你还没有关注任何用户</div>';
}else{
    $valid_fans = []; // 创建一个新数组存储有效关注ID
    foreach ($fans_arr as $fan_id) {
        $query = mysqli_query($conn, "SELECT uid FROM ppz_newusername WHERE uid = '$fan_id'");
        if (mysqli_num_rows($query) == 1) {
            $valid_fans[] = $fan_id;
        }
    }

    // 基于有效ID计算分页
    $valid_count = count($valid_fans); // 有效文章总数
    $total_pages = ceil($valid_count / $num_rec_per_page); // 计算总页数
    if ($p > $total_pages) {
        $p = max(1, $total_pages); // 确保页码有效
    }
    $start_from = ($p - 1) * $num_rec_per_page; // 计算从第几条数据开始显示
    /* 设置分页按钮 */
    if ($p == 1) {
        $pageindex = '<a class="page-no-button nocopy">首页</a>'; // 首页按钮
    } else {
        $pageindex = '<a class="page-button nocopy" href="?type=7">首页</a>'; // 首页按钮
    }

    if ($p == $total_pages || $total_pages < 1) {
        $pagebody = '<a class="page-no-button nocopy">尾页</a>';
    } else {
        $pagebody = '<a class="page-button nocopy" href="?type=7&p='.$total_pages.'">尾页</a>';
    }

    if ($total_pages > 1 && $p < $total_pages) {
        $exit = $p + 1;
        $pageexit = '<a class="page-button nocopy" href="?type=7&p='.$exit.'">下一页</a>';
    } else {
        $pageexit = '<a class="page-no-button nocopy">下一页</a>';
    }

    if ($p <= $total_pages && $p > 1) {
        $exitup = $p - 1;
        $pageup = '<a class="page-button nocopy" href="?type=7&p='.$exitup.'">上一页</a>';
    } else {
        $pageup = '<a class="page-no-button nocopy">上一页</a>';
    }
    echo '<div class="padding_15px flex-wrap parent-element">';
    if ($valid_count > 0) {
        //输出关注信息
        $fs_user_sql = "SELECT uid,uimg,uname,usex FROM ppz_newusername WHERE uid IN (".implode(',', $valid_fans).") ORDER BY uid ASC LIMIT $start_from, $num_rec_per_page";
        $fs_user_retval = mysqli_query($conn, $fs_user_sql);
        while ($fs_user_query = mysqli_fetch_assoc($fs_user_retval)) {
            $fs_user_id = $fs_user_query['uid'];
            $fs_user_img = $fs_user_query['uimg'];
            $fs_user_username = $fs_user_query['uname'];
            $fs_user_sex = $fs_user_query['usex'];
            if ($fs_user_sex == 1) {
                $fs_user_sex = '♂帅哥';
            } else{
                $fs_user_sex = '♀美女';
            }
            if (empty($fs_user_img)) {
                $fs_user_img = '/images/web/default.jpg';
            }
            echo '
            <a href="/user.php?id='.$fs_user_id.'" target="_blank" class="fs_box_a">
                <div class="fs_box_img" style="background:url('.$fs_user_img.');background-repeat: no-repeat; background-size:100%;"></div>
                <div class="fs_box_text"><b>'.$fs_user_username.'</b>'.$fs_user_sex.'</div>
                <i class="fa fa-chevron-circle-right" aria-hidden="true"></i>
            </a>
            ';
        }
    }else{
        echo '<div class="empty-collection">你还没有关注任何用户</div>';
    }
    

    echo'
    </div>
    <div class="clear flex-wrap justify-content-space-between">
        <span class="page-left">第'.$p.'页（共'.$total_pages.'页）- 共计：'.$valid_count.'条记录</span>
        <span class="page-right">
        '.$pageindex.$pageup.$pageexit.$pagebody.'
        </span>
    </div>
    ';
    

}
ob_end_flush();
?>