<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION
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
        if($vip==4){//判断是否为站长
            $indexset=$_POST['index'];//首页版面设置
            $dayset=$_POST['day'];//白天模式设置
            $nightset=$_POST['night'];//暗夜模式设置

            if(empty($indexset)||empty($dayset)||empty($nightset)){
                echo 500;
            }else{
                // 定义一个包含有效值的数组  
                $validValues = [1, 2, 3];
                if (!in_array($indexset, $validValues) || !in_array($dayset, $validValues) || !in_array($nightset, $validValues)) { // 检查是否包含无效值
                    echo 500;
                }else{
                    //将数据写入ppz_diy表中，indexset写入diyindex字段,dayset写入diyday，nightset写入diynight字段
                    $diysqlf = "update ppz_diy set diyindex='$indexset',diyday='$dayset',diynight='$nightset' where diyid=1";
                    if(mysqli_query($conn,$diysqlf)){
                        echo 200;
                    }else{
                        echo 500;
                    }
                }


            }

        }else{
            echo 500;
        }
    }
}
?>