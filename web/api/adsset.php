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

if ($ustatus != 4 && $ustatus != 3) {
    echo json_encode(array('code' => 500,'msg' => '错误操作！'));
    exit;
}
if(!isset($_POST["off_hf"])){
    $_POST["off_hf"]=0;
}
if(!isset($_POST["vipoff_hf"])){
    $_POST["vipoff_hf"]=0;
}
if(!isset($_POST["ads_hf"])){
    $_POST["ads_hf"]='';
}
if(!isset($_POST["img_hf"])){
    $_POST["img_hf"]='';
}
if(!isset($_POST["url_hf"])){
    $_POST["url_hf"]='';
}
if(!isset($_POST["time_hf"])){
    $_POST["time_hf"]='1980-01-01 00:00:00';
}

if(!isset($_POST["off_rowhf"])){
    $_POST["off_rowhf"]=0;
}
if(!isset($_POST["vipoff_rowhf"])){
    $_POST["vipoff_rowhf"]=0;
}
if(!isset($_POST["ads_rowhf"])){
    $_POST["ads_rowhf"]='';
}
if(!isset($_POST["img_rowhf"])){
    $_POST["img_rowhf"]='';
}
if(!isset($_POST["url_rowhf"])){
    $_POST["url_rowhf"]='';
}
if(!isset($_POST["time_rowhf"])){
    $_POST["time_rowhf"]='1980-01-01 00:00:00';
}

if(!isset($_POST["off_yxj"])){
    $_POST["off_yxj"]=0;
}
if(!isset($_POST["vipoff_yxj"])){
    $_POST["vipoff_yxj"]=0;
}
if(!isset($_POST["ads_yxj"])){
    $_POST["ads_yxj"]='';
}
if(!isset($_POST["img_yxj"])){
    $_POST["img_yxj"]='';
}
if(!isset($_POST["url_yxj"])){
    $_POST["url_yxj"]='';
}
if(!isset($_POST["time_yxj"])){
    $_POST["time_yxj"]='1980-01-01 00:00:00';
}

if(!isset($_POST["off_ybl"])){
    $_POST["off_ybl"]=0;
}
if(!isset($_POST["vipoff_ybl"])){
    $_POST["vipoff_ybl"]=0;
}
if(!isset($_POST["ads_ybl"])){
    $_POST["ads_ybl"]='';
}
if(!isset($_POST["img_ybl"])){
    $_POST["img_ybl"]='';
}
if(!isset($_POST["url_ybl"])){
    $_POST["url_ybl"]='';
}
if(!isset($_POST["time_ybl"])){
    $_POST["time_ybl"]='1980-01-01 00:00:00';
}

if(!isset($_POST["off_left"])){
    $_POST["off_left"]=0;
}
if(!isset($_POST["vipoff_left"])){
    $_POST["vipoff_left"]=0;
}
if(!isset($_POST["ads_left"])){
    $_POST["ads_left"]='';
}
if(!isset($_POST["img_left"])){
    $_POST["img_left"]='';
}
if(!isset($_POST["url_left"])){
    $_POST["url_left"]='';
}
if(!isset($_POST["time_left"])){
    $_POST["time_left"]='1980-01-01 00:00:00';
}

if(!isset($_POST["off_right"])){
    $_POST["off_right"]=0;
}
if(!isset($_POST["vipoff_right"])){
    $_POST["vipoff_right"]=0;
}
if(!isset($_POST["ads_right"])){
    $_POST["ads_right"]='';
}
if(!isset($_POST["img_right"])){
    $_POST["img_right"]='';
}
if(!isset($_POST["url_right"])){
    $_POST["url_right"]='';
}
if(!isset($_POST["time_right"])){
    $_POST["time_right"]='1980-01-01 00:00:00';
}

if(!isset($_POST["off_js"])){
    $_POST["off_js"]=0;
}
if(!isset($_POST["vipoff_js"])){
    $_POST["vipoff_js"]=0;
}
if(!isset($_POST["ads_js"])){
    $_POST["ads_js"]='';
}
if(!isset($_POST["time_js"])){
    $_POST["time_js"]='1980-01-01 00:00:00';
}
if(!isset($_POST["js"])){
    $_POST["js"]='';
}

$adsarr=[1,2,3,4];//允许的广告区域位置参数，出现1/2/3/4以外的数值则不合法
$adsoffarr=[0,1];//允许的开关参数，出现0/1以外的数值则不合法

function validateAdPositions($positionsStr, $allowedValues) {
    if (empty($positionsStr)) return true;
    $positions = explode(',', $positionsStr);
    foreach ($positions as $pos) {
        if (!in_array((int)$pos, $allowedValues)) {
            return false;
        }
    }
    return true;
}

