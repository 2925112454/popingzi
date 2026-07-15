<?php
if ($admin == 1 && $typeuser == 13 && ($allvip == 4||$allvip == 3) && !empty($ppzusername)) {

    $plavalue="";//搜索post
    $go='';//返回按钮
    $st="积分记录";//标题
    $p=1;//页码
    $num_rec_per_page=20;// 每页显示数量

    if  (isset($_POST['logso']) && !empty($_POST['logso'])) {
        $plavalue = trim($_POST['logso']);
    }
    if (isset($_GET["k"]) && !empty($_GET["k"])){ 
        $plavalue=trim($_GET['k']);
    }
    
    if(!empty($plavalue)){
        $go='<a href="popingzi.php?type=13">返回列表</a>';
        $st='订单号查询';
        $plavaluex = '%' . mysqli_real_escape_string($conn, $plavalue) . '%';
        $from_sql="SELECT * FROM ppz_log where logmun LIKE '$plavaluex' order by logid desc";
    }else{
        $from_sql="SELECT * FROM ppz_log order by logid desc";
    }
    $log_sql_num=mysqli_query($conn,$from_sql);
    $total_recordsll = mysqli_num_rows($log_sql_num);// 获取数据总数
    $total_pages = ceil($total_recordsll / $num_rec_per_page);// 计算总页数
    if (empty($total_pages)||$total_pages<1) {$total_pages = 1;}
    if (isset($_GET["p"]) && !empty($_GET["p"]) && is_numeric($_GET["p"]) && $_GET["p"]>0 && intval($_GET["p"])<=$total_pages){ 
        $p=intval(trim($_GET['p']));
    }

    if ($p==1){
        $pageindex='<a class="page-no-button nocopy">首页</a>';//首页按钮
    }else{
        $pageindex='<a class="page-button nocopy" href="?type=13&k='.$plavalue.'">首页</a>';//首页按钮
    }
    
    if ($p==$total_pages){
        $pagebody='<a class="page-no-button nocopy">尾页</a>';
    }else{
        $pagebody='<a class="page-button nocopy" href="?type=13&p='.$total_pages."&k=".$plavalue.'">尾页</a>';
    }
    
    if ($total_pages>1&&$p<$total_pages){
        $exit=$p+1;
        $pageexit='<a class="page-button nocopy" href="?type=13&p='.$exit."&k=".$plavalue.'">下一页</a>';
    }else{
        $pageexit='<a class="page-no-button nocopy" >下一页</a>';
    }
    
    if ($p<=$total_pages&&$p>1){
        $exitup=$p-1;
        $pageup='<a class="page-button nocopy" href="?type=13&p='.$exitup."&k=".$plavalue.'">上一页</a>';
    }else{
        $pageup='<a class="page-no-button nocopy" >上一页</a>';
    }

    echo '
    <div class="user-h1">'.$st.'
        <form id="listformso" method="post" action="popingzi.php?type=13">
            <input type="text" name="logso" placeholder="搜索订单号" value="'.$plavalue.'" />
            <button type="submit">搜索</button>'.$go.'
        </form>
    </div>
    ';

    $start_from = ($p-1) * $num_rec_per_page;
    $rsql = "$from_sql LIMIT $start_from, $num_rec_per_page";//获取数据库表
    $rretval=mysqli_query($conn,$rsql);
    if(mysqli_num_rows($rretval) < 1){ 
        echo '<div class="adminrownull">什么也没有~</div>';
    }else{
        echo '<div class="regtxt-row">
                    <table class="regtxt-table">
                        <thead>
                          <tr>
                            <th width="8%">选择</th>
                            <th width="8%">类别</th>
                            <th width="14%">用户</th>
                            <th width="20%">订单号</th>
                            <th width="10%">变动</th>
                            <th width="10%">余额</th>
                            <th width="10%">订单时间</th>
                            <th width="10%">操作IP</th>
                            <th width="10%">操作</th>
                          </tr>
                        </thead>
                        <tfoot>
                          <tr>
                            <td colspan="9">
                                <div class="clear">
                                  <span class="page-left"><a id="allcheckbox">全选/全不选</a><a id="dellog" class="delbut">删除选中</a><a id="ifdellog" class="delbut">清空一年前</a><a id="ifdellogmo" class="delbut">清空三月前</a>第'.$p.'页（共'.$total_pages.'页）- 共计：'.$total_recordsll.'条记录</span>
                                  <span class="page-right">
                                    '.$pageindex.$pageup.$pageexit.$pagebody.'
                                  </span>
                              </div></td>
                          </tr>
                        </tfoot>
                        <tbody>
                    ';
        
        while($listr = mysqli_fetch_array($rretval)){
            $log_id=$listr['logid'];//交易id
            $log_time=$listr['logtime'];//交易时间
            $log_ip=$listr['logip'];///交易ip
            $log_admin=$listr['logadmin'];//交易会员
            $log_type=$listr['logtype'];//交易类型
            $log_mun=$listr['logmun'];//订单号
            $log_rmb=$listr['logrmb'];//交易积分变动
            $log_money=$listr['logab'];//交易后的余额
            $log_row=$listr['logrowid'];//交易来源
            $log_ip=$listr['logip'];//交易IP

            if(empty($log_row) || !is_numeric($log_row) || $log_row<1){
                $log_row='';
            }else{
                $log_row='<a href="/show.php?id='.$log_row.'" title="来源" target="_blank"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></a>';
            }

            if(empty($log_admin) || !is_numeric($log_admin) || $log_admin<1){
                $log_admin='未知会员';
            }else{
                //查询会员信息
                $user_sql="select uname from ppz_newusername where uid='$log_admin'";
                $user_rretval=mysqli_query($conn,$user_sql);
                if (mysqli_num_rows($user_rretval)==1) {
                    while($user_row=mysqli_fetch_assoc($user_rretval)){
                        $log_admin='<a href="/user.php?id='.$log_admin.'" target="_blank">'.$user_row['uname'].'</a>';
                    }
                }else{
                    $log_admin='未知会员';
                }
            }

            if (empty($log_ip)){
                $log_ip="<span style='color:red'>未知IP</span>";
            }else{
                if ($log_ip=="127.0.0.1"||$log_ip=="::1"||$log_ip=="0.0.0.0"||$log_ip=="localhost"||$log_ip=="localhost:8080"||$log_ip=="127"){
                    $log_ip="保留地址";
                }else{
                    $log_ip='<a href="https://www.ipshudi.com/'.$log_ip.'" target="_blank">'.$log_ip.'</a>';
                }
            }

            echo '
            <tr class="alt-row">
                <td style="max-width:40px;"><input type="checkbox" name="id" value="'.$log_id.'"></td>
                <td style="max-width:80px;">'.$log_type.'</td>
                <td style="max-width:100px;">'.$log_admin.'</td>
                <td style="max-width:180px;">'.$log_mun.'</td>
                <td style="max-width:80px;">'.$log_rmb.'</td>
                <td style="max-width:80px;">'.$log_money.'</td>
                <td style="max-width:120px;">'.$log_time.'</td>
                <td style="max-width:100px;">'.$log_ip.'</td>
                <td style="max-width:40px;">'.$log_row.'<a class="dellog" data-d="'.$log_id.'" title="删除"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
            </tr>
            ';

        }


        echo '
                </tbody>
                </table>
            </div>
            <script src="/style/js/log.js" type="text/javascript"></script>
        ';
    }




} else {
    echo "请勿胡搞！";
}
?>