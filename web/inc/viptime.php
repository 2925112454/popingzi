<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION
if(!isset($_SESSION["codeerr"])){
    $_SESSION["codeerr"] = 0;
}
$codeerr=$_SESSION["codeerr"];
if (empty($ppzusername)||$codeerr>10){
    echo 500; 
}else{

    if (!isset($_POST["usernamevip"])){
        $_POST["usernamevip"]="";
    }
    $usernamevip = trim($_POST["usernamevip"]);//用户提交的充值卡号

    if (empty($usernamevip)){
        echo 1;
    }else{

        include __DIR__.'/conn.php';//连接数据库
        $vtsql = "select * from ppz_vtime where BINARY vvar = '$usernamevip'";//查询数据库，判断卡号是否存在
        $vtretval=mysqli_query($conn,$vtsql);

        $sql = "select * from ppz_newusername where binary uusername = $ppzusername";//查询数据库，判断用户名是否存在
        $retval=mysqli_query($conn,$sql);

        if(mysqli_num_rows($retval) !== 1){	//如果用户名不存在，返回错误代码
            echo 500;
            $_SESSION["ppzusername"] == "";
            $_SESSION["codeerr"] = $codeerr+1;
        }else{
            $query = $conn->query($sql);
            while($row = $query->fetch_array()){
            $uviptime=$row['uviptime'];//获取会员当前会员时间
            $ustatus=$row['ustatus'];//获取会员身份；1普通会员，2为管理员，3为副站长，4为站长
            $ugold=$row['ugold'];//获取会员积分
            $uid=$row['uid'];//会员ID
            }

    if (empty($uviptime)){
        $new2time= time();//获取当前时间
    }else{
        $currentTimea = time();
        // 判断会员时间是否为空或已过期
        if (empty($uviptime) || strtotime($uviptime) < $currentTimea) {
            // 会员时间为空或已过期，以当前时间为基础
            $new2time = $currentTimea;
        } else {
            // 会员时间有效，以现有会员时间为基础
            $new2time = strtotime($uviptime);
        }
    };

      if($ustatus!=='4' && $ustatus!=='3' && $ustatus!=='2'){

            if(mysqli_num_rows($vtretval) !== 1){	//如果卡号不存在，返回错误代码
                echo 2;
                //设置session
                $_SESSION["codeerr"] = $codeerr+1;
            }else{
                
                $vtquery = $conn->query($vtsql);
                while($vtrow = $vtquery->fetch_array()){
                $vbin=$vtrow['vbin'];//充值卡类型，1月度会员，2季度会员，3年度会员，4百年会员，5积分充值
                $vgold=$vtrow['vgold'];//积分充值数量，1为10,2为20，3为30,4为40，5为50,6为100,7为1000；充值卡类型为5时生效
                $vid=$vtrow['vid'];//充值卡id
                }
    
                
                 if($vbin==5){
                    //会员积分充值
                    if ($vgold==1){
                        $newgold=$ugold+10;
                        $wglod="+10";
                    }else if ($vgold==2){
                        $newgold=$ugold+20;
                        $wglod="+20";
                    }else if ($vgold==3){
                        $newgold=$ugold+30;
                        $wglod="+30";
                    }else if ($vgold==4){
                        $newgold=$ugold+40;
                        $wglod="+40";
                    }else if ($vgold==5){
                        $newgold=$ugold+50;
                        $wglod="+50";
                    }else if ($vgold==6){
                        $newgold=$ugold+100;
                        $wglod="+100";
                    }else if ($vgold==7){
                        $newgold=$ugold+1000;
                        $wglod="+1000";
                    }else{
                        $newgold=$ugold;
                        $wglod="+0";
                    }

                    $newusql = "UPDATE ppz_newusername SET ugold = $newgold WHERE uid = $uid";//更新用户积分

                    if ($conn->query($newusql) === TRUE) {  

                        $newusql2 = "DELETE FROM ppz_vtime WHERE vid = $vid";//删除充值卡号
                        if ($conn->query($newusql2) === TRUE) {
                            $nowtime = date("Y-m-d H:i:s", time());
                            //生成订单编号
                            $ordernumber = date("YmdHis").rand(100000,999999)."-".$uid;
                            //获取用户ip
                            $userip = $_SERVER['REMOTE_ADDR'];
                            //记录交易行为
                            $newlog_sql = "INSERT INTO ppz_log (logadmin,logtime,logtype,logrmb,logab,logmun,logrowid,logip) VALUES ('$uid','$nowtime', '积分充值', '$wglod', '$newgold','$ordernumber','0','$userip')";
                            if ($conn->query($newlog_sql) === TRUE) { 
                                echo 800;
                            }else{
                                echo $newlog_sql . "<br>" . $conn->error;
                            }

                        }else{echo 404;}

                    } else {  
                        echo 404; 
                    }

                    //删除session
                    unset($_SESSION['codeerr']);


                 }else{

                    //会员时间充值
                if($vbin==1){
                    $vtxt =  200;
                    $viptime = strtotime('+30 days', $new2time); // 在当前时间戳上增加一个月
                    $nextMonthDate = date('Y-m-d H:i:s',$viptime);
                }else if($vbin==2){
                    $vtxt =  300;
                    $viptime = strtotime('+90 days', $new2time); // 在当前时间戳上增加三个月
                    $nextMonthDate = date('Y-m-d H:i:s',$viptime);
                }else if ($vbin==3){
                    $vtxt =  400;
                    $viptime = strtotime('+360 days', $new2time); // 在当前时间戳上增加十二个月
                    $nextMonthDate = date('Y-m-d H:i:s',$viptime);
                }else if ($vbin==4){
                    $vtxt =  600;
                    $viptime = strtotime('+36000 days', $new2time); // 在当前时间戳上增加100年
                    $nextMonthDate = date('Y-m-d H:i:s',$viptime);
                }else{
                    $vtxt = 500;
                    $viptime = null;
                    $nextMonthDate = null;
                }

                $ttime=$nextMonthDate;

                $newusql = "UPDATE ppz_newusername SET uviptime = '$ttime' WHERE uid = $uid";//更新用户会员时间

                if ($conn->query($newusql) === TRUE) {  
                    
                    $newusql2 = "DELETE FROM ppz_vtime WHERE vid = $vid";//删除充值卡号
                    if ($conn->query($newusql2) === TRUE) {echo $vtxt;}else{echo 500;}

                } else {  
                           echo 500;
                }
                //删除session
                unset($_SESSION['codeerr']);
                 }
    
    
            };

        }else{echo 700;}

        }

        mysqli_close($conn); 
    }
};
?>