<?php
// 将响应转换为JSON格式并输出  
header('Content-Type: application/json');
session_start(); // 开始 Session 会话
ob_start();
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION

// 生成安全随机验证码
function generateSecureCode($length = 6) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';
    
    // 使用更安全的随机数生成器
    $max = strlen($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
            $code .= $characters[random_int(0, $max)];
        } else {
            $code .= $characters[mt_rand(0, $max)];
        }
    }
    
    return $code;
}

try {
    // 检查用户是否登录
    if (empty($ppzusername)) {
        throw new Exception('错误操作！', 500);
    }
    
    $email_time = "";
    $now_time = time();
    $email_maxsize = 5;
    
    // 获取今日已发送次数
    $email_size = isset($_SESSION['email_size']) ? $_SESSION['email_size'] : 0;
    
    // 检查是否超过每日最大请求次数
    if ($email_size > $email_maxsize) {
        throw new Exception('达到验证次数限制，请明天再试！', 500);
    }
    
    // 检查是否在5分钟内重复发送
    if (isset($_SESSION['email_time']) && !empty($_SESSION['email_time'])) {
        $email_time = $_SESSION['email_time'];
        if ($now_time - $email_time < 300) {
            throw new Exception('5分钟后才能再发送验证码！', 500);
        }
    }
    
    // 连接数据库
    include $_SERVER['DOCUMENT_ROOT'].'/inc/conn.php';
    
    // 获取用户信息 (使用预处理语句防止SQL注入)
    $stmt = $conn->prepare("SELECT uid, utel, uname, utelyes FROM ppz_newusername WHERE binary uusername = ?");
    $stmt->bind_param("s", $ppzusername);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows !== 1) {
        throw new Exception('错误操作！', 500);
    }
    
    $user = $result->fetch_assoc();
    $uid = $user['uid'];
    $utel = $user['utel'];
    $uname = $user['uname'];
    $utelyes = $user['utelyes'];
    
    // 检查手机号码
    if (empty($utel)) {
        throw new Exception('请先填写手机！', 500);
    }
    
    // 验证手机号格式
    if (!preg_match('/^1[3456789]\d{9}$/', $utel)) {
        throw new Exception('手机格式错误！', 500);
    }
    
    // 检查是否已验证
    if ($utelyes == 2) {
        throw new Exception('您的手机已验证！', 500);
    }

    //判断是否其它用户已验证该手机
    $yes=2;
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM ppz_newusername WHERE utel = ? AND uid != ? AND utelyes = ?");
    $stmt->bind_param("sii", $utel,$uid,$yes);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    if ($result['count'] > 0) {
        throw new Exception('该手机已被其他用户验证！', 500);
    }
    
    // 获取短信配置信息
    $email_config_stmt = $conn->prepare("SELECT apiname, apikey, apidiy, apibody FROM ppz_tel WHERE id = 1");
    $email_config_stmt->execute();
    $email_config_result = $email_config_stmt->get_result();
    
    if ($email_config_result->num_rows !== 1) {
        throw new Exception('网站短信配置错误！', 500);
    }
    
    $email_config = $email_config_result->fetch_assoc();
    $apiname = $email_config['apiname'];
    $apikey = $email_config['apikey'];
    $apidiy = $email_config['apidiy'];
    $apibody = $email_config['apibody'];
    
    // 检查短信配置
    if (empty($apiname) || empty($apikey) || empty($apidiy)) {
        throw new Exception('网站短信配置错误！', 500);
    }
    
    // 生成安全验证码
    $email_code = generateSecureCode();
    
    // 更新发送次数
    $_SESSION['email_size'] = $email_size + 1;
    
    // 准备短信内容
    $text = urlencode($apidiy . "亲爱的『" . $uname . "』，您的验证码是" . $email_code . "。有效期为5分钟，请尽快验证。" . $apibody);
    $apiurl = "https://api.smsbao.com/sms?u=" . urlencode($apiname) . "&p=" . md5($apikey) . "&m=" . $utel . "&c=" . $text;
    
    // 发送短信
    $result = file_get_contents($apiurl);
    
    // 更新发送时间
    $_SESSION['email_time'] = $now_time;
    
    // 处理发送结果
    $errorMap = array(
        '30' => '错误密码',
        '40' => '账号不存在',
        '41' => '余额不足',
        '43' => 'IP地址限制',
        '50' => '内容含有敏感词',
        '51' => '手机号码不正确',
    );
    
    if ($result == '0') {
        // 保存验证码到数据库
        $save_code_stmt = $conn->prepare("UPDATE ppz_newusername SET uformtel = ? WHERE uid = ?");
        $save_code_stmt->bind_param("si", $email_code, $uid);
        
        if ($save_code_stmt->execute()) {
            $response = array('code' => 200, 'msg' => '');
        } else {
            throw new Exception('验证码保存失败！', 500);
        }
    } else {
        // 获取错误信息
        $errorMsg = isset($errorMap[$result]) ? $errorMap[$result] : "未知错误: $result";
        throw new Exception($errorMsg, 500);
    }
    
} catch (Exception $e) {
    $response = array(
        'code' => $e->getCode(),
        'msg' => $e->getMessage()
    );
} finally {
    // 关闭所有数据库连接
    if (isset($stmt)) $stmt->close();
    if (isset($email_config_stmt)) $email_config_stmt->close();
    if (isset($save_code_stmt)) $save_code_stmt->close();
    if (isset($conn)) $conn->close();
    
    // 输出JSON响应
    echo json_encode($response);
}
?>