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
                    if (!isset($_POST['id'])){
                        $_POST['id'] = "";
                    }
                    if (!isset($_POST['type'])){
                        $_POST['type'] = "";
                    }
                    $id=trim($_POST['id']);//获取评论id
                    $type=trim($_POST['type']);//获取评论类型：1为公告评论，2为文章评论

                    if ($type==1||$type==2){
                        if(empty($id) || !filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])){
                            echo 500;
                        }else{
                                    $plsqlname = "ppz_commentary"; // 文章评论数据库名
                                    $rpsqlname = "ppz_reply";//回复数据库名

                                    switch ($type) {// 根据 $type 的值选择不同的数据库表名
                                        case 1:
                                            $plsqlname = "ppz_ggcommentary"; // 公告评论数据库名
                                            $rpsqlname = "ppz_ggreply";
                                            break;
                                        case 2:
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
                                    //查询评论下是否存在回复
                                    $nrpql="select * from $rpsqlname where repplid=$id";
                                    $retvalrp=mysqli_query($conn,$nrpql);
                                    if(mysqli_num_rows($retvalrp) > 0){
                                        $rpdelt=0;//回复删除状态，0为删除失败，1为删除成功
                                        //删除回复
                                        while($row = $retvalrp->fetch_array()){
                                            $rid=$row['repid'];//获取回复id
                                            $delrp="delete from $rpsqlname where repid=$rid";
                                            if(mysqli_query($conn,$delrp)){
                                                $rpdelt=1;//删除成功
                                            }else{
                                                $rpdelt=0;//删除失败
                                            }
                                        }

                                        if ($rpdelt==1){
                                            //开始删除评论
                                            $newsql="delete from $plsqlname where plid=$id";
                                            if(mysqli_query($conn,$newsql)){
                                                echo 200;//成功
                                            }else{
                                                echo 600;//错误
                                            }
                                        }else{
                                            echo 601;
                                        }

                                    }else{
                                        //开始删除评论
                                        $newsql="delete from $plsqlname where plid=$id";
                                        if(mysqli_query($conn,$newsql)){
                                            echo 200;//成功
                                        }else{
                                            echo 600;//错误
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