<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量

// 检查是否登录
if (empty($ppzusername)) {
    echo json_encode(array('error' => 500));
    exit;
}

include $_SERVER['DOCUMENT_ROOT'].'/inc/conn.php';//链接数据库

// 获取登录会员信息
$rowsql = "SELECT * FROM ppz_newusername WHERE BINARY uusername = '$ppzusername'";
$rowretval = mysqli_query($conn, $rowsql);

if (mysqli_num_rows($rowretval) !== 1) {
    echo json_encode(array('error' => 500));
    exit;
}

// 获取用户状态和ID
$userData = mysqli_fetch_assoc($rowretval);
$ustatus = $userData['ustatus']; // 身份，1普通会员，2为管理员，3为副站长，4为站长；此处为LOGO上传操作，仅限站长身份操作
$uid = $userData['uid'];

// 检查用户权限
if ($ustatus == 4 || $ustatus == 3 || $ustatus == 2) {
    // 生成随机字符串函数
    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
        }
        
        return $randomString;
    }

    $daytime = date("Ymd", time()); // 当前日期：年月日
    
    // 检查是否有文件上传
    if (!isset($_FILES['files'])) {
        echo json_encode(array('error' => 500));
        exit();
    }
    
    $file = $_FILES['files']; // 获取上传文件信息（多个文件）
    
    // 将单文件转换为数组格式
    if (!is_array($file['name'])) {
        $file['name'] = array($file['name']);
        $file['type'] = array($file['type']);
        $file['tmp_name'] = array($file['tmp_name']);
        $file['error'] = array($file['error']);
        $file['size'] = array($file['size']);
    }
    
    $yesurl = array(); // 存放上传成功的文件路径
    
    /*自定义信息配置*/
    $maxsize = 1024; // 允许的单个文件最大大小，单位KB
    $maxallsize = 20; // 允许的总大小，单位MB
    $maxallmun = 20; // 允许的总文件数
    $maxwidth = 3600; // 允许的图片最大宽度，单位px
    $upfileurl = '/upload/' . $daytime . '/'; // 上传目录：相对路径
    $allowedTypes = array('image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif', 'application/octet-stream'); // 允许的文件类型
    $upext = array('jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'); // 允许上传的文件后缀
    
    /*转换单位*/
    $maxFileSize = $maxsize * 1024; // 单个文件最大大小，单位转换
    $maxTotalSize = $maxallsize * 1024 * 1024; // 总文件最大大小，单位转换

    // 判断上传目录是否存在，不存在则创建
    if (!is_dir($_SERVER['DOCUMENT_ROOT'] . $upfileurl)) {
        mkdir($_SERVER['DOCUMENT_ROOT'] . $upfileurl, 0777, true);
    }

    // 获取文件总数量
    $fileCount = count($file['name']);
    
    // 检查是否有文件
    if ($fileCount <= 0) {
        echo json_encode(array('error' => 404));
        exit();
    }
    
    // 判断文件总数量是否超过限制
    if ($fileCount > $maxallmun) {
        echo json_encode(array('error' => 501));
        exit();
    }
    
    // 获取所有文件的总大小
    $totalSize = 0;
    for ($i = 0; $i < $fileCount; $i++) {
        $totalSize += $file['size'][$i];
    }
    
    // 判断总大小是否超过限制
    if ($totalSize > $maxTotalSize) {
        echo json_encode(array('error' => 502));
        exit();
    }

    // 遍历文件数组
    foreach ($file['name'] as $key => $name) {
        $tmpName = $file['tmp_name'][$key]; // 临时文件名
        $mewname = date("YmdHis", time()) . generateRandomString(4) . "-" . $uid . "-"; // 文件名称规则：以当前时间+随机生成的4位数+用户ID作为文件名
        
        // 获取图片宽度
        $upimgwidth = @getimagesize($tmpName);
        
        // 获取文件后缀
        $fileExt = strtolower(pathinfo($file['name'][$key], PATHINFO_EXTENSION));
        
        // 组合最新文件路径
        $yesfile = $upfileurl . $mewname . "." . $fileExt;
        $url = $_SERVER['DOCUMENT_ROOT'] . $yesfile; // 获取上传目录绝对路径
        
        // 判断单个文件大小是否超过限制
        if ($file['size'][$key] > $maxFileSize) {
            echo json_encode(array('error' => 503));
            exit();
        }
        
        // 判断文件类型是否允许
        if (!in_array($file['type'][$key], $allowedTypes)) {
            echo json_encode(array('error' => 504));
            exit();
        }
        
        // 判断文件后缀是否允许
        if (!in_array($fileExt, $upext)) {
            echo json_encode(array('error' => 505));
            exit();
        }
        
        // 判断文件宽度是否超过限制，若超过则缩小，没超过则直接上传
        if (is_array($upimgwidth) && $upimgwidth[0] > $maxwidth) {
            $img = imagecreatefromstring(file_get_contents($tmpName));
            $width = imagesx($img);
            $height = imagesy($img);
            
            if (in_array($fileExt, array('jpg', 'jpeg', 'webp', 'png', 'avif')) && ($width > $maxwidth)) {
                $newwidth = $maxwidth;
                $newheight = $height * ($maxwidth / $width);
                $newimg = imagecreatetruecolor($newwidth, $newheight);
                
                // 对于 PNG 图像，设置透明背景
                if ($fileExt == 'png') {
                    // 分配一个透明颜色
                    $transparent = imagecolorallocatealpha($newimg, 0, 0, 0, 127);
                    // 填充背景为透明
                    imagefill($newimg, 0, 0, $transparent);
                }
                
                imagecopyresampled($newimg, $img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                
                $success = false;
                
                if ($fileExt == 'png') {
                    imagesavealpha($newimg, true); // 启用 alpha blending ;保留PNG透明通道
                    $success = imagepng($newimg, $url, 9);
                } else if ($fileExt == 'jpg' || $fileExt == 'jpeg') {
                    $success = imagejpeg($newimg, $url, 80);
                } else if ($fileExt == 'webp') {
                    if (function_exists('imagewebp')) {
                        $success = imagewebp($newimg, $url, 80);
                    } else {
                        echo json_encode(array('error' => 506));
                        exit();
                    }
                } else if ($fileExt == 'avif') {
                    if (function_exists('imageavif')) {
                        $success = imageavif($newimg, $url, 80);
                    } else {
                        echo json_encode(array('error' => 506));
                        exit();
                    }
                }
                
                imagedestroy($img);      // 释放内存
                imagedestroy($newimg);   // 释放内存
                
                if ($success) {
                    $yesurl[] = $yesfile;
                } else {
                    echo json_encode(array('error' => 400));
                    exit();
                }
            }
        } else {
            if (move_uploaded_file($tmpName, $url)) {
                $yesurl[] = $yesfile;
            } else {
                echo json_encode(array('error' => 400));
                exit();
            }
        }
    }

    if (!empty($yesurl)) {
        $yesurlx = implode(',', $yesurl);
        echo json_encode(array('error' => 200, 'msg' => $yesurlx));
    } else {
        echo json_encode(array('error' => 400));
    }
} else {
    echo json_encode(array('error' => 500));
}
?>