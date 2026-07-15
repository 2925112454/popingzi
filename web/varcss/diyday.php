<?php
header("Content-type: text/css; charset=utf-8");//输出为css
include $_SERVER['DOCUMENT_ROOT'].'/inc/conn.php';//链接数据库配置文件
//链接数据库ppz_diy
$diy_sqlert = "SELECT * FROM ppz_diy WHERE diyid=1";
$diy_resultaert = mysqli_query($conn,$diy_sqlert);
$diysizeert = mysqli_num_rows($diy_resultaert);
if ($diysizeert>0){
    while($diyert=mysqli_fetch_assoc($diy_resultaert)){
        $dayert=$diyert['day'];//自定义白天模式样式的自定义css样式，自定义白天模式设置为3时生效
        echo ':root{'.$dayert.'}';//输出自定义css样式
    }
}
?>