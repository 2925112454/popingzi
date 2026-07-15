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
if(!isset($_POST["diy"])){
    $_POST["diy"]="";
}
if(!isset($_POST["body"])){
    $_POST["body"]="";
}

$tuser=trim($_POST["tuser"]);//账号
$key=trim($_POST["key"]);//密码
$diy=trim($_POST["diy"]);//签名
$body=trim($_POST["body"]);//后缀

$allnull=!empty($tuser)||!empty($key)||!empty($diy)||!empty($body);

if($allnull&&(empty($tuser)||empty($key)||empty($diy))){
    echo json_encode(array('err' => 500, 'msg' => '必填项不能为空！'));
    exit;
}
if ($allnull&&!isValidCustomFormat($diy)) {
    echo json_encode(array('err' => 500, 'msg' => '短信签名格式错误！'));
    exit;
}
if (containsHtmlTags($tuser) || containsHtmlTags($key) || containsHtmlTags($diy) || containsHtmlTags($body)) {
    echo json_encode(array('err' => 500, 'msg' => '输入内容不能包含HTML标签！'));
    exit;
}
$setsql = "UPDATE ppz_tel SET apiname = ?,apikey = ?,apidiy = ?,apibody = ? WHERE id = 1";
$stmts = $conn->prepare($setsql);
$stmts->bind_param("ssss", $tuser, $key, $diy, $body);
if ($stmts->execute()) {
    echo json_encode(array('err' => 200, 'msg' => '保存成功！'));
} else {
    echo json_encode(array('err' => 500, 'msg' => '保存失败！'));
}
$stmts->close();
$stmt->close();
$conn->close();
?>