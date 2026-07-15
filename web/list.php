<!DOCTYPE html>
<html lang="zh-CN" translate="no">
<head>
<?php
function extractPlainTextFromHTML($html) {  
    $dom = new DOMDocument();  
    // 使用libxml_use_internal_errors(true)来避免HTML错误导致警告  
    libxml_use_internal_errors(true);  
    $dom->loadHTML('<?xml version="1.0" encoding="UTF-8"?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);  
    libxml_clear_errors();  
  
    $textContent = '';  
    // 遍历DOM中的所有文本节点  
    $xpath = new DOMXPath($dom);  
    foreach ($xpath->query('//text()') as $textNode) {  
        $textContent .= $textNode->nodeValue;  
    }  
  
    // 使用strip_tags去除HTML标签（但在这里其实不需要，因为我们只取了文本节点）  
    $plainText = $textContent;  
  
    // 去除多余的空白字符  
    $plainText = preg_replace('/\s+/', ' ', $plainText);  
    $plainText = trim($plainText);  
  
    return $plainText;  
}  

include $_SERVER['DOCUMENT_ROOT'].'/inc/inc.php';//通用

if (isset($_GET['id'])) {
    $liid=$_GET['id'];//获取列表id
}else{
    $liid="";
}


if (isset($_GET['tag'])) {
    $tag=$_GET['tag'];//获取分类
}else{
    $tag="";
}

if (isset($_GET['order'])) {
    $order=$_GET['order'];//获取排序方式
}else{
    $order="";
}


if(is_numeric($liid) && $liid > 0 && $liid !=="" && !is_null($liid)){
$id=$liid;
}else{
$id=1;
};

if ($order==1){
    $ordertxt="rowid desc LIMIT";
    $ortext="最新发布";
    $orpage="&order=1";
}else if ($order==2){
    $ordertxt=" roweye desc LIMIT";
    $ortext="热门浏览";
    $orpage="&order=2";
}else if ($order==3){
    $ordertxt=" rowid asc LIMIT";
    $ortext="最早发布";
    $orpage="&order=3";
}else if ($order==4){
    $ordertxt="roweye asc LIMIT";
    $ortext="冷门浏览";
    $orpage="&order=4";
}else if ($order==5){
    $ordertxt="rowsc desc LIMIT";
    $ortext="最多收藏";
    $orpage="&order=5";
}else if ($order==6){
    $ordertxt="rowsc asc LIMIT";
    $ortext="最少收藏";
    $orpage="&order=6";
}else{
    $ordertxt="rowid desc LIMIT";
    $ortext="最新发布";
    $orpage="";
};


$lisql = "select * from ppz_link where linkid = $id"; //查询列表设置
$liretval=mysqli_query($conn,$lisql);
if(mysqli_num_rows($liretval) < 1){ 
    $linkimg=1;
    $linkint=1;
    $linknamet="神秘领域";
}else{
    
    $liq = $conn->query($lisql);
    while($li = $liq ->fetch_array()){
        $linkimg=$li['linkimg'];//列表设置，1为竖图，2为横图,3资讯
        $linkint=$li['linkint'];//列表图片一行展示数量，1为一行4张，2为一行3张 
        $linknamet=$li['linkname']; //列表名称
    };
    
};

$linkimgsz=$linkimg; 
$linkimgsize=$linkint; 

if ($linkimgsz==3){
    $lbsz=3;
    $lb=3;
    $txts=3;
    $num_rec_per_page=15;   // 资讯页，每页显示数量
}else{
$num_rec_per_page=24;   // 其余页，每页显示数量
$txts='';
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
}


if (is_numeric($tag) && $tag!=="" &&!is_null($tag)){
    $flid = array($tag);
    $tagtxt="&tag=".$tag."";
    $tagthen="&tag=".$tag;
}else{
$liflsql = "select * from ppz_fl where fllinkid = $id"; //查询分类
$liflretval=mysqli_query($conn,$liflsql);
if(mysqli_num_rows($liflretval) < 1){ 
    $flid = array(0);
}else{
    $flid = array(); 
    $liflq = $conn->query($liflsql);
    while($lifl = $liflq ->fetch_array()){
    $flid[]=$lifl['flid'];
    };
}
$tagthen="";
};
?>
<meta charset="utf-8">
<title><?php echo $linknamet; ?> - <?php echo $webtext;?>丨<?php echo $webby;?></title>
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
$ADS=200;
$ADSPAGE=2;
@include $_SERVER['DOCUMENT_ROOT'].'/api/indexads.php';//广告
echo $adson_js;
?>
<?php echo $adson_hf?>
<?php

//分页
if (isset($_GET["p"])) {
    $getp=$_GET["p"];//获取GET传参P
}else{ 
    $getp=""; 
}
if (isset($_POST["p"])) {
    $tpx=$_POST["p"];//获取POST传参P
}else{ 
    $tpx=""; 
}

/*判断参数P是否为空，且是否是数字*/
if (isset($tpx) && is_numeric($tpx) && $tpx>=1 && !is_null($tpx) ){ 
    $pa = $tpx;
} else { 
    if (isset($getp) && is_numeric($getp) && $getp>=1 && !is_null($getp)){
        $pa = $getp;
    }else{
        $pa=1; 
    }

}; 

$flid_list = implode(',',$flid); // 将数组转换为逗号分隔的字符串  

$sqlll = "SELECT * FROM ppz_row WHERE rowyes = 4 AND rowfl IN ($flid_list)"; //链接数据表
$rs_result = mysqli_query($conn,$sqlll); //查询数据
$total_records = mysqli_num_rows($rs_result);  // 统计数据总数
$total_pages = ceil($total_records / $num_rec_per_page);  // 计算总页数

if ($total_pages < $pa){
    $p=1;
    }else{
    $p=$pa; 
}

$start_from = ($p-1) * $num_rec_per_page; 

$rowsql = "select * from ppz_row WHERE rowyes = 4 AND rowfl IN ($flid_list) ORDER BY CASE WHEN rowtop = 2 THEN 0 ELSE 1 END, $ordertxt $start_from, $num_rec_per_page";
$rowretval=mysqli_query($conn,$rowsql);

if(mysqli_num_rows($rowretval) < 1){ 
    $rownull=1; //没有文章
}else{
    $rownull=2;//有文章
};
?>

<div class="body-div">

    <div class="body-left">
        <div class="listdiv">
        <div class="listdivl">    <span><?php echo $linknamet;?></span>
            <ul>
           
<?php
$liflsql2 = "select * from ppz_fl where fllinkid = $id order by flid asc"; //查询分类
$liflretval2=mysqli_query($conn,$liflsql2);
if(mysqli_num_rows($liflretval2) < 1){ 
    $flid2 = array(0);
    $flnamet = array('空白分类'); 
}else{
    $flid2 = array(); 
    $flnamet = array(); 
    $liflq2 = $conn->query($liflsql2);
    while($lifl2 = $liflq2 ->fetch_array()){
    $flid2[]=$lifl2['flid'];
    $flnamet[]=$lifl2['flname'];
    };
}

if ( ($tag==0 && $tag==null && $tag=="") || !is_numeric($tag) ){
    $allclass = 'listlithen';
}else{
    $allclass = 'listlit';
};

echo '
<a href="list.php?id='.$id.'"><div class="li '.$allclass.'">全部</div></a>
';

// 将数组$flnamet和$flid循环输出
for($i=0;$i<count($flid2);$i++){
if ($tag==$flid2[$i] && $tag !=="" && !is_null($tag) && $tag!=null && is_numeric($tag)){
    $class = 'listlithen';
}else{
    $class = 'listlit';
};
echo '<a href="list.php?id='.$id.'&tag='.$flid2[$i].'"><div class="li '.$class.'">'. $flnamet[$i].'</div></a>';
};
?>

            </ul></div>

                <div class="listdivr">
                 <div class="dropdown">
                <button class="dropbtn"><?php echo $ortext;?><i class="fa fa-sort"></i></button>
                <div class="dropdown-content">
                    <a href="?id=<?php echo $id;?>&order=1<?php echo $tagthen;?>">最新发布</a>
                    <a href="?id=<?php echo $id;?>&order=2<?php echo $tagthen;?>">热门浏览</a>
                    <a href="?id=<?php echo $id;?>&order=5<?php echo $tagthen;?>">最多收藏</a>
                    <a href="?id=<?php echo $id;?>&order=3<?php echo $tagthen;?>">最早发布</a>
                    <a href="?id=<?php echo $id;?>&order=4<?php echo $tagthen;?>">冷门浏览</a>
                    <a href="?id=<?php echo $id;?>&order=6<?php echo $tagthen;?>">最少收藏</a>
                </div>
                </div>
            </div> 

        </div>
        <ul id="uheight" class="body-ul<?php if($linkimgsize==1){}else{ if($linkimgsize==2){echo '2';}else{}  }?>">

<?php
if ($rownull == 1){
echo "<div class='nulldiv'>空空如也~</div>";//没有文章时显示
}else{
    
//数据库里有内容，执行文章展示查询
        $rowquery = $conn->query($rowsql);
        while($lrow = $rowquery->fetch_array()){
        //判断文章置顶等级
            if ($lrow['rowtop'] == 2){
                $rowtop = "置顶";
                $top = "top";
            }else if($lrow['rowtop'] == 3){
                $rowtop = "热门";
                $top = "hot";
            }else if($lrow['rowtop'] == 4){
                $rowtop = "精华";
                $top = "elite";
            }else{
                $rowtop="";
                $top = "";
            };

            if ($lrow['rowvip']==3){
                $rowvipico='<i id="vipico"></i>';
            }else{
                $rowvipico="";
            }

         if ($lrow['rowimg']=="" || is_null($lrow['rowimg']) || $lrow['rowimg'] == null ){ //判断是否含有文章封面，没有则选用文章内第一张图片作为封面


            if ($lrow['rowif']==2){ //判断是否是相册类型，相册类型则选数组第一张图片

                if ($lrow['rowbigtext']==''||$lrow['rowbigtext']==null||is_null($lrow['rowbigtext'])){
                    $rowimg = "/images/web/null.jpg"; 
                }else{
                    $rowimgg=$lrow['rowbigtext'];
                    $arrayrowf = explode("|",$rowimgg); 
                    $firstValue = null;   
                    foreach ($arrayrowf as $value) {  
                        if ($firstValue === null) {  
                            $firstValue = $value;  
                        }  
                        break; }
                    $rowimg=$firstValue;
                        
                }
                


            }else{
                // 使用正则表达式提取图片 URL
                $pattern = '/<img[^>]+src="([^"]+)"/i';
                if (preg_match($pattern, $lrow['rowbigtext'], $matches)) {
                    $imageUrl = $matches[1];
                    $rowimg = $imageUrl; // 如果匹配到图片 URL，则使用该 URL
                } else {
                    $rowimg = "/images/web/null.jpg";  // 如果没有匹配到图片 URL，则使用默认图片
                }
            }


 

             }else{
            $rowimg = $lrow['rowimg'];
             }
          
             if ($lrow['rowif']==2){
                if ($lrow['rowbigtext']!==''&&$lrow['rowbigtext']!==null&&!is_null($lrow['rowbigtext'])){
                $arrimgx=$lrow['rowbigtext'];
                $arrayrowfx = explode("|",$arrimgx);
                $arrsize=count($arrayrowfx);
                $arrvtxt="<div class='arrsize nocopy'><i class='fa fa-file-photo-o'></i>".$arrsize."P</div>";
                }
             }else if ($lrow['rowif']==3){
                if ($lrow['rowbigtext']!==''&&$lrow['rowbigtext']!==null&&!is_null($lrow['rowbigtext'])){
                    $arrimgxx=$lrow['rowbigtext'];
                    $arrayrowfxx = explode("|",$arrimgxx);
                    $arrsizex=count($arrayrowfxx);
                    $arrvtxt="<div class='arrsize nocopy'><i class='fa fa-file-video-o'></i>".$arrsizex."V</div>";
                 }
             }else{
                $arrvtxt="";
             }



           $uid=$lrow['rowadmin'];//获取文章发布者id
           $rowdate = date('Y-m-d',strtotime($lrow['rowtime']));//获取文章发布时间
           $rowbigtext=$lrow['rowbigtext'];//获取文章内容
           $string = strip_tags($rowbigtext); 
            $newString = str_replace(' ', '', $string);
           if ($newString==''||$newString==null||is_null($newString)){
            $rowtxt="<p class='pnull'>没有找到文字摘要！</p>";
           }else{
            $rowt=mb_substr($newString,0,70,'utf-8');
            $rowtxt="<p>".$rowt."…</p>";
           }

         


           //获取文章发布者信息
           $adminsql = "select * from ppz_newusername where binary uid = $uid";//发布者信息查询表
           $adminretval=mysqli_query($conn,$adminsql);
             if(mysqli_num_rows($adminretval) < 1){  //用户不存在，返回错误代码
                          $adneme="未知用户";
                          $adid="1";
                          $adimg="/images/web/default.jpg";
            }else{
                        //用户存在，获取用户信息
                        $adminquery = $conn->query($adminsql);
                        while($admin = $adminquery->fetch_array()){
                            $adneme=$admin['uname'];
                            $adid=$admin['uid'];
                            $img=$admin['uimg'];
                            if ($img==null || $img==""){
                                $adimg="/images/web/default.jpg";
                            }else{
                                $adimg="$img";
                            };
                            
                        };
                        
               };


          //获取分类
          if ($lrow['rowfl'] < 1){
            $flname="未知分类";
            $flid=0;
            $fllinkid=$id;
         }else{
            $flsql = "select * from ppz_fl where binary flid = $lrow[rowfl]";//获取分类
            $flretval=mysqli_query($conn,$flsql);
            if(mysqli_num_rows($flretval) < 1){
            }else{
            
                $flquery = $conn->query($flsql);
                    while($flrow = $flquery->fetch_array()){
                    $flid=$flrow['flid'];//获取分类id
                    $flname=$flrow['flname'];//获取分类名称
                    $fllinkid=$flrow['fllinkid'];
                    };
            };

         };

                $sqlpl = "SELECT * FROM ppz_commentary where plrowid = $lrow[rowid]"; //链接评论数据表
                $pl_result = mysqli_query($conn,$sqlpl); //查询评论数据
                $pl_records = mysqli_num_rows($pl_result);  // 统计评论总数

                $weye=$lrow['roweye'];

                
                if ($weye > 999999998){
                    $weyetext="10亿+";
                }else if ($weye < 999999998 && $weye >= 100000000){
                    $weyetext=number_format($weye/100000000,0)."亿+";
                }else if ($weye < 100000000 && $weye >= 10000000){
                    $weyetext=number_format($weye/10000000,0)."千万+";
                }else if ($weye < 10000000 && $weye >= 1000000){
                    $weyetext=number_format($weye/1000000,0)."百万+";
                }else if ($weye < 1000000 && $weye >= 10000){
                    $weyetext=number_format($weye/10000,0)."万+";
                }else{
                    $weyetext=$weye;
                }

                if ($pl_records > 99){
                    $plrecordstext="99+";
                }else{
                    $plrecordstext=$pl_records;
                }

            //输出HTML主体内容
            echo ' <li class="body-ul-li'.$lb.'"><div class="body-ul-li-div'.$lbsz.'">';

            if ($rowtop!=="" && $top!=="" && !is_null($rowtop)){
                echo '<div class="body-ul-li-div-'.$top.'">'.$rowtop.'</div>';//置顶等级
            };
             if ($lrow['rowdw'] !== "" && !is_null($lrow['rowdw'])){ //是否存在下载链接，存在则显示
            echo '<div class="body-ul-li-div-download"><i class="fa fa-arrow-circle-down"></i>下载</div>';
             };

             if ($lrow['rowif']==2||$lrow['rowif']==3){echo $arrvtxt;}

             echo ' <a href="show.php?id='.$lrow['rowid'].'" class="images-a"><img id="postimgnull" class="post-thumb-img'.$lbsz.'"  src="'.$rowimg.'" /></a></div>
               
             <div class="body-ul-li-text'.$txts.'">
                        <h2><a href="show.php?id='.$lrow['rowid'].'">'.$rowvipico.$lrow['rowtexe'].'</a></h2>';
                      if ($linkimgsz==3){
                        if($lrow['rowif']==1){
                            echo $rowtxt;
                        }elseif($lrow['rowif']==2){
                            if($lrow['videotext']!==""&&$lrow['videotext']!==null&&!is_null($lrow['videotext'])){
                                echo '<p>'.mb_substr(extractPlainTextFromHTML($lrow['videotext']),0,70,'utf-8').'</p>';
                            }else{
                                echo '<p>该相册暂无摘要内容</p>';
                            }
                        }elseif($lrow['rowif']==3){
                            if($lrow['videotext']!==""&&$lrow['videotext']!==null&&!is_null($lrow['videotext'])){
                                echo '<p>'.mb_substr(extractPlainTextFromHTML($lrow['videotext']),0,70,'utf-8').'</p>';
                            }else{
                                echo '<p>该视频暂无摘要内容</p>';
                            }
                        }else{
                            echo '错误参数！';
                        }
                        
                    }
                  echo'      <div class="post-list-meta-box">
                        <a href="list.php?id='.$fllinkid.'&tag='.$flid.'">'.$flname.'</a>
                        <ul class="post-list-meta">
                        <li><i class="fa fa-commenting"></i>'.$plrecordstext.'</li>
                        <li><i class="fa fa-eye"></i>'.$weyetext.'</li>
    
                        </ul>
                        </div>
                        <div class="list-footer">
                        <a href="user.php?id='.$adid.'"><div class="admin">
                             <div class="admin-img" style="background-image: url('.$adimg.');" ></div>'.$adneme.'
                        </div></a>
                        <span>'.$rowdate.'</span>
                        </div>
                
                    </div>
    
                </li>';

     
        };

    
};
if (isset($tagtxt)) {
    $tagtxt=$tagtxt;
}else{
    $tagtxt="";
}
?>
</ul>

            <div class="page">
              <div class="page-left">第<?php echo $p;?>页 / 共<?php echo $total_pages;?>页<div class="tpage"><form name="page" onsubmit="return checkformpage()" method="post" ><input name="p" placeholder="跳页"/></form></div></div>
                <div class="page-right">
                <a href="?id=<?php echo $id;?>&p=1<?php echo $tagtxt;?><?php echo $orpage;?>" class="<?php if ($p==1 || $p < 1){echo "page-no-button nocopy";}else{echo "page-button nocopy";}?>">首页</a>
                <a <?php if ($p==1 || $p < 1){}else{echo "href='?id=".$id."&p=".($p-1)."".$tagtxt."".$orpage."'";}?> class="<?php if ($p==1 || $p < 1){echo "page-no-button nocopy";}else{echo "page-button nocopy";}?>">上一页</a>
                <a <?php if ($total_pages<$p+1){}else{echo "href='?id=".$id."&p=".($p+1)."".$tagtxt."".$orpage."'";}?> class="<?php if ($total_pages<$p+1){echo "page-no-button nocopy";}else{echo "page-button nocopy";}?>">下一页</a>
                <a <?php if ($total_pages<$p+1){}else{echo "href='?id=".$id."&p=".$total_pages."".$tagtxt."".$orpage."'";} ?> class="<?php if ($total_pages<$p+1){echo "page-no-button nocopy";}else{echo "page-button nocopy";}?>">尾页</a>
                </div>
            </div>

    </div>

<?php include $_SERVER['DOCUMENT_ROOT'].'/inc/right.php';?>


</div>
<?php include $_SERVER['DOCUMENT_ROOT'].'/inc/footer.php';?>
<?php 
if ($ppzusername == "" ){ echo '<script src="/style/js/login.js" type="text/javascript"></script>';} 
?>
<?php echo $adson_yxj.$adson_left.$adson_right?>
<?php @include $_SERVER['DOCUMENT_ROOT'].'/wap/index.php';?>
</body>
</html>