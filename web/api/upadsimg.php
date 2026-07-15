<?php
// 将响应转换为JSON格式并输出  
header('Content-Type: application/json');
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
// 判断是否登录
if (empty($ppzusername)) {
    echo json_encode(array('code' => 500,'meg' => '错误操作','url' => ''));
    exit;
}
include $_SERVER['DOCUMENT_ROOT'] . '/inc/conn.php'; // 链接数据库
$stmt = $conn->prepare("SELECT * FROM ppz_newusername WHERE binary uusername = ?");
$stmt->bind_param("s", $ppzusername);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo json_encode(array('code' => 500,'meg' => '错误操作','url' => ''));
    exit;
}

$row = $result->fetch_assoc();
$ustatus = $row['ustatus']; // 身份，1普通会员，2为管理员，3为副站长，4为站长；

if ($ustatus != 4 && $ustatus != 3) {
    echo json_encode(array('code' => 500,'meg' => '错误操作','url' => ''));
    exit;
}

if(!isset($_FILES["file"])){
    echo json_encode(array('code' => 500,'meg' => '文件丢失','url' => ''));
    exit;
}

$uperr=$_FILES["file"]["error"];
if  ($uperr > 0) {
     if($uperr==1){
        $webmaxsize=ini_get('upload_max_filesize');// 获取php.ini中设置的上传文件最大体积
        echo json_encode(array('code' => 500,'meg' => '上传文件大小超出服务器限制:'.$webmaxsize.'','url' => ''));
        exit;
    }
    if($uperr==2){
         $webmaxsizex=ini_get('post_max_size');// 获取php.ini中设置的表单最大体积
        echo json_encode(array('code' => 500,'meg' => '上传文件大小超出表单限制：'.$webmaxsizex.'','url' => ''));
        exit;
    }
    if ($uperr == 3) {
        echo json_encode(['code' => 500, 'meg' => '文件只有部分被上传', 'url' => '']);
        exit;
    }
    if ($uperr == 6) {
        echo json_encode(['code' => 500, 'meg' => '找不到临时文件夹', 'url' => '']);
        exit;
    }
    if ($uperr == 7 || $uperr == 8) {
        echo json_encode(['code' => 500, 'meg' => '系统错误导致无法上传', 'url' => '']);
        exit;
    }
}

//获取formData
$files=$_FILES["file"];

//读取文件信息
$tmpName = $files['tmp_name'];

//自定义配置信息
$maxsize= 1024;//允许的最大体积，单位kb
$typeon= array('image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif', 'image/x-icon', 'image/bmp','application/octet-stream');//允许上传的文件MIME类型
$imgurl= '/upload/ads-images/';//广告图片保存的相对路径
$newname= date('YmdHis') . '_' . rand(10000, 99999);// 新文件名规则【年月日时间+随机数(10000-99999)】
$blackExts = ['php', 'php3', 'php4', 'php5', 'php7', 'phtml', 'phps', 'asp', 'aspx', 'jsp', 'exe', 'sh','js','html','py','svg'];//不允许的文件后缀名

//开始判断
$path= $_SERVER['DOCUMENT_ROOT'] .$imgurl;
$ext= strtolower(pathinfo($files['name'], PATHINFO_EXTENSION));// 获取文件扩展名
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$real_mime = finfo_file($finfo, $tmpName);


if(!in_array($files['type'], $typeon)){
    echo json_encode(array('code' => 500,'meg' => '文件格式不支持','url' => ''));
    exit;
}

if (!in_array($real_mime, $typeon)) {
    echo json_encode(array('code' => 500, 'meg' => '文件格式不支持', 'url' => ''));
    exit;
}

if (in_array($ext, $blackExts)) {
    echo json_encode(array('code' => 500, 'meg' => '该文件不允许被上传', 'url' => ''));
    exit;
}

if($files['size'] > $maxsize * 1024){
    echo json_encode(array('code' => 500,'meg' => '文件大小不能超过'.$maxsize.'KB','url' => ''));
    exit;
}
        // 判断后缀是否是图片文件
        if (preg_match('/^(gif|png|jpg|jpeg|webp|avif|svg|ico|bmp)$/i', $ext)) {
            // 如果是 avif 格式
            if ($ext === 'avif') {
                if (version_compare(PHP_VERSION, '8.1.0', '>=')) {
                    // PHP >= 8.1 支持 AVIF
                    if (!@getimagesize($tmpName)) {
                        echo json_encode(array('code' => 500, 'meg' => '文件不是合法的AVIF图片', 'url' => ''));
                        exit;
                    }
                } else {
                    // PHP < 8.1 不支持 AVIF，跳过检查
                }
            }

            // 如果是 webp 格式
            elseif ($ext === 'webp') {
                if (version_compare(PHP_VERSION, '7.2.0', '>=')) {
                    // PHP >= 7.2 支持 WEBP
                    if (!@getimagesize($tmpName)) {
                        echo json_encode(array('code' => 500, 'meg' => '文件不是合法的WEBP图片', 'url' => ''));
                        exit;
                    }
                } else {
                    // PHP < 7.2 不支持 WEBP，跳过检查
                }
            }

            // 其他通用图片格式（如 jpg/png/gif 等）
            else {
                if (!@getimagesize($tmpName)) {
                    echo json_encode(array('code' => 500, 'meg' => '文件不是合法的图片', 'url' => ''));
                    exit;
                }
            }
        }

//判断文件夹是否存在，不存在则新建
 if (!file_exists($path)) {
    if (!mkdir($path, 0755, true)) {
        echo json_encode(array('code' => 500, 'meg' => '上传目录不存在 且 创建失败', 'url' => ''));// 创建目录失败
        exit;
    }
}

//保存文件
if (move_uploaded_file($_FILES['file']['tmp_name'], $path . $newname . '.' . $ext)) {
    echo json_encode(array('code' => 200,'meg' => '上传成功','url' => $imgurl.$newname.'.'.$ext));
}else {
     echo json_encode(array('code' => 500,'meg' => '上传失败','url' => ''));
}

?>