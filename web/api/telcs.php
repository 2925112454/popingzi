<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
// 判断是否登录
if (empty($ppzusername)) {
    echo json_encode(array('err' => 500, 'msg' => '错误操作！'));
    exit;
}
include $_SERVER['DOCUMENT_ROOT'] . '/inc/conn.php'; // 链接数据库
// 获取登录会员信息
$stmt = $conn->prepare("SELECT * FROM ppz_newusername WHERE binary uusername = ?");
$stmt->bind_param("s", $ppzusername);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo json_encode(array('err' => 500, 'msg' => '错误操作！'));
    exit;
}

$row = $result->fetch_assoc();
$ustatus = $row['ustatus']; // 身份，1普通会员，2为管理员，3为副站长，4为站长；

if ($ustatus != 4) {
    echo json_encode(array('err' => 500, 'msg' => '错误操作！'));
    exit;
}

if(!isset($_POST["tuser"])){
    $_POST["tuser"]="";
}
if(!isset($_POST["key"])){
    $_POST["key"]="";
}

$tuser=trim($_POST["tuser"]);//账号
$key=trim($_POST["key"]);//密码

if(empty($tuser) || empty($key)){
    echo json_encode(array('err' => 500, 'msg' => '账号或密码不能为空！'));
    exit;
}
//短信宝余额查询接口https://www.smsbao.com/query?u=账号&p=密码
//返回结果:第一行返回 ‘0’ 视为发送成功，其他内容为错误提示内容 如果第一行返回成功，则第二行返回 ‘发送条数,剩余条数’
$apiurl="https://www.smsbao.com/query?u=".$tuser."&p=".$key;
$result=file_get_contents($apiurl);

// 分割返回结果为行
$lines = explode("\n", $result);
$status = trim($lines[0]);

// 错误码映射表
$errorMap = array(
    '30' => '错误密码',
    '40' => '账号不存在',
    '41' => '余额不足',
    '43' => 'IP地址限制',
    '50' => '内容含有敏感词',
    '51' => '手机号码不正确',
);

if($status == '0'){
    // 返回剩余条数
    if (isset($lines[1])) {
        $details = explode(',', $lines[1]);
        if (isset($details[1])) {
            $remaining = $details[1];
            echo json_encode(array('err' => 200, 'msg' => "短信剩余: $remaining 条"));
        } else {
            echo json_encode(array('err' => 500, 'msg' => '无法解析返回结果！'));
        }
    } else {
        echo json_encode(array('err' => 500, 'msg' => '返回结果格式不正确！'));
    }
} else {
    // 返回错误信息，优先使用映射表中的描述
    $errorMsg = isset($errorMap[$status]) ? $errorMap[$status] : "未知错误: $status";
    echo json_encode(array('err' => 500, 'msg' => $errorMsg));
}

//关闭连接
$stmt->close();
$conn->close();
?>