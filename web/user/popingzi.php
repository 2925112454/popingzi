<?php
$admin=1;
@include $_SERVER['DOCUMENT_ROOT'].'/inc/inc.php';//通用
if (empty($ppzusername)|| $allvip==1){
  header("HTTP/1.1 404 Not Found");  
  header("Status: 404 Not Found");
  echo "<script>location.href='/';</script>";
  exit;
}else{
if ($allvip==4 || $allvip==3 || $allvip==2){
  if  (!isset($_GET['type'])) {
    $_GET['type']="";
  }
$typeuserx=$_GET["type"];
  if (($typeuserx==0 || $typeuserx==1 || $typeuserx==2 || $typeuserx==3 || $typeuserx==4 || $typeuserx==5 || $typeuserx==6 || $typeuserx==7 || $typeuserx==8 || $typeuserx==9 || $typeuserx==10 || $typeuserx==11 || $typeuserx==12 || $typeuserx==13 || $typeuserx==14 || $typeuserx==15 || $typeuserx==16) && !is_null($typeuserx)&&$typeuserx!==""){
    $typeuser=$typeuserx;
  }else{

    if ($allvip==2){
      $typeuser=12;
    }else{
      if($allvip==3 || $allvip==4){
        $typeuser=0;
      }else{
        $typeuser="";
      }
    }

  }
        //待审核的话题数量
        $sub_total_wait = 0;
        $sub_total_wait_sql = "SELECT COUNT(yes) AS totalwait FROM `ppz_subject` WHERE `yes` = 1";
        $sub_total_wait_query = mysqli_query($conn,$sub_total_wait_sql);
        if ($sub_total_wait_query && mysqli_num_rows($sub_total_wait_query) > 0) {
            $sub_total_wait_row = mysqli_fetch_assoc($sub_total_wait_query);
            $sub_total_wait = $sub_total_wait_row['totalwait'];
        }
        if($sub_total_wait>99):
            $sub_total_waitx = "99+";
        else:
            $sub_total_waitx = $sub_total_wait;
        endif;
?>
<!DOCTYPE html>
<html lang="zh-CN" translate="no">
<head>
<meta charset="utf-8">
<title>后台管理 - <?php echo $webtext;?>丨<?php echo $webby;?></title>
<meta name="keywords" content="<?php echo $webpass;?>" />
<meta name="description" content="<?php echo $webvar;?>" />
<link rel="icon" href="/favicon.ico"/>
<?php @include $_SERVER['DOCUMENT_ROOT'].'/inc/style.php';?>
<link type="text/css" rel="stylesheet" href="/style/css/font-awesome-4.7.0/css/font-awesome.min.css">
<script src="/style/js/jquery-3.5.1.min.js" type="text/javascript"></script>
<script src="/style/js/input.js" type="text/javascript"></script>
<script src="/style/js/alert.js" type="text/javascript"></script>
</head>
<body>
<?php
@include $_SERVER['DOCUMENT_ROOT'].'/inc/header.php';//头部
?>
<div class="user-all">
<div class="user-left adminbg">
  <?php if ($allvip==4 || $allvip==3){?><a href="?type=0" ><div class="user-menu nocopy<?php if($typeuser==0){echo " hover";}?>"><i class="fa fa-home"></i> 控 制 台</div></a><?php }; ?>
  <?php if ($allvip==4){?><a href="?type=1" ><div class="user-menu nocopy<?php if($typeuser==1){echo " hover";}?>"><i class="fa fa-cog"></i>网站设置</div></a><?php }; ?>
<?php if ($allvip==4){?><a href="?type=2" ><div class="user-menu nocopy<?php if($typeuser==2){echo " hover";}?>"><i class="fa fa-navicon"></i>分类导航</div></a><?php }; ?>
<?php if ($allvip==4 || $allvip==3 || $allvip==2){?><a href="?type=12" ><div class="user-menu nocopy<?php if($typeuser==12){echo " hover";}?>"><i class="fa fa-pencil-square"></i>发布内容</div></a><?php }; ?>
<?php if ($allvip==4 || $allvip==3 || $allvip==2){?><a href="?type=3" ><div class="user-menu nocopy<?php if($typeuser==3){echo " hover";}?>"><i class="fa fa-folder-open"></i>文章列表</div></a><?php }; ?>
<?php if ($allvip==4 || $allvip==3){?><a href="?type=4" ><div class="user-menu nocopy<?php if($typeuser==4){echo " hover";}?>"><i class="fa fa-address-book"></i>用户管理</div></a><?php }; ?>
<?php if ($allvip==4 || $allvip==3 || $allvip==2){?><a href="?type=5" ><div class="user-menu nocopy<?php if($typeuser==5){echo " hover";}?>"><i class="fa fa-comments"></i>评论管理</div></a><?php }; ?>
<?php if ($allvip==4 || $allvip==3 || $allvip==2){?><a href="?type=6" ><div class="user-menu nocopy<?php if($typeuser==6){echo " hover";}?>"><i class="fa fa-commenting"></i>私信管理</div></a><?php }; ?>
<?php if ($allvip==4 || $allvip==3 || $allvip==2){?><a href="?type=7" ><div class="user-menu nocopy<?php if($typeuser==7){echo " hover";}?>"><i class="fa fa-suitcase"></i>工单管理</div></a><?php }; ?>
<?php if ($allvip==4 || $allvip==3 || $allvip==2){?><a href="?type=16" ><div class="user-menu nocopy<?php if($typeuser==16){echo " hover";}?>"><i class="fa fa-coffee"></i>话题管理<?php if($sub_total_wait>0){echo '<div class="submessdiv">'.$sub_total_waitx.'</div>';};?></div></a><?php }; ?>
<?php if ($allvip==4 || $allvip==3){?><a href="?type=8" ><div class="user-menu nocopy<?php if($typeuser==8){echo " hover";}?>"><i class="fa fa-credit-card-alt"></i> 充 值 卡</div></a><?php }; ?>
<?php if ($allvip==4 || $allvip==3){?><a href="?type=15" ><div class="user-menu nocopy<?php if($typeuser==15){echo " hover";}?>"><i class="fa fa-id-card"></i> 邀 请 码</div></a><?php }; ?>
<?php if ($allvip==4 || $allvip==3){?><a href="?type=9" ><div class="user-menu nocopy<?php if($typeuser==9){echo " hover";}?>"><i class="fa fa-file-text"></i>平台公告</div></a><?php }; ?>
<?php if ($allvip==4){?><a href="?type=13" ><div class="user-menu nocopy<?php if($typeuser==13){echo " hover";}?>"><i class="fa fa-jpy"></i>积分记录</div></a><?php }; ?>
<?php if ($allvip==4||$allvip==3){?><a href="?type=14" ><div class="user-menu nocopy<?php if($typeuser==14){echo " hover";}?>"><i class="fa fa-shield"></i>广告管理</div></a><?php }; ?>
<?php if ($allvip==4){?><a href="?type=10" ><div class="user-menu nocopy<?php if($typeuser==10){echo " hover";}?>"><i class="fa fa-envelope"></i>邮件短信</div></a><?php }; ?>
<?php if ($allvip==4 || $allvip==3){?><a href="?type=11" ><div class="user-menu nocopy<?php if($typeuser==11){echo " hover";}?>"><i class="fa fa-map"></i>网站SEO</div></a><?php }; ?>
</div>
<div class="user-right">
<?php
if ($admin==1 && $typeuser==0 && ($allvip==4 || $allvip==3)){
@include("admin/index.php");//引入控制面板首页文件
}else if ($typeuser==1 && $allvip==4){
@include("admin/web.php");//引入网站设置文件
}else if ($typeuser==14 && ($allvip==4|| $allvip==3)){
  @include("admin/ads.php");//引入广告管理文件
}else if($typeuser==2 && $allvip==4){
  @include("admin/navbar.php");//引入分类设置文件
}else if($typeuser==3 && ($allvip==4 || $allvip==3 || $allvip==2)){
  @include("admin/list.php");//引入列表文件
}else if($typeuser==4 && ($allvip==4 || $allvip==3)){
  @include("admin/useradmin.php");//引入会员管理文件
}else if($typeuser==5 && ($allvip==4 || $allvip==3 || $allvip==2)){
  @include("admin/comment.php");//引入评论管理文件
}else if($typeuser==6 && ($allvip==4 || $allvip==3 || $allvip==2)){
  @include("admin/message.php");//引入私信管理文件
}else if($typeuser==7 && ($allvip==4 || $allvip==3 || $allvip==2)){
  @include("admin/service.php");//引入工单管理文件
}else if($typeuser==8 && ($allvip==4 || $allvip==3)){
  @include("admin/usercard.php");//引入充值卡列表文件
}else if($typeuser==9 && ($allvip==4 || $allvip==3)){
  @include("admin/notice.php");//引入公告列表文件
}else if($typeuser==10 && $allvip==4){
  @include("admin/emailset.php");//引入邮件和短信的配置文件
}else if($typeuser==11 && ($allvip==4 || $allvip==3)){
  @include("admin/map.php");//引入地图生生成文件
}else if($typeuser==12 && ($allvip==4 || $allvip==3 || $allvip==2)){
  @include("admin/newrow.php");//引入发布文章文件
}else if($typeuser==13 && ($allvip==4 || $allvip==3)){
  @include("admin/log.php");//引入积分记录文件
}else if($typeuser==15 && ($allvip==4 || $allvip==3)){
  @include("admin/regcode.php");//引入邀请码管理文件
}else if($typeuser==16 && ($allvip==4 || $allvip==3 || $allvip==2)){
  include("admin/subiect.php");//引入话题管理文件
}else{
echo "<div class='err'>错误操作</div>";
}
?>
</div>
</div>
<?php @include $_SERVER['DOCUMENT_ROOT'].'/inc/footer.php';?>
</body>
</html>
<?php
}else{
  header("HTTP/1.1 404 Not Found");  
  header("Status: 404 Not Found");
  echo "<script>location.href='/';</script>";
  exit;
} 
} ?>