<?php
if ($admin==1 && $typeuser==9 && ($allvip==4||$allvip==3)  && !empty($ppzusername)){
    if (!isset($_POST["ns"])){
        $_POST["ns"]="";
    }
    if (!isset($_GET["sid"])){ 
        $_GET["sid"]="";
    }
    if (!isset($_GET["k"])){ 
        $_GET["k"]="";
    }
    if (!isset($_GET["p"])){ 
        $_GET["p"]="";
    }
    $listid=trim($_GET["sid"]);//文章id
    $listid=intval($listid);
    $so=trim($_POST["ns"]);
    $kv=trim($_GET["k"]);
    $num_rec_per_page=20;// 每页显示数量
    $getp=trim($_GET["p"]);//获取GET传参P
    /*判断参数P是否为空，且是否是数字*/
    if (isset($getp) && is_numeric($getp) && $getp>=1 ){ 
    $pa = trim($_GET["p"]);
    } else { 
    $pa=1; 
    };

    if (empty($kv)||!isset($kv)){
        if(empty($so)||!isset($so)){
            $s="";
            $plavalue="";
            $where="";
            $st="公告列表";
        }else{
            $s='<a href="popingzi.php?type=9">返回列表</a>';
            $plavalue=strip_tags($so);
            $where="where ggtext like '%$plavalue%'";
            $st="公告搜索";
        }
    }else{
            $s='<a href="popingzi.php?type=9">返回列表</a>';
            $plavalue=strip_tags($kv);
            $where="where ggtext like '%$plavalue%'";
            $st="公告搜索";
    }

    if(empty($listid)||!is_numeric($listid)||$listid<1||!is_int($listid)||!ctype_digit(trim($_GET["sid"]))){

        echo '
        <div class="user-h1">'.$st.'
            <form id="listformso" method="post" action="popingzi.php?type=9">
            <input type="text" name="ns" placeholder="输入关键词" value="'.$plavalue.'" />
            <button type="submit">搜索</button>'.$s.'       
            </form>
        </div>
        ';

        $sqlll = "SELECT * FROM ppz_announcement $where"; //链接数据表
        $rs_result = mysqli_query($conn,$sqlll); //查询数据
        $total_records = mysqli_num_rows($rs_result);  // 统计数据总数
        $total_pages = ceil($total_records / $num_rec_per_page);  // 计算总页数

        if ($total_pages < $pa){
            $p=1;
            }else{
            $p=$pa; 
        }

        $start_from = ($p-1) * $num_rec_per_page;

        if ($plavalue!==""&&!is_null($plavalue)){
            $k="&k=".$plavalue;
        }else{
            $k="";
        }

        if ($p==1){
            $pageindex='<a class="page-no-button nocopy">首页</a>';//首页按钮
        }else{
            $pageindex='<a class="page-button nocopy" href="?type=9'.$k.'">首页</a>';//首页按钮
        }

        if ($p==$total_pages){
            $pagebody='<a class="page-no-button nocopy" >尾页</a>';
        }else{
            $pagebody='<a class="page-button nocopy" href="?type=9&p='.$total_pages.$k.'">尾页</a>';
        }

        if ($total_pages>1&&$p<$total_pages){
            $exit=$p+1;
            $pageexit='<a class="page-button nocopy" href="?type=9&p='.$exit.$k.'">下一页</a>';
        }else{
            $pageexit='<a class="page-no-button nocopy" >下一页</a>';
        }

        if ($p<=$total_pages&&$p>1){
            $exitup=$p-1;
            $pageup='<a class="page-button nocopy" href="?type=9&p='.$exitup.$k.'">上一页</a>';
        }else{
            $pageup='<a class="page-no-button nocopy" >上一页</a>';
        }

        $rsql = "select * from ppz_announcement $where ORDER BY ggid desc LIMIT $start_from, $num_rec_per_page";//获取文章数据库表
        $rretval=mysqli_query($conn,$rsql);
        if(mysqli_num_rows($rretval) < 1){ 
            echo '<div class="adminrownull">什么也没有~</div>';
        }else{
            echo '<div class="regtxt-row">
            <table class="regtxt-table">
                <thead>
                  <tr>
                    <th width="6%">选择</th>
                    <th width="54%">标题</th>
                    <th width="15%">作者</th>
                    <th width="15%">发布时间</th>
                    <th width="10%">操作</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <td colspan="5">
                        <div class="clear">
                          <span class="page-left"><a id="allcheckbox">全选/全不选</a><a id="allcheckboxdel">批量删除</a>第'.$p.'页（共'.$total_pages.'页）- 共计：'.$total_records.'条记录</span>
                          <span class="page-right">
                          '.$pageindex.$pageup.$pageexit.$pagebody.'
                          </span>
                      </div></td>
                  </tr>
                </tfoot>
                <tbody>            
            ';
            while($listr = mysqli_fetch_array($rretval)){
                $rid=$listr["ggid"];//文章id
                $title=$listr["ggtext"];//文章标题
                $time=$listr["ggtime"];//发布时间
                $admin=$listr["ggrowid"];//文章作者id

                //获取发布者信息
                    $adminsql="select * from ppz_newusername where uid=$admin";
                    $adminsqlretval=mysqli_query($conn,$adminsql);
                    if(mysqli_num_rows($adminsqlretval) == 1){
                        while($adminr = mysqli_fetch_array($adminsqlretval)){
                            $adminname=$adminr["uname"];
                        }
                    }else{
                        $adminname="未知发布者";
                    }

                    if ($plavalue!==""&&!is_null($plavalue)){
                        //替换搜索词
                        $titleyes=str_replace($plavalue,"<span class='search-key'>".$plavalue."</span>",$title);
                    }else{
                        $titleyes=$title;
                    }

                    echo'
                    <tr class="alt-row"> 
                    <td><input type="checkbox" name="id" value="'.$rid.'">
                    </td>
                    <td style="max-width: 460px;display:revert;text-align:center;" class="rtitle"><a href="/anctshow.php?id='.$rid.'" target="_blank" >'.$titleyes.'</a></td>
                    <td style="max-width:60px;"><a href="/user.php?id='.$admin.'" target="_blank" >'.$adminname.'</a></td> 
                    <td style="max-width:130px;">'.$time.'</td>
                    <td><a href="?type=9&sid='.$rid.'" title="编辑"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> <a class="rowdel" data-d='.$rid.' title="删除"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                    </tr>
                    ';
            }
            echo '
            </tbody>
            </table>
            </div>
            <script src="/style/js/notice.js" type="text/javascript"></script>
            ';
        }


    }else{
        //获取公告信息
        $noticesql = "select * from ppz_announcement where ggid=$listid";
        $noticeretval=mysqli_query($conn,$noticesql);
        if(mysqli_num_rows($noticeretval) === 1){
            while($noticer = mysqli_fetch_array($noticeretval)){
                $gg_tite=$noticer["ggtext"];//标题
                $gg_img=$noticer["ggimg"];//封面
                $gg_text=$noticer["ggbigtext"];//内容
                $gg_top=$noticer["ggtop"];//1不置顶，2置顶
            }
            if ($gg_top==2){
                $gg_top1="";
                $gg_top2="checked";
            }else{
                $gg_top1="checked";
                $gg_top2="";
            }
            echo '<div class="user-h1">编辑公告：<a href="?type=9"><i class="fa fa-angle-double-left" aria-hidden="true"></i>返回列表</a></div>';
            echo '
            <div class="newword">
                <form method="post" id="wordform">
                    <div class="newword-title"><span class="letter1em">标题：</span><input type="text" name="rowhead" id="rowhead" value="'.$gg_tite.'" /></div>
                    <div class="newword-title"><span class="letter1em">封面：</span><input type="text" name="rowimg" id="rowimg" value="'.$gg_img.'" /><a id="newworduploadimg">上传</a></div>
                    <textarea name="rowtext" id="rowtext">'.$gg_text.'</textarea>
                    <div class="newword-title"><span class="letter1em">置顶：</span>
                    <input type="radio" id="nawrowtop" name="rowtop" value="1" '.$gg_top1.'/><label for="nawrowtop">不置顶</label>
                    <input type="radio" id="rowtop" name="rowtop" value="2" '.$gg_top2.'/><label for="rowtop">置顶</label>
                    </div>
                    <div class="newword-title3"><button id="newwordsubmit">提交</button></div>
                </form>
            </div>';
            echo '
            <div class="upload-overlay" id="uploadOverlay">  
            <div class="upload-box">  
            <div class="custom-file-upload" id="dragArea">  
            <input type="file" id="fileUpload" style="display: none;">  
            <button id="fileimgbox" type="button"><i class="fa fa-plus"></i></button>  
            </div>
            <div class="file-info"><span id="fileInfo" style="display: flex;">请先点击上方选择要上传的文件</span></div>  
            <button id="closeUploadOverlay"><i class="fa fa-times"></i></button>
            <button id="openUploadOverlay">上传</button>
            <div id="fileerr" style="display: none;"></div>
            </div>  
            </div>
            ';
            echo '
            <script src="/style/tinymce/tinymce.min.js"></script><script src="/style/tinymce/index.js"></script>
            <script src="/style/js/noticeform.js" type="text/javascript"></script>';
        }else{
            echo '<div class="user-h1">编辑公告：空空如也~<a href="?type=9"><i class="fa fa-angle-double-left" aria-hidden="true"></i>返回列表</a></div>';
            echo '<div class="adminrownull">什么也没有~</div>';
        }
    }

    

}else{
    echo "请勿胡搞！";
}
?>