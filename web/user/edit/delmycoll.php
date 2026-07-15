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
$u_sql = "SELECT uid,uban,ucollect FROM ppz_newusername where uusername='$ppzusername'";//获取会员信息
$u_res = mysqli_query($conn, $u_sql);
if(mysqli_num_rows($u_res) < 1){
    $response = array('code' => 500, 'msg' => '错误操作！');
    echo json_encode($response);
    exit;
}
while($u_row = mysqli_fetch_array($u_res)){
    $uid=$u_row['uid'];//会员id
    $uban=$u_row['uban'];//封禁状态
    $ucollect=$u_row['ucollect'];//收藏（按|分割的字符串）
}
if($uban!=1||empty($uid)||$uid<1||empty($ucollect)){
    $response = array('code' => 500, 'msg' => '错误操作！');
    echo json_encode($response);
    exit;
}

$ucollectarr=explode("|",$ucollect);

if(!in_array($id,$ucollectarr)){
    $response = array('code' => 500, 'msg' => '错误操作！');
    echo json_encode($response);
    exit;
}
//从数组中移除
$ucollectarr=array_diff($ucollectarr,array($id));
//转换为字符串
$ucollect_new=implode("|",$ucollectarr);
$new_sql="update ppz_newusername set ucollect='$ucollect_new' where uid='$uid'";
$new_res=mysqli_query($conn,$new_sql);
if($new_res){
    //获取文章
    $my_sql="select rowsc from ppz_row where rowid='$id'";
    $my_res=mysqli_query($conn,$my_sql);
    if ($my_res&&mysqli_num_rows($my_res) == 1){ 
        while($my_row=mysqli_fetch_array($my_res)){
            $rowsc=$my_row['rowsc'];//文章收藏数
        }
        if($rowsc>0&&!empty($rowsc)&&is_numeric($rowsc)){
            $new_rowsc=$rowsc-1;
        }else{
            $new_rowsc=0;
        }
        //更新文章收藏数
        $new_sc_sql="update ppz_row set rowsc='$new_rowsc' where rowid='$id'";
        $new_sc_res=mysqli_query($conn,$new_sc_sql);
        if($new_sc_res){}
    }
    $response = array('code' => 200, 'msg' => '');
    echo json_encode($response);
}else{
    $response = array('code' => 500, 'msg' => '取消收藏失败！');
    echo json_encode($response);
}
mysqli_close($conn);
?>