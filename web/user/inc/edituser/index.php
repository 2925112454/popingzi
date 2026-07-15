<?php
    session_start(); // 开始 Session 会话
    include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION
    if (empty($ppzusername)){//判断是否登录
        echo 500; 
    }else{
        include $_SERVER['DOCUMENT_ROOT'].'/inc/conn.php';//链接数据库
        $rowsql = "select * from ppz_newusername where binary uusername = $ppzusername";//获取登录会员信息
        $rowretval=mysqli_query($conn,$rowsql);
        if(mysqli_num_rows($rowretval) !== 1){ 
            echo 500;
        }else{
            $query = $conn->query($rowsql);
            while($row = $query->fetch_array()){
                $ustatus=$row['ustatus'];//身份，1普通会员，2为管理员，3为副站长，4为站长；
                $uidx=$row['uid'];//登录者id
                $fj=$row['uban'];//是否封禁
            }
            if ($ustatus==4||$ustatus==3){
                if(!isset($_POST['haderimg'])){
                    $_POST['haderimg']="";
                }
                if(!isset($_POST['newtime'])){
                    $_POST['newtime']="";
                }
                if(!isset($_POST['viptime'])){
                    $_POST['viptime']="";
                }
                if(!isset($_POST['name'])){
                    $_POST['name']="";
                }
                if(!isset($_POST['user'])){
                    $_POST['user']="";
                }
                if(!isset($_POST['email'])){
                    $_POST['email']="";
                }
                if(!isset($_POST['tel'])){
                    $_POST['tel']="";
                }
                if(!isset($_POST['url'])){
                    $_POST['url']="";
                }
                if(!isset($_POST['gold'])){
                    $_POST['gold']="";
                }
                if(!isset($_POST['userif'])){
                    $_POST['userif']="";
                }
                if(!isset($_POST['sexif'])){
                    $_POST['sexif']="";
                }
                if(!isset($_POST['vipif'])){
                    $_POST['vipif']="";
                }
                if(!isset($_POST['ip'])){
                    $_POST['ip']="";
                }
                if(!isset($_POST['ict'])){
                    $_POST['ict']="";
                }
                if(!isset($_POST['pcl'])){
                    $_POST['pcl']="";
                }
                if(!isset($_POST['cl'])){
                    $_POST['cl']="";
                }
                if(!isset($_POST['telif'])){
                    $_POST['telif']="";
                }
                if(!isset($_POST['emilif'])){
                    $_POST['emilif']="";
                }
                if(!isset($_POST['id'])){
                    $_POST['id']="";
                }
                $haderimg = trim($_POST['haderimg']);//头像
                $newtime = trim($_POST['newtime']);//注册时间
                $viptime =  trim($_POST['viptime']);//会员时间
                $name = trim($_POST['name']);//昵称
                $user = trim($_POST['user']);//账号
                $email = trim($_POST['email']);//邮箱
                $tel = trim($_POST['tel']);//手机
                $url = trim($_POST['url']);//网址
                $gold = trim($_POST['gold']);//积分
                $userif = trim($_POST['userif']);//状态，1正常，2封禁
                $sexif = trim($_POST['sexif']);//性别，1男，2女
                $vipifx = trim($_POST['vipif']);//身份，1普通，2管理员，3副站长
                $ip = trim($_POST['ip']);//IP
                $ict = trim($_POST['ict']);//简介
                $pcl = trim($_POST['pcl']);//购买记录
                $cl = trim($_POST['cl']);//收藏记录
                $telif = trim($_POST['telif']);//手机验证状态
                $emilif = trim($_POST['emilif']);//邮箱验证状态
                $sid= trim($_POST['id']);//会员id

                $vsql = "select * from ppz_newusername where uid='$sid'";
                $vres = $conn->query($vsql);
                if($vres->num_rows>0){
                    while($vrow = $vres->fetch_array()){
                        $vipifus=$vrow['ustatus'];//被修改者的身份
                    }
                }else{
                    $vipifus=9999;
                }
 

                if ($ustatus>=$vipifx && $ustatus>=$vipifus && $vipifx>0){

                if($sid==$uidx){
                    if ($fj==1){
                        $userif=1;
                    }else{
                        $userif=2;
                    }
                }

                if ($ustatus==4){
                    //判断被修改的是不是自己
                    if ($sid==$uidx){
                        $vipif=4;
                    }else{
                        $vipif=$vipifx;
                    }
                }else if ($ustatus==3){
                    //判断被修改的是不是自己
                    if ($sid==$uidx){
                        $vipif=$ustatus;
                    }else{
                        if ($vipifx==4){
                            $vipif=0;
                        }else if($vipifx==3){
                            $vipif=0;
                        }else{
                            $vipif=$vipifx;
                        }
                    }
                }else{
                    $vipif=0;
                }

                //时间为空时
                if(empty($viptime)){
                    $viptime=null;
                }
                if(empty($newtime)){
                    $newtime='2000-12-12 00:00:00';
                }

                if($userif&&$sexif&&$vipif&&$telif&&$emilif&&$gold>=0&&$newtime){
                    //若头像不为空，则判断头像路径后缀是否是图片
                    if(!empty($haderimg)){
                        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif','webp','ico','svg','bmp');
                        $file_extension = pathinfo($haderimg, PATHINFO_EXTENSION);
                        if(!in_array($file_extension, $allowed_extensions)){
                            echo 500;
                            exit;
                        }
                    }
                    //若注册时间不为空，则判断时间是否是合法的格式
                    if(!empty($newtime)){
                        if(!strtotime($newtime)){
                            echo 500;
                            exit;
                        }
                    }
                    //若会员时间不为空，则判断会员时间是否是合法的格式
                    if(!empty($viptime)){
                        if(!strtotime($viptime)){
                            echo 500;
                            exit;
                        }
                    }
                    //判断账号格式是否合法：账号必须是正整数、位数不能低于6位数和超过11位数、不能以0开头、不能包含特殊符号
                    if(!preg_match('/^[1-9]\d{5,10}$/', $user)){
                        echo 500;
                        exit;
                    }                    
                    //若邮箱不为空，则判断邮箱地址是否合法
                    if(!empty($email)){
                        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                            echo 500;
                            exit;
                        }
                    }
                    //若手机号不为空，则判断手机号是否合法：不能超过11位数、不能以0开头、不能包含特殊符号
                    if(!empty($tel)){
                        if(!preg_match('/^[1-9]\d{0,10}$/', $tel)){
                            echo 500;
                            exit;
                        }
                    }
                    //若网址不为空，则判断网址是否是合法格式
                    if(!empty($url)){
                        if(!filter_var($url, FILTER_VALIDATE_URL)){
                            echo 500;
                            exit;
                        }
                    }
                    //判断积分是否是非0开头的正整数
                    if(!preg_match('/^[1-9]\d*$/', $gold) && $gold != 0){
                        echo 500;
                        exit;
                    }
                    //判断参数是否合法
                    if (($userif != 1 && $userif != 2) || ($sexif != 1 && $sexif != 2) || ($vipif != 1 && $vipif != 2 && $vipif != 3 && $vipif != 4) || ($telif != 1 && $telif != 2) || ($emilif != 1 && $emilif != 2)) {
                        echo 500;
                        exit;
                    }
                    //若简介不为空，判断简介的字数是否超过240字(UTF-8编码)
                    if(!empty($ict)){
                        if(mb_strlen($ict,'utf-8')>240){
                            echo 500;
                            exit;
                        }
                    }
                    //若购买记录不为空，判断格式是否是以"|"分割的有效数组，且数组的每个值是否都是非零开头的正整数
                    if(!empty($pcl)){
                        $pcl_arr = explode("|",$pcl);//分割
                        foreach($pcl_arr as $key=>$value){
                            if(!preg_match('/^[1-9]\d*$/', $value)){
                                echo 500;
                                exit;
                            }
                        }
                    }
                    //若收藏记录不为空，判断格式是否是以"|"分割的有效数组，且数组的每个值是否都是非零开头的正整数
                    if(!empty($cl)){
                        $cl_arr = explode("|",$cl);//分割
                        foreach($cl_arr as $key=>$value){
                            if(!preg_match('/^[1-9]\d*$/', $value)){
                                echo 500;
                                exit;
                            }
                        }
                    }
                    //判断会员是否存在
                    $esql = "select * from ppz_newusername where uid='$sid'";
                    $eres = $conn->query($esql);
                    if($eres->num_rows>0){
                        //判断会员账号是否已存在(排除自己)
                        $exsql = "select * from ppz_newusername where uusername='$user' and uid!='$sid'";
                        $exres = $conn->query($exsql);
                        if($exres->num_rows>0){
                            echo 501;
                        }else{
                            //判断会员邮箱是否已存在(排除自己)
                            $exesql = "select * from ppz_newusername where uemail='$email' and uid!='$sid' and uemail IS NOT NULL and uemailyes=2";
                            $exeres = $conn->query($exesql);
                            if($exeres->num_rows>0){
                                echo 502;
                            }else{
                                //判断会员手机号是否已存在(排除自己)
                                $exttsql = "select * from ppz_newusername where utel='$tel' and uid!='$sid' and utelyes=2 and utel IS NOT NULL and utel!='' and utel<>'' ";
                                $exttres = $conn->query($exttsql);
                                if($exttres->num_rows>0){
                                    echo 503;
                                }else{

                                    $logyes=500;

                                    //判断会员积分是否变动
                                    $ueidt_rmb_sql = "select ugold from ppz_newusername where uid='$sid'";
                                    $ueidt_rmb_res = $conn->query($ueidt_rmb_sql);
                                    while($ueidt_rmb_row = $ueidt_rmb_res->fetch_assoc()){
                                        $ueidt_rmb = $ueidt_rmb_row['ugold'];
                                    }

                                    if($ueidt_rmb!=$gold){
                                        //计算差额
                                        $ueidt_rmb_dif = $gold-$ueidt_rmb;
                                        //判断是增加了还是减少了
                                        if($ueidt_rmb_dif>0){
                                            $wglod="+".$ueidt_rmb_dif;
                                        }else{
                                            $wglod=$ueidt_rmb_dif;
                                        }
                                        //记录积分
                                        $nowtime = date("Y-m-d H:i:s", time());
                                        //生成订单编号
                                        $ordernumber = date("YmdHis").rand(100000,999999)."-".$sid;
                                        //获取用户ip
                                        $userip = $_SERVER['REMOTE_ADDR'];
                                        $newlog_sql = "INSERT INTO ppz_log (logadmin,logtime,logtype,logrmb,logab,logmun,logrowid,logip) VALUES ('$sid','$nowtime', '管理修改', '$wglod', '$gold','$ordernumber','0','$userip')";
                                        if ($conn->query($newlog_sql) === TRUE) { 
                                            $logyes=200;
                                        }
                                    }else{
                                        $logyes=200;
                                    }

                                    if($logyes==200){
                                        // 更新信息
                                        $ueidtsql = "
                                        UPDATE ppz_newusername 
                                        SET 
                                            uimg = ?,
                                            utime = ?,
                                            uviptime = ?,
                                            uname = ?,
                                            uusername = ?,
                                            uemail = ?,
                                            utel = ?,
                                            uurl = ?,
                                            ugold = ?,
                                            uban = ?,
                                            usex = ?,
                                            ustatus = ?,
                                            uip = ?,
                                            upersonal = ?,
                                            urowyes = ?,
                                            ucollect = ?,
                                            utelyes = ?,
                                            uemailyes = ?
                                        WHERE uid = ?
                                        ";

                                        // 准备预处理语句
                                        $stmt = $conn->prepare($ueidtsql);
                                        if (!$stmt) {
                                            die("预处理语句准备失败: " . $conn->error);
                                        }

                                        // 绑定参数
                                        $stmt->bind_param(
                                            "ssssisssiiiissssiii",
                                            $haderimg,
                                            $newtime,
                                            $viptime,
                                            $name,
                                            $user,
                                            $email,
                                            $tel,
                                            $url,
                                            $gold,
                                            $userif,
                                            $sexif,
                                            $vipif,
                                            $ip,
                                            $ict,
                                            $pcl,
                                            $cl,
                                            $telif,
                                            $emilif,
                                            $sid
                                        );
                                        // 执行预处理语句
                                        if ($stmt->execute()) {
                                            echo 200;
                                        } else {
                                            echo 600;
                                        }
                                        // 关闭预处理语句和数据库连接
                                        $stmt->close();
                                    }else{
                                        echo 600;
                                    }                                    
                                    
                                }

                            }

                        }
                        
                        }else{
                            echo 404;
                        }

                    }else{
                        echo 500;
                    }
                }else{
                    echo 500;
                }
            }else{
                echo 500;
            }
        }
        $conn->close();
    }
?>