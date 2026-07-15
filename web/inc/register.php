<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION
if (empty($ppzusername)){//判断是否登录
    include __DIR__.'/conn.php';//连接数据库
//获取注册配置
$regif_sql = "SELECT * FROM ppz_regif WHERE id=1";
$regif_result = mysqli_query($conn,$regif_sql);
$regifsize = mysqli_num_rows($regif_result);
if($regifsize==1){
  while($regif_row = mysqli_fetch_assoc($regif_result)){
    $regif=$regif_row['regif'];//注册状态：1开启，2关闭
    $regoff=$regif_row['regoff'];//注册方式：1开放注册，2邀请码注册
    $regtext=$regif_row['regtext'];//注册协议
  }
}


if ($regif==1){

$nowtime = time();

if (!isset($_COOKIE['timetoken'])) {
    $_COOKIE['timetoken']="";
}

if (!isset($_POST["newname"])) {
    $_POST["newname"]="";
}
if (!isset($_POST["newusername"])) {
    $_POST["newusername"]="";
}
if (!isset($_POST["newpassword"])) {
    $_POST["newpassword"]="";
}
if (!isset($_POST["newemail"])) {
    $_POST["newemail"]="";
}
if (!isset($_POST["newyzm"])) {
    $_POST["newyzm"]="";
}
if (!isset($_SESSION["captchanewyzm"])) {
    $_SESSION["captchanewyzm"]="";
}
if (!isset($_POST["newyqm"])) {
    $_POST["newyqm"]="";
}

if( $_COOKIE['timetoken'] == "" || abs($nowtime - $_COOKIE['timetoken'] ) / (60 * 60) == 12){

if(empty($_POST["newname"])|| empty($_POST["newusername"])|| empty($_POST["newpassword"]) || empty($_POST["newemail"])||empty( $_POST["newyzm"])){
    echo 4;
}else{
//校验验证码
if (empty($_SESSION["captchanewyzm"])) {
    echo 1;
    exit();
};
// 获取当前 PHP 版本号
$phpVersion = phpversion();

//赋值用户提交内容给对应变量
$newname = trim($_POST["newname"]);//昵称
$newnamehtml=htmlentities($newname);//防止XSS
$newusername = trim($_POST["newusername"]);//账号
$newusername = preg_replace('/\s+/u', '', $newusername);
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
        $hashedPassword = password_hash($_POST["newpassword"], PASSWORD_BCRYPT, array("salt" => $salt));//密码加密
    } else {
        $hashedPassword = password_hash($_POST["newpassword"], PASSWORD_BCRYPT);
    }
$newyqm = trim($_POST["newyqm"]);//邀请码
$newemail = trim($_POST["newemail"]);//邮箱

if ($regoff==2){
    if (is_null($newyqm)){
        echo 505;
        exit();
    }
// 比对邀请码是否存在  
$stmtcode = $conn->prepare("SELECT * FROM ppz_code WHERE binary invitecode = ?");  
// 绑定参数
$stmtcode->bind_param("s", $newyqm); // "s" 表示字符串类型的参数  
// 执行预处理语句  
$stmtcode->execute();
// 获取结果  
$resultcode = $stmtcode->get_result();
if ($resultcode->num_rows !== 1) {
    echo 506;
    exit();
}
// 关闭预处理语句  
$stmtcode->close();
}

if (mb_strlen($newname,'UTF-8') >12){
    echo 5;
    exit();
}
if (!preg_match('/^\d{6,11}$/', $newusername)||empty($newusername)) {
    echo 6;
    exit();
}
//账号不得以0开头
if ($newusername[0] == '0'){
    echo 60;
    exit();
}
if ($newusername == $newname){
    echo 11;
    exit();
}
if (is_numeric($_POST["newpassword"])){
    echo 8;
    exit();
}
if (mb_strlen($_POST["newpassword"], 'UTF-8') < 6){
    echo 9;
    exit();
}
if (!filter_var($newemail, FILTER_VALIDATE_EMAIL)){
    echo 10;
    exit();
}

if (strlen($_POST["newyzm"]) < 4){
    echo 3;
    exit();
}
    if (!empty($_POST["newyzm"])) { 

            $user_input = trim($_POST["newyzm"]);

        if (strcasecmp($user_input, $_SESSION["captchanewyzm"]) !== 0) {
        $_POST=array();
        echo 3;
         $_SESSION["captchanewyzm"] = "";//验证码不正确，清空验证码的session
         unset($_SESSION["captchanewyzm"]);//释放验证码的session
        }else{

            $ip = $_SERVER['REMOTE_ADDR'];//获取用户ip

            $sqlip = "select * from ppz_newusername where uip = '$ip'"; 
            $retvalip=mysqli_query($conn,$sqlip);
            if(mysqli_num_rows($retvalip) < 1){
                $sql = "select * from ppz_newusername where binary uusername = $newusername"; 
                $retval=mysqli_query($conn,$sql);
    
                if(mysqli_num_rows($retval) < 1){
    
                    $sqlemil = "select * from ppz_newusername where uemail = '$newemail'"; 
                    $retvalemil=mysqli_query($conn,$sqlemil);
                    if(mysqli_num_rows($retvalemil) < 1){
                        $strsql = "insert into ppz_newusername(uname,uusername,upass,uemail,uip) values('$newnamehtml','$newusername','$hashedPassword','$newemail','$ip')";
                        $result =mysqli_query($conn,$strsql);
                        if($result){ //注册成功
                            
                                                if ($regoff==2){
                                                                $deletestrsql = "delete from ppz_code where binary invitecode = '$newyqm'";
                                                                $deleteresult =mysqli_query($conn,$deletestrsql);
                                                                if($deleteresult){ //删除成功
                                                                    echo 200;
                                                                    $timetoken = time();
                                                                    $expiration = time() + 3600*12; // 令牌有效期为12小时
                                                                    setcookie('timetoken', $timetoken, $expiration);//设置cookie
                                                                    $_SESSION["captchanewyzm"] = "";//注册成功，清空验证码session
                                                                    unset($_SESSION["captchanewyzm"]);//释放验证码的session
                                                                }else{
                                                                    echo 14;
                                                                }
                                                }else{
                                                                    echo 200;
                                                                    $timetoken = time();
                                                                    $expiration = time() + 3600*12; // 令牌有效期为12小时
                                                                    setcookie('timetoken', $timetoken, $expiration);//设置cookie
                                                                    $_SESSION["captchanewyzm"] = "";//注册成功，清空验证码session
                                                                    unset($_SESSION["captchanewyzm"]);//释放验证码的session
                                                }
    
    
                            }else{ //注册失败
                            echo 14; 
                            }
                    }else{
                        $emilrz=0;
                        //获取已存在邮箱，判断是否已被人认证
                        while($row = mysqli_fetch_array($retvalemil)){
                            $uemailyes = $row['uemailyes'];
                            if ($uemailyes==2){
                                $emilrz=1;
                            }
                        }
               
                        if ($emilrz==1){
                            echo 16;
                        }else{
                            if (!preg_match('/^\d{6,11}$/', $newusername)) {
                                echo 6; // 既验证长度又验证纯数字
                                exit();
                            }

                            $strsql = "insert into ppz_newusername(uname,uusername,upass,uemail,uip) values('$newnamehtml','$newusername','$hashedPassword','$newemail','$ip')";
                            $result =mysqli_query($conn,$strsql);
                        if($result){ //注册成功
                            
                                                if ($regoff==2){
                                                                $deletestrsql = "delete from ppz_code where binary invitecode = '$newyqm'";
                                                                $deleteresult =mysqli_query($conn,$deletestrsql);
                                                                if($deleteresult){ //删除成功
                                                                    echo 200;
                                                                    $timetoken = time();
                                                                    $expiration = time() + 3600*12; // 令牌有效期为12小时
                                                                    setcookie('timetoken', $timetoken, $expiration);//设置cookie
                                                                    $_SESSION["captchanewyzm"] = "";//注册成功，清空验证码session
                                                                    unset($_SESSION["captchanewyzm"]);//释放验证码的session
                                                                }else{
                                                                    echo 14;
                                                                }
                                                }else{
                                                                    echo 200;
                                                                    $timetoken = time();
                                                                    $expiration = time() + 3600*12; // 令牌有效期为12小时
                                                                    setcookie('timetoken', $timetoken, $expiration);//设置cookie
                                                                    $_SESSION["captchanewyzm"] = "";//注册成功，清空验证码session
                                                                    unset($_SESSION["captchanewyzm"]);//释放验证码的session
                                                }
    
    
                            }else{ //注册失败
                            echo 14; 
                            }
                        }
    
                    }
    
                }else{
    
                    echo 13;
    
                }
            }else{
                echo 17;
            }

           

            mysqli_close($conn); //关闭数据库连接


        }


    }else{
       echo 2;
    }


};

}else{
    echo 12;
}

}else{
    echo 15;
}

}else{
    echo 15;
}
?>