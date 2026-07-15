<?php
header("Content-Type: text/html; charset=utf-8");
mb_internal_encoding("UTF-8");
mb_http_output("UTF-8");
ini_set('default_charset', 'utf-8');

if (!function_exists('mysqli_connect')) {
    die('MySQLi 扩展未启用，请检查 PHP 配置。');
    exit;
}

// 2. 数据库配置（根据实际情况修改）
$mysql_server_name = '127.0.0.1';     // 数据库服务器地址（推荐使用IP地址，域名在Mysql会造成很大的延迟）
$mysql_username = 'root';             // 数据库用户名
$mysql_password = '123456';           // 数据库密码
$mysql_database = 'popingzi';         // 数据库名

// 3. 连接数据库
$conn = @mysqli_connect(
    $mysql_server_name,
    $mysql_username,
    $mysql_password
);

// 4. 检查连接是否成功，并输出详细错误
if (mysqli_connect_errno()) { 
    die("连接 MySQL 失败！");
    //die("连接 MySQL 失败: " . mysqli_connect_error()); // 可选，输出详细错误！
    exit;
}

// 5. 选择数据库
if (!mysqli_select_db($conn, $mysql_database)) {
    die("选择数据库失败！");
    //die("选择数据库失败：" . mysqli_error($conn)); // 可选，输出详细错误！
    exit;
}

// 6. 检测MySQL版本并自动选择字符集
$mysql_version = mysqli_get_server_info($conn);
// 提取主版本+次版本+修订版，转换为数字（如5.0.96 → 50096，5.5.3 → 50503）
$version_parts = explode('.', $mysql_version);
$version_num = (int)$version_parts[0] * 10000 + (int)$version_parts[1] * 100 + (int)$version_parts[2];

// 判定字符集：MySQL 5.5.3及以上支持utf8mb4，否则用utf8
if ($version_num >= 50503) {
    $charset = 'utf8mb4';
} else {
    $charset = 'utf8';
}

// 7. 设置字符集
if (method_exists($conn, 'set_charset')) {
    if (!$conn->set_charset($charset)) {
        die("设置字符集 {$charset} 失败！");
        //die("设置字符集 {$charset} 失败: " . $conn->error); // 可选，输出详细错误！
        exit;
    }
} else {
    // 兼容极低版本PHP的mysqli
    mysqli_query($conn, "SET NAMES {$charset}");
    if (mysqli_errno($conn)) {
        die("执行 SET NAMES {$charset} 失败！");
        //die("执行 SET NAMES {$charset} 失败: " . mysqli_error($conn)); // 可选，输出详细错误！
        exit;
    }
}
date_default_timezone_set('Asia/Shanghai');// 8. 启用上海时区
?>