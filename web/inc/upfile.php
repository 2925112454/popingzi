<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION
if (empty($ppzusername)){//判断是否登录
    $response = array('code' => 500, 'url' => 'null');
}else{

    if(!isset($_FILES["file"])){
        $response = array('code' => 500, 'url' => 'null');
    }else{
        if(!isset($_POST["typeup"])){
            $_POST["typeup"]="";
        }
        $files=$_FILES["file"];//获取文件
        $typeup=$_POST["typeup"];//上传所属，1为顶部LOGO，2为底部LOGO
        $sizex=1024;//限制文件大小，单位KB，1024KB=1MB
    
        $typex=array('image/jpeg', 'image/png', 'image/gif','image/x-icon','image/bmp','image/webp','image/avif');//允许上传的文件MIME类型，此文件为LOGO上传，所以仅限图片
        $upfileurl='/images/logo/';//上传目录
        $maxFileSize=$sizex*1024; //KB转字节
        $maxFileSizeMB=round($sizex/1024);//KB转MB
        $tmpName = $files['tmp_name']; // 临时文件名 
        $size = $files['size']; // 文件大小
        if ($size < $maxFileSize) {
    
        if ($typeup==1 || $typeup==2 || $typeup==3){//判断是否是规定内的上传所属
            if (isset($files)) {//判断文件是否上传成功
    
                    // 获取文件信息  
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);  
                    $mimeType = finfo_file($finfo, $tmpName); // 文件MIME类型
                    finfo_close($finfo);
                    $ext = strtolower(pathinfo($files['name'], PATHINFO_EXTENSION));//获取文件后缀
                    if ($typeup==1){$name = 'uplogo.'.$ext.'';}else if ($typeup==2){$name = 'butlogo.'.$ext.'';}else{$name = 'ewm.'.$ext.'';}//重命文件名
                    $targetDir = $_SERVER['DOCUMENT_ROOT'].$upfileurl;//创建目录物理路径
                    if(!is_dir($targetDir)){mkdir($targetDir,0777,true);}//判断目录是否存在，不存在则创建目录
                    $targetFile = $upfileurl . basename($name);//获取最新文件名
                    $yesfile=$targetDir . basename($name);//获取最新物理路径
    
                    //判断文件大小是否超过限制
                    if ($size > $maxFileSize) {
                        $response = array('code' => 1, 'url' => 'null', 'size' => $maxFileSizeMB);  
                    }else{
                        if (version_compare(PHP_VERSION, '8.1.0', '>=')) {}else{
                            if ($ext=='avif'){ 
                                $mimeType= 'image/avif';//手动指定mime类型
                            }
                        }

                        if (version_compare(PHP_VERSION, '7.2.0', '>=')) {}else{
                            if ($ext=='webp'){ 
                                $mimeType= 'image/webp';//手动指定mime类型
                            }
                        }
                      //判断文件类型是否允许的类型
                      if(in_array($mimeType, $typex)){
                      
                    include __DIR__.'/conn.php';//连接数据库
                    $rowsql = "select * from ppz_newusername where binary uusername = $ppzusername";//获取登录会员信息
                    $rowretval=mysqli_query($conn,$rowsql);
                    if(mysqli_num_rows($rowretval) !== 1){ 
                        $response = array('code' => 500, 'url' => 'null');
                    }else{
                        $query = $conn->query($rowsql);
                        while($row = $query->fetch_array()){
                        $ustatus=$row['ustatus'];//身份，1普通会员，2为管理员，3为副站长，4为站长；此处为LOGO上传操作，仅限站长身份操作
                        }
                        if ($ustatus==4){
    
                            if (move_uploaded_file($tmpName, $yesfile)) {  
                                $response = array('code' => 200, 'url' => $targetFile);
                            } else {  
                                $response = array('code' => 500, 'url' => 'null');  
                            }
                                  
                        }else{
                            $response = array('code' => 500, 'url' => 'null');
                        }
                    }
    
                      }else{
                        $response = array('code' => 0, 'url' => 'null');
                      }
    
                    }
    
    
    
    
            }else{
                $response = array('code' => 500, 'url' => 'null');
            }
    
    
        }else{
            $response = array('code' => 500, 'url' => 'null');
        }
    }else{
        $response = array('code' => 1, 'url' => 'null', 'size' => $maxFileSizeMB);  
    }

    }


}
// 将响应转换为JSON格式并输出  
header('Content-Type: application/json');  
echo json_encode($response);  
?>