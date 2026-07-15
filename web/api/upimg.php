<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
if (empty($ppzusername)){//判断是否登录
    header("HTTP/1.1 500 Server Error");
}else{
                include $_SERVER['DOCUMENT_ROOT'].'/inc/conn.php';//链接数据库
                $rowsql = "select * from ppz_newusername where binary uusername = $ppzusername";//获取登录会员信息
                $rowretval=mysqli_query($conn,$rowsql);
                if(mysqli_num_rows($rowretval) !== 1){ 
                    header("HTTP/1.1 500 Server Error");
                }else{
                    $query = $conn->query($rowsql);
                    while($row = $query->fetch_array()){
                        $ustatus=$row['ustatus'];//身份，1普通会员，2为管理员，3为副站长，4为站长；此处为LOGO上传操作，仅限站长身份操作
                        $uid=$row['uid'];
                    }
                    if ($ustatus==4||$ustatus==3||$ustatus==2){
                        function generateRandomString($length = 10) {//生成随机字符串函数
                            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';  
                            $charactersLength = strlen($characters);  
                            $randomString = '';  
                            for ($i = 0; $i < $length; $i++) {  
                                $randomString .= $characters[mt_rand(0, $charactersLength - 1)];  
                            }  
                            return $randomString;  
                        }

                        reset ($_FILES);
                        $temp = current($_FILES);
                        $daytime=date("Ymd",time());//获取当前日期：年月日
                        // if (!isset($temp['file'])) {
                        //     echo json_encode(array('error' => "文件上传失败"));
                        //     exit;
                        // }
                        //$files=$temp["file"];//获取文件
                        $sizex=1024;//限制文件大小，单位KB，1024KB=1MB
                        $maxWidth=2400;//限制图片宽度,最宽1200像素
                        $upfileurl='/upload/'.$daytime.'/';//上传目录
                        $maxFileSize=$sizex*1024; //KB转字节
                        $maxFileSizeMB=round($sizex/1024);//KB转MB
                        $tmpName = $temp['tmp_name']; // 临时文件名 
                        $size = $temp['size']; // 文件大小
                        $imgname=date("YmdHis",time()).generateRandomString(4)."-".$uid."-";//以当前时间+随机生成的4位数+用户ID作为文件名
                        $upimgwidth= getimagesize($tmpName);//获取图片宽度

                        if ($size < $maxFileSize) {
                            // 获取文件信息
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);  
                            $mimeType = finfo_file($finfo, $tmpName); // 文件MIME类型
                            finfo_close($finfo);
                            $ext = strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION));//获取文件后缀
                            $targetDir = $_SERVER['DOCUMENT_ROOT'].$upfileurl;//创建目录物理路径
                            if(!is_dir($targetDir)){mkdir($targetDir,0777,true);}//判断目录是否存在，不存在则创建目录
                            $targetFile = $upfileurl . basename($imgname.'.'.$ext);//获取最新文件名
                            $yesfile=$targetDir . basename($imgname.'.'.$ext);//获取最新物理路径
                            $allowed_extensions = array('jpg', 'png', 'gif', 'jpeg', 'bmp', 'webp', 'ico','avif');//允许上传的文件后缀

                            if (strtolower($ext) === 'webp'|| strtolower($ext) === 'avif') {
                                $typex=array('image/jpeg', 'image/png', 'image/gif','image/x-icon','image/webp','image/avif','application/octet-stream');
                            } else {
                                $typex=array('image/jpeg', 'image/png', 'image/gif','image/x-icon','image/webp','image/avif');
                            }

                            if (in_array($ext, $allowed_extensions)){
                                if(in_array($mimeType, $typex)){//判断文件类型
                                    if( $upimgwidth[0]<=$maxWidth ){

                                             if (move_uploaded_file($tmpName, $yesfile)) {//上传文件
                                                    echo json_encode(array('location' => $targetFile));
                                            } else {  
                                                echo json_encode(array('error' => "文件上传失败"));
                                            }

                                    }else{
                                        //echo json_encode(array('error' => "图片宽度不能超过".$maxWidth."像素"));
                                        //缩小图片宽度至最大宽度限制，高度等比例缩放，最后将缩小后的图片保存到服务器
                                        $img = imagecreatefromstring(file_get_contents($tmpName));
                                        $width = imagesx($img);
                                        $height = imagesy($img);
                                        if (in_array($ext, ['jpg', 'jpeg', 'webp', 'png']) && ($width>$maxWidth)){

                                            $newwidth = $maxWidth;
                                            $newheight = $height*($maxWidth/$width);
                                            $newimg = imagecreatetruecolor($newwidth, $newheight);
                                            // 对于 PNG 图像，设置透明背景
                                            if ($ext == 'png') {  
                                                // 分配一个透明颜色  
                                                $transparent = imagecolorallocatealpha($newimg, 0, 0, 0, 127);  
                                                // 填充背景为透明  
                                                imagefill($newimg, 0, 0, $transparent);  
                                                // 释放颜色
                                                imagecolordeallocate($newimg, $transparent);  
                                            }  
                                            imagecopyresampled($newimg, $img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                                                if ($ext=='png'){
                                                    imagesavealpha($newimg, true); // 启用 alpha blending ;保留PNG透明通道
                                                    if(imagepng($newimg, $yesfile, 9)){
                                                        echo json_encode(array('location' => $targetFile));
                                                    }else{
                                                        echo json_encode(array('error' => "PNG文件上传失败"));
                                                    }                                                
                                                }else if($ext=='jpg'||$ext=='jpeg'){
                                                    if (imagejpeg($newimg, $yesfile, 80)) {//保存图片
                                                        echo json_encode(array('location' => $targetFile));
                                                    } else {  
                                                        echo json_encode(array('error' => "JPG文件上传失败"));
                                                    }
                                                }else if($ext=='webp'){
                                                    if (imagewebp($newimg, $yesfile, 80)) {//保存图片
                                                        echo json_encode(array('location' => $targetFile));
                                                    } else {  
                                                        echo json_encode(array('error' => "WEBP文件上传失败"));
                                                    }
                                                }else{
                                                    echo json_encode(array('error' => "缩小文件处理失败"));
                                                }
                                                imagedestroy($img);  //释放内存
                                                imagedestroy($newimg); //释放内存
                                        }else{


                                                    if (move_uploaded_file($tmpName, $yesfile)) {//上传文件
                                                            echo json_encode(array('location' => $targetFile));
                                                    } else {  
                                                        echo json_encode(array('error' => "文件上传失败"));
                                                    }



                                            

                                        }


                                    }

                                }else{
                                    echo json_encode(array('error' => "文件格式不支持"));
                                }
            
                            }else{
                                echo json_encode(array('error' => "文件格式不支持"));
                            }
                        }else{
                           echo json_encode(array('error' => "文件不能超过".$maxFileSizeMB."MB"));
                        }

                    }else{
                        header("HTTP/1.1 500 Server Error");
                    }

                }
//断开数据库链接
mysqli_close($conn);
}
?>