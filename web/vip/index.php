<?php
include $_SERVER['DOCUMENT_ROOT'].'/inc/inc.php';//通用
$cardsetprice = 0;

// 连接充值卡配置数据库
$cardsetsql = "SELECT * FROM `ppz_cardset` WHERE `setid`=1";
$cardsetresult = mysqli_query($conn, $cardsetsql);

//连接充值卡数据库
$month_vip_invitex_sql = "SELECT * FROM `ppz_vtime` WHERE `vbin`=1";//vbin是充值卡类型：1月度会员，2季度会员，3年度会员，4百年会员，5积分充值
$month_inviteresult = mysqli_query($conn, $month_vip_invitex_sql);
if (mysqli_num_rows($month_inviteresult) > 0) {
    $month_size=mysqli_num_rows($month_inviteresult);
}else{
    $month_size=0;
}
//连接充值卡数据库
$quarter_vip_invitex_sql = "SELECT * FROM `ppz_vtime` WHERE `vbin`=2";
$quarter_inviteresult = mysqli_query($conn, $quarter_vip_invitex_sql);
if (mysqli_num_rows($quarter_inviteresult) > 0) {
    $quarter_size=mysqli_num_rows($quarter_inviteresult);
}else{
    $quarter_size=0;
}


function getVipCardCount($conn, $new_vbin, $new_vgold = null) {
    $new_vbin = intval($new_vbin);
    if(!empty($new_vgold)){
        $new_vgold = intval($new_vgold);
    }  

    if (!empty($new_vbin) && !empty($new_vgold) && $new_vbin == 5) {
        $vip_size_sql = "SELECT * FROM `ppz_vtime` WHERE `vbin` = 5 AND `vgold` = $new_vgold";
    } else {
        $vip_size_sql = "SELECT * FROM `ppz_vtime` WHERE `vbin` = $new_vbin";
    }
    $vip_size_result = mysqli_query($conn, $vip_size_sql);
    return $vip_size_result ? mysqli_num_rows($vip_size_result) : 0;
}



if (mysqli_num_rows($cardsetresult) !== 1) {
    $cardsetprice = 0;
} else {
    $cardsetprice = 1;
    $cardsetx = $cardsetresult->fetch_array();
    
    // 定义会员和积分配置
    $membership_types = [
        'month' => ['name' => '月会员', 'url' => 'seturlyue', 'price' => 'setrmbyue', 'duration' => '+30天','size' => 1,'class_diy' => 'month','class_text' => 'month_font','class_but'=> 'month_but'],
        'quarter' => ['name' => '季会员', 'url' => 'seturlji', 'price' => 'setrmbji', 'duration' => '+90天','size' => 2,'class_diy' => 'quarter','class_text' => 'quarter_font','class_but'=> 'quarter_but'],
        'year' => ['name' => '年会员', 'url' => 'seturlnian', 'price' => 'setrmbnian', 'duration' => '+360天','size' => 3,'class_diy' => 'year','class_text' => 'year_font','class_but'=> 'year_but'],
        'allyear' => ['name' => '百年会员', 'url' => 'seturlbai', 'price' => 'setrmbbai', 'duration' => '+36000天','size' => 4,'class_diy' => 'allyear','class_text' => 'allyear_font','class_but'=> 'allyear_but']
    ];
    
    $points_types = [
        'shi' => ['name' => '10积分', 'url' => 'seturlshi', 'price' => 'setrmbshi','size' => 1],
        'er' => ['name' => '20积分', 'url' => 'seturler', 'price' => 'setrmber','size' => 2],
        'san' => ['name' => '30积分', 'url' => 'seturlsan', 'price' => 'setrmbsan','size' => 3],
        'si' => ['name' => '40积分', 'url' => 'seturlsi', 'price' => 'setrmbsi','size' => 4],
        'wu' => ['name' => '50积分', 'url' => 'seturlwu', 'price' => 'setrmbwu','size' => 5],
        'yi' => ['name' => '100积分', 'url' => 'seturlyi', 'price' => 'setrmbyi','size' => 6],
        'qian' => ['name' => '1000积分', 'url' => 'seturlqian', 'price' => 'setrmbqian','size' => 7]
    ];
    
    // 处理会员信息
    $memberships = [];
    foreach ($membership_types as $key => $type) {
        $url = htmlspecialchars(isset($cardsetx[$type['url']]) ? $cardsetx[$type['url']] : '');
        $price = htmlspecialchars(isset($cardsetx[$type['price']]) ? $cardsetx[$type['price']] : '0');
        
        $memberships[$key] = [
            'name' => $type['name'],
            'url' => $url,
            'price' => $price,
            'duration' => $type['duration'],
            'class' => empty($url) ? 'disabled-link disabled-link-nullvip' : '',
            'target' => !empty($url) && preg_match('#^(http|https|ftp)://|//#i', $url) ? 'target="_blank"' : '',
            'size' => $type['size'],
            'class_diy' => $type['class_diy'],
            'class_text' => $type['class_text'],
            'class_but' => $type['class_but']
        ];
    }
    
    // 处理积分信息
    $points = [];
    foreach ($points_types as $key => $type) {
            $url = htmlspecialchars(isset($cardsetx[$type['url']]) ? $cardsetx[$type['url']] : '');
            $price = htmlspecialchars(isset($cardsetx[$type['price']]) ? $cardsetx[$type['price']] : '0');
            
            $points[$key] = [
                'name' => $type['name'],
                'url' => $url,
                'price' => $price,
                'class' => empty($url) ? 'disabled-link disabled-link-nullvip' : '',
                'target' => !empty($url) && preg_match('#^(http|https|ftp)://|//#i', $url) ? 'target="_blank"' : '',
                'size' => $type['size']
            ];
    }
}

