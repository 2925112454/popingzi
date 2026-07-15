<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
if (empty($ppzusername)){//判断是否登录
    echo json_encode(array('err'=>500)); 
}else{
    include __DIR__.'/conn.php';//连接数据库
    $sql = "select * from ppz_newusername where binary uusername = $ppzusername";//获取登录会员信息
    $retval=mysqli_query($conn,$sql);
    if(mysqli_num_rows($retval) !== 1){ 
        echo json_encode(array('err'=>500));
    }else{
        $query = $conn->query($sql);
        while($row = $query->fetch_array()){
            $vip=$row['ustatus'];//身份，1普通会员，2为管理员，3为副站长，4为站长
        }
        if ($vip==4){
            if  (!isset($_POST['ser'])) {
                $_POST['ser']="";
            }
            $newser = trim($_POST['ser']);
            $newser = htmlspecialchars($newser);//防止xss
            $newser = addslashes($newser);//防止sql注入
            if(!empty($newser)){
                //判断newser的utf-8的字数
                $newser_length = mb_strlen($newser,'utf-8');
                if($newser_length<11&&$newser_length>0){
                    //判断newser是否重复
                    $sqlx = "select * from ppz_workfl where binary wkname = '$newser'";
                    $retvalx=mysqli_query($conn,$sqlx);
                    if(mysqli_num_rows($retvalx) == 0){
                        //添加newser
                        $newsql = "insert into ppz_workfl (wkname) values ('$newser')";
                        if(mysqli_query($conn,$newsql)){
                            //获取新增的newser的id
                            $ssql = "select * from ppz_workfl where binary wkname = '$newser'";
                            $sretval=mysqli_query($conn,$ssql);
                            if(mysqli_num_rows($sretval) == 1){
                                while($srow = $sretval->fetch_array()){
                                    $newserid=$srow['id'];//获取新增的newser的id
                                }
                            //输出json
                            echo json_encode(array('id'=>$newserid,'name'=>$newser,'err'=>200));
                            }else{
                                echo json_encode(array('err'=>600));
                            }
                        }else{
                            echo json_encode(array('err'=>600));
                        }
                       
                    }else{
                        echo json_encode(array('err'=>400));
                    }
                }else{
                    echo json_encode(array('err'=>500));
                }

            }else{
                echo json_encode(array('err'=>500));
            }
        }else{
            echo json_encode(array('err'=>500));
        }
    }
    mysqli_close($conn);//关闭数据库
}
?>