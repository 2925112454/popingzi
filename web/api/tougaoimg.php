<?php
// 将响应转换为JSON格式并输出  
header('Content-Type: application/json');
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION
if (empty($ppzusername)||!isset($ppzusername)){//判断是否登录
    echo json_encode(array('code' => 500, "error" => array( "message" => "错误操作！" )));
    exit();
}
include $_SERVER['DOCUMENT_ROOT'] . '/inc/conn.php'; // 链接数据库
$stmt = $conn->prepare("SELECT * FROM ppz_newusername WHERE binary uusername = ?");// 获取登录会员信息
$stmt->bind_param("s", $ppzusername);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo json_encode(array('code' => 500, "error" => array( "message" => "错误操作！" )));
    exit;
}
$row = $result->fetch_assoc();
$uban = $row['uban']; // 封禁状态，1为正常
$uid = $row['uid'];// 用户id
$ustatus = $row['ustatus']; // 身份，1普通会员，2为管理员，3为副站长，4为站长；

if ($ustatus != 4 && $ustatus != 3 && $ustatus != 2 && $ustatus != 1) {
    echo json_encode(array('code' => 500,'msg' => '账号状态异常！'));
    exit;
}
if ($uban !== 1 || $uid < 1 || empty($uban) || empty($uid)) {
    echo json_encode(array('code' => 500, "error" => array( "message" => "账号状态异常！" )));
    exit;
}
if(!isset($_FILES['image'])){
    echo json_encode(array('code' => 500, "error" => array( "message" => "没有获取到图片！" )));
    exit;
}
//链接上传配置数据库
$result_up = $conn->query("SELECT * FROM `ppz_upfile` WHERE `id` = 1");
$upfile_t = $result_up->fetch_assoc();
if(!$upfile_t || (int)$upfile_t['upif'] !== 1) {
    echo json_encode(array('code' => 500, "error" => array( "message" => "投稿功能已关闭！" )));
    exit;
}
if((int)$upfile_t['upifimg'] !== 1) {
    echo json_encode(array('code' => 500, "error" => array( "message" => "上传功能未开启！" )));
    exit;
}
function generateRandomString($length = 10) {//生成随机字符串函数
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';  
    $charactersLength = strlen($characters);  
    $randomString = '';  
    for ($i = 0; $i < $length; $i++) {  
        $randomString .= $characters[mt_rand(0, $charactersLength - 1)];  
    }  
    return $randomString;  
}
$image= $_FILES['image'];
$imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp','avif'];//允许的图片后缀
$imagemime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp','image/avif'];//允许的图片mime

$daytime=date("Ymd",time());//获取当前日期：年月日
$imageExtension = pathinfo($image['name'], PATHINFO_EXTENSION);//获取图片后缀
$upfileurl='/upload/'.$daytime.'/';//上传目录

$imgname=date("YmdHis",time()).generateRandomString(4)."-".$uid."-";//以当前时间+随机生成的4位数+用户ID作为文件名
$targetDir = $_SERVER['DOCUMENT_ROOT'].$upfileurl;//创建目录物理路径
if(!is_dir($targetDir)){mkdir($targetDir,0775,true);}//判断目录是否存在，不存在则创建目录
$targetFile = $upfileurl . basename($imgname.'.'.$imageExtension);//获取最新文件名
$yesfile=$targetDir . basename($imgname.'.'.$imageExtension);//获取最新物理路径

if(empty($image)|| !is_uploaded_file($image['tmp_name'])) {
    echo json_encode(array('code' => 500, "error" => array( "message" => "没有获取到图片！" ))); 
    exit;
}

if(($image['size'] > (int)$upfile_t['upsize']*1024)&&$upfile_t['upsize']>0 ){
    echo json_encode(array('code' => 500, "error" => array( "message" => "图片不能超过".$upfile_t['upsize']."KB" )));
    exit;
}
if(!in_array($imageExtension, $imageExtensions)){
    echo json_encode(array('code' => 500, "error" => array( "message" => "图片格式错误！" )));
    exit;
}

// 获取真实MIME类型（兼容PHP 5.5.9+）
$fileMimeType = false;
if (function_exists('finfo_open')) {
    // 使用FileInfo扩展（推荐）
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    if ($finfo) {
        $fileMimeType = finfo_file($finfo, $image['tmp_name']);
        finfo_close($finfo);
    }
} elseif (function_exists('mime_content_type')) {
    // 回退到mime_content_type（PHP 8.0+已弃用）
    $fileMimeType = @mime_content_type($image['tmp_name']);
}

