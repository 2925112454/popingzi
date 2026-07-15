<?php
include $_SERVER['DOCUMENT_ROOT'].'/inc/inc.php';//通用
if (empty($ppzusername) || !isset($ppzusername)){
?>
<!DOCTYPE html>
<html lang="zh-CN" translate="no">
<head>
<meta charset="utf-8">
<title>找回密码 - <?php echo $webtext;?>丨<?php echo $webby;?></title>
<meta name="keywords" content="<?php echo $webpass;?>" />
<meta name="description" content="<?php echo $webvar;?>" />
<link rel="icon" href="/favicon.ico"/>
<?php include $_SERVER['DOCUMENT_ROOT'].'/inc/style.php';?>
<link type="text/css" rel="stylesheet" href="/style/css/font-awesome-4.7.0/css/font-awesome.min.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="/style/js/jquery-3.5.1.min.js" type="text/javascript"></script>
<script src="/style/js/input.js" type="text/javascript"></script>
<script src="/style/js/alert.js" type="text/javascript"></script>
</head>
<body>
<?php
include $_SERVER['DOCUMENT_ROOT'].'/inc/header.php';//头部
if(!isset($_SESSION['steps'])){
  $_SESSION["steps"] = 0;
}
if(!isset($_SESSION['stepsname'])){
  $_SESSION["stepsname"] = "";
}
if(!isset($_SESSION['stepstime'])){
  $_SESSION["stepstime"] = 0;
}
$passt= $_SESSION["steps"];//获取操作步骤,1或空为步骤一，2为步骤二，3为步骤三，4为步骤四
$passtname=$_SESSION["stepsname"];//获取找回密码的账号
$passttime=$_SESSION["stepstime"];//操作时间
$passtnowtime=time();//当前时间
$sumpassttime=$passtnowtime-$passttime;//计算时间差
$djstimex=300 - $sumpassttime;//5分钟(300秒)减去时间差，则是倒计时开始时间
//对比当前时间和操作时间是否超过5分钟
if (!empty($passttime) && $sumpassttime > 300){
  unset($_SESSION['steps']);
  unset($_SESSION['stepsname']);
  unset($_SESSION['stepstime']);
  $past=1; 
}else{

  if (empty($passt) || !isset($passt) || $passt < 1 || $passt > 4 || !is_numeric($passt) || empty($passtname) || !is_numeric($passtname)){
    $past=1; 
    unset($_SESSION['steps']);
    unset($_SESSION['stepsname']);
    unset($_SESSION['stepstime']);
  }else if($passt==2){
    $past=2; 
  }else if($passt==3){
    $past=3; 
  }else if($passt==4){
    $past=4; 
  }else{
    $past=1;
    unset($_SESSION['steps']);
    unset($_SESSION['stepsname']);
    unset($_SESSION['stepstime']);
  }


}


if ($past == 1){
  $style="25%";
  $btext="请输入您的账号";//对应标题
  $htmlps=' <form id="passxform" method="post">
  <input id="loginxinp" type="text" name="usert" class="usert"  maxlength="40" required/>
  <button id="logxbut">下一步</button>
</form>
<script src="/style/js/loginx.js" type="text/javascript"></script>
<div id="djsdiv">操作剩余时间：05:00</div>';
}else if($past == 2){
  $style="50%";
  $btext="请选择您的验证方式";

  //获取会员信息
  $fuusqlfa = "select * from ppz_newusername where binary uusername = $passtname";//获取会员
  $fuuretvalfa=mysqli_query($conn,$fuusqlfa);
  if(mysqli_num_rows($fuuretvalfa) !== 1){
    unset($_SESSION['steps']);
    unset($_SESSION['stepsname']);
    unset($_SESSION['stepstime']);
    //刷新当前页面
    header("Location: ".$_SERVER['DOCUMENT_ROOT']."/user/loginx.php");
  }else{
    $fuuqueryfa = $conn->query($fuusqlfa);
    while($fuufa = $fuuqueryfa->fetch_array()){
      $utelyesfa = $fuufa['utelyes'];//手机号验证状态，1为未验证，2为已验证
      $uemailyesfa = $fuufa['uemailyes'];//邮箱验证状态，1为未验证，2为已验证
      $uemail = $fuufa['uemail'];//邮箱地址
      $utel = $fuufa['utel'];//手机号码
    };

    //邮箱隐藏
    if (!empty($uemail)){
      function hideEmail($emailxs) {  
        $emailParts = explode('@', $emailxs);  
        $localPart = substr_replace($emailParts[0], '******', 0, 6); // 替换前四个字符为星号  
        $domainPart = $emailParts[1];  
        return $localPart . '@' . $domainPart;  
    }
      $email=hideEmail($uemail);
    }else{
      $email="错误邮箱号";
    }

    //电话隐藏
    if (!empty($utel)){
      if (strlen($utel) >= 7) {  
        // 提取前三位  
        $firstPart = substr($utel, 0, 3);  
        // 提取后四位  
        $lastPart = substr($utel, -4);  
        // 隐藏中间部分，用星号代替  
        $hiddenPart = str_repeat('*', strlen($utel) - 7);  
        // 组合结果  
        $Phone = $firstPart . $hiddenPart . $lastPart;  
    } else {  
         // 前面不显示  
        $firstPart = substr($utel, 0, 0);  
        // 提取后二位  
        $lastPart = substr($utel, -2);  
        // 隐藏中间部分，用星号代替  
        $hiddenPart = str_repeat('*', strlen($utel) - 2);  
        // 组合结果  
        $Phone = $firstPart . $hiddenPart . $lastPart;  
    }
    }else{
      $Phone = "错误手机号";
    }

    if ($utelyesfa==2&&$uemailyesfa==2){
        $checkedtel="";
        $checkedemail="checked";
    }else{
      if ($utelyesfa==2){
        $checkedtel="checked";
        $checkedemail="";
      }else{
        $checkedemail="checked";
        $checkedtel="";
      }
    }

    if ($utelyesfa==2){
      $htmltel='<div class="passtel"><input type="radio" name="passtel" value="tel" id="tel" '.$checkedtel.' /><label class="nocopy" for="tel">手机验证('.$Phone.')</label></div>';
    }else{
      $htmltel="";
    }

    if ($uemailyesfa==2){
      $htmlemail='<div class="passemail"><input type="radio" name="passtel" value="email" id="email" '.$checkedemail.'/><label class="nocopy" for="email">邮箱验证('.$email.')</label></div>';
    }else{
      $htmlemail="";
    }
    $htmlps=' <form id="passxformtel" method="post">
    '.$htmlemail.'
    '.$htmltel.'
    <button id="nextbut">下一步</button>
    </form>
    <div id="djsdiv"></div>
    <script type="text/javascript">var djstime = '.$djstimex.';</script>
    <script src="/style/js/timeout.js" type="text/javascript"></script>
    <script src="/style/js/emailpost.js" type="text/javascript"></script>
    ';
  }


}else if($past == 3){
  $style="75%";
  $btext="请输入您接收到的验证码";
  $htmlps='  <form id="pascode" method="post">
  <input id="codeinput" type="text" class="usert" maxlength="6" required/>
  <button id="codebut">确定</button>
</form>
    <div id="djsdiv"></div>
    <script type="text/javascript">var djstime = '.$djstimex.';</script>
    <script src="/style/js/timeout.js" type="text/javascript"></script>
    <script src="/style/js/codepost.js" type="text/javascript"></script>
      ';
}else if($past == 4){
  $style="100%";
  $btext="请设置您的新密码";
  $htmlps='  <form id="newpass" method="post">
  <div class="input-code"><input id="newpassinput" placeholder="请输入新密码" type="password" name="newpass1" class="usert" required /><i class="fa fa-unlock-alt" ></i></div>
  <div class="input-code"><input id="newpassinput2" placeholder="请确认新密码" type="password" name="newpass2" class="usert" required /><i class="fa fa-lock" ></i></div>
  <button id="newpassbut">确定</button>
</form>
    <div id="djsdiv"></div>
    <script type="text/javascript">var djstime = '.$djstimex.';</script>
    <script src="/style/js/timeout.js" type="text/javascript"></script>
    <script src="/style/js/newpass.js" type="text/javascript"></script>
      ';
}else{
  $style="25%";
  $btext="请输入您的账号";
  $htmlps=' <form id="passxform" method="post">
  <input id="loginxinp" type="text" name="usert" class="usert"  maxlength="40" required/>
  <button id="logxbut">确定</button>
</form>
<script src="/style/js/loginx.js" type="text/javascript"></script>';
}
?>
<div class="body-div">

  <div class="passx">
    <div class="passx-top">
      <div class="passx-top-hr">
      <div class="passx-top-hrx"><div class="passx-top-hrxl" style="width: <?php echo $style;?>;"></div></div>
              <div class="passx-top-rel">
              <div class="passx-top-box"><div class="passx-top-boxr boxhover"></div><div class="passx-top-text txthover">第一步：输入账号</div></div>
                <div class="passx-top-box"><div class="passx-top-boxr <?php if  ($past == 4 || $past == 3 || $past == 2){ echo 'boxhover';} ?>"></div><div class="passx-top-text <?php if  ($past == 4 || $past == 3 || $past == 2){ echo 'txthover';} ?>">第二步：选择验证方式</div></div>
                <div class="passx-top-box"><div class="passx-top-boxr <?php if  ($past == 4 || $past == 3){ echo 'boxhover';} ?>"></div><div class="passx-top-text <?php if  ($past == 4 || $past == 3){ echo 'txthover';} ?>">第三步：输入验证码</div></div>
                <div class="passx-top-box"><div class="passx-top-boxr <?php if  ($past == 4){ echo 'boxhover';} ?>"></div><div class="passx-top-text <?php if  ($past == 4){ echo 'txthover';} ?>">第四步：设置新密码</div></div>
              </div>
      </div>
    </div>
    <div class="passx-body">
      <b class="nocopy"><?php echo $btext;?></b>
       <?php echo $htmlps;?>
    </div>
  </div>


</div>
<?php 
include $_SERVER['DOCUMENT_ROOT'].'/inc/footer.php';
if (empty($ppzusername)){ echo '<script src="/style/js/login.js" type="text/javascript"></script>';} 
?>
<?php @include $_SERVER['DOCUMENT_ROOT'].'/wap/index.php';?>
</body>
</html>
<?php  
}else{
  header("HTTP/1.1 404 Not Found");  
  header("Status: 404 Not Found");
  echo "<script>location.href='/';</script>";
  exit;
} ?>