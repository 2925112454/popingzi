<!DOCTYPE html>
<html lang="zh-CN" translate="no">
<head>
<?php
include $_SERVER['DOCUMENT_ROOT'].'/inc/inc.php';//链接数据库

if  (isset($_GET["id"])){ 
    $rowidif=$_GET['id'];
}else{
    $rowidif="";
}

if  (isset($_GET["t"])){ 
$one=$_GET['t'];
}else{
    $one="";
}

if ($rowidif == false || empty($rowidif) ||!isset($rowidif) || !is_numeric($rowidif)){
    $rowid="";
}else{
    $rowid=$rowidif;
}

$leftbutsc="";
$leftbutsct="收藏";

//判断id是否是数字且是否不为空
if(is_numeric($rowid) && !empty($rowid) && $rowid>0){
$id=$rowid;//获取文章id


if (empty($ppzusername) || $ppzusername == false || !isset($ppzusername) ||!is_numeric($ppzusername)){
    $sclogin='onclick="loginFunction()"';
    $sclogintxt='收藏';
    $sessionyes=false;
    $wkhref='id="alertLink"';
}else{
    $wkhref='href="/user/user.php?type=9"';
$sessionyes=true;
    $vipsql = "select * from ppz_newusername  WHERE binary uusername=$ppzusername";//获取登录会员信息
    $vipretval=mysqli_query($conn,$vipsql);
    if(mysqli_num_rows($vipretval) !== 1){ 
    // 跳转到另一个页面  
    function redirect($url){  
    // 确保没有输出到浏览器
    if (ob_get_contents()) {  
        ob_clean();  
    }  
    // 设置重定向头  
    header('Location: ' . $url);  
    exit;  
    } 
    redirect('/inc/loginout.php'); 
    }else{

        $vipquery = $conn->query($vipsql);
        while($vip = $vipquery->fetch_array()){
                $ucollect=$vip['ucollect'];//获取用户收藏列表
        }

        $ucollectyes=explode('|',$ucollect);

        if(in_array($id,$ucollectyes) &&  ($ucollect!=="" || $ucollect!==null || $ucollect!==false || !is_null($ucollect)|| !empty($ucollect) ||!isset($ucollect))){ //判断用户是否收藏该文章,已收藏则显示收藏
            $sclogin='id="rowsc"';
            $scclass='yes';
            $scscript='<script type="text/javascript">var rid='.$id.';</script><script src="/style/js/rowsc.js" type="text/javascript"></script>';
            $sclogintxt='已收藏';
            $leftbutsc="scyes";
            $leftbutsct="取消收藏";
        }else{
            $sclogin='id="rowsc"';
            $scscript='<script type="text/javascript">var rid='.$id.';</script><script src="/style/js/rowsc.js" type="text/javascript"></script>';
            $sclogintxt='收藏';
            $leftbutsc="";
            $leftbutsct="收藏";
        }


    }

}

$rowsql = "select * from ppz_row  WHERE rowyes = 4 AND rowid=$id";//获取文章数据库表
$rowretval=mysqli_query($conn,$rowsql);

            if(mysqli_num_rows($rowretval) <= 0){ 
                $rownull=1; //没有文章
                $wvip="";
                $wd="";
                $wtag="";
                $sessionyes="";
                $wtxt="文章不存在";
                $wrow="";
                $rowimg="";
            }else{
                $rownull=2;//有文章

                            $rowquery = $conn->query($rowsql);
                            while($lrow = $rowquery->fetch_array()){
                                    $wid=$lrow['rowid'];//文章id
                                    $wtxt=$lrow['rowtexe'];//标题
                                    $wrow=$lrow['rowbigtext'];//内容
                                    $weye=$lrow['roweye'];//预览量
                                    $wfl=$lrow['rowfl'];//分类
                                    $wtag=$lrow['rowtag'];//标签
                                    $wtime=$lrow['rowtime'];//发布时间
                                    $wadmin=$lrow['rowadmin'];//发布人
                                    $wcp=$lrow['rowcp'];//版权方
                                    $wcpurl=$lrow['rowcpurl'];//版权方链接
                                    $wd=$lrow['rowdw'];//下载数组
                                    $wglod=$lrow['rowdwgold'];//下载所需积分
                                    $wdifvip=$lrow['rowdwif'];//资源下载权限-->1所有已登录会员，2仅限VIP会员，3仅限网站管理人员
                                    $wvip=$lrow['rowvip'];//文章访问权限-->1所有人，2登录可见，3充值会员及管理员可见
                                    $wif=$lrow['rowif'];//文章类型，1图文，2相册，3视频
                                    $wtop=$lrow['rowtop'];//是否置顶，1默认不置顶，2置顶，3,热门，4精华
                                    $videotext=$lrow['videotext'];//视频或相册的说明
                                    $videotexttop=$lrow['videotexttop'];//相册或者视频的说明介绍位置，1显示在文章下方，2显示在文章上方
                                    $imageseye=$lrow['vorimg'];//游客可见
                                    $vipimageseye=$lrow['vorimg_log'];//登录可见
                                    $rowimg=$lrow['rowimg'];//封面图
                            }
                            if(empty($imageseye)||$imageseye<1||!is_numeric($imageseye)||$imageseye>999999999){
                                $imageseye=0;
                            }
                            if(empty($vipimageseye)||$vipimageseye<1||!is_numeric($vipimageseye)||$vipimageseye>999999999){
                                $vipimageseye=0;
                            }
                            if(empty($videotexttop)||$videotexttop<1||$videotexttop>2){
                                $videotexttop=1;
                            }

                            //获取vip会员折扣配置
                            $vipdiscount_sql="select upvipsize from ppz_upfile where id=1";
                            $vipdiscount_retval=mysqli_query($conn,$vipdiscount_sql);
                            if(mysqli_num_rows($vipdiscount_retval) > 0){
                                $vipdiscount_query = $conn->query($vipdiscount_sql);
                                while($v_row = $vipdiscount_query->fetch_assoc()){
                                    $vipdiscount = $v_row['upvipsize'];
                                }
                                if(empty($vipdiscount)||$vipdiscount<0){
                                    $vipdiscount = 0;
                                }else{
                                    $vipdiscount = $vipdiscount;
                                }
                            }else{
                                $vipdiscount=0;
                            }



                            if ($sessionyes==true){
                                if($allvip==2 || $allvip==3 || $allvip==4){
                                    $admintext="<div class='dix'><a id='edit' href='/user/popingzi.php?type=3&sid=".$id."'>编辑</a><a id='del' data-del='".$id."'>删除</a><script src='/style/js/delete.js' type='text/javascript'></script></div>";
                                }else{
                                    $admintext="";
                                }
                            }

                            if ($videotext!=="" && !is_null($videotext) && $videotext !== null && !empty($videotext) && isset($videotext) && ($wif==2||$wif==3)){
                                $videotextdiv='<div class="vtext">'.$videotext.'</div>';
                            }

                            if ($weye > 999999998){
                                $weyetext="10亿+";
                            }else if ($weye >= 100000000){
                                $weyetext=number_format($weye/100000000,2)."亿+";
                            }else if($weye >= 10000 &&  $weye < 100000000){
                                $weyetext=number_format($weye/10000,2)."万+"; 
                            }else{
                                $weyetext=$weye;
                            }

                            if ($weye < 999999999){ //阅览量低于10亿才执行
                                if(!isset($_COOKIE['eyecookie'])){
                                    $_COOKIE['eyecookie']="";   
                                }
                                $eyecookie=$_COOKIE['eyecookie'];//获取用户已浏览的文章数组
                                $eyeckarr=explode('|',$eyecookie);//转换数组
                            
                                if (is_null($eyecookie)||empty($eyecookie)||$eyecookie==null||$eyecookie==""){
                                                $eyesql = "UPDATE ppz_row SET roweye = roweye + 1 WHERE rowid = $id";//更新阅览量
                                                if ($conn->query($eyesql) === TRUE) {
                                                    setcookie("eyecookie",$id,0);//设置cookie
                                                }  
                                             
                                }else{
                            
                                    if(!in_array($id,$eyeckarr)){ //若用户没有阅览过该文章，则添加预览量
                                        $eyesql = "UPDATE ppz_row SET roweye = roweye + 1 WHERE rowid = $id";//更新阅览量
                                        $newcookie = $eyecookie."|".$id;
                                        if ($conn->query($eyesql) === TRUE) {
                                            setcookie("eyecookie",$newcookie,0);//设置cookie
                                        }
                                        
                                    }
                            
                                }
                                
                            }

                            if ($wif == 1){//图文
                                $newwrow=$wrow;
                                $rowico="fa-file-word-o";
                            }else if($wif == 2){//相册
                                $newwrow=$wrow;
                                $rowico="fa-file-photo-o";
                            }else if($wif == 3){//视频
                                $newwrow=$wrow;
                                $rowico="fa-file-video-o";
                                
                        if ( $one == "" || is_null($one) || empty($one) || $one <= 0 || !is_numeric($one)  ){
                            $oneid=0;
                        }else{
                            $oneid=$one;
                        }

                         if ( $wrow !== "" && !is_null($wrow)){
                            $str = $wrow;  
                            $array = explode("|", $str);
                            $index = array_search($oneid, array_keys($array)); // 获取id对应的索引
                            if ($index !== false) {  
                                $video=$array[$index];
                                if(empty($video)){
                                  $video="/video/default.mp4"; 
                                }else{
                                    $video=$array[$index];
                                }
                                $vs=count($array); // 输出数组中一共有多少条数据  
                            } else {  
                                $video="/video/default.mp4";
                                $vs=1;
                            }

                            $vmp4=pathinfo($video, PATHINFO_EXTENSION);
                            if ($vmp4=='mp4'||$vmp4=='m4v'||$vmp4=='webm'||$vmp4=='ogg'||$vmp4=='m3u8'){
                                $vmp4x=$vmp4;
                            }else{
                                $vmp4x='mp4';
                            }

                            if ($vmp4=='m3u8'){
                                $vt='window.HlsPlayer';
                            }else if($vmp4=='mp3'||$vmp4=='m4a'||$vmp4=='ogg'){
                                $vt='Mp4Plugin';
                                $poster="poster: './images/web/music.jpg',";
                                echo'<style>.xgplayer video{background: url(./images/web/music.jpg);    background-position: center;
                                    background-repeat: no-repeat;background-size: 100%;}</style>';
                            }else{
                                $vt='Mp4Plugin';
                            }
                     
                        }
                                echo"
                                <link rel='stylesheet' href='/style/css/index.min.css'>
                                <script type='text/javascript'>
                                  document.addEventListener('ready', () => {
                                    const resizeObserver = new ResizeObserver(() => {
                                      document.getElementById('mse').style.height = document.body.clientHeight + 'px'
                                    })
                                    resizeObserver.observe(document.body)
                                  })
                              </script>
                                ";
                            }else{
                                $newwrow="参数配置错误！";
                                $rowico="fa-unlock-alt";
                            }

                       


                        
                      
            };

}else{
    $rownull=1;
    $wvip="";
    $wd="";
    $wtag="";
    $sessionyes=false;
    $wtxt="文章不存在";
}

