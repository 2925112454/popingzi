<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
if (empty($ppzusername)){//判断是否登录
    echo 500; 
}else{
    include __DIR__.'/conn.php';//连接数据库
        $rowsql = "select * from ppz_newusername where binary uusername = $ppzusername";//获取登录会员信息
        $rowretval=mysqli_query($conn,$rowsql);
        if(mysqli_num_rows($rowretval) !== 1){ 
            echo 500;
        }else{
            $vipquery = $conn->query($rowsql);
            while($vip = $vipquery->fetch_array()){
                $ustatus=$vip['ustatus'];//会员身份，1普通会员，2为管理员，3为副站长，4为站长
            }
            if ($ustatus==3||$ustatus==4){
                $id=[];
                $time="";
                if(!empty($_POST['time'])&&isset($_POST['time'])&&is_numeric($_POST['time'])&&$_POST['time']>0){
                    $time=trim($_POST['time']);
                }
                if(!is_null($_POST['id'])&&isset($_POST['id'])){
                    $id=trim($_POST['id']);
                }

                if(!empty($time)){
                    if($time==1){

                        if(!empty($id)){
                            //将id转为数组
                            $id=explode(",",$id);
                            $yes=200;//合法状态

                            for($i=0;$i<count($id);$i++){
                                //判断所有id是否都是正整数
                                if (!is_numeric($id[$i]) || $id[$i] < 1 || strpos($id[$i], '.') !== false || strpos($id[$i], ',') !== false) {
                                    $yes=500;
                                    break;
                                }
                                //判断所有记录是否都存在
                                $logsql = "select * from ppz_log  WHERE binary logid=$id[$i]";
                                $logretval=mysqli_query($conn,$logsql);
                                if(mysqli_num_rows($logretval) !== 1){
                                    $yes=404;
                                    break;
                                }
                            }

                            if ($yes==200){
                                $delyes=200;
                                for($i=0;$i<count($id);$i++){
                                    $logsql_del = "delete from ppz_log where binary logid=$id[$i]";//删除记录
                                    if ($conn->query($logsql_del) === TRUE) {
                                        $delyes=200;
                                    }else{
                                        $delyes++;
                                    }
                                }
                                    if ($delyes==200){
                                        echo 200;
                                    }else{
                                        echo 600;
                                    }
                            }else{
                                echo $yes;
                            }

                        }else{
                            echo 500;
                        }

                    }elseif($time==2){
                        $logsql_del = "delete from ppz_log where logtime < DATE_SUB(NOW(), INTERVAL 1 YEAR)";
                        if ($conn->query($logsql_del) === TRUE) {
                            echo 200;
                        }else{
                            echo 700;
                        }
                    }elseif($time==3){
                        $logsql_del = "delete from ppz_log where logtime < DATE_SUB(NOW(), INTERVAL 3 MONTH)";
                        if ($conn->query($logsql_del) === TRUE) {
                            echo 200;
                        }else{
                            echo 700;
                        }
                    }else{
                        echo 500;
                    }
                }else{
                    echo 500;
                };
                
            }else{
                echo 500;
            };

        }
    mysqli_close($conn);//关闭数据库连接
}
?>