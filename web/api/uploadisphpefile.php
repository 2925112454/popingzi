<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';

// 定义JSON响应函数
function response($code, $message = '', $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'code' => $code,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// 判断是否登录
if (empty($ppzusername)) {
    response(500, '错误操作！');
}

include $_SERVER['DOCUMENT_ROOT'] . '/inc/conn.php';

// 获取登录会员信息
$stmt = $conn->prepare("SELECT * FROM ppz_newusername WHERE binary uusername = ?");
$stmt->bind_param("s", $ppzusername);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    response(2, '错误操作！');
}

$row = $result->fetch_assoc();
$ustatus = $row['ustatus'];

// 检查权限
if ($ustatus != 4 && $ustatus != 3) {
    response(2, '错误操作！');
}

$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp','ico', 'avif', 'mp4', 'mp3', 'wav', 'ogg', 'webm','7z','rar','zip'];

// 定义系统文件结构
$systemFiles = [
    'directories' => [
        'upload' => '/upload/',
        'images' => '/images/',
        'style' => '/style/',
        'video' => '/video/',
        'error' => '/error/',
    ],

    'dirs' => [
        '/api/', '/images/', '/inc/', '/style/', '/upload/','/user/', '/varcss/', '/video/',
        '/style/js/','/style/css/','/vip/','/user/myuser/','/user/edit/','/wap/','/subject/','/subject/admin/'
    ],
    
    'fileGroups' => [
        'core' => [
            '/anct.php', '/anctshow.php', '/index.php', '/list.php', '/sw.js',
            '/show.php', '/search.php', '/top.php', '/user.php', '/favicon.ico','/plreply.php'
        ],
        'api' => [
            '/api/card.php', '/api/carddel.php', '/api/cardedit.php', '/api/cardset.php',
            '/api/code.php', '/api/codedel.php', '/api/codeedit.php', '/api/codeset.php',
            '/api/delimg.php', '/api/delimgx.php', '/api/edit.php', '/api/newli.php',
            '/api/upimg.php', '/api/upimgall.php', '/api/upload.php', '/api/uploadimg.php',
            '/api/upvideoall.php', '/api/sessionnotice.php', '/api/smtp.php', '/api/smtpcs.php',
            '/api/telcs.php', '/api/telcsx.php', '/api/telset.php', '/api/index_1.php',
            '/api/index_2.php', '/api/indexads.php', '/api/get_carousel_data.php', '/api/index_3.php',
            '/api/upadsimg.php', '/api/uploadisphpe.php', '/api/uploadisphpefile.php','/api/adsset.php',
            '/api/adsimgdel.php','/api/SiteMap.php','/api/SiteMapdel.php','/api/Robots.php','/api/readrobots.php',
            '/api/tougaoimg.php','/api/post.php','/api/share.php','/api/og.php'
        ],
        'inc' => [
            '/inc/alert.php', '/inc/alldelmess.php', '/inc/alldelrow.php', '/inc/alldelservice.php',
            '/inc/alldeluser.php', '/inc/alleditservice.php', '/inc/allexirow.php', '/inc/allexiuser.php',
            '/inc/allmesdel.php', '/inc/allmesyes.php', '/inc/captcha.php', '/inc/carouseljson.php',
            '/inc/codepost.php', '/inc/commentalldel.php', '/inc/commentdel.php', '/inc/comments.php',
            '/inc/conn.php', '/inc/date.php', '/inc/daycss.php', '/inc/delete.php',
            '/inc/delservice.php', '/inc/down.php', '/inc/editmess.php', '/inc/editservice.php',
            '/inc/editservicefl.php', '/inc/emailpost.php', '/inc/fldel.php', '/inc/fledit.php',
            '/inc/flnew.php', '/inc/folus.php', '/inc/footer.php', '/inc/fucket.php',
            '/inc/ggcomments.php', '/inc/ggreply.php', '/inc/ggreptop.php', '/inc/header.php',
            '/inc/inc.php', '/inc/ipset.php', '/inc/letter.php', '/inc/login.php',
            '/inc/loginout.php', '/inc/logxpost.php', '/inc/mesdel.php', '/inc/mesyes.php',
            '/inc/mesyestow.php', '/inc/navdel.php', '/inc/newcard.php', '/inc/newcode.php',
            '/inc/newcomment.php', '/inc/newnav.php', '/inc/newnavtwo.php', '/inc/newnotice.php',
            '/inc/newpass.php', '/inc/newpost.php', '/inc/newservice.php', '/inc/nightcss.php',
            '/inc/quickly.php', '/inc/register.php', '/inc/regset.php', '/inc/reply.php',
            '/inc/replydel.php', '/inc/reptop.php', '/inc/right.php', '/inc/rowsc.php',
            '/inc/select.php', '/inc/session.php', '/inc/style.php', '/inc/styleset.php',
            '/inc/upfile.php', '/inc/upfileset.php', '/inc/uprowimg.php', '/inc/upuser.php',
            '/inc/url404.php', '/inc/viptime.php', '/inc/webmaxsize.php', '/inc/webset.php',
            '/inc/noticeform.php', '/inc/alldelnot.php',
            '/inc/phpmailer5.5/Exception.php', '/inc/phpmailer5.5/PHPMailer.php', '/inc/phpmailer5.5/SMTP.php','/inc/alldellog.php'
        ],
        'user' => [
            '/user/admin/inc/listrowimage.php', '/user/admin/inc/listrowvideo.php',
            '/user/admin/inc/listrowword.php', '/user/admin/inc/rowimage.php',
            '/user/admin/inc/rowuser.php', '/user/admin/inc/rowvideo.php',
            '/user/admin/inc/rowword.php', '/user/admin/inc/rowwordtow.php',
            '/user/admin/comment.php', '/user/admin/index.php', '/user/admin/list.php',
            '/user/admin/message.php', '/user/admin/navbar.php', '/user/admin/newrow.php',
            '/user/admin/notice.php', '/user/admin/regcode.php', '/user/admin/service.php',
            '/user/admin/useradmin.php', '/user/admin/usercard.php', '/user/admin/web.php',
            '/user/admin/emailset.php', '/user/admin/map.php', '/user/admin/ads.php',
            '/user/inc/edituser/index.php', '/user/loginx.php', '/user/message.php',
            '/user/popingzi.php', '/user/regtxt.php', '/user/user.php','/user/admin/log.php','/user/myuser/myvip.php','/user/myuser/my.php',
            '/user/edit/index.php','/user/edit/pass.php','/user/edit/email.php','/user/edit/code.php','/user/edit/tel.php','/user/myuser/myrow.php',
            '/user/edit/delmyrow.php','/user/myuser/mycomment.php','/user/edit/delmycomm.php','/user/myuser/mycollect.php','/user/edit/delmycoll.php',
            '/user/myuser/myfans.php','/user/myuser/myfollow.php','/user/myuser/mywork.php','/user/myuser/postwork.php','/user/myuser/post.php','/user/myuser/get_subcategories.php',
            '/user/myuser/mybuy.php','/user/edit/delwork.php','/user/edit/postwork.php','/user/admin/subiect.php'
        ],
        'varcss' => [
            '/varcss/diynight.php', '/varcss/diyday.php'
        ],
        'video' => [
            '/video/default.mp4'
        ],
        'images' => [
            '/images/sprite.png','/images/web/001.jpg','/images/web/002.jpg','/images/web/003.jpg',
            '/images/web/004.jpg','/images/web/005.jpg','/images/web/006.jpg','/images/web/007.jpg',
            '/images/web/008.jpg','/images/web/009.jpg','/images/web/default.jpg','/images/web/null.jpg',
            '/images/web/music.jpg','/images/web/model-bg.png','/images/web/model-bgn.png','/images/web/ubg.jpg',
        ],
        'vip' => [
            '/vip/index.php'
        ],
        'wap' => [
            '/wap/index.php'
        ],
        'subject' => [
            '/subject/allyessub.php','/subject/comment.php','/subject/delsub.php','/subject/detail.php','/subject/edit.php','/subject/index.php','/subject/like.php','/subject/newedit.php','/subject/newsub.php',
            '/subject/post.php','/subject/response.php','/subject/subdel.php','/subject/yessub.php','/subject/admin/alldel_rep.php','/subject/admin/alldel_sub.php','/subject/admin/del.php','/subject/admin/deltag.php',
            '/subject/admin/newrep.php','/subject/admin/newtag.php','/subject/admin/newtop.php','/subject/admin/newyes.php','/subject/admin/set.php','/subject/admin/tagset.php'
        ],
        'pwa' => [
            '/pwa/manifest.php','/pwa/offline.html','/pwa/dwindex.js'
        ],
        'upload' => [
            '/upload/.htaccess'//禁止上传目录执行PHP配置文件
        ],
    ]
];