$xid=null;

?>
<meta charset="utf-8">
<title><?php echo $wtxt;?> - <?php echo $webtext;?></title>
<?php include $_SERVER['DOCUMENT_ROOT'].'/api/og.php';?>
<meta property="og:site_name" content="<?php echo $webtext;?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php include $_SERVER['DOCUMENT_ROOT'].'/inc/style.php';?>
<link rel="icon" href="/favicon.ico" />
<link type="text/css" rel="stylesheet" href="/style/highlight/arta.css" />
<link type="text/css" rel="stylesheet" href="/style/css/font-awesome-4.7.0/css/font-awesome.min.css" />
<script src="/style/js/jquery-3.5.1.min.js" type="text/javascript"></script>
<script src="/style/js/input.js" type="text/javascript"></script>
<script src="/style/js/alert.js" type="text/javascript"></script>
<script src="/style/highlight/highlight.js" type="text/javascript"></script>
<script src="/style/videojs/hls.js"></script>
</head>
<body>
<?php
include $_SERVER['DOCUMENT_ROOT'].'/inc/header.php';//头部
$ADS=200;
$ADSPAGE=3;
@include $_SERVER['DOCUMENT_ROOT'].'/api/indexads.php';//广告
echo $adson_js;
?>
<?php echo $adson_hf?>
<div class="body-div">

