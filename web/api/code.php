<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量

// 判断是否登录
if (empty($ppzusername)) {
    echo json_encode(array('err' => 500));
    exit;
}

include $_SERVER['DOCUMENT_ROOT'] . '/inc/conn.php'; // 链接数据库

// 获取登录会员信息
$stmt = $conn->prepare("SELECT * FROM ppz_newusername WHERE binary uusername = ?");
$stmt->bind_param("s", $ppzusername);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo json_encode(array('err' => 500));
    exit;
}

$row = $result->fetch_assoc();
$ustatus = $row['ustatus']; // 身份，1普通会员，2为管理员，3为副站长，4为站长；

if ($ustatus != 4 && $ustatus != 3) {
    echo json_encode(array('err' => 500));
    exit;
}


$vvarArray = []; // 用于存储所有符合条件的充值卡号

//获取所有邀请码
$stmt = $conn->prepare("SELECT invitecode FROM ppz_code");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $vvarArray[] = $row['invitecode'];
}

$stmt->close();

if (empty($vvarArray)) {
    echo json_encode(array('err' => 600));
    exit;
}

echo json_encode(array('err' => 200, 'codet' => $vvarArray));
$conn->close();
exit;
?>