// 递归扫描目录查找PHP文件，限制递归深度
function scanDirectoryForPhp($directoryPath, $maxDepth = 5, $currentDepth = 0) {
    if (!is_dir($directoryPath) || !is_readable($directoryPath) || $currentDepth > $maxDepth) {
        return [];
    }
    
    $phpFiles = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directoryPath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            $phpFiles[] = $file->getPathname();
        }
    }

    return $phpFiles;
}

// 检查上传目录中是否存在可疑文件，优化版
function checkUploadDirectoryForSuspiciousFiles($uploadDirectory) {
    $suspiciousFiles = [];
    $dangerFunctions = ['eval', 'system', 'exec', 'shell_exec', 'passthru'];
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif','bmp','webp','svg','ico','avif'];
    $maxFileSize = 5 * 1024 * 1024; // 最大扫描5MB的文件

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($uploadDirectory, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
        if ($file->isFile()) {
            if ($file->getSize() > $maxFileSize) {
                continue;
            }
            
            $fileContent = @file_get_contents($file->getPathname());
            if ($fileContent === false) {
                continue;
            }
            
            $fileExtension = pathinfo($file, PATHINFO_EXTENSION);

            // 检查是否包含危险函数
            foreach ($dangerFunctions as $function) {
                if (strpos($fileContent, $function) !== false) {
                    $suspiciousFiles[] = $file->getPathname();
                    break;
                }
            }

            // 检查图片文件是否为合法图片
            if (in_array(strtolower($fileExtension), $imageExtensions)) {
                $imageInfo = @getimagesize($file->getPathname());
                if (!$imageInfo) {
                    $suspiciousFiles[] = $file->getPathname();
                }
            }
        }
    }
    
    if (!empty($suspiciousFiles)) {
        $suspiciousFiles = array_unique($suspiciousFiles);
    }
   
    return $suspiciousFiles;
}

