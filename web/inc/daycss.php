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
            if(!isset($_POST['daycss'])){
                $_POST['daycss']="";
            }
            $cssx=$_POST['daycss'];//原始css内容
            // 去除注释，但保留变量声明内的空格  
            $css = preg_replace('/\/\*.*?\*\//s', '', $cssx); // 去掉注释，包括多行注释 
            if(!empty($css)){
                 // CSS变量声明遵循规范的格式，即“--variable-name: value;”  
                preg_match_all('/^--[a-zA-Z_-][a-zA-Z0-9_-]*\s*:\s*(.*?);/m', $css, $matches, PREG_SET_ORDER); 
                // 遍历所有匹配到的CSS变量声明  
            foreach ($matches as $match) {
                if (strpos($match[0], '--') !== 0) {  
                    echo 404; // 如果发现一个不合法的变量名，输出404
                    exit(); // 退出脚本  
                }
                // 提取变量名（去掉“--”和冒号及其后面的部分）  
                $variableName = substr($match[0], 2, strpos($match[0], ':') - 2);  
                // 检查变量名是否合法  
                if (!preg_match('/^[a-zA-Z_-][a-zA-Z0-9_-]*$/', $variableName)) {  
                    echo 404; // 如果发现一个不合法的变量名，输出404
                     exit(); // 退出脚本  
                }
            }
                    $lines = explode("\n", $css);  
                    foreach ($lines as $line) {  
                        // 去除行首尾的空白字符  
                        $trimmedLine = trim($line);  
                        // 检查是否包含非法的CSS变量声明（即以单个-开头但不是CSS变量的）  
                        if (preg_match('/^-[^-].*?:[^;]*;/', $trimmedLine) && !preg_match('/^--[a-zA-Z_-][a-zA-Z0-9_-]*\s*:\s*(.*?);/', $trimmedLine)) {  
                            echo 404; // 如果发现一个不合法的变量名，输出404
                            exit();                   
                        }  
                    };            
            }

           //将数据写入ppz_diy表中，cssx写入day字段
           $daysql = "update ppz_diy set day='$cssx' where diyid=1";
           if(mysqli_query($conn,$daysql)){
               echo 200;
           }else{
               echo 500;
           }

        }else{
            echo 500;
        }
    }
}
?>