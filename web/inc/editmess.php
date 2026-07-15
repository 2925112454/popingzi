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
            if ($vip==3||$vip==4||$vip==2){
                if (!isset($_POST['id'])){
                    $_POST['id']="";
                }
                if (!isset($_POST['text'])){
                    $_POST['text']="";
                }
                $eid=trim($_POST['id']);
                $evel=trim($_POST['text']);
                $evel=strip_tags($evel);//过滤html标签
                //判断id是否是大于0的正整数
                if(preg_match("/^[1-9]\d*$/",$eid)){
                    //判断评论内容是否为空
                    if(!empty($evel)){
                        //判断id是否存在私信
                        $esql = "select * from ppz_letter where binary terid = $eid";
                        $eretval=mysqli_query($conn,$esql);
                        if(mysqli_num_rows($eretval) !== 1){
                            echo 500;
                        }else{
                            $mewesql = "update ppz_letter set tertext = '$evel' where terid = $eid";
                            if(mysqli_query($conn,$mewesql)){
                                echo 200;
                            }else{
                                echo 600;
                            }

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

 mysqli_close($conn);
}
?>