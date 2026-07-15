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

$row = $rowretval->fetch_assoc();
$ustatus = $row['ustatus']; // 身份，1普通会员，2为管理员，3为副站长，4为站长；

if (!in_array($ustatus, [3, 4])) {
    echo 500;
    exit();
}

// 判断是否为有效网址
function checkUrl($url) {
    $url = trim($url);
    if (empty($url)) {
        return true; // 空字符串视为有效，不强制要求URL
    }else{
        if (!is_string($url)) {
            return false;
        }
    }
    $urlPattern = '/^(http:\/\/|https:\/\/|ftp:\/\/|\/|\S+\.(html|php))\S*$/i';
    return preg_match($urlPattern, $url) === 1;
}

// 判断价格是否是大于或等于0的整数
function checkPrice($price) {
    return is_int($price) && $price >= 0;
}


// 获取所有 POST 数据
$postData = [
    'url' => $_POST['url'], // 地址
    'price' => $_POST['price'], // 价格
    'text' => $_POST['text'], // 说明(不能包含html标签)
];

// 检查价格是否为有效整数
if (!checkPrice((int)$postData['price'])) {
    echo 500;
    exit();
}
//检查地址是否为有效网址
if (!checkUrl($postData['url'])) {
    echo 500;
    exit();
}
//检查说明是否包含html标签
if (preg_match('/<[^>]+>/', $postData['text'])) {
    echo 500;
    exit();
}

// 获取配置信息数据表
$setsql = "SELECT * FROM ppz_codeset WHERE setid = 1";
$setres = mysqli_query($conn, $setsql);

if ($setres->num_rows !== 1) {
    echo 404;
    exit();
}

// 修改数据表
$updatesql = "UPDATE ppz_codeset SET 
    seturl = ?,
    setrmb = ?,
    settext = ?
    WHERE setid = 1";

$stmt = $conn->prepare($updatesql);
$params = array_values($postData);

// 手动绑定参数
$types = str_repeat('s', count($params)); // 定义参数类型字符串
$refs = [];
foreach ($params as $key => $value) {
    $refs[$key] = &$params[$key]; // 创建引用
}
call_user_func_array([$stmt, 'bind_param'], array_merge([$types], $refs));

if ($stmt->execute()) {
    echo 200;
} else {
    echo 600;
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>