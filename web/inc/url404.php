<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION

// 验证并获取参数
if (!isset($_POST['uuid'])) {
    $_POST['uuid'] = "";
}
$dwid = trim($_POST['uuid']);

// 检查登录状态 - 修正：使用$_SESSION变量
if (empty($ppzusername)) {
    echo 500; // 错误操作
    exit;
}

// 验证参数有效性
if (empty($dwid) || !is_numeric($dwid) || $dwid < 1) {
    echo 500; // 错误操作
    exit;
}

// 简单的请求频率限制
$limit = 30;          // 最大请求次数
$duration = 1800;     // 时间窗口（秒）

// 初始化 session 中的计数器结构
if (!isset($_SESSION['url404'])) {
    $_SESSION['url404'] = [
        'count' => 0,
        'start_time' => time()
    ];
}

// 获取当前 session 中的计数信息
$requestInfo = &$_SESSION['url404']; // 使用引用方便修改

// 判断是否超过时间窗口
if (time() - $requestInfo['start_time'] > $duration) {
    // 超时，重置计数器
    $requestInfo['count'] = 0;
    $requestInfo['start_time'] = time();
}

// 增加访问次数
$requestInfo['count']++;

// 判断是否超出限制
if ($requestInfo['count'] > $limit) {
    echo 429; // 请求过多，请稍后再试
    exit;
}

include __DIR__.'/conn.php';//连接数据库
$ch = null;
$stmt = null;

try {
    // 准备SQL查询，防止SQL注入
    $stmt = $conn->prepare("SELECT rowdw FROM ppz_row WHERE binary rowid = ?");
    $stmt->bind_param("i", $dwid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("404");
    }
    
    $row = $result->fetch_assoc();
    $wd = $row['rowdw'];
    
    if (empty($wd)) {
        throw new Exception("404");
    }
    
    $dow = explode(",", $wd);
    $dwurl = isset($dow[1]) ? $dow[1] : '';
    
    if (empty($dwurl)) {
        throw new Exception("404"); // 链接不存在或无法访问(404)！
    }
    
    // 验证下载链接有效性
    $ch = curl_init($dwurl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 设置超时时间
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($httpCode == 404) {
        throw new Exception("404"); // 链接不存在或无法访问(404)！
    } elseif ($httpCode >= 500) {
        throw new Exception("401"); // 链接页面的服务器存在错误(500)！
    } else {
        if ($response !== false) {
            // 使用DOMDocument提取标题更可靠
            $dom = new DOMDocument();
            @$dom->loadHTML($response);
            $title = $dom->getElementsByTagName('title')->item(0);
            
            if ($title) {
                echo $title->textContent;
            } else {
                echo "未找到标题";
            }
        } else {
            throw new Exception("403"); // 该文件下载链接无法访问或访问超时！
        }
    }
    
} catch (Exception $e) {
    echo $e->getMessage(); // 处理异常情况
} finally {
    // 确保所有资源都被正确释放
    if ($ch) curl_close($ch);
    if ($stmt) $stmt->close();
    if (isset($conn) && $conn instanceof mysqli) $conn->close();
}
?>