<?php
ob_start();
if (empty($allnameid) || !isset($allnameid) || !is_numeric($allnameid) || $allnameid < 1 ||
    !isset($myuser) || empty($myuser) || $myuser != 200 ||
    !isset($ppzusername) || empty($ppzusername) ||
    !isset($typeuser) || empty($typeuser) || $typeuser != 8) {
    if (!headers_sent()) {
        ob_clean();
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
        header("Location: /");
    } else {
        echo "<script>window.location.href = '/';</script>";
    }
    die(); 
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

$count_sql = "SELECT COUNT(*) as total FROM ppz_work WHERE wkadmin = $allnameid";
$count_result = mysqli_query($conn, $count_sql);
$count_row = mysqli_fetch_assoc($count_result);
$total_recordsll = $count_row['total'];

$total_pages = ceil($total_recordsll / $num_rec_per_page);//计算总页数
if($p>$total_pages){$p = $total_pages;}//页码大于总页数时，设为总页数
$start_from = ($p-1) * $num_rec_per_page;//计算从第几条数据开始显示

/* 设置分页按钮 */
if ($p==1||empty($p)){
   $pageindex='<a class="page-no-button nocopy">首页</a>';//首页按钮
}else{
   $pageindex='<a class="page-button nocopy" href="?type=8">首页</a>';//首页按钮
}

if ($p==$total_pages||$total_pages<1){
   $pagebody='<a class="page-no-button nocopy" >尾页</a>';
}else{
   $pagebody='<a class="page-button nocopy" href="?type=8&p='.$total_pages.'">尾页</a>';
}

if ($total_pages>1&&$p<$total_pages){
   $exit=$p+1;
   $pageexit='<a class="page-button nocopy" href="?type=8&p='.$exit.'">下一页</a>';
}else{
   $pageexit='<a class="page-no-button nocopy" >下一页</a>';
}

if ($p<=$total_pages&&$p>1){
   $exitup=$p-1;
   $pageup='<a class="page-button nocopy" href="?type=8&p='.$exitup.'">上一页</a>';
}else{
   $pageup='<a class="page-no-button nocopy" >上一页</a>';
}

$rsrkql = "SELECT w.*, f.wkname as category_name 
           FROM ppz_work w 
           LEFT JOIN ppz_workfl f ON w.wkfl = f.id 
           WHERE w.wkadmin = $allnameid 
           ORDER BY w.id DESC 
           LIMIT $start_from, $num_rec_per_page";
$rsrkqr=mysqli_query($conn,$rsrkql);

echo '<div class="user-h1 myuser">我的工单</div>
<div class="padding_15px">
<table class="regtxt-table">
                <thead>
                  <tr>
                    <th width="15%">时间</th>
                    <th width="25%">工单</th>
                    <th width="25%">管理回复</th>
                    <th width="15%">分类</th>
                    <th width="10%">状态</th>
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
                if (mysqli_num_rows($rsrkqr) < 1){
                    echo '<tr class="alt-row"><td  colspan="6">暂无工单</td></tr>';
                }else{
                    while($listwk = mysqli_fetch_array($rsrkqr)){
                        $workid=$listwk['id'];//工单id
                        $worktime=$listwk['wktime'];//工单时间
                        $worktext=$listwk['wktext'];//工单标题
                        $work=$listwk['wkword'];//工单内容
                        $workyes=$listwk['wkyes'];//工单状态，1为未处理，2为已处理
                        $workfl=$listwk['wkfl'];//工单分类id
                        $workhf=$listwk['wkhf'];//管理回复
                        if($workyes==2){
                            $workyes='<span class="yes">已处理</span>';
                        }else{
                            $workyes='<span class="no">未处理</span>';
                        }
                        $worktext=strip_tags($worktext);
                        $worktext=str_replace("\n", "", $worktext);//去除换行符
                        $worktext=str_replace("\r", "", $worktext);//去除回车符
                        $worktext=str_replace("\t", "", $worktext);//去除制表符

                        $work=strip_tags($work);
                        $work=str_replace("\n", "", $work);
                        $work=str_replace("\r", "", $work);
                        $work=str_replace("\t", "", $work);

                        if(empty($workhf)){
                            $workhf="暂无回复";
                        }else{
                            $workhf='<a class="workeyehf" data-h="'.$workhf.'">'.$workhf.'</a>';
                        }
                        
                        $workfl_name = isset($listwk['category_name']) ? $listwk['category_name'] : "未知分类";
                        
                        echo '
                            <tr class="alt-row" id="mywork'.$workid.'">
                                <td>'.$worktime.'</td>
                                <td style="max-width:200px;"><a class="workeye" data-t="'.$work.'">'.$worktext.'</a></td>
                                <td style="max-width:200px;">'.$workhf.'</td>
                                <td>'.$workfl_name.'</td>
                                <td>'.$workyes.'</td>
                                <td><a class="workdel" data-kid="'.$workid.'" title="删除"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                            </tr>
                        ';
                    }
                }
    echo '</tbody>
            </table>
    </div>
    <dialog id="work_dialog" class="user_dialog padding_15px">
        <div class="user_dialog_title" id="dialog_title"></div>
        <a class="user_dialog_close" id="work_dialog_close"><i class="fa fa-times" aria-hidden="true"></i></a>
        <div class="user_dialog_content">
                <div id="work_dialog_content" class="work_dialog_content"></div>
        </div>
    </dialog>
    <script src="/style/js/mywork.js" type="text/javascript"></script>
   ';

ob_end_flush();
?>