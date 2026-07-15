<!DOCTYPE html>
<html lang="zh-CN" translate="no">
<head>
<?php
include __DIR__.'/inc/inc.php';//通用
if (isset($_GET['id'])) {
  $ids=trim($_GET['id']);//获取会员id
}else{
  $ids="";
}
if (isset($_GET['p'])) {
  $getp=$_GET["p"];//获取GET传参P
}else{
  $getp="";
}
if (isset($_POST['p'])) {
  $tpx=$_POST["p"];//获取POST传参P
}else{
  $tpx="";
}

if (is_numeric($ids) && $ids > 0 &&!empty($ids)) {
  $id=$ids;
}else{
  $id=0;
}

$usersql = "select * from ppz_newusername where uid = $id";//获取会员信息
$userretval=mysqli_query($conn,$usersql);
if(mysqli_num_rows($userretval) !== 1){
  $useryes=0;
  $uidlove="";
  $unamelove="~找不到相关会员~";
}else{
  $currentUrl = "http";  
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {  
      $currentUrl .= "s";  
  }  
  $currentUrl .= "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; //获取当前网址

  $useryes=1;
  $userquery = $conn->query($usersql);
  while($user = $userquery->fetch_array()){
    $uidlove=$user['uid'];//id
    $unamelove=$user['uname'];//昵称
    $uimglove=$user['uimg'];//头像
    $upersonallove=$user['upersonal'];//简介
    $usexlove=$user['usex'];//性别,1男，2女
    $ustatuslove=$user['ustatus'];//身份：1普通会员，2为管理员，3为副站长，4为站长
    $ugoldlove=$user['ugold'];//积分
    $utimelove=$user['utime'];//注册时间
    $uurllove=$user['uurl'];//网址
    $ubanlove=$user['uban'];//账号状态，1为正常，反之为异常
    $udatedaylove=$user['udateday'];//签到天数
    $ucollectlover=$user['ucollect'];//收藏
  };
//判断会员性别
  if($usexlove==1){
    $sextxt="♂帅哥";
    $sexch="他";
  }else{
    $sextxt="♀美女";
    $sexch="她";
  }
//判断会员身份
  if($ustatuslove==4){
    $vipid="站长";
  }else if($ustatuslove==3){
    $vipid="副站长";
  }else if($ustatuslove==2){
    $vipid="管理员";
  }else{
    $vipid="注册会员";
  }
//判断是否有头像
  if($uimglove=="" || $uimglove==null || is_null($uimglove) || empty($uimglove) || !isset($uimglove)){
    $userimg="/images/web/default.jpg";
  }else{
    $userimg=$uimglove;
  }
//是否有简介
  if($upersonallove==""||$upersonallove==null||is_null($upersonallove)||empty($upersonallove)||!isset($upersonallove)){
      $textjj="这个人很懒，什么都没有留下！";
  }else{
    $textjj=$upersonallove;
  }
//是否有网址
  if($uurllove==""||$uurllove==null||is_null($uurllove)){
    $urlint="没有网址";
  }else{
    $urlint=$uurllove;
  }
//统计加入本站的时间(天数)
$zcnewtime = time() - strtotime($utimelove);//时间差
$zcdays = floor($zcnewtime / (60 * 60 * 24));//转换为天数
if($zcdays<=0){
  $timextxt=0.5;
}else{
  $timextxt=$zcdays;
}
//获取评论数量
$sqlplalove = "SELECT * FROM ppz_commentary where pladmin = $uidlove"; //链接评论数据表
$pla_resultalove = mysqli_query($conn,$sqlplalove); //查询评论数据
$pla_recordsxlove = mysqli_num_rows($pla_resultalove);  // 统计评论总数
$sqlplagglove = "SELECT * FROM ppz_ggcommentary where pladmin = $uidlove"; //链接公告评论数据表
$pla_resultagglove = mysqli_query($conn,$sqlplagglove); //查询公告评论数据
$pla_recordsgglove = mysqli_num_rows($pla_resultagglove);  // 统计公告评论总数
$pla_recordslove = $pla_recordsxlove+$pla_recordsgglove;//总评论数
//获取关注数量
$sqlfoluslove = "SELECT * FROM ppz_folus where usvip = $uidlove"; //链接关注数据表
$folusresultalove = mysqli_query($conn,$sqlfoluslove); //查询关注数据
$folusrecordslove = mysqli_num_rows($folusresultalove);  // 统计关注总数
//获取粉丝数量
$sqlfolus2love = "SELECT * FROM ppz_folus where usuename = $uidlove"; //链接粉丝数据表
$folusresulta2love = mysqli_query($conn,$sqlfolus2love); //查询粉丝数据
$folusrecords2love = mysqli_num_rows($folusresulta2love);  // 统计粉丝总数
//获取发贴数量
$sqlrowadminlove = "SELECT * FROM ppz_row where rowyes = 4 AND rowadmin = $uidlove"; //链接文章数据表
$rowadmin_resultalove = mysqli_query($conn,$sqlrowadminlove); //查询文章数据
$rowadmin_recordslove = mysqli_num_rows($rowadmin_resultalove);  // 统计文章总数
//获取收藏数量
if ($ucollectlover==""||$ucollectlover==null||is_null($ucollectlover)||empty($ucollectlover)||!isset($ucollectlover)){
$ucollectlove=0;
}else{
$ucollectlovearr=explode('|',$ucollectlover);//转换数组
$ucollectlove=count($ucollectlovearr);//计算数量
}
}
?>
<meta charset="utf-8">
<title><?php echo $unamelove; ?>的主页 - <?php echo $webtext;?>丨<?php echo $webby;?></title>
<meta name="keywords" content="<?php echo $webpass;?>" />
<meta name="description" content="<?php echo $webvar;?>" />
<link rel="icon" href="/favicon.ico"/>
<?php include __DIR__.'/inc/style.php';?>
<link type="text/css" rel="stylesheet" href="/style/css/font-awesome-4.7.0/css/font-awesome.min.css">
<script src="/style/js/jquery-3.5.1.min.js" type="text/javascript"></script>
<script src="/style/js/input.js" type="text/javascript"></script>
<script src="/style/js/alert.js" type="text/javascript"></script>
</head>
<body>
<?php
include __DIR__.'/inc/header.php';//头部
if (isset($_GET['t'])) {
  $txx=$_GET['t'];//获取栏目
}else{
  $txx="";
}

