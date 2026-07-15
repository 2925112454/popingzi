<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
session_start(); // 开始 Session 会话
if(!isset($_POST["typepost"])){
    $_POST["typepost"]="";
}
if (!isset($_SESSION["stepsname"])){
    $_SESSION["stepsname"]="";
}
if (!isset($_SESSION["stepstime"])){
    $_SESSION["stepstime"]=0;
}
if (!isset($_SESSION["steps"])){
    $_SESSION["steps"]="";
}
$typepost=trim($_POST["typepost"]);//获取短信类型
$stepsname=$_SESSION["stepsname"];//账号
$stepstime=$_SESSION["stepstime"];//时间
$steps=$_SESSION["steps"];//所处步骤
$nowtime=time();//当前时间
$sumpassttime=$nowtime-$stepstime;//计算时间差
if ((!empty($stepstime)) &&$sumpassttime > 300){
    unset($_SESSION['steps']);
    unset($_SESSION['stepsname']);
    unset($_SESSION['stepstime']);
    echo 505;
  }else{

    if ($steps==2){
        if ($typepost=='email' || $typepost=='tel'){
            include __DIR__.'/conn.php';//链接数据库

            $sql = "select * from ppz_newusername where binary uusername = $stepsname";//查询数据库，判断用户名是否存在
            $retval=mysqli_query($conn,$sql);
            if(mysqli_num_rows($retval) !== 1){	//如果用户名不存在，返回错误代码
                echo 505;
            }else{

                function isValidEmail($email) {  
                    $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';  //判断邮箱格式正则
                    return preg_match($pattern, $email);  
                }

                $query = $conn->query($sql);
                while($row = $query->fetch_array()){
                $utelyes=$row['utelyes'];//手机验证状态，1未验证，2已验证
                $uemailyes=$row['uemailyes'];//邮箱验证状态，1未验证，2已验证
                $uformemail=$row['uformemail'];//临时储存邮箱验证码
                $uformtel=$row['uformtel'];//临时储存手机验证码
                $uban=$row['uban'];//封禁状态，1为正常，反之封禁
                $ustatus=$row['ustatus'];//身份，1普通会员，2为管理员，3为副站长，4为站长；该参数用于限制管理员等特殊身份的线上找回密码
                $email=$row['uemail'];//邮箱号
                $tel=$row['utel'];//手机号
                $uname=$row['uname'];//昵称
                }
            
                if ($uban==1 && $ustatus==1 && ($utelyes==2 || $uemailyes==2)){//判断条件：未被封禁且身份是普通会员且邮箱或手机号已验证过

                  //获取网站配置信息
                  $websql = "select * from ppz_web where binary webid = 1";
                  $retvalweb=mysqli_query($conn,$websql);
                  if(mysqli_num_rows($retvalweb) !== 1){
                    $webname="";//网站主标题
                  }else{
                    $queryweb = $conn->query($websql);
                    while($rowweb = $queryweb->fetch_array()){
                      $webname="[".$rowweb['webtext']."]";//网站主标题
                    }
                  }
                  function generateVerificationCode($length = 6) {  
                    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'; // 包含所有数字及英文字母大小写  
                    $code = '';  
                    $max = strlen($characters) - 1;  
                    for ($i = 0; $i < $length; $i++) {  
                        $rand = mt_rand(0, $max); // 生成一个随机数  
                        $code .= $characters[$rand]; // 从字符集中取出一个字符并添加到验证码中  
                    }  
                    return $code;  
                }
                $verificationCode = generateVerificationCode();// 生成一个 6 位数的验证码  

                    if ($typepost=='email' && $uemailyes==2){
                        if (!is_null($email) && isValidEmail($email)){
                            //将验证码存入“临时储存邮箱验证码”数据库
                            $sqlCode = "UPDATE ppz_newusername SET uformemail = '$verificationCode' WHERE uusername = $stepsname";
                            $retvalCode=mysqli_query($conn,$sqlCode);
                            if (@$retvalCode){
                                $sqlem = "select * from ppz_email where binary id = 1";//查询email配置信息
                                $retvalem=mysqli_query($conn,$sqlem);
                                if(mysqli_num_rows($retvalem) !== 1){
                                    echo 500;
                                }else{
                                    $queryem = $conn->query($sqlem);
                                    while($rowem = $queryem->fetch_array()){
                                        $smtp=$rowem['smtp'];//SMTP服务器地址
                                        $username=$rowem['username'];//SMTP服务器账户
                                        $password=$rowem['password'];//SMTP服务器密码
                                        $diy=$rowem['diy'];//自定义落款
                                        $port=$rowem['port'];//端口
                                        $maile=$rowem['email'];//发件人邮箱,用于接收回复邮件
                                        $mailname=$rowem['name'];//发件人名称
                                        $diyhed=$rowem['diyhed'];//自定义前缀
                                    }
                                    
                                      // 引入PHPMailer的核心文件，切换PHP版本记得修改下面的路径
                                      require_once("phpmailer5.5/PHPMailer.php");
                                      require_once("phpmailer5.5/SMTP.php");
                                      require_once("phpmailer5.5/Exception.php");
                                      $mail = new PHPMailer(true);
                                      try {
                                        $mail->CharSet = 'UTF-8';
                                      // 设置邮件发送的服务
                                        $mail->isSMTP();
                                        $mail->Host       = ''.$smtp.'';   // SMTP服务器地址              
                                        $mail->SMTPAuth   = true;                              
                                        $mail->Username   = ''.$username.''; // SMTP服务器用户名               
                                        $mail->Password   = ''.$password.''; // SMTP服务器密码       
                                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                                        $mail->Port       = $port; //SMTP服务器端口号
                                      // 邮件发送人和接收人
                                        $mail->setFrom(''.$username.'', ''.$mailname.'');    // 发件人地址和名称               
                                        $mail->addAddress(''.$email.'', ''.$uname.'');    // 收件人地址和名称 
                                        $mail->addReplyTo(''.$maile.'', ''.$mailname.'');//回复地址和名称
                                      // 邮件内容
                                        $mail->isHTML(true);                                  
                                        $mail->Subject = ''.$webname.'找回密码验证';//邮件标题
                                        $mail->Body    = ''.$diyhed.'<p>尊敬的会员['.$uname.']，您好！<br/>您正在使用邮箱进行找回密码操作，您的验证码是：<b style="color:#ff5722; font-size:32px;">'.$verificationCode.'</b><br/>验证码5分钟内有效，请尽快验证。<br/>如果这不是您本人的操作，请忽略此邮件；此邮件由系统自动发送，请勿回复。</p>'.$diy.'';//html内容
                                        $mail->AltBody = '尊敬的会员['.$uname.']您好！您正在使用邮箱找回密码，验证码为：'.$verificationCode.'。验证码5分钟内有效，请尽快验证。如果这不是您本人的操作，请忽略此邮件。此邮件由系统自动发送，请勿回复。';//不支持html所显示内容
                                      /*添加附件，需要则去掉注释
                                        $mail->addAttachment('/tmp/image.jpg');
                                      */
                                        $mail->send();
                                        echo 200;//发送成功
                                        $_SESSION["steps"] = 3;//步骤
                                      } catch (Exception $e) {
                                        echo 600;//发送失败
                                        echo $e->getMessage();//输出错误信息
                                      }
                                }


                            }else{
                                echo 500;
                            }
                            
                        }else{
                            echo 505;
                        }
                    }else if ($typepost=='tel' && $utelyes==2){

                      function isValidPhoneNumber($phoneNumber) {  
                        $pattern = '/^1[3-9]\d{9}$/';  // 中国大陆手机号的正则表达式：11位数字，以1开头  
                        if (preg_match($pattern, $phoneNumber)) {  
                            return true;  
                        } else {  
                            return false;  
                        }  
                    }

                      if (empty($tel) || !isValidPhoneNumber($tel)){//手机号验证
                        echo 505;
                      }else{

                        //将验证码存入“临时手机验证码”数据库中
                        $sqlCodetel = "UPDATE ppz_newusername SET uformtel = '$verificationCode' WHERE uusername = $stepsname";
                        $retvalCodetel=mysqli_query($conn,$sqlCodetel);
                        if (@$retvalCodetel){

                          $sqltel = "select * from ppz_tel where binary id = 1";//查询email配置信息
                                $retvaltel=mysqli_query($conn,$sqltel);
                                if(mysqli_num_rows($retvaltel) !== 1){
                                    echo 500;
                                }else{
                                  $querytel = $conn->query($sqltel);
                                    while($rowtel = $querytel->fetch_array()){
                                        $apiname=$rowtel['apiname'];//短信宝账号
                                        $apikey=$rowtel['apikey'];//短信宝KEY
                                        $apidiy=$rowtel['apidiy'];//短信自定义签名
                                        $apibody=$rowtel['apibody'];//自定义落款内容
                                    }
                                    
                        $statusStr = array(
                          "0" => 200,//发送成功
                          "-1" => 500,//参数不全
                          "-2" => 500,//服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！
                          "30" => 500,//密码错误
                          "40" => 500,//账号不存在
                          "41" => 500,//余额不足
                          "42" => 500,//帐户已过期
                          "43" => 500,//IP地址限制
                          "50" => 500,//内容含有敏感词
                          "51" => 500,//手机号不存在
                          );
                          $smsapi ="https://api.smsbao.com/";//短信宝API地址
                          $user = "". $apiname.""; //短信平台帐号
                          $pass = "". $apikey.""; //短信平台API KEY
                          $content = "".$apidiy."亲爱的『".$uname."』，您的验证码是".$verificationCode."；有效期为5分钟，请尽快验证。".$apibody."";
                          $phone = $tel;//接收者手机号
                          $sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
                          $result =file_get_contents($sendurl) ;
                          if ($result==0 && !is_null($result)){
                            if ($statusStr[$result]==200){
                              $_SESSION["steps"] = 3;//步骤
                              echo 200;
                            }else{
                              echo $statusStr[$result];
                            }
                          }else{
                            echo $statusStr[$result];
                          }
                                }

                        }else{
                          echo 500;
                        }

                      }                    
                    }else{
                        echo 505;
                    }

                }else{
                  echo 505;
                }
                
            }

//关闭数据库
mysqli_close($conn);
        }else{
            echo 505;
        }
    }else{
        echo 505;
    }

  }
?>