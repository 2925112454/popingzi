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
        }
        if($vip==4){//判断是否为站长
            if  (!isset($_POST['size'])) {
               $_POST['size']="";
            }
            $size=trim($_POST['size']);//获取值
            if(empty($size)||!is_numeric($size)||!ctype_digit($size)||$size<1){//判断是否为数字
                echo 404;
            }else{
                $maxsize=abs(intval($size));
                if($maxsize>999999999){//判断是否大于999999999
                    echo 400;
                }else{
                    include __DIR__.'/conn.php';//链接数据库
                    $rowsql = "select * from ppz_newusername where binary uusername = $ppzusername";//获取登录会员信息
                    $rowretval=mysqli_query($conn,$rowsql);
                    if(mysqli_num_rows($rowretval) !== 1){ 
                        echo 500;
                    }else{
                        $query = $conn->query($rowsql);
                        while($row = $query->fetch_array()){
                            $ustatus=$row['ustatus'];//身份，1普通会员，2为管理员，3为副站长，4为站长；此处为LOGO上传操作，仅限站长身份操作
                            $uid=$row['uid'];
                        }
                        if ($ustatus==4||$ustatus==3){
                            $newsql = "update ppz_web set webmaxsize = $maxsize where webid = 1";
                            if ($conn->query($newsql) === TRUE) {
                                echo 200;
                            } else {
                                echo 600;
                            }
                        }else{
                            echo 500;
                        }

                    }
                    mysqli_close($conn);
                }
            }
        }else{
            echo 700;
        }
        
    }
    

}
?>