<!DOCTYPE html>
<html lang="zh-CN" translate="no">
<head>
<?php
include $_SERVER['DOCUMENT_ROOT'].'/inc/inc.php';//通用

//分页
$num_rec_per_page=10;   // 每页显示数量
if (isset($_GET["p"])){
    $getp=$_GET["p"];//获取GET传参P
}else{
    $getp="";
}

/*判断参数P是否为空，且是否是数字*/

    if (isset($getp) && is_numeric($getp) && $getp>=1 && !is_null($getp)){
        $pa = $_GET["p"];
    }else{
        $pa=1; 
    }

$sqlll = "SELECT * FROM ppz_announcement"; //链接数据表
$rs_result = mysqli_query($conn,$sqlll); //查询数据
$total_records = mysqli_num_rows($rs_result);  // 统计数据总数
$total_pages = ceil($total_records / $num_rec_per_page);  // 计算总页数

if ($total_pages < $pa){
    $p=1;
    }else{
    $p=$pa; 
}

$start_from = ($p-1) * $num_rec_per_page; 

$gsql = "select * from ppz_announcement ORDER BY CASE WHEN ggtop = 2 THEN 0 ELSE 1 END,ggid desc LIMIT $start_from, $num_rec_per_page";//获取公告
$gretval=mysqli_query($conn,$gsql);

if(mysqli_num_rows($gretval) < 1){ 
    $gnull=1; //没有公告
}else{
    $gnull=2;//有公告
};

?>
<meta charset="utf-8">
<title>公告 - <?php echo $webtext;?>丨<?php echo $webby;?></title>
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
echo $adson_js;
?>
<?php echo $adson_hf?>
<div class="body-div">
<div class="body-left">
<?php
if ($gnull==1){
echo '<div class="gnull">暂无公告</div>';
}else if($gnull==2){

    $gquery = $conn->query($gsql);
        while($g = $gquery->fetch_array()){

            if ($g['ggrowid'] !==0 && $g['ggrowid'] !=="" && $g['ggrowid']!== null && !is_null($g['ggrowid'])){
                $wadmin=$g['ggrowid'];
                $uer = "select * from ppz_newusername where binary uid = $wadmin";//查询数据库，判断用户名是否存在
                $uerquery=mysqli_query($conn,$uer);
                if(mysqli_num_rows($uerquery) !== 1){
                }else{
                    $uerqun= $conn->query($uer);
                            while($xrow = $uerqun->fetch_array()){
                            $uername=$xrow['uname'];//作者昵称
                            $uerid=$xrow['uid'];//作者id
                            }
                }
            
            }

            //输出摘要
            $contentxx = strip_tags($g['ggbigtext']); // 先去标签
            $contentxx = html_entity_decode($contentxx, ENT_QUOTES, 'UTF-8'); // 还原引号、破折号
            $ggt = mb_substr(trim($contentxx), 0, 240, 'UTF-8') . "…"; // 截取

            echo '
            <div class="ggdivtop">
            <div class="ggleft"></div>
            <div class="ggright">';
            if ($g['ggtop'] == 2){
                echo '<div class="ggtop">置顶</div>';
            }
            if (!empty($g['ggimg'])){
                echo '<div class="ggimg" style="background-image: url('.$g['ggimg'].');"><a href="anctshow.php?id='.$g['ggid'].'"></a></div>';
            }
            echo '<div class="ggtext"><a href="anctshow.php?id='.$g['ggid'].'">'.$g['ggtext'].'</a></div>
            <div class="ggtime"><span><i class="fa fa-user-circle"></i><a href="/user.php?id='.$uerid.'">'.$uername.'</a></span><span><i class="fa fa-clock-o"></i>'.date('Y年m月d日 H:i',strtotime($g['ggtime'])).'</span></div>
            <div class="ggrow">'.$ggt.'</div>
            <a href="anctshow.php?id='.$g['ggid'].'"><div class="ggnew">查看详情</div></a>
            </div>
            </div>
            ';

        }

}else{
    echo '<div class="gnull">错误参数</div>';
}

?>

<div class="border-page">
            <div class="page">
              <div class="page-left">第<?php echo $p;?>页 / 共<?php echo $total_pages;?>页<div class="tpage"><form name="page" onsubmit="return checkformpage()" method="get" ><input name="p" placeholder="跳页"/></form></div></div>
                <div class="page-right">
                <a href="?p=1" class="<?php if ($p==1 || $p < 1){echo "page-no-button nocopy";}else{echo "page-button nocopy";}?>">首页</a>
                <a <?php if ($p==1 || $p < 1){}else{echo "href='?p=".($p-1)."'";}?> class="<?php if ($p==1 || $p < 1){echo "page-no-button nocopy";}else{echo "page-button nocopy";}?>">上一页</a>
                <a <?php if ($total_pages<$p+1){}else{echo "href='?p=".($p+1)."'";}?> class="<?php if ($total_pages<$p+1){echo "page-no-button nocopy";}else{echo "page-button nocopy";}?>">下一页</a>
                <a <?php if ($total_pages<$p+1){}else{echo "href='?p=".$total_pages."'";} ?> class="<?php if ($total_pages<$p+1){echo "page-no-button nocopy";}else{echo "page-button nocopy";}?>">尾页</a>
                </div>
            </div>
</div>

</div>
<?php include $_SERVER['DOCUMENT_ROOT'].'/inc/right.php';?>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'].'/inc/footer.php';?>
<?php 
  if ($ppzusername == "" ){ echo '<script src="/style/js/login.js" type="text/javascript"></script>';} 
?>
<?php echo $adson_yxj.$adson_left.$adson_right?>
<?php @include $_SERVER['DOCUMENT_ROOT'].'/wap/index.php';?>
</body>
</html>