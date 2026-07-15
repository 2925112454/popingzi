<?php
if ($admin==1 && $typeuser==10 && $allvip==4  && !empty($ppzusername)){
    //获取邮箱配置
    $smtpsql = "SELECT * FROM ppz_email WHERE id=1";
    $smtpresult = $conn->query($smtpsql);
    if ($smtpresult->num_rows > 0) {
        while($smtprow = $smtpresult->fetch_assoc()) {
            $smtp = htmlentities($smtprow["smtp"]);//smtp服务器
            $username = htmlentities($smtprow["username"]);//发件人邮箱
            $password = htmlentities($smtprow["password"]);//密码
            $diy = htmlentities($smtprow["diy"]);//自定义后缀
            $port = htmlentities($smtprow["port"]);//端口
            $email = htmlentities($smtprow["email"]);//邮箱
            $name = htmlentities($smtprow["name"]);//自定义昵称
            $diyhed = htmlentities($smtprow["diyhed"]);//自定义前缀
        }
    }else{
            $smtp = "";
            $username = "";
            $password = "";
            $diy = "";
            $port = "";
            $email = "";
            $name = "";
            $diyhed = "";
    }
    //获取短信配置
    $telsql="SELECT * FROM ppz_tel WHERE id=1";
    $telresult = $conn->query($telsql);
    if ($telresult->num_rows > 0) {
        while($telrow = $telresult->fetch_assoc()) {
            $apiname = htmlentities($telrow["apiname"]);//短信宝账号
            $apikey = htmlentities($telrow["apikey"]);//短信宝key
            $apidiy = htmlentities($telrow["apidiy"]);//自定义签名
            $apibody = htmlentities($telrow["apibody"]);//自定义落款
        }
    }else{
            $apiname = "";
            $apikey = "";
            $apidiy = "";
            $apibody = "";
    }

    echo '
<div class="tabs">  
  <div class="tab active" id="tab1"><div class="tab-title"><i class="fa fa-envelope"></i>邮箱配置</div></div>  
  <div class="tab" id="tab2"><div class="tab-title"><i class="fa fa-comments-o"></i>短信配置</div></div>  
</div> 
<div class="tab-content"> 
    <div class="content active" id="content1">
        <form class="content-form" id="emailform" method="post">
            <div class="content-title">SMTP服务器：<input type="text" name="smtp" value="'.$smtp.'"/><span><b>*</b>比如：smtp.qq.com</span></div>
            <div class="content-title">发件人邮箱：<input type="text" name="smtpemail" value="'.$email.'"/><span><b>*</b>发件人邮箱号</span></div>
            <div class="content-title">发件人账号：<input type="text" name="smtpemailname" value="'.$username.'"/><span><b>*</b>发件人邮箱账号</span></div>
            <div class="content-title">发件人密码：<input type="text" name="smtpemailpass" value="'.$password.'"/><span><b>*</b>发件人邮箱密码</span></div>
            <div class="content-title">发送端口号：<input type="text" name="smtpport" value="'.$port.'"/><span><b>*</b>邮箱服务器端口，默认465</span></div>
            <div class="content-title">自定义昵称：<input type="text" name="smtpname" value="'.$name.'"/><span></div>
            <div class="content-title">自定义前缀：<input type="text" name="smtpdiyhed" value="'.$diyhed.'"/></div>
            <div class="content-title">自定义后缀：<input type="text" name="smtpdiy" value="'.$diy.'"/></div>
            <div class="content-btn-div"><button class="content-btn content-btnleft" id="smtpces">发送测试邮件</button><button class="content-btn" id="smtpbtn">保存配置</button></div>
        </form>
    </div>
    <div class="content" id="content2">
        <div class="telset">验证码免费报备VIP通道模板：<span id="telsetdiy">'.$apidiy.'</span>亲爱的『{user_name}』，您的验证码是{code}。有效期为{time}，请尽快验证。<span id="telsetbody">'.$apibody.'</span></div>
            <form class="content-form" id="telform" method="post">
                <div class="content-title">账号：<input type="text" name="teluser" value="'.$apiname.'"/><span><b>*</b>短信宝账号<a href="//www.smsbao.com/" target="_blank" class="smsbao">前往短信宝获取</a></span></div>
                <div class="content-title">密码：<input type="text" name="telkey" value="'.$apikey.'"/><span><b>*</b>短信宝KEY</span></div>
                <div class="content-title">签名：<input type="text" name="teldiy" value="'.$apidiy.'"/><span><b>*</b>必须用【】包裹签名，不要用【测试】、特殊字符等作为签名。</span></div>
                <div class="content-title">后缀：<input type="text" name="telbody" value="'.$apibody.'"/><span>自定义内容(后缀)</span></div>
                <div class="content-btn-div"><button class="content-btn content-btnleft" id="telces">发送测试短信</button><button class="content-btn content-btnleft" id="telyue">查询余额</button><button class="content-btn" id="telbtn">保存配置</button></div>
            </form>
    </div>
</div>
<script src="/style/js/emailset.js" type="text/javascript"></script>
<script src="/style/js/tab.js" type="text/javascript"></script>
';

}else{
    echo "请勿胡搞！";
}
?>