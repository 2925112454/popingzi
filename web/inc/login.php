<?php  
// 设置session过期时间 3小时 
session_set_cookie_params(10800);  
session_start(); // 开始 Session 会话

include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量

if(isset($_POST["username"])) {
    $uname = trim($_POST["username"]);//用户提交的账户
    $uname = preg_replace('/\s+/u', '', $uname);
}else{
    $uname = "";
}

if(isset($_POST["password"])) {
    $upass = $_POST["password"];//用户提交的密码
}else{
    $upass = "";
}

if(!isset($_SESSION["ucodeerr"])){
    $_SESSION["ucodeerr"] = 0;
}
$ucodeerr=$_SESSION["ucodeerr"];//登录错误次数

unset($_SESSION['steps']);
unset($_SESSION['stepsname']);
unset($_SESSION['stepstime']);

if (empty($ppzusername)&&$ucodeerr<=10){

    include __DIR__.'/conn.php';//连接数据库

    if (empty($upass) || !preg_match('/^\d{6,11}$/', $uname) || empty($uname)){ //如果用户名为空，或者密码为空，或者用户名长度大于11位，或者用户名不是数字，返回错误代码
        echo 2;
        exit(); //停止执行代码
    }
    //账号不得以0开头
    if ($uname[0] == '0'){
        echo 2;
        exit();
    }

    $websql = "select * from ppz_web where webid = 1";//获取网站信息
    $webretval=mysqli_query($conn,$websql);
    if(mysqli_num_rows($webretval) !== 1){
    }else{
        $webquery = $conn->query($websql);
        while($web = $webquery->fetch_array()){
            $webip=$web['webip'];//拉黑ip名单
        };
    };

    $sql = "select * from ppz_newusername where binary uusername = $uname";//查询数据库，判断用户名是否存在
    $retval=mysqli_query($conn,$sql);

    if(mysqli_num_rows($retval) !== 1){	//如果用户名不存在，返回错误代码
        echo 3;
        $_SESSION["ppzusername"] = "";
        $_SESSION["ucodeerr"]=$ucodeerr+1;
    }else{
        $query = $conn->query($sql);
        while($row = $query->fetch_array()){
            $hspass=$row['upass'];
            $uban=$row['uban'];
            $uip=$row['uip'];
        }

        //删除临时验证码
        $delsql = "update ppz_newusername set uformemail=null,uformtel=null where uusername='$uname'";
        $delretval=mysqli_query($conn,$delsql);
        if(!$delretval){
            echo 500;
            exit(); //停止执行代码
        }

        if (!empty($webip)){
            $nowip = $_SERVER['REMOTE_ADDR'];//获取用户客户端ip
            $webiparr= explode("|",$webip);//转换ip黑名单为|分割的数组
            $webiparr = array_filter($webiparr);//去除数组中的空值
            $webiparr = array_unique($webiparr);//去除数组中的重复值
            $webiparr = array_values($webiparr);//重新排序数组
            if (in_array($nowip,$webiparr)||in_array($uip,$webiparr)){
                echo 9;
                exit(); //停止执行代码
            }
        }

        if ($uban==1){
            if (password_verify($upass,$hspass)) {  
                $ulogintimetime= time();//获取当前时间
                $nextMonthDate = date('Y-m-d H:i:s',$ulogintimetime);//时间戳转换为时间

                $newusql = "UPDATE ppz_newusername SET ulogintime = '$nextMonthDate'  WHERE uusername = $uname";//更新用户登录时间
                if ($conn->query($newusql) === TRUE) {  
                    $_SESSION["ppzusername"]= $uname;//设置登录session
                    $_SESSION["logintime"]= strtotime($nextMonthDate);//设置登录时间session
                    unset($_SESSION['ucodeerr']);
                    echo 200;
                } else {
                    echo 500; 
                }
            } else {  
                $_SESSION["ppzusername"] = "";
                $_SESSION["ucodeerr"]=$ucodeerr+1;
                echo 3;  
            }  
        }else{
            echo 4;  
        }
    }

    mysqli_close($conn); 
}else{
    echo 1;
}
?>