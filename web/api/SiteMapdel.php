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
        throw new Exception('错误操作！');
    }
    $user = $result->fetch_assoc();
    $ustatus = $user['ustatus'];
    // 只有站长(4)和副站长(3)有权限
    if ($ustatus != 4 && $ustatus != 3) {
        throw new Exception('错误操作！');
    }
    $stmt->close();
    if(!isset($_POST['type'])){
        throw new Exception('参数错误！');
    }
    $type = $_POST['type'];
    if(empty($type)||($type != 'xml' && $type != 'txt' &&  $type != 'robots')){
        throw new Exception('参数错误！');
    }

    if($type == 'xml'){
        $filename = 'sitemap.xml';
    }elseif($type == 'txt'){
        $filename = 'sitemap.txt';
    }elseif($type == 'robots'){
        $filename = 'robots.txt';
    }

    $url=$_SERVER['DOCUMENT_ROOT'] ."/".$filename;//获取文件路径

    if(file_exists($url)){
        unlink($url);
        echo json_encode(['code' => 200, 'msg' => '']);
    }else{
        throw new Exception('文件不存在！');
    }

} catch (Exception $e) {
    echo json_encode(['code' => 500, 'msg' => $e->getMessage()]);
} finally {
    // 确保数据库连接关闭
    if (isset($conn)) {
        $conn->close();
    }
}
?>