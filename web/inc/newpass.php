<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量

if (!isset($_SESSION['stepsname'])){
    $_SESSION["stepsname"]='';
}
if (!isset($_SESSION['stepstime'])){
    $_SESSION["stepstime"]='';
}
if (!isset($_SESSION['steps'])){
    $_SESSION["steps"]='';
}
if (!isset($_POST["pass"])){
    $_POST["pass"]='';
}
if (!isset($_POST["pass2"])){
    $_POST["pass2"]='';
}

$stepsname=trim($_SESSION["stepsname"]);//账号
$stepstime=$_SESSION["stepstime"];//时间
$steps=$_SESSION["steps"];//所处步骤
$nowtime=time();//当前时间
$sumpassttime=$nowtime-$stepstime;//计算时间差

$pass=$_POST["pass"];//获取密码
$pass2=$_POST["pass2"];//获取重复确认密码

if (empty($ppzusername)){//判断是否已登录

    if (!empty($stepstime) && ( $sumpassttime > 300) && !empty($stepsname)){//判断是否超时
        unset($_SESSION['steps']);
        unset($_SESSION['stepsname']);
        unset($_SESSION['stepstime']);
        echo 505;
        }else{

            if (empty($pass) || empty($pass2) || strlen($pass) < 6 || strlen($pass2) < 6){
                echo 301;
            }else{
                if (preg_match('/^\d+$/', $pass)){//判断密码是否为纯数字
                    echo 302;
                }else{

                    if ($pass==$pass2){

                        if ($steps==4){

                            include __DIR__.'/conn.php';//链接数据库
                            $sql = "select * from ppz_newusername where binary uusername = $stepsname";//查询数据库，判断用户名是否存在
                            $retval=mysqli_query($conn,$sql);
                            if(mysqli_num_rows($retval) !== 1){	//如果用户名不存在，返回错误代码
                                echo 500;
                            }else{

                                // 获取当前 PHP 版本号
                                $phpVersion = phpversion();

                                // 检查 PHP 版本是否小于 7.0.0
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
                                    $hashedPassword = password_hash($pass, PASSWORD_BCRYPT, array("salt" => $salt));//密码加密
                                } else {
                                    $hashedPassword = password_hash($pass, PASSWORD_BCRYPT);
                                }

                                //更新验证码
                                $sqlpasscode = "update ppz_newusername set uformtel = null , uformemail = null  where binary uusername = '$stepsname'";
                                $resultpasscode = mysqli_query($conn,$sqlpasscode);
                                if($resultpasscode){
                                    //更新密码
                                    $sqlpass = "update ppz_newusername set upass = '$hashedPassword' where binary uusername = '$stepsname'";
                                    $resultpass = mysqli_query($conn,$sqlpass);
                                    if($resultpass){
                                        unset($_SESSION['steps']);
                                        unset($_SESSION['stepsname']);
                                        unset($_SESSION['stepstime']);
                                        echo 200;
                                    }else{
                                        echo 500;
                                    }
                                }else{
                                    echo 500;
                                }
                   
    
                            }
                            //关闭数据库
                            mysqli_close($conn);

                        }else{
                            echo 505;
                        }


                    }else{
                        echo 300;
                    }

                }
            }

        }
    
}else{
    echo 500;
}
?>