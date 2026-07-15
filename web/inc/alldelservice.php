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
                if(!isset($_POST['allid'])  ){
                    $_POST['allid']="";
                }
                $allid=$_POST['allid'];//获取要删除的id
                $allid=str_replace("，",",",$allid);//将中文逗号转换为英文
                //判断是否有id
                if (empty($allid)){
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
                            //获取附件地址（本地或外链地址）
                            while ($row = $result->fetch_assoc()) {
                                $wkimgilnk = $row['wkimg'];
                            }
                            if ($wkimgilnk != "" && !is_null($wkimgilnk) && !empty($wkimgilnk)) {
                                if (strpos($wkimgilnk, 'http') !== false) {
                                    // 外链地址，不进行删除操作
                                } else {
                                    // 本地地址
                                    $wkimgilnk = str_replace('../', '/', $wkimgilnk);//去除../
                                    $wkimgilnk = $_SERVER['DOCUMENT_ROOT'] . $wkimgilnk;//获取本地地址
                                    if (file_exists($wkimgilnk)) {
                                        unlink($wkimgilnk);
                                    }
                                }
                            }
                            // 删除内容
                            $deleteStmt = $conn->prepare("DELETE FROM ppz_work WHERE binary id = ?");
                            $deleteStmt->bind_param("s", $id);
                            if ($deleteStmt->execute()) {
                                $successCount++;
                            } else {
                                // 记录错误日志
                                //error_log("Failed to delete record with ID: " . $id);
                            }
                            $deleteStmt->close();
                        } else {
                            // 记录错误日志
                            //error_log("Record not found for ID: " . $id);
                        }
                        $stmt->close();
                    }
                    if ($successCount == $totalCount) {
                        echo 200; // 全部删除成功
                    } else {
                        echo 600; // 存在删除失败的情况
                    }
                }

            }
    }
    mysqli_close($conn);//关闭数据库
}
?>