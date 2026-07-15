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
        if($vip==4){//判断是否为站长
            if(!isset($_POST['json'])){
                $_POST['json']="";
            }
            if(!isset($_POST['mode'])){
                $_POST['mode']="";
            }
            $jsonx=$_POST['json'];//原始json内容
            $json=preg_replace('/\s+/', '', $jsonx);//去除所有空格、回车、换行、制表符
            $mode=$_POST['mode'];//轮播图模式：数字1-6，分别代表热门文章、精华文章、置顶文章、最新发布、最多阅览、自定义JSON
            if($mode==1||$mode==2||$mode==3||$mode==4||$mode==5||$mode==6){
                
                    if (!is_null($json)&&$json!=""&&$json!=null){

                        function isValidJsonFormat($input) {  
                            // 尝试解码JSON字符串  
                            $decoded = json_decode($input, true);  
                            // 检查是否解码成功  
                            if (json_last_error() !== JSON_ERROR_NONE) {  
                                return false; // 解码失败，返回false  
                            }  
                            // 检查是否是一个数组  
                            if (!is_array($decoded)) {  
                                return false; // 不是数组，返回false  
                            }  
                            // 遍历数组检查每个元素  
                            foreach ($decoded as $item) {  
                                // 检查每个元素是否是一个关联数组，并且包含'img'和'url'键  
                                if (!is_array($item) || !isset($item['img']) || !isset($item['url'])) {  
                                    return false; // 元素不符合要求，返回false  
                                }  
                            }  
                            // 所有检查都通过，返回true  
                            return true;  
                        }
                        if(!isValidJsonFormat($json)){
                            echo 404;
                            exit();
                        }


                    }

                        //将数据写入ppz_diy表中，mode写入carousel字段，json写入image字段
                        $diysql = "update ppz_diy set carousel='$mode',image='$json' where diyid=1";
                        if(mysqli_query($conn,$diysql)){
                            echo 200;
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
}
?>