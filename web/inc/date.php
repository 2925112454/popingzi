<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
if (empty($ppzusername)){
    echo '{"err":500,"txt":""}'; 
}else{
    include __DIR__.'/conn.php';//连接数据库
    $sql = "select * from ppz_newusername where binary uusername = $ppzusername";//查询数据库，判断用户名是否存在
    $retval=mysqli_query($conn,$sql);
    if(mysqli_num_rows($retval) !== 1){	//如果用户名不存在，返回错误代码
        echo '{"err":500,"txt":""}'; 
        $_SESSION["ppzusername"] == "";
    }else{

        $query = $conn->query($sql);
        while($row = $query->fetch_array()){
        $udate=$row['udate'];//获取用户最近签到时间
        $ugold=$row['ugold'];//获取用户当前积分
        $uid=$row['uid'];//获取用户ID
        $udateday=$row['udateday'];//获取用户连续签到天数
        }

        //获取网站配置信息
        $web_sql = "select webjifen from ppz_web where webid=1";
        $web_retval=mysqli_query($conn,$web_sql);
        if(mysqli_num_rows($web_retval) !== 1){ 
            echo '{"err":500,"txt":""}'; 
        }else{

            while($row = $web_retval->fetch_array()){
                $webjifen=$row['webjifen'];//获取签到积分奖励范围,纯数或者两个数字中间用-隔开的范围
            }

            if (empty($webjifen)){
                $web_jifen=0;
            }else{
                $web_jifen=$webjifen;
            }

            //判断是纯数字还是两个数字中间用-隔开的范围
            if (preg_match("/^[0-9]+-[0-9]+$/",$web_jifen)){
                $web_jifen_arr = explode("-", $web_jifen);//将字符串转换为数组
                $web_jifen_min = $web_jifen_arr[0];//获取数组中的最小值
                $web_jifen_max = $web_jifen_arr[1];//获取数组中的最大值
                $randomNumber = rand($web_jifen_min, $web_jifen_max);
            }else{
                $randomNumber = $web_jifen;
            }

                $time=time();//获取当前时间:年月日
                
                $newgold=$ugold+$randomNumber;//给用户增加积分
                $nextMonthDate = date('Y-m-d H:i:s',time());//获取当前时间:年月日时分秒

                if (empty($udate)){
                    $newudate=strtotime('2020-01-01 00:00:00');
                }else{
                    $newudate=strtotime($udate);
                }

                $days = date('Ymd', time()) - date('Ymd', $newudate); // 将时间差转换为天数：年月日

            if ($days>0){ 
                if (empty($udateday)){
                    $uday=0;
                }else{
                    $uday=$udateday;
                };

                if ($days == 1){
                    $day=$uday+1;
                }else{
                    $day=1;
                };
                //更新用户签到时间及积分
                $newusql = "UPDATE ppz_newusername SET udate = '$nextMonthDate',ugold = '$newgold' WHERE uid = $uid";
                if ($conn->query($newusql) === TRUE) {      
                    $dayusql = "UPDATE ppz_newusername SET udateday = '$day' WHERE uid = $uid"; //更新用户签到天数
                    if ($conn->query($dayusql) === TRUE) {
                            //记录积分
                            $nowtime = date("Y-m-d H:i:s", time());
                            $wglod="+".$randomNumber;
                            //生成订单编号
                            $ordernumber = date("YmdHis").rand(100000,999999)."-".$uid;
                            //获取用户ip
                            $userip = $_SERVER['REMOTE_ADDR'];
                            if($randomNumber<=0){
                                echo '{"err":400,"txt":"0"}';
                            }else{
                                //记录交易行为
                                $newlog_sql = "INSERT INTO ppz_log (logadmin,logtime,logtype,logrmb,logab,logmun,logrowid,logip) VALUES ('$uid','$nowtime', '每日签到', '$wglod', '$newgold','$ordernumber','0','$userip')";
                                if ($conn->query($newlog_sql) === TRUE) { 
                                    echo '{"err":400,"txt":'.$randomNumber.'}';
                                } else {
                                    echo '{"err":500,"txt":""}';
                                };
                            }                            
                    }else{
                        echo '{"err":500,"txt":""}'; 
                    };
                    
                } else {  
                    echo '{"err":500,"txt":""}'; 
                };

            }else{
                echo '{"err":404,"txt":""}'; 
            }
        }

    }
//关闭数据库
mysqli_close($conn);
}
?>