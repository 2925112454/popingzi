<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
if (empty($ppzusername)){//判断是否登录
    echo 500; 
}else{
    $fuckyes="";
    if(!isset($_POST['coid'])){
        $_POST['coid']="";
    }
    if(!isset($_POST['cotext'])){
        $_POST['cotext']="";
    }
    $id=trim($_POST['coid']);//公告id
    $text=trim($_POST['cotext']);//评论内容
    $text=strip_tags($text);//过滤html标签
    include __DIR__.'/conn.php';
    $sqltxt = "select * from ppz_announcement where binary ggid = $id";
    $retvaltxt=mysqli_query($conn,$sqltxt);
    if(mysqli_num_rows($retvaltxt) !== 1){ 
        echo 500;
    }else{

//获取违禁词配置
$fuck_sql="select * from ppz_fuck where id=1";
$fuck_result=mysqli_query($conn,$fuck_sql);
$fucksize = mysqli_num_rows($fuck_result);
if($fucksize==1){
  while($fuckrow = $fuck_result->fetch_array()){
    $fucktext=$fuckrow['fuck'];
  }
  if(!empty($fucktext)){
        //敏感词数组
        $fuck=$fucktext;
        $fuckarr=explode("|",$fuck);
        foreach ($fuckarr as $word) {  
            if (stripos($text, $word) !== false) {
                $fuckyes=200;
                break; // 如果检测到敏感词，就跳出循环，不再继续检查其他词汇  
            }
        }
        if (isset($word) && $fuckyes===200){
            $fucknum=500;
        }else{
            $fucknum=200;
        }
        
  }else{
    $fucknum=200;
  }
}

if (isset($fucknum) && $fucknum===500){//如果存在脏话
    echo 305;
}else{

    if (empty($id)||!is_numeric($id)||$id<1){
        echo 500;
    }else{
        $idx=intval($id);//转化为整型

        if (is_int($idx)&&$idx>0){//判断是不是整数        
        if (empty($text)){
            echo 404;
        }else{
            
            $rowsql = "select * from ppz_newusername where binary uusername = $ppzusername";//获取评论者数据库表
            $rowretval=mysqli_query($conn,$rowsql);
            if(mysqli_num_rows($rowretval) !== 1){ 
                echo 500;
            }else{
                $query = $conn->query($rowsql);
                while($row = $query->fetch_array()){
                $uid=$row['uid'];//用户id
                }

                $sql = "select * from ppz_ggcommentary  WHERE binary plrowid=$idx && binary pladmin = $uid";//获取评论
                $retval=mysqli_query($conn,$sql);
                if(mysqli_num_rows($retval) >= 3){//判断是否超过3条评论
                    echo 400;
                }else{

                    //转义text
                    $texth=htmlspecialchars($text);
                    //除去回车
                    $texthtml=str_replace("\r\n","",$texth);
      
                    if (mb_strlen($texthtml,'UTF-8')>240){//判断是否超过240个字符
                        echo 300;
                    }else{
                        $ip=$_SERVER['REMOTE_ADDR'];
                        
                        // 创建预处理语句    
                    $stmt = $conn->prepare("INSERT INTO ppz_ggcommentary (plbigtext,plip,plrowid,pladmin) VALUES (?,?,?,?)");    
                    if ($stmt === false) {  
                        die("数据库写入异常: " . $conn->error);  
                    }else{
                        // 绑定参数  
                        $stmt->bind_param("ssss", $texthtml, $ip, $idx,$uid);  // 绑定参数  
                        if ($stmt->execute() === TRUE) {   // 执行预处理语句    
                            echo 200;                                        
                        } else {      
                            echo 500;  
                        } 
                    }                   
                    $stmt->close();// 关闭预处理语句

                    }        


                }

            }



            }
        }

    }
    mysqli_close($conn);//关闭数据库连接
}
}
}
?>