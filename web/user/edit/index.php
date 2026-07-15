<?php
// 将响应转换为JSON格式并输出  
header('Content-Type: application/json');
session_start(); // 开始 Session 会话
ob_start();
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION
if (empty($ppzusername)){//判断是否登录
    $response = array('code' => 500, 'msg' => '错误操作！');
    echo json_encode($response);
}else{
                include $_SERVER['DOCUMENT_ROOT'].'/inc/conn.php';//连接数据库
                $rowsql = "select * from ppz_newusername where binary uusername = $ppzusername";//获取登录会员信息
                $rowretval=mysqli_query($conn,$rowsql);
                if(mysqli_num_rows($rowretval) !== 1){ 
                    $response = array('code' => 500, 'msg' => '错误操作！');
                    echo json_encode($response);
                }else{
                    $query = $conn->query($rowsql);
                    while($row = $query->fetch_array()){
                        $uid=$row['uid'];
                        $uname = $row['uname'];//昵称
                        $uimg = $row['uimg'];//头像
                        $upass = $row['upass'];//密码
                        $uemail = $row['uemail'];//邮箱
                        $utel = $row['utel'];//手机
                        $usex = $row['usex'];//性别
                        $uurl = $row['uurl'];//网址
                        $utelyes = $row['utelyes'];//手机验证状态
                        $uemailyes = $row['uemailyes'];//邮箱验证状态
                        $upersonal = $row['upersonal'];//简介
                    }
                    $value="";
                    $type="";
                    if(!empty($_POST['value'])&&isset($_POST['value'])){
                        $value=trim($_POST['value']);
                    }
                    if(!empty($_POST['type'])&&isset($_POST['type'])){
                        $type=trim($_POST['type']);
                    }
                    if(!empty($type)){
                        if($type=='name'){
                            $value=htmlspecialchars($value);//过滤html标签
                            if(empty($value)){
                                $response = array('code' => 500, 'msg' => '昵称不能为空！');
                                echo json_encode($response);
                                exit;
                            }

                            if(mb_strlen($value,'utf-8')>12){
                                $response = array('code' => 500, 'msg' => '昵称不能超过12字！');
                                echo json_encode($response);
                                exit;
                            }

                            if($uname==$value){
                                $response = array('code' => 500, 'msg' => '昵称未修改！');
                                echo json_encode($response);
                                exit;
                            }

                            if($ppzusername==$value){
                                $response = array('code' => 500, 'msg' => '昵称不能和账号一样！');
                                echo json_encode($response);
                                exit;
                            }

                            $sql = "update ppz_newusername set uname='$value' where uid='$uid'";
                            $retval = mysqli_query($conn,$sql);
                            if(!$retval){
                                $response = array('code' => 500, 'msg' => '昵称修改失败！');
                                echo json_encode($response);
                            }else{
                                $response = array('code' => 200, 'msg' => '');
                                echo json_encode($response);
                            }

                        }elseif($type=='email'){
                            $value=htmlspecialchars($value);//过滤html标签
                            if(empty($value)){
                                $response = array('code' => 500, 'msg' => '邮箱不能为空！');
                                echo json_encode($response);
                                exit;
                            }
                            if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
                                $response = array('code' => 500, 'msg' => '邮箱格式错误！');
                                echo json_encode($response);
                                exit;
                            }
                            if($uemail==$value){
                                $response = array('code' => 500, 'msg' => '邮箱未修改！');
                                echo json_encode($response);
                                exit;
                            }

                            $ifsql="select uemail,uid,uemailyes from ppz_newusername where uemail='$value' and uid!=$uid and uemailyes=2";
                            $ifretval=mysqli_query($conn,$ifsql);
                            if(mysqli_num_rows($ifretval) > 0){
                                $response = array('code' => 500, 'msg' => '其它会员已验证该邮箱！');
                                echo json_encode($response);
                                exit;
                            }
                            $sql = "update ppz_newusername set uemail='$value',uemailyes=1 where uid='$uid'";
                            $result = $conn->query($sql);
                            if ($result) {
                                $response = array('code' => 200, 'msg' => '');
                                echo json_encode($response);
                            } else {
                                $response = array('code' => 500, 'msg' => '邮箱修改失败！');
                                echo json_encode($response);
                            }
                        }elseif($type=='tel'){
                            function checkPhoneValid(string $phone): bool
                                {
                                    // 1. 先判断是否为 11 位纯数字
                                    if (!preg_match('/^1\d{10}$/', $phone)) {
                                        return false;
                                    }
                                    // 2. 三大运营商 正规号段
                                    $validPrefix = [
                                        '130','131','132','133','134','135','136','137','138','139',
                                        '145','146','147','148','149',
                                        '150','151','152','153','155','156','157','158','159',
                                        '165','166','172',//虚拟运营商
                                        '173','175','176','177','178',
                                        '180','181','182','183','184','185','186','187','188','189',
                                        '190','191','192','193','195','196','197','198','199',
                                    ];
                                    // 3. 截取前 3 位判断是否在合法号段内
                                    $prefix = substr($phone, 0, 3);
                                    return in_array($prefix, $validPrefix);
                             }
                            $value=htmlspecialchars($value);//过滤html标签
                            if(!empty($value)){
                                //验证手机格式
                                if(!checkPhoneValid($value)){
                                    $response = array('code' => 500, 'msg' => '号码不支持，请更换！');
                                    echo json_encode($response);
                                    exit;
                                }
                            }
                            if($utel==$value){
                                $response = array('code' => 500, 'msg' => '手机未修改！');
                                echo json_encode($response);
                                exit;
                            }
                            $ifsql="select utel,uid,utelyes from ppz_newusername where utel='$value' and uid!=$uid and utelyes=2";
                            $ifretval=mysqli_query($conn,$ifsql);
                            if(mysqli_num_rows($ifretval) > 0){
                                $response = array('code' => 500, 'msg' => '其它会员已验证该手机！');
                                echo json_encode($response);
                                exit;
                            }
                            $sql = "update ppz_newusername set utel='$value',utelyes=1 where uid='$uid'";
                            $result = $conn->query($sql);
                            if ($result) {
                                $response = array('code' => 200, 'msg' => '');
                                echo json_encode($response);
                            } else {
                                $response = array('code' => 500, 'msg' => '手机修改失败！');
                                echo json_encode($response);
                            }
                        }elseif ($type == 'url') {
                            $value=htmlspecialchars($value);//过滤html标签
                            if(!empty($value)){
                                //判断是不是有效网址
                                if(!preg_match('/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i',$value)){
                                    $response = array('code' => 500, 'msg' => '网址格式错误！');
                                    echo json_encode($response);
                                    exit;
                                }
                            }
                            if($uurl==$value){
                                $response = array('code' => 500, 'msg' => '网址未修改！');
                                echo json_encode($response);
                                exit;
                            }
                            $sql = "update ppz_newusername set uurl='$value' where uid='$uid'";
                            $result = $conn->query($sql);
                            if ($result) {
                                $response = array('code' => 200, 'img' => '');
                                echo json_encode($response);
                            }else{
                                $response = array('code' => 400, 'msg' => '网址修改失败');
                                echo json_encode($response);
                            }

                        }elseif($type=='text'){
                            $value=htmlspecialchars($value);//过滤html标签
                            if(!empty($value)){
                                if(mb_strlen($value,'utf-8')>120){
                                    $response = array('code' => 500, 'msg' => '简介不能超过120字！');
                                    echo json_encode($response);
                                    exit;
                                }
                            }
                            if($upersonal==$value){
                                $response = array('code' => 500, 'msg' => '简介未修改！');
                                echo json_encode($response);
                                exit;
                            }
                            $sql = "update ppz_newusername set upersonal='$value' where uid='$uid'";
                            $result = $conn->query($sql);
                            if ($result) {
                                $response = array('code' => 200, 'msg' => '');
                                echo json_encode($response);
                            }else{
                                $response = array('code' => 500, 'msg' => '简介修改失败！');
                                echo json_encode($response);
                            }
                        }elseif($type=='sex'){
                            $value=htmlspecialchars($value);//过滤html标签
                            if($value==1||$value==2){
                                if($usex==$value){
                                    $response = array('code' => 500, 'msg' => '性别未修改！');
                                    echo json_encode($response);
                                    exit;
                                }
                                $sql = "update ppz_newusername set usex='$value' where uid='$uid'";
                                $result = $conn->query($sql);
                                if ($result) {
                                    $response = array('code' => 200, 'msg' => '');
                                    echo json_encode($response);
                                }else{
                                    $response = array('code' => 500, 'msg' => '性别修改失败！');
                                    echo json_encode($response);
                                }
                            }else{
                                $response = array('code' => 500, 'msg' => '错误操作！');
                                echo json_encode($response);
                            }
                        }elseif($type=='img'){
                            if(!empty($_FILES['avatar'])&&isset($_FILES['avatar'])){
                                $file  = $_FILES['avatar'];
                                
                                // 文件上传错误检查
                                if ($file['error'] !== UPLOAD_ERR_OK) {
                                    $response = array('code' => 500, 'msg' => '文件上传错误！');
                                    echo json_encode($response);
                                    exit;
                                }
                                
                                $originalName = mb_convert_encoding($file['name'], 'UTF-8', 'UTF-8');// 获取原始文件名并防止乱码
                                $ext = pathinfo($originalName, PATHINFO_EXTENSION);
                                $upurl='/upload/user/';//上传目录
                                $file_name_new =$uid.'.'.$ext;//新文件名称
                                $file_url = $_SERVER['DOCUMENT_ROOT'].$upurl;//上传目录（绝对路径）
                                $file_maxwidth =  240;//图片最大宽高（px）
                                $file_maxsize=100;//图片最大大小（KB）
                                $file_maxsizex =  $file_maxsize * 1024;
                                $allowedExts = ['gif', 'webp'];//允许的后缀
                                $allowedTypes = ['image/gif', 'image/webp'];//允许的mime类型

                                // 只允许图片格式
                                if (!in_array($file['type'], $allowedTypes)) {
                                    $response = array('code' => 500, 'msg' => '文件格式错误！'.$file['type']);
                                    echo json_encode($response);
                                    exit;
                                }

                                //判断图片宽高
                                $imageInfo = getimagesize($file['tmp_name']);
                                if ($imageInfo[0] > $file_maxwidth || $imageInfo[1] > $file_maxwidth) {
                                    $response = array('code' => 500, 'msg' => '图片尺寸过大！');
                                    echo json_encode($response);
                                    exit;
                                }

                                if($originalName!="avatar.webp"&&$originalName!="avatar.gif"){
                                    $response = array('code' => 500, 'msg' => "错误操作！");
                                    echo json_encode($response);
                                    exit;
                                }

                                if (!in_array($ext, $allowedExts)) {
                                    $response = array('code' => 500, 'msg' => "文件格式错误2！");
                                    echo json_encode($response);
                                    exit;
                                }

                                if ($file['size'] > $file_maxsizex ) {
                                    $response = array('code' => 500, 'msg' => '文件不能超过'.$file_maxsize.'KB！');
                                    echo json_encode($response);
                                    exit;
                                }      

                                if (!file_exists($file_url)) {
                                    mkdir($file_url, 0777, true);//创建目录
                                }

                                if (move_uploaded_file($file['tmp_name'], $file_url.$file_name_new)) {//移动文件
                                    $timestamp = time();
                                    $file_nema=$upurl.$file_name_new."?t=".$timestamp;
                                    $sql = "update ppz_newusername set uimg='$file_nema' where uid='$uid'";
                                    $result = mysqli_query($conn,$sql);
                                    if($result){
                                        $response = array('code' => 200, 'img' => $file_nema);
                                        echo json_encode($response);
                                    }else{
                                        $response = array('code' => 500, 'msg' => '头像修改失败！');
                                        echo json_encode($response);
                                    }
                                }else{
                                   $response = array('code' => 500, 'msg' => '头像修改失败！');
                                    echo json_encode($response);
                                }



                            }else{
                                $response = array('code' => 500, 'msg' => '错误操作！');
                                echo json_encode($response);
                            }
                        }else{
                            $response = array('code' => 500, 'msg' => '错误操作！');
                            echo json_encode($response);
                        }

                    }else{
                        $response = array('code' => 500, 'msg' => '错误操作！');
                        echo json_encode($response);
                    }

                }
}
?>