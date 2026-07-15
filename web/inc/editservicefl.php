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
            if(!isset($_POST['name'])){
                $_POST['name']="";
            }
            $id = trim($_POST['id']);
            $id = intval($id);//转换为整数
            $name = trim($_POST['name']);
            $name = htmlspecialchars($name);//防止xss
            $name = addslashes($name);//防止sql注入
            if(!empty($id)&&is_numeric($id)&&$id>0&&is_int($id)&&!empty($name)){
                $sqlif="select * from ppz_workfl where binary id = $id";
                $retvalif=mysqli_query($conn,$sqlif);
                if(mysqli_num_rows($retvalif) == 1){
                    //判断是否有重复
                    $sqlif2="select * from ppz_workfl where binary wkname = '$name'";
                    $retvalif2=mysqli_query($conn,$sqlif2);
                    if(mysqli_num_rows($retvalif2) == 0){
                                            // 修改数据
                                            $editsql = "update ppz_workfl set wkname = '$name' where binary id = $id";
                                            if(mysqli_query($conn,$editsql)){
                                                echo 200;
                                            }else{
                                                echo 600;
                                            }
                    }else{
                        echo 400;
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
    mysqli_close($conn);//关闭数据库
}
?>