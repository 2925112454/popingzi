<?php
// 将响应转换为JSON格式并输出  
header('Content-Type: application/json');
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php'; // SESSION变量

// 安全检查和权限验证
if (empty($ppzusername)) {
    echo json_encode(['code' => 500, 'msg' => '错误操作']);
    exit;
}

// 数据库连接
include $_SERVER['DOCUMENT_ROOT'] . '/inc/conn.php';

try {
    // 验证用户权限
    $stmt = $conn->prepare("SELECT ustatus FROM ppz_newusername WHERE binary uusername = ?");
    $stmt->bind_param("s", $ppzusername);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows !== 1) {
        throw new Exception('错误操作');
    }
    
    $user = $result->fetch_assoc();
    $ustatus = $user['ustatus'];
    
    // 只有站长(4)和副站长(3)有权限
    if ($ustatus != 4 && $ustatus != 3) {
        throw new Exception('错误操作');
    }
    
    $stmt->close();
    
    // 输入验证
    $input = filter_input_array(INPUT_POST, [
        'url' => FILTER_SANITIZE_URL,//网站域名(不包含尾部斜杠的网站根URL)
        'google' => FILTER_VALIDATE_INT,//用户代理（User-Agent）:  谷歌（0关闭，1开启）
        'baidu' => FILTER_VALIDATE_INT,//用户代理（User-Agent）:  百度（0关闭，1开启）
        'bing' => FILTER_VALIDATE_INT,//用户代理（User-Agent）:  必应（0关闭，1开启）
        'sanliuling' => FILTER_VALIDATE_INT,//用户代理（User-Agent）:  360（0关闭，1开启）
        'Yahoo' => FILTER_VALIDATE_INT,//用户代理（User-Agent）:  雅虎（0关闭，1开启）
        'sogou' => FILTER_VALIDATE_INT,//用户代理（User-Agent）:  搜狗（0关闭，1开启）
        'toutiao' => FILTER_VALIDATE_INT,//用户代理（User-Agent）:  今日头条（0关闭，1开启）
        'douyin' => FILTER_VALIDATE_INT,//用户代理（User-Agent）:  抖音（0关闭，1开启）
        'wechat' => FILTER_VALIDATE_INT,//用户代理（User-Agent）:  微信搜索（0关闭，1开启）
        'shenma' => FILTER_VALIDATE_INT,//用户代理（User-Agent）:  神马UC（0关闭，1开启）
        'all' => FILTER_VALIDATE_INT,//用户代理（User-Agent）:  所有爬虫(*)（0关闭，1开启）
        'yes'=> FILTER_SANITIZE_STRING,//允许访问的路径
        'no'=> FILTER_SANITIZE_STRING,//禁止访问的路径
        'number'=> FILTER_VALIDATE_INT,//抓取延迟（秒）,为0则不延迟
        'map' => FILTER_SANITIZE_URL,//SiteMap地址
        'diy' => FILTER_SANITIZE_STRING,//自定义Robots规则(添加额外的自定义规则，每行一条规则)
    ]);
    
    // 确保必要的参数存在
    $url = isset($input['url']) ? trim($input['url']) : '';
    $map = isset($input['map']) ? trim($input['map']) : '';
    $diy = isset($input['diy']) ? trim($input['diy']) : '';
    $number = intval(isset($input['number']) ? trim($input['number']) : 0);
    $no = isset($input['no']) ? trim($input['no']) : '';
    $yes = isset($input['yes']) ? trim($input['yes']) : '';
    $google = isset($input['google']) ? trim($input['google']) : '';
    $baidu = isset($input['baidu']) ? trim($input['baidu']) : '';
    $bing = isset($input['bing']) ? trim($input['bing']) : '';
    $sanliuling = isset($input['sanliuling']) ? trim($input['sanliuling']) : '';
    $Yahoo = isset($input['Yahoo']) ? trim($input['Yahoo']) : '';
    $sogou = isset($input['sogou']) ? trim($input['sogou']) : '';
    $toutiao = isset($input['toutiao']) ? trim($input['toutiao']) : '';
    $douyin = isset($input['douyin']) ? trim($input['douyin']) : '';
    $wechat = isset($input['wechat']) ? trim($input['wechat']) : '';
    $shenma = isset($input['shenma']) ? trim($input['shenma']) : '';
    $all = isset($input['all']) ? trim($input['all']) : '';

    if(empty($url)){
        throw new Exception('网站域名不能为空！');
    }

    if($all==0&&$shenma==0&&$bing==0&&$sanliuling==0&&$Yahoo==0&&$sogou==0&&$toutiao==0&&$douyin==0&&$wechat==0&&$baidu==0&&$google==0){
        throw new Exception('至少选择一个搜索爬虫！');
    }

    if (empty($url) || empty($number) || !is_numeric($number) || 
        $number < 0 || $number > 315360000 || 
        !in_array($google, [0, 1]) || !in_array($baidu, [0, 1])||
        !in_array($bing, [0, 1])||  !in_array($sanliuling, [0, 1])||  !in_array($Yahoo, [0, 1])||
        !in_array($sogou, [0, 1])||  !in_array($toutiao, [0, 1])||!in_array($douyin, [0, 1])||!in_array($wechat, [0, 1])||
        !in_array($shenma, [0, 1])|| !in_array($all, [0, 1])) {
        throw new Exception('参数不正确');
    }

    $filesurl= $_SERVER['DOCUMENT_ROOT'] . '/robots.txt';//robots.txt文件路径常量
    // 处理允许和禁止路径的函数
    function processPaths($paths, $prefix) {
        $result = '';
        if (!empty($paths)) {
            // 分割多行路径
            $pathArray = explode("\n", $paths);
            foreach ($pathArray as $path) {
                $path = trim($path);
                if (!empty($path)) {
                    $result .= $prefix . ' ' . $path . "\n";
                }
            }
        }
        return $result;
    }

    // 初始化 robots.txt 内容
    $robotsContent = "# robots.txt 自动生成于 " . date('Y-m-d H:i:s') . "\n";
    $robotsContent .= "# ".$url."\n\n";

    //将配置规则保存为robots.txt文件
    if ($all == 1) {
        $robotsContent .= "User-agent: *\n";
        
        // 处理允许路径
        $robotsContent .= processPaths($yes, "Allow:");
        
        // 处理禁止路径
        $robotsContent .= processPaths($no, "Disallow:");

        // 抓取延迟
        if ($number > 0) {
            $robotsContent .= "Crawl-delay: " . $number . "\n";
        }

        $robotsContent .= "\n";
    }

    // 支持的爬虫列表
    $agents = [
        'google' => 'Googlebot',
        'baidu' => 'Baiduspider',
        'bing' => 'BingBot',
        'sanliuling' => '360Spider',
        'Yahoo' => 'YahooBot',
        'sogou' => 'Sogou',
        'toutiao' => 'BytedanceSpider',
        'douyin' => 'DuckDuckBot',
        'wechat' => 'WeChat',
        'shenma' => 'YisouSpider'
    ];

    foreach ($agents as $key => $agentName) {
        if (${$key} == 1) {
            $robotsContent .= "User-agent: " . $agentName . "\n";
            
            // 处理允许路径
            $robotsContent .= processPaths($yes, "Allow:");
            
            // 处理禁止路径
            $robotsContent .= processPaths($no, "Disallow:");

            // 抓取延迟
            if ($number >= 0) {
                $robotsContent .= "Crawl-delay: " . $number . "\n";
            }

            $robotsContent .= "\n";
        }
    }

    // 添加 Sitemap 地址
    if (!empty($map)) {
        $robotsContent .= "Sitemap: " . $map . "\n\n";
    }

    // 添加自定义规则
    if (!empty($diy)) {
        $robotsContent .= "# 自定义规则\n";
        $robotsContent .= $diy . "\n\n";
    }
    // 确保内容为UTF-8编码
    if (function_exists('mb_convert_encoding')) {
        $robotsContent = mb_convert_encoding($robotsContent, 'UTF-8', mb_detect_encoding($robotsContent, 'UTF-8, GBK, GB2312, ISO-8859-1', true));
    }

     // 添加UTF-8 BOM (可选)
    $bom = "\xEF\xBB\xBF";
    $robotsContent = $bom . $robotsContent;

    // 写入 robots.txt 文件
    if (file_put_contents($filesurl, $robotsContent) !== false) {
        echo json_encode(['code' => 200, 'msg' => '']);
    } else {
        throw new Exception('写入文件失败，请检查目录权限！');
    }
    
} catch (Exception $e) {
    // 错误处理
    echo json_encode(['code' => 500, 'msg' => $e->getMessage()]);
} finally {
    // 确保数据库连接关闭
    if (isset($conn)) {
        $conn->close();
    }
}
?>