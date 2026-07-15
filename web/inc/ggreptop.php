<?php
//评论点击界面
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
if (empty($ppzusername)){//判断是否登录
    echo 500; 
}else{
    if (!isset($_POST['topid'])){
        $_POST['topid']="";
    }
    $id=trim($_POST['topid']);//评论id
    if (empty($id)||!is_numeric($id)||$id<1){
        echo 500;
    }else{
        $idx=intval($id);//转化为整型
        if (is_int($idx)&&$idx>0){//判断是不是整数
            include __DIR__.'/conn.php';//连接数据库
            $rowsql = "select * from ppz_newusername where binary uusername = $ppzusername";//获取回复者数据库表
            $rowretval=mysqli_query($conn,$rowsql);
            if(mysqli_num_rows($rowretval) !== 1){ 
                echo 500;
            }else{
                $query = $conn->query($rowsql);
                while($row = $query->fetch_array()){
                $uid=$row['uid'];//用户id
                }

                $sql = "select * from ppz_ggcommentary  WHERE binary plid=$idx";//获取评论
                $retval=mysqli_query($conn,$sql);
                if(mysqli_num_rows($retval) !== 1){
                    echo 500;
                }else{

                    $pl = $conn->query($sql);
                    while($p = $pl->fetch_array()){
                        $pltop=$p['pltop'];//获取点赞数组
                    }

                    if (empty($pltop)){
                        $newpl=$uid;
                        $err=200;
                    }else{
                        $pltopyes=explode('|',$pltop);
                        //判断是否已经点赞
                        if(in_array($uid,$pltopyes)){//如果用户已经点赞则取消点赞
                            $filteredArray = array_filter($pltopyes, function($value) use ($uid) {  
                                return $value != $uid;  
                            });  
                            $newpl=implode("|",$filteredArray);  //重新将去除后的数组赋值给点赞列表
                            $err=300;
                        }else{
                        $newpl=$pltop.'|'.$uid; //如果没点赞则加入点赞
                        $err=200;
                        }

                    }

                    $newtopsql = "UPDATE ppz_ggcommentary SET pltop='$newpl' WHERE plid=$idx";
                    
                    if ($conn->query($newtopsql) === TRUE) { 
                       echo $err;                                    
                    } else {  
                      // echo $newsc;
                       echo "Error: " . $conn->error;  // 输出详细的错误信息  
                    };







                }
            }

        }else{
            echo 500;

        }
    }
}
mysqli_close($conn);//关闭数据库连接
?>