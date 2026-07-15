<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION
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
            function isNumberStartsWithZero($str) {  
                // 确保输入是字符串类型  
                if (!is_string($str)) {  
                    return false;  
                }  

            }
            if(!isset($_POST['size'])||empty($_POST['size'])){
                $_POST['size']=0;
            }
            if(!isset($_POST['fcsize'])||empty($_POST['fcsize'])){
                $_POST['fcsize']=0;
            }
            if(!isset($_POST['vipsize'])||empty($_POST['vipsize'])){
                $_POST['vipsize']=0;
            }
            if(!isset($_POST['mime'])||empty($_POST['mime'])){
                $_POST['mime']="";
            }
            if(!isset($_POST['type'])||empty($_POST['type'])){
                $_POST['type']=0;
            }
            if(!isset($_POST['imgtype'])||empty($_POST['imgtype'])){
                $_POST['imgtype']=0;
            }
            $size=trim($_POST['size']);//允许文件上传的大小，单位KB
            $fcsize=trim($_POST['fcsize']);//分成，0-100
            $vipsize=trim($_POST['vipsize']);//vip折扣，0-100
            $mime=htmlspecialchars(strip_tags(trim($_POST['mime'])));
            $type=trim($_POST['type']);//投稿开关，0关闭，1开启
            $imgtype=trim($_POST['imgtype']);//是否允许用户上传文件，1允许，0不允许
            $sizemun=round((float)$size);
            $typemun=round((float)$type);
            $imgtypemun=round((float)$imgtype);
            $fcsizemun=round((float)$fcsize);//round函数，将数字四舍五入为最接近的整数。
            $vipsizemun=round((float)$vipsize);
            if (($type==1||$type==0)&&($imgtype==1||$imgtype==0)){

                if(!empty($size)&&$size>0){
                    if(!is_numeric($size)){//判断是否为数字
                        echo 400;
                        exit();
                    }
                    if (isNumberStartsWithZero($size)) { //判断是否以0开头
                        echo 300;
                        exit();
                    }
                }

                if(!empty($fcsize)&&$fcsize>0){
                    if(!is_numeric($fcsize)){//判断是否为数字
                        echo 400;
                        exit();
                    }
                    if($fcsize<0||$fcsize>100){
                        echo 400;
                        exit();
                    }
                    if (isNumberStartsWithZero($fcsize)) { //判断是否以0开头
                        echo 300;
                        exit();
                    }
                }

                if(!empty($vipsize)&&$vipsize>0){
                    if(!is_numeric($vipsize)){//判断是否为数字
                        echo 400;
                        exit();
                    }
                    if($vipsize<0||$vipsize>100){
                        echo 400;
                        exit();
                    }
                    if (isNumberStartsWithZero($vipsize)) { //判断是否以0开头
                        echo 300;
                        exit();
                    }
                }

                $newsql = "update ppz_upfile set upif=$typemun,upifimg=$imgtypemun,upmime='$mime',upsize=$sizemun,upfcsize=$fcsizemun,upvipsize=$vipsizemun where id=1";
                    if(mysqli_query($conn,$newsql)){
                        echo 200;
                    }else{
                        echo 600;
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