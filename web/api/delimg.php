<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
if (empty($ppzusername)){//判断是否登录
    echo 500;
}else{
    include $_SERVER['DOCUMENT_ROOT'].'/inc/conn.php';//链接数据库
    $rowsql = "select * from ppz_newusername where binary uusername = $ppzusername";//获取登录会员信息
    $rowretval=mysqli_query($conn,$rowsql);
    if(mysqli_num_rows($rowretval) !== 1){ 
        echo 500;
    }else{
                    $query = $conn->query($rowsql);
                    while($row = $query->fetch_array()){
                        $ustatus=$row['ustatus'];//身份，1普通会员，2为管理员，3为副站长，4为站长；此处为LOGO上传操作，仅限站长身份操作
                        $uid=$row['uid'];//用户ID
                    }
                    if ($ustatus==4||$ustatus==3||$ustatus==2||$ustatus==1){
                        if(!isset($_POST['url'])){
                            $_POST['url']="";
                        }
                        $url=$_POST['url'];//获取要删除的图片地址
                        if (is_null($url)||$url==""||$url==" "){
                            echo 404;
                        }else{
                            $url=str_replace("../","/",$url); //替换url中的“../”为“/”
                            $urla=$_SERVER['DOCUMENT_ROOT'].$url;//获取要删除的图片绝对路径
                        //判断图片地址开头是否是/upload/或upload/或./upload/或../upload/
                        if (strpos($url, '/upload/') !== false || strpos($url, 'upload/') !== false || strpos($url, './upload/') !== false || strpos($url, '../upload/') !== false) {
                            //判断路径结尾后缀是否是图片后缀
                            if (strpos($url, '.jpg') !== false ||strpos($url, '.webp') !== false || strpos($url, '.png') !== false || strpos($url, '.gif') !== false || strpos($url, '.jpeg') !== false || strpos($url, '.avif') !== false || strpos($url, '.JPG') !== false || strpos($url, '.PNG') !== false || strpos($url, '.GIF') !== false || strpos($url, '.JPEG') !== false|| strpos($url, '.AVIF') !== false) {
                                //获取/后，.jpg前的--里面的内容，如“/upload/20240628/1548784114asadx-1-.jpg”这种路径，只取-1-.jpg里面的1
                                $pattern = '/-([0-9]+)-(?=\.(jpg|png|gif|jpeg|bmp|webp|avif|tiff|svg|...)$)/i';
                                preg_match($pattern, $url, $matches);
                                $id=intval($matches[1]);
                                if($id==$uid){
                                    //判断图片是否存在
                                    if(file_exists($urla)){
                                        unlink($urla);//删除图片
                                        echo 200;
                                    }else{
                                        echo 404;
                                    }

                                }else{
                                    if ($ustatus==1){
                                        echo 400;
                                    }else{
                                        
                                        if(file_exists($urla)){
                                            unlink($urla);//删除图片
                                            echo 200;
                                        }else{
                                            echo 404;
                                        }
                                    }
                                }

                            }else{
                                echo 500;
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