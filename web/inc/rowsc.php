<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION

if (!isset($_POST['rid'])){
    $_POST['rid']="";
}
$rid=trim($_POST['rid']);

if (empty($ppzusername)){
    echo 500; //错误操作
}else{

    if (empty($rid)||!is_numeric($rid)||$rid<1){
        echo 500;
    }else{

        include __DIR__.'/conn.php';
        $sql = "select * from ppz_row  WHERE binary rowid=$rid";//获取文章
        $retval=mysqli_query($conn,$sql);
        if(mysqli_num_rows($retval) !== 1){
            echo 404;
        }else{
  
        $vipsql = "select * from ppz_newusername  WHERE binary uusername=$ppzusername";//获取登录会员信息
        $vipretval=mysqli_query($conn,$vipsql);

                if(mysqli_num_rows($vipretval) !== 1){ 
                 echo 500;
                 }else{

                    $vipquery = $conn->query($vipsql);
                    while($vip = $vipquery->fetch_array()){
                     $ucollect=$vip['ucollect'];//获取用户收藏列表
                     $vuid=$vip['uid'];
                    }

                    if (empty($ucollect)){
                        $newsc=$rid;
                        $err=200;
                        $rowsc="rowsc+1";
                    }else{

                        $ucollectyes=explode('|',$ucollect);

                        if(in_array($rid,$ucollectyes)){//如果用户已经收藏，则取消收藏
                            $filteredArray = array_filter($ucollectyes, function($value) use ($rid) {  
                                return $value != $rid;  
                            });  
                            $newsc=implode("|",$filteredArray);  //重新将去除后的数组赋值给收藏列表
                            $err=203;
                            $rowsc="rowsc-1";
                        }else{
                        $newsc=$ucollect.'|'.$rid; //如果没收藏则加入收藏id
                        $err=200;
                        $rowsc="rowsc+1";
                        }
                    }
                    $newrowsql = "UPDATE ppz_row SET rowsc=$rowsc WHERE rowid=$rid";
                    $newvipsql = "UPDATE ppz_newusername SET ucollect='$newsc' WHERE uid=$vuid";

                    if ($conn->query($newvipsql) === TRUE && $conn->query($newrowsql) === TRUE) { 
                       echo $err;                                    
                    } else {  
                       echo "收藏写入失败！";
                    };



                 };

        }

    }
//关闭数据库
mysqli_close($conn);
}
?>