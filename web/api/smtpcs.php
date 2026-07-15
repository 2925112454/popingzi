<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
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

if(!isset($_POST["useremail"])){
    $_POST["useremail"]="";
}

$smtp=trim($_POST["smtp"]);//smtp服务器
$port=trim($_POST["port"]);//端口
$user=trim($_POST["user"]);//账号
$pass=trim($_POST["pass"]);//密码
$email=trim($_POST["email"]);//邮箱
$name=trim($_POST["name"]);//名称
$diyhed=trim($_POST["diyhed"]);//自定义头部
$diytail=trim($_POST["diytail"]);//自定义尾部
$useremail=trim($_POST["useremail"]);//接收邮箱

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
//检查必填项是否为空
if (empty($smtp) || empty($port) || empty($user) || empty($pass) || empty($email)||empty($useremail)) {
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

if(!empty($useremail)){
    if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/", $useremail)) {
        echo 400;
        exit;
    }
}

//判断前缀和后缀的html是否是允许的范围
if (!isValidHtml($diyhed) || !isValidHtml($diytail)) {
    echo 500;
    exit;
}

//发送测试邮件
try {
    // 引入 PHPMailer 库
    require_once($_SERVER['DOCUMENT_ROOT']."/inc/phpmailer5.5/PHPMailer.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/inc/phpmailer5.5/SMTP.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/inc/phpmailer5.5/Exception.php");
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    
    // 配置 SMTP
    $mail->isSMTP();
    $mail->Host = $smtp;
    $mail->SMTPAuth = true;
    $mail->Username = $user;
    $mail->Password = $pass;
    
    // 根据端口号设置加密方式
    if ($port == 465) {
        $mail->SMTPSecure = 'ssl';
    } elseif ($port == 587) {
        $mail->SMTPSecure = 'tls';
    }
    
    $mail->Port = $port;
    
    // 设置发件人
    $mail->setFrom($email, $name);
    
    // 设置收件人
    $mail->addAddress($useremail);
    
    // 设置邮件内容
    $mail->Subject = '邮箱配置信息测试邮件';
    
    // 构建邮件正文，包含自定义头部和尾部
    $emailBody = $diyhed . '<p>这是一封测试邮件。如果你收到了这封邮件，说明你的网站的 SMTP 信息配置正确。</p>' . $diytail;
    
    $mail->isHTML(true);
    $mail->Body = $emailBody;
    $mail->AltBody = strip_tags($emailBody);
    
    // 发送邮件
    $mail->send();
    
    echo 200; // 成功
} catch (Exception $e) {
    echo 600; // 失败
}

$stmt->close();
$conn->close();
?>