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
        if($vip==4){
            if (!isset($_POST['if'])){
                $_POST['if']="";
            }
            if (!isset($_POST['off'])){
                $_POST['off']="";
            }
            if (!isset($_POST['text'])){
                $_POST['text']="";
            }
            $if=trim($_POST['if']);
            $off=trim($_POST['off']);
            $text=trim($_POST['text']);
            if (($if==1||$if==2)&&($off==1||$off==2)){

                $ifmun=round((float)$if);
                $offmun=round((float)$off);
                if(!is_null($text)&&$text!=""&&$text!=null){
                    $texthtml=htmlentities($text);
                }else{
                    $texthtml="";
                }

                $regifnewsql="update ppz_regif set regif=$ifmun,regoff=$offmun,regtext='$texthtml' where id=1";
                $regifnewresult=mysqli_query($conn,$regifnewsql);
                if($regifnewresult){
                    echo 200;
                }else{
                    echo 600;
                }

            }else{
                echo 500;
            }

        }else{
            echo 500;
        }
    }
}
?>