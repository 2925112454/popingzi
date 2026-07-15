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
    'yuermb' => $_POST['yuermb'], // 月度价格
    'yueurl' => $_POST['yueurl'], // 月度购买地址
    'jirmb' => $_POST['jirmb'], // 季度价格
    'jiurl' => $_POST['jiurl'], // 季度购买地址
    'nianrmb' => $_POST['nianrmb'], // 年度价格
    'nianurl' => $_POST['nianurl'], // 年度购买地址
    'bairmb' => $_POST['bairmb'], // 百年价格
    'baiurl' => $_POST['baiurl'], // 百年购买地址
    'shirmb' => $_POST['shirmb'], // 10积分价格
    'shiurl' => $_POST['shiurl'], // 10积分购买地址
    'errmb' => $_POST['errmb'], // 20积分价格
    'erurl' => $_POST['erurl'], // 20积分购买地址
    'sanrmb' => $_POST['sanrmb'], // 30积分价格
    'sanurl' => $_POST['sanurl'], // 30积分购买地址
    'sirmb' => $_POST['sirmb'], // 40积分价格
    'siurl' => $_POST['siurl'], // 40积分购买地址
    'wurmb' => $_POST['wurmb'], // 50积分价格
    'wuurl' => $_POST['wuurl'], // 50积分购买地址
    'yirmb' => $_POST['yirmb'], // 100积分价格
    'yiurl' => $_POST['yiurl'],  // 100积分购买地址
    'qianrmb' => $_POST['qianrmb'], // 1000积分价格
    'qianurl' => $_POST['qianurl']  // 1000积分购买地址
];

// 手动筛选包含 'rmb' 的键
$rmbKeys = array_filter(array_keys($postData), function($key) {
    return strpos($key, 'rmb') !== false;
});
foreach ($rmbKeys as $key) {
    $value = $postData[$key];
    if (!isset($value) || $value === '') {
        echo 500;
        exit();
    }
}

// 检查所有价格是否为有效整数
foreach ($rmbKeys as $key) {
    $value = $postData[$key];
    if (!checkPrice((int)$value)) {
        echo 500;
        exit();
    }
}

// 手动筛选包含 'url' 的键
$urlKeys = array_filter(array_keys($postData), function($key) {
    return strpos($key, 'url') !== false;
});
foreach ($urlKeys as $key) {
    $value = $postData[$key];
    if (!checkUrl($value)) {
        echo 500;
        exit();
    }
}

// 获取配置信息数据表
$setsql = "SELECT * FROM ppz_cardset WHERE setid = 1";
$setres = mysqli_query($conn, $setsql);

if ($setres->num_rows !== 1) {
    echo 404;
    exit();
}

// 修改数据表
$updatesql = "UPDATE ppz_cardset SET 
    setrmbyue = ?, seturlyue = ?,
    setrmbji = ?, seturlji = ?,
    setrmbnian = ?, seturlnian = ?,
    setrmbbai = ?, seturlbai = ?,
    setrmbshi = ?, seturlshi = ?,
    setrmber = ?, seturler = ?,
    setrmbsan = ?, seturlsan = ?,
    setrmbsi = ?, seturlsi = ?,
    setrmbwu = ?, seturlwu = ?,
    setrmbyi = ?, seturlyi = ?,
    setrmbqian = ?, seturlqian = ?
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