<?php
// 将响应转换为JSON格式并输出  
header('Content-Type: application/json');
session_start(); // 开始 Session 会话
ob_start();
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION

// 验证用户是否登录
if (empty($ppzusername)){
    output_error(500, '错误操作！');
}

function is_empty($str) {
    return !isset($str) || empty($str);
}

// 验证表单数据
if (is_empty($_POST['title']) || is_empty($_POST['content']) || 
    (is_empty($_POST['select']) && $_POST['select'] != 0) || 
    !is_numeric($_POST['select']) || $_POST['select'] < 0) {
    output_error(500, '错误操作！');
}

if(!isset($_POST['images']) || empty($_POST['images'])){
    $_POST['images'] = "";
}

// 数据清理
$title = htmlspecialchars(strip_tags(trim($_POST['title']))); // 工单标题
$content = htmlspecialchars(strip_tags(trim($_POST['content']))); // 工单内容
$select = (int)$_POST['select']; // 工单分类
$images = htmlspecialchars(strip_tags(trim($_POST['images']))); // 工单附件

// 验证附件URL
if(!empty($images)){
    if(!preg_match('/^(http|https|)\:\/\//', $images)){
        output_error(500, '错误操作！');
    }
}

// 判断标题字数
if (mb_strlen($title, 'utf-8') < 1 || mb_strlen($title, 'utf-8') > 60){
    output_error(500, '错误操作！');
}

// 判断内容字数
if (mb_strlen($content, 'utf-8') < 1 || mb_strlen($content, 'utf-8') > 500){
    output_error(500, '错误操作！');
}

// 连接数据库
include $_SERVER['DOCUMENT_ROOT'].'/inc/conn.php';

// 检查数据库连接
if (!$conn) {
    output_error(500, '数据库连接失败！');
}

// 获取会员信息
$u_sql = "SELECT uid, uban FROM ppz_newusername WHERE uusername = ?";
$stmt = mysqli_prepare($conn, $u_sql);
mysqli_stmt_bind_param($stmt, "s", $ppzusername);
mysqli_stmt_execute($stmt);
$u_res = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($u_res) < 1){
    mysqli_stmt_close($stmt);
    output_error(500, '错误操作！');
}

$u_row = mysqli_fetch_assoc($u_res);
$uid = $u_row['uid']; // 会员id
$uban = $u_row['uban']; // 封禁状态

mysqli_stmt_close($stmt);

// 检查用户状态
if($uban != 1 || empty($uid) || $uid < 1){
    output_error(500, '用户状态异常！');
}

// 统计今天发布的工单数量
$wk_sql = "SELECT wktime FROM ppz_work WHERE wkadmin = ? AND DATE(wktime) = CURDATE()";
$stmt = mysqli_prepare($conn, $wk_sql);
mysqli_stmt_bind_param($stmt, "i", $uid);
mysqli_stmt_execute($stmt);
$wk_res = mysqli_stmt_get_result($stmt);
$today_count = mysqli_num_rows($wk_res);
mysqli_free_result($wk_res);
mysqli_stmt_close($stmt);

// 如果今天已经发布了3个工单则返回错误
if ($today_count >= 3) {
    output_error(500, '达到工单上限，请明天再试！');
}

// 获取分类信息，判断分类是否存在
if ($select != 0) {
    $fl_sql = "SELECT * FROM ppz_workfl WHERE id = ?";
    $stmt = mysqli_prepare($conn, $fl_sql);
    mysqli_stmt_bind_param($stmt, 'i', $select);
    mysqli_stmt_execute($stmt);
    $fl_res = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($fl_res) < 1) {
        mysqli_free_result($fl_res);
        mysqli_stmt_close($stmt);
        output_error(500, '错误操作！');
    }    
    mysqli_free_result($fl_res);
    mysqli_stmt_close($stmt);
} else {
    // 判断是否存在分类，若存在，分类ID则不能为0
    $nfl_sql = "SELECT id FROM ppz_workfl";
    $nfl_res = mysqli_query($conn, $nfl_sql);
    
    if (mysqli_num_rows($nfl_res) > 0) {
        mysqli_free_result($nfl_res);
        output_error(500, '请选择有效分类！');
    }
    
    mysqli_free_result($nfl_res);
}

// 开始事务
mysqli_begin_transaction($conn);

try {
    // 写入工单
    $new_sql = "INSERT INTO ppz_work (wktext, wkword, wkimg, wkfl, wkadmin) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $new_sql);
    mysqli_stmt_bind_param($stmt, 'sssii', $title, $content, $images, $select, $uid);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('工单提交失败');
    }
    
    // 提交事务
    mysqli_commit($conn);
    $response = array('code' => 200, 'msg' => '');
    echo json_encode($response);
} catch (Exception $e) {
    // 回滚事务
    mysqli_rollback($conn);
    output_error(500, $e->getMessage());
} finally {
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    ob_end_flush();
}

// 输出错误信息并终止脚本
function output_error($code, $message) {
    $response = array('code' => $code, 'msg' => $message);
    echo json_encode($response);
    exit;
}
?>