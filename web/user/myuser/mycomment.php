<?php
ob_start();
if (empty($allnameid) || !isset($allnameid) || !is_numeric($allnameid) || $allnameid < 1 ||
    !isset($myuser) || empty($myuser) || $myuser != 200 ||
    !isset($ppzusername) || empty($ppzusername) ||
    !isset($typeuser) || empty($typeuser) || $typeuser != 4) {
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
    $sqlll = "SELECT * FROM ppz_commentary where pladmin=$allnameid";
    $rs_resultll = mysqli_query($conn, $sqlll);//执行SQL语句，获取结果集
    $total_recordsll = mysqli_num_rows($rs_resultll);//获取记录总数
    $total_pages = ceil($total_recordsll / $num_rec_per_page);//计算总页数
    if($p>$total_pages){$p = $total_pages;}//页码大于总页数时，设为总页数
    $start_from = ($p-1) * $num_rec_per_page;//计算从第几条数据开始显示
    /* 设置分页按钮 */
    if ($p==1||empty($p)){
       $pageindex='<a class="page-no-button nocopy">首页</a>';//首页按钮
   }else{
       $pageindex='<a class="page-button nocopy" href="?type=4">首页</a>';//首页按钮
   }

   if ($p==$total_pages||$total_pages<1){
       $pagebody='<a class="page-no-button nocopy" >尾页</a>';
   }else{
       $pagebody='<a class="page-button nocopy" href="?type=4&p='.$total_pages.'">尾页</a>';
   }
   
   if ($total_pages>1&&$p<$total_pages){
       $exit=$p+1;
       $pageexit='<a class="page-button nocopy" href="?type=4&p='.$exit.'">下一页</a>';
   }else{
       $pageexit='<a class="page-no-button nocopy" >下一页</a>';
   }
   
   if ($p<=$total_pages&&$p>1){
       $exitup=$p-1;
       $pageup='<a class="page-button nocopy" href="?type=4&p='.$exitup.'">上一页</a>';
   }else{
       $pageup='<a class="page-no-button nocopy" >上一页</a>';
   }
   /* 查询数据表 */
   $rsql = "$sqlll ORDER BY plid desc LIMIT $start_from, $num_rec_per_page";//获取数据库表
   $rretval=mysqli_query($conn,$rsql);
   echo '<div class="user-h1 myuser">我的评论</div>
   <div class="padding_15px">
   <table class="regtxt-table">
                <thead>
                  <tr>
                    <th width="15%">时间</th>
                    <th width="30%">评论</th>
                    <th width="15%">回复</th>
                    <th width="15%">点赞</th>
                    <th width="15%">来源</th>
                    <th width="10%">操作</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <td colspan="6">
                        <div class="clear">
                            <span class="page-left">第'.$p.'页（共'.$total_pages.'页）- 共计：'.$total_recordsll.'条记录</span>
                            <span class="page-right">
                            '.$pageindex.$pageup.$pageexit.$pagebody.'
                            </span>
                        </div>
                      </td>
                  </tr>
                </tfoot>
                <tbody>';
                if(mysqli_num_rows($rretval) < 1){
                    echo '<tr class="alt-row"><td  colspan="6">暂无评论</td></tr>';
                }else{
                    while($listcomm = mysqli_fetch_array($rretval)){
                        $plid=$listcomm['plid'];//评论id
                        $pltime=$listcomm['pltime'];//评论时间
                        $plrowid=$listcomm['plrowid'];//评论文章id
                        $pltop=$listcomm['pltop'];//评论点赞（数组）
                        $plcontent=$listcomm['plbigtext'];//评论内容

                        if(!empty($pltop)){
                            //按| 分割
                            $pltoparr=explode("|",$pltop);
                            $pltop=count($pltoparr);
                        }else{
                            $pltop=0;
                        }

                        if(empty($plrowid)||$plrowid<1){
                            $rowcomm_title="没找到来源文章";
                        }else{
                            $commrow_sql="select rowtexe from ppz_row where rowid='$plrowid'";
                            $commrow_res=mysqli_query($conn,$commrow_sql);
                            if(mysqli_num_rows($commrow_res)!==1){
                                $rowcomm_title="没找到来源文章";
                            }else{
                                while($commrow_row=mysqli_fetch_assoc($commrow_res)){
                                    $rowcomm_title=$commrow_row['rowtexe'];
                                }
                            }
                        }
                        //获取回复
                        $commrep_sql="select repid from ppz_reply where repplid='$plid'";
                        $commrep_res=mysqli_query($conn,$commrep_sql);
                        $commrep_num=mysqli_num_rows($commrep_res);
                        
                        echo '
                            <tr class="alt-row" id="mycomm'.$plid.'">
                                <td>'.$pltime.'</td>
                                <td><a href="/plreply.php?id='.$plid.'"  target="_blank">'.$plcontent.'</a></td>
                                <td>'.$commrep_num.'</td>
                                <td>'.$pltop.'</td>
                                <td><a href="/show.php?id='.$plrowid.'" target="_blank">'.$rowcomm_title.'</a></td>
                                <td><a class="commdel" data-cid="'.$plid.'" title="删除"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                            </tr>
                        ';
                    }
                    
                }
                echo '</tbody>
            </table>
    </div>
    <script src="/style/js/mycommdel.js" type="text/javascript"></script>
   ';
ob_end_flush();
?>