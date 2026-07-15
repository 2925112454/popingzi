<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 响应JSON格式
header('Content-Type: application/json');
session_start();
ob_start();
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';

// 验证用户登录状态
if (empty($ppzusername)) {
    echo json_encode(['code' => 500, 'msg' => '错误操作！']);
    exit;
}

// 初始化变量
$email_time = "";
$now_time = time();
$email_maxsize = 5;

// 获取今日已发送次数
$email_size = isset($_SESSION['email_size']) ? $_SESSION['email_size'] : 0;

// 验证发送次数限制
if ($email_size > $email_maxsize) {
    echo json_encode(['code' => 500, 'msg' => '达到验证次数限制，请明天再试！']);
    exit;
}

// 验证发送间隔（5分钟）
if (isset($_SESSION['email_time']) && ($now_time - $_SESSION['email_time'] < 300)) {
    echo json_encode(['code' => 500, 'msg' => '5分钟后才能再发送验证码！']);
    exit;
}

// 数据库连接
include $_SERVER['DOCUMENT_ROOT'].'/inc/conn.php';

// 获取用户信息
$stmt = $conn->prepare("SELECT uid, uemail, uname, uemailyes FROM ppz_newusername WHERE binary uusername = ?");
$stmt->bind_param("s", $ppzusername);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo json_encode(['code' => 500, 'msg' => '错误操作！']);
    exit;
}

$user = $result->fetch_assoc();
extract($user);

// 验证邮箱有效性
if (empty($uemail)) {
    echo json_encode(['code' => 500, 'msg' => '请先填写邮箱！']);
    exit;
}

if (!filter_var($uemail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['code' => 500, 'msg' => '你的邮箱地址不正确！']);
    exit;
}

if ($uemailyes == 2) {
    echo json_encode(['code' => 500, 'msg' => '您的邮箱已验证！']);
    exit;
}

//判断是否其它用户已验证该邮箱
$yes=2;
$stmt = $conn->prepare("SELECT COUNT(*) AS count FROM ppz_newusername WHERE uemail = ? AND uid != ? AND uemailyes = ?");
$stmt->bind_param("sii", $uemail,$uid,$yes);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
if ($result['count'] > 0) {
    echo json_encode(['code' => 500, 'msg' => '该邮箱已被其他用户验证！']);
    exit;
}

// 获取邮箱配置
$stmt = $conn->prepare("SELECT smtp, username, password, port, email, name, diyhed, diy FROM ppz_email WHERE id = 1");
$stmt->execute();
$email_config = $stmt->get_result()->fetch_assoc();

if (!$email_config) {
    echo json_encode(['code' => 500, 'msg' => '网站邮箱配置错误！']);
    exit;
}

extract($email_config);

// 验证邮箱配置完整性
if (empty($smtp) || empty($username) || empty($password) || empty($port) || empty($email)) {
    echo json_encode(['code' => 500, 'msg' => '网站邮箱配置错误！']);
    exit;
}

// 获取网站名称
$stmt = $conn->prepare("SELECT webtext FROM ppz_web WHERE webid = 1");
$stmt->execute();
$website_config = $stmt->get_result()->fetch_assoc()['webtext'];

if (!$website_config) {
    echo json_encode(['code' => 500, 'msg' => '网站配置错误！']);
    exit;
}

// 生成随机验证码
function getRandChar($length) {
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr(str_shuffle(str_repeat($strPol, $length)), 0, $length);
}

$email_code = getRandChar(6);
$_SESSION['email_size'] = $email_size + 1;

// 配置PHPMailer
require_once($_SERVER['DOCUMENT_ROOT']."/inc/phpmailer5.5/PHPMailer.php");
require_once($_SERVER['DOCUMENT_ROOT']."/inc/phpmailer5.5/SMTP.php");
require_once($_SERVER['DOCUMENT_ROOT']."/inc/phpmailer5.5/Exception.php");

$mail = new PHPMailer(true);
$mail->CharSet = 'UTF-8';

try {
    // SMTP配置
    $mail->isSMTP();
    $mail->Host = $smtp;
    $mail->SMTPAuth = true;
    $mail->Username = $username;
    $mail->Password = $password;
    $mail->SMTPSecure = ($port == 465) ? 'ssl' : (($port == 587) ? 'tls' : '');
    $mail->Port = $port;

    // 邮件内容配置
    $mail->setFrom($email, $name);
    $mail->addAddress($uemail);
    $mail->Subject = "[$website_config]邮箱验证码";
    
    $htmlBody = ''.$diyhed.'<br/>尊敬的用户『'.$uname.'』您好：<p>您的验证码是：<br/><b style="font-size:26px;color:red;">'.$email_code.'</b></p><p>验证码五分钟内有效，请及时进行验证。</p>'.$diy.'';
    $plainBody = strip_tags($htmlBody);
    
    $mail->Body = $htmlBody;
    $mail->AltBody = $plainBody;

    // 发送邮件
    $mail->send();
    
    // 保存验证码到数据库
    $stmt = $conn->prepare("UPDATE ppz_newusername SET uformemail = ? WHERE uid = ?");
    $stmt->bind_param("si", $email_code, $uid);
    
    if ($stmt->execute()) {
        $_SESSION['email_time'] = $now_time;
        echo json_encode(['code' => 200, 'msg' => '']);
    } else {
        throw new Exception('数据库更新失败');
    }
    
} catch (Exception $e) {
    echo json_encode(['code' => 500, 'msg' => "验证码发送失败！"]);
} finally {
    // 关闭数据库连接
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>