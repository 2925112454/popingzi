<div class="body-right">

<div class="body-right-top">
<?php

if (empty($ppzusername)){ echo '
<div class="body-right-text"><span class="body-hello">嗨！朋友</span> <span>登录后即可收藏你喜欢的内容！</span></div>
<div class="no-social"><a class="logon-button" id="showModaladl2">登录</a><a class="logon-button" id="showModalazc2" >注册</a></div>
<div class="announcement">
<ul class="announcement-ul">';
$ggsql = "select * from ppz_announcement ORDER BY ggid desc LIMIT 4";//获取公告
$ggretval=mysqli_query($conn,$ggsql);
if(mysqli_num_rows($ggretval) < 1){
echo '<a><li class="announcement-li">暂无公告！</li></a>';
}else{
$ggquery = $conn->query($ggsql);
    while($ggrow = $ggquery->fetch_array()){
    $ggid=$ggrow['ggid'];//获取id
    $ggtext=$ggrow['ggtext'];
    echo '
    <a href="/anctshow.php?id='.$ggid.'"><li class="announcement-li">'.$ggtext.'</li></a>
    ';
    };
};
echo '
</ul>
<div class="announcement-button"><a href="/anct.php">全部公告</a></div>
</div>';
}else{ echo '

<div class="information-div">
<div class="information-div-img" style="background-image: url('.$uimg.');"><a href="/user.php?id='.$allnameid.'" target="_blank"></a></div>
<div class="information-div-info"><span class="info-vip"><a href="/user.php?id='.$allnameid.'" target="_blank">'.$uname.'</a>'.$uviptext.'</span><span>VIP剩余：'. $timetext.'<i><span id="newugoldtwo">'.$ugold.'</span>积分</i></span></div>
</div>
<div class="statistics">
<div class="statistics-div"><span>文章</span><span>'.$rowadmin_records.'</span></div>
<div class="statistics-div"><span>评论</span><span>'.$pla_records.'</span></div>
<div class="statistics-div"><span>关注</span><span>'.$folusrecords.'</span></div>
<div class="statistics-div"><span>粉丝</span><span>'.$folusrecords2.'</span></div>
</div>
<div class="announcement">
<ul class="announcement-ul">';
$gg2sql = "select * from ppz_announcement ORDER BY ggid desc LIMIT 4";//获取公告
$gg2retval=mysqli_query($conn,$gg2sql);
if(mysqli_num_rows($gg2retval) < 1){
echo '<a><li class="announcement-li">暂无公告！</li></a>';
}else{
$gg2query = $conn->query($gg2sql);
    while($gg2row = $gg2query->fetch_array()){
    $ggid2=$gg2row['ggid'];//获取id
    $ggtext2=$gg2row['ggtext'];
    echo '
    <a href="/anctshow.php?id='.$ggid2.'"><li class="announcement-li">'.$ggtext2.'</li></a>
    ';
    };
};
echo '
</ul>
<div class="announcement-button"><a href="/anct.php">全部公告</a></div>
</div>';} ?>

</div>

<?php echo $adson_ybl?>

<div class="body-right-top">
<div class="hot-title">标签云</div>
<div class="hot-img flex-wrap">
<?php
$tsqlxx = "select rowtag from ppz_row where rowyes = 4 ORDER BY rowid desc LIMIT 100";//获取前100条文章的标签
$tretvalxx=mysqli_query($conn,$tsqlxx);
if(mysqli_num_rows($tretvalxx) < 1){
echo '<a><li class="announcement-li">暂无标签</li></a>';
}else{
    $rowtagarr=[];
    $tqueryxx = $conn->query($tsqlxx);
    while($trowxx = $tqueryxx->fetch_array()){
    $rowtag=trim($trowxx['rowtag']);//获取标签
        if (!empty($rowtag)) {
            $rowtag = explode(',', $rowtag);
            foreach ($rowtag as $tagss) {
                $tagss = trim($tagss); // 去除每个标签前后的空格
                if (!empty($tagss)) {
                    $rowtagarr[] = $tagss;
                }
            }
        }
    };
    $rowtagarr = array_unique($rowtagarr);//去重
    $rowtagarr = array_filter($rowtagarr);//删除空值

    $randarr=[];
    if (count($rowtagarr) < 20) {
        $randarr = $rowtagarr; // 直接赋值整个数组
    } else {
        $keysxx = array_rand($rowtagarr, 20); // 一次性获取20个随机键
        foreach ($keysxx as $keyxx) {
            $randarr[] = $rowtagarr[$keyxx]; // 根据键获取对应的值
        }
    }
    $randarr = array_unique($randarr);//去重
    $randarr = array_filter($randarr);//删除空值
    // 输出链接
    foreach ($randarr as $tagtt) {
        $safe_tag = htmlspecialchars(urlencode($tagtt), ENT_QUOTES, 'UTF-8');
        echo '<a class="tagnet" target="_blank" href="/search.php?v=tag&s=' . $safe_tag . '">' . htmlspecialchars($tagtt, ENT_QUOTES, 'UTF-8') . '</a>';
    }
    
}
?>
</div>
</div>

<div class="body-right-top" id="navright">
<div class="hot-title">热门 TOP10</div>
<ul class="hot-link">
<?php
$tsql = "select * from ppz_row where rowyes = 4 ORDER BY roweye desc LIMIT 10";//获取热门文章
$tretval=mysqli_query($conn,$tsql);
if(mysqli_num_rows($tretval) < 1){
echo '<a><li class="announcement-li">暂无文章</li></a>';
}else{

$tquery = $conn->query($tsql);
    while($trow = $tquery->fetch_array()){
    $tid=$trow['rowid'];//获取id
    $tbt=$trow['rowtexe'];
    echo '<a href="/show.php?id='.$tid.'"><li class="announcement-li">'.$tbt.'</li></a>';
    };
}
?>
</ul>
</div>
</div>