if (empty($ppzusername) || $cardsetprice != 1) {
    header("Location:/");//跳转
    exit;
}

if (strtotime($allviptime) - time() <= 0) {
    $viptimenow = "普通会员";
    $viptextnow = "购买充值卡";
} else {
    $viptimenow = "VIP会员（".$allviptime."）";
    $viptextnow = "续买充值卡";
}
// 获取VIP会员折扣信息
$vipz_sql="select upvipsize from ppz_upfile where id=1";
$vipz_res=mysqli_query($conn,$vipz_sql);
if(mysqli_num_rows($vipz_res) > 0){
    while($vipz_row=mysqli_fetch_array($vipz_res)){
        $vipz_size=$vipz_row['upvipsize'];
    }
        if($vipz_size>=0&&$vipz_size<=100){
            $vipz_size=$vipz_size;
        }else{
            $vipz_size=0;
        }
}else{
    $vipz_size=0;
}
if($vipz_size>100||$vipz_size<=0){
    $vipz_size="";
}else{
    if($vipz_size==100){
        $vipz_size='积分下载全站<span class="vipzk">免费</span>';
    }else{
        //转换为折扣
        $vipz_size_s=round((1-$vipz_size/100)*10,1);
        $vipz_size='积分下载享<span class="vipzk">'.$vipz_size_s.'折</span>优惠';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN" translate="no">
<head>
    <meta charset="utf-8">
    <title>充值商城 - <?php echo $webtext;?>丨<?php echo $webby;?></title>
    <meta name="keywords" content="<?php echo $webpass;?>" />
    <meta name="description" content="<?php echo $webvar;?>" />
    <link rel="icon" href="/favicon.ico"/>
    <?php @include $_SERVER['DOCUMENT_ROOT'].'/inc/style.php';?>
    <link type="text/css" rel="stylesheet" href="/style/css/font-awesome-4.7.0/css/font-awesome.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @media (max-width: 480px){
            .social-bot-right {
                display: block !important;
                position: fixed;
                bottom:9rem;
                right: -1rem;
            }
        }
    </style>
    <script src="/style/js/jquery-3.5.1.min.js" type="text/javascript"></script>
    <script src="/style/js/input.js" type="text/javascript"></script>
    <script src="/style/js/alert.js" type="text/javascript"></script>
</head>
<body>
    <?php @include $_SERVER['DOCUMENT_ROOT'].'/inc/header.php';//头部?>
    <div class="body-div">
        <div class="body-div-main nocopy">
            <div class="body-div-main-title"><span>丨</span>VIP充值卡<i>您当前的身份：<?php echo htmlspecialchars($viptimenow);?></i></div>
            <div class="body-div-main-content">
                <?php foreach ($memberships as $membership): ?>
                <div class="body-div-main-content-two">
                    <div class="body-div-main-content-two-title <?php echo $membership['class_diy']; ?>"><?php echo $membership['name']; ?></div>
                    <div class="body-div-main-content-two-content">
                        <div class="body-div-main-content-two-content-rmb <?php echo $membership['class_text']; ?>"><div class="margin-right-5px">¥</div><?php echo $membership['price']; ?></div>
                        <div class="body-div-main-content-two-content-text">VIP期限：<?php echo $membership['duration']; ?></div>
                        <div class="body-div-main-content-two-content-texttwo">
                            <ul>
                                <li>全站免广告</li>
                                <li>VIP专属资源</li>
                                <li>24h专属客服</li>
                                <li>全站内容不限阅览</li>
                                <li><?php echo $vipz_size; ?></li>
                                <li class="texttwo-li">库存<?php echo isset($membership['size']) ? getVipCardCount($conn, (int)$membership['size']) : 0; ?>张</li>
                            </ul>
                        </div>
                        <a href="<?php echo $membership['url']; ?>" <?php echo $membership['target']; ?> class="body-div-main-content-two-content-button <?php echo $membership['class']; ?> <?php echo $membership['class_but']; ?>"><?php echo htmlspecialchars($viptextnow); ?></a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="body-div-main-title"><span>丨</span>积分充值卡<i>您当前的积分：<?php echo $ugold; ?></i></div>
            <div class="body-div-main-content padding_left_5px padding_right_5px">
                <?php foreach ($points as $point): ?>
                <div class="body-div-main-content-jifen">
                    <div class="body-div-main-content-two-title"><?php echo $point['name']; ?></div>
                    <div class="body-div-main-content-two-content">
                        <div class="body-div-main-content-two-content-rmb"><div class="margin-right-5px">¥</div><?php echo $point['price']; ?></div><div class="padding_bottom_15px text-align-center texttwo-li">库存<?php echo isset($point['size']) ? getVipCardCount($conn,5,(int)$point['size']) : 0; ?>张</div>
                        <a href="<?php echo $point['url']; ?>" <?php echo $point['target']; ?> class="body-div-main-content-two-content-button <?php echo $point['class']; ?>">获取卡号</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php @include $_SERVER['DOCUMENT_ROOT'].'/inc/footer.php';?>
    <?php @include $_SERVER['DOCUMENT_ROOT'].'/wap/index.php';?>
</body>
</html>