<?php if (!empty($wid)) :?>
    <div class="share">
        <div class="sharelink" data-type="share" data-id="<?php echo $wid;?>">
            <i class="fa fa-paper-plane" aria-hidden="true"></i>
            <span class="tooltipxx">分享</span>
        </div>
        <div class="sharelink" data-type="comm">
            <i class="fa fa-comments" aria-hidden="true"></i>
            <span class="tooltipxx">评论</span>
        </div>
        <div class="sharelink <?php echo $leftbutsc;?>" data-type="star">
            <i class="fa fa-star" aria-hidden="true"></i>
            <span class="tooltipxx"><?php echo $leftbutsct;?></span>
        </div>
        <div class="sharelink" data-type="refresh">
            <i class="fa fa-refresh" aria-hidden="true"></i>
            <span class="tooltipxx">刷新</span>
        </div>
        <div class="sharelink" data-type="back">
            <i class="fa fa-arrow-left" aria-hidden="true"></i>
            <span class="tooltipxx">返回</span>
        </div>
    </div>
    <div class="sharelogbox">
        <div class="sharelog">
            <div class="sharelogimg">
                <div class="shareimgx" style="background:url(/images/web/share1.jpg) center/cover no-repeat;">
                    <div id="shareimgxtime"></div>
                    <div id="shareimgxisvideo"><i class="fa fa-play" aria-hidden="true"></i></div>
                </div>
                <h1><?php echo $wtxt;?></h1>
                <div class="sharestyle"><span>作者：<i id="shareuser">获取中……</i></span><span>分类：<i id="sharetype">获取中……</i></span></div>
                <div class="sharevue" id="sharetexe">摘要获取中……</div>
                <div class="sharecode">
                    <div class="shareheda">
                        <div class="shareimg" style="background: url(<?php echo $weblogo;?>) left/contain no-repeat;"></div>
                        <b><?php echo $webby;?></b>
                    </div>
                    <div id="sharebody"></div>
                </div>
            </div>
            <div class="sharelogtitle">
                <div class="sharelogtitlebox">
                    <div id="sharelogboxcircle"><i class="fa fa-times" aria-hidden="true"></i></div>
                    <i class="sharegoto">分享到：</i>
                    <div class="sharelogbtn">
                        <button id="goweibo">新浪微博</button>
                        <button id="douban">豆瓣</button>
                        <button id="goqq">QQ好友</button>                        
                        <button id="goqzone">QQ空间</button>
                        <button id="gotwitter">Twitter X</button>
                        <button id="gofacebook">Facebook</button>
                        <button id="goinstagram">Telegram</button>
                    </div>
                    <i>点击复制链接：</i>
                    <input type="text" id="sharelinkurl" value="" readonly/>
                    <i>下载海报：</i>
                    <button id="sharelogdown" style="background:#969696;" disabled>海报生成中……</button>
                    
                </div>
            </div>
        </div>
    </div>
    <script src="/style/js/html2canvas/html2canvas.min.js" type="text/javascript"></script>
    <script src="/style/js/qrcode/qrcode.min.js" type="text/javascript"></script>
    <script src="/style/js/share.js" type="text/javascript"></script>
<?php endif;?>

    <div  id="uheight"  class="body-left">
    <div class="rowbg">
<?php
if ($rownull==2){

if ($wadmin !==0 && $wadmin !=="" && $wadmin!== null && !is_null($wadmin)){
    $uer = "select * from ppz_newusername where binary uid = $wadmin";//查询数据库，判断用户名是否存在
    $uerquery=mysqli_query($conn,$uer);
    if(mysqli_num_rows($uerquery) !== 1){

    }else{
        $uerqun= $conn->query($uer);
                while($xrow = $uerqun->fetch_array()){
                $uername=$xrow['uname'];//作者昵称
                $uerid=$xrow['uid'];//作者id
                }
    }

}
if (!isset($uername)) {
    $uername="未知会员";
}

if (!isset($uerid)) {
    $uerid="";
}

if ($wcp!==0 && $wcp!=="" && $wcp!== null &&!is_null($wcp)){
    $wcptxt="来源：".$wcp;
}else{
    $wcptxt="来源：".$webtext;
}

if ($wcpurl!==0 && $wcpurl!=="" && $wcpurl!== null &&!is_null($wcpurl)){
    $wcpurltxt="href='".$wcpurl."' target='_blank'";
}else{
    $wcpurltxt="";
}

$liflsql2 = "select * from ppz_fl where flid = $wfl"; //查询分类
$liflretval2=mysqli_query($conn,$liflsql2);
if(mysqli_num_rows($liflretval2) !== 1){ 
    $flid2 =0;
    $fllinkid = 1;
    $flnamet = "未知分类"; 
}else{
    $liflq2 = $conn->query($liflsql2);
    while($lifl2 = $liflq2 ->fetch_array()){
    $flid2=$lifl2['flid'];
    $fllinkid=$lifl2['fllinkid'];
    $flnamet=$lifl2['flname'];
    };
}


$sqlpl = "SELECT * FROM ppz_commentary where plrowid = $id"; //链接评论数据表
$pl_result = mysqli_query($conn,$sqlpl); //查询评论数据
$pl_records = mysqli_num_rows($pl_result);  // 统计评论总数

};

if ($wvip == 1 || $wvip == 0 || $wvip == null || $wvip == "" ){
    $gk=1;//所有人可访问
}else if($wvip == 2){

    if ($sessionyes==false){
        $gk=2;
    }else{
        $gk=1;//登录可访问
    }

}else if($wvip == 3){

         if ($sessionyes==false){
            $gk=2;
        }else{
        //链接数据库，获取会员身份信息，若会员身份不是管理员，则验证会员时间是否过期，没过期才能访问
        $xcsql = "select * from ppz_newusername where binary uusername = $ppzusername";//查询数据库，判断用户名是否存在
       $xcretval=mysqli_query($conn,$xcsql);
       if(mysqli_num_rows($xcretval) !== 1){	//如果用户名不存在
        $gk=0;
        }else{
                $xcquery = $conn->query($xcsql);
                while($xrow = $xcquery->fetch_array()){
                $xcustatus=$xrow['ustatus'];//身份，1普通会员，2为管理员，3为副站长，4为站长
                $xcuviptime=$xrow['uviptime'];//会员时间
                $xid=$xrow['uid'];//用户id
                }
    
                if ($xcustatus==2 || $xcustatus==3 || $xcustatus==4){
                    $gk=1;//管理员可访问
                }else if($xcustatus==1){

                    if ($uerid==$xid){
                        $gk=1;//自己发布的文章可见
                    }else{
                            //获取当前时间
                            $xxtime=time();
                            //将会员时间转换为时间戳
                            $uxxtime=strtotime($xcuviptime);
                            

                            //判断当前会员时间是否小于当前时间
                            if ($xxtime < $uxxtime){
                                $gk=1;
                            }else{
                                $gk=3;
                            }

                    }


                }else{
                    $gk=0;
                }



            }


        }



}else{
    $gk=0;
}

if (isset($admintext)){
    $admintext=$admintext;
}else{
    $admintext="";
}

if (isset($scclass)){
    $scclass=$scclass;
}else{
    $scclass="";
}

if (isset($urljs)){ 
    $urljs=$urljs;
}else{
    $urljs="";
}

if (!isset($poster)){
    $poster="";
}

if (!isset($videotextdiv)){
    $videotextdiv="";
}

if($wvip==3){
    $wvipmsg="此内容仅对VIP会员开放，请充值后再试！";
}elseif($wvip==2){
    $wvipmsg="此内容仅对注册用户开放，请登录后再试！";
}else{
    $wvipmsg="您无权查看此内容，请登录后再试！";
}

