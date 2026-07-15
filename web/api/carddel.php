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

//关闭预处理
$stmt->close();

$row = $rowretval->fetch_assoc();
$ustatus = $row['ustatus']; // 身份，1普通会员，2为管理员，3为副站长，4为站长；

if (!in_array($ustatus, [3, 4])) {
    echo 500;
    exit();
}

// 判断ID是否是大于或等于0的整数
function checkPrice($price) {
    return is_int($price) && $price >= 0;
}

$cardid= $_POST['id'];//获取要删除的ID集(逗号分隔的字符串)

if (empty($cardid)) {
    echo 500;
    exit();
}

$arrcardid = explode(',', $cardid);//将字符串分割为数组
$arrcardid = array_unique($arrcardid);//去除重复的ID
$arrcardid = array_filter($arrcardid);//去除空值
$arrcardid = array_values($arrcardid);//重新排序数组的索引

foreach ($arrcardid as $key => $value) {
    if (!checkPrice((int)$value)) {//判断每个ID是否为有效整数
        echo 500;
        exit();
    }
}

//判断是否每一个ID都存在
$vsql = "SELECT * FROM ppz_vtime WHERE vid IN (".implode(',', $arrcardid).")";
$vretval = mysqli_query($conn, $vsql);
if ($vretval->num_rows !== count($arrcardid)) {
    echo 500;
    exit();
}

// 删除数据
$delsql = "DELETE FROM ppz_vtime WHERE vid IN (".implode(',', $arrcardid).")";
if (mysqli_query($conn, $delsql)) {
    echo 200;
} else {
    echo 600;
}

// 关闭数据库连接
mysqli_close($conn);

?>