<?php
if ($admin==1 && $typeuser==12 && ($allvip==4||$allvip==3||$allvip==2)  && !empty($ppzusername)){
    if(!isset($_GET["t"])){
        $_GET["t"]="";
    }
    $typeid=$_GET["t"];//发布类型，1图文，2相册，3视频
    if ($typeid==1 || $typeid==2 || $typeid==3 || $typeid==4 || $typeid==5){
        $typeyes=$typeid;
    }else{
        $typeyes=0;
    }
    if($typeyes==1){
        echo '<script src="/style/tinymce/tinymce.min.js"></script><script src="/style/tinymce/index.js"></script>';
        include $_SERVER['DOCUMENT_ROOT'].'/user/admin/inc/rowword.php';//发布图文
    }else if($typeyes==2){
        echo '<script src="/style/tinymce/tinymce.min.js"></script><script src="/style/tinymce/indeximg.js"></script>';
        include $_SERVER['DOCUMENT_ROOT'].'/user/admin/inc/rowimage.php';//发布相册
    }else if($typeyes==3){
        echo '<script src="/style/tinymce/tinymce.min.js"></script><script src="/style/tinymce/indeximg.js"></script>';
        include $_SERVER['DOCUMENT_ROOT'].'/user/admin/inc/rowvideo.php';//发布视频
    }else if($typeyes==4){
        echo '<script src="/style/tinymce/tinymce.min.js"></script><script src="/style/tinymce/index.js"></script>';
        include $_SERVER['DOCUMENT_ROOT'].'/user/admin/inc/rowwordtow.php';//发布公告
    }else if($typeyes==5){
        include $_SERVER['DOCUMENT_ROOT'].'/user/admin/inc/rowuser.php';//发布私信
    }else{
        echo '
        <div class="user-h1">选择发布内容<span class="ifrowdiv-text"><b>文章：</b>图文、相册、视频均为文章内容；<b>公告：</b>平台公告,完全公开；<b>私信：</b>站内消息、通知，可发送给所有会员或指定会员。</span></div>
        <div class="ifrowdiv">
            <a href="popingzi.php?type=12&t=1"><i class="fa fa-file-word-o"></i>发布图文<span><i class="fa fa-angle-double-right"></i></span></a>
            <a href="popingzi.php?type=12&t=2"><i class="fa fa-file-image-o"></i>发布相册<span><i class="fa fa-angle-double-right"></i></span></a>
            <a href="popingzi.php?type=12&t=3"><i class="fa fa-file-video-o"></i>发布视频<span><i class="fa fa-angle-double-right"></i></span></a>
            <a href="popingzi.php?type=12&t=4"><i class="fa fa-file-text-o"></i>平台公告<span><i class="fa fa-angle-double-right"></i></span></a>
            <a href="popingzi.php?type=12&t=5"><i class="fa fa-commenting"></i>站内私信<span><i class="fa fa-angle-double-right"></i></span></a>
        </div>
        ';

    }
    
    if ($typeid==1 || $typeid==2 || $typeid==3 || $typeid==4 || $typeid==5){
        echo '<script src="/style/js/video.js" type="text/javascript"></script>';
    }
}else{
    echo "请勿胡搞！";
}
?>