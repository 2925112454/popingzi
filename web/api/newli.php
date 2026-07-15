<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
if (empty($ppzusername)){//判断是否登录
    echo 500;
}else{
    include $_SERVER['DOCUMENT_ROOT'].'/inc/conn.php';//链接数据库
    $rowsql = "select * from ppz_newusername where binary uusername = $ppzusername";//获取登录会员信息
    $rowretval=mysqli_query($conn,$rowsql);
    if(mysqli_num_rows($rowretval) !== 1){ 
        echo 500;
    }else{
        $query = $conn->query($rowsql);
         while($row = $query->fetch_array()){
            $ustatus=$row['ustatus'];//身份，1普通会员，2为管理员，3为副站长，4为站长；此处为LOGO上传操作，仅限站长身份操作
        }
        if ($ustatus==4||$ustatus==3||$ustatus==2){
            if(!isset($_POST['id'])){
                $_POST['id']="";
            }
            $id=trim($_POST['id']);//文章id
            if (empty($id)||!is_numeric($id)||$id<1){
                echo 500;
            }else{
                $sqlc = "select * from ppz_row where rowid=$id";
                $retvalc=mysqli_query($conn,$sqlc);
                if(mysqli_num_rows($retvalc) !== 1){ 
                    echo 500;
                }else{
                    $new=$_POST['new'];//新图片的字符串，以|分割
                    if (!empty($new)){
                        $extensions = array('jpg', 'png', 'gif', 'svg', 'jpeg', 'bmp', 'webp', 'ico','avif');//允许的文件后缀
                        $newarr=explode("|",$new);
                        $newarr=array_unique($newarr);//去重
                        $newarr = array_filter($newarr, 'strlen');//删除空元素
                        //判断数组中每个url地址的后缀是否允许
                        for ($i=0;$i<count($newarr);$i++){
                            $ext = pathinfo($newarr[$i], PATHINFO_EXTENSION);
                            if (!in_array($ext, $extensions)) {
                                echo 500;
                                exit;
                            }
                        }
                        if (count($newarr)>0){
                            $newstr=implode("|",$newarr);//拼接字符串
                            $sql = "update ppz_row set rowbigtext='$newstr' where rowid=$id";
                            if ($conn->query($sql) === TRUE) {
                                echo 200;
                            } else {
                                echo 500;
                            }
                        }
                    }else{
                        echo 500;
                    }
                }
           
            }
        }else{
            echo 500;
        }
    }
    mysqli_close($conn);
}
?>