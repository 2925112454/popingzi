<?php
//链接数据库ppz_diy
$diy_sqlert = "SELECT * FROM ppz_diy WHERE diyid=1";
$diy_resultaert = mysqli_query($conn,$diy_sqlert);
$diysizeert = mysqli_num_rows($diy_resultaert);
//获取cookie值
if(isset($_COOKIE['style'])){
  $cookie_style= trim($_COOKIE['style']);
}else{
  $cookie_style=0;
}


if ($diysizeert>0){
  while($diyert=mysqli_fetch_assoc($diy_resultaert)){
      $diyindexert=$diyert['diyindex'];//首页版面，1为普通，2为小轮播图，3为大轮播图
      $diydayert=$diyert['diyday'];//自定义白天模式，1为样式1，2为样式2，3为自定义样式
      $diynightert=$diyert['diynight'];//自定义夜间模式，1为样式1，2为样式2，3为自定义样式
      $dayert=$diyert['day'];//自定义白天模式样式的自定义css样式，自定义白天模式设置为3时生效
      $nightert=$diyert['night'];//自定义夜间模式样式的自定义css样式，自定义夜间模式设置为3时生效
      $imageert=$diyert['image'];//自定义轮播图，JSON格式，包含图片及超链接；轮播图模式设置为6时生效
      $carouselert=$diyert['carousel'];//轮播图模式，1为加“热门”内容，2为加"精华"内容，3为加"置顶"内容，4为自动最新内容，5为自动最高阅览量内容，6为自定义内容
    }

    if($diydayert==1){
      $daystylecss='/style/css/day1.css';
    }else if($diydayert==2){
      $daystylecss='/style/css/day2.css';
    }else if($diydayert==3){
      $daystylecss='/varcss/diyday.php';
    }else{
      $daystylecss='/style/css/day1.css';
    }

    if($diynightert==1){
      $nightstylecss = '/style/css/night1.css';
    }else if($diynightert==2){
      $nightstylecss = '/style/css/night2.css';
    }else if($diynightert==3){
      $nightstylecss = '/varcss/diynight.php';
    }else{
      $nightstylecss = '/style/css/night1.css';
    }

    if($cookie_style==0){
      echo '<link type="text/css" rel="stylesheet" id="diystylecss" href="'.$daystylecss.'">';
    }else{
      echo '<link type="text/css" rel="stylesheet" id="diystylecss" href="'.$nightstylecss.'">';
    }


}
echo '
<script>
var daystyle = "'.$daystylecss.'";
var nightstyle = "'.$nightstylecss.'";
</script>
<link rel="stylesheet" href="/style/css/style.css">  
<link rel="preload" href="/style/js/diystyle.js" as="script">  
<script src="/style/js/diystyle.js"></script>
';
?>