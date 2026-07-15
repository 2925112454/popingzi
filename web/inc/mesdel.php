<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
if (empty($ppzusername)){//判断是否登录
    echo 500; 
}else{
    if (!isset($_POST["mesid"])){
        $_POST["mesid"]="";
    }
    $id=$_POST["mesid"];//私信id
    if (empty($id)||!is_numeric($id)||$id<1){  //判断id是否为空，且是否为数字
        echo 500;
    }else{
        include __DIR__.'/conn.php';//连接数据库
        $rowsql = "select * from ppz_newusername where binary uusername = $ppzusername";//获取登录会员信息
            $rowretval=mysqli_query($conn,$rowsql);
            if(mysqli_num_rows($rowretval) !== 1){ 
                echo 500;
            }else{
                $query = $conn->query($rowsql);
                while($row = $query->fetch_array()){
                $uid=$row['uid'];//会员id
                }
                $rowsql2 = "select * from ppz_letter where binary terid = $id";//获取私信信息
                $rowretval2=mysqli_query($conn,$rowsql2);
                if(mysqli_num_rows($rowretval2) !== 1){ 
                    echo 404;
                }else{

                    $query2 = $conn->query($rowsql2);
                    while($row2 = $query2->fetch_array()){
                    $uid2=$row2['teruser'];//收件人id
                    $uid3=$row2['teradmin'];//发件人id
                    }
                    if ($uid2==$uid || $uid3==$uid){
 
                        $drowsql = "delete from ppz_letter where binary terid=$id";//删除私信
                        if ($conn->query($drowsql) === TRUE) {
                            echo 200;
                        }else{
                            echo 500;
                        }

                    }else{
                        echo 500;
                    }

                }


            }
            mysqli_close($conn);//关闭数据库连接
    }

}
?>