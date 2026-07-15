<?php
// 将响应转换为JSON格式并输出  
header('Content-Type: application/json');
session_start(); // 开始 Session 会话
ob_start();
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION
if (empty($ppzusername)){// 验证用户是否登录
    $response = array('code' => 500, 'msg' => '错误操作！');
    echo json_encode($response);
    exit;
}
if(!isset($_POST['id'])||empty($_POST['id'])||!is_numeric($_POST['id'])||$_POST['id']<1){
    $response = array('code' => 500, 'msg' => '错误操作！');
    echo json_encode($response);
    exit;
}
$id=trim($_POST['id']);
include $_SERVER['DOCUMENT_ROOT'].'/inc/conn.php';// 连接数据库
$u_sql = "SELECT uid,uban FROM ppz_newusername where uusername='$ppzusername'";//获取会员信息
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
//获取工单
$mywk_sql="select id,wkadmin from ppz_work where id='$id' and wkadmin='$uid'";
$mywk_res=mysqli_query($conn,$mywk_sql);
if(mysqli_num_rows($mywk_res) < 1){
    $response = array('code' => 500, 'msg' => '错误操作！');
    echo json_encode($response);
    exit;
}

//删除工单
$new_sql="delete from ppz_work where id='$id' and wkadmin='$uid'";
$new_res=mysqli_query($conn,$new_sql);
if($new_res){
    $response = array('code' => 200, 'msg' => '');
    echo json_encode($response);
}else{
    $response = array('code' => 500, 'msg' => '工单删除失败！');
    echo json_encode($response);
}

mysqli_close($conn);
?>