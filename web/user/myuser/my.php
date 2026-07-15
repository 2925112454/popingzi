<?php
ob_start();
if(!isset($myuser)||empty($myuser)||$myuser!=200||!isset($ppzusername)||empty($ppzusername)||!isset($typeuser)||empty($typeuser)||$typeuser!=2){
    if (!headers_sent()) {
        ob_clean();
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
        header("Location: /");
    } else {
        echo "<script>window.location.href = '/';</script>";
    }
    die();
}

if(empty($uemail)){
    $uemailx="未填写邮箱";
}else{
    $uemailx=$uemail;
}
if(empty($utel)){
    $utelx="未填写手机";
}else{
    $utelx=$utel;
}
if(!empty($utel_yes)&&$utel_yes==2){
    $utelx_text='';
    $utelx_text_icon='<i class="fa fa-check-circle" aria-hidden="true"></i>';
}else{
    $utelx_text='<a id="user_yan_tel" class="no-opacity"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>验证手机</a>';
    $utelx_text_icon='';
}
if(!empty($uemail_yes)&&$uemail_yes==2){
    $uemailx_text='';
    $uemailx_text_icon='<i class="fa fa-check-circle" aria-hidden="true"></i>';
}else{
    $uemailx_text='<a id="user_yan_email" class="no-opacity"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>验证邮箱</a>';
    $uemailx_text_icon='';
}
if(!empty($user_url_http)){
    $user_url_http_go=$user_url_http;
}else{
    $user_url_http_go='未填写网址';
}
if(!empty($user_text_big)){
    $user_text_big_go=$user_text_big;
}else{
    $user_text_big_go='未填写简介';
}
if(!empty($user_sex_if)&&$user_sex_if==1){
    $user_sex_if_go="帅哥";
}else{
    $user_sex_if_go="美女";
}

echo '<div class="user-h1">我的资料</div>';
echo '<div class="padding_15px flex-wrap-column">
<div class="padding_bottom_15px flex-wrap"><div id="user_edit_img" class="user_title_img cursor-pointer" style="background:url('.$uimg .');background-repeat: no-repeat; background-size: 100%;"><div class="user_title_img_hover nocopy">修改头像</div></div></div>
<div class="user_title flex-wrap flex-wrap-cn">ID：<span>'.$uid.'</span></div>
<div class="user_title flex-wrap flex-wrap-cn">账号：<span>'.$uusername.'</span></div>
<div class="user_title flex-wrap flex-wrap-cn">昵称：<span id="user_name" class="cursor-pointer nocopy">'.$uname.'</span><a id="user_a_name" class="position-absolute-right"><i class="fa fa-pencil" aria-hidden="true"></i></a></div>
<div class="user_title flex-wrap flex-wrap-cn">邮箱：<span id="user_email" class="cursor-pointer nocopy">'.$uemailx.$uemailx_text_icon.'</span><a id="user_a_email" class="position-absolute-right"><i class="fa fa-pencil" aria-hidden="true"></i></a>'.$uemailx_text.'</div>
<div class="user_title flex-wrap flex-wrap-cn">手机：<span id="user_tel" class="cursor-pointer nocopy">'.$utelx.$utelx_text_icon.'</span><a id="user_a_tel" class="position-absolute-right"><i class="fa fa-pencil" aria-hidden="true"></i></a>'.$utelx_text.'</div>
<div class="user_title flex-wrap flex-wrap-cn">性别：<span id="user_sex" class="cursor-pointer nocopy">'.$user_sex_if_go.'</span><a id="user_a_sex" class="position-absolute-right"><i class="fa fa-pencil" aria-hidden="true"></i></a></div>
<div class="user_title flex-wrap flex-wrap-cn">网址：<span id="user_url" class="cursor-pointer nocopy">'.$user_url_http_go.'</span><a id="user_a_url" class="position-absolute-right"><i class="fa fa-pencil" aria-hidden="true"></i></a></div>
<div class="user_title flex-wrap flex-wrap-cn">简介：<span id="user_text" class="cursor-pointer nocopy">'.$user_text_big_go.'</span><a id="user_a_text" class="position-absolute-right"><i class="fa fa-pencil" aria-hidden="true"></i></a></div>
<div class="flex-wrap flex-wrap-right"><a id="user_pass">修改密码</a></div>
</div>
<dialog id="user_pass_dialog" class="user_dialog padding_15px">
    <div class="user_dialog_title">修改密码</div>
    <a class="user_dialog_close" id="user_dialog_pass_close"><i class="fa fa-times" aria-hidden="true"></i></a>
    <div class="user_dialog_content">
        <div class="user_dialog_item">旧密码：<input type="password" id="user_pass_y" /></div>
        <div class="user_dialog_item">新密码：<input type="password" id="user_pass_new" /></div>
        <div class="user_dialog_item">确定密码：<input type="password" id="user_pass_new_two" /></div>
        <a class="user_dialog_btn" id="user_pass_but">确定</a>
        <span id="user_dialog_err">错误</span>
    </div>
</dialog>
<dialog id="user_email_dialog" class="user_dialog padding_15px">
    <div class="user_dialog_title">邮箱验证</div>
    <a class="user_dialog_close" id="user_dialog_email_close"><i class="fa fa-times" aria-hidden="true"></i></a>
    <span id="goemailcode" class="goemailcode"></span>
    <div class="user_dialog_content">
        <div class="user_dialog_item">验证码：<input type="text" id="user_email_y" /></div>
        <a class="user_dialog_btn" id="user_email_but">确定</a>
        <span id="user_dialog_email_err">错误</span>
    </div>
</dialog>
<dialog id="user_tel_dialog" class="user_dialog padding_15px">
    <div class="user_dialog_title">手机验证</div>
    <a class="user_dialog_close" id="user_dialog_tel_close"><i class="fa fa-times" aria-hidden="true"></i></a>
    <span id="gotelcode" class="goemailcode"></span>
    <div class="user_dialog_content">
        <div class="user_dialog_item">验证码：<input type="text" id="user_tel_y" /></div>
        <a class="user_dialog_btn" id="user_tel_but">确定</a>
        <span id="user_dialog_tel_err">错误</span>
    </div>
</dialog>
<script src="/style/js/user-my.js" type="text/javascript"></script>
';
ob_end_flush();
?>