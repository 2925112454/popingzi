<?php
include $_SERVER['DOCUMENT_ROOT'].'/inc/inc.php';//通用
if ($ppzusername == "" || $ppzusername == null || $ppzusername == "null" || $ppzusername == "undefined" || $ppzusername == 0 || is_null($ppzusername) || empty($ppzusername) || !isset($ppzusername)){
  header("HTTP/1.1 404 Not Found");  
  header("Status: 404 Not Found");
  echo "<script>location.href='/';</script>";
  exit;
}else{
  if (!isset($_GET['type'])) {
    $_GET['type']="";
  }
  $typex=$_GET['type'];
  if($typex==1){
    $type="teruser=0";
    $typet="返回私信列表";
    $typeu="href='/user/message.php'";
    $typtx="通知";
    $typea=0;
    $deltxt="";
    $alltext="";
    $alltzmun="";
    $typertxt="&type=1";
  }else{
    $messql2 = "select * from ppz_letter  WHERE binary teruser=0";//获取通知
    $mesretval2=mysqli_query($conn,$messql2);
    $mesmun2=mysqli_num_rows($mesretval2);//统计通知数量
    if($mesmun2 < 100){
      $mesmun2x=$mesmun2;
    }else{
      $mesmun2x="99+";
    }
    $typertxt="";
    $type="teruser=$allnameid";
    $typet="查看通知";
    $typeu="href='/user/message.php?type=1'";
    $typtx="消息";
    $typea=1;
    $alltext='<button class="mes-but" id="mesallyes">全部已读</button><button class="mes-but" id="mesalldel">全部删除</button>';
    $alltzmun="<i class='alltzmun'>".$mesmun2x."</i>";
  }
  
//获取会员消息信息
$messql = "select * from ppz_letter  WHERE binary $type order by terid desc";//获取私信内容
$mesretval=mysqli_query($conn,$messql);
$mesmun=mysqli_num_rows($mesretval);

//分页
if  (!isset($_GET['p'])) {
  $_GET['p']="";
}
if  (!isset($_POST['p'])) {
  $_POST['p']="";
}
$num_rec_per_page=15; //每页显示数量
$getp=$_GET["p"];//获取GET传参P
$tpx=$_POST["p"];//获取POST传参P

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
$total_pages = ceil($mesmun / $num_rec_per_page);  // 计算总页数
if ($total_pages < $pa){
  $p=1;
  }else{
  $p=$pa; 
}
$start_from = ($p-1) * $num_rec_per_page; 
$messql3 = "select * from ppz_letter  WHERE binary $type order by terid DESC LIMIT $start_from, $num_rec_per_page";//分页展示
$mesretval3=mysqli_query($conn,$messql3);
?>
<!DOCTYPE html>
<html lang="zh-CN" translate="no">
<head>
<meta charset="utf-8">
<title>我的消息 - <?php echo $webtext;?>丨<?php echo $webby;?></title>
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
<dialog id="eyemesdiv">
  <button id="eyemesdivx"><i class="fa fa-window-close"></i></button>
  <b class="nocopy"><?php echo $typtx;?>内容</b>
  <div id="eyemesdivshow" class="eyemesdivshow"></div>
</dialog>
<div class="body-div">
  <div class="body-left">
    <div class="mes-head"><div class="mes-but-x"><i>丨</i><b><?php echo $typtx;?></b><span>[<?php echo $mesmun;?>条]</span></div>
      <div class="mes-but-div"><?php echo $alltext;?> <a style="grid-column: 3;" <?php echo $typeu;?>><button id="mes-but" class="mes-but" ><?php echo $alltzmun;?><?php echo $typet;?></button></a></div>
    </div>
    <div class="mes-body">
      <?php
      if(mysqli_num_rows($mesretval) < 1){
        echo '<div class="mesnull">暂无任何消息</div>';
      }else{
        echo '<div class="mes-text"><ul>';
          while($mesx = $mesretval3->fetch_array()){
            $mesid=$mesx['terid'];//消息id
            $mestext=$mesx['tertext'];//消息内容
            $mesadmin=$mesx['teradmin'];//发送者
            $mestime=$mesx['tertime'];//私信时间
            $mesyes=$mesx['teryes'];//阅读状态，0为未读，1为已读
            if ($typea==1){
              if ($mesyes==1){
                $mesyestxt="";
                $ttx="未读";
              }else{
                $mesyestxt="<span class='red' id='redmes".$mesid."'>[未读]</span>";
                $ttx="已读";
              }
              $deltxt="<a class='mesdel' data-mesid='".$mesid."'>删除</a><a class='mesyes' data-yesid='".$mesid."'>标记".$ttx."</a>";
            }else{
              $mesyestxt="";
            }
          

            //获取发送者信息
            $mesadminsql = "select * from ppz_newusername where binary uid='$mesadmin'";
            $mesadminretval=mysqli_query($conn,$mesadminsql);
            if(mysqli_num_rows($mesadminretval) !== 1){
              $mesadminname="未知用户";
            }else{
              $mesadminquery = $conn->query($mesadminsql);
              while($mesadminer = $mesadminquery->fetch_array()){
                $mesadminname=$mesadminer['uname'];
              }
            }
            //时间格式转换
            $mestimex = date("Y年m月d日",strtotime($mestime));
  
            echo "
            <li><a class='eyemes' data-mesidr='".$mesid."' data-mestext='".$mestext."' ><span class='mesnot'>".$mesyestxt."<span>".mb_substr($mestext, 0,60,'UTF-8')."</span></span></a><span class='mesname'><a href='/user.php?id=".$mesadmin."' target='_blank'><i class='fa fa-user'></i>".$mesadminname."</a><i>".$mestimex."</i>".$deltxt."</span></li>
            ";
          }
        echo '</ul>';?>
        <div class="page" style="box-shadow:none;">
        <div class="page-left">第<?php echo $p;?>页 / 共<?php echo $total_pages;?>页<div class="tpage"><form name="page" onsubmit="return checkformpage()" method="post" ><input name="p" placeholder="跳页"/></form></div></div>
          <div class="page-right">
          <a href="?p=1<?php echo $typertxt;?>" class="<?php if ($p==1 || $p < 1){echo "page-no-button nocopy";}else{echo "page-button nocopy";}?>">首页</a>
          <a <?php if ($p==1 || $p < 1){}else{echo "href='?p=".($p-1)."".$typertxt."'";}?> class="<?php if ($p==1 || $p < 1){echo "page-no-button nocopy";}else{echo "page-button nocopy";}?>">上一页</a>
          <a <?php if ($total_pages<$p+1){}else{echo "href='?p=".($p+1)."".$typertxt."'";}?> class="<?php if ($total_pages<$p+1){echo "page-no-button nocopy";}else{echo "page-button nocopy";}?>">下一页</a>
          <a <?php if ($total_pages<$p+1){}else{echo "href='?p=".$total_pages."".$typertxt."'";} ?> class="<?php if ($total_pages<$p+1){echo "page-no-button nocopy";}else{echo "page-button nocopy";}?>">尾页</a>
          </div>
      </div>
<?php     
      echo '</div><script src="/style/js/eyemes.js" type="text/javascript"></script>';
        if ($typea==1){
          echo "<script src='/style/js/mesdel.js' type='text/javascript'></script>";
        }
      }
?>
    </div>

  </div>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/inc/right.php';?></div>
</div>
<?php include $_SERVER['DOCUMENT_ROOT'].'/inc/footer.php';?>
<?php 
  if ($ppzusername == "" || $ppzusername == null || is_null($ppzusername)){ echo '<script src="/style/js/login.js" type="text/javascript"></script>';} 
?>
<?php @include $_SERVER['DOCUMENT_ROOT'].'/wap/index.php';?>
</body>
</html>
<?php } ?>