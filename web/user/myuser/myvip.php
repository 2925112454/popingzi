<?php
ob_start();
if(!isset($myuser)||empty($myuser)||$myuser!=200||!isset($ppzusername)||empty($ppzusername)||!isset($typeuser)||empty($typeuser)||$typeuser!=1){
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
if(time()<strtotime($allviptime)){
    $vipcard='<div class="vipcard padding_15px">会员卡<div class="vipcard_id">ID：'.$allnameid.'</div><div class="vipcard_if padding_15px">VIP会员</div><div class="vipcard_time">有效期至：'.$allviptime.'</div></div>';
}else{
    $vipcard='<div class="vipcard padding_15px novipcard">会员卡<div class="vipcard_id">ID：'.$allnameid.'</div><div class="vipcard_if padding_15px">普通会员</div><div class="vipcard_time">有效期至：永久</div></div>';
}
function numberToChinese($num) {
    $chineseNum = ['零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖'];
    $chineseUnit = ['', '拾', '佰', '仟', '万', '拾', '佰', '仟', '亿'];
    $numStr = (string)$num;
    $result = '';
    $length = strlen($numStr);
    
    for ($i = 0; $i < $length; $i++) {
        $digit = (int)$numStr[$i];
        $unitPos = $length - $i - 1;
        
        if ($digit != 0) {
            $result .= $chineseNum[$digit] . $chineseUnit[$unitPos];
        } else {
            // 处理连续的零
            if ($i < $length - 1 && (int)$numStr[$i + 1] != 0) {
                $result .= $chineseNum[$digit];
            }
        }
    }
    
    return $result;
}
if($ugold>50000000){
    $ugold_text='<div class="star">Lv.10<span>万界主宰</span></div>';
}elseif($ugold>10000000){
    $ugold_text='<div class="star">Lv.9<span>财神在世</span></div>';
}elseif($ugold>1000000){
    $ugold_text='<div class="star">Lv.8<span>富甲天下</span></div>';
}elseif($ugold>500000){
    $ugold_text='<div class="star">Lv.7<span>富可敌国</span></div>';
}elseif($ugold>100000){
    $ugold_text='<div class="star">Lv.6<span>腰缠万贯</span></div>';
}elseif($ugold>50000){
    $ugold_text='<div class="star">Lv.5<span>金玉满堂</span></div>';
}elseif($ugold>10000){
    $ugold_text='<div class="star">Lv.4<span>衣食无忧</span></div>';
}elseif($ugold>5000){
    $ugold_text='<div class="star">Lv.3<span>略有结余</span></div>';
}elseif($ugold>1000){
    $ugold_text='<div class="star">Lv.2<span>捉襟见肘</span></div>';
}elseif($ugold>100){
    $ugold_text='<div class="star">Lv.1<span>两袖清风</span></div>';
}else{
    $ugold_text='<div class="star">Lv.0<span>数字难民</span></div>';
}

$formattedNumber = number_format($ugold);
$chineseNumber = numberToChinese($ugold);
if (!isset($_GET["p"])){ 
    $_GET["p"]="";
}

$num_rec_per_page=20;// 每页显示数量


    $getp=trim($_GET["p"]);//获取GET传参P
    /*判断参数P是否为空，且是否是数字*/
    if (isset($getp) && is_numeric($getp) && $getp>=1 && !empty($getp)){ 
        $pa = $getp;
    } else {
        $pa=1; 
    };

    $sqlll = "SELECT * FROM ppz_log where logadmin=$allnameid";
    $rs_resultll = mysqli_query($conn, $sqlll);//执行SQL语句，获取结果集
    $total_recordsll = mysqli_num_rows($rs_resultll);//获取记录总数
    $total_pages = ceil($total_recordsll / $num_rec_per_page);//计算总页数
    if ($total_pages < $pa){
       $p=1;
       }else{
       $p=$pa; 
    }
   $start_from = ($p-1) * $num_rec_per_page;
   if ($p==1||empty($p)){
       $pageindex='<a class="page-no-button nocopy">首页</a>';//首页按钮
   }else{
       $pageindex='<a class="page-button nocopy" href="?type=1">首页</a>';//首页按钮
   }
   
   if ($p==$total_pages||$total_pages<1){
       $pagebody='<a class="page-no-button nocopy" >尾页</a>';
   }else{
       $pagebody='<a class="page-button nocopy" href="?type=1&p='.$total_pages.'">尾页</a>';
   }
   
   if ($total_pages>1&&$p<$total_pages){
       $exit=$p+1;
       $pageexit='<a class="page-button nocopy" href="?type=1&p='.$exit.'">下一页</a>';
   }else{
       $pageexit='<a class="page-no-button nocopy" >下一页</a>';
   }
   
   if ($p<=$total_pages&&$p>1){
       $exitup=$p-1;
       $pageup='<a class="page-button nocopy" href="?type=1&p='.$exitup.'">上一页</a>';
   }else{
       $pageup='<a class="page-no-button nocopy" >上一页</a>';
   }

    $rsql = "$sqlll ORDER BY logid desc LIMIT $start_from, $num_rec_per_page";//获取数据库表
    $rretval=mysqli_query($conn,$rsql);

echo '<div class="user-h1">我的会员</div>
<div class="padding_15px flex-wrap">
    '.$vipcard.'<div class="vipcard padding_15px rmbcard">'.$ugold_text.'积分卡<div class="vipcard_id">ID：'.$allnameid.'</div><div class="vipcard_if padding_15px">'.$formattedNumber.'</div><div class="vipcard_time">大写：'.$chineseNumber.'</div></div>
</div>
<div class="padding_15px flex-wrap">
    <div class="vipcard-title">积分记录</div>

    <div class="padding_10px vipcard-list">
        <table class="regtxt-table">
                <thead>
                  <tr>
                    <th width="20%">时间</th>
                    <th width="20%">来源</th>
                    <th width="15%">积分变化</th>
                    <th width="15%">剩余积分</th>
                    <th width="30%">订单号</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <td colspan="5">
                        <div class="clear">
                          <span class="page-left">第'.$p.'页（共'.$total_pages.'页）- 共计：'.$total_recordsll.'条记录</span>
                          <span class="page-right">
                          '.$pageindex.$pageup.$pageexit.$pagebody.'
                          </span>
                      </div></td>
                  </tr>
                </tfoot>
                <tbody>';
                if(mysqli_num_rows($rretval) < 1){
                    echo '<tr class="alt-row"><td  colspan="5">暂无积分记录</td></tr>';
                }else{
                    while($listrlog = mysqli_fetch_array($rretval)){
                        $log_time=$listrlog['logtime'];//时间
                        $log_type=$listrlog['logtype'];//来源
                        $log_num=$listrlog['logrmb'];//变化
                        $log_order=$listrlog['logab'];//余额
                        $log_mun=$listrlog['logmun'];//订单号
                        $log_rowurl=$listrlog['logrowid'];//文章来源ID
                        if(!empty($log_rowurl)){
                            $log_type='<div class="flex-center">'.$log_type.'<a href="/show.php?id='.$log_rowurl.'" title="前往资源页" target="_blank"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></a></div>';
                        }
                        //替换
                        $log_num=str_replace("-",'<span class="log_span_r">-</span>',$log_num);
                        $log_num=str_replace("+",'<span class="log_span_g">+</span>',$log_num);
                        
                        echo '
                        <tr class="alt-row">
                            <td>'.$log_time.'</td>
                            <td>'.$log_type.'</td>
                            <td>'.$log_num.'</td>
                            <td>'.$log_order.'</td>
                            <td>'.$log_mun.'</td>
                        </tr>';
                    }                    
                }
                        
          echo '</tbody>
            </table>
    </div>

</div>
';
ob_end_flush();
?>