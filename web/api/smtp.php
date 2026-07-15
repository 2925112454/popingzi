<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
// 判断是否登录
if (empty($ppzusername)) {
    echo 500;
    exit;
}
include $_SERVER['DOCUMENT_ROOT'] . '/inc/conn.php'; // 链接数据库
// 获取登录会员信息
$stmt = $conn->prepare("SELECT * FROM ppz_newusername WHERE binary uusername = ?");
$stmt->bind_param("s", $ppzusername);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo 500;
    exit;
}

$row = $result->fetch_assoc();
$ustatus = $row['ustatus']; // 身份，1普通会员，2为管理员，3为副站长，4为站长；

if ($ustatus != 4) {
    echo 500;
    exit;
}

if(!isset($_POST["smtp"])){
    $_POST["smtp"]="";
}
if(!isset($_POST["port"])){
    $_POST["port"]="";
}
if(!isset($_POST["user"])){
    $_POST["user"]="";
}
if(!isset($_POST["pass"])){
    $_POST["pass"]="";
}
if(!isset($_POST["email"])){
    $_POST["email"]="";
}
if(!isset($_POST["name"])){
    $_POST["name"]="";
}
if(!isset($_POST["diyhed"])){
    $_POST["diyhed"]="";
}
if(!isset($_POST["diytail"])){
    $_POST["diytail"]="";
}

$smtp=trim($_POST["smtp"]);//smtp服务器
$port=trim($_POST["port"]);//端口
$user=trim($_POST["user"]);//账号
$pass=trim($_POST["pass"]);//密码
$email=trim($_POST["email"]);//邮箱
$name=trim($_POST["name"]);//名称
$diyhed=trim($_POST["diyhed"]);//自定义头部
$diytail=trim($_POST["diytail"]);//自定义尾部

//中文双引号转为英文
$diyhed=str_replace('“','"',$diyhed);
$diyhed=str_replace('”','"',$diyhed);
$diytail=str_replace('”','"',$diytail);
$diytail=str_replace('“','"',$diytail);
//去除换行符
$diyhed=str_replace("\n","",$diyhed);
$diytail=str_replace("\n","",$diytail);
$diytail=str_replace("\r","",$diytail);
$diyhed=str_replace("\r","",$diyhed);

//转义名称中的html
$name=htmlspecialchars($name);
function isValidHtml($htmlString) {
    $allowedTags = ['a', 'p', 'b', 'img', 'br', 'span', 'h1', 'h2', 'h3', 'h4', 'h5'];
    $matches = [];
    // 使用正则表达式匹配所有HTML标签
    preg_match_all('/<([a-z]+)(?:\s[^>]*)?>/i', $htmlString, $matches);
    // 获取所有匹配到的标签名（小写）
    $foundTags = array_map('strtolower', $matches[1]);
    // 检查是否存在不允许的标签
    foreach ($foundTags as $tag) {
        if (!in_array($tag, $allowedTags)) {
            return false;
        }
    }
    return true;
}
$allEmpty = !empty($smtp) && !empty($port) && !empty($user) && !empty($pass) && !empty($email) && !empty($name) && !empty($diytail) && !empty($diyhed);
//检查必填项是否为空
if ($allEmpty&&(empty($smtp) || empty($port) || empty($user) || empty($pass) || empty($email))) {
    echo 404;
    exit;
}

//判断邮箱地址是否合法
if(!empty($email)){
    if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/", $email)) {
        echo 400;
        exit;
    }
}

//判断前缀和后缀的html是否是允许的范围
if (!isValidHtml($diyhed) || !isValidHtml($diytail)) {
    echo 500;
    exit;
}

$getConfig = $conn->query("SELECT * FROM ppz_email WHERE id = 1");
$config = $getConfig->fetch_assoc();
    if (
        $config['smtp'] !== $smtp ||
        $config['username'] !== $user ||
        $config['password'] !== $pass ||
        $config['diy'] !== $diytail ||
        $config['port'] != $port ||
        $config['email'] !== $email ||
        $config['name'] !== $name ||
        $config['diyhed'] !== $diyhed
    ) {
        //修改邮箱配置
        $stmtx = $conn->prepare("UPDATE ppz_email SET smtp = ?, username = ?, password = ?, diy = ?, port = ?, email = ?, name = ?, diyhed = ? WHERE id = 1");
        $stmtx->bind_param("ssssssss", $smtp, $user, $pass, $diytail, $port, $email, $name, $diyhed);
        $stmtx->execute();

        if ($stmtx->affected_rows > 0) {
            echo 200;
        } else {
            echo 600;
        }
        $stmtx->close();
    }else {
        echo 200; // 数据未变化，但视为成功
    }
$stmt->close();
$conn->close();
?>