<?php
// 将响应转换为JSON格式并输出  
header('Content-Type: application/json');
session_start(); // 开始 Session 会话
ob_start();
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION
// 验证用户是否登录
if (empty($ppzusername)){
    $response = array('code' => 500, 'msg' => '错误操作！');
    echo json_encode($response);
    exit;
}
if(!isset($_POST['myrowid'])||empty($_POST['myrowid'])||!is_numeric($_POST['myrowid'])||$_POST['myrowid']<1){
    $response = array('code' => 500, 'msg' => '错误操作！');
    echo json_encode($response);
    exit;
}
$mid=trim($_POST['myrowid']);
// 连接数据库
include $_SERVER['DOCUMENT_ROOT'].'/inc/conn.php';
//获取会员信息
$u_sql = "SELECT uid,uban FROM ppz_newusername where uusername='$ppzusername'";
$u_res = mysqli_query($conn, $u_sql);
if(mysqli_num_rows($u_res) < 1){
    $response = array('code' => 500, 'msg' => '错误操作！');
    echo json_encode($response);
    exit;
}

while($u_row = mysqli_fetch_array($u_res)){
    $uid=$u_row['uid'];//会员id
    $uban=$u_row['uban'];//封禁状态
}

if($uban!=1||empty($uid)||$uid<1){
    $response = array('code' => 500, 'msg' => '错误操作！');
    echo json_encode($response);
    exit;
}

//获取文章信息
$my_sql="select rowadmin,rowyes from ppz_row where rowid='$mid'";
$my_res=mysqli_query($conn,$my_sql);
if(mysqli_num_rows($my_res) < 1){
    $response = array('code' => 500, 'msg' => '错误操作！');
    echo json_encode($response);
    exit;
}

while($my_row=mysqli_fetch_array($my_res)){
    $my_admin=$my_row['rowadmin'];//作者id
    $my_yes=$my_row['rowyes'];//状态
}

if($my_admin!=$uid){
    $response = array('code' => 500, 'msg' => '错误操作！');
    echo json_encode($response);
    exit;
}

if($my_yes==3||$my_yes==4){
    $response = array('code' => 500, 'msg' => '该帖子不能被撤销！');
    echo json_encode($response);
    exit;
}

$new_sql="update ppz_row set rowyes=3 where rowid='$mid'";
$new_res=mysqli_query($conn,$new_sql);
if($new_res){
    $response = array('code' => 200, 'msg' => '');
    echo json_encode($response);
}else{
    $response = array('code' => 500, 'msg' => '撤销失败！');
    echo json_encode($response);
}
mysqli_close($conn);
?>