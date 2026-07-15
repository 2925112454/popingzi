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

            if (!isset($_POST['name'])){
                $_POST['name']="";      
            }
            if (!isset($_POST['ico'])){
                $_POST['ico']="";      
            }
            if (!isset($_POST['nav'])){
                $_POST['nav']="";      
            }
            if (!isset($_POST['int'])){
                $_POST['int']="";      
            }
            if (!isset($_POST['id'])){
                $_POST['id']="";      
            }

            $name=trim($_POST['name']);//获取导航名称
            $ico=trim($_POST['ico']);//获取导航图标
            $nav=$_POST['nav'];//封面的类别，1为竖屏，2为横屏，3为资讯类(即一排左侧单图或无图模式)
            $int=$_POST['int'];//封面的单行数量，1为默认4张，2为3张（注明：对于类别为‘资讯类’的，此参数无效）
            $id=$_POST['id'];//获取导航id

            if(($nav==1||$nav==2||$nav==3)&&($int==1||$int==2)){
                if(empty($name)){
                    echo 404;
                }else{
                    $navmun=round((float)$nav);//导航类别
                    $intmun=round((float)$int);//单行数量
                    $updatesql = "update ppz_link set linkname='$name',linkico='$ico',linkimg=$navmun,linkint=$intmun where linkid=$id";
                    if(mysqli_query($conn,$updatesql)){
                        echo 200;
                    }else{
                        echo 600;
                    }
                }
            }else{
                echo 500;
            }
        }else{
            echo 500;
        }
    }
    mysqli_close($conn);
}
?>