// 检查目录中是否有PHP文件
foreach ($systemFiles['directories'] as $dirName => $dirPath) {
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . rtrim($dirPath, '/');
    $phpFiles = scanDirectoryForPhp($fullPath);
    
    if (!empty($phpFiles)) {
        $relativePaths = array_map(function($file) use ($fullPath) {
            return substr($file, strlen($fullPath));
        }, $phpFiles);
        
        response(3, "在 {$dirName} 目录中发现PHP文件", [
            'directory' => $dirPath,
            'sample_files' => $relativePaths
        ]);
    }
}

// 扁平化文件列表
$requiredFiles = [];
foreach ($systemFiles['fileGroups'] as $group => $files) {
    $requiredFiles = array_merge($requiredFiles, $files);
}

// 检查缺失文件
$missingFiles = [];
foreach ($requiredFiles as $file) {
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . $file;
    if (!file_exists($fullPath)) {
        $missingFiles[] = $file;
    }
}

if (!empty($missingFiles)) {
    response(3, "系统缺少 " . count($missingFiles) . " 个必要文件", [
        'sample_files' => $missingFiles
    ]);
}

// 扫描整个网站查找未授权的PHP文件，限制递归深度
$rootDirectory = $_SERVER['DOCUMENT_ROOT'];
$foundFiles = scanDirectoryForPhp($rootDirectory, 3); // 只扫描3层深度

