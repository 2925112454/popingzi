<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
if (empty($ppzusername)){
    echo 500; 
}else{

    include __DIR__.'/conn.php';//连接数据库
    $sql = "select * from ppz_newusername where binary uusername = $ppzusername";//查询数据库，判断用户名是否存在
    $retval=mysqli_query($conn,$sql);

    if(mysqli_num_rows($retval) !== 1){	//如果用户名不存在，返回错误代码
        echo 500;
        $_SESSION["ppzusername"] == "";
    }else{
                $query = $conn->query($sql);
                while($row = $query->fetch_array()){
                $ustatus=$row['ustatus'];//会员身份，1普通会员，2为管理员，3为副站长，4为站长
                }
                if ($ustatus==2 || $ustatus==3 || $ustatus==4){
                    if (!isset($_POST['text'])){
                        $_POST['text']="";      
                    }
                    if (!isset($_POST['id'])){
                        $_POST['id']="";      
                    }
                    if (!isset($_POST['if'])){
                        $_POST['if']="";      
                    }
                    $text=trim($_POST['text']);//获取评论内容
                    $id=trim($_POST['id']);//获取评论id
                    $if=trim($_POST['if']);//获取评论类型：2为公告评论，1为文章评论

                    if ($if==1||$if==2){

                        $textlen=mb_strlen($text,'utf-8');//获取内容长度

                        if(empty($text)||$textlen<1){//判断评论内容是否为空
                            echo 400;
                        }else{

                            if ($textlen>240){//判断评论内容长度是否超过240字
                                echo 401;
                            }else{

                                if(empty($id) || !filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])){//判断评论id是否为空
                                    echo 500;
                                }else{

                                    $plsqlname = "ppz_commentary"; // 文章评论数据库名

                                    switch ($if) {// 根据 $if 的值选择不同的数据库表名
                                        case 2:
                                            $plsqlname = "ppz_ggcommentary"; // 公告评论数据库名
                                            break;
                                        case 1:
                                        default:
                                            // 默认情况下使用文章评论数据库名
                                            break;
                                    }

                                    //查询评论是否存在
                                $nplsql="select * from $plsqlname where plid=$id";
                                $retvalpl=mysqli_query($conn,$nplsql);
                                if(mysqli_num_rows($retvalpl) < 1){
                                    echo 404;
                                }else{
                                    while($row = $retvalpl->fetch_array()){
                                        $pltext=$row['plbigtext'];
                                    }
                                    if ($pltext==$text){//判断评论内容是否相同
                                        echo 402;
                                    }else{
                                        //开始修改评论
                                        $newsql="update $plsqlname set plbigtext='$text' where plid=$id";
                                        if(mysqli_query($conn,$newsql)){
                                            echo 200;//成功
                                        }else{
                                            echo 600;//错误
                                        }
                                    }


                                }






                                }

            
                            }


                        }
                    }else{
                        echo 500;
                    }


                }else{
                    echo 500;
                }
    }

    mysqli_close($conn);//关闭数据库连接
}
?>