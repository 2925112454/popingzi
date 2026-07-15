<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
if (empty($ppzusername)){//判断是否登录
    echo 500;
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
                    $uid=$row['uid'];//会员ID
                }
                if ($ustatus==4||$ustatus==3||$ustatus==2){

                    if (!isset($_POST['title'])){
                        $_POST['title']="";
                    }
                    if (!isset($_POST['img'])){
                        $_POST['img']="";
                    }
                    if (!isset($_POST['content'])){
                        $_POST['content']="";
                    }
                    if (!isset($_POST['top'])){
                        $_POST['top']="";
                    }


                    $title=trim(strip_tags($_POST['title']));//标题,去除HTML标签和转义字符、空格
                    $img=trim(str_replace(" ","%20",$_POST['img']));//封面图片
                    $content=trim($_POST['content']);//内容
                    $top=trim($_POST['top']);//置顶

                    //判断标题或内容是否为空
                    if (empty($title)||empty($content)){
                        echo 404;
                        exit();
                    }

                                         //判断封面图片是否是有效的url和后缀
                                         if (!empty($img)){
                                            $typeimg=['jpg', 'jpeg', 'gif', 'png', 'webp', 'svg','avif'];//允许的图片后缀
                                            if (!in_array(pathinfo($img, PATHINFO_EXTENSION), $typeimg)){//判断是否是有效的后缀
                                                echo 401;
                                                exit();
                                            }
                                        }
                    if ($top==1||$top==2){
                        $newsql = "INSERT INTO ppz_announcement (ggtext, ggimg, ggbigtext, ggtop,ggrowid) VALUES (?, ?, ?, ?, ?)";
                        $stmt = mysqli_prepare($conn, $newsql);//预处理
                        if ($stmt) {
                            mysqli_stmt_bind_param($stmt, "sssss", $title,$img,$content,$top,$uid);
                            if (mysqli_stmt_execute($stmt)) {
                                echo 200;
                            }else{
                                echo 500;
                            }
                        }else{
                            echo 600;
                        }
                        mysqli_stmt_close($stmt);
                    }else{
                        echo 500;
                        exit();
                    }
                                       
                    

                }else{
                    echo 500;
                }
    }
}
?>