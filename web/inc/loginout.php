<?php 
session_start(); // 开始 Session 会话
// 清除所有的 Session 变量
$_SESSION = array();
// 销毁服务器端的 Session 文件
session_destroy();
// 进行页面重定向
header("Location:/");
exit; 
?>