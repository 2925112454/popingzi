<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
if (empty($ppzusername)){//判断是否登录
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
                $rowsql2 = "select * from ppz_letter where binary teruser = $uid || teradmin = $uid";//获取私信
                $rowretval2=mysqli_query($conn,$rowsql2);
                if(mysqli_num_rows($rowretval2) < 1){ 
                    echo 500;
                }else{
                    $ids = [];//初始化数组
                    $query2 = $conn->query($rowsql2);
                    while($row2 = $query2->fetch_array()){
                        $ids[]=$row2['terid'];//获取所有私信id
                    }
                    //删除所以私信
                    $drowsql = "delete from ppz_letter where binary terid in (" . implode(',', $ids) . ")";
                    if ($conn->query($drowsql) === TRUE) {
                        echo 200;
                    }else{
                        echo 500;
                    }


                }


            }
            mysqli_close($conn);//关闭数据库连接
}
?>