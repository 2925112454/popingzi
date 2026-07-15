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
            if ($vip==3||$vip==4){//判断是否为管理员
                if(!isset($_POST["if"])){
                    $_POST["if"]="";
                }
                if(!isset($_POST["idsx"])){
                    $_POST["idsx"]="";
                }
                $if=$_POST["if"];//状态，1正常，2封禁
                $idsxx=$_POST["idsx"];//会员id数组(英文逗号分割的字符串)
                if($if==1||$if==2){
                    //判断ids是否为空
                    if(empty($idsxx)){
                        echo 500;
                    }else{
                        $err=200;
                        $ids = explode(",", $_POST["idsx"]);  
                        $ids = array_map('intval', $ids); // 将所有ID转换为整数  
                        $idsarr = array_unique(array_filter($ids, function($id) { return $id > 0; })); // 移除重复项和非正数
                        //判断数组是否都是正整数
                        for($i=0;$i<count($idsarr);$i++){
                            if(!is_numeric($idsarr[$i])||$idsarr[$i]<1){
                                $err=500;
                            }else{
                                //判断是否存在会员
                                $sqlrow = "select * from ppz_newusername where uid = $idsarr[$i] and ustatus!=4 and ustatus<$vip and uusername!= $ppzusername";//获取会员信息
                                $retvalrow=mysqli_query($conn,$sqlrow);
                                if(mysqli_num_rows($retvalrow) < 1){
                                    $err=404;
                                }
                            }
                        }

                        if($err==500){
                            echo 500;
                        }else if($err==404){
                            echo 404;
                        }else if($err==200){
                            for($i=0;$i<count($idsarr);$i++){
                                $sqlnew = "update ppz_newusername set uban = $if where uid = $idsarr[$i] and ustatus!=4 and uusername!= $ppzusername";
                                if(mysqli_query($conn,$sqlnew)){
                                    $errcon= 200;//成功
                                }else{
                                    $errcon= 600;//失败
                                }
                            }
                            echo $errcon;
                        }else{
                            echo 500;
                        }


                    }

                }else{
                    echo 500;
                }
                
            }else{
                echo 500;
            }
        }
    }
?>