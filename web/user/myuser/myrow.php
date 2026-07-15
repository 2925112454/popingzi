<?php
// 启用输出缓冲
ob_start();
if (empty($allnameid) || !isset($allnameid) || !is_numeric($allnameid) || $allnameid < 1 ||
    !isset($myuser) || empty($myuser) || $myuser != 200 ||
    !isset($ppzusername) || empty($ppzusername) ||
    !isset($typeuser) || empty($typeuser) || $typeuser != 3) {
    
    // 检查头部是否已发送
    if (!headers_sent()) {
        // 清除输出缓冲区
        ob_clean();
        // 添加缓存控制头
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
        header("Location: /");
    } else {
        // 如果头部已发送，使用 JavaScript 重定向作为备选
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
    $sqlll = "SELECT * FROM ppz_row where rowadmin=$allnameid";
    $rs_resultll = mysqli_query($conn, $sqlll);//执行SQL语句，获取结果集
    $total_recordsll = mysqli_num_rows($rs_resultll);//获取记录总数
    $total_pages = ceil($total_recordsll / $num_rec_per_page);//计算总页数
    if($p<1){$p = 1;}//页码小于1时，设为1
    if($p>$total_pages){$p = $total_pages;}//页码大于总页数时，设为总页数
    $start_from = ($p-1) * $num_rec_per_page;//计算从第几条数据开始显示
    /* 设置分页按钮 */
    if ($p==1){
       $pageindex='<a class="page-no-button nocopy">首页</a>';//首页按钮
   }else{
       $pageindex='<a class="page-button nocopy" href="?type=3">首页</a>';//首页按钮
   }

   if ($p==$total_pages||$total_pages<1){
       $pagebody='<a class="page-no-button nocopy" >尾页</a>';
   }else{
       $pagebody='<a class="page-button nocopy" href="?type=3&p='.$total_pages.'">尾页</a>';
   }
   
   if ($total_pages>1&&$p<$total_pages){
       $exit=$p+1;
       $pageexit='<a class="page-button nocopy" href="?type=3&p='.$exit.'">下一页</a>';
   }else{
       $pageexit='<a class="page-no-button nocopy" >下一页</a>';
   }
   
   if ($p<=$total_pages&&$p>1){
       $exitup=$p-1;
       $pageup='<a class="page-button nocopy" href="?type=3&p='.$exitup.'">上一页</a>';
   }else{
       $pageup='<a class="page-no-button nocopy" >上一页</a>';
   }
   /* 查询数据表 */
   $rsql = "$sqlll ORDER BY rowid desc LIMIT $start_from, $num_rec_per_page";//获取数据库表
   $rretval=mysqli_query($conn,$rsql);
   echo '<div class="user-h1 myuser">我的文章<span>*已发布的稿件 编辑/删除 权归平台所有，您若有改稿/删除的需求请提交工单联系管理员进行处理。</span></div>
   <div class="padding_15px">
   <table class="regtxt-table">
                <thead>
                  <tr>
                    <th width="15%">时间</th>
                    <th width="15%">状态</th>
                    <th width="30%">标题</th>
                    <th width="15%">分类</th>
                    <th width="15%">浏览量</th>
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
                    echo '<tr class="alt-row"><td  colspan="6">暂无文章</td></tr>';
                }else{
                    while($r = mysqli_fetch_array($rretval)){
                        $rid=$r['rowid'];//文章id
                        $title=$r['rowtexe'];//文章标题
                        $time=$r['rowtime'];//发布时间
                        $rif=$r['rowyes'];//审核状态，1待审核，2未通过，3已撤销，4已通过
                        $reye=$r['roweye'];//浏览量
                        $myflname=$r['rowfl'];//分类id

                        $atitle='<a data-id="'.$rid.'" class="delmyrow">撤销投稿</a>';

                        if($rif==1){
                            $rif='<span class="yesorno">待审核</span>';
                        }elseif($rif==2){
                            $rif='<span class="no">未通过</span>';
                        }elseif($rif==3){
                            $rif='<span class="no">已撤销</span>';
                            $atitle='<i class="fa fa-ban" aria-hidden="true"></i>';
                        }elseif($rif==4){
                            $rif='<span class="yes">已发布</span>';
                            $atitle='<i class="fa fa-ban" aria-hidden="true"></i>';
                        }

                        //获取对应分类名称
                        $my_fl_sql="select flname from ppz_fl where flid='$myflname'";
                        $my_fl_res=mysqli_query($conn,$my_fl_sql);
                        if(mysqli_num_rows($my_fl_res) > 0){
                            $my_fl_row=mysqli_fetch_array($my_fl_res);
                            $my_fl_name=$my_fl_row['flname'];
                        }else{
                            $my_fl_name="未知分类";
                        }

                        echo '
                        <tr class="alt-row">
                            <td style="max-width:150px;">'.$time.'</td>
                            <td style="max-width:150px;" id="myrow'.$rid.'">'.$rif.'</td>
                            <td style="max-width:350px;"><a href="/show.php?id='.$rid.'" target="_blank">'.$title.'</a></td>
                            <td style="max-width:150px;">'.$my_fl_name.'</td>
                            <td style="max-width:150px;">'.$reye.'</td>
                            <td style="max-width:150px;">'.$atitle.'</td>
                        </tr>';
                    }                    
                }

              echo '</tbody>
            </table>
    </div>
    <script src="/style/js/myrowdel.js" type="text/javascript"></script>
   ';
   ob_end_flush();
?>