<?php
include $_SERVER['DOCUMENT_ROOT'].'/inc/inc.php';//通用
if (empty($ppzusername) || !isset($ppzusername)){
  header("Location: /");
  exit;
}else{
  if  (!isset($_GET['type'])) {
    $_GET['type']="";
  }
$typeuserx=$_GET["type"];
if ($typeuserx==1 || $typeuserx==2 || $typeuserx==3 || $typeuserx==4 || $typeuserx==5 || $typeuserx==6 || $typeuserx==7 || $typeuserx==8 || $typeuserx==9 || $typeuserx==10|| $typeuserx==11){
  $typeuser=$typeuserx;
}else{
  $typeuser=1;
}
$myuser=200;
?>
<!DOCTYPE html>
<html lang="zh-CN" translate="no">
<head>
<meta charset="utf-8">
<title>我的主页 - <?php echo $webtext;?>丨<?php echo $webby;?></title>
<meta name="keywords" content="<?php echo $webpass;?>" />
<meta name="description" content="<?php echo $webvar;?>" />
<link rel="icon" href="/favicon.ico"/>
<?php @include $_SERVER['DOCUMENT_ROOT'].'/inc/style.php';?>
<link type="text/css" rel="stylesheet" href="/style/css/font-awesome-4.7.0/css/font-awesome.min.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="/style/js/jquery-3.5.1.min.js" type="text/javascript"></script>
<script src="/style/js/input.js" type="text/javascript"></script>
<script src="/style/js/alert.js" type="text/javascript"></script>
</head>
<body>
<?php
if($typeuser==2){
  echo '<style>
  @media(max-width: 480px) {
    #dayornight,.daynighttext{
          display: flex !important;
    }
  }
  </style>
  ';
}
@include $_SERVER['DOCUMENT_ROOT'].'/inc/header.php';//头部
?>
<div class="user-all">
<div class="user-left">
  <div class="hedimg"><div class="hedimg-url" style="background:url(<?php echo $hedimg;?>);background-repeat: no-repeat; background-size: 100%;"></div><div class="hedimg-name nocopy"><?php echo $fuuloginname;?></div></div>
<a href="?type=1" ><div class="user-menu nocopy<?php if($typeuser==1){echo " hover";}?>">我的会员</div></a>
<a href="?type=2" ><div class="user-menu nocopy<?php if($typeuser==2){echo " hover";}?>">我的资料</div></a>
<a href="?type=3" ><div class="user-menu nocopy<?php if($typeuser==3){echo " hover";}?>">我的文章</div></a>
<a href="?type=4" ><div class="user-menu nocopy<?php if($typeuser==4){echo " hover";}?>">我的评论</div></a>
<a href="?type=5" ><div class="user-menu nocopy<?php if($typeuser==5){echo " hover";}?>">我的收藏</div></a>
<a href="?type=11" ><div class="user-menu nocopy<?php if($typeuser==11){echo " hover";}?>">我的购买</div></a>
<a href="?type=6" ><div class="user-menu nocopy<?php if($typeuser==6){echo " hover";}?>">我的粉丝</div></a>
<a href="?type=7" ><div class="user-menu nocopy<?php if($typeuser==7){echo " hover";}?>">我的关注</div></a>
<a href="?type=8" ><div class="user-menu nocopy<?php if($typeuser==8){echo " hover";}?>">我的工单</div></a>
<a href="?type=9" ><div class="user-menu nocopy<?php if($typeuser==9){echo " hover";}?>">提交工单</div></a>
<a href="?type=10" ><div class="user-menu nocopy<?php if($typeuser==10){echo " hover";}?>">发布投稿</div></a>
</div>
<div class="user-right">
<?php
if ($typeuser==1){
@include __DIR__.'/myuser/myvip.php';//我的会员
}else if($typeuser==2){
@include __DIR__.'/myuser/my.php';//我的资料
}else if($typeuser==3){
@include __DIR__.'/myuser/myrow.php';//我的文章
}else if($typeuser==4){
@include __DIR__.'/myuser/mycomment.php';//我的评论
}else if($typeuser==5){
@include __DIR__.'/myuser/mycollect.php';//我的收藏
}else if($typeuser==6){
@include __DIR__.'/myuser/myfans.php';//我的粉丝
}else if($typeuser==7){
@include __DIR__.'/myuser/myfollow.php';//我的关注
}else if($typeuser==8){
@include __DIR__.'/myuser/mywork.php';//我的工单
}else if($typeuser==9){
@include __DIR__.'/myuser/postwork.php';//提交工单
}else if($typeuser==10){
@include __DIR__.'/myuser/post.php';//投稿
}else if($typeuser==11){
  @include __DIR__.'/myuser/mybuy.php';//我的购买
}else{
echo "<div class='err'>错误操作</div>";
}
?>
</div>
</div>
<?php @include $_SERVER['DOCUMENT_ROOT'].'/inc/footer.php';?>
<?php @include $_SERVER['DOCUMENT_ROOT'].'/wap/index.php';?>
</body>
</html>
<?php } ?>