if($txx==1||$txx==2||$txx==3||$txx==4||$txx==5){
$t=$txx;
}else{
$t=1;
}

if ($ppzusername == "" || $ppzusername == null || $ppzusername == "null" || $ppzusername == "undefined" || $ppzusername == 0 || is_null($ppzusername) || empty($ppzusername) || !isset($ppzusername)){
  $tlogs1='onclick="loginFunction()"';
  $tlogs2='onclick="loginFunction()"';  //判断是否登录
  $tloga="";
  $tlogt="关注";
}else{
if($allnameid==$uidlove){ //判断是否是自己
  $tlogs1='onclick="iandiFunction()"';
  $tlogs2='onclick="iandiFunction()"';
  $tloga="";
  $tlogt="关注";
}else{
  if($useryes==1){
  $vipsql = "select * from ppz_newusername  WHERE binary uusername=$ppzusername";//获取登录会员信息
  $vipretval=mysqli_query($conn,$vipsql);
  if(mysqli_num_rows($vipretval) !== 1){ 
  function redirect($url){  
  if (ob_get_contents()) {  
      ob_clean();  
  }  
  header('Location: ' . $url);  
  exit;  
  }  
redirect('/inc/loginout.php');  
  }else{

    $liqxasa = $conn->query($vipsql);
    while($lixasa = $liqxasa ->fetch_array()){
        $idxasa=$lixasa['uid'];//登录会员id
    };

    $vipsqlfs = "select * from ppz_folus  WHERE binary usvip=$idxasa AND binary usuename=$uidlove";//获取登录会员关注信，，判断是否关注了当前用户
    $vipretvalfs=mysqli_query($conn,$vipsqlfs);
    if(mysqli_num_rows($vipretvalfs) < 1){
      $tlogs1="data-fid='".$uidlove."'";
      $tlogs2="data-id='".$uidlove."'";
      $tloga='
      <dialog id="lettertext" class="lettertext" >
      <button id="letterx"><i class="fa fa-times"></i></button>
      <form id="lettertextform" method="post">  
        <div class="input-all"><label class="label-sign" for="lettertext"><input type="text" id="lettertextinput" name="letter" required="">  <span><b>私信内容</b></span> </label> </div>
        <input class="sign-inp" type="submit" value="发送" id="fsipbut">
      </form>
      <span id="lettertextx" class="lettertextx"></span>
      </dialog>
      <script src="/style/js/myfolus.js" type="text/javascript"></script>
      ';
      $tlogt="关注";
    }else{
      $tlogs1="data-fid='".$uidlove."'";
      $tlogs2="data-id='".$uidlove."'";
      $tloga='
      <dialog id="lettertext" class="lettertext" >
      <button id="letterx"><i class="fa fa-times"></i></button>
      <form id="lettertextform" method="post">  
        <div class="input-all"><label class="label-sign" for="lettertext"><input type="text" id="lettertextinput" name="letter" required="">  <span><b>私信内容</b></span> </label> </div>
        <input class="sign-inp" type="submit" value="发送" id="fsipbut">
      </form>
      <span id="lettertextx" class="lettertextx"></span>
      </dialog>
      <script src="/style/js/myfolus.js" type="text/javascript"></script>
     ';
      $tlogt="已关注";
    }

  }
}
}
}