if ($gk===1){

if ($rownull == 1){
    echo "<div class='nulldiv'>空空如也~</div>";//没有文章时显示
    }else if ($rownull == 2){

       if ($wif == 2){//相册
        if (isset($_COOKIE['viewswitching'])) {
        $viewswitching=$_COOKIE['viewswitching'];
        }else{
        $viewswitching="";
        }
        if($viewswitching==2){
            $viewswitching='<i class="fa fa-th" aria-hidden="true"></i>小图模式';
            $vsclass='vsclass';
        }else{
            $viewswitching='<i class="fa fa-align-justify" aria-hidden="true"></i>大图模式';
            $vsclass='';
        }
        echo $admintext;
            echo '<div  class="rowtxt"><h5>'.$wtxt.'</h5></div>
            <div class="rowadmin">
            <div class="rowl">
                <a href="list.php?id='.$fllinkid.'&tag='.$flid2.'" class="rowfla">'.$flnamet.'</a><span><i class="fa fa-user-circle"></i><a href="user.php?id='.$uerid.'" target="_blank">'.$uername.'</a></span><span><i class="fa fa-clock-o"></i>'.date('Y年m月d日', strtotime($wtime)).'</span><span><i class="fa fa-commenting"></i>'.$pl_records.'</span><span><i class="fa fa-eye"></i>'.$weyetext.'</span><span><a '.$wcpurltxt.'>'.$wcptxt.'</a></span>
                <a id="viewswitching">'.$viewswitching.'</a>
            </div><div class="rowr"><a '.$sclogin.' class="rowsc nocopy '.$scclass.'"><i class="fa fa-star"></i>'.$sclogintxt.'</a></div></div>
            <div class="rowdiv">';
            if($videotexttop==2){echo $videotextdiv;};
            echo '<link rel="stylesheet" href="/style/PhotoSwipe/viewer.min.css">
            <script src="/style/PhotoSwipe/viewer.min.js"></script>
            <script src="/style/js/lazy/jquery.lazyload.js"></script>
            <ul id="imgxc" class="'.$vsclass.'">
            ';

            $arrayrow = explode("|", $newwrow);//相册图片数组
            // 确定最大可显示的图片数量
            $maxShow = 0;
            $totalImages = count($arrayrow); // 图片总数量
            // 判断用户状态并设置最大显示数量
            if (empty($ppzusername) || $ppzusername == false || !isset($ppzusername) ||!is_numeric($ppzusername)) {
                // 游客：使用游客可见数量
                if (is_numeric($imageseye) && $imageseye > 0) {
                    $maxShow = $imageseye;
                } else {
                    if (is_numeric($vipimageseye) && $vipimageseye > 0){
                        $maxShow = $vipimageseye;
                    }else{
                        $maxShow = $totalImages;
                    }
                }
            } else {
                if($allvip==2 || $allvip==3 || $allvip==4){
                    //管理员身份畅通无阻
                    $maxShow = $totalImages;
                }else{
                        // 登录用户：检查是否为有效VIP
                        if (!empty($allviptime) && strtotime($allviptime) > time()) {
                            $maxShow = $totalImages; // VIP可查看全部
                        } else {
                            // 非VIP登录用户：使用登录后可见数量
                            if (is_numeric($vipimageseye) && $vipimageseye > 0) {
                                $maxShow = $vipimageseye;
                            } else {
                                $maxShow = $totalImages;
                            }
                        }
                }
            }            
            // 确保最大显示数量不超过实际图片总数
            $maxShow = min($maxShow, $totalImages);
            // 循环输出图片，限制在最大可显示数量内
            $count = 0;
            foreach ($arrayrow as $itemrow) {
                if ($count < $maxShow) {
                    echo '<img class="lazy" data-original="' . htmlspecialchars($itemrow) . '" alt="'.$wtxt.'" />';
                    $count++;
                } else {
                    break; // 超过数量则停止输出
                }
            }

    echo '</ul>';
    if($videotexttop==1){echo $videotextdiv;};
            // 当总数量超过可显示数量时，显示提示信息
            if ($totalImages > $maxShow) {
                echo '<div class="image-limit-info">';
                echo '您当前可查看 ' . $maxShow . ' 张图片，本相册共有 ' . $totalImages . ' 张图片。';
                // 根据用户状态显示不同的升级提示
                if (empty($ppzusername) || $ppzusername == false || !isset($ppzusername) ||!is_numeric($ppzusername)) {
                    echo '登录可查看更多。';
                } elseif (empty($allviptime) || strtotime($allviptime) <= time()) {
                    echo '<a href="/vip/" target="_blank">升级VIP会员</a>可查看全部。';
                }
                echo '</div>';
            }
    echo '</div>
    <script>var viewer = new Viewer(document.getElementById("imgxc")); $(function() { $("img.lazy").lazyload({effect: "fadeIn"});});</script>
    <script src="/style/js/viewswitching.js"></script>    
    ';
        }else if($wif == 3){//视频
            echo $admintext;
            echo '<div  class="rowtxt">'.$wtxt.'</div>
            <div class="rowadmin"><div class="rowl"><a href="list.php?id='.$fllinkid.'&tag='.$flid2.'" class="rowfla">'.$flnamet.'</a><span><i class="fa fa-user-circle"></i><a href="user.php?id='.$uerid.'" target="_blank">'.$uername.'</a></span><span><i class="fa fa-clock-o"></i>'.date('Y年m月d日', strtotime($wtime)).'</span><span><i class="fa fa-commenting"></i>'.$pl_records.'</span><span><i class="fa fa-eye"></i>'.$weyetext.'</span><span><a '.$wcpurltxt.'>'.$wcptxt.'</a></span></div><div class="rowr"><a '.$sclogin.' class="rowsc nocopy '.$scclass.'"><i class="fa fa-star"></i>'.$sclogintxt.'</a></div></div>
            <div class="rowdiv">';
            if($videotexttop==2){echo $videotextdiv;};
            echo '<div id="mse"></div>
            <script src="/style/js/videojs/index.min.js" charset="utf-8"></script>
            <script src="/style/js/videojs/mp4.min.js" charset="utf-8"></script>
            <script src="/style/js/videojs/hls.min.js" charset="utf-8"></script>
            <script>
            const urlParams = new URLSearchParams(window.location.search);
            const videoid = urlParams.get("id");
            let videot = urlParams.get("t");
            let videonewtime = 0;
            if (!videot) {
                videot=0;
            }
            const savedTime = localStorage.getItem(`xgplayerVideoTime${videoid}${videot}`);
            if (savedTime) {
                videonewtime = parseFloat(savedTime);
            }
            const player = new window.Player({
              id: "mse",
              url: "'.$video.'",
              height: "600px",
              width: "100%",
              startTime: videonewtime,
              pip: true,
              volume:1,
              type:"'.$vmp4x.'",
             plugins: ['.$vt.'],
              "screenShot": true,
              '.$poster.'
            })
              //准备就绪
              player.on("ready", () => {
                player.currentTime=videonewtime;
              });

              //播放
              player.on("play", () => {
                player.currentTime=videonewtime;
              });

  
            if (savedTime) {
                    player.on("play", () => {
                            player.on("timeupdate", () => {
                                try {
                                    //判断视频时长是否接近结尾
                                    if (player.currentTime > player.duration - 5) {
                                        localStorage.removeItem(`xgplayerVideoTime${videoid}${videot}`);
                                        player.startTime = 0;
                                    }else{
                                        localStorage.setItem(`xgplayerVideoTime${videoid}${videot}`, player.currentTime);
                                    }                                   
                                } catch (error) {
                                    console.error("Failed to save video time:", error);
                                }
                            });
                });
            }else{

                        player.on("timeupdate", () => {
                            try {
                                    if (player.currentTime > player.duration - 5) {
                                        localStorage.removeItem(`xgplayerVideoTime${videoid}${videot}`);
                                        player.startTime = 0;
                                    }else{
                                        localStorage.setItem(`xgplayerVideoTime${videoid}${videot}`, player.currentTime);
                                    }        
                            } catch (error) {
                                console.error("Failed to save video time:", error);
                            }
                    });

            }
            
            </script><div class="aia">';
            if ($vs>1){
                
                for ($i = 1; $i <= $vs; $i++) { 
                    $ai= $i-1; 
                    if ($i == $oneid+1){
                        echo '<a href="?id='.$id.'&t='.$ai.'" class="ai videonow">第'.$i.'集</a>';
                    }else{
                        echo '<a href="?id='.$id.'&t='.$ai.'" class="ai">第'.$i.'集</a>'; 
                    }
                } 
            }else{
                echo '<a href="" class="ai">全1集</a>';
            }
            
   echo '</div></div>';
   if($videotexttop==1){echo $videotextdiv;};
        }else if($wif == 1){//图文
            echo $admintext;
            echo '
            <link rel="stylesheet" href="/style/PhotoSwipe/viewer.min.css">
            <script src="/style/PhotoSwipe/viewer.min.js" type="text/javascript"></script>
            <div  class="rowtxt"><h5>'.$wtxt.'</h5></div>
            <div class="rowadmin"><div class="rowl"><a href="list.php?id='.$fllinkid.'&tag='.$flid2.'" class="rowfla">'.$flnamet.'</a><span><i class="fa fa-user-circle"></i><a href="user.php?id='.$uerid.'" target="_blank">'.$uername.'</a></span><span><i class="fa fa-clock-o"></i>'.date('Y年m月d日', strtotime($wtime)).'</span><span><i class="fa fa-commenting"></i>'.$pl_records.'</span><span><i class="fa fa-eye"></i>'.$weyetext.'</span><span><a '.$wcpurltxt.'>'.$wcptxt.'</a></span></div><div class="rowr"><a '.$sclogin.' class="rowsc nocopy '.$scclass.'"><i class="fa fa-star"></i>'.$sclogintxt.'</a></div></div>
            <div id="imgrow" class="rowdiv">'.$newwrow.'</div>
            <script type="text/javascript">var viewer = new Viewer(document.getElementById("imgrow"));</script>
            <script src="/style/js/copy/clipboard.js" type="text/javascript"></script>
            <script type="text/javascript">
            var copyBtn = new ClipboardJS(".article-aff");
            copyBtn.on("success",function(e){
                alert("<font>(◕ܫ◕)</font> 复制成功！");
                e.clearSelection();
            });
            copyBtn.on("error",function(e){
                alert("<font>(｡ŏ_ŏ)</font> 复制失败！");
                console.log( e.action )
            });
            </script>
            ';
        }else{
            echo "<div class='nulldiv'>请勿乱搞~</div>";
        }


    }else{
        echo "<div class='nulldiv'>请勿乱搞~</div>";
    }

    if (!empty($wd)){
        $dow= explode(",",$wd);//数组格式说明：网盘名称,下载地址,内容数量,文件大小,解压密码,网盘提取码,内容分辨率
        $dwp=$dow[0];//网盘名称
        $dwurl=$dow[1];//下载地址
        $dws=$dow[2];//数量说明
        $dwsize=$dow[3];//大小说明
        $dwpass=$dow[4];//文件解压所需的密码说明
        $dwppass=$dow[5];//网盘所需的提取码说明
        $dwpx=$dow[6];//分辨率说明
        if ($dwp===""||is_null($dwp)){$dwptxt="文件下载";}else{$dwptxt=$dwp;}
        if ($dws===""||is_null($dws)){$dwstxt="未知数量";}else{$dwstxt=$dws;}
        if ($dwsize===""||is_null($dwsize)){$dwsizetxt="未知大小";}else{$dwsizetxt=$dwsize;}
        if ($dwpass===""||is_null($dwpass)){$dwpasstxt="无解压密码";}else{$dwpasstxt=$dwpass;}
        if ($dwppass===""||is_null($dwppass)){$durl="href='".$dwurl."' target='_blank'";$dwppasstxt="";$downid="";}else{$durl="";$dwppasstxt=$dwppass;$downid="down";}
        if ($dwpx===""||is_null($dwpx)){$dwpxtxt="未知分辨率";}else{$dwpxtxt=$dwpx;}
        if (empty($wglod)){
            $wglodtxt="免费";
        }else{
            if(!empty($uviptime)&&isset($uviptime)){
                $vip_time_str=strtotime($uviptime);
            }else{
                $vip_time_str=strtotime(date("2010-01-01 00:00:00"));
            }

            if(time()<$vip_time_str){
                $yuan_glod=$wglod;
                $wglod=intval($wglod*(1-$vipdiscount/100));
                if($wglod<1){
                   $wglod_t="免费";
                }else{
                    $wglod_t=$wglod."积分";
                }
                $wglodtxt=$yuan_glod.'积分<span class="vipgold">（会员:'.$wglod_t.'）</span>';
            }else{
                $wglod=intval($wglod);
                $wglodtxt=$wglod."积分";
            }            
        }

        if (empty($dwurl)){
echo "<div class='nulldowdiv'>文件下载地址配置不正确，请联系管理员修复纠正！</div>";
        }else{
            echo '<div class="dowdiv">
            <div id="d" class="fieldsetdiv">
<fieldset class="erphpdown-box">
<legend>'.$dwptxt.'</legend>
<div class="item price"><t>下载价格：</t>
<span class="itemmianf">'.$wglodtxt.'</span></div>';
if ($sessionyes==false){
echo '<a id="showModaladl3" class="down signin-loader">请先登录</a>';
 $url404="请先登录！";
}else{
$gmsql = "select * from ppz_newusername where binary uusername='$ppzusername' ";//获取会员购买的下载
$gmretval=mysqli_query($conn,$gmsql);
if(mysqli_num_rows($gmretval) !== 1){
    $urowyes="";
}else{
    $gmquery = $conn->query($gmsql);
    while($gmrow = $gmquery->fetch_array()){
        $urowyes=$gmrow['urowyes'];
    }
}

if ($urowyes!==""&&!is_null($urowyes)){
    $gmarr=explode("|",$urowyes);
}else{
    $gmarr=array();
}

if ($wdifvip==1){
    
    if ($allvip==1||$allvip==2||$allvip==3||$allvip==4){
        $newvipsf=1;
    }else{
        $wdifviptext='参数错误!!!';
        $newvipsf=0;
    }
}elseif ($wdifvip==2){
    if ($allvip==2||$allvip==3||$allvip==4){
        $newvipsf=1;
    }else{
        $xxtimess=time();//当前时间戳
        $uxxtimess=strtotime($allviptime);//会员到期时间戳
        if ($xxtimess < $uxxtimess){
            $newvipsf=1;
        }else{
            $wdifviptext='仅限VIP会员购买/下载';
            $newvipsf=0;
        }
    }
}elseif ($wdifvip==3){
    if ($allvip==2||$allvip==3||$allvip==4){
        $newvipsf=1;
    }else{
        $newvipsf=0;
        $wdifviptext='内部资料，禁止购买/下载！';
    }
}else{
    $wdifviptext='错误参数!!!';
    $newvipsf=0;
}



    if (empty($wglod)&&$newvipsf==1){
        $url404="免费文件不做分析！";
        echo "
        <a id='down' class='down'>立即下载</a>
        <script>
            const copytext = '".$dwppasstxt."';
            const downcopy = document.getElementById('down');
            const targetUrl = '".$dwurl."';
            const isEmpty = !copytext || copytext.trim() === '';
            downcopy.addEventListener('click', function (e) {
                e.preventDefault();
                
                if (isEmpty) {
                    window.open(targetUrl, '_blank');
                    return;
                }

                const copySuccess = copyToClipboard(copytext);
                
                if (copySuccess) {
                    if (confirm('提取码已复制，是否跳转到下载页？')) {
                        window.open(targetUrl, '_blank');
                    }
                } else {
                    if (confirm('提取码：' + copytext + '，是否跳转到下载页？')) {
                        window.open(targetUrl, '_blank');
                    }
                }
            });

            function copyToClipboard(text) {
                try {
                    const textArea = document.createElement('textarea');
                    textArea.value = text;
                    textArea.style.cssText = 'position:fixed;left:-9999px;top:0;opacity:0;pointer-events:none;';
                    
                    document.body.appendChild(textArea);
                    textArea.select();
                    textArea.setSelectionRange(0, textArea.value.length);
                    
                    const result = document.execCommand('copy');
                    document.body.removeChild(textArea);
                    return result;
                } catch (err) {
                    return false;
                }
            }
        </script>
        ";}else{

            if ((!in_array($id, $gmarr)) && $uerid!==$xid) {  
                if ($newvipsf==1){
                    echo "<a id='gdown' class='down' >立即购买</a><script>var dwid=".$id.";var gold=".$wglod.";</script><script src='/style/js/down.js' type='text/javascript'></script>";
                    $url404= "<span id='url200'></span><a id='urlss'>点击获取状态</a> ";
                    $urljs='<script type="text/javascript">var id='.$id.';</script></script><script src="/style/js/url404.js" type="text/javascript"></script>';
                }else{
                    echo "<a class='down' >".$wdifviptext."</a>";
                    $url404="无此资源下载权限，不做分析！";
                    $urljs='';
                }
                
            } else {  
                $url404="已购买文件不做分析！";
                echo "
                <a $durl id='".$downid."' class='down' data-clipboard-text='".$dwppasstxt."'>立即下载</a>
                <script src='/style/js/copy/clipboard.js' type='text/javascript'></script>
                <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var copyBtn = document.querySelector('.down');
                            if (!copyBtn) return;
                        
                            var copyText = copyBtn.getAttribute('data-clipboard-text');
                            var downUrl = '".$dwurl."';
                            try {
                                var clipboard = new ClipboardJS('.down');
                        
                                clipboard.on('success', function(e) {
                                    e.clearSelection();
                                    if (confirm('提取码已复制，是否跳转到下载页？')) {
                                        window.open(downUrl, '_blank');
                                    }
                                });
                        
                                clipboard.on('error', function() {
                                    showCopyFailDialog();
                                });
                        
                            } catch (e) {
                                showCopyFailDialog();
                            }
                            function showCopyFailDialog() {
                                if (confirm('提取码：[' + '".$dwppasstxt."' + ']是否前往下载页面？')) {
                                    window.open(downUrl, '_blank');
                                }
                            }
                        });
                </script>";
            }

        }
}
echo '
<div class="custom-metas">
<div class="item item2"><t>内容数量：</t>'.$dwstxt.'</div>
<div class="item item2"><t>分 辨 率：</t>'.$dwpxtxt.'</div>
<div class="item item2"><t>文件大小：</t>'.$dwsizetxt.'</div>
<div class="item item2"><t>文件密码：</t>'.$dwpasstxt.'</div>
<div class="item item2"><t id="fhzt">链接状态：</t><font style="color: var(--hover-color);">'.$url404.'</font>'.$urljs.'</div>
<div class="item item3"><p><t>下载说明：</t>注意查看上方链接返回状态，可判断文件下载地址是否有效；如果显示<font>链接不存在</font>或<font>404</font>等字样，说明文件异常，需要谨慎购买。</p>
</div>


<div class="item item4">
<p><t2>版权说明：</t2>该文件由 <a href="/user.php?id='.$uerid.'" target="_blank">'.$uername.'</a> 发布，若有侵权、违规、发布无效链接等行为，请联系网站管理员进行<a '.$wkhref.' target="_blank">举报反馈</a>。</p>
</div>
</div>
</fieldset></div>
            
            </div>';
        }
    }

    if ($wtag!=="" && $wtag!=null && $wtag!==false && $wtag!==0 && !is_null($wtag) && !empty($wtag)){ //是否存在标签，存在则显示
        $wtagarr=explode(",",$wtag);
        echo '<div class="tags"><span>标签：</span>';
        for ($i=0;$i<count($wtagarr);$i++){
            echo '<a href="search.php?v=tag&s=' . $wtagarr[$i] . '" class="tagsa">' . $wtagarr[$i] . '</a>';
        }
        echo '</div>';
    }


}else if($gk===0){
    echo '<div  class="rowtxt">'.$wtxt.'</div>
            <div class="rowadmin"><div class="rowl"><a href="list.php?id='.$fllinkid.'&tag='.$flid2.'" class="rowfla">'.$flnamet.'</a><span><i class="fa fa-user-circle"></i><a href="user.php?id='.$uerid.'" target="_blank">'.$uername.'</a></span><span><i class="fa fa-clock-o"></i>'.date('Y年m月d日', strtotime($wtime)).'</span><span><i class="fa fa-commenting"></i>'.$pl_records.'</span><span><i class="fa fa-eye"></i>'.$weye.'</span><span><a '.$wcpurltxt.'>'.$wcptxt.'</a></span></div><div class="rowr"><a '.$sclogin.' class="rowsc nocopy '.$scclass.'"><i class="fa fa-star"></i>'.$sclogintxt.'</a></div></div>
            <div class="rowdiv"><div class="vnull"><i class="fa '.$rowico.'"></i>非法操作，系统拒绝访问！</div></div>';
}else if($gk===2){
    echo '<div  class="rowtxt">'.$wtxt.'</div>
            <div class="rowadmin"><div class="rowl"><a href="list.php?id='.$fllinkid.'&tag='.$flid2.'" class="rowfla">'.$flnamet.'</a><span><i class="fa fa-user-circle"></i><a href="user.php?id='.$uerid.'" target="_blank">'.$uername.'</a></span><span><i class="fa fa-clock-o"></i>'.date('Y年m月d日', strtotime($wtime)).'</span><span><i class="fa fa-commenting"></i>'.$pl_records.'</span><span><i class="fa fa-eye"></i>'.$weye.'</span><span><a '.$wcpurltxt.'>'.$wcptxt.'</a></span></div><div class="rowr"><a '.$sclogin.' class="rowsc nocopy '.$scclass.'"><i class="fa fa-star"></i>'.$sclogintxt.'</a></div></div>
            <div class="rowdiv"><div class="vnull"><i class="fa '.$rowico.'"></i>'.$wvipmsg.'</div></div>';
}else if($gk===3){
    echo '<div  class="rowtxt">'.$wtxt.'</div>
    <div class="rowadmin"><div class="rowl"><a href="list.php?id='.$fllinkid.'&tag='.$flid2.'" class="rowfla">'.$flnamet.'</a><span><i class="fa fa-user-circle"></i><a href="user.php?id='.$uerid.'" target="_blank">'.$uername.'</a></span><span><i class="fa fa-clock-o"></i>'.date('Y年m月d日', strtotime($wtime)).'</span><span><i class="fa fa-commenting"></i>'.$pl_records.'</span><span><i class="fa fa-eye"></i>'.$weye.'</span><span><a '.$wcpurltxt.'>'.$wcptxt.'</a></span></div><div class="rowr"><a '.$sclogin.' class="rowsc nocopy '.$scclass.'"><i class="fa fa-star"></i>'.$sclogintxt.'</a></div></div>
    <div class="rowdiv"><div class="vnull"><i class="fa '.$rowico.'"></i>此内容仅对VIP会员开放，请充值后再试！<p><a target="_blank" href="/vip/">购买充值卡</a></p></div></div>';
}else{
    echo '<div  class="rowtxt">'.$wtxt.'</div>
    <div class="rowadmin"><div class="rowl"><a href="list.php?id='.$fllinkid.'&tag='.$flid2.'" class="rowfla">'.$flnamet.'</a><span><i class="fa fa-user-circle"></i><a href="user.php?id='.$uerid.'" target="_blank">'.$uername.'</a></span><span><i class="fa fa-clock-o"></i>'.date('Y年m月d日', strtotime($wtime)).'</span><span><i class="fa fa-commenting"></i>'.$pl_records.'</span><span><i class="fa fa-eye"></i>'.$weye.'</span><span><a '.$wcpurltxt.'>'.$wcptxt.'</a></span></div><div class="rowr"><a '.$sclogin.' class="rowsc nocopy '.$scclass.'"><i class="fa fa-star"></i>'.$sclogintxt.'</a></div></div>
    <div class="rowdiv"><div class="vnull"><i class="fa '.$rowico.'"></i>'.$wvipmsg.'</div></div>';
}

