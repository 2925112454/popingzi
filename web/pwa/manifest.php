<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// ========== 第二步：安全包含数据库文件 + 错误处理 ==========
$conn = null;
$dbError = false;
// 先检查文件是否存在
$connFile = $_SERVER['DOCUMENT_ROOT'] . '/inc/conn.php';
if (file_exists($connFile)) {
    include $connFile;
    // 检查数据库连接是否成功
    if (!isset($conn) || $conn->connect_errno) {
        $dbError = true;
    }
} else {
    $dbError = true;
}

// ========== 第三步：安全的字符串清理函数 ==========
function cleanString($str, $mergeSpace = true) {
    if (!is_string($str)) $str = ''; // 防止非字符串传入
    $str = strip_tags($str);
    $str = preg_replace('/[\r\n\t]/', '', $str);
    $str = trim($str);
    if ($mergeSpace) {
        $str = preg_replace('/\s+/', ' ', $str);
    }
    return $str;
}

// ========== 第四步：数据库查询 + 兜底逻辑 ==========
$webtext = "没有设置网站标题";
$webvar = "没有设置网站简介";
$webtextx = ''; // 初始化变量，避免未定义
$webvarx = '';

if (!$dbError && $conn) {
    $sql = "select webtext,webvar from ppz_web where webid=1";
    // 预处理SQL，防止注入+错误捕获
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc(); // 单条数据无需循环
            $webtextx = $row["webtext"] ?? '';
            $webvarx = $row["webvar"] ?? '';
        }
        $stmt->close();
    }
    $conn->close();
}

// 赋值时做空值判断，避免未定义变量
if (!empty($webtextx)) {
    $webtext = cleanString($webtextx);
}
if (!empty($webvarx)) {
    $webvar = cleanString($webvarx);
}

// ========== 第五步：安全的协议/域名判断 ==========
// 强制HTTPS，避免$_SERVER变量未定义
$protocol = 'https://';
// 兜底域名
$domain = $_SERVER['HTTP_HOST'] ?? '';
$currentUrl = $protocol . $domain;

// ========== 第六步：Manifest配置 ==========
$siteInfo = [
    'name' => $webtext,
    'short_name' => $webtext,
    'description' => $webvar,
    'theme_color' => '#2196F3',
    'background_color' => '#ffffff',
    'display' => 'standalone',
    'display_override' => ['standalone', 'fullscreen'],
    'start_url' => '/',
    'scope' => '/',
    'lang' => 'zh-CN',
    'prefer_related_applications' => false
];

$manifest = [
    'name' => $siteInfo['name'],
    'short_name' => $siteInfo['short_name'],
    'description' => $siteInfo['description'],
    'theme_color' => $siteInfo['theme_color'],
    'background_color' => $siteInfo['background_color'],
    'display' => $siteInfo['display'],
    'start_url' => $siteInfo['start_url'],
    'scope' => $siteInfo['scope'],
    'lang' => $siteInfo['lang'],
    'orientation' => 'portrait-primary',
    'prefer_related_applications' => $siteInfo['prefer_related_applications'],
    'icons' => [
        [
            'src' => $currentUrl . '/pwa/icons/icon-192x192.png',
            'sizes' => '192x192',
            'type' => 'image/png',
            'purpose' => 'maskable any'
        ],
        [
            'src' => $currentUrl . '/pwa/icons/icon-512x512.png',
            'sizes' => '512x512',
            'type' => 'image/png',
            'purpose' => 'maskable any'
        ]
    ]
];

// ========== 第七步：安全的JSON输出  ==========
$jsonOutput = json_encode($manifest, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
// 编码失败时输出兜底JSON
if ($jsonOutput === false) {
    $fallbackManifest = [
        'name' => '网站名称',
        'short_name' => '网站名称',
        'display' => 'standalone',
        'start_url' => '/',
        'icons' => [
            ['src' => '//pwa/icons/icon-192x192.png', 'sizes' => '192x192', 'type' => 'image/png']
        ]
    ];
    echo json_encode($fallbackManifest, JSON_UNESCAPED_UNICODE);
} else {
    echo $jsonOutput;
}
?>