// 如果无法获取MIME类型，使用上传的类型（不推荐但兼容旧版）
if (!$fileMimeType) {
    $fileMimeType = $image['type'];
}
// 验证文件头签名（针对常见格式）
$valid = false;
$fileContent = file_get_contents($image['tmp_name'], false, null, 0, 16);
// JPEG
if (($fileMimeType === 'image/jpeg' || strtolower($imageExtension) === 'jpg' || strtolower($imageExtension) === 'jpeg') && 
    (substr($fileContent, 0, 2) === "\xFF\xD8" && substr($fileContent, -2) === "\xFF\xD9")) {
    $valid = true;
}
// PNG
elseif (($fileMimeType === 'image/png' || strtolower($imageExtension) === 'png') && 
        substr($fileContent, 0, 8) === "\x89PNG\x0D\x0A\x1A\x0A") {
    $valid = true;
}
// GIF
elseif (($fileMimeType === 'image/gif' || strtolower($imageExtension) === 'gif') && 
        (substr($fileContent, 0, 6) === 'GIF87a' || substr($fileContent, 0, 6) === 'GIF89a')) {
    $valid = true;
}
// WebP
elseif (($fileMimeType === 'image/webp' || strtolower($imageExtension) === 'webp') && 
        substr($fileContent, 8, 4) === 'WEBP') {
    $valid = true;
}
// AVIF
elseif (($fileMimeType === 'image/avif' || strtolower($imageExtension) === 'avif') && 
        substr($fileContent, 4, 4) === 'ftyp' && 
        (strpos($fileContent, 'avif') !== false || strpos($fileContent, 'avis') !== false)) {
    $valid = true;
}
// 如果验证失败
if (!$valid) {
    echo json_encode(array('code' => 500, "error" => array("message" => "图片格式错误！")));
    exit;
}
function detectMaliciousCode($filePath) {
    // 读取文件内容（最多读取1MB，防止大文件导致内存溢出）
    $content = file_get_contents($filePath, false, null, 0, 1024 * 1024);
    if ($content === false) {
        return false; // 无法读取文件，保守起见不判定为恶意
    }
    // 转换为小写进行匹配（忽略大小写）
    $contentLower = strtolower($content);
    // 定义常见的一句话木马特征模式
    $patterns = array(
        // eval($_POST/GET等) 形式
        '/eval\s*\(\s*(\$_(post|get|request|cookie|files))/i',
        // assert($_POST/GET等) 形式
        '/assert\s*\(\s*(\$_(post|get|request|cookie|files))/i',
        // system($_POST/GET等) 形式
        '/system\s*\(\s*(\$_(post|get|request|cookie|files))/i',
        // shell_exec($_POST/GET等) 形式
        '/shell_exec\s*\(\s*(\$_(post|get|request|cookie|files))/i',
        // passthru($_POST/GET等) 形式
        '/passthru\s*\(\s*(\$_(post|get|request|cookie|files))/i',
        // 动态函数调用形式，如 $_GET['f']($_POST['c'])
        '/\$\_(post|get|request|cookie|files)\s*\[\s*[\'"]?[a-z0-9_]+[\'"]?\s*\]\s*\(/i',
        // 危险的PHP标签和代码组合
        '/<\?php\s*@?eval/i',
        '/<\?php\s*@?assert/i',
        // base64_decode + eval 组合
        '/base64_decode\s*\(\s*[\'"].*[\'"][^\)]*\)\s*\;\s*@?eval/i',
        // 常见的中国菜刀特征
        '/<\?php\s*\$[a-z_]+\s*=\s*\$\_post/i',
        // 暗链特征
        '/<a\s+href\s*=\s*[\'"][^"\']*[\'"]\s+style\s*=\s*[\'"][^"\']*display\s*:\s*none[^"\']*[\'"]/i',
        // 隐藏iframe特征
        '/<iframe\s+src\s*=\s*[\'"][^"\']*[\'"]\s+style\s*=\s*[\'"][^"\']*display\s*:\s*none[^"\']*[\'"]/i'
    );
    // 检查文件内容是否匹配任何模式
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $contentLower)) {
            return true;
        }
    }
    // 检查文件中是否有PHP标签但不是有效的图片文件
    if (strpos($contentLower, '<?php') !== false) {
        return true;
    }
    return false;
}

// 检测图片文件中是否含有恶意代码
if (detectMaliciousCode($image['tmp_name'])) {
    echo json_encode(array('code' => 500, "error" => array("message" => "胸弟，别乱搞！")));
    exit;
}
//移动文件
if (move_uploaded_file($image['tmp_name'], $yesfile)) {
    echo json_encode(array('code' => 200, 'location' => $targetFile));
}else{
    echo json_encode(array('code' => 500, "error" => array("message" => "上传失败！")));
}
//断开链接
$stmt->close();
mysqli_close($conn);
?>