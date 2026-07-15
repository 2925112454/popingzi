<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
if (empty($ppzusername)){
    if(!isset($_POST['ue'])){
        $_POST['ue']="";
    }
    $loginx=$_POST['ue'];//获取账号
    if (empty($loginx)){ 
        echo 400;
    }else{

        if (strlen($loginx) < 6 || strlen($loginx) > 11 || !ctype_digit($loginx) || $loginx <= 0){
            echo 403;
        }else{
            include __DIR__.'/conn.php';//连接数据库
            $sql = "select * from ppz_newusername where binary uusername = $loginx";//查询数据库，判断用户名是否存在
            $retval=mysqli_query($conn,$sql);
            if(mysqli_num_rows($retval) !== 1){	//如果用户不存在
            echo 404;
            }else{
                $query = $conn->query($sql);
                while($row = $query->fetch_array()){
                $utelyes=$row['utelyes'];//手机验证状态，1未验证，2已验证
                $uemailyes=$row['uemailyes'];//邮箱验证状态，1未验证，2已验证
                $uformemail=$row['uformemail'];//临时储存邮箱验证码
                $uformtel=$row['uformtel'];//临时储存手机验证码
                $uban=$row['uban'];//封禁状态，1为正常，反之封禁
                $ustatus=$row['ustatus'];//身份，1普通会员，2为管理员，3为副站长，4为站长；该参数用于限制管理员等特殊身份的线上找回密码
                }
                if ($uban == 1){

                    if ($ustatus==2||$ustatus==3||$ustatus==4){
                        echo 402;
                    }else{

                        if (($utelyes==1&&$uemailyes==1)||(empty($utelyes)&&empty($uemailyes))){
                        echo 505;
                        }else{
                            if (!empty($uformemail)||!empty($uformtel)){ //如果存在旧验证码没有清除
                                if (!empty($uformemail)){
                                    //删除临时储存的邮箱验证码
                                    $sqldel = "UPDATE ppz_newusername SET uformemail = NULL WHERE uusername = $loginx";
                                    $retval=mysqli_query($conn,$sqldel);
                                    if (@$retval){}else{}
                                }

                                if (!empty($uformtel)){
                                    //删除临时储存的手机验证码
                                    $sqldeltel = "UPDATE ppz_newusername SET uformtel = NULL WHERE uusername = $loginx";
                                    $retvaltel=mysqli_query($conn,$sqldeltel);
                                    if (@$retvaltel){}else{}
                                }
                            }
                            $_SESSION["steps"] = 2;//步骤
                            $_SESSION["stepsname"] = $loginx;//账号
                            $_SESSION["stepstime"] = time();//当前时间
                            echo 200;
                        }

                    }


                }else{
                    echo 401;
                }


            }
            //关闭数据库链接
            mysqli_close($conn);
        }
    }
}else{
    echo 500; 
}
?>