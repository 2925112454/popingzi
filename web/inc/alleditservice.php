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
                if(!isset($_POST['allidx'])){
                    $_POST['allidx']="";
                }
                if(!isset($_POST['allmuns'])){
                    $_POST['allmuns']="";
                }
                $allid=$_POST['allidx'];//获取要修改状态的id
                $allid=str_replace("，",",",$allid);//将中文逗号转换为英文
                $allmuns=$_POST['allmuns'];//状态
                if ($allmuns==1||$allmuns==2){
                    //判断是否有id
                    if (is_null($allid)||empty($allid) || !isset($allid)){
                        echo 500;
                    }else{
                        // 将id转换为数组
                        $allid = explode(",", $allid);
                        $successCount = 0;
                        $totalCount = count($allid);
                        foreach ($allid as $id) {
                            // 使用预处理语句防止SQL注入
                            $stmt = $conn->prepare("SELECT * FROM ppz_work WHERE binary id = ?");
                            $stmt->bind_param("s", $id);
                            $stmt->execute();
                            $result = $stmt->get_result();
    
                            if ($result->num_rows == 1) {
                                // 修改内容
                                $updateStmt = $conn->prepare("UPDATE ppz_work SET wkyes = ? WHERE binary id = ?");
                                $updateStmt->bind_param("ss", $allmuns, $id);
                                if ($updateStmt->execute()) {
                                    $successCount++;
                                } else {
                                    // 记录错误日志
                                    //error_log("Failed to update record with ID: " . $id);
                                }
                                $updateStmt->close();                                
                            } else {
                                // 记录错误日志
                                //error_log("Record not found for ID: " . $id);
                            }
                            $stmt->close();
                        }
                        if ($successCount == $totalCount) {
                            echo 200; // 全部修改成功
                        } else {
                            echo 600; // 存在修改失败的情况
                        }
                    }
                }else{
                    echo 500;
                }
            }else{
                echo 500;
            }
    }
    mysqli_close($conn);//关闭数据库
}
?>