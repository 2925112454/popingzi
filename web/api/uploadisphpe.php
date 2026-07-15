<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
$code="";//返回值
// 判断是否登录
if (empty($ppzusername)) {
    echo 500;
    exit;
}

include $_SERVER['DOCUMENT_ROOT'] . '/inc/conn.php'; // 链接数据库
// 获取登录会员信息
$stmt = $conn->prepare("SELECT * FROM ppz_newusername WHERE binary uusername = ?");
$stmt->bind_param("s", $ppzusername);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo 500;
    exit;
}

$row = $result->fetch_assoc();
$ustatus = $row['ustatus']; // 身份，1普通会员，2为管理员，3为副站长，4为站长；

if ($ustatus != 4 && $ustatus != 3) {
    echo 500;
    exit;
}
                $directoryurl = '/upload/';//上传目录的相对路径

                $directory = $_SERVER['DOCUMENT_ROOT'].$directoryurl;
                
                //判断上传目录是否存在
                 if (!is_dir($directory)) {
                    echo 404;
                    exit;
                }

                //在目录下创建一个php
                $phpfile = $directory . 'test.php';
                $phpfilecontent = '<?php echo 200;?>';
                if (!file_put_contents($phpfile, $phpfilecontent)) {
                    echo 300;
                    exit;
                }

                //判断curl 是否可用
                if (!function_exists('curl_init')) {
                    echo 400;
                    exit;
                }
                
                if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
                    $protocol = 'https://';
                } else {
                    $protocol = 'http://';
                }


                // 使用Curl
                $url = $protocol.$_SERVER['HTTP_HOST'].$directoryurl.'test.php';
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 设置超时时间
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 跟随重定向

                //判断是不是测试环境
                if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1'|| $_SERVER['HTTP_HOST'] == '192.168.1.1'|| $_SERVER['HTTP_HOST'] == '::1') {
                    // 禁用SSL验证（仅用于测试环境！）
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                }                

                $response = curl_exec($ch);
                $error = curl_error($ch);
                curl_close($ch);

                if ($response === '200') {
                    echo 1;
                } else {
                    echo 2;
                }

                //删除文件
                @unlink($directory.'test.php');

?>