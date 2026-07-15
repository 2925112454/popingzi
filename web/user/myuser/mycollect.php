<?php
ob_start();
if (empty($allnameid) || !isset($allnameid) || !is_numeric($allnameid) || $allnameid < 1 ||
    !isset($myuser) || empty($myuser) || $myuser != 200 ||
    !isset($ppzusername) || empty($ppzusername) ||
    !isset($typeuser) || empty($typeuser) || $typeuser != 5) {
    if (!headers_sent()) {
        ob_clean();
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
        header("Location: /");
    } else {
        echo "<script>window.location.href = '/';</script>";
    }
    die(); // 确保脚本完全终止
}
if (!isset($_GET["p"])){ 
    $_GET["p"]="";
}
$num_rec_per_page=20;//每页显示数量
$p=trim($_GET["p"]);//页码
if (empty($p)||!is_numeric($p)||$p<1){ 
    $p=1;
}else{ 
    $p=intval($p);
}

// 获取收藏数组
$collect_sql = "SELECT ucollect FROM ppz_newusername WHERE uid = $allnameid";
$collect_retval = mysqli_query($conn, $collect_sql);
if($collect_retval && mysqli_num_rows($collect_retval) == 1){
    $collect_query = mysqli_fetch_assoc($collect_retval);
    $collect_array = $collect_query['ucollect'];
} else {
    $collect_array = "";
}

// 处理收藏数组
$collect_array = explode("|", $collect_array);
$collect_array = array_unique(array_filter($collect_array));

// 如果有收藏ID，验证每个ID是否存在对应文章
$valid_ids = [];
if (!empty($collect_array)) {
    $ids_string = implode(',', array_map('intval', $collect_array));
    $check_sql = "SELECT rowid FROM ppz_row WHERE rowid IN ($ids_string)";
    $check_result = mysqli_query($conn, $check_sql);
    
    if ($check_result && mysqli_num_rows($check_result) > 0) {
        while ($row = mysqli_fetch_assoc($check_result)) {
            $valid_ids[] = $row['rowid'];
        }
    }
}
//对有效ID进行降序排序，确保rowid大的（最新的）排在前面
rsort($valid_ids);
// 基于有效ID计算分页
$valid_count = count($valid_ids); // 有效文章总数
$total_pages = ceil($valid_count / $num_rec_per_page); // 计算总页数
if ($p > $total_pages) {
    $p = max(1, $total_pages); // 确保页码有效
}
$start_from = ($p - 1) * $num_rec_per_page; // 计算从第几条数据开始显示

// 截取当前页需要展示的有效ID
$current_page_ids = array_slice($valid_ids, $start_from, $num_rec_per_page);

// 如果有有效文章ID，则获取这些文章的信息
$articles = [];
if (!empty($current_page_ids)) {
    $ids_string = implode(',', array_map('intval', $current_page_ids));
    $articles_sql = "SELECT rowid, rowimg, rowtexe, rowbigtext, rowif, rowdw, rowyes FROM ppz_row WHERE rowid IN ($ids_string)";
    $articles_result = mysqli_query($conn, $articles_sql);
    
    if ($articles_result && mysqli_num_rows($articles_result) > 0) {
        while ($article = mysqli_fetch_assoc($articles_result)) {
            $articles[$article['rowid']] = $article;
        }
    }
}

/* 设置分页按钮 */
if ($p == 1) {
    $pageindex = '<a class="page-no-button nocopy">首页</a>'; // 首页按钮
} else {
    $pageindex = '<a class="page-button nocopy" href="?type=5">首页</a>'; // 首页按钮
}

if ($p == $total_pages || $total_pages < 1) {
    $pagebody = '<a class="page-no-button nocopy">尾页</a>';
} else {
    $pagebody = '<a class="page-button nocopy" href="?type=5&p='.$total_pages.'">尾页</a>';
}

