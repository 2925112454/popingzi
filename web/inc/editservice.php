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
                if(!isset($_POST['id'])){
                    $_POST['id']=""; 
                }
                if(!isset($_POST['text'])){
                    $_POST['text']=""; 
                }
                if(!isset($_POST['if'])){
                    $_POST['if']=""; 
                }
                $id=trim($_POST['id']);//工单id
                $text=trim($_POST['text']);//回复内容
                $if=trim($_POST['if']);//状态，1待处理，2已处理
                $text=strip_tags($text);//过滤标签
                $text=htmlspecialchars($text);//过滤特殊字符
                //判断id格式是否是正整数
                if (preg_match("/^[0-9]*$/",$id)){
                    //判断回复内容是否为空
                        if (!is_null($text) && $text!=""){
                            $newtext=$text;
                        }else{
                            $newtext=null;
                        }
                        if ($if==1||$if==2){
                            //查询工单是否存在
                            $sql = "select * from ppz_work where binary id = $id";
                            $retval=mysqli_query($conn,$sql);
                            if(mysqli_num_rows($retval) !== 1){
                                echo 404;
                            }else{
                                //修改工单,使用预处理语句防止SQL注入
                                $stmt = $conn->prepare("UPDATE ppz_work SET wkhf = ?, wkyes = ? WHERE binary id = ?");
                                $stmt->bind_param("ssi", $newtext, $if, $id);
                                if ($stmt->execute()) {
                                    echo 200;
                                } else {
                                    echo 500;
                                }
                                $stmt->close();
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
    mysqli_close($conn);//关闭数据库
}
?>