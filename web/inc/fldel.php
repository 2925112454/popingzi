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
            if(!isset($_POST['fid'])){
                $_POST['fid']="";
            }
            $navid=trim($_POST['fid']);
            if(empty($navid)||$navid<1||!is_numeric($navid)){
                echo 500;
            }else{
                $arr=null;
                //查询列表是否存在
                $navsql = "select * from ppz_fl where flid=$navid";
                $navretval=mysqli_query($conn,$navsql);

                if(mysqli_num_rows($navretval) !== 1){ 
                    $arr=1;
                }else{
                        $flquery = $conn->query($navsql);
                        while($flrow = $flquery->fetch_array()){
                            $flid=$flrow['flid'];
                        //判断分类下是否存在文章
                        $rowrsql = "select * from ppz_row where rowfl=$flid";
                        $rowrretval=mysqli_query($conn,$rowrsql);
                        if(mysqli_num_rows($rowrretval) > 0){
                            $arr=2;
                        }
                        }
                }

                if ($arr===1){
                    echo 500;
                    exit;
                }

                if ($arr===2){
                    echo 501;
                    exit;
                }


                $delsql = "delete from ppz_fl where flid=$navid";
                if(mysqli_query($conn,$delsql)){
                    echo 200;
                }else{
                    echo 600;
                }

            }

        }else{
            echo 500;
        }
    }
    mysqli_close($conn);
}
?>