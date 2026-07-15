<?php
header('Content-Type: application/json');
include $_SERVER['DOCUMENT_ROOT'] . '/inc/conn.php';//数据库连接
$webcustomsqlx = "SELECT * FROM ppz_diy WHERE diyid = 1";
$webcustomretvalx = mysqli_query($conn, $webcustomsqlx);
if (mysqli_num_rows($webcustomretvalx) !== 1) {
    echo json_encode([], JSON_UNESCAPED_UNICODE);
} else {
    $webcustomqueryx = $conn->query($webcustomsqlx);
    while ($webcustomx = $webcustomqueryx->fetch_array()) {
        $indexflexx = $webcustomx['diyindex'];     // 首页版面
        $indeximagex = $webcustomx['image'];       // 自定义轮播图JSON（字符串）
        $indexcarouselx = $webcustomx['carousel']; // 轮播图模式：1为加“热门”内容，2为加"精华"内容，3为加"置顶"内容，4为自动最新内容，5为自动最高阅览量内容，6为自定义内容
    }
    if ($indexcarouselx == 6 && ($indexflexx == 3 || $indexflexx == 2) && !empty($indeximagex)) {
        // 先解码原始 JSON 字符串为 PHP 数组或对象
        $carouselArray = json_decode($indeximagex, true);
        // 确保解码成功且是数组
        if (is_array($carouselArray)) {
            echo json_encode($carouselArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } else {
            echo json_encode([], JSON_UNESCAPED_UNICODE);
        }
    }elseif(($indexcarouselx == 5||$indexcarouselx == 4||$indexcarouselx == 3||$indexcarouselx == 2||$indexcarouselx == 1) && ($indexflexx == 3 || $indexflexx == 2)){

        if($indexcarouselx == 5){
            $sqlrow="SELECT * FROM ppz_row WHERE rowyes = 4 ORDER BY roweye DESC LIMIT 10";//阅览次数前10
        }elseif($indexcarouselx == 4){
            $sqlrow="SELECT * FROM ppz_row WHERE rowyes = 4 ORDER BY rowid DESC LIMIT 10";//最新前10
        }elseif($indexcarouselx == 3){
            $sqlrow="SELECT * FROM ppz_row WHERE rowtop = 2 AND rowyes = 4 ORDER BY rowid DESC LIMIT 10";//置顶前10
        }elseif($indexcarouselx == 2){
            $sqlrow="SELECT * FROM ppz_row WHERE rowtop = 4 AND rowyes = 4 ORDER BY rowid DESC LIMIT 10";//精华前10
        }elseif($indexcarouselx == 1){
            $sqlrow="SELECT * FROM ppz_row WHERE rowtop = 3 AND rowyes = 4 ORDER BY rowid DESC LIMIT 10";//热门前10
        }else{
            $sqlrow="SELECT * FROM ppz_row WHERE rowyes = 4  ORDER BY rowid DESC LIMIT 10";//最新前10
        }

        $retvalrow=mysqli_query($conn,$sqlrow);
        if(mysqli_num_rows($retvalrow) > 0){
            while($rowrow = $retvalrow->fetch_array()){
                $rowid=$rowrow['rowid'];//文章id
                $rowtitle=$rowrow['rowtexe'];//文章标题
                $rowimg=$rowrow['rowimg'];//文章封面
                $rowtext=$rowrow['rowbigtext'];//文章内容
                $rowif=$rowrow['rowif'];//文章类型，1图文，2相册，3视频
                $videotext=$rowrow['videotext'];//摘要
                if($rowif==1&&!empty($rowtext)){
                    if(!empty($rowimg)){
                        $rowimg=$rowimg;
                    }else{
                        //获取内容里面的第一张图片作为封面
                        $pattern = '/<img[^>]+src="([^"]+)"/i';
                        if (preg_match($pattern, $rowtext, $matches)) {
                            $rowimg = $matches[1];
                        } else {
                            $rowimg = "/images/web/null.jpg";  // 如果没有匹配到图片 URL，则使用默认图片
                        }
                    } 
                }elseif($rowif==2&&!empty($rowtext)){
                    if(!empty($rowimg)){
                        $rowimg=$rowimg;
                    }else{
                    $arrayrowf = explode("|",$rowtext);   
                    $firstValue = null;   
                    foreach ($arrayrowf as $value) {  
                        if ($firstValue === null) {  
                            $firstValue = $value;  
                        }  
                        break; }
                    $rowimg=$firstValue;
                    }
                }else{
                    if(!empty($rowimg)){
                        $rowimg=$rowimg;
                    }else{
                        $rowimg = "/images/web/null.jpg";
                    }
                }
                if($rowif==2||$rowif==3){
                    if(!empty($videotext)){
                        $rowtextx = mb_substr(strip_tags($videotext), 0,28, 'UTF-8')."……";
                    }else{
                        $rowtextx="";
                    }
                }else{
                    if(!empty($rowtext)){
                        $rowtextx=mb_substr(strip_tags($rowtext), 0,28, 'UTF-8')."……";
                    }else{
                        $rowtextx="";
                    }                    
                }
                $carouselArray[] = [
                    'img' => $rowimg,
                    'url' => "/show.php?id=".$rowid,
                    'title' => $rowtitle,
                    'desc' => $rowtextx,
                ];
            }
                echo json_encode($carouselArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
        
    }else{
        echo json_encode([], JSON_UNESCAPED_UNICODE);
    }
}
mysqli_close($conn);
?>