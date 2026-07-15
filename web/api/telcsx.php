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


function isValidCustomFormat($str) {
    $str = trim($str);
    if (mb_strlen($str, 'UTF-8') < 3) {
        return false;
    }
    if (mb_substr($str, 0, 1, 'UTF-8') !== '【' || mb_substr($str, -1, 1, 'UTF-8') !== '】') {
        return false;
    }
    $content = mb_substr($str, 1, -1, 'UTF-8');
    if (empty($content)) {
        return false;
    }
    return preg_match('/^[\w\x{4e00}-\x{9fa5}\-\_]+$/u', $content) === 1;
}

function containsHtmlTags($str) {//判断是否包含HTML标签
    $htmlTagRegex = '/<(?!\s*(?:area|base|br|col|embed|hr|img|input|keygen|link|meta|param|source|track|wbr)[^>]*\/?\s*>)[^>]+>/i';
    return preg_match($htmlTagRegex, $str) === 1;
}    

if(!isset($_POST["tuser"])){
    $_POST["tuser"]="";
}
if(!isset($_POST["key"])){
    $_POST["key"]="";
}
if(!isset($_POST["tel"])){
    $_POST["tel"]="";
}
if(!isset($_POST["diy"])){
    $_POST["diy"]="";
}
if(!isset($_POST["body"])){
    $_POST["body"]="";
}

$tuser=trim($_POST["tuser"]);//账号
$key=trim($_POST["key"]);//密码
$tel=trim($_POST["tel"]);//手机号
$diy=trim($_POST["diy"]);//签名
$body=trim($_POST["body"]);//后缀

if(empty($tuser)||empty($key)||empty($tel)||empty($diy)){
    echo json_encode(array('err' => 500, 'msg' => '必填项不能为空！'));
    exit;
}

if (!preg_match("/^1[3-9]\d{9}$/", $tel)) {
    echo json_encode(array('err' => 500, 'msg' => '手机号格式错误！'));
    exit;
}

if (!isValidCustomFormat($diy)) {
    echo json_encode(array('err' => 500, 'msg' => '短信签名格式错误！'));
    exit;
}

if (containsHtmlTags($tuser) || containsHtmlTags($key) || containsHtmlTags($tel) || containsHtmlTags($diy) || containsHtmlTags($body)) {
    echo json_encode(array('err' => 500, 'msg' => '输入内容不能包含HTML标签！'));
    exit;
}

$text=$diy."你的验证码是666888，如果你收到了这条短信，说明您的配置是成功的。".$body;//短信内容

/*短信宝发送短信接口：https://api.smsbao.com/sms?u=账号&p=密码&m=接收者手机号&c=内容
返回结果:接收到数据后，CP处理成功请返回字符串”0″，其他返回值将被认为是失败，若失败，我们将会于一分钟，三分钟，十分钟后重试推送三次。若接口1小时内累计调用10次都获取不到正确返回值，将暂停推送1小时
*/
$apiurl="https://api.smsbao.com/sms?u=".$tuser."&p=".$key."&m=".$tel."&c=".$text;
$result=file_get_contents($apiurl);
// 错误码映射表
$errorMap = array(
    '30' => '错误密码',
    '40' => '账号不存在',
    '41' => '余额不足',
    '43' => 'IP地址限制',
    '50' => '内容含有敏感词',
    '51' => '手机号码不正确',
);

if($result == '0'){
    echo json_encode(array('err' => 200, 'msg' => '发送成功！'));
} else {
    // 错误信息，优先使用映射表中的描述
    $errorMsg = isset($errorMap[$result]) ? $errorMap[$result] : "未知错误: $result";
    echo json_encode(array('err' => 500, 'msg' => $errorMsg));
}

//关闭连接
$stmt->close();
$conn->close();

?>