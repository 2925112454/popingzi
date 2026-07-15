<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
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
        }
        if($vip==4){//判断是否为站长
            if(!isset($_POST['newip'])){
                $_POST['newip']="";
            }
            $newipx=trim($_POST['newip']);//IP地址
            $newip=preg_replace('/\s+/', '', $newipx);//去除空格
            if(!empty($newip)){                                
                $newiparr=explode("|",$newip);//拆分IP地址,按|拆分为数组
                function validate_ip($ip) {  
                    // IPv4 validation  
                    $ipv4Pattern = '/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/';  
                    if (preg_match($ipv4Pattern, $ip)) {  
                        return true;  
                    }  
                  
                    // IPv6 validation  
                    $ipv6Pattern = '/^(?:[A-Fa-f0-9]{1,4}:){7}[A-Fa-f0-9]{1,4}$/'; // 标准的IPv6地址  
                    if (preg_match($ipv6Pattern, $ip)) {  
                        return true;  
                    }  
                  
                    // 允许IPv6压缩表示法  
                    $compressedIpv6Pattern = '/^(?:(?:[A-Fa-f0-9]{1,4}:){1,7}|:)(?:(?:[A-Fa-f0-9]{1,4}:){1,6}(?::?[A-Fa-f0-9]{1,4})|(?::?[A-Fa-f0-9]{1,4})){1}(?::?)$/';  
                    if (preg_match($compressedIpv6Pattern, $ip)) {  
                        return true;  
                    }  
                  
                    // 如果既不是IPv4也不是IPv6，则返回false  
                    return false;  
                }  
  
                for($i=0;$i<count($newiparr);$i++){
                    // 判断IP地址是否合法，不是IPV4和IPV6则输出403
                    $ip=$newiparr[$i];
                    if(validate_ip($ip)==false){
                        echo 403;
                        exit();
                    }
                }
                    function hasDuplicates($array) {  // 判断数组是否有重复元素
                        return count($array) !== count(array_unique($array));  
                    }
                    
                    $array = $newiparr;  
                    if (hasDuplicates($array)) {  
                        echo 404;
                        exit();
                    }

            }
                $newipsql = "update ppz_web set webip='$newip' where webid=1";
                if(mysqli_query($conn,$newipsql)){
                    echo 200;
                }else{
                    echo 500;
                }

  
        }else{
            echo 500;
        }
    }
}
?>