function isValidDateTime($dateTimeString) {
    if (empty($dateTimeString)) {
        return false;
    }
    // 尝试不同的时间格式解析
    $formats = ['Y-m-d H:i:s', 'Y-m-d H:i', 'Y-m-d\TH:i', 'Y-m-d\TH:i:s'];
    foreach ($formats as $format) {
        $dateTime = DateTime::createFromFormat($format, $dateTimeString);
        if ($dateTime && $dateTime->format($format) === $dateTimeString) {
            return true;
        }
    }
    return false;
}

function ifimgnull($off,$img) {
    if ($off==1) {
       if(!empty($img)){
        return true;
       }else{
        return false;
       }
    }else{
        return true;
    }
}

//转换时间格式
function convertDateTime($dateTimex) {
    $formattedTime = date("Y-m-d H:i:s", strtotime($dateTimex));
    return $formattedTime;
}

function hasDangerousFunctions($jsCode) {
    $patterns = [
        // 检测 AJAX 相关函数
        '/\bxhr\s*\.\s*open\b/i',         // XMLHttpRequest.open()
        '/\bfetch\s*\(/i',                // fetch()
        '/\bjQuery\s*\.\s*ajax\b/i',      // jQuery.ajax()
        '/\baxios\s*\.\s*[a-z]+\b/i',     // axios.get()/post() 等
        '/\bjQuery\s*\.\s*(get|post|getJSON)\b/i', // jQuery.get() 等
        // 其他危险函数
        '/eval\s*\(/i',                   // eval()
        '/\bsessionStorage\b/i',          // 会话存储
        '/\bunescape\b/i',                // 编码绕过
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $jsCode)) {
            return true;
        }
    }
    
    return false;
}
function isjstag($t) {
    // 如果为空字符串，根据业务需求决定是否允许
    if (empty($t)) {
        return true;
    }
    
    // 反转义字符串
    $unescaped = htmlspecialchars_decode($t, ENT_QUOTES);

    // 检查是否为图片路径（简单判断，可根据需求扩展）
    if (filter_var($unescaped, FILTER_VALIDATE_URL) !== false || preg_match('/\.(jpg|jpeg|png|gif|bmp|webp|avif|ico)(\?.*)?$/i', $unescaped)) {
        return true;
    }

    // 定义script标签的正则表达式
    $pattern = '/^<script\s+(type="text\/javascript"\s+)?src="([^"]+)"(\s+type="text\/javascript")?>[\s]*<\/script>$/i';

    // 执行匹配
    if (preg_match($pattern, $unescaped, $matches)) {
        // 确保src不为空
        if (!empty($matches[2])) {
            return true;
        }
    }

    return false;
}


// 要验证的变量数组
$fieldsToValidate = [
    'img_hf'=>'横幅广告',
    'img_rowhf'=>'内容页横幅',
    'img_yxj'=>'右下角弹窗',
    'img_ybl'=>'右侧边栏',
    'img_left'=>'左侧悬浮',
    'img_right'=>'右侧悬浮',
];

//横幅广告
$adsid_hf=1;//广告ID
$off_hf=$_POST["off_hf"];//开关：0关闭，1开启
$vipoff_hf=$_POST["vipoff_hf"];//VIP免广告开关：0关闭，1开启
$ads_hf=$_POST["ads_hf"];//广告展示区域(只有：1,2,3,4四个区域可设置)
$img_hf=htmlspecialchars($_POST["img_hf"]);//图片
$url_hf=htmlspecialchars($_POST["url_hf"]);//链接
$time_hf=convertDateTime($_POST["time_hf"]);//展现时间

//内容页横幅广告
$adsid_rowhf=2;
$off_rowhf=$_POST["off_rowhf"];
$vipoff_rowhf=$_POST["vipoff_rowhf"];
$ads_rowhf=$_POST["ads_rowhf"];//广告展示区域(只有：3可设置，包含其余值均不合法)
$img_rowhf=htmlspecialchars($_POST["img_rowhf"]);
$url_rowhf=htmlspecialchars($_POST["url_rowhf"]);
$time_rowhf=convertDateTime($_POST["time_rowhf"]);

//右下角弹窗
$adsid_yxj=3;
$off_yxj=$_POST["off_yxj"];
$vipoff_yxj=$_POST["vipoff_yxj"];
$ads_yxj=$_POST["ads_yxj"];
$img_yxj=htmlspecialchars($_POST["img_yxj"]);
$url_yxj=htmlspecialchars($_POST["url_yxj"]);
$time_yxj=convertDateTime($_POST["time_yxj"]);

