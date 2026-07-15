<?php
if ($set_off>0&&!empty($set_off)){
    $subject_but='';
    $subject_url='href="/subject/"';
}else{
    $subject_but='<div id="areachartbox">暂未开放</div>';
    $subject_url='';
}
if(!isset($ppzusername)||empty($ppzusername)){
        echo '
            <div class="wapfooter" id="wapfooter">
                <a href="/" class="footer_nav"><i class="fa fa-home" aria-hidden="true"></i>首页</a>
                <a class="footer_nav" id="areachart" '.$subject_url.'><i class="fa fa-coffee" aria-hidden="true"></i>话题'.$subject_but.'</a>
                <a href="javascript:void(0);" onclick="togglePopup()" class="footer_nav up"><i class="fa fa-plus" aria-hidden="true"></i></a>
                <a href="javascript:void(0);" onclick="togglePopup()" class="footer_nav"><i class="fa fa-comments-o" aria-hidden="true"></i>消息</a>
                <a href="javascript:void(0);" onclick="togglePopup()" class="footer_nav"><i class="fa fa-diamond" aria-hidden="true"></i>充值</a>
            </div>
            <script src="/style/js/wap.js" type="text/javascript"></script>
            <script>
                function togglePopup() {
                alert("<font>(,,•́ . •̀,,)</font> 请先登录！");
                }
            </script>
            ';
}else{
    if(isset($mesmunpotxt)&&$mesmunpotxt>0){
        $mesmunpotxt_to_text='<span class="mesmunpotxtto">'.$mesmunpotxt.'</span>';
    }else{
        $mesmunpotxt_to_text='';
    }
    echo '
        <div class="wapfooter" id="wapfooter">
            <a href="/" class="footer_nav"><i class="fa fa-home" aria-hidden="true"></i>首页</a>
            <a class="footer_nav" id="areachart" '.$subject_url.'><i class="fa fa-coffee" aria-hidden="true"></i>话题'.$subject_but.'</a>
            <a href="/user/user.php?type=10" class="footer_nav up"><i class="fa fa-plus" aria-hidden="true"></i></a>
            <a href="/user/message.php" class="footer_nav"><i class="fa fa-comments-o" aria-hidden="true"></i>消息'.$mesmunpotxt_to_text.'</a>
            <a href="/vip/" class="footer_nav"><i class="fa fa-diamond" aria-hidden="true"></i>充值</a>
        </div>
        <script src="/style/js/wap.js" type="text/javascript"></script>
    ';
}
?>
