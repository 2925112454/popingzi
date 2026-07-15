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

    $filesurl= $_SERVER['DOCUMENT_ROOT'] . '/robots.txt';//robots.txt文件路径常量

    if (!file_exists($filesurl)) {
        throw new Exception('文件不存在！');
    }

    //获取文件大小
    $filesize = filesize($filesurl);

    if  ($filesize<=0) {
        throw new Exception('文件没有内容！');
    }

     // 读取文件内容
    $robotsContent = file_get_contents($filesurl);

    // 移除UTF-8 BOM
    if (substr($robotsContent, 0, 3) === pack('CCC', 0xEF, 0xBB, 0xBF)) {
        $robotsContent = substr($robotsContent, 3);
    }

    // 返回成功响应
    echo json_encode(['code' => 200, 'msg' => '', 'data' => $robotsContent]);
    
    
    
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