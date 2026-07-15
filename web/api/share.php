<?php
header('Content-Type: application/json; charset=utf-8');
include $_SERVER['DOCUMENT_ROOT'] . '/inc/conn.php'; // 链接数据库
$id = isset($_POST['id']) ? $_POST['id'] : '';

function isPositiveInt($id)
{
    if (!preg_match('/^[1-9]\d*$/', $id)) {
        return false;
    }
    return true;
}

function html_to_plain_substr($htmlx, $lengthx)
{
    // 移除script、style区块
    $htmlx = preg_replace('#<(script|style).*?>.*?</\\1>#is', '', $htmlx);
    $textx = strip_tags($htmlx);
    $textx = html_entity_decode($textx, ENT_QUOTES, 'UTF-8');

    // ========== 敏感内容过滤 ==========
    // 1. 密码相关内容过滤
    $pwdPattern = '/(解压码|提取码|密码|password).{0,8}/iu';
    $textx = preg_replace($pwdPattern, '******', $textx);

    // 2. 邮箱地址过滤（标准邮箱正则，支持中英文前后字符）
    $emailPattern = '/[a-zA-Z0-9_\-\.\+]+@[a-zA-Z0-9_\-\.]+\.[a-zA-Z]{2,}/iu';
    $textx = preg_replace($emailPattern, '******', $textx);

    // 3. URL拦截正则
    $pattern = '/(?:(?:https?|ftps?):\/\/|\/\/|www\.)[^\s，。！？；：""\'()（）、]+|[a-zA-Z0-9_-]+\.[a-zA-Z0-9_-]{1,}[^\s，。！？；：""\'()（）、]*/iu';
    $textx = preg_replace($pattern, '******', $textx);

    // 合并空白 + 首尾修剪
    $textx = preg_replace('/\s+/u', ' ', $textx);
    $textx = trim($textx);

    // 优先使用mb截取UTF8字符
    if (function_exists('mb_substr')) {
        $textx = mb_substr($textx, 0, $lengthx, 'UTF-8');
    } else {
        $textx = substr($textx, 0, $lengthx * 1);
    }

    // 截取完成后再次去除末尾空格
    $textx = trim($textx);
    return $textx . "……";
}

// 参数校验
if (empty($id) || !isPositiveInt($id)) {
    echo json_encode(array(
        'code' => 500,
        'title' => '',
        'img' => '',
        'name' => '',
        'type' => '',
        'text' => '',
        'isvideo' => 0
    ), JSON_UNESCAPED_UNICODE);
    exit;
}

$newid = (int)$id;

// 初始化变量防止未定义警告
$title  = '';
$name = '';
$type = '';
$img  = '';
$text = '';
$isvideo = 0;

$sql = "SELECT rowtexe,rowbigtext,rowadmin,rowif,rowfl,rowimg,videotext FROM ppz_row WHERE rowid = ? AND rowyes = 4";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $newid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) < 1) {
    echo json_encode(array(
        'code' => 500,
        'title' => '',
        'img' => '',
        'name' => '',
        'type' => '',
        'text' => '',
        'isvideo' => 0
    ), JSON_UNESCAPED_UNICODE);
    exit;
}

$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt); // 关闭预处理

$title = $row['rowtexe'];
$rowbigtext = $row['rowbigtext'];
$rowadmin   = $row['rowadmin'];
$rowif      = $row['rowif'];
$rowfl      = $row['rowfl'];
$rowimg     = $row['rowimg'];
$videotext  = $row['videotext'];

// 查询作者昵称
$usersql = "SELECT uname FROM ppz_newusername WHERE uid = ?";
$ustmt = mysqli_prepare($conn, $usersql);
mysqli_stmt_bind_param($ustmt, 'i', $rowadmin);
mysqli_stmt_execute($ustmt);
$userresult = mysqli_stmt_get_result($ustmt);
if (mysqli_num_rows($userresult) > 0) {
    $userrow = mysqli_fetch_assoc($userresult);
    $name = $userrow['uname'];
}
mysqli_stmt_close($ustmt);
// 查询分类名称
$flsql = "SELECT flname FROM ppz_fl WHERE flid = ?";
$flstmt = mysqli_prepare($conn, $flsql);
mysqli_stmt_bind_param($flstmt, 'i', $rowfl);
mysqli_stmt_execute($flstmt);
$flresult = mysqli_stmt_get_result($flstmt);
if (mysqli_num_rows($flresult) > 0) {
    $flrow = mysqli_fetch_assoc($flresult);
    $type = $flrow['flname'];
}
mysqli_stmt_close($flstmt);
// 判断是否视频
if ($rowif == 3) {
    $isvideo = 1;
} else {
    $isvideo = 0;
}
// 逻辑分支处理内容与封面
if ($rowif == 3) {
    // 视频
    $text = !empty($videotext) ? html_to_plain_substr($videotext, 80) : '';
    $img  = !empty($rowimg) ? $rowimg : '';
} elseif ($rowif == 2) {
    // 相册
    $text = !empty($videotext) ? html_to_plain_substr($videotext, 80) : '';
    if (!empty($rowimg)) {
        $img = $rowimg;
    } else {
        $rowbigtextarr = explode('|', $rowbigtext);
        $filterArr = array_filter($rowbigtextarr);
        $img = empty($filterArr) ? '' : $rowbigtextarr[0];
    }
} else {
    // 图文
    $text = !empty($rowbigtext) ? html_to_plain_substr($rowbigtext, 80) : '';
    if (!empty($rowimg)) {
        $img = $rowimg;
    } else {
        if (preg_match('#<img.*?src="(.*?)".*?>#', $rowbigtext, $match)) {
            $img = $match[1];
        } else {
            $img = '';
        }
    }
}
// 输出JSON
echo json_encode(array(
    'code' => 200,
    'title' => $title,
    'img' => $img,
    'name' => $name,
    'type' => $type,
    'text' => $text,
    'isvideo' => $isvideo
), JSON_UNESCAPED_UNICODE);
exit;
?>
