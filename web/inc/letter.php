<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
if (empty($ppzusername)){//判断是否登录
    echo 500; 
}else{
    include __DIR__.'/conn.php';//连接数据库
    if (!isset($_POST["text"])) {
        $_POST["text"]="";
    }
    if (!isset($_POST["terid"])) {
        $_POST["terid"]="";
    }
$fuckyes=0;
$text=trim($_POST["text"]);//获取私信内容
$id=trim($_POST["terid"]);//收件人id
//敏感词数组
$fucksql="select * from ppz_fuck where binary id = 1";
$fuckretval=mysqli_query($conn,$fucksql);
if (mysqli_num_rows($fuckretval)!==1){
    $fuck="大撒比|fuck|FUCK|Fuck|Sb|sB|tmd|Tmd|TmD|TMd|tmD|傻*|操你|操你妈|草你妈|傻逼|你妈逼|傻屌|傻B|SB|TMD|骚货|骚狐狸|狗日的|屌丝|草泥马|艹尼|艹你|艹泥|艹逼|艹B|哈鸡儿|傻鸡儿|鸡8|鸡巴|鸡八|操逼|我日你|沃日你|日你妈|策你妈|太阳你妈|dog太阳的";//使用默认的敏感词
}else{
    $queryfuck = $conn->query($fucksql);
     while($rowfc = $queryfuck->fetch_array()){
         $fuck=$rowfc['fuck'];//读取数据库中自定义的敏感词
    }
}
$fuckarr = explode("|", $fuck);
foreach ($fuckarr as $word) {  
    if (empty($word)) {
        continue;
    }
    if (strpos($text, $word) !== false) {
        $fuckyes = 200;
        break;
    }
} 
if (isset($word) && $fuckyes===200){//如果存在脏话
    echo 305;
}else{
//获取$text字数
$text_len=mb_strlen($text,"utf-8");
if ($text_len > 80){
    echo 80;
}else{

    if (empty($text)||$text_len<1){
        echo 100;
    }else{

        if (empty($id) ||!is_numeric($id)||$id<1){//判断id是不是有效数字且是不是空值
            echo 500; 
        }else{
            $tx=htmlspecialchars($text);//防止xss攻击
            $texthtml = str_replace("\n", "", $tx);//去掉换行符
            $ip = $_SERVER['REMOTE_ADDR'];//获取用户ip
            
            $rowsql = "select * from ppz_newusername where binary uusername = $ppzusername";//获取登录会员信息
            $rowretval=mysqli_query($conn,$rowsql);
            if(mysqli_num_rows($rowretval) !== 1){ 
                echo 500;
            }else{
                $query = $conn->query($rowsql);
                while($row = $query->fetch_array()){
                $uid=$row['uid'];//发件人id
                }
                if ($id==$uid){ 
                    echo 400;//如果是自己给自己发送私信的情况
                }else{
                    $rowsql2 = "select * from ppz_newusername where binary uid = $id";//获取收件人信息
                    $rowretval2=mysqli_query($conn,$rowsql2);
                    if(mysqli_num_rows($rowretval2) !== 1){ 
                        echo 404;//收件人不存在
                    }else{

                $query2 = $conn->query($rowsql2);
                while($row2 = $query2->fetch_array()){
                $ubanx=$row2['uban'];//收件人状态，1为正常，其余为封禁
                }
                  if ($ubanx==1){
  
                        $rowsql3 = "select * from ppz_letter where teradmin = $uid order by terid desc limit 1";//获取数据表里，关于发件人最新的一条私信
                        $rowretval3=mysqli_query($conn,$rowsql3);
                        if(mysqli_num_rows($rowretval3) < 1){
                            $yes=0;
                        }else{
                            $yes=1;
                            $query3 = $conn->query($rowsql3);
                            while($ter = $query3->fetch_array()){
                            $newtime=$ter['tertime'];//最近发送时间
                            }
                        }

                        if ($yes==0){

                                // 创建预处理语句    
                                $stmt = $conn->prepare("INSERT INTO ppz_letter (tertext,teradmin,teruser,terip) VALUES (?,?,?,?)");    
                                if ($stmt === false) {  
                                    die("数据库写入异常: " . $conn->error);  
                                }else{
                                    // 绑定参数  
                                    $stmt->bind_param("ssss", $texthtml, $uid, $id,$ip);  // 绑定参数  
                                    if ($stmt->execute() === TRUE) {   // 执行预处理语句    
                                        echo 200;                                        
                                    } else {      
                                        echo 500;  
                                    } 
                                }                   
                                $stmt->close();// 关闭预处理语句


                        }else if($yes==1){
                            $time=time();//获取当前时间戳
                            $newtimestr=strtotime($newtime);//转换为时间戳
                            if ($time-$newtimestr > 180){ //判断是否超过3分钟

                                     // 创建预处理语句    
                                     $stmt2 = $conn->prepare("INSERT INTO ppz_letter (tertext,teradmin,teruser,terip) VALUES (?,?,?,?)");    
                                     if ($stmt2 === false) {  
                                         die("数据库写入异常: " . $conn->error);  
                                     }else{
                                         // 绑定参数  
                                         $stmt2->bind_param("ssss", $texthtml, $uid, $id,$ip);  // 绑定参数  
                                         if ($stmt2->execute() === TRUE) {   // 执行预处理语句    
                                             echo 200;                                        
                                         } else {      
                                             echo 500;  
                                         } 
                                     }                   
                                     $stmt2->close();// 关闭预处理语句

                            }else{
                                echo 505;
                            }
                    
                      

                        }else{
                            echo 500;
                        }

                    }else{
                        echo 9527;
                    }   


                    }
                }
            }

        }          

    }

}

}
mysqli_close($conn);//关闭数据库连接
}
?>