if ($total_pages > 1 && $p < $total_pages) {
    $exit = $p + 1;
    $pageexit = '<a class="page-button nocopy" href="?type=5&p='.$exit.'">下一页</a>';
} else {
    $pageexit = '<a class="page-no-button nocopy">下一页</a>';
}

if ($p <= $total_pages && $p > 1) {
    $exitup = $p - 1;
    $pageup = '<a class="page-button nocopy" href="?type=5&p='.$exitup.'">上一页</a>';
} else {
    $pageup = '<a class="page-no-button nocopy">上一页</a>';
}

echo'
<div class="user-h1 myuser">我的收藏</div>
<div class="padding_15px flex-wrap parent-element">';

// 根据有效ID获取收藏文章
if (!empty($articles)) {
    
    foreach ($current_page_ids as $article_id) {
        if (isset($articles[$article_id])) {
            $article = $articles[$article_id];
            
            if (($article['rowif'] == 3 || $article['rowif'] == 2)&&!empty($article['rowbigtext'])){
                        $content_array = explode("|", $article['rowbigtext']);
                        $content_count = count($content_array);
            }

            // 处理文章图片
            if (!empty($article['rowimg'])) {
                $image_url = $article['rowimg'];
            } else {
                $image_url = '/images/web/null.jpg';
                if ($article['rowif'] == 2) {
                    if (!empty($article['rowbigtext']) && $content_count > 0) {
                        $image_url = $content_array[0];
                    }
                } else {
                    // 从内容中提取第一张图片
                    if (!empty($article['rowbigtext'])) {
                        preg_match('/<img[^>]+src="([^"]+)"[^>]*>/', $article['rowbigtext'], $matches);
                        if (isset($matches[1])) {
                            $image_url = $matches[1];
                        }
                    }
                }
            }



            if ($article['rowif'] == 3){
                $article_if_ico='<span><i class="fa fa-file-video-o"></i>'.$content_count.'V</span>';//视频数量
            }elseif ($article['rowif'] == 2){
                $article_if_ico= '<span><i class="fa fa-file-photo-o"></i>'.$content_count.'P</span>';//相册图片数量
            }else{
                $article_if_ico= '';
            }

            if (!empty($article['rowdw'])){
                $article_dw='<span class="dw"><i class="fa fa-arrow-circle-down"></i>下载</span>';
            }else{
                $article_dw='';
            }

                if($article['rowyes']==4){
                    $_rowyes_none='';
                    $_rowyes_nonex='';
                }else{
                    $_rowyes_nonex='disabled-clickx';
                    $_rowyes_none='disabled-click';
                }

            
            // 输出文章HTML
            echo '
            <div class="row_box '.$_rowyes_none.'" id="delcollect' . htmlspecialchars($article['rowid']) . '">
                <a class="delcollect" data-coll="' . htmlspecialchars($article['rowid']) . '"><i class="fa fa-times" aria-hidden="true"></i></a>
                <div class="row_box_img '.$_rowyes_nonex.'" style="background:url(' . htmlspecialchars($image_url) . ');background-repeat: no-repeat; background-size:cover;"><a href="/show.php?id=' . htmlspecialchars($article['rowid']) . '" target="_blank">'.$article_if_ico.$article_dw.'</a></div>
                <div class="row_box_text"><a href="/show.php?id=' . htmlspecialchars($article['rowid']) . '" target="_blank">' . htmlspecialchars($article['rowtexe']) . '</a></div>
            </div>';
        }
    }
} else {
    echo '<div class="empty-collection">您还没有收藏任何内容</div>';
}

echo'
    </div>
    <div class="clear flex-wrap justify-content-space-between">
        <span class="page-left">第'.$p.'页（共'.$total_pages.'页）- 共计：'.$valid_count.'条记录</span>
        <span class="page-right">
        '.$pageindex.$pageup.$pageexit.$pagebody.'
        </span>
    </div>
    <script src="/style/js/delcollect.js" type="text/javascript"></script>
    ';

ob_end_flush();
?>