// 转换为相对路径并统一路径格式
$relativeFoundFiles = [];
foreach ($foundFiles as $file) {
    $relativePath = substr($file, strlen($rootDirectory));
    $relativePath = ltrim($relativePath, '/');
    
    if (!empty($relativePath)) {
        $relativePath = '/' . $relativePath;
    } else {
        $relativePath = '/' . basename($file);
    }
    
    $relativePath = str_replace('\\', '/', $relativePath);
    $relativePath = preg_replace('~/+~', '/', $relativePath);
    
    $relativeFoundFiles[] = $relativePath;
}

// 统一系统文件路径格式
$normalizedRequiredFiles = array_map(function($file) {
    return str_replace('\\', '/', $file);
}, $requiredFiles);

// 查找不在允许列表中的文件（不区分大小写）
$extraFiles = [];
foreach ($relativeFoundFiles as $foundFile) {
    $foundInRequired = false;
    foreach ($normalizedRequiredFiles as $requiredFile) {
        if (strcasecmp($foundFile, $requiredFile) === 0) {
            $foundInRequired = true;
            break;
        }
    }
    if (!$foundInRequired) {
        $extraFiles[] = $foundFile;
    }
}

// 输出详细的可疑文件信息
if (!empty($extraFiles)) {
    $sampleFiles = array_slice($extraFiles, 0, 10);
    
    response(3, "发现 " . count($extraFiles) . " 个可疑文件", [
        'sample_files' => $sampleFiles,
        'total' => count($extraFiles)
    ]);
}

//判断所有关键目录是否都存在
$missingDirs = [];
foreach ($systemFiles['dirs'] as $dir) {
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . $dir;
    if (!is_dir($fullPath)) {
        $missingDirs[] = $dir;
    }
}

if (!empty($missingDirs)) {
    response(3, "系统缺少 " . count($missingDirs) . " 个关键目录", [
        'sample_files' => $missingDirs
    ]);
}

//检查上传目录中的文件是否存在可疑情况，优化版
$uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . $systemFiles['directories']['upload'];
$suspiciousFiles = checkUploadDirectoryForSuspiciousFiles($uploadDirectory);
if (!empty($suspiciousFiles)) {
    $relativeSuspiciousFiles = array_map(function($file) use ($uploadDirectory) {
        return substr($file, strlen($uploadDirectory));
    }, $suspiciousFiles);
    response(3, "{$systemFiles['directories']['upload']}存在 " . count($suspiciousFiles) . " 个可疑文件", [
        'sample_files' => $relativeSuspiciousFiles,
        'total' => count($suspiciousFiles)
    ]);
}

//检测上传目录是否有白名单以外的文件，优化版
$invalidFiles = [];
$maxScanFiles = 500; // 最多扫描500个文件
$fileCount = 0;

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($uploadDirectory, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $file) {
    if ($file->isFile()) {
        $fileCount++;
        if ($fileCount > $maxScanFiles) {
            break;
        }
        $fileName = $file->getFilename();
        if ($fileName === '.htaccess') {
            continue;
        }
        $fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            $invalidFiles[] = substr($file->getPathname(), strlen($uploadDirectory));
        }
    }
}


if (!empty($invalidFiles)) {
    response(3, "在{$systemFiles['directories']['upload']}发现 " . count($invalidFiles) . " 个未知文件", [
        'sample_files' => $invalidFiles, 
        'total' => count($invalidFiles)
    ]);
}

// 所有检查通过
response(1, '扫描完毕，文件未发现异常！');
?>