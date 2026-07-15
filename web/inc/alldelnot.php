<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
if (empty($ppzusername)) { // 判断是否登录
    echo 500;
} else {
    include __DIR__.'/conn.php'; // 连接数据库
    // 使用预处理语句防止SQL注入
    $stmt = $conn->prepare("SELECT * FROM ppz_newusername WHERE binary uusername = ?");
    $stmt->bind_param("s", $ppzusername);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        echo 500;
    } else {
        $row = $result->fetch_assoc();
        $vip = $row['ustatus']; // 身份，1普通会员，2为管理员，3为副站长，4为站长
        if ($vip ==4 || $vip == 3) {
            if (!isset($_POST["ids"])){
                $_POST["ids"]="";
            }
            $ids=$_POST["ids"];
            if(empty($ids)){
                echo 500;
            }else{
                $yesorno=0;
                //转换为数组
                $idsarr=explode(",",$ids);
                //判断数组的每一个值是否都是正整数
                for($i=0;$i<count($idsarr);$i++){
                    if(!preg_match("/^[1-9]\d*$/",$idsarr[$i])){
                        $yesorno=1;
                        break;//跳出循环
                    }
                }

                if($yesorno==0){
                    $delyes=404;

                    for($i=0;$i<count($idsarr);$i++){

                        $sqla="select * from ppz_announcement where ggid=$idsarr[$i]";
                        $resa=mysqli_query($conn,$sqla);
                        if(mysqli_num_rows($resa)==1){
                            //获取封面和内容
                            while($rowa=mysqli_fetch_assoc($resa)){
                                $ggbigtext=$rowa['ggbigtext'];//内容
                                $ggimg=$rowa['ggimg'];//封面
                            }

                            if(!empty($ggimg)){
                                if (strpos($ggimg, "http://") === false && strpos($ggimg, "https://") === false){
                                    $rowimage_url = str_replace("../", "/", $ggimg);//将“../”替换为“/”
                                    $rowimgx = $_SERVER['DOCUMENT_ROOT'] . $rowimage_url;
                                    if (@file_exists($rowimgx)) {
                                        unlink($rowimgx);
                                    }
                               }
                            }

                            if(!empty($ggbigtext)){
                                 // 提取图片地址并删除
                                $pattern = '/<img[^>]+src="([^"]+)"/i';
                                preg_match_all($pattern, $ggbigtext, $matches);
                                $image_urls = $matches[1];//转换数组
                                $image_urls = array_unique($image_urls);//去重
                                foreach ($image_urls as $image_url) {
                                    $image_url = str_replace("../", "/", $image_url);//将“../”替换为“/”
                                    if (@file_exists($_SERVER['DOCUMENT_ROOT'] . $image_url)) {
                                        unlink($_SERVER['DOCUMENT_ROOT'] . $image_url);
                                    }
                                }

                            }
                                $sqld="delete from ppz_announcement where ggid=$idsarr[$i]";
                                $resd=mysqli_query($conn,$sqld);
                                if(!$resd){
                                    $delyes=600;
                                }else{
                                    $delyes=200;
                                }
                        }else{
                            $delyes=404;
                            break;
                        }

                    }

                    if($delyes==200){
                        echo 200;
                    }elseif($delyes==600){
                        echo 600;
                    }else{
                        echo 404;
                    }


                }else{
                    echo 500;
                }
                

            }
        }else{
            echo 500;
        }
    }
}
?>