<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
if (empty($ppzusername)){//判断是否登录
    echo 500; 
}else{
    if (!isset($_POST["fsid"])) {
        $_POST["fsid"]="";
    }
    $id=trim($_POST["fsid"]);//获取id
    if (empty($id) || !is_numeric($id)||$id<1){//判断id是否为空且是否是整数数字
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
            $uid=$row['uid'];//关注者id
            }

            if ($id==$uid){ 
                echo 400;//如果是自己关注自己的情况
            }else{
                
                $rowsql2 = "select * from ppz_newusername where binary uid = $id";//获取被关注者信息
                $rowretval2=mysqli_query($conn,$rowsql2);
                if(mysqli_num_rows($rowretval2) !== 1){ 
                    echo 404;//被关注者若不存在
                }else{

                    $query2 = $conn->query($rowsql2);
                    while($row2 = $query2->fetch_array()){
                    $ubanx=$row2['uban'];//被关注者状态，1为正常，其余为封禁
                    }

                if ($ubanx==1){

                    $rowsql3 = "select * from ppz_folus where binary usvip = $uid && binary usuename = $id";//判断是否关注
                    $rowretval3=mysqli_query($conn,$rowsql3);
                    if(mysqli_num_rows($rowretval3) !== 1){ 
                        $rowsql4 = "INSERT INTO ppz_folus (usuename,usvip) VALUES ($id,$uid)";//插入关注
                        if ($conn->query($rowsql4) === TRUE) {  
                            echo 200;  
                        } else {  
                            echo 500;  
                        }
                    }else{
                       //如果已关注，则取消关注
                       $delsql = "delete from ppz_folus where binary usvip = $uid && binary usuename = $id";
                       if ($conn->query($delsql) === TRUE) {  
                            echo 202;  
                        } else {  
                            echo 500;  
                        }
                    }
                }else{
                    echo 9527;
                }

            }

            }



        }

    }


}
?>