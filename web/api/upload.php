<?php
header('Content-Type: application/json');
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
if (empty($ppzusername)){//判断是否登录
    $data = array('errno' => 1, 'message' => '<font>(｡ŏ_ŏ)</font> 错误操作！');
}else{

                 include $_SERVER['DOCUMENT_ROOT'].'/inc/conn.php';//链接数据库
                $rowsql = "select * from ppz_newusername where binary uusername = $ppzusername";//获取登录会员信息
                $rowretval=mysqli_query($conn,$rowsql);
                if(mysqli_num_rows($rowretval) !== 1){ 
                    $response = array('errno' => 1, 'message' => '<font>(｡ŏ_ŏ)</font> 错误操作！');
                }else{
                    $query = $conn->query($rowsql);
                    while($row = $query->fetch_array()){
                        $ustatus=$row['ustatus'];//身份，1普通会员，2为管理员，3为副站长，4为站长；
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
   
                        $daytime=date("Ymd",time());//获取当前日期：年月日
                        if(!isset($_FILES["images"])){
                            $data = array('errno' => 1, 'message' => '<font>(｡ŏ_ŏ)</font> 错误操作！');
                            exit;
                        }
                        $files=$_FILES["images"];//获取文件
                        $sizex=1024;//限制文件大小，单位KB，1024KB=1MB
                        $typex=array('image/jpeg', 'image/png', 'image/gif','image/x-icon','image/avif','image/webp','application/octet-stream');//允许上传的文件MIME类型，此文件为LOGO上传，所以仅限图片
                        $upfileurl='/upload/'.$daytime.'/';//上传目录
                        $maxFileSize=$sizex*1024; //KB转字节
                        $maxFileSizeMB=round($sizex/1024);//KB转MB
                        $tmpName = $files['tmp_name']; // 临时文件名 
                        $size = $files['size']; // 文件大小
                        $imgname=date("YmdHis",time()).generateRandomString(4);//以当前时间+随机生成的4位数作为文件名

                        if ($size < $maxFileSize) {
                            // 获取文件信息  
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);  
                            $mimeType = finfo_file($finfo, $tmpName); // 文件MIME类型
                            finfo_close($finfo);
                            $ext = strtolower(pathinfo($files['name'], PATHINFO_EXTENSION));//获取文件后缀
                            $targetDir = $_SERVER['DOCUMENT_ROOT'].$upfileurl;//创建目录物理路径
                            if(!is_dir($targetDir)){mkdir($targetDir,0777,true);}//判断目录是否存在，不存在则创建目录
                            $targetFile = $upfileurl . basename($imgname.'.'.$ext);//获取最新文件名
                            $yesfile=$targetDir . basename($imgname.'.'.$ext);//获取最新物理路径

                            if ($ext=='jpg'||$ext=='png'||$ext=='gif'||$ext=='jpeg'||$ext=='bmp'||$ext=='webp'||$ext=='ico'||$ext=='avif'){
                                if(in_array($mimeType, $typex)){
                                    if (move_uploaded_file($tmpName, $yesfile)) {  
                                        $data = array(
                                            'errno' => 0,
                                                'data' => array(
                                                    'url' => $targetFile,
                                                )
                                            );
                                    } else {  
                                        $data = array('errno' => 1, 'message' => '<font>(｡ŏ_ŏ)</font> 上传失败！');
                                    }

                                }else{
                                    $data = array('errno' => 1, 'message' => '<font>(｡ŏ_ŏ)</font> 图片格式错误！');
                                }
            
                            }else{
                                $data = array('errno' => 1, 'message' => '<font>(｡ŏ_ŏ)</font> 图片格式错误！');
                            }
                        }else{
                            $data = array('errno' => 1, 'message' => '<font>(｡ŏ_ŏ)</font> 图片超过1MB了！');
                        }

                    }else{
                        $data = array('errno' => 1, 'message' => '<font>(｡ŏ_ŏ)</font> 错误操作！');
                    }

                }

}
echo json_encode($data);
?>