?>
</div>
<?php echo $adson_rowhf;?>
<?php if ($rownull == 2){ ?>
<div class="pldiv"><div class="plli"><span>丨</span>您想说点什么？</div>
<?php
if ($sessionyes==true){
    echo '<script>var uname="'.$uname.'"; var unameid='.$uid.';var unameimg="'.$uimg.'";</script>
    <link rel="stylesheet" type="text/css" href="/style/emoji/emojionearea.css" media="screen">
    <script type="text/javascript" src="/style/emoji/emojionearea.js"></script>
<textarea placeholder="在这里输入评论..." name="pltext" id="pltext" maxlength="240"></textarea><div class="plfontsize"><span><i>剩余字数：</i><span id="plnum" class="din">240</span></span><button data-txt="'.$id.'" id="plbut" class="plbut">提交</button></div>
    <script type="text/javascript">

    $(document).ready(function() {
    $("#pltext").emojioneArea({
      autoHideFilters: true,
      hideSource: true,
      useSprite: false,
      spellcheck:true,
      autocorrect:true,
      autocomplete:true,
      events: {
        keyup: function (editor, event) {
          countChar(this);
       }
     }
    });
  });
  function countChar(val) {
    var len = val.getText().length;
    if (len >= 240) {
          val.value = val.content.substring(0, 240);
          $("#plnum").text(0);
          alert("最多只能输入240个字哦");
    } else {
         $("#plnum").text(240 - len);
    }
}
  </script>
    ';
}else if($sessionyes==false){
    echo '<div class="nullgion">请先登录！</div>';
}else{
    echo '<div class="nullgion">请勿乱搞！</div>';
}


?>
            <div id="plall" class="pllink">

            
 <?php
 //分页
$num_rec_per_page=10;   // 每页显示评论数量
if (isset($_GET["p"])){
    $getp=$_GET["p"];//获取GET传参P
}else{
    $getp="";
}


/*判断参数P是否为空，且是否是数字*/
if (isset($getp) && is_numeric($getp) && $getp>=1 ){ 
$pa = $_GET["p"];
} else { 
$pa=1; 
}; 

$plsqlll = "SELECT * FROM ppz_commentary where binary plrowid = $id"; //链接数据表
$plrs_result = mysqli_query($conn,$plsqlll); //查询数据
$plmu = mysqli_num_rows($plrs_result);  // 统计数据总数
$total_pages = ceil($plmu / $num_rec_per_page);  // 计算总页数

if ($total_pages < $pa){
$p=1;
}else{
$p=$pa; 
}

$start_from = ($p-1) * $num_rec_per_page; 

$plsql = "select * from ppz_commentary where binary plrowid = $id ORDER BY plid DESC LIMIT $start_from, $num_rec_per_page";//获取评论
$plretval=mysqli_query($conn,$plsql);
if(mysqli_num_rows($plretval) < 1){
    echo "<div class='plnull'><span>沙发，等你来坐！</span></div>";
}else{
    $plquery = $conn->query($plsql);
    while($plrow = $plquery->fetch_array()){
        $plid=$plrow['plid'];//评论id
        $plbigtext=$plrow['plbigtext'];//评论内容
        $pltime=$plrow['pltime'];//评论时间
        $pladmin=$plrow['pladmin'];//评论人
        $pltops=$plrow['pltop'];//评论点赞数组

        if ($pltops===""||is_null($pltops)||empty($pltops)||$pltops==null){
            $pltop=0;
        }else{
            $pltop=count(explode('|',$pltops));
        }

        $plusql = "select * from ppz_newusername where binary uid = $pladmin";//获取评论者信息
        $pluretval=mysqli_query($conn,$plusql);
        if(mysqli_num_rows($pluretval) < 1){
            $pluimg = "/images/web/default.jpg";
            $pluuname = "佚名";
            $pluustatus = "错误用户";
        }else{
             $pluquery = $conn->query($plusql);
            while($plurow = $pluquery->fetch_array()){
                $pluimg = $plurow['uimg'];//评论者头像
                $pluuname = $plurow['uname'];//评论者昵称
                $pluustatus = $plurow['ustatus'];//评论者身份：1普通会员，2为管理员，3为副站长，4为站长
            }
        }

        if (empty($pluimg)){
           $plimga="/images/web/default.jpg";
        }else{
           $plimga = $pluimg;
        }

if ($pluustatus==2){
$ttus="<i class='iadmin'>管理员</i>";
}else if($pluustatus==3){
    $ttus="<i class='iadmin'>副站长</i>";
}else if($pluustatus==4){
    $ttus="<i class='iadmin'>站长</i>";
}else{
    $ttus="";
}


$repsqlx = "select * from ppz_reply where repplid = $plid"; //获取回复
$repretvalx=mysqli_query($conn,$repsqlx);
$rep_records = mysqli_num_rows($repretvalx); 


        //转为时间格式
        $pltimea=date("Y年m月d日",strtotime($pltime));

echo '<div  class="plall"><div class="pllinkl"><img src="'.$plimga.'"/></div>
<div class="pllinkr">
<div class="pltext">'.$plbigtext.'</div>
<div class="pladmin"><div class="topl">
<a href="user.php?id='.$pladmin.'" target="_blank"><i class="fa fa-user-circle"></i>'.$pluuname.''.$ttus.'</a>
<span><i class="fa fa-clock-o"></i>'.$pltimea.'</span>
</div><div class="topr">';
if ($sessionyes==true){
echo '
<a id="reply" data-id="'.$plid.'" class="huifu nocopy">回复('.$rep_records.')</a>
<a id="topa'.$plid.'" data-rid="'.$plid.'" class="reptop nocopy"><i class="fa fa-thumbs-o-up"></i><span id="one'.$plid.'">'.$pltop.'</span></a>';
}else{
echo '
<a onclick="loginFunction()" class="huifu nocopy">回复('.$rep_records.')</a>
<a onclick="loginFunction()" id="topa" ><i class="fa fa-thumbs-o-up"></i><span>'.$pltop.'</span></a>';
}

echo '</div></div>'; 

if ($sessionyes==true){
echo '
<div class="reply-form" id="reply-form'.$plid.'" style="display:none;">
   <textarea placeholder="回复：'.$pluuname.'" class="reply-text" id="reply-text'.$plid.'" maxlength="90"></textarea>  
   <div class="reply-up"><span id="reply-num"><i>剩余字数：</i><span id="spanmun'.$plid.'">90</span></span><button class="reply-submit" id="reply-submit'.$plid.'">确定</button>  </div>
 '; 
  
$repsql = "select * from ppz_reply where repplid = $plid ORDER BY repid DESC LIMIT 0,5"; //获取回复,并只显示5篇
$repretval=mysqli_query($conn,$repsql);
if(mysqli_num_rows($repretval) < 1){ 
}else{
    echo '<div id="plreply'.$plid.'" class="plreply">';
    $repsqla = $conn->query($repsql);
    while($rep = $repsqla ->fetch_array()){
        $repid=$rep['repid'];//回复id
        $repadmin=$rep['repadmin'];//回复者id
        $reptext=$rep['reptext'];//回复内容 
        $reptime=$rep['reptime'];//回复时间
        $plusql2 = "select * from ppz_newusername where binary uid = $repadmin";//获取回复者信息
        $pluretval2=mysqli_query($conn,$plusql2);
        if(mysqli_num_rows($pluretval2) < 1){
            $pluuname2 = "佚名";
        }else{
             $pluquery2 = $conn->query($plusql2);
            while($plurow2 = $pluquery2->fetch_array()){
                $pluuname2 = $plurow2['uname'];//回复者昵称
                $uuimg2 = $plurow2['uimg'];//回复者头像
            }

            if ($uuimg2==""||is_null($uuimg2)||empty($uuimg2)){
               $uxuimg2="/images/web/default.jpg";
            }else{
               $uxuimg2 = $uuimg2;
            }
        }
        echo '<p><span class="detspan"><a href="user.php?id='.$repadmin.'" target="_blank"><i style="background:url('.$uxuimg2.');background-size: 100%;    background-repeat: no-repeat;"></i>'.$pluuname2.'：</a><span class="timess">'.date("Y-m-d",strtotime($reptime)).'</span></span><span>'.$reptext.'</span></p>';
};

if ($rep_records>5){
    echo '<a href="plreply.php?id='.$plid.'" target="_blank" class="repall nocopy">查看更多回复</a>'; 
}


echo '</div>';   
}
   echo '</div>';
}

echo '
</div>
</div>
';

        };

if ($sessionyes==true){
echo '<script type="text/javascript" src="/style/js/reply.js"></script>';
}
}
 ?>
          

                <div class="plpage">
                <div class="page-left"><?php echo $plmu;?>条评论(共<?php echo $total_pages;?>页)</div>
                <div class="pl-right">
                <a href="show.php?id=<?php echo $id;?>&p=1#plall" class="<?php if ($p==1 || $p < 1){echo "page-no-button nocopy";}else{echo "page-button nocopy";}?>">首页</a>
                <a <?php if ($p==1 || $p < 1){}else{echo "href='show.php?id=".$id."&p=".($p-1)."#plall'";}?> class="<?php if ($p==1 || $p < 1){echo "page-no-button nocopy";}else{echo "page-button nocopy";}?>">上一页</a>
                <a <?php if ($total_pages<$p+1){}else{echo "href='show.php?id=".$id."&p=".($p+1)."#plall'";}?> class="<?php if ($total_pages<$p+1){echo "page-no-button nocopy";}else{echo "page-button nocopy";}?>">下一页</a>
                <a <?php if ($total_pages<$p+1){}else{echo "href='show.php?id=".$id."&p=".$total_pages."#plall'";} ?> class="<?php if ($total_pages<$p+1){echo "page-no-button nocopy";}else{echo "page-button nocopy";}?>">尾页</a>
                </div>
                </div>
                
            </div>
</div>
<?php }?>
    </div>
<?php include $_SERVER['DOCUMENT_ROOT'].'/inc/right.php';?>
</div>
<?php include $_SERVER['DOCUMENT_ROOT'].'/inc/footer.php'; 
if ($sessionyes==false && !empty($wid)){
     echo '<script src="/style/js/login.js" type="text/javascript"></script>';
};
if (isset($scscript)){echo $scscript;}
echo '<script src="/style/js/reppl.js" type="text/javascript"></script>';
?>
<script>
hljs.highlightAll();
</script>
<script>
    const alertLink = document.getElementById("alertLink");
    if(alertLink){
        alertLink.addEventListener("click", function(e) {
            e.preventDefault();
            alert("<font>(,,•́ . •̀,,)</font> 请先登录！");
        });
    }
</script>
<script src="/style/js/video.js" type="text/javascript"></script>
<script src="/style/js/audio.js" type="text/javascript"></script>
<?php echo $adson_yxj.$adson_left.$adson_right?>
<?php @include $_SERVER['DOCUMENT_ROOT'].'/wap/index.php';?>
</body>
</html>