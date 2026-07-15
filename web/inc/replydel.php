<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION
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
                    if (!isset($_POST['rid'])){
                        $_POST['rid']="";
                    }
                    if (!isset($_POST['dif'])){
                        $_POST['dif']="";
                    }
                    if (!isset($_POST['all'])){
                        $_POST['all']="";
                    }
                    if (!isset($_POST['allrepid'])){
                        $_POST['allrepid']="";
                    }
                    if (!isset($_POST['allrepif'])){
                        $_POST['allrepif']="";
                    }

                    $rid=trim($_POST['rid']);//回复id
                    $dif=trim($_POST['dif']);//获取评论类型：1为公告评论，2为文章评论
                    $all=trim($_POST['all']);//操作类型，1为清空全部回复，反之只删除本条回复
                    
                    if ($all==1){
                        $allrepid=trim($_POST['allrepid']);//获取评论id
                        $allrepif=trim($_POST['allrepif']);//获取评论类型：2为公告评论，1为文章评论
                        if ($allrepif==1||$allrepif==2){
                            if (empty($allrepid)||$allrepid<1||!is_numeric($allrepid)){
                                echo 500;
                            }else{
                                if ($allrepif==2){
                                    $plsqlname="ppz_ggcommentary";//公告评论数据库名
                                    $psqlname="ppz_ggreply";//公告回复数据库名
                                }else{
                                    $plsqlname="ppz_commentary";//文章评论数据库名
                                    $psqlname="ppz_reply";//文章回复数据库名
                                }
                                //查询评论是否存在
                                $nplsql="select * from $plsqlname where plid=$allrepid";
                                $retvalpl=mysqli_query($conn,$nplsql);
                                if(mysqli_num_rows($retvalpl) < 1){
                                    echo 404;
                                }else{
                                    //查询回复数据库中是否存在回复
                                    $nprepsql="select * from $psqlname where repplid=$allrepid";
                                    $retvalprep=mysqli_query($conn,$nprepsql);
                                    if(mysqli_num_rows($retvalprep) > 0){
                                        //清空对应评论下的所有回复
                                        $delprepsql="delete from $psqlname where repplid=$allrepid";
                                        if(mysqli_query($conn,$delprepsql)){
                                            echo 200;
                                        }else{
                                            echo 600;
                                        }
                                    }else{
                                        echo 405;
                                    }
                                }
                            }
                        }else{
                            echo 500;
                        }
                    }else{
                        if(empty($rid)||$rid<1||!is_numeric($rid)){
                            echo 500;
                        }else{
                            if ($dif==1||$dif==2){
                                if ($dif==1){
                                    $sqlname="ppz_ggreply";//公告回复数据库名
                                }else{
                                    $sqlname="ppz_reply";//文章回复数据库名
                                }
                                
                                $repsql="select * from $sqlname where repid=$rid";//查询回复
    
                                //查询回复是否存在
                                $retval=mysqli_query($conn,$repsql);
                                if(mysqli_num_rows($retval) !== 1){
                                    echo 404;
                                }else{
                                    $delrepsql="delete from $sqlname where repid=$rid";
                                    if(mysqli_query($conn,$delrepsql)){
                                        echo 200;
                                    }else{
                                        echo 600;
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
    mysqli_close($conn);//关闭数据库连接
}
?>