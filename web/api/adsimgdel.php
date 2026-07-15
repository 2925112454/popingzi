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
    echo json_encode(array('code' => 500,'msg' => '您无权限进行此操作！'));
    exit;
}
    $fliesurl='/upload/ads-images/';//广告图片目录
    $flies=$_SERVER['DOCUMENT_ROOT'] .$fliesurl;

    if (!file_exists($flies)) {
       echo json_encode(array('code' => 200,'msg' => ''));
       exit;
    }
        $js_img=[];//保存js包含的所有图片
        $images=[];//保存所有图片
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'ico','avif'];//// 对常见图片文件扩展名进行定义
        function get_js_images($ajs,$imageExtensionsa) {
        // 用于存储检测到的图片 URL
        $js_img = [];
        $extPattern = implode('|', $imageExtensionsa);
        // 对单引号和双引号字符串中的图片 URL 进行匹配
        $stringPattern = "/(['\"])(?:(?=(\\\\?))\\2.)*?\\.({$extPattern})(\\?[^'\"]+)?\\1/i";
        preg_match_all($stringPattern, $ajs, $stringMatches);

        // 对赋值语句中的图片 URL 进行匹配，例如：var img = 'image.jpg';
        $assignmentPattern = "/\b(?:var|let|const)?\s*[\w\[\]]+\s*=\s*['\"]([^'\"]+\.({$extPattern}))['\"]/i";
        preg_match_all($assignmentPattern, $ajs, $assignmentMatches);

        // 对数组中的图片 URL 进行匹配，例如：['image.jpg', 'image2.png']
        $arrayPattern = "/\[([^[\]]*?)\]/";
        preg_match_all($arrayPattern, $ajs, $arrayMatches);
        $arrayImages = [];
        foreach ($arrayMatches[0] as $arrayMatch) {
            preg_match_all($stringPattern, $arrayMatch, $tempMatches);
            $arrayImages = array_merge($arrayImages, $tempMatches[0]);
        }

        // 对函数参数里的图片 URL 进行匹配，例如：loadImage('image.jpg')
        $functionPattern = "/\b\w+\s*\(\s*['\"]([^'\"]+\.({$extPattern}))['\"]\s*\)/i";
        preg_match_all($functionPattern, $ajs, $functionMatches);

        // 把所有匹配结果合并到一个数组中
        $allMatches = array_merge(
            $stringMatches[0],
            $assignmentMatches[1],
            $arrayImages,
            $functionMatches[1]
        );

        // 对结果进行处理，去除引号并过滤重复项
        foreach ($allMatches as $match) {
            $url = trim($match, '\'"');
            $js_img[] = $url;
        }

        // 去除重复的 URL
        $js_img = array_unique($js_img);
        return $js_img;
    }

    function normalizeImagePaths(array $imagesArr) {
    $result = [];
    foreach ($imagesArr as $path) {
        // 去除首尾空白字符
        $path = trim($path);
        // 跳过空路径
        if ($path === '') {
            continue;
        }
        // 处理以 '../' 开头的情况
        if (strpos($path, '../')) {
            $result[] = '/' . substr($path, 3);
        }
        // 处理以 './' 开头的情况
        elseif (strpos($path, './')) {
            $result[] = '/' . substr($path, 2);
        }
        // 处理以 '//' 开头的情况
        elseif (strpos($path, '//')) {
            // 递归处理直到不以 '//' 开头
            $cleanPath = ltrim($path, '/');
            $result[] = '/' . $cleanPath;
        }
        // 其他情况：确保以单个 '/' 开头
        else {
            $result[] = '/' . ltrim($path, '/');
        }
    }
    return $result;
}