?>
<div class="body-divx">
  <?php if($useryes==1){?>
<div class="user">
<div class="user-img"><div class="user-but"><button id="folus" class="folusbut" <?php echo $tlogs1;?>><i class="fa fa-plus"></i><?php echo $tlogt;?></button><button id="letter" <?php echo $tlogs2;?>><i class="fa fa-envelope-o"></i>私信</button></div></div>
<div class="user-text">
  <div class="user-head" style="background-image:url('<?php echo $userimg;?>');"></div>
  <div class="user-name"><p><b><?php echo $unamelove;?></b><span class="user-sex"><?php echo $sextxt;?></span><span class="user-vipid"><?php echo $vipid;?></span><?php if(($ubanlove!==1&&$ubanlove!=='1')&&$ubanlove!==""&&$ubanlove!==null&&!is_null($ubanlove)){echo "<i>该账号已被封禁</i>";}?></p><p class="user-tjj"><?php echo $textjj;?></p></div>
</div>
</div> 
<?php echo $tloga;?>
<div class="userx">
<div class="userx-left">
  <ul>
    <a href="?id=<?php echo $id;?>"><li <?php if($t==1||$t==0||$t==""||$t==null||is_null($t)){echo 'class="activeli"';}?>><?php echo $sexch;?>的概况<span><i class="fa fa-angle-right"></i></span></li></a>
    <a href="?id=<?php echo $id;?>&t=2"><li <?php if($t==2){echo 'class="activeli"';}?>><?php echo $sexch;?>的发布<span><i class="fa fa-angle-right"></i></span></li></a>
    <a href="?id=<?php echo $id;?>&t=3"><li <?php if($t==3){echo 'class="activeli"';}?>><?php echo $sexch;?>的粉丝<span><i class="fa fa-angle-right"></i></span></li></a>
    <a href="?id=<?php echo $id;?>&t=4"><li <?php if($t==4){echo 'class="activeli"';}?>><?php echo $sexch;?>的关注<span><i class="fa fa-angle-right"></i></span></li></a>
    <a href="?id=<?php echo $id;?>&t=5"><li <?php if($t==5){echo 'class="activeli"';}?>><?php echo $sexch;?>的评论<span><i class="fa fa-angle-right"></i></span></li></a>
</ul>
</div>

<div class="userx-right">
<?php

if($t==1){
  echo "<ul class='name-vip'>
  <li><b>昵称：</b>".$unamelove."</li>
  <li><b>时间：</b>".$sexch."加入本站<span>".$timextxt."</span>天了</li>
  <li><b>性别：</b>".$sextxt."</li>
  <li><b>网址：</b>".$urlint."</li>
  <li><b>简介：</b>".$textjj."</li>
  <li><b>主页：</b>".$currentUrl."</li>
  </ul>
  <div class='user-statistics'>
  <div><p>发布</p><b>".$rowadmin_recordslove."</b><span>".$sexch."发布的文章数量</span></div>
  <div><p>评论</p><b>".$pla_recordslove."</b><span>".$sexch."评论的数量</span></div>
  <div><p>关注</p><b>".$folusrecordslove."</b><span>".$sexch."关注的人数</span></div>
  <div><p>粉丝</p><b>".$folusrecords2love."</b><span>".$sexch."粉丝的数量</span></div>
  <div><p>收藏</p><b>".$ucollectlove."</b><span>".$sexch."收藏的数量</span></div>
  <div><p>签到</p><b>".$udatedaylove."</b><span>".$sexch."连续签到的天数</span></div>
  </div>
  ";
}else if($t==2){
  //分页
$num_rec_per_page=12;   // 每页显示数量
/*判断参数P是否为空，且是否是数字*/
if (isset($tpx) && is_numeric($tpx) && $tpx>=1 && !is_null($tpx) ){ 
  $pa = $_POST["p"];
} else { 
  if (isset($getp) && is_numeric($getp) && $getp>=1 && !is_null($getp)){
      $pa = $_GET["p"];
  }else{
      $pa=1; 
  }
}; 
// 计算总页数
$total_pages = ceil($rowadmin_recordslove / $num_rec_per_page); 
if ($total_pages < $pa){
  $p=1;
  }else{
  $p=$pa; 
}
$start_from = ($p-1) * $num_rec_per_page;

if($rowadmin_recordslove <= 0){ 
echo "<div class='nulldiv2'>空空如也~</div>";
}else{
  $rowsqllove = "SELECT * FROM ppz_row where rowyes = 4 AND rowadmin = $uidlove ORDER BY rowid DESC LIMIT $start_from, $num_rec_per_page";
  $rowretvallove=mysqli_query($conn,$rowsqllove);
  if(mysqli_num_rows($rowretvallove) < 1){ 
   $rownull=1; //没有文章
  }else{
   $rownull=2;//有文章
  };

echo "<ul class='name-row'>";

if ($rownull==2){
  $rowquerylove = $conn->query($rowsqllove);
  while($lrowlove = $rowquerylove->fetch_array()){
    $widlove=$lrowlove['rowid'];//文章id
    $wtxtlove=$lrowlove['rowtexe'];//标题
    $wiflove=$lrowlove['rowif'];//文章类型，1图文，2相册，3视频
    $wimglove=$lrowlove['rowimg'];//封面
    $wfllove=$lrowlove['rowfl'];//分类
    $roweyelove=$lrowlove['roweye'];//阅览量

    if ($wimglove=="" || is_null($wimglove) || $wimglove == null ){ //判断是否含有文章封面，没有则选用文章内第一张图片作为封面
      if ($wiflove==2){ //判断是否是相册类型，相册类型则选数组第一张图片

          if ($lrowlove['rowbigtext']==''||$lrowlove['rowbigtext']==null||is_null($lrowlove['rowbigtext'])){
              $rowimglove = "/images/web/null.jpg"; 
          }else{
              $rowimgg=$lrowlove['rowbigtext'];
              $arrayrowf = explode("|",$rowimgg); 
              $firstValue = null;   
              foreach ($arrayrowf as $value) {  
                  if ($firstValue === null) {  
                      $firstValue = $value;  
                  }  
                  break; }
              $rowimglove=$firstValue;
          }
   
      }else{
          // 使用正则表达式提取图片 URL
          $pattern = '/<img[^>]+src="([^"]+)"/i';
          if (preg_match($pattern, $lrowlove['rowbigtext'], $matches)) {
              $imageUrl = $matches[1];
              $rowimglove = $imageUrl; // 如果匹配到图片 URL，则使用该 URL
          } else {
              $rowimglove = "/images/web/null.jpg";  // 如果没有匹配到图片 URL，则使用默认图片
          }
      }
       }else{
      $rowimglove = $wimglove;
       }

$liflsql2 = "select * from ppz_fl where flid = $wfllove"; //查询分类
$liflretval2=mysqli_query($conn,$liflsql2);
if(mysqli_num_rows($liflretval2) !== 1){ 
    $flid2 =0;
    $fllinkid = 1;
    $flnamet = "未知分类"; 
}else{
    $liflq2 = $conn->query($liflsql2);
    while($lifl2 = $liflq2 ->fetch_array()){
    $flid2=$lifl2['flid'];
    $fllinkid=$lifl2['fllinkid'];
    $flnamet=$lifl2['flname'];
    };
}

$sqlpllove = "SELECT * FROM ppz_commentary where plrowid = $widlove"; //链接评论数据表
$pl_resultlove = mysqli_query($conn,$sqlpllove); //查询评论数据
$pl_recordslove = mysqli_num_rows($pl_resultlove);  // 统计评论总数

echo"
<li>
<div class='user-d-img' style='background:url(".$rowimglove.");background-repeat: no-repeat;background-size: 100%;background-position: center;'>
<a href='/show.php?id=".$widlove."' target='_blank'></a>
</div>
<div class='user-d-text'><a href='/show.php?id=".$widlove."' target='_blank'>".$wtxtlove."</a></div>
<div class='user-d-row'>
<a href='/list.php?id=".$fllinkid."&tag=".$flid2."' target='_blank'>".$flnamet."</a>
<div class='user-d-eye'>
<span><i class='fa fa-commenting'></i>".$pl_recordslove."</span>
<span><i class='fa fa-eye'></i>".$roweyelove."</span>
</div>
</div>
</li>
";
}

}else{
  echo "";
}
  
  echo "</ul>";
 
  $p1=$p-1;
  $p2=$p+1;

  //跳页开始
  if ($p>1){
    $aurl1="href='?id=".$uidlove."&t=".$t."&p=1'";
    $abut1="page-button";
  }else{
    $abut1="page-no-button";
  }

  if ($p <= $total_pages && $p>1){
    $aurl2="href='?id=".$uidlove."&t=".$t."&p=".$p1."'";
    $abut2="page-button";
  }else{
    $abut2="page-no-button";
  }

  if ($p < $total_pages && $total_pages>1){
    $aurl3="href='?id=".$uidlove."&t=".$t."&p=".$p2."'";
    $abut3="page-button";
  }else{
    $abut3="page-no-button";
  }

  if ($p < $total_pages && $total_pages>1){
    $aurl4="href='?id=".$uidlove."&t=".$t."&p=".$total_pages."'";
    $abut4="page-button";
  }else{
    $abut4="page-no-button";
  }

  if (!isset($aurl1)) {
    $aurl1="";
  }

  if (!isset($aurl2)) {
    $aurl2="";
  }

  if (!isset($aurl3)) {
    $aurl3="";
  }

  if (!isset($aurl4)) {
    $aurl4="";
  }

  if (!isset($abut1)) {
    $abut1="";
  }
  if (!isset($abut2)) {
    $abut2="";
  }
  if (!isset($abut3)) {
    $abut3="";
  }
  if (!isset($abut4)) {
    $abut4="";
  }


  echo "
  <div class='page' style='box-shadow:none;'>
  <div class='page-left'>第".$p."页 / 共".$total_pages."页<div class='tpage'><form name='page' onsubmit='return checkformpage()' method='post'><input name='p' placeholder='跳页'></form></div></div>
    <div class='page-right'>
    <a ".$aurl1." class='".$abut1." nocopy'>首页</a>
    <a ".$aurl2." class='".$abut2." nocopy'>上一页</a>
    <a ".$aurl3." class='".$abut3." nocopy'>下一页</a>
    <a ".$aurl4." class='".$abut4." nocopy'>尾页</a>
    </div>
</div>
  ";


}

}else if($t==3){
//分页
$num_rec_per_page2=20;   // 每页显示数量
/*判断参数P是否为空，且是否是数字*/
if (isset($tpx) && is_numeric($tpx) && $tpx>=1 && !is_null($tpx) ){ 
  $pa = $_POST["p"];
} else { 
  if (isset($getp) && is_numeric($getp) && $getp>=1 && !is_null($getp)){
      $pa = $_GET["p"];
  }else{
      $pa=1; 
  }
}; 
// 计算总页数
$total_pages2 = ceil($folusrecords2love / $num_rec_per_page2); 
if ($total_pages2 < $pa){
  $p=1;
  }else{
  $p=$pa; 
}
$start_from2 = ($p-1) * $num_rec_per_page2;
if($folusrecords2love <= 0){ 
echo "<div class='nulldiv2'>空空如也~</div>";
$rownull2=1; //没有粉丝
}else{
  $rowsqllove2 = "SELECT * FROM ppz_folus where usuename = $uidlove ORDER BY usid DESC LIMIT $start_from2, $num_rec_per_page2";
  $rowretvallove2=mysqli_query($conn,$rowsqllove2);
  if(mysqli_num_rows($rowretvallove2) < 1){ 
   $rownull2=1; //没有粉丝
  }else{
   $rownull2=2;//有粉丝
  };
}

if($rownull2 == 2){
  echo "<ul class='name-fs'>";
  $rowquerylove2 = $conn->query($rowsqllove2);
  while($lrowlove2 = $rowquerylove2->fetch_array()){
    $loveid=$lrowlove2['usvip'];//粉丝id
    $fslovesql="select * from ppz_newusername where uid='$loveid'";//获取会员信息
    $fsretvalx=mysqli_query($conn,$fslovesql);
if(mysqli_num_rows($fsretvalx) < 1){ 
  $lovename="该用户已被删除";//粉丝昵称
  $loveimg="";//粉丝头像
  $lovesex=1;//粉丝性别
}else{
  $fslovesqlx = $conn->query($fslovesql);
  while($fsxc = $fslovesqlx->fetch_array()){
  $lovename=$fsxc['uname'];//粉丝昵称
  $loveimg=$fsxc['uimg'];//粉丝头像
  $lovesex=$fsxc['usex'];//粉丝性别
}
}


    if ($lovesex==""||$lovesex==1){
      $lovesexx="♂帅哥";
    }else{
      $lovesexx="♀美女";
    }

    if($loveimg==""||is_null($loveimg)||empty($loveimg)||!isset($loveimg)){
    $loveimgx="/images/web/default.jpg";
    }else{
      $loveimgx=$loveimg;
    }

    echo "<a href='/user.php?id=".$loveid."' target='_blank'><li>
    <div style='background:url(".$loveimgx.");background-repeat: no-repeat;background-size: 100%;background-position: center;'></div>
    <b><p>".$lovename."</p><span>".$lovesexx."</span></b>
    </li></a>";

  }
  echo "</ul>";
  $p3=$p-1;
  $p4=$p+1;

  //跳页开始
  if ($p>1){
    $aurl1="href='?id=".$uidlove."&t=".$t."&p=1'";
    $abut1="page-button";
  }else{
    $abut1="page-no-button";
  }

  if ($p <= $total_pages2 && $p>1){
    $aurl2="href='?id=".$uidlove."&t=".$t."&p=".$p3."'";
    $abut2="page-button";
  }else{
    $abut2="page-no-button";
  }

  if ($p < $total_pages2 && $total_pages2>1){
    $aurl3="href='?id=".$uidlove."&t=".$t."&p=".$p4."'";
    $abut3="page-button";
  }else{
    $abut3="page-no-button";
  }

  if ($p < $total_pages2 && $total_pages2>1){
    $aurl4="href='?id=".$uidlove."&t=".$t."&p=".$total_pages2."'";
    $abut4="page-button";
  }else{
    $abut4="page-no-button";
  }

  if (!isset($aurl1)) {
    $aurl1="";
  }
  if (!isset($aurl2)) {
    $aurl2="";
  }
  if (!isset($aurl3)) {
    $aurl3="";
  }
  if (!isset($aurl4)) {
    $aurl4="";
  }

  if (!isset($abut1)) {
    $abut1="";
  }
  if (!isset($abut2)) {
    $abut2="";
  }
  if (!isset($abut3)) {
    $abut3="";
  }
  if (!isset($abut4)) {
    $abut4="";
  }

  echo "
  <div class='page' style='box-shadow:none;'>
  <div class='page-left'>第".$p."页 / 共".$total_pages2."页<div class='tpage'><form name='page' onsubmit='return checkformpage()' method='post'><input name='p' placeholder='跳页'></form></div></div>
    <div class='page-right'>
    <a ".$aurl1." class='".$abut1." nocopy'>首页</a>
    <a ".$aurl2." class='".$abut2." nocopy'>上一页</a>
    <a ".$aurl3." class='".$abut3." nocopy'>下一页</a>
    <a ".$aurl4." class='".$abut4." nocopy'>尾页</a>
    </div>
</div>
  ";
}else{
  echo "";
}


}else if($t==4){

  //分页
$num_rec_per_page3=20;   // 每页显示数量
/*判断参数P是否为空，且是否是数字*/
if (isset($tpx) && is_numeric($tpx) && $tpx>=1 && !is_null($tpx) ){ 
  $pa = $_POST["p"];
} else { 
  if (isset($getp) && is_numeric($getp) && $getp>=1 && !is_null($getp)){
      $pa = $_GET["p"];
  }else{
      $pa=1; 
  }
}; 
// 计算总页数
$total_pages3 = ceil($folusrecordslove / $num_rec_per_page3); 
if ($total_pages3 < $pa){
  $p=1;
  }else{
  $p=$pa; 
}
$start_from3 = ($p-1) * $num_rec_per_page3;
if($folusrecordslove <= 0){ 
echo "<div class='nulldiv2'>空空如也~</div>";
$rownull2=1; //没有关注
}else{
  $rowsqllove3 = "SELECT * FROM ppz_folus where usvip = $uidlove ORDER BY usid DESC LIMIT $start_from3, $num_rec_per_page3";
  $rowretvallove3=mysqli_query($conn,$rowsqllove3);
  if(mysqli_num_rows($rowretvallove3) < 1){ 
   $rownull2=1; //没有关注
  }else{
   $rownull2=2;//有关注
  };
}

if($rownull2 == 2){
  echo "<ul class='name-fs'>";
  $rowquerylove3 = $conn->query($rowsqllove3);
  while($lrowlove3 = $rowquerylove3->fetch_array()){
    $loveid=$lrowlove3['usuename'];//关注id
    $fslovesql="select * from ppz_newusername where uid='$loveid'";//获取会员信息
    $fsretvalx=mysqli_query($conn,$fslovesql);
if(mysqli_num_rows($fsretvalx) < 1){ 
  $lovename="该用户已被删除";//关注昵称
  $loveimg="";//关注头像
  $lovesex=1;//关注性别
}else{
  $fslovesqlx = $conn->query($fslovesql);
  while($fsxc = $fslovesqlx->fetch_array()){
  $lovename=$fsxc['uname'];//昵称
  $loveimg=$fsxc['uimg'];//头像
  $lovesex=$fsxc['usex'];//性别
}
}


    if ($lovesex==""||$lovesex==1){
      $lovesexx="♂帅哥";
    }else{
      $lovesexx="♀美女";
    }

    if($loveimg==""||is_null($loveimg)||empty($loveimg)||!isset($loveimg)){
    $loveimgx="/images/web/default.jpg";
    }else{
      $loveimgx=$loveimg;
    }

    echo "<a href='/user.php?id=".$loveid."' target='_blank'><li>
    <div style='background:url(".$loveimgx.");background-repeat: no-repeat;background-size: 100%;background-position: center;'></div>
    <b><p>".$lovename."</p><span>".$lovesexx."</span></b>
    </li></a>";

  }
  echo "</ul>";
  $p3=$p-1;
  $p4=$p+1;

  //跳页开始
  if ($p>1){
    $aurl1="href='?id=".$uidlove."&t=".$t."&p=1'";
    $abut1="page-button";
  }else{
    $abut1="page-no-button";
  }

  if ($p <= $total_pages3 && $p>1){
    $aurl2="href='?id=".$uidlove."&t=".$t."&p=".$p3."'";
    $abut2="page-button";
  }else{
    $abut2="page-no-button";
  }

  if ($p < $total_pages3 && $total_pages3>1){
    $aurl3="href='?id=".$uidlove."&t=".$t."&p=".$p4."'";
    $abut3="page-button";
  }else{
    $abut3="page-no-button";
  }

  if ($p < $total_pages3 && $total_pages3>1){
    $aurl4="href='?id=".$uidlove."&t=".$t."&p=".$total_pages2."'";
    $abut4="page-button";
  }else{
    $abut4="page-no-button";
  }

  if (!isset($aurl1)) {
    $aurl1="";
  }
  if (!isset($aurl2)) {
    $aurl2="";
  }
  if (!isset($aurl3)) {
    $aurl3="";
  }
  if (!isset($aurl4)) {
    $aurl4="";
  }

  if (!isset($abut1)) {
    $abut1="";
  }
  if (!isset($abut2)) {
    $abut2="";
  }
  if (!isset($abut3)) {
    $abut3="";
  }
  if (!isset($abut4)) {
    $abut4="";
  }



  echo "
  <div class='page' style='box-shadow:none;'>
  <div class='page-left'>第".$p."页 / 共".$total_pages3."页<div class='tpage'><form name='page' onsubmit='return checkformpage()' method='post'><input name='p' placeholder='跳页'></form></div></div>
    <div class='page-right'>
    <a ".$aurl1." class='".$abut1." nocopy'>首页</a>
    <a ".$aurl2." class='".$abut2." nocopy'>上一页</a>
    <a ".$aurl3." class='".$abut3." nocopy'>下一页</a>
    <a ".$aurl4." class='".$abut4." nocopy'>尾页</a>
    </div>
</div>
  ";
}else{
  echo "";
}

}else if($t==5){
  if ($pla_recordsxlove <= 0){
    echo "<div class='nulldiv2'>空空如也~</div>";
  }else{
//分页
$num_rec_per_page5=20;   // 每页显示数量
/*判断参数P是否为空，且是否是数字*/
if (isset($tpx) && is_numeric($tpx) && $tpx>=1 && !is_null($tpx) ){ 
  $pa = $_POST["p"];
} else { 
  if (isset($getp) && is_numeric($getp) && $getp>=1 && !is_null($getp)){
      $pa = $_GET["p"];
  }else{
      $pa=1; 
  }
}; 
// 计算总页数
$total_pages5 = ceil($pla_recordsxlove / $num_rec_per_page5); 
if ($total_pages5 < $pa){
  $p=1;
  }else{
  $p=$pa; 
}
$start_from5 = ($p-1) * $num_rec_per_page5;
    $sqlplaloves5 = "SELECT * FROM ppz_commentary where pladmin = $uidlove ORDER BY plid DESC LIMIT $start_from5, $num_rec_per_page5"; //链接评论数据表
    $pla_resultaloves5 = mysqli_query($conn,$sqlplaloves5); //查询评论数据
    if(mysqli_num_rows($pla_resultaloves5) < 1){ 
      $rownull5=1; //没有评论
     }else{
      $rownull5=2;//有评论
     };
     if ($rownull5==2){
      echo "<ul class='name-pl'>";
      $liq = $conn->query($sqlplaloves5);
      while($li = $liq ->fetch_array()){
        $plrowid = $li['plrowid'];//评论所属文章id
        $plbigtext = $li['plbigtext'];//评论内容
        $pltime = $li['pltime'];//评论时间
        echo "<a href='show.php?id=".$plrowid."#plall' target='_blank'><li><p>".$plbigtext."</p><span>(".date('Y年m月d日',strtotime($pltime)).")</span></li></a>";
    };
    echo "</ul>";
      //跳页开始
      $p5=$p-1;
      $p6=$p+1;
  if ($p>1){
    $aurl1="href='?id=".$uidlove."&t=".$t."&p=1'";
    $abut1="page-button";
  }else{
    $abut1="page-no-button";
  }

  if ($p <= $total_pages5 && $p>1){
    $aurl2="href='?id=".$uidlove."&t=".$t."&p=".$p5."'";
    $abut2="page-button";
  }else{
    $abut2="page-no-button";
  }

  if ($p < $total_pages5 && $total_pages5>1){
    $aurl3="href='?id=".$uidlove."&t=".$t."&p=".$p6."'";
    $abut3="page-button";
  }else{
    $abut3="page-no-button";
  }

  if ($p < $total_pages5 && $total_pages5>1){
    $aurl4="href='?id=".$uidlove."&t=".$t."&p=".$total_pages5."'";
    $abut4="page-button";
  }else{
    $abut4="page-no-button";
  }

  if (!isset($aurl1)) {
    $aurl1="";
  }
  if (!isset($aurl2)) {
    $aurl2="";
  }
  if (!isset($aurl3)) {
    $aurl3="";
  }
  if (!isset($aurl4)) {
    $aurl4="";
  }

  if (!isset($abut1)) {
    $abut1="";
  }
  if (!isset($abut2)) {
    $abut2="";
  }
  if (!isset($abut3)) {
    $abut3="";
  }
  if (!isset($abut4)) {
    $abut4="";
  }

  echo "
  <div class='page' style='box-shadow:none;'>
  <div class='page-left'>第".$p."页 / 共".$total_pages5."页<div class='tpage'><form name='page' onsubmit='return checkformpage()' method='post'><input name='p' placeholder='跳页'></form></div></div>
    <div class='page-right'>
    <a ".$aurl1." class='".$abut1." nocopy'>首页</a>
    <a ".$aurl2." class='".$abut2." nocopy'>上一页</a>
    <a ".$aurl3." class='".$abut3." nocopy'>下一页</a>
    <a ".$aurl4." class='".$abut4." nocopy'>尾页</a>
    </div>
</div>
  ";
     }else{
      echo "<div class='nulldiv2'>空空如也~</div>";
     }
  }
}
?>
</div>
</div>
<?php }else{echo "<div class='nulldiv'>空空如也~</div>";}?>
</div>
<?php include __DIR__.'/inc/footer.php';?>
<?php 
  if ($ppzusername == "" ){ echo '<script src="/style/js/login.js" type="text/javascript"></script>';} 
?>
</body>
</html>