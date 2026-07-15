<!DOCTYPE html>
<html lang="zh-CN" translate="no">
<head>
<?php
include $_SERVER['DOCUMENT_ROOT'].'/inc/inc.php';//通用
?>
<meta charset="utf-8">
<title>排行榜 - <?php echo $webtext;?>丨<?php echo $webby;?></title>
<meta name="keywords" content="<?php echo $webpass;?>" />
<meta name="description" content="<?php echo $webvar;?>" />
<link rel="icon" href="/favicon.ico"/>
<?php include $_SERVER['DOCUMENT_ROOT'].'/inc/style.php';?>
<link type="text/css" rel="stylesheet" href="/style/css/font-awesome-4.7.0/css/font-awesome.min.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="/style/js/jquery-3.5.1.min.js" type="text/javascript"></script>
<script src="/style/js/input.js" type="text/javascript"></script>
<script src="/style/js/alert.js" type="text/javascript"></script>
</head>
<body>
<?php
include $_SERVER['DOCUMENT_ROOT'].'/inc/header.php';//头部
$ADS=200;
$ADSPAGE=2;
@include $_SERVER['DOCUMENT_ROOT'].'/api/indexads.php';//广告
?>

<div class="body-div">
    <div class="body-left">
<div class="topdiv">
    <ul>
<span class="topspan"><i class="fa fa-bookmark"></i>文章阅览榜 TOP50</span>
<?php
//获取阅览文章信息表
$allrsql2= "select * from ppz_row where rowyes = 4 ORDER BY roweye desc LIMIT 0,50";//获取文章数据库表，
$allrretval2=mysqli_query($conn,$allrsql2);
if(mysqli_num_rows($allrretval2) < 1){
    echo '<a><li>暂无文章显示……</li></a>';
}else{
        $allrquery2 = $conn->query($allrsql2);
        while($allr2 = $allrquery2->fetch_array()){
        $allrid2=$allr2['rowid'];//文章id
        $allrtxt2=$allr2['rowtexe'];//标题
        $allreye=$allr2['roweye'];//预览数量
        echo '<a href="show.php?id='.$allrid2.'"><li class="topli"><div class="divpltxt">'.$allrtxt2.'</div><i>'.$allreye.'阅览</i></li></a>';
    };
};
?>
    </ul>

    <ul>
<span class="topspan"><i class="fa fa-heart"></i>文章收藏榜  TOP50</span>
<?php
//获取收藏文章信息表
$allrsql= "select * from ppz_row where rowyes = 4 ORDER BY rowsc desc LIMIT 0,50";//获取文章数据库表，
$allrretval=mysqli_query($conn,$allrsql);
if(mysqli_num_rows($allrretval) < 1){
    echo '<a><li>暂无文章显示……</li></a>';
}else{
        $allrquery = $conn->query($allrsql);
        while($allr = $allrquery->fetch_array()){
        $allrid=$allr['rowid'];//文章id
        $allrtxt=$allr['rowtexe'];//标题
        $allrsc=$allr['rowsc'];//收藏数量
        echo '<a href="show.php?id='.$allrid.'"><li class="topli"><div class="divpltxt">'.$allrtxt.'</div><i>'.$allrsc.'收藏</i></li></a>';
    };
};
?>
    </ul>

    <ul>
<span class="topspan"><i class="fa fa-comments"></i>会员评论榜  TOP50</span>
<?php
//获取评论信息表
$allpsql= "SELECT *,(LENGTH(pltop) - LENGTH(REPLACE(pltop, '|', ''))) AS total_pl FROM ppz_commentary ORDER BY total_pl DESC LIMIT 0,50;";//获取评论数据库表
$allpretval=mysqli_query($conn,$allpsql);
if(mysqli_num_rows($allpretval) < 1){
    echo '<a><li class="topli"><div class="divpltxt">暂无会员评论……</div><i>0赞</i></li></a>';
}else{
        $allpquery = $conn->query($allpsql);
        while($allp = $allpquery->fetch_array()){
        $allpid=$allp['plrowid'];//获取评论所在文章id
        $allptxt=$allp['plbigtext'];//获取评论
        $allptop=$allp['pltop'];//点赞数组
        if ($allptop==""||is_null($allptop)||empty($allptop)||$allptop==null){
            $pltop=0;
        }else{
            $pltop=count(explode('|',$allptop));
        }
       echo '<a href="show.php?id='.$allpid.'#plall"><li class="topli"><div class="divpltxt">'.$allptxt.'</div><i>'.$pltop.'赞</i></li></a>';
    };
};
?></ul>

    <ul>
<span class="topspan"><i class="fa fa-usd"></i>会员积分榜  TOP50</span>
<?php
//获取会员信息表
$allusql= "select * from ppz_newusername ORDER BY ugold desc LIMIT 0,50";//获取会员数据库表
$alluretval=mysqli_query($conn,$allusql);
if(mysqli_num_rows($alluretval) < 1){
    echo '<a class="topjfa"><li class="topli"><div class="divvtxt"><div style="background-image:url(/images/web/default.jpg);" class="divjfimg"></div>暂无会员加入网站……</div><i>0积分</i></li></a>';
}else{
        $alluquery = $conn->query($allusql);
        while($allu = $alluquery->fetch_array()){
        $alluid=$allu['uid'];//获取会员id
        $alluimg=$allu['uimg'];//获取头像
        $alluname=$allu['uname'];//昵称
        $allugold=$allu['ugold'];//积分
        if ($alluimg==""||$alluimg==null||is_null($alluimg)){
            $alluimgyes="/images/web/default.jpg";
        }else{
            $alluimgyes=$alluimg;
        }
       echo '<a href="user.php?id='.$alluid.'" class="topjfa"><li class="topli"><div class="divvtxt"><div style="background-image:url('.$alluimgyes.');" class="divjfimg"></div>'.$alluname.'</div><i>'.$allugold.'积分</i></li></a>';
    };
};
?>
    </ul>

</div>
    </div>
<?php include $_SERVER['DOCUMENT_ROOT'].'/inc/right.php';?></div>
<?php include $_SERVER['DOCUMENT_ROOT'].'/inc/footer.php';?>
<?php if ($ppzusername == "" ){ echo '<script src="/style/js/login.js" type="text/javascript"></script>';} ?>
<?php @include $_SERVER['DOCUMENT_ROOT'].'/wap/index.php';?>
</body>
</html>