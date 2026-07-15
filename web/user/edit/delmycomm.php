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
if(!isset($_POST['cid'])||empty($_POST['cid'])||!is_numeric($_POST['cid'])||$_POST['cid']<1){
    $response = array('code' => 500, 'msg' => '错误操作！');
    echo json_encode($response);
    exit;
}
$cid=trim($_POST['cid']);
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
$my_sql="select pladmin from ppz_commentary where plid='$cid'";//获取评论信息
$my_res=mysqli_query($conn,$my_sql);
if(mysqli_num_rows($my_res) < 1){
    $response = array('code' => 500, 'msg' => '错误操作！');
    echo json_encode($response);
    exit;
}
while($my_row=mysqli_fetch_array($my_res)){
    $my_admin=$my_row['pladmin'];//评论者id
}
if($my_admin!=$uid){
    $response = array('code' => 500, 'msg' => '错误操作！');
    echo json_encode($response);
    exit;
}

//获取回复信息
$mytwo_sql="select repid from ppz_reply where repplid='$cid'";
$mytwo_res=mysqli_query($conn,$mytwo_sql);
if(mysqli_num_rows($mytwo_res) < 1){
    //直接删除评论
    $new_sql="delete from ppz_commentary where plid='$cid'";
    $new_res=mysqli_query($conn,$new_sql);
    if($new_res){
        $response = array('code' => 200, 'msg' => '');
        echo json_encode($response);
    }else{
        $response = array('code' => 500, 'msg' => '评论删除失败！');
        echo json_encode($response);
    }
}else{

    $delyes=0;//删除回复状态
    while($mytwo_row=mysqli_fetch_array($mytwo_res)){
        $id=$mytwo_row['repid'];//回复id
        $newc_sql="delete from ppz_reply where repid='$id'";
        $newc_res=mysqli_query($conn,$newc_sql);
        if($newc_res){
            $delyes=$delyes+1;
        }else{
            $delyes=0;
        }
    }
    if($delyes>0){
        $new_sql="delete from ppz_commentary where plid='$cid'";
        $new_res=mysqli_query($conn,$new_sql);
        if($new_res){
            $response = array('code' => 200, 'msg' => '');
            echo json_encode($response);
        }else{
            $response = array('code' => 500, 'msg' => '评论删除失败！');
            echo json_encode($response);
        }
    }else{
        $response = array('code' => 500, 'msg' => '存在残留回复无法删除！');
        echo json_encode($response);
    }

}

mysqli_close($conn);
?>