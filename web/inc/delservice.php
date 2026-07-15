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
        if ($vip==4){
            if(!isset($_POST['id'])){
                $_POST['id']="";
            }
            $id = trim($_POST['id']);
            $id = intval($id);//转换为整数
            if(!empty($id)&&is_numeric($id)&&$id>0&&is_int($id)){//判断id是否为数字

                $sqlif="select * from ppz_workfl where binary id = $id";
                $retvalif=mysqli_query($conn,$sqlif);
                if(mysqli_num_rows($retvalif) == 1){
                    $delsql = "delete from ppz_workfl where binary id = $id";
                    if(mysqli_query($conn,$delsql)){
                        echo 200;
                    }else{
                        echo 600;
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
    }
    mysqli_close($conn);//关闭数据库
}
?>