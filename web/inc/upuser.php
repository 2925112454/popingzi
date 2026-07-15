<?php
    session_start(); // 开始 Session 会话
    include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION
    if (empty($ppzusername)){//判断是否登录
        echo 500; 
    }else{
        include __DIR__.'/conn.php';//连接数据库
        $sql = "select * from ppz_newusername where binary uusername = $ppzusername";//获取登录会员信息
        $retval=mysqli_query($conn,$sql);
        if(mysqli_num_rows($retval) !== 1){ 
            echo 500;
        }else{
            $query = $conn->query($sql);
            while($row = $query->fetch_array()){
                $vip=$row['ustatus'];//身份，1普通会员，2为管理员，3为副站长，4为站长
                $id=$row['uid'];
            }
            if($vip==4||$vip==3||$vip==2){
                $allSuccess = true;
                if(!isset($_POST['value'])){
                    $_POST['value']="";
                }
                if(!isset($_POST['user'])){
                    $_POST['user']="";
                }
                $value=trim($_POST['value']);//内容
                $user=trim($_POST['user']);//收件人
                if (empty($value)){
                    echo 400;
                }else{

                    if(empty($user)){
                        $userup=explode(",","0");//默认为0
                    }else{
                        //将user中的中文逗号替换为英文逗号，且转换为数组
                        $usera=str_replace("，",",",$user);
                        $userarr=explode(",",$usera);
                        //去除数组中的重复项和空值
                        $userarryes=array_unique($userarr);
                            for($i=0;$i<count($userarryes);$i++){
                                //判断每个值是否都是正整数
                                if(!is_numeric($userarryes[$i])){
                                   echo 404;
                                   exit();
                                }
                                //判断每个值是否在数据库中存在
                                $ysql = "select * from ppz_newusername where binary uusername = $userarryes[$i]";
                                $yretval=mysqli_query($conn,$ysql);
                                if(mysqli_num_rows($yretval) !== 1){ 
                                    echo 404;
                                    exit();
                                }
                            }
                            $userup=$userarryes;
                    }
                    $ip=$_SERVER['REMOTE_ADDR'];//获取ip
                    for ($i = 0; $i < count($userup); $i++) {

                        if ($userup[$i]==0){
                            $userid=0;
                        }else{
                            //获取相应的收件人id
                            $ysqlx = "select * from ppz_newusername where binary uusername = $userup[$i]";
                            $yretvalx=mysqli_query($conn,$ysqlx);
                            if(mysqli_num_rows($yretvalx) !== 1){ 
                                echo 404;
                                exit();
                            }
                            while($row = $yretvalx->fetch_array()){
                                $userid=$row['uid'];
                            }
                        }
                        
                        $stmt = $conn->prepare("INSERT INTO ppz_letter (teradmin, teruser, tertext, terip) VALUES (?, ?, ?, ?)");
                        $stmt->bind_param("issi", $id, $userid, $value, $ip);
                        if (!$stmt->execute()) {
                            $allSuccess = false;
                        }
                        $stmt->close();
                    }

                }
                if ($allSuccess) {
                    echo 200;
                } else {
                    echo 600;
                }
            }else{
                echo 500;
            }

        }
    }
?>