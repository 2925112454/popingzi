<?php
if ($admin==1 && $typeuser==3 && ($allvip==4||$allvip==3||$allvip==2)  && !empty($ppzusername)){
    if(!isset($_GET["sid"])){
        $_GET["sid"]="";
    }
    if(!isset($_GET["k"])){
        $_GET["k"]="";
    }
    if (!isset($_POST["s"])){
        $_POST["s"]="";
    }
    if (!isset($_GET["p"])){
        $_GET["p"]="";
    }
    $listid=$_GET["sid"];//文章id
    $listid=intval($listid);    //转换为数字
    $so=$_POST["s"];
    $kv=$_GET["k"];
    $num_rec_per_page=20;// 每页显示数量
    $getp=$_GET["p"];//获取GET传参P
    /*判断参数P是否为空，且是否是数字*/
    if (isset($getp) && is_numeric($getp) && $getp>=1 ){ 
    $pa = $_GET["p"];
    } else { 
    $pa=1; 
    }; 

if (empty($kv)||!isset($kv)){
    if(empty($so)||!isset($so)){
        $s="";
        $plavalue="";
        $where="";
        $st="文章列表";
    }else{
        $s='<a href="popingzi.php?type=3">返回列表</a>';
        $plavalue=strip_tags($so);
        if ($so==="%待审核%"){
            $where="where rowyes=1";
        }else if( $so==="%已撤销%" ){
            $where="where rowyes=3";
        }else if( $so==="%未通过%" ){
            $where="where rowyes=2";
        }else if( $so==="%已发布%" ){
            $where="where rowyes=4";
        }else{
            $where="where rowtexe like '%$plavalue%'";
        }

        
        $st="筛选文章";
    }
}else{
        $s='<a href="popingzi.php?type=3">返回列表</a>';
        $plavalue=strip_tags($kv);
        if ($kv==="%待审核%"){
            $where="where rowyes=1";
        }else if( $kv==="%已撤销%" ){
            $where="where rowyes=3";
        }else if( $kv==="%未通过%" ){
            $where="where rowyes=2";
        }else if( $kv==="%已发布%" ){
            $where="where rowyes=4";
        }else{
            $where="where rowtexe like '%$plavalue%'";
        }
        $st="筛选文章";
}
    

if(empty($listid)||!is_numeric($listid)||$listid<1||!is_int($listid)||!ctype_digit($_GET["sid"])){

$sqlll = "SELECT * FROM ppz_row $where"; //链接数据表
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
    $pageindex='<a class="page-button nocopy" href="?type=3'.$k.'">首页</a>';//首页按钮
}

if ($p==$total_pages){
    $pagebody='<a class="page-no-button nocopy" >尾页</a>';
}else{
    $pagebody='<a class="page-button nocopy" href="?type=3&p='.$total_pages.$k.'">尾页</a>';
}

if ($total_pages>1&&$p<$total_pages){
    $exit=$p+1;
    $pageexit='<a class="page-button nocopy" href="?type=3&p='.$exit.$k.'">下一页</a>';
}else{
    $pageexit='<a class="page-no-button nocopy" >下一页</a>';
}

if ($p<=$total_pages&&$p>1){
    $exitup=$p-1;
    $pageup='<a class="page-button nocopy" href="?type=3&p='.$exitup.$k.'">上一页</a>';
}else{
    $pageup='<a class="page-no-button nocopy" >上一页</a>';
}




        echo '
        <div class="user-h1">'.$st.'
            <form id="listformso" method="post" action="popingzi.php?type=3">
            <input type="text" name="s" placeholder="输入关键词 或 状态(%待审核%、%已撤销%、%未通过%、%已发布%)" value="'.$plavalue.'" />
            <button type="submit">搜索</button>'.$s.'       
            </form>
        </div>
        ';
        
        $rsql = "select * from ppz_row $where ORDER BY rowid desc LIMIT $start_from, $num_rec_per_page";//获取文章数据库表
        $rretval=mysqli_query($conn,$rsql);
        if(mysqli_num_rows($rretval) < 1){ 
            echo '<div class="adminrownull">什么也没有~</div>';
        }else{
            echo '<div class="regtxt-row">
            <table class="regtxt-table">
                <thead>
                  <tr>
                    <th width="6%">选择</th>
                    <th width="6%">状态</th>
                    <th width="8%">分类</th>
                    <th width="45%">标题</th>
                    <th width="15%">作者</th>
                    <th width="10%">发布时间</th>
                    <th width="10%">操作</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <td colspan="7">
                        <div class="clear">
                          <span class="page-left"><a id="allcheckbox">全选/全不选</a><a id="allcheckboxdel">批量删除</a><a id="allcheckboxexe">批量审核</a>第'.$p.'页（共'.$total_pages.'页）- 共计：'.$total_records.'条记录</span>
                          <span class="page-right">
                          '.$pageindex.$pageup.$pageexit.$pagebody.'
                          </span>
                      </div></td>
                  </tr>
                </tfoot>
                <tbody>            
            ';
            while($listr = mysqli_fetch_array($rretval)){
                $rid=$listr["rowid"];//文章id
                $title=$listr["rowtexe"];//文章标题
                $top=$listr["rowtop"];//是否置顶，1默认不置顶，2置顶，3,热门，4精华
                $fl=$listr["rowfl"];//所属分类
                $time=$listr["rowtime"];//发布时间
                $admin=$listr["rowadmin"];//文章作者id
                $yes=$listr["rowyes"];//审核状态，1待审核，2未通过，3已撤销，4已通过
                $if=$listr["rowif"];//文章类型，1图文，2相册，3视频
                if($yes==1){
                    $yes="<span class='yesorno'>待审核</span>";
                }elseif($yes==2){
                    $yes="<span class='no'>未通过</span>";
                }elseif($yes==3){
                    $yes="<span class='no'>已撤销</span>";
                }elseif($yes==4){
                    $yes="<span class='yes'>已发布</span>";
               }else{
                    $yes="<span class='no'>异常状态</span>";
               }
                    if($top==1){
                        $top="普通文章";
                    }elseif($top==2){
                        $top="置顶文章";
                    }elseif($top==3){
                        $top="热门文章";
                    }elseif($top==4){
                        $top="精华文章";
                    }else{
                        $top="普通文章";
                    }
                
                    if($if==1){
                        $if="<span class='yesif' title='图文'><i class='fa fa-file-word-o' aria-hidden='true'></i></span>";
                    }elseif($if==2){
                        $texeimg=$listr["rowbigtext"];//相册图片数组,每张图片以"|"分割
                        $texeimgarr=explode("|",$texeimg);
                        //获取图片数量
                        $texeimgarrnum=count($texeimgarr);
                        $if="<span class='yesif' title='相册'><i class='fa fa-file-image-o' aria-hidden='true'></i>[".$texeimgarrnum."P]</span>";
                    }elseif($if==3){
                        $texevideo=$listr["rowbigtext"];//相册视频数组,每张图片以"|"分割
                        $texevideoarr=explode("|",$texevideo);
                        //获取视频数量
                        $texevideoarrnum=count($texevideoarr);
                        $if="<span class='yesif' title='视频'><i class='fa fa-file-video-o' aria-hidden='true'></i>[".$texevideoarrnum."V]</span>";
                    }else{
                        $if="<span class='noif' title='异常类型'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i></span>";
                    }

                    //获取发布者信息
                    $adminsql="select * from ppz_newusername where uid=$admin";
                    $adminsqlretval=mysqli_query($conn,$adminsql);
                    if(@mysqli_num_rows($adminsqlretval) == 1){
                        while($adminr = mysqli_fetch_array($adminsqlretval)){
                            $adminname=$adminr["uname"];
                        }
                    }else{
                        $adminname="未知发布者";
                    }
                    //获取分类信息
                    $newflsql="select * from ppz_fl where flid=$fl";
                    $newflsqlretval=mysqli_query($conn,$newflsql);
                    if($newflsqlretval===false){
                         die("查询失败: " . mysqli_error($conn)); // 输出具体错误信息
                    }
                    if(@mysqli_num_rows($newflsqlretval) == 1){
                        while($flinr = mysqli_fetch_array($newflsqlretval)){
                            $newflname=$flinr["flname"];
                            $newflid=$flinr["fllinkid"];
                        }
                    }else{
                        $newflname="未知分类";
                        $newflid=1;
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
                    <td style="max-width:60px;">'.$yes.'</td>
                    <td style="max-width:60px;"><a href="/list.php?id='.$newflid.'&tag='.$fl.'" target="_blank">'.$newflname.'</a></td>
                    <td style="max-width:350px;" class="rtitle">'.$if.'<a href="/show.php?id='.$rid.'" target="_blank" title="'.$top.'">'.$titleyes.'</a></td>
                    <td style="max-width:60px;"><a href="/user.php?id='.$admin.'" target="_blank" >'.$adminname.'</a></td> 
                    <td style="max-width:130px;">'.$time.'</td>
                    <td><a href="?type=3&sid='.$rid.'" title="编辑"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> <a class="rowdel" data-d='.$rid.' title="删除"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                    </tr>
                    ';
            }
            echo '
            </tbody>
            </table>
            </div>
            <div id="customMenu">
                <div id="menuCheck">通过审核</div>
                <div id="menuCheckx">撤销审核</div>
                <div id="menuCheckxx">等待审核</div>
                <div id="menuCheckxxx">驳回审核</div>
            </div>
            <script src="/style/js/checkbox.js" type="text/javascript"></script>
            <script src="/style/js/rightclick.js" type="text/javascript"></script>
            ';


        };

    }else{
        
        if ($listid>0&&!is_null($listid)&&$listid!==""&&is_numeric($listid)&&is_int($listid)){
           $listinfosqlxc="select * from ppz_row where rowid=$listid";//获取文章信息
           $listinfosqlxcretval=mysqli_query($conn,$listinfosqlxc);
           if(mysqli_num_rows($listinfosqlxcretval) == 1){
            while($listr = mysqli_fetch_array($listinfosqlxcretval)){
                $rlit_tite=$listr["rowtexe"];//文章标题
                $rlit_text=$listr["rowbigtext"];//文章内容
                $rlit_top=$listr["rowtop"];//是否置顶，1默认不置顶，2置顶，3,热门，4精华
                $rlit_fl=$listr["rowfl"];//所属分类
                $rlit_tag=$listr["rowtag"];//标签
                $rlit_time=$listr["rowtime"];//发布时间
                $rlit_admin=$listr["rowadmin"];//发布者
                $rlit_cp=$listr["rowcp"];//版权方
                $rlit_cpurl=$listr["rowcpurl"];//版权方链接
                $rlit_dw=$listr["rowdw"];//下载配置数组
                $rlit_dwg=$listr["rowdwgold"];//下载所需积分
                $rlit_vip=$listr["rowvip"];//文章访问权限-->1所有人，2登录可见，3充值会员及管理员可见
                $rlit_if=$listr["rowif"];//文章类型，1图文，2相册，3视频
                $rlit_img=$listr["rowimg"];//封面
                $rlit_vt=$listr["videotext"];//相册或者视频的说明介绍
                $rlit_yes=$listr["rowyes"];//审核状态，1待审核，2未通过，3待修改，4已通过
                $rlit_dowif=$listr["rowdwif"];//下载权限，1所有会员，2VIP会员及以上，3仅限管理员及以上
                $imageseye=$listr['vorimg'];//游客可见
                $vipimageseye=$listr['vorimg_log'];//登录可见
                $videotexttop=$listr["videotexttop"];//相册或者视频的说明介绍位置，1显示在文章下方，2显示在文章上方
            }
            if(!is_numeric($imageseye)||$imageseye<0||$imageseye>999999999||empty($imageseye)){
                $imageseye=0;
            }
            if(!is_numeric($vipimageseye)||$vipimageseye<0||$vipimageseye>999999999||empty($vipimageseye)){
                $vipimageseye=0;
            }
            if(empty($videotexttop)||$videotexttop<1||$videotexttop>2){
                $videotexttop=1;
            }
            if($videotexttop==2){
                $text_top2="checked";
                $text_top1="";
            }else{
                $text_top2="";
                $text_top1="checked";
            }

            if($rlit_dowif==1){
                $dowif1="selected";
                $dowif2="";
                $dowif3="";
            }elseif ($rlit_dowif==2){
                $dowif1="";
                $dowif2="selected";
                $dowif3="";
            }elseif ($rlit_dowif==3){
                $dowif1="";
                $dowif2="";
                $dowif3="selected";
            }else{
                $dowif1="";
                $dowif2="";
                $dowif3="";
            }

            if ($rlit_vip==1){
                $rlit_viptxt1="selected";
                $rlit_viptxt2="";
                $rlit_viptxt3="";
            }else if($rlit_vip==2){
                $rlit_viptxt1="";
                $rlit_viptxt2="selected";
                $rlit_viptxt3="";
            }else if($rlit_vip==3){
                $rlit_viptxt1="";
                $rlit_viptxt2="";
                $rlit_viptxt3="selected";
            }else{
                $rlit_viptxt1="selected";
                $rlit_viptxt2="";
                $rlit_viptxt3="";
            }

            if ($rlit_top==1){
                $rlit_top1="checked";
                $rlit_top2="";
                $rlit_top3="";
                $rlit_top4="";
            }else if ($rlit_top==2){
                $rlit_top1="";
                $rlit_top2="checked";
                $rlit_top3="";
                $rlit_top4="";
            }else if ($rlit_top==3){
                $rlit_top1="";
                $rlit_top2="";
                $rlit_top3="checked";
                $rlit_top4="";
            }else if ($rlit_top==4){
                $rlit_top1="";
                $rlit_top2="";
                $rlit_top3="";
                $rlit_top4="checked";
            }else{
                $rlit_top1="checked";
                $rlit_top2="";
                $rlit_top3="";
                $rlit_top4="";
            }

            if (!is_null($rlit_dw)&&$rlit_dw!==""){
                $rlit_dwarr=explode(",",$rlit_dw);//格式如下：‘网盘名称,下载地址,数量,大小,解压密码,提取码,分辨率’
                $rlit_dwname=$rlit_dwarr[0];//网盘名称
                $rlit_dwurl=$rlit_dwarr[1];//下载地址
                $rlit_dwnum=$rlit_dwarr[2];//数量
                $rlit_dwsz=$rlit_dwarr[3];//大小
                $rlit_dwpwd=$rlit_dwarr[4];//解压密码
                $rlit_dwxt=$rlit_dwarr[5];//提取码
                $rlit_dwfx=$rlit_dwarr[6];//分辨率
            }

            $listfllinkid="";

            if (!is_null($rlit_fl)&&$rlit_fl!==""&&$rlit_fl>0){
                $listflsql="select * from ppz_fl where flid=$rlit_fl";
                $listflsqlretval=mysqli_query($conn,$listflsql);
                if (mysqli_num_rows($listflsqlretval) == 1){
                    while($listflr = mysqli_fetch_array($listflsqlretval)){
                        $listfl=$listflr["flname"];//分类名称
                        $listfllinkid=$listflr["fllinkid"];//所属列表ID
                    }
                }else{
                    $listlink='<option value="">未知领域</option>';
                    $listfl='<option value="0">暂无分类</option>';
                }                
            }else{
                $listlink='<option value="">未知领域</option>';
                $listfl='<option value="0">暂无分类</option>';
            }
            

            $adminyeslist=200;
            echo '<div class="user-h1">编辑文章：'.$rlit_tite.'<a href="?type=3"><i class="fa fa-angle-double-left" aria-hidden="true"></i>返回列表</a></div>';

            if ($rlit_if==1){
                echo '<script src="/style/tinymce/tinymce.min.js"></script><script src="/style/tinymce/index.js"></script>';
                include __DIR__.'/inc/listrowword.php';//修改图文
            }else if($rlit_if==2){
                echo '<script src="/style/tinymce/tinymce.min.js"></script><script src="/style/tinymce/indeximg.js"></script>';
                include __DIR__.'/inc/listrowimage.php';//修改相册
            }else if($rlit_if==3){
                echo '<script src="/style/tinymce/tinymce.min.js"></script><script src="/style/tinymce/indeximg.js"></script>';
                include __DIR__.'/inc/listrowvideo.php';//修改视频
            }else{
                echo '<div class="adminrownull">错误参数~</div>';
            }
           }else{
               echo '<div class="user-h1">编辑文章：空空如也~<a href="?type=3"><i class="fa fa-angle-double-left" aria-hidden="true"></i>返回列表</a></div>';
               echo '<div class="adminrownull">空空如也~</div>';
           }
        }
    }

}else{
    echo "请勿胡搞！";
}
?>