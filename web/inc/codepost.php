<?php
session_start(); // 开始 Session 会话
if(!isset($_SESSION['stepsname'])){
    $_SESSION["stepsname"]  = "";
}
if(!isset($_SESSION['stepstime'])){
    $_SESSION["stepstime"]  = "";
}
if(!isset($_SESSION['steps'])){
    $_SESSION["steps"]  = "";
}
if(!isset($_POST["codetxt"])){
    $_POST["codetxt"]  = "";
}
$stepsname=$_SESSION["stepsname"];//账号
$stepstime=$_SESSION["stepstime"];//时间
$steps=$_SESSION["steps"];//所处步骤
$nowtime=time();//当前时间
$sumpassttime=$nowtime-$stepstime;//计算时间差
$code=strtoupper(trim($_POST["codetxt"]));//获取验证码并转为大写
if (empty($code) || strlen($code) !== 6 || !ctype_alnum($code)){//验证码验证
echo 500;
}else{
    if ((!is_null($stepstime) && !empty($stepstime)) && ( $sumpassttime > 300)){//判断是否超时
    unset($_SESSION['steps']);
    unset($_SESSION['stepsname']);
    unset($_SESSION['stepstime']);
    echo 505;
    }else{

        if ($steps==3){
            include __DIR__.'/conn.php';//链接数据库
            $sql = "select * from ppz_newusername where binary uusername = $stepsname";//查询数据库，判断用户名是否存在
            $retval=mysqli_query($conn,$sql);
            if(mysqli_num_rows($retval) !== 1){	//如果用户名不存在，返回错误代码
                echo 505;
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
                $newemailcode=strtoupper($uformemail);//将邮箱验证码转为大写
                $newtelcode=strtoupper($uformtel);//将手机验证码转为大写
                if (($utelyes==2 || $uemailyes==2) && $uban==1 && $ustatus==1 && (!empty($uformemail) || !empty($uformtel))){

                    if ($newemailcode==$code || $newtelcode==$code){
                        //删除临时验证码
                        $delsql = "update ppz_newusername set uformemail=null,uformtel=null where uusername='$stepsname'";
                        $delretval=mysqli_query($conn,$delsql);
                        if($delretval){
                            echo 200;
                            $_SESSION["steps"] = 4;//更新步骤
                        }else{
                            echo 500;
                        }
                    }else{
                        echo 500;
                    }

                }else{
                    echo 505;
                }
            }
//关闭数据库
mysqli_close($conn);
        }else{
            echo 505;
        }


    }
}
?>