//右边栏广告
$adsid_ybl=4;
$off_ybl=$_POST["off_ybl"];
$vipoff_ybl=$_POST["vipoff_ybl"];
$ads_ybl=$_POST["ads_ybl"];
$img_ybl=htmlspecialchars($_POST["img_ybl"]);
$url_ybl=htmlspecialchars($_POST["url_ybl"]);
$time_ybl=convertDateTime($_POST["time_ybl"]);

//左悬浮广告
$adsid_left=5;
$off_left=$_POST["off_left"];
$vipoff_left=$_POST["vipoff_left"];
$ads_left=$_POST["ads_left"];
$img_left=htmlspecialchars($_POST["img_left"]);
$url_left=htmlspecialchars($_POST["url_left"]);
$time_left=convertDateTime($_POST["time_left"]);

//右悬浮广告
$adsid_right=6;
$off_right=$_POST["off_right"];
$vipoff_right=$_POST["vipoff_right"];
$ads_right=$_POST["ads_right"];
$img_right=htmlspecialchars($_POST["img_right"]);
$url_right=htmlspecialchars($_POST["url_right"]);
$time_right=convertDateTime($_POST["time_right"]);

//自定义js广告
$adsid_js=7;
$off_js=$_POST["off_js"];
$vipoff_js=$_POST["vipoff_js"];
$ads_js=$_POST["ads_js"];
$time_js=convertDateTime($_POST["time_js"]);
if (!empty($_POST["js"])) {
    $js = mysqli_real_escape_string($conn, $_POST["js"]);//防止sql注入
} else {
    $js = '';
}

if (!empty($js)) {
    //检查JS危险函数
    if (hasDangerousFunctions($js)) {
        echo json_encode(['code' => 500, 'msg' => 'JS代码含有危险函数！']);
        exit;
    }
}

//验证广告位置参数
if (!validateAdPositions($ads_hf, $adsarr) ||
    !validateAdPositions($ads_rowhf, $adsarr) ||
    !validateAdPositions($ads_yxj, $adsarr) ||
    !validateAdPositions($ads_ybl, $adsarr) ||
    !validateAdPositions($ads_left, $adsarr) ||
    !validateAdPositions($ads_right, $adsarr) ||
    !validateAdPositions($ads_js, $adsarr)) {
    echo json_encode(['code' => 500, 'msg' => '错误操作！']);
    exit;
}

//判断横幅广告位置是否为3
if(!empty($ads_rowhf)){
    //转换为数字
    $ads_rowhfx=intval($ads_rowhf);
        if($ads_rowhfx<>3||!is_numeric($ads_rowhfx)){
            echo json_encode(['code' => 500, 'msg' => '错误操作！']);
            exit;
        }
}

//判断广告开关是否为空
if(is_null($off_hf) || is_null($off_rowhf) || is_null($off_yxj) || is_null($off_ybl) || is_null($off_left) || is_null($off_right) || is_null($off_js)){
    echo json_encode(array('code' => 500,'msg' => '错误操作！'));
    exit;
}

//判断VIP广告开关是否为空
if(is_null($vipoff_hf) || is_null($vipoff_rowhf) || is_null($vipoff_yxj) || is_null($vipoff_ybl) || is_null($vipoff_left) || is_null($vipoff_right) || is_null($vipoff_js)){
    echo json_encode(array('code' => 500,'msg' => '错误操作！'));
    exit;
}


//判断提交的数据是否合法(广告开关判断，不允许为空)
if(!in_array($off_hf,$adsoffarr) || !in_array($off_rowhf,$adsoffarr) || !in_array($off_yxj,$adsoffarr) || !in_array($off_ybl,$adsoffarr) || !in_array($off_left,$adsoffarr) || !in_array($off_right,$adsoffarr) || !in_array($off_js,$adsoffarr)){
    echo json_encode(array('code' => 500,'msg' => '错误操作！'));
    exit;
}

//判断提交的数据是否合法(VIP免广告开关判断，不允许为空)
if(!in_array($vipoff_hf,$adsoffarr) || !in_array($vipoff_rowhf,$adsoffarr) || !in_array($vipoff_yxj,$adsoffarr) || !in_array($vipoff_ybl,$adsoffarr) || !in_array($vipoff_left,$adsoffarr) || !in_array($vipoff_right,$adsoffarr) || !in_array($vipoff_js,$adsoffarr)){
    echo json_encode(array('code' => 500,'msg' => '错误操作！'));
    exit;
}

