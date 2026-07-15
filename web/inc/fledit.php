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
        if($vip==4){
            if(!isset($_POST['id'])){
                $_POST['id']="";
            }
            if(!isset($_POST['name'])){
                $_POST['name']="";
            }
            $id=trim($_POST['id']);
            $name=trim($_POST['name']);
                if(!empty($id) && $id>0 && is_numeric($id)){
                    if (!empty($name)){
                       $ysql="SELECT * FROM ppz_fl WHERE flid=$id";
                       $yresult=mysqli_query($conn,$ysql);
                       if(mysqli_num_rows($yresult)==1){

                        $newname=htmlspecialchars(str_replace(array(" ", "\n", "\r", "'", '"'), '', trim($name)));//防止SQL注入
                        $flquery = $conn->query($ysql);
                        while($flrow = $flquery->fetch_array()){
                            $fllinkid=$flrow['fllinkid'];//所属列表id
                        }
                            $notsql="SELECT * FROM ppz_fl WHERE flname='$newname' AND flid=$id";
                            $notsqlresult=mysqli_query($conn,$notsql);
                            if(mysqli_num_rows($notsqlresult)>0){//判断分类是否进行了更改
                                echo 401;
                                exit;
                            }

                            $linksql="SELECT * FROM ppz_fl WHERE fllinkid=$fllinkid AND flname='$newname'";
                            $linkresult=mysqli_query($conn,$linksql);
                            if(mysqli_num_rows($linkresult)>0){ //判断列表下是否存在重名分类
                                echo 400;
                                exit;
                            }

                            $newsql="UPDATE ppz_fl SET flname='$newname' WHERE flid=$id";
                            if(mysqli_query($conn,$newsql)){
                                echo 200;
                            }else{
                                echo 600;
                            }
                       }else{
                        echo 500;
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
    mysqli_close($conn);
}
?>