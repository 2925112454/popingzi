<?php
    // 将响应转换为JSON格式并输出  
header('Content-Type: application/json');
session_start(); // 开始 Session 会话
ob_start();
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION
if (empty($ppzusername)){//判断是否登录
    $response = array('code' => 500, 'msg' => '错误操作！');
    echo json_encode($response);
}else{
        include $_SERVER['DOCUMENT_ROOT'].'/inc/conn.php';//连接数据库
        $rowsql = "select * from ppz_newusername where binary uusername = $ppzusername";//获取登录会员信息
        $rowretval=mysqli_query($conn,$rowsql);
        if(mysqli_num_rows($rowretval) !== 1){ 
            $response = array('code' => 500, 'msg' => '错误操作！');
            echo json_encode($response);
        }else{
                $query = $conn->query($rowsql);
                while($row = $query->fetch_array()){
                    $uid=$row['uid'];
                    $upass = $row['upass'];//密码
                }
                $pass="";
                $newpass="";
                if(isset($_POST['pass'])&&!empty($_POST['pass'])){
                    $pass = $_POST['pass'];
                }
                if(isset($_POST['newpass'])&&!empty($_POST['newpass'])){
                    $newpass = $_POST['newpass'];
                }
                if(!empty($pass)&&!empty($newpass)){
                    if (password_verify($pass,$upass)) {
                        // 获取当前 PHP 版本号
                        $phpVersion = phpversion();
                        if (version_compare($phpVersion, '7.0.0', '<')) {
                            //生成一个唯一的盐值
                            function generateSalt() {  
                                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';  
                                $salt = '';  
                                for ($i = 0; $i < 32; $i++) {  
                                    $salt .= $characters[rand(0, strlen($characters) - 1)];  
                                }  
                                return $salt;  
                            }
                            $salt = generateSalt(); // 生成一个唯一的盐值 
                            $hashedPassword = password_hash($newpass, PASSWORD_BCRYPT, array("salt" => $salt));//密码加密
                        } else {
                            $hashedPassword = password_hash($newpass, PASSWORD_BCRYPT);
                        }

                        $newsql="update ppz_newusername set upass='$hashedPassword' where uid='$uid'";
                        $newresult=mysqli_query($conn,$newsql);
                        if($newresult){
                            $response = array('code' => 200, 'msg' => '');
                            echo json_encode($response);
                            //清除登录状态
                            unset($_SESSION['ppzusername']);
                            $ppzusername="";
                        }else{
                            $response = array('code' => 500, 'msg' => '密码修改失败');
                            echo json_encode($response);
                        }
                        
                    }else{
                        $response = array('code' => 500, 'msg' => '旧密码错误');
                        echo json_encode($response);
                    }
                }else{
                    $response = array('code' => 500, 'msg' => '错误操作');
                    echo json_encode($response);
                }
                
        }
}
?>