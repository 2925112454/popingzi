<?php
// 将响应转换为JSON格式并输出  
header('Content-Type: application/json');
session_start(); // 开始 Session 会话
ob_start();
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION

// 验证用户是否登录
if (empty($ppzusername)){
    $response = array('code' => 500, 'msg' => '错误操作！');
    echo json_encode($response);
    exit;
}

// 初始化变量
$now_time = time();
$email_maxsize = 5;
$type = '';
$code = '';

// 获取请求参数
if(isset($_POST['type']) && !empty($_POST['type'])){
    $type = htmlspecialchars(str_replace(" ","",$_POST['type']));
}

if(isset($_POST['code']) && !empty($_POST['code'])){
    $code = htmlspecialchars(strtoupper(str_replace(" ","",$_POST['code'])));
}

// 检查请求次数限制
if(isset($_SESSION['email_size_post'])){
    $email_size = $_SESSION['email_size_post'];
} else {
    $email_size = 0;
}

$_SESSION['email_size_post'] = $email_size + 1;

if($email_size > $email_maxsize){
    $response = array('code' => 500, 'msg' => '达到验证次数限制，请明天再试！');
    echo json_encode($response);
    exit;
}

// 连接数据库
include $_SERVER['DOCUMENT_ROOT'].'/inc/conn.php';

// 安全查询用户信息，防止SQL注入
$stmt = $conn->prepare("SELECT * FROM ppz_newusername WHERE binary uusername = ?");
$stmt->bind_param("s", $ppzusername);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows !== 1){ 
    $response = array('code' => 500, 'msg' => '错误操作！');
    echo json_encode($response);
    exit;
}

$row = $result->fetch_assoc();
$uid = $row['uid'];
$uformemail = $row['uformemail'];
$uemailyes = $row['uemailyes'];
$uformtel = $row['uformtel'];
$utelyes = $row['utelyes'];

// 验证验证码格式
if(empty($code) || strlen($code) !== 6){
    $response = array('code' => 500, 'msg' => '验证码不正确');
    echo json_encode($response);
    exit;
}

// 处理邮箱验证
if($type === "email"){
    // 获取邮箱验证码生成时间
    $verification_time = isset($_SESSION['email_time']) ? $_SESSION['email_time'] : 0;
    
    // 判断是否超过5分钟
    if($verification_time && ($now_time - $verification_time > 300)){
        $stmt = $conn->prepare("UPDATE ppz_newusername SET uformemail = NULL WHERE uid = ?");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        
        $response = array('code' => 500, 'msg' => '验证码已过期');
        echo json_encode($response);
        exit;
    }        
    
    if(empty($uformemail)){
        $response = array('code' => 500, 'msg' => '请先获取验证码');
        echo json_encode($response);
        exit;
    }

    if ($uemailyes == 2) {
        $response = array('code' => 500, 'msg' => '您的邮箱已验证');
        echo json_encode($response);
        exit;
    }

    if ($code !== $uformemail) {
        $response = array('code' => 500, 'msg' => '验证码不正确');
        echo json_encode($response);
        exit;
    }

    // 开始事务
    $conn->begin_transaction();
    
    try {
        // 更新验证状态
        $stmt = $conn->prepare("UPDATE ppz_newusername SET uemailyes = 2 WHERE uid = ?");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        
        // 删除验证码
        $stmt = $conn->prepare("UPDATE ppz_newusername SET uformemail = NULL WHERE uid = ?");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        
        // 提交事务
        $conn->commit();
        
        // 清除会话数据
        unset($_SESSION['email_time'], $_SESSION['email_size_post']);
        
        $response = array('code' => 200, 'msg' => '');
        echo json_encode($response);
    } catch (Exception $e) {
        // 回滚事务
        $conn->rollback();
                
        $response = array('code' => 500, 'msg' => '验证状态设置失败');
        echo json_encode($response);
    }

// 处理手机验证
} elseif($type === "tel"){
    // 获取短信验证码生成时间
    $verification_time = isset($_SESSION['tel_time']) ? $_SESSION['tel_time'] : 0;
    
    // 判断是否超过5分钟
    if($verification_time && ($now_time - $verification_time > 300)){
        $stmt = $conn->prepare("UPDATE ppz_newusername SET uformtel = NULL WHERE uid = ?");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        
        $response = array('code' => 500, 'msg' => '验证码已过期');
        echo json_encode($response);
        exit;
    }        
    
    if(empty($uformtel)){
        $response = array('code' => 500, 'msg' => '请先获取验证码');
        echo json_encode($response);
        exit;
    }

    if ($utelyes == 2) {
        $response = array('code' => 500, 'msg' => '您的手机已验证');
        echo json_encode($response);
        exit;
    }

    if ($code !== $uformtel) {
        $response = array('code' => 500, 'msg' => '验证码不正确');
        echo json_encode($response);
        exit;
    }

    // 开始事务
    $conn->begin_transaction();
    
    try {
        // 更新验证状态
        $stmt = $conn->prepare("UPDATE ppz_newusername SET utelyes = 2 WHERE uid = ?");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        
        // 删除验证码
        $stmt = $conn->prepare("UPDATE ppz_newusername SET uformtel = NULL WHERE uid = ?");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        
        // 提交事务
        $conn->commit();
        
        // 清除会话数据
        unset($_SESSION['tel_time'], $_SESSION['email_size_post']);
        
        $response = array('code' => 200, 'msg' => '');
        echo json_encode($response);
    } catch (Exception $e) {
        // 回滚事务
        $conn->rollback();
        $response = array('code' => 500, 'msg' => '验证状态设置失败');
        echo json_encode($response);
    }

} else {
    $response = array('code' => 500, 'msg' => '错误操作！');
    echo json_encode($response);
}
?>