function cleanImagePaths(array $paths) {
        return array_map(function($pathx) {
            // 使用正则表达式替换开头的多个斜杠为单个斜杠
            return preg_replace('/^\/+/', '/', $pathx);
        }, $paths);
    }

    function filterExternalImages(array $imagePaths){
        return array_filter($imagePaths, function($path) {
            // 检查是否为空路径
            if (empty($path)) {
                return false;
            }
            
            // 移除可能存在的前导斜杠
            $cleanPath = ltrim($path, '/');
            
            // 检查是否以协议开头
            return !preg_match('/^(http|https|ftp|ftps):/i', $cleanPath);
        });
    }

    function getImageFiles($directory, array $imageExtensionsx, $recursive = true) {
    $imageFiles = [];

    // 统一路径格式，去除多余的斜杠
    $directory = rtrim($directory, '/\\');

    // 检查目录是否存在
    if (!is_dir($directory)) {
        echo "错误：目录 '$directory' 不存在\n";
        return $imageFiles;
    }

    // 打开目录
    $dir = opendir($directory);
    if (!$dir) {
        echo "错误：无法打开目录 '$directory'\n";
        return $imageFiles;
    }

    // 遍历目录中的所有文件
    while (($file = readdir($dir)) !== false) {
        // 跳过当前目录和上级目录
        if ($file === '.' || $file === '..') {
            continue;
        }

        $filePath = $directory . '/' . $file;

        // 处理子目录（如果递归选项开启）
        if (is_dir($filePath) && $recursive) {
            $imageFiles = array_merge($imageFiles, getImageFiles($filePath, $imageExtensionsx, true));
        }
        // 处理文件
        elseif (is_file($filePath)) {
            // 获取文件扩展名
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            // 检查是否为图片文件
            if (in_array($extension, $imageExtensionsx)) {
                // 使用 realpath 去除多余斜杠
                $imageFiles[] = str_replace('\\', '/', realpath($filePath));
            }
        }
    }

    // 关闭目录
    closedir($dir);

    return $imageFiles;
    }

    function add_absolute_path_to_images(array $imagePaths,$flies,$rep) {
        $result = [];        
        foreach ($imagePaths as $path) {
            //判断路径是不是rep开头
            if (strpos($path, $rep) === 0) {
                $path = substr($path, strlen($rep));
            }
            // 移除图片路径开头的斜杠
            $cleanedPath = ltrim($path, '/');
            // 拼接绝对路径
            $result[] = $flies . $cleanedPath;
        }
        return $result;
    }

    //链接广告配置数据库
    $adssql = "SELECT * FROM ppz_ads ORDER BY aid ASC LIMIT 7";
    $adretval = mysqli_query($conn, $adssql);
    if (mysqli_num_rows($adretval) != 7) {
        echo json_encode(array('code' => 500,'msg' => '数据库结构错误！'));
        exit;
    }
    $adsimgdel = 0;
    while ($rowads = mysqli_fetch_assoc($adretval)) {
        $aid = $rowads['aid'];//  广告ID
        $aimg= $rowads['aimg'];// 广告图片
        $ajs= $rowads['ajs'];//  自定义JS
        if ($aid == 7) {
            //获取js里面包含的所有本地图片（不含站外图片）
            $js_img = get_js_images($ajs,$imageExtensions);
        }else{
            //获取aimg图片
            $images[] = $aimg;            
        }
    }
    //去除空
    $js_img = array_filter($js_img);
    $images = array_unique($images);
    $images = array_filter($images);
    //合并数组
    $imagesarr = array_merge($images,$js_img);
    $imagesarr = array_filter($imagesarr);
    $imagesarr = array_unique($imagesarr);
    //去除路径中的参数，比如  ?v=123456789或者  &v=123456789
    $imagesarr = array_map(function ($item) {
        return preg_replace('/\?.*$/', '', $item);
    }, $imagesarr);
    //判断路径开头是否有/，没有则加上
    $imagesarr = array_map(function ($item) {
        if (strpos($item, '/') !== 0) {
            return '/' . $item;
        }
        return $item;
    }, $imagesarr);

    $imagesarr = normalizeImagePaths($imagesarr);//去除多余的斜杠和空格
    $imagesarr = cleanImagePaths($imagesarr);//去除多余的斜杠和空格
    $imagesarr = filterExternalImages($imagesarr);//过滤掉外部图片


    //获取$flies目录下的所有图片
    $allimages = getImageFiles($flies,$imageExtensions);

    if (empty($imagesarr)) {
        //删除目录下全部图片
        $delimages = $allimages;
        if (!empty($delimages)) {
            foreach ($delimages as $delimage) {
                if (file_exists($delimage)) {
                    unlink($delimage);
                    $adsimgdel++;
                }
            }
            echo json_encode(array('code' => 300,'msg' => '成功删除了'.$adsimgdel.'张图片'));
        } else {
            echo json_encode(array('code' => 200,'msg' => ''));
        }
    }else{
        
        if  (empty($allimages)) {
            echo json_encode(array('code' => 200,'msg' => ''));
        }else{
            //将$imagesarr转换为绝对路径
            $imagesarr = add_absolute_path_to_images($imagesarr,$flies,$fliesurl);
            $delimages = array_diff($allimages,$imagesarr);//删除除了$imagesarr以外的所有图片
            if (!empty($delimages)) {
                foreach ($delimages as $delimage) {
                    if (file_exists($delimage)) {
                        unlink($delimage);
                        $adsimgdel++;
                    }
                }
                echo json_encode(array('code' => 300,'msg' => '成功删除了'.$adsimgdel.'张图片'));
            } else {
                echo json_encode(array('code' => 200,'msg' => ''));
            }
        }
    }

    mysqli_close($conn);

?>