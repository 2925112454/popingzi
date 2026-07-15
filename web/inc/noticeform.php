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
            if ($vip==3||$vip==4){
                if (!isset($_POST['id'])){
                    $_POST['id']="";
                }
                if (!isset($_POST['text'])){
                    $_POST['text']="";
                }
                if (!isset($_POST['tite'])){
                    $_POST['tite']="";
                }
                if (!isset($_POST['top'])){
                    $_POST['top']="";
                }
                if (!isset($_POST['img'])){
                    $_POST['img']="";
                }
                $eid=trim($_POST['id']);//id
                $evel=trim($_POST['text']);//内容
                $tite=trim($_POST['tite']);//标题
                $noticetop=trim($_POST['top']);//置顶
                $img=trim($_POST['img']);//封面
                //判断id是否是大于0的正整数
                if(preg_match("/^[1-9]\d*$/",$eid)&&($noticetop==1||$noticetop==2)){
                    if(!empty($evel)&&!empty($tite)&&!empty($noticetop)){

                        if (!empty($img)){
                            //判断url地址后缀是否是图片后缀
                            if (!preg_match("/.(jpg|jpeg|png|gif|bmp|webp|svg|ico)$/i",$img)){
                                echo 500;
                                exit();
                            }
                        }
                        //判断id是否存在
                        $ggsql = "select * from ppz_announcement where ggid = $eid";
                        $ggretval=mysqli_query($conn,$ggsql);
                        if(mysqli_num_rows($ggretval) !== 1){ 
                            echo 404;
                        }else{
                            $stmt = $conn->prepare("UPDATE ppz_announcement SET ggbigtext = ?, ggtext = ?, ggtop = ?, ggimg = ? WHERE ggid = ?");
                            $stmt->bind_param("sssss", $evel, $tite, $noticetop, $img, $eid);
                            if ($stmt->execute()) {
                                echo 200;
                            } else {
                                echo 600;
                            }
                            $stmt->close();
                        }


                    }else{
                        echo 500;
                    }
                }else{
                    echo 800;
                }

            }else{
                echo 500;
            }
        }
}

?>