//判断时间格式是否合法
if(!isValidDateTime($time_hf) || !isValidDateTime($time_rowhf) || !isValidDateTime($time_yxj) || !isValidDateTime($time_ybl) || !isValidDateTime($time_left) || !isValidDateTime($time_right) || !isValidDateTime($time_js)){
    echo json_encode(array('code' => 500,'msg' => '时间格式错误！'));
    exit;
}

//当广告开启，判断图片是否为空
if(!ifimgnull($off_hf,$img_hf)||!ifimgnull($off_rowhf,$img_rowhf)||!ifimgnull($off_yxj,$img_yxj)||!ifimgnull($off_ybl,$img_ybl)||!ifimgnull($off_left,$img_left)||!ifimgnull($off_right,$img_right)){
    echo json_encode(array('code' => 500,'msg' => '开启广告 图片不能为空！'));
    exit;
}

//当自定义JS开启，判断js内容是否为空
if(!ifimgnull($off_js,$js)){
    echo json_encode(array('code' => 500,'msg' => '开启广告 JS内容不能为空！'));
    exit;
}

// 验证所有字段
foreach ($fieldsToValidate as $key => $label) {
    $value = $$key; 
    if (!$value && isset($_POST[$key])) {
        $value = $_POST[$key];
    }
    if (!isjstag($value)) {
        echo json_encode([
            'code' => 500,
            'msg' => "'{$label}' 的图片字段格式不合规！"
        ]);
        exit;
    }
}

try {
    //按id分别更新数据库
    $sql_hf = "UPDATE ppz_ads SET ayes='$off_hf',avip='$vipoff_hf',aeye='$ads_hf',aimg='$img_hf',aurl='$url_hf',atime='$time_hf' WHERE aid=1";
    $retval_hf = mysqli_query($conn, $sql_hf);
    if(!$retval_hf){
        echo json_encode(array('code' => 500,'msg' => '保存失败！'));
        exit;
    }
    $sql_rowhf = "UPDATE ppz_ads SET ayes='$off_rowhf',avip='$vipoff_rowhf',aeye='$ads_rowhf',aimg='$img_rowhf',aurl='$url_rowhf',atime='$time_rowhf' WHERE aid=2";
    $retval_rowhf = mysqli_query($conn, $sql_rowhf);
    if(!$retval_rowhf){
        echo json_encode(array('code' => 500,'msg' => '保存失败！'));
        exit;
    }
    $sql_yxj = "UPDATE ppz_ads SET ayes='$off_yxj',avip='$vipoff_yxj',aeye='$ads_yxj',aimg='$img_yxj',aurl='$url_yxj',atime='$time_yxj' WHERE aid=3";
    $retval_yxj = mysqli_query($conn, $sql_yxj);
    if(!$retval_yxj){
        echo json_encode(array('code' => 500,'msg' => '保存失败！'));
        exit;
    }
    $sql_ybl = "UPDATE ppz_ads SET ayes='$off_ybl',avip='$vipoff_ybl',aeye='$ads_ybl',aimg='$img_ybl',aurl='$url_ybl',atime='$time_ybl' WHERE aid=4";
    $retval_ybl = mysqli_query($conn, $sql_ybl);
    if(!$retval_ybl){
        echo json_encode(array('code' => 500,'msg' => '保存失败！'));
        exit;
    }
    $sql_left = "UPDATE ppz_ads SET ayes='$off_left',avip='$vipoff_left',aeye='$ads_left',aimg='$img_left',aurl='$url_left',atime='$time_left' WHERE aid=5";
    $retval_left = mysqli_query($conn, $sql_left);
    if(!$retval_left){
        echo json_encode(array('code' => 500,'msg' => '保存失败！'));
        exit;
    }
    $sql_right = "UPDATE ppz_ads SET ayes='$off_right',avip='$vipoff_right',aeye='$ads_right',aimg='$img_right',aurl='$url_right',atime='$time_right' WHERE aid=6";
    $retval_right = mysqli_query($conn, $sql_right);
    if(!$retval_right){
        echo json_encode(array('code' => 500,'msg' => '保存失败！'));
        exit;
    }
    $sql_js = "UPDATE ppz_ads SET ayes='$off_js',avip='$vipoff_js',aeye='$ads_js',ajs='$js',atime='$time_js' WHERE aid=7";
    $retval_js = mysqli_query($conn, $sql_js);
    if(!$retval_js){
        echo json_encode(array('code' => 500,'msg' => '保存失败！'));
        exit;
    }
    echo json_encode(array('code' => 200,'msg' => '保存成功！'));
}
catch (Exception $e) {
    echo json_encode(array('code' => 500,'msg' => '程序错误！'));
    exit;
}

mysqli_close($conn);
?>