<?php
if ($kiydtaghdjagd==1){
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
  include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
  $codeset_alert='';
  if (empty($ppzusername)){
//获取注册配置
$regifindex_sql = "SELECT * FROM ppz_regif WHERE id=1";
$regifindex_result = mysqli_query($conn,$regifindex_sql);
$regifindexsize = mysqli_num_rows($regifindex_result);
if($regifindexsize==1){
  while($regifindex_row = mysqli_fetch_assoc($regifindex_result)){
    $indexregif=$regifindex_row['regif'];//注册状态：1开启，2关闭
    $indexregoff=$regifindex_row['regoff'];//注册方式：1开放注册，2邀请码注册
    $indexregtext=$regifindex_row['regtext'];//注册协议
  }

if($indexregoff==1){
    $offtextreg="";
  }else{
    $codeset_text="";//邀请码说明
    $codeset_rmb=0;//邀请码价格
    $codeset_url="";//邀请码购买地址
    if($indexregoff==2){
      $codeset_sql = "SELECT * FROM ppz_codeset WHERE setid=1";
      $codeset_result = mysqli_query($conn,$codeset_sql);
      if(mysqli_num_rows($codeset_result)==1){
        while($codeset_row = mysqli_fetch_assoc($codeset_result)){
          $codeset_text=trim($codeset_row['settext']);//邀请码说明
          $codeset_rmb=trim($codeset_row['setrmb']);//邀请码价格
          $codeset_url=trim($codeset_row['seturl']);//邀请码购买地址
        }
      }
    }
    if(!empty($codeset_text)){
      $codeset_text_new = nl2br(htmlspecialchars($codeset_text));
    }else{
      $codeset_text_new='';
    }
    if(empty($codeset_rmb)||$codeset_rmb<0){
      $codeset_rmb_new='';
    }else{
      $codeset_rmb_new=' ¥'.$codeset_rmb.'';
    }
    
    if(!empty($codeset_url)){
      if(!empty($codeset_text)){
        $codeset_urlxx='<a class="yqmalert" id="yqmalertinfo">获取邀请码'.$codeset_rmb_new.'</a>';
        $codeset_alert='<dialog id="yqm_dialog"><button id="yqm_zcxx"><i class="fa fa-times"></i></button><h3>关于邀请码的须知</h3><div class="yqm_text">'.$codeset_text_new.'</div><a class="gourl" href="'.$codeset_url.'" target="_blank">我已阅读并知晓，确认前往获取邀请码</a></dialog><script src="/style/js/yqmalert.js" type="text/javascript"></script>';
      }else{
        $codeset_urlxx='<a class="yqmalert" href="'.$codeset_url.'" target="_blank">获取邀请码'.$codeset_rmb_new.'</a>';
        $codeset_alert='';
      }
      
    }else{
      $codeset_urlxx='';
      $codeset_alert='';
    }
    $offtextreg='<div class="input-all"><label class="label-sign" for="newyqm"><input type="text" id="newyqm" name="newyqm" required/>  <span><b class="yaoqingma-b">邀请码'.$codeset_urlxx.'</b></span></label> </div>';
  }

}

  echo '
  <!--登录-->
  <dialog id="Signinlog">
  <div class="Signinlog-div"><b>安全登录</b></div>
  <span id="logx" class="x"></span>
      <form id="Signinlogform" method="post">  
        <div class="input-all"><label class="label-sign" for="username"><input maxlength="11" type="text" id="username" name="username" required />  <span><b>账号</b></span> </label> </div>
        <div class="input-all"><label class="label-sign" for="password"><input type="password" id="password" name="password" required /><span><b>密码</b></span> <a onclick="passwordeye()" id="login-eye-a"> <div class="login-eye"  ><i class="fa fa-eye-slash"></i></div></a></label>   </div>
        <input class="sign-inp" type="submit" value="登录" id="logbut"/>
        <div class="sign-txt"><a href="/user/loginx.php">忘记密码？</a> <a id="loga">注册新账号</a></div>  
      </form>
      <button id="dlxx"><i class="fa fa-times"></i></button>
  </dialog>
  
  <!--注册-->
  <dialog id="Signuplog">
  <div class="Signinlog-div"><b>快速注册</b></div>
  <span id="x" class="x"></span>';
if ($indexregif==1){
  echo '<form  id="Signuplogform" method="post">  
      <div class="input-all"><label class="label-sign" for="newname"><input maxlength="12" type="text" id="newname" name="newname" required />  <span><b>昵称</b></span> </label> </div>
        <div class="input-all"><label class="label-sign" for="newusername"><input maxlength="11" type="text" id="newusername" name="newusername" required />  <span><b>账号</b></span> </label> </div>
        <div class="input-all"><label class="label-sign" for="newpassword"><input type="password" id="newpassword" name="newpassword" required /><span><b>密码</b></span><a onclick="newpasswordeye()" id="newlogin-eye-a"> <div class="login-eye"  ><i id="eyei" class="fa fa-eye-slash"></i></div></a> </label>   </div>
        <div class="input-all"><label class="label-sign" for="newemail"><input type="email" id="newemail" name="newemail" required />  <span><b>电子邮箱</b></span> </label> </div>'.$offtextreg.'
        <div class="input-all"><label class="label-sign yzm" for="newyzm"><input maxlength="4" type="text" id="newyzm" name="newyzm" required />  <span><b>验证码</b></span> <img id="captcha" onclick="this.src=\'/inc/captcha.php?\'+Math.random()" src="/inc/captcha.php" alt="验证码"  />  </label> </div>
        <input class="sign-inp" type="submit" name="dosubmit" value="注册" id="newbut" />
        <div class="sign-txt"><a href="/user/regtxt.php" target="_blank" id="textvip">注册即表示同意本站《注册协议》</a> <a id="newa">登录</a></div>  
      </form>
      <button id="zcxx"><i class="fa fa-times"></i></button>';
      echo $codeset_alert;
}else{
  echo '<form  id="Signuplogform">  
      <div class="no-reg"><i class="fa fa-exclamation-triangle"></i>注册功能暂未开放</div>
    <div class="sign-txt"><a href="/user/regtxt.php" target="_blank" id="textvip">注册即表示同意本站《注册协议》</a> <a id="newa">登录</a></div>  
  </form>
  <button id="zcxx"><i class="fa fa-times"></i></button>';
}
  echo '</dialog>
  
  <script>
  function captchaimg() {  
      var imgc = document.getElementById("captcha");  
      if (imgc) {  
          // img元素存在,可以进行后续操作  
          imgc.click();  // 点击图片,触发点击事件  
      } 
  };
  </script>
  ';
  }else{
  echo '<script>alert("请勿乱搞！");</script>';
  };
}
?>