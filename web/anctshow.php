<!DOCTYPE html>
<html lang="zh-CN" translate="no">
<head>
<?php
include $_SERVER['DOCUMENT_ROOT'].'/inc/inc.php';//链接数据库

if (isset($_GET['id'])) {
    $rowidif=$_GET['id'];
}else{
    $rowidif="";
}
if (isset($_GET['t'])) {
$one=$_GET['t'];
}else{
$one="";
}
if ($ppzusername == "" || is_null($ppzusername) || $ppzusername == false || empty($ppzusername) ||!isset($ppzusername) ||!is_numeric($ppzusername)){
    $sessionyes=false;
}else{
    $sessionyes=true;
};

if ($rowidif=="" || $rowidif == null || $rowidif == false || is_null($rowidif)||empty($rowidif) ||!isset($rowidif) || !is_numeric($rowidif)){
    $rowid=0;
}else{
    $rowid=$rowidif;
}

//判断id是否是数字且是否不为空
if(is_numeric($rowid) && !empty($rowid) && $rowid>0 && $rowid!=="" && $rowid !==null && !is_null($rowid)){
    $id=$rowid;//获取id
    $rowsql = "select * from ppz_announcement  WHERE ggid=$id";//获取公告数据库表
    $rowretval=mysqli_query($conn,$rowsql);
    if(mysqli_num_rows($rowretval) <= 0){ 
        $rownull=1; //没有公告
        $ggtext="没有找到公告";
    }else{
                                $rownull=2;//有公告
                                $rowquery = $conn->query($rowsql);
                                while($lrow = $rowquery->fetch_array()){
                                        $ggtext=$lrow['ggtext'];//公告标题
                                        $ggbigtext=$lrow['ggbigtext'];//公告内容
                                        $ggrowid=$lrow['ggrowid'];//发布者id
                                        $ggtime=$lrow['ggtime'];//发布时间
                                }
    }
}else{
    $rownull=1; //没有公告
    $ggtext="没有找到公告";
}
?>
<meta charset="utf-8">
<title><?php echo $ggtext;?> - <?php echo $webtext;?>丨<?php echo $webby;?></title>
<meta name="keywords" content="<?php echo $webpass;?>" />
<meta name="description" content="<?php echo $webvar;?>" />
<link rel="icon" href="/favicon.ico"/>
<?php include $_SERVER['DOCUMENT_ROOT'].'/inc/style.php';?>
<link type="text/css" rel="stylesheet" href="/style/css/font-awesome-4.7.0/css/font-awesome.min.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="/style/js/jquery-3.5.1.min.js" type="text/javascript"></script>
<script src="/style/js/input.js" type="text/javascript"></script>
<script src="/style/js/alert.js" type="text/javascript"></script>
<script src="/style/highlight/highlight.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="/style/highlight/arta.css">

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
<div  id="uheight"  class="body-left">
<div class="rowbg">
<?php

if ($rownull==2){

if ($ggrowid !==0 && $ggrowid !=="" && $ggrowid!== null && !is_null($ggrowid)){
    $uer = "select * from ppz_newusername where binary uid = $ggrowid";//查询数据库，判断用户名是否存在
    $uerquery=mysqli_query($conn,$uer);
    if(mysqli_num_rows($uerquery) !== 1){

    }else{
        $uerqun= $conn->query($uer);
                while($xrow = $uerqun->fetch_array()){
                $uername=$xrow['uname'];//作者昵称
                $uerid=$xrow['uid'];//作者id
                }
    };

    echo '
    <link rel="stylesheet" href="/style/PhotoSwipe/viewer.min.css">
    <script src="/style/PhotoSwipe/viewer.min.js"></script>
    <div  class="rowtxt">'.$ggtext.'</div>
            <div class="rowadmin">
            <div class="rowl">
            <span><i class="fa fa-user-circle"></i><a href="user.php?id='.$uerid.'" target="_blank">'.$uername.'</a></span>
            <span><i class="fa fa-clock-o"></i>'.date('Y年m月d日 H:i:s', strtotime($ggtime)).'</span>
            </div>
            </div>
            <div id="imgrow" class="rowdiv">'.$ggbigtext.'</div>
            <script>var viewer = new Viewer(document.getElementById("imgrow"));</script>';

}
}else{
    echo "<div class='nulldiv'>没有公告~</div>";
}

?>
</div>
<?php echo $adson_rowhf;?>
<?php if ($rownull==2){ ?>
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
if  (isset($_GET["p"])){ 
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

$plsqlll = "SELECT * FROM ppz_ggcommentary where binary plrowid = $id"; //链接数据表
$plrs_result = mysqli_query($conn,$plsqlll); //查询数据
$plmu = mysqli_num_rows($plrs_result);  // 统计数据总数
$total_pages = ceil($plmu / $num_rec_per_page);  // 计算总页数

if ($total_pages < $pa){
$p=1;
}else{
$p=$pa; 
}

$start_from = ($p-1) * $num_rec_per_page; 

$plsql = "select * from ppz_ggcommentary where binary plrowid = $id ORDER BY plid DESC LIMIT $start_from, $num_rec_per_page";//获取评论
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

        if ($pltops==""||is_null($pltops)||empty($pltops)||$pltops==null){
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

        if ($pluimg==""||is_null($pluimg)||empty($pluimg)){
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


$repsqlx = "select * from ppz_ggreply where repplid = $plid"; //获取回复
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
  
$repsql = "select * from ppz_ggreply where repplid = $plid ORDER BY repid DESC LIMIT 0,5"; //获取回复,并只显示5篇
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
    echo '<a href="plreply.php?id='.$plid.'&type=999" target="_blank" class="repall nocopy">查看更多回复</a>'; 
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
echo '<script type="text/javascript" src="/style/js/ggreply.js"></script>';
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
<?php } ?>
</div>
<?php include $_SERVER['DOCUMENT_ROOT'].'/inc/right.php';?>
</div>
<?php include $_SERVER['DOCUMENT_ROOT'].'/inc/footer.php'; ?>
<?php
if ($sessionyes==true){
echo '<script src="/style/js/ggreppl.js" type="text/javascript"></script>';
};
if ($sessionyes==false){
    echo '<script src="/style/js/login.js" type="text/javascript"></script>';
};
echo '
<script src="/style/js/copy/clipboard.js" type="text/javascript"></script>
<script>
hljs.highlightAll();
const copyBtn = new ClipboardJS(".article-aff");
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

?>
<?php echo $adson_yxj.$adson_left.$adson_right?>
<?php @include $_SERVER['DOCUMENT_ROOT'].'/wap/index.php';?>
</body>
</html>