<?php
//回复评论页面
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION
if (empty($ppzusername)){//判断是否登录
    echo 500; 
}else{
    if (!isset($_POST['rplid'])){
        $_POST['rplid']='';
    }
    if (!isset($_POST['rtext'])){
        $_POST['rtext']='';
    }

$id=trim($_POST['rplid']);//回复id
$text=trim($_POST['rtext']);//回复内容
$text=strip_tags($text);//过滤html标签
include __DIR__.'/conn.php';//连接数据库
//获取违禁词配置
$fuck_sql="select * from ppz_fuck where id=1";
$fuck_result=mysqli_query($conn,$fuck_sql);
$fucksize = mysqli_num_rows($fuck_result);
if($fucksize==1){
  while($fuckrow = $fuck_result->fetch_array()){
    $fucktext=$fuckrow['fuck'];
  }
  if(!is_null($fucktext)&&$fucktext!==false&&$fucktext!==null){
    $fuckyes=null;
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

//判断id是否为空且是否是数字
if (!empty($id)&&is_numeric($id)&&$id>0){

//获取$text字数
$text_len=mb_strlen($text,"utf-8");

    if ($text_len > 90){
        echo 400;
    }else{

        if (empty($text)){ //判断text是否为空
            echo 404;
        }else{
            $tx=htmlspecialchars($text);//防止xss攻击
            $texthtml = str_replace("\n", "", $tx);//去掉换行符
            $ip = $_SERVER['REMOTE_ADDR'];//获取用户ip
            $sql = "select * from ppz_commentary where binary plid = $id";//查询数据库，判断评论
            $retval=mysqli_query($conn,$sql);
            if(mysqli_num_rows($retval) !== 1){	//评论不存在
                echo 404;
            }else{

                $rowsql = "select * from ppz_newusername where binary uusername = $ppzusername";//获取回复者数据库表
                $rowretval=mysqli_query($conn,$rowsql);
                if(mysqli_num_rows($rowretval) !== 1){ 
                    echo 500;
                }else{
                    $query = $conn->query($rowsql);
                    while($row = $query->fetch_array()){
                    $uid=$row['uid'];//用户id
                    $uban=$row['uban'];//用户封禁状态，1正常，反之被封禁
                    }

        if ($uban==1){
                     
                     $rowsqlnew = "select * from ppz_reply where binary repadmin = $uid  ORDER BY repid DESC LIMIT 1";//获取回复者最新的一条数据
                     $rowretvalnew=mysqli_query($conn,$rowsqlnew);
                     if(mysqli_num_rows($rowretvalnew) < 1){ 
                       $new=true;
                    }else{ 

                    $querynew = $conn->query($rowsqlnew);
                    while($newx = $querynew->fetch_array()){
                    $reptime=$newx['reptime'];//回复时间
                    }
                    $time=time();//获取当前时间戳
                    $newtime=strtotime($reptime);//转换为时间戳
                    if ($time-$newtime > 180){ //判断是否超过3分钟
                    $new=true;
                    }else{
                    $new=false;
                    }
                    };

                 if($new===true){

                    $sqlmun = "select * from ppz_reply  WHERE binary repplid=$id && binary repadmin = $uid";//获取回复
                    $retvalun=mysqli_query($conn,$sqlmun);
                    if(mysqli_num_rows($retvalun) >= 3){//判断是否超过3条回复
                        echo 303;
                    }else{

                        // 创建预处理语句    
                    $stmt = $conn->prepare("INSERT INTO ppz_reply (repplid,reptext,repadmin,repip) VALUES (?,?,?,?)");    
                    if ($stmt === false) {  
                        die("数据库写入异常: " . $conn->error);  
                    }else{
                        // 绑定参数  
                        $stmt->bind_param("ssss", $id, $texthtml, $uid,$ip);  // 绑定参数  
                        if ($stmt->execute() === TRUE) {   // 执行预处理语句    
                            echo 200;                                        
                        } else {      
                            echo 500;  
                        } 
                    }                   
                    $stmt->close();// 关闭预处理语句


                    }

                }else{
                    echo 300;
                }
        }else{
            echo 500;
        }



                }

            }
        }

    }

}else{
    echo 500;
};
mysqli_close($conn);//关闭数据库连接
}
}
?>