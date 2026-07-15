<?php
session_start(); // 开始 Session 会话
if(isset($_SESSION['ppzusername'])&&!empty($_SESSION['ppzusername'])){
    $ppzusername = $_SESSION["ppzusername"];
}else{
    $ppzusername = "";
}
?>