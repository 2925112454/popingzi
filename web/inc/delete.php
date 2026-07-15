<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
if(!isset($_POST['did'])){
    $_POST['did']="";
}
$did=trim($_POST['did']);//获取id

if (empty($ppzusername)){
    echo 500; //错误操作
}else{

    if (empty($did)){
        echo 500;
    }else{

        if (!is_numeric($did) || $did <= 0 || strpos($did, '.') !== false || strpos($did, ',') !== false) {
            echo 500;
        }else{

        include __DIR__.'/conn.php';//链接数据库
        $sql = "select * from ppz_row  WHERE binary rowid=$did";//获取文章
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
                     $vuid=$vip['uid'];//获取会员id
                     $ustatus=$vip['ustatus'];//会员身份，1普通会员，2为管理员，3为副站长，4为站长
                    }

                    if ($ustatus==2||$ustatus==3||$ustatus==4){
                        $yes=200;
                    }else{
                        $yes=0;
                    };

                        $query = $conn->query($sql);
                        while($row = $query->fetch_array()){
                            $rowadmin=$row['rowadmin'];//获取文章发布者id
                            $rowimg=$row['rowimg'];//封面
                            $rowif=$row['rowif'];//类型：1图文，2相册，3视频
                            $rowbigtext=$row['rowbigtext'];//内容
                        }

                        if ($yes===200){

                            if (!empty($rowimg)){
                                        $rowimage_url = str_replace("../", "/", $rowimg);//将“../”替换为“/”
                                     if(@file_exists($_SERVER['DOCUMENT_ROOT'].$rowimage_url)){ //判断文件是否存在
                                        unlink($_SERVER['DOCUMENT_ROOT'].$rowimage_url);//存在则删除
                                    }
                            };
                            
                            if ($rowif==1){ //图文

                                // 提取图片地址并删除
                            $pattern = '/<img[^>]+src="([^"]+)"/i';
                            preg_match_all($pattern,$rowbigtext, $matches);
                            $image_urls = $matches[1];//转换数组
                            $image_urls = array_unique($image_urls);//去重
                            foreach ($image_urls as $image_url) {
                                $image_url = str_replace("../", "/", $image_url);//将“../”替换为“/”
                                if (@file_exists($_SERVER['DOCUMENT_ROOT'] . $image_url)) {
                                    unlink($_SERVER['DOCUMENT_ROOT'] . $image_url);
                                }
                            }

                            }else if($rowif == 2 || $rowif == 3){ //相册或视频
                                $image_urlsx = explode("|",$rowbigtext);//转换数组
                                for($i=0;$i<count($image_urlsx);$i++){//判断数组内的图片是否是本地连接
                                        if(@file_exists($_SERVER['DOCUMENT_ROOT'].$image_urlsx[$i])){ //判断文件是否存在
                                            unlink($_SERVER['DOCUMENT_ROOT'].$image_urlsx[$i]);
                                        }
                                }
                            }else{
                                echo 500;
                            }

                            if ($rowif == 2 || $rowif == 3 || $rowif == 1){

                            //获取文章下的所有评论
                            $plsql = "select * from ppz_commentary where binary plrowid=$did";
                            $plretval=mysqli_query($conn,$plsql);
                            if (mysqli_num_rows($plretval) > 0){
                                $i = 0; // 初始化索引变量  
                                $array = array(); // 创建空数组

                                $plquery = $conn->query($plsql);

                                while($pl = $plquery->fetch_array()){
                                    $array[$i]=$pl['plid'];
                                    $i++;
                                }
                                $dplsql = "DELETE FROM ppz_commentary WHERE plid IN (".implode(",", $array).")";//删除评论
                                $conn->query($dplsql); // 执行删除操作  

                                $hfsql = "SELECT * FROM ppz_reply WHERE repid IN (".implode(",", $array).")"; //获取评论下的所有回复  
                                $hfretval=mysqli_query($conn,$hfsql);  
                                
                                if (mysqli_num_rows($hfretval) > 0){  
                                    $ix = 0; // 初始化索引变量    
                                    $arrayx = array(); // 创建空数组  
                                    while($hf = $hfretval->fetch_array()){  
                                        $arrayx[$ix]=$hf['repid'];  
                                        $ix++;  
                                    }  
                                
                                    $dhfsql = "DELETE FROM ppz_reply WHERE repid IN (".implode(",", $arrayx).")";//删除回复  
                                    $conn->query($dhfsql); // 执行删除操作  
                                }
                                

                            }
                                $drowsql = "delete from ppz_row where binary rowid=$did";//删除文章

                                if ($conn->query($drowsql) === TRUE) {
                                    echo 200;
                                }else{
                                    echo 500;
                                }
                            

                            }
                            


                        }else{
                            echo 505;
                        }
                    

                }

        }
        mysqli_close($conn);//关闭数据库连接
     }
    }
    
}
?>