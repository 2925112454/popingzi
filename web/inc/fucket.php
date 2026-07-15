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
        if($vip==4){
            if(!isset($_POST['fuck'])){
                $_POST['fuck']="";
            }
            $fuck=trim($_POST['fuck']);
            if(!empty($fuck)){
                function isStringAnArrayLikeByPipe($str) {    
                    // 确保是字符串    
                    if (!is_string($str)) {    
                        return false;    
                    }    
                  
                    // 如果字符串不包含'|'，则它必须是非空的单个元素    
                    if (strpos($str, '|') === false) {    
                        return trim($str) !== '';    
                    }    
                  
                    // 如果字符串包含'|'，则按'|'分割并检查每个部分是否非空    
                    $parts = explode('|', $str);    
                  
                    // 过滤掉空字符串  
                    $filteredParts = array_filter($parts, function($part) {    
                        return trim($part) !== '';    
                    });  
                  
                    // 检查是否有重复项  
                    $uniqueParts = array_unique($filteredParts);  
                    if (count($filteredParts) !== count($uniqueParts)) {  
                        return false; // 如果有重复项，返回 false  
                    }
                    // 如果没有重复项，并且所有部分都非空，则返回 true  
                    return $filteredParts !== [];    
                }
                if(!isStringAnArrayLikeByPipe($fuck)){
                    echo 400;
                    exit();
                }


            }
            $fucknewsql="update ppz_fuck set fuck='$fuck' where id=1";
            $fucknewresult=mysqli_query($conn,$fucknewsql);
            if($fucknewresult){  
                echo 200;  
            }else{  
                echo 600;  
            }
        }else{
            echo 500;
        }
    }
}
?>