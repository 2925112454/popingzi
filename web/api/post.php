<?php
header('Content-Type: application/json');
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
// 判断是否登录
if (empty($ppzusername)) {
    echo json_encode(array('code' => 500,'msg' => '错误操作！'));
    exit;
}
include $_SERVER['DOCUMENT_ROOT'] . '/inc/conn.php'; // 链接数据库
// 获取登录会员信息
$stmt = $conn->prepare("SELECT * FROM ppz_newusername WHERE binary uusername = ?");
$stmt->bind_param("s", $ppzusername);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo json_encode(array('code' => 500,'msg' => '错误操作！'));
    exit;
}
$row = $result->fetch_assoc();
$ustatus = $row['ustatus']; // 身份，1普通会员，2为管理员，3为副站长，4为站长；
$uid = $row['uid'];// 会员id
$uban = $row['uban']; // 封禁状态，1为正常

if ($ustatus != 4 && $ustatus != 3 && $ustatus != 2 && $ustatus != 1) {
    echo json_encode(array('code' => 500,'msg' => '账号状态异常！'));
    exit;
}
if ($uban !== 1 || $uban <0 || empty($uban) || $uid <1 || empty($uid)) {
    echo json_encode(array('code' => 500,'msg' => '账号状态异常！'));
    exit;
}
$allowedTags = ['p', 'br', 'strong', 'em', 'u', 's', 'ol', 'ul', 'li', 'a', 'img', 'h2', 'h3', 'h4', 'blockquote', 'span', 'video', 'source'];//允许的标签
$text=trim(isset($_POST['text'])?$_POST['text']:'');// 内容
$title=htmlspecialchars(trim(isset($_POST['title'])?$_POST['title']:''));// 标题
$link=htmlspecialchars(intval(trim(isset($_POST['link'])?$_POST['link']:'')));// 列表
$fl=htmlspecialchars(intval(trim(isset($_POST['fl'])?$_POST['fl']:'')));//分类
$img=htmlspecialchars(trim(isset($_POST['img'])?$_POST['img']:''));//封面
$jf=htmlspecialchars(intval(trim(isset($_POST['jf'])?$_POST['jf']:'')));//下载所需积分，为0则免费
$name = htmlspecialchars(trim(isset($_POST['name'])?$_POST['name']:''));//网盘名称
$px = htmlspecialchars(trim(isset($_POST['px'])?$_POST['px']:''));//分辨率
$url = htmlspecialchars(trim(isset($_POST['url'])?$_POST['url']:''));//网盘地址
$pass = htmlspecialchars(trim(isset($_POST['pass'])?$_POST['pass']:''));//提取码
$number = htmlspecialchars(trim(isset($_POST['number'])?$_POST['number']:''));//文件数量
$size = htmlspecialchars(trim(isset($_POST['size'])?$_POST['size']:''));//文件大小
$zip=htmlspecialchars(trim(isset($_POST['zip'])?$_POST['zip']:''));//压缩包解压密码

if(empty($text)||empty($title)||is_null($jf)||is_null($link)||is_null($fl)|| $jf<0 || $jf>999999999 || !is_numeric($jf)||!is_numeric($link)||!is_numeric($fl)){
    echo json_encode(array('code' => 500,'msg' => '标题内容为空或参数不正确！'));
    exit;
}
if (mb_strlen($title, 'UTF-8')>120){
    echo json_encode(array('code' => 500,'msg' => '标题不能超过120字符！'));
    exit;
}
// 初始化违规标签数组
$forbiddenTags = [];
// 提取所有HTML标签
preg_match_all('/<([a-z0-9]+)(\s[^>]*)?>/i', $text, $matches);
$tags = $matches[1];
// 检查每个标签是否在白名单中
foreach ($tags as $tag) {
    if (!in_array(strtolower($tag), $allowedTags)) {
        $forbiddenTags[] = $tag;
    }
}
// 判断是否存在违规标签
if (!empty($forbiddenTags)) {
   echo json_encode(array('code' => 500,'msg' => '兄弟，你越界了！'));
   exit;
}

if(!empty($img)){
    //验证路径是是不是http或者https或者//或者/开头,不是则不通过
    if(!preg_match('/^https?:\/\/|^http?:\/\/|^\\/\/|^\\//',$img)){
        echo json_encode(array('code' => 500,'msg' => '封面地址不正确！'));
        exit;
    }
    //判断是不是/开头
    if(substr($img,0,1)=='/'){
        if(substr($img,0,8)!='/upload/'){
            echo json_encode(array('code' => 500,'msg' => '封面地址不正确！'));
            exit;
        }
        //判断后缀是不是jpg/png/jpeg/gif/webp/avif
        $imgext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
        if(!in_array($imgext, array('jpg','png','jpeg','gif','webp','avif'))){
            echo json_encode(array('code' => 500,'msg' => '封面格式不正确！'));
            exit;
        }
    }
}
$dw="";
if(!empty($name)||!empty($px)||!empty($url)||!empty($pass)||!empty($number)||!empty($size)||!empty($zip)){
    if(empty($url)||empty($name)||empty($number)||empty($size)){
        echo json_encode(array('code' => 500,'msg' => '下载信息不完整！'));
        exit;
    }
    if(!empty($url)){
        if(!preg_match('/^(http|https|)\:\/\//', $url)){
            echo json_encode(array('code' => 500,'msg' => '下载信息地址错误！'));
            exit;
        }
    }
    //网盘名称,下载地址,内容数量,文件大小,解压密码,网盘提取码,内容分辨率
    $dw=$name.",".$url.",".$number.",".$size.",".$zip.",".$pass.",".$px;
}

$fl_sql="select * from ppz_fl where flid='$fl' and fllinkid='$link'";
$fl_res=mysqli_query($conn,$fl_sql);
if(!$fl_res || mysqli_num_rows($fl_res) !== 1){
    echo json_encode(array('code' => 500,'msg' => '分类信息错误！'));
    exit;
}

$link_sql="select * from ppz_link where linkid='$link'";
$link_res=mysqli_query($conn,$link_sql);
if(!$link_res || mysqli_num_rows($link_res) !== 1){
    echo json_encode(array('code' => 500,'msg' => '列表信息错误！'));
    exit;
}

$new_sql = "INSERT INTO ppz_row (rowid,rowtexe,rowbigtext,rowfl,rowdwgold,rowdw,rowimg,rowadmin) VALUES (NULL,?,?,?,?,?,?,?)";
$stmt = $conn->prepare($new_sql);
$stmt->bind_param("ssssssi", $title,$text,$fl,$jf,$dw,$img,$uid);
$stmt->execute();
$stmt->store_result();
if ($stmt->affected_rows > 0) {
    echo json_encode(array('code' => 200,'msg' => ''));
} else {
    echo json_encode(array('code' => 500,'msg' => '投稿失败！'));
}
$stmt->close();
mysqli_close($conn);
?>