<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
// 判断是否登录
if (empty($ppzusername)) {
    echo 500;
    exit();
}

include $_SERVER['DOCUMENT_ROOT'].'/inc/conn.php'; // 链接数据库

// 获取登录会员信息
$rowsql = "SELECT * FROM ppz_newusername WHERE BINARY uusername = ?";
$stmt = $conn->prepare($rowsql);
$stmt->bind_param("s", $ppzusername);
$stmt->execute();
$rowretval = $stmt->get_result();

if ($rowretval->num_rows !== 1) { 
    echo 500;
    exit();
}

$stmt->close();

$row = $rowretval->fetch_assoc();
$ustatus = $row['ustatus']; // 身份，1普通会员，2为管理员，3为副站长，4为站长；

if (!in_array($ustatus, [3, 4])) {
    echo 500;
    exit();
}

$eid= $_POST['id'];//id
$value= $_POST['text'];//值

if (empty($eid) || empty($value) || !is_numeric($eid) || $eid<1) {
    echo 500;
    exit();
}

//判断id是否存在
$sql = "SELECT * FROM ppz_vtime WHERE vid = ?";
$stmtx = $conn->prepare($sql);
$stmtx->bind_param("i", $eid);
$stmtx->execute();
$retval = $stmtx->get_result();
if ($retval->num_rows !== 1) {
    echo 500;
    exit();
}
$stmtx->close();

//判断value是否已存在
$sqlv = "SELECT * FROM ppz_vtime WHERE vvar = ?";
$stmtv = $conn->prepare($sqlv);
$stmtv->bind_param("s", $value);
$stmtv->execute();
$retvalv = $stmtv->get_result();
if ($retvalv->num_rows > 0) {
    echo 800;
    exit();
}
$stmtv->close();

//更新数据
$newsql = "UPDATE ppz_vtime SET vvar = ? WHERE vid = ?";
$newstmt = $conn->prepare($newsql);
$newstmt->bind_param("si", $value, $eid);
if ($newstmt->execute()) {
    echo 200;
} else {
    echo 600;
}
$newstmt->close();

mysqli_close($conn);


?>