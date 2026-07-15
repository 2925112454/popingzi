<?php
if(!isset($indexone)){
    $indexone=0;
}
if($indexone!==200){
    header("HTTP/1.1 404 Not Found");
    exit;
}

?>
<!DOCTYPE html>
<html lang="zh-CN" translate="no">
<head>
<?php
$linkimgsz=1; //列表设置，1为竖图，2为横图
$linkimgsize=1; //列表图片一行展示数量，1为一行4张，2为一行3张
if($linkimgsz==2){
       $lbsz=2;
}else{
 $lbsz='';
};
if($linkimgsize==2){
    $lb=2;
}else{
$lb='';
};
?>
<meta charset="utf-8">
<title><?php echo $webtext;?>丨<?php echo $webby;?></title>
<meta name="keywords" content="<?php echo $webpass;?>" />
<meta name="description" content="<?php echo $webvar;?>" />
<link rel="icon" href="/favicon.ico"/>
<?php @include $_SERVER['DOCUMENT_ROOT'].'/inc/style.php';?>
<link type="text/css" rel="stylesheet" href="/style/css/font-awesome-4.7.0/css/font-awesome.min.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="/style/js/jquery-3.5.1.min.js" type="text/javascript"></script>
<script src="/style/js/input.js" type="text/javascript"></script>
<script src="/style/js/alert.js" type="text/javascript"></script>
</head>
<body>
<?php
@include $_SERVER['DOCUMENT_ROOT'].'/inc/header.php';//头部
$ADS=200;
$ADSPAGE=1;
@include $_SERVER['DOCUMENT_ROOT'].'/api/indexads.php';//广告
echo $adson_js;
?>
<?php echo $adson_hf?>
<?php
echo '
<div class="body-div">
<div class="body-left">
    <main class="flex-grow-main" id="mainElement">
        <section>
            <div>
                <div id="carousel-container">
                    <div id="carouselxx"></div>
                    <button class="carousel-control prev" id="prev-btn" aria-label="上一张">
                        <i class="fa fa-chevron-left"></i>
                    </button>
                    <button class="carousel-control next" id="next-btn" aria-label="下一张">
                        <i class="fa fa-chevron-right"></i>
                    </button>
                    <div id="carousel-indicators"></div>
                </div>
            </div>
        </section>
        <script src="/style/js/carousel.js" type="text/javascript"></script>
    </main>
';
    //获取文章列表
    $listsql="select * from ppz_link";
    $listretval=mysqli_query($conn,$listsql);
    if(mysqli_num_rows($listretval) > 0){
        $indexmun = 0; // 计数器
        while($list = $listretval->fetch_array()){
            $indexmun++; // 每次循环增加计数器
            $linkidx=$list['linkid'];//列表id
            $linknamex=$list['linkname'];//列表名称
            $linkimgif=$list['linkimg'];//列表封面类别，1为竖屏，2为横屏，3为资讯类

            
            echo '<div class="indexmun">
                <div class="module">
                    <div class="module-title"><span>丨</span>'.$linknamex.'</div>
                    <a href="/list.php?id='.$linkidx.'" target="_blank">更多<i class="fa fa-angle-right" aria-hidden="true"></i></a>
                </div>
                <div class="module-content">';
                    //查找分类
                    $linkcatesql="select * from ppz_fl where fllinkid = $linkidx";
                    $linkcateretval=mysqli_query($conn,$linkcatesql);
                    if(mysqli_num_rows($linkcateretval) > 0){

                        $has_content = false;
                        $linkcateids=[];
                        while($linkcate = $linkcateretval->fetch_array()){
                            $linkcateids[] = $linkcate['flid'];//获取分类id
                        }
                        if(count($linkcateids) > 0){
                            $linkcateids_placeholder = implode(',', array_map('intval', $linkcateids));
                                        $linklistsql = "
                                        SELECT * FROM ppz_row 
                                        WHERE rowfl IN ($linkcateids_placeholder) AND rowyes=4 
                                        ORDER BY 
                                            CASE WHEN rowtop=2 THEN 0 ELSE 1 END,
                                            rowid DESC
                                        LIMIT 8
                                    ";
                                    $linklistretval=mysqli_query($conn,$linklistsql);
                                    if(mysqli_num_rows($linklistretval) > 0){
                                        $has_content = true;
                                        while($linklist = $linklistretval->fetch_array()){
                                            $rowidxx=$linklist['rowid'];//文章id
                                            $rowtexexx=$linklist['rowtexe'];//文章标题
                                            $rowimgxx=$linklist['rowimg'];//文章图片
                                            $rowtimexx=$linklist['rowtime'];//文章时间
                                            $rowadminxx=$linklist['rowadmin'];//文章作者
                                            $rowbigtextxx=$linklist['rowbigtext'];//文章内容
                                            $rowifxx=$linklist['rowif'];//文章类型，1图文，2相册，3视频
                                            $desc=$linklist['videotext'];//描述，仅限视频和相册类型的文章
                                            $rowtopindex=$linklist['rowtop'];//是否置顶，1默认不置顶，2置顶，3,热门，4精华
                                            $indexvipif=$linklist['rowvip'];//文章预览权限，1.所有人，2.登录可见，3.VIP会员可见
                                            $index_dow=$linklist['rowdw'];//下载

                                            if (!empty($index_dow)){ //是否存在下载链接，存在则显示
                                                $index_dow = '<div class="body-ul-li-div-download"><i class="fa fa-arrow-circle-down"></i>下载</div>';
                                            }else{ $index_dow=''; }

                                            if($indexvipif==3){
                                                $indexvipif='<i id="vipico"></i>';
                                            }else{
                                                $indexvipif='';
                                            }

                                            if($rowtopindex==4){
                                                $rowtopindex="<span class='badge badge-success'>精华</span>";
                                            }elseif($rowtopindex==3){
                                                $rowtopindex="<span class='badge badge-danger'>热门</span>";
                                            }elseif($rowtopindex==2){
                                                $rowtopindex="<span class='badge badge-warning'>置顶</span>";
                                            }else{
                                                $rowtopindex="";
                                            }
                                            if($rowifxx==2){
                                                if(!empty($rowbigtextxx)){
                                                    $rowifindexmunarr = explode('|', $rowbigtextxx);
                                                    $rowifindexmun="<span class='badge-r'><i class='fa fa-file-photo-o'></i>".count($rowifindexmunarr)."P</span>";//获取图片数量
                                                    $rowifindexmunv="";
                                                }else{
                                                    $rowifindexmun="<span class='badge-r'><i class='fa fa-file-photo-o'></i>0P</span>";
                                                    $rowifindexmunv="";
                                                }                                            
                                            }elseif($rowifxx==3){
                                                if(!empty($rowbigtextxx)){
                                                    $rowifindexmunarr = explode('|', $rowbigtextxx);
                                                    $rowifindexmunv= "<span class='badge-r'><i class='fa fa-file-video-o'></i>".count($rowifindexmunarr)."V</span>";//获取视频数量
                                                    $rowifindexmun="";
                                                }else{
                                                    $rowifindexmunv="<span class='badge-r'><i class='fa fa-file-video-o'></i>0V</span>";
                                                    $rowifindexmun="";
                                                }     
                                            }else{
                                                $rowifindexmun="";
                                                $rowifindexmunv="";
                                            }
                                            //格式化时间
                                            $rowtimexx=date('Y-m-d',strtotime($rowtimexx));
                                            //获取文章图片
                                            if($rowifxx==1){
                                                if(!empty($rowimgxx)){
                                                    $rowimgxx=$rowimgxx;
                                                }else{
                                                    if(!empty($rowbigtextxx)){
                                                        //获取内容里面的第一张图片作为封面
                                                        $pattern = '/<img[^>]+src="([^"]+)"/i';
                                                        if (preg_match($pattern, $rowbigtextxx, $matches)) {
                                                            $rowimgxx = $matches[1];
                                                        } else {
                                                            $rowimgxx = "/images/web/null.jpg";  // 如果没有匹配到图片 URL，则使用默认图片
                                                        }
                                                    }else{
                                                        $rowimgxx='/images/web/null.jpg';
                                                    }
                                                }
                                            }elseif($rowifxx==2){
                                                if(!empty($rowimgxx)){
                                                    $rowimgxx=$rowimgxx;
                                                }else{
                                                    if(!empty($rowbigtextxx)){

                                                        $arrayrowf = explode("|",$rowbigtextxx);   
                                                        $firstValue = null;   
                                                        foreach ($arrayrowf as $value) {  
                                                            if ($firstValue === null) {  
                                                                $firstValue = $value;  
                                                            }  
                                                            break; }
                                                        $rowimgxx=$firstValue;

                                                    }else{
                                                    $rowimgxx='/images/web/null.jpg'; 
                                                    }
                                                }
                                            }else{
                                                if(!empty($rowimgxx)){
                                                    $rowimgxx=$rowimgxx;
                                                }else{
                                                    $rowimgxx='/images/web/null.jpg';
                                                }
                                            }
                                            //获取会员信息
                                            $rowadminxxsql="select * from ppz_newusername where uid = $rowadminxx";
                                            $rowadminxxretval=mysqli_query($conn,$rowadminxxsql);
                                            if(mysqli_num_rows($rowadminxxretval) > 0){
                                                while($rowadminxx = $rowadminxxretval->fetch_array()){
                                                    $rowadminxxname=$rowadminxx['uname'];//会员昵称
                                                    $rowuidxx=$rowadminxx['uid'];
                                                    $rowadminxxuim=$rowadminxx['uimg'];//头像
                                                }
                                            }else{
                                                $rowadminxxname='未知会员';
                                                $rowuidxx="";
                                                $rowadminxxuim="";
                                            }

                                            if(!empty($rowadminxxuim)){
                                                $rowadminxxuim=$rowadminxxuim;
                                            }else{
                                                $rowadminxxuim='/images/web/default.jpg';
                                            }

                                            if($rowifxx==1){
                                                if(!empty($rowbigtextxx)){
                                                    //获取内容里面的前120个纯文字(utf-8编码)
                                                    $rowdesc=mb_substr(strip_tags($rowbigtextxx),0,180,'utf-8')."……";
                                                }else{
                                                    $rowdesc="暂无描述内容……";
                                                }
                                            }else{
                                                if(!empty($desc)){
                                                    //获取内容里面的前120个纯文字(utf-8编码)
                                                    $rowdesc=mb_substr(strip_tags($desc),0,180,'utf-8')."……";
                                                }else{
                                                    $rowdesc="暂无描述内容……";
                                                }
                                            }

                                            

                                            if($linkimgif==3){
        
                                                    echo ' <div class="module-list-big">
                                                    <div class="module-list-img-big" style="background-image:url('.$rowimgxx.');"><a href="/show.php?id='.$rowidxx.'">'.$rowtopindex.$rowifindexmun.$rowifindexmunv.$index_dow.'</a></div>
                                                    <div class="module-list-right">
                                                    <div class="module-list-text-big"><a href="/show.php?id='.$rowidxx.'">'.$indexvipif.$rowtexexx.'</a></div>
                                                    <div class="module-list-desc-big">'.$rowdesc.'</div>
                                                    <div class="module-list-time-big"><a href="/user.php?id='.$rowuidxx.'" target="_blank"><div class="indexuimg" style="background-image:url('.$rowadminxxuim.');"></div>'.$rowadminxxname.'</a><span>'.$rowtimexx.'</span></div>
                                                    </div></div>';
                        
                                                
                                            }else{
                                                echo ' <div class="module-list">
                                                    <div class="module-list-img" style="background-image:url('.$rowimgxx.');"><a href="/show.php?id='.$rowidxx.'">'.$rowtopindex.$rowifindexmun.$rowifindexmunv.$index_dow.'</a></div>
                                                    <div class="module-list-text"><a href="/show.php?id='.$rowidxx.'">'.$indexvipif.$rowtexexx.'</a></div>
                                                    <div class="module-list-time"><a href="/user.php?id='.$rowuidxx.'" target="_blank"><div class="indexuimg" style="background-image:url('.$rowadminxxuim.');"></div>'.$rowadminxxname.'</a><span>'.$rowtimexx.'</span></div>
                                                </div>';
                                            }
                                            
                                        }

                                    }
                            
                        }

                        if (!$has_content) {
                            echo '<div class="nulldiv">没有找到文章~</div>';
                        }
    
                    }else{
                        echo '<div class="nulldiv">没有找到分类~</div>';
                    }

                echo '</div>

            </div>';
            if ($indexmun == 2) {
                //获取文章总数
                $indexmunsqlrow = "select rowid from ppz_row";
                $indexmunretvalrow=mysqli_query($conn,$indexmunsqlrow);
                if(mysqli_num_rows($indexmunretvalrow) < 1){
                    $indexmunrow=0;
                }else{
                    $indexmunrow=mysqli_num_rows($indexmunretvalrow);
                }
                //获取VIP文章总数
                $indexmunsqlvip = "select rowid from ppz_row where rowvip = 3";
                $indexmunretvalvip=mysqli_query($conn,$indexmunsqlvip);
                if(mysqli_num_rows($indexmunretvalvip) < 1){
                    $indexmunvip=0;
                }else{
                    $indexmunvip=mysqli_num_rows($indexmunretvalvip);
                }
                //获取会员数量
                $indexmunsqluser = "select uid from ppz_newusername";
                $indexmunretvaluser=mysqli_query($conn,$indexmunsqluser);
                if(mysqli_num_rows($indexmunretvaluser) < 1){ 
                    $indexmunuser=0;
                }else{
                    $indexmunuser=mysqli_num_rows($indexmunretvaluser);
                }
                //获取vip会员数量（会员时间大于当前时间的）
                $indexmunsqlvipuser = "select uid from ppz_newusername where uviptime > '".date('Y-m-d H:i:s')."'";
                $indexmunretvalvipuser=mysqli_query($conn,$indexmunsqlvipuser);
                if(mysqli_num_rows($indexmunretvalvipuser) < 1){ 
                    $indexmunvipuser=0;
                }else{
                    $indexmunvipuser=mysqli_num_rows($indexmunretvalvipuser);
                }

                echo '
                <div class="indexmun">
                    <div class="module color-bg-hover">
                        <div class="module-title-user">
                            <li><i class="fa fa-folder-open" aria-hidden="true"></i><span>'.$indexmunrow.'</span>总文章数</li>
                            <li><i class="fa fa-folder" aria-hidden="true"></i><span>'.$indexmunvip.'</span>VIP文章数</li>
                            <li><i class="fa fa-users" aria-hidden="true"></i><span>'.$indexmunuser.'</span>总用户数</li>
                            <li><i class="fa fa-id-card" aria-hidden="true"></i><span>'.$indexmunvipuser.'</span>VIP用户数</li>
                        </div>
                    </div>
                </div>
                ';
            }
            
        }
    }else{
        echo '<div class="nulldiv">空空如也~</div>';
    }
    
echo '</div>';
?>



<?php @include $_SERVER['DOCUMENT_ROOT'].'/inc/right.php';?>

</div>
<?php @include $_SERVER['DOCUMENT_ROOT'].'/inc/footer.php';?>
<?php 
  if (empty($ppzusername)){ echo '<script src="/style/js/login.js" type="text/javascript"></script>';} 
?>
<?php echo $adson_yxj.$adson_left.$adson_right?>
<?php @include $_SERVER['DOCUMENT_ROOT'].'/wap/index.php';?>
</body>
</html>