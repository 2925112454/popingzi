<?php
if ($admin==1 && $typeuser==12 && ($allvip==4||$allvip==3||$allvip==2)  && !is_null($ppzusername) && $typeyes==5){
    if(!isset($_GET["u"])){
        $_GET["u"]="";
    }
    $ue=$_GET["u"];
    if (!is_null($ue)&& $ue!=""){
        $u='value="'.$ue.'"';
    }else{
        $u="";
    }
echo '<div class="user-h1"><span><i class="fa fa-plus-circle"></i>发布私信</span><a href="?type=12"><i class="fa fa-angle-double-left" aria-hidden="true"></i>返回类型</a></div>';
echo '
<div class="newword">
    <form method="post" id="userform">
    <div class="newword-title"><span>收件人：</span><input placeholder="会员账号" style="min-width:48%" type="text" name="user" '.$u.'/><i>* 多账号请用逗号分割；留空则发送给所有会员。</i></div>
        <textarea name="rowtext" id="rowtext" placeholder="请输入私信内容……"></textarea>
        <div class="newword-title3"><button id="newwordsubmit">提交</button></div>
    </form>
    <script src="/style/js/rowuser.js" type="text/javascript"></script>
</div>';
}else{
    echo "请勿胡搞！";
}
?>