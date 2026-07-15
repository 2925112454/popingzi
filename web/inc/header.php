<?php
header('Content-Type: text/html; charset=utf-8');
$unread_count = 0;
        //自定义判断是否是图片扩展名的函数
        function isImageFile($filename) {  
            $imageExtensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'ico','avif');//定义图片扩展名数组
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));  
            if (in_array($ext, $imageExtensions)) {  
                return true;  
            }  
            return false;  
        }
if (empty($ppzusername)){
$kiydtaghdjagd=1;//为被引用文件设置的变量，若被引入文件并不是在这里被正确引入使用，如单独打开被引入的文件等操作，引用文件将不会显示任何内容。
include $_SERVER['DOCUMENT_ROOT'].'/inc/alert.php';//自定义登录及注册弹窗文件
}else{
    $kiydtaghdjagd=0;
    $loginsql = "select * from ppz_newusername where binary uusername = $ppzusername";//查询用户数据
    $loginretval=mysqli_query($conn,$loginsql);
    if(mysqli_num_rows($loginretval) !== 1){
        session_start();
        $_SESSION = array();
        session_destroy();
        header("Location:/");
    }else{
        //获取用户数据，并赋值给变量
        $loginquery = $conn->query($loginsql);
        while($loginrow = $loginquery->fetch_array()){
        $uid=$loginrow['uid'];//获取用户id
        $uname=$loginrow['uname'];//获取昵称
        $uusername=$loginrow['uusername'];//获取用户账号
        $nulluimg=$loginrow['uimg'];//获取用户头像
        $uemail=$loginrow['uemail'];//获取用户邮箱
        $utel=$loginrow['utel'];//获取用户电话
        $ugold=$loginrow['ugold'];//获取用户积分
        $uviptime=$loginrow['uviptime'];//获取用户会员时间
        $utime=$loginrow['utime'];//获取用户注册时间
        $uban=$loginrow['uban'];//获取用户是否被禁
        $ustatus=$loginrow['ustatus'];//获取用户身份，1普通会员，2为管理员，3为副站长，4为站长
        $udate=$loginrow['udate'];//获取用户上次签到时的时间
        $utel_yes=$loginrow['utelyes'];//获取用户手机是否被验证。1未验证，2已验证
        $uemail_yes=$loginrow['uemailyes'];//获取用户邮箱是否被验证。1未验证，2已验证
        $user_url_http=$loginrow['uurl'];//网址
        $user_text_big=$loginrow['upersonal'];//简介
        $user_sex_if=$loginrow['usex'];//性别，1为男，2为女
        };
       
        $sqlpla = "SELECT * FROM ppz_commentary where pladmin = $uid"; //链接评论数据表
        $pla_resulta = mysqli_query($conn,$sqlpla); //查询评论数据
        $pla_recordsx = mysqli_num_rows($pla_resulta);  // 统计评论总数

        $sqlplagg = "SELECT * FROM ppz_ggcommentary where pladmin = $uid"; //链接公告评论数据表
        $pla_resultagg = mysqli_query($conn,$sqlplagg); //查询公告评论数据
        $pla_recordsgg = mysqli_num_rows($pla_resultagg);  // 统计公告评论总数

        $pla_records = $pla_recordsx+$pla_recordsgg;//总评论数

        $sqlrowadmin = "SELECT * FROM ppz_row where rowadmin = $uid and rowyes = 4"; //链接文章数据表
        $rowadmin_resulta = mysqli_query($conn,$sqlrowadmin); //查询文章数据
        $rowadmin_records = mysqli_num_rows($rowadmin_resulta);  // 统计文章总数

        $sqlfolus = "SELECT * FROM ppz_folus where usvip = $uid"; //链接关注数据表
        $folusresulta = mysqli_query($conn,$sqlfolus); //查询关注数据
        $folusrecords = mysqli_num_rows($folusresulta);  // 统计关注总数

        $sqlfolus2 = "SELECT * FROM ppz_folus where usuename = $uid"; //链接粉丝数据表
        $folusresulta2 = mysqli_query($conn,$sqlfolus2); //查询粉丝数据
        $folusrecords2 = mysqli_num_rows($folusresulta2);  // 统计粉丝总数

        if(empty($ugold)||!isset($ugold)||!is_numeric($ugold)){
            $ugold=0;
        }

        if (!empty($uviptime)){

            $time=time();//获取当前时间
            $newtime = strtotime($uviptime) - $time;//计算用户会员时间剩余秒数

            if ($newtime > 0){

            if ($newtime > 86400){
                $t="天";
                $timetext=floor($newtime/86400).$t;//计算用户会员时间剩余天数
            }else{
                if ($newtime > 3600){
                    $t="小时";
                    $timetext=floor($newtime/3600).$t;//计算用户会员时间剩余小时数
                }else{
                    if ($newtime > 60){
                        $t="分钟";
                        $timetext=floor($newtime/60).$t;//计算用户会员时间剩余分钟数

                    }else{
                        $t="秒";
                        $timetext=$newtime.$t;//计算用户会员时间剩余秒数
                    }
                }
            }
        }else{
            $newtime = -1;
            $timetext="0天";
        }


            }else{
            $newtime = -1;
            $timetext="0天";
         };

        if ($uban === '1'){}else{
            session_start();
            $_SESSION = array();
            session_destroy();
            header("Location:/");
            exit; // 立即终止脚本执行，确保重定向生效
        };

        if (empty($nulluimg)){ //如果用户没有上传头像,则使用默认头像
            $uimg = "/images/web/default.jpg";
        }else{
            $uimg = $nulluimg; //如果用户上传了头像，则使用上传的头像
        }

if ($ustatus==4){
$uviptext="<i>站长</i>";
}else if($ustatus==3){
    $uviptext="<i>副站长</i>";
}else if($ustatus==2){
    $uviptext="<i>管理员</i>";
}else{
    $uviptext="";
}

if (empty($udate)){ //如果用户从来没签到过
    $timetxt="签到"; 
    $timeid="newdate";
}else{
$time1 = date('Ymd',strtotime($udate));
$time2 = date('Ymd',time()); //当前时间
$diff = $time2 - $time1; // 计算时间差
if ($diff <= 0) {
$timetxt="已签到"; 
$timeid="newdatenull";
} else {  
    $timetxt="签到"; 
    $timeid="newdate";
}


}


        //获取未读私信数量
        $mesrsqlpo="select * from ppz_letter where teruser = $uid && teryes = 0";
        $mesrresultpo=mysqli_query($conn,$mesrsqlpo);
        $mesmunpo = mysqli_num_rows($mesrresultpo); //获取数量
        if ($mesmunpo>0){
            
            if ($mesmunpo>100){
                $mesmunpotxt="99+";
            }else{
                $mesmunpotxt=$mesmunpo;
            }

            $mespot="<div class='mesherd'>".$mesmunpotxt."</div>";
        }else{
            $mespot="";
        }

        // 查询未读回复数
            function get_direct_reply_to_me_ids($conn, $uid) {
                $reply_ids = array();
                
                // 第一步：获取你的所有评论ID
                $my_comm_sql = "SELECT comm_id FROM ppz_subcomm WHERE comm_admin = {$uid}";
                $my_comm_ret = mysqli_query($conn, $my_comm_sql);
                $my_comm_ids = array();
                if ($my_comm_ret && mysqli_num_rows($my_comm_ret) > 0) {
                    while ($row = mysqli_fetch_array($my_comm_ret)) {
                        $my_comm_ids[] = intval($row['comm_id']);
                    }
                    mysqli_free_result($my_comm_ret);
                }
                
                if (empty($my_comm_ids)) {
                    return $reply_ids;
                }
                
                // 第二步：获取直接回复你评论的回复ID（父评论ID是你的评论ID）
                $my_comm_ids_str = implode(',', $my_comm_ids);
                $direct_reply_sql = "SELECT comm_id FROM ppz_subcomm 
                                    WHERE comm_type IN ({$my_comm_ids_str}) 
                                    AND comm_admin != {$uid}";
                $direct_reply_ret = mysqli_query($conn, $direct_reply_sql);
                if ($direct_reply_ret && mysqli_num_rows($direct_reply_ret) > 0) {
                    while ($row = mysqli_fetch_array($direct_reply_ret)) {
                        $reply_ids[] = intval($row['comm_id']);
                    }
                    mysqli_free_result($direct_reply_ret);
                }
                
                return $reply_ids;
            }
            if ($uid > 0) {
                // 统计1：直接回复我评论的未读回复
                $direct_reply_ids = get_direct_reply_to_me_ids($conn, $uid);
                $related_ids_str = !empty($direct_reply_ids) ? implode(',', $direct_reply_ids) : '-1';
                
                $direct_unread_sql = "SELECT COUNT(comm_id) AS unread_num FROM ppz_subcomm WHERE comm_id IN ({$related_ids_str}) AND comm_yes = 0";
                $direct_unread_ret = mysqli_query($conn, $direct_unread_sql);
                $direct_unread_num = 0;
                if ($direct_unread_ret && mysqli_num_rows($direct_unread_ret) > 0) {
                    $direct_unread_row = mysqli_fetch_array($direct_unread_ret);
                    $direct_unread_num = intval($direct_unread_row['unread_num']);
                }

                // 统计2：评论我文章的未读评论
                $article_unread_sql = "SELECT COUNT(sc.comm_id) AS unread_num FROM ppz_subcomm sc LEFT JOIN ppz_subject s ON sc.comm_subid = s.id WHERE sc.comm_type = 0 AND s.id IS NOT NULL AND s.yes = 3 AND s.admin = {$uid} AND sc.comm_admin != {$uid} AND sc.comm_yes = 0";
                $article_unread_ret = mysqli_query($conn, $article_unread_sql);
                $article_unread_num = 0;
                if ($article_unread_ret && mysqli_num_rows($article_unread_ret) > 0) {
                    $article_unread_row = mysqli_fetch_array($article_unread_ret);
                    $article_unread_num = intval($article_unread_row['unread_num']);
                }

                // 总未读数
                $unread_count = $direct_unread_num + $article_unread_num;
                
                // 安全释放资源
                if (is_resource($direct_unread_ret)) {
                    mysqli_free_result($direct_unread_ret);
                }
                if (is_resource($article_unread_ret)) {
                    mysqli_free_result($article_unread_ret);
                }
            }


    };
    
}
if(empty($webtoplogourl)){
    $webtoplogourl="/";
    $webtoplogourltarget="";
}else{
    $webtoplogourl=$webtoplogourl;
    //判断$webtoplogourl是否包含http://、https://、//
    if (strpos($webtoplogourl, 'http://') !== false || strpos($webtoplogourl, 'https://') !== false || strpos($webtoplogourl, '//') !== false) {
        $webtoplogourltarget="target='_blank'";
    } else {
        $webtoplogourltarget="";
    }
}

if (empty($ppzusername) ){
    $vip_href='
    <li class="menu-li"><a id="nologin"><i class="fa fa-credit-card-alt" aria-hidden="true"></i>充值商城</a></li>
    <script>
        const nologin = document.getElementById("nologin");
        if (nologin) {
            nologin.addEventListener("click", function () {
                alert("<font>(,,•́ . •̀,,)</font> 请先登录！");
            });
        }
    </script>
    ';
}else{
    $vip_href='<li class="menu-li"><a href="/vip/"><i class="fa fa-credit-card-alt" aria-hidden="true"></i>充值商城</a></li>';
}

?>
<noscript>您禁用了本站的JavaScript，此操作将导致本站功能受限，请在浏览器中允许本站运行JavaScript！<p><a href="https://www.baidu.com/s?wd=%E5%A6%82%E4%BD%95%E5%BC%80%E5%90%AF%E6%B5%8F%E8%A7%88%E5%99%A8JavaScript" target='_blank'>如何开启浏览器JavaScript？</a></p></noscript>
<header>
<div class="top">
<div class="social-top">
    <div class="social-logo">
          <div class="logo-img" style="background-image: url(<?php echo $weblogo;?>);"><a href="<?php echo $webtoplogourl;?>" <?php echo $webtoplogourltarget;?>></a></div>
          <div class="logo-menu">
            <ul class="menu-ul">
                <?php echo $vip_href;?>
                <?php
                $subsettext_msg="";
                if ($unread_count > 0) {
                    $subsettext_msg='<div class="submsg_yes"></div>';
                }
                if ($set_off<1||empty($set_off)){
                    $subsettext='<li class="menu-li"><a href="/anct.php"><i class="fa fa-question-circle"></i>平台公告</a></li>';
                }else{
                    $subsettext='<li class="menu-li subli"><a href="/subject/"><i class="fa fa-coffee" aria-hidden="true"></i>'.$set_title.'</a>'.$subsettext_msg.'</li>';
                }
                echo $subsettext;?>
                <li class="menu-li"><a href="/top.php"><i class="fa fa-tags"></i>排行榜</a></li>
            </ul>
          </div>
    <div class="search">
        <div class="search-div">
            <form id="search" onsubmit="return checkformss()" class="search-form" name="search" method="get" action="/search.php">
            <input  id="sinput" z type="text" name="s" class="search-input" maxlength="20" onkeyup="checkLen2(this)" placeholder="多个关键词用两个减号 -- 分隔" value="<?php if(isset($s)){echo $s;}?>">
            <button type="submit" class="search-button"><i class="fa fa-search"></i></button>
            </form>
        </div>
        <div class="up-new" title="投稿" deta-title><a <?php if ($ppzusername ==""){echo 'onclick="loginFunction()"';}else{echo 'href="/user/user.php?type=10"'; }?>  ><i class="fa fa-edit"></i></a></div>
        <div class="up-new" title="消息" deta-title><a <?php if ($ppzusername ==""){echo 'onclick="loginFunction()"';}else{echo 'href="/user/message.php"'; }?>><?php if (isset($mespot)) { echo $mespot; }?><i class="fa fa-comments-o"></i></a></div>
<?php
    if (empty($ppzusername) ){
        echo ' <div class="login-button"><a id="showModaladl">登录</a></div> <div class="login-button"><a id="showModalazc" >注册</a></div>';
    
    }else{
        
        echo '
            <div class="login-img" style="background-image: url('.$uimg.');"><a id="openinfolog"></a></div>
        <dialog id="infolog" class="infologshow">
            <div class="infolog">
                    <div class="infolog-div-img" style="background-image: url('.$uimg.');"></div>
                 <div class="infolog-div"><span>'.$uusername.'</span><span>会员剩余：'.$timetext.'</span></div>
                 <a class="infolog-a" title="退出登录" onclick="outlogin()" deta-title><i class="fa fa-sign-in"></i></a>
            </div>
            <div class="infolog-new">
             <div class="statistics-div"><a href="/user/user.php?type=3"><span>文章</span><span>'.$rowadmin_records.'</span></a></div>
             <div class="statistics-div"><a href="/user/user.php?type=4"><span>评论</span><span>'.$pla_records.'</span></a></div>
             <div class="statistics-div"><a href="/user/user.php?type=7"><span>关注</span><span>'.$folusrecords.'</span></a></div>
             <div class="statistics-div"><a href="/user/user.php?type=6"><span>粉丝</span><span>'.$folusrecords2.'</span></a></div>
            </div>
            <div class="infolog-mygold">
                <a class="infolog-gold" ><i class="fa fa-database"></i><b id="newugoldone">'.$ugold.'</b><span>积分</span></a>
                <a class="infolog-czgold" id="'.$timeid.'" >'.$timetxt.'</a>
            </div>
            <div class="infolog-my">
            <a href="/user/user.php?type=2" ><div class="publishing"><i class="fa fa-server"></i>我的资料</div></a>
            <a href="/user/user.php?type=3" ><div class="publishing"><i class="fa fa-pencil-square"></i>我的文章</div></a>
            <a href="/user/user.php?type=11" ><div class="publishing"><i class="fa fa-shopping-bag" aria-hidden="true"></i>我的购买</div></a>
            <a href="/user/user.php?type=5" ><div class="publishing"><i class="fa fa-star"></i>我的收藏</div></a>
            <a href="/user/user.php?type=9" ><div class="publishing"><i class="fa fa-book" aria-hidden="true"></i>提交工单</div></a>
            <a href="/user/user.php?type=8" ><div class="publishing"><i class="fa fa-archive" aria-hidden="true"></i>我的工单</div></a>
            <a href="/user/user.php?type=1" class="wap_mybuy"><div class="publishing"><i class="fa fa-address-card" aria-hidden="true"></i>我的会员</div></a>
            </div>';
if ($ustatus==4||$ustatus==3||$ustatus==2){
    echo '   <a class="workorder" href="/user/popingzi.php" >后台管理</a>';
}else{
    echo '  <a class="workorder" href="/user/user.php">我的会员</a>';
}
 echo ' </dialog>
        <script>
function toggleDialog() {  
	var infolog = document.getElementById("infolog");  
	var isOpeninfolo = window.getComputedStyle(infolog).display !== "none";  
	  
	if (isOpeninfolo) {  
    infolog.classList.remove("open");
	} else {  
    infolog.classList.add("open");
	};

  };

var openinfolog = document.getElementById("openinfolog");  
var infolog = document.getElementById("infolog");  
  
openinfolog.addEventListener("click", function() {  
    toggleDialog(); 
});  
  
document.addEventListener("click", function(event) {  
  if (event.target !== openinfolog && event.target !== infolog) {  
    infolog.classList.remove("open");
  }  
});  
        </script>      
        ';
}
?>
        
    </div>
    </div>
</div>
<nav class="social-bot"  id="navbar" ><div class="social-bot-a">
     <div class="social-bot-left">
        <ul class="home-menu-ul">
            <a href="/"><div class="home-menu-li"><span class="home-span"><i class="fa fa-home"></i>首页</span></div></a>
            <?php
            $linksql = "select * from ppz_link order by linkid asc";//获取导航栏列表,正序
            $linkretval=mysqli_query($conn,$linksql);
            if(mysqli_num_rows($linkretval) < 1){
            }else{
            
                $linkquery = $conn->query($linksql);
                    while($linkrow = $linkquery->fetch_array()){
                    $linkid=$linkrow['linkid'];//获取列表id
                    $linkname=$linkrow['linkname'];//获取列表名称
                    $linkico=$linkrow['linkico'];//获取列表图标
                    if(!empty($linkico)){
                        //检查$linkico是否以fa-开头
                        if(strpos($linkico,'fa-') !== false){
                            $linkicoyes='<i class="fa '.$linkico.'"></i>';
                        }else{
                            if (filter_var($linkico, FILTER_VALIDATE_URL) !== false){//检查$linkico是否为url，是则输出img标签
                                $linkicoyes='<img class="link-img" src="'.$linkico.'" alt="'.$linkname.'">';
                            }else{
                                if (isImageFile($linkico)) {//检查$linkico是否为图片后缀，是则输出img标签
                                    $linkicoyes='<img class="link-img" src="'.$linkico.'" alt="'.$linkname.'">';
                                } else {
                                    $linkicoyes=$linkico;//不是图片后缀，则输出本体(即字符串本身)
                                }
                            }
                        }
                    }else{
                        $linkicoyes='';
                    }
                    echo ' <a href="/list.php?id='.$linkid.'"><div class="home-menu-li"><span class="home-li-span">'.$linkicoyes.$linkname.'</span></div></a>';
                    };
            };
                if ($set_off>0&&!empty($set_off)){
                    $padgg="pad_gg";
                }else{
                    $padgg="";
                }
            ?>
            <a href="/anct.php" class="wap_gg <?php echo $padgg;?>"><div class="home-menu-li"><span class="home-span"><i class="fa fa-question-circle"></i>公告</span></div></a>
            <a href="/top.php" class="wap_gg"><div class="home-menu-li"><span class="home-span"><i class="fa fa-tags"></i>排行榜</span></div></a>
    </ul>
     </div>
     <div class="social-bot-right">
     <ul class="home-menu-ul">
            <a <?php if (empty($ppzusername)){echo 'onclick="loginFunction()"';}else{echo 'id="vipa"'; }?>><div class="vip-menu-li"><span class="vip-li-span"><i class="fa fa-diamond"></i>充值中心</span></div></a>
    </ul>

     </div></div>
</nav>
</div>
</header>

<?php if (!empty($ppzusername)){
    echo '
    <dialog id="viplog" class="viplog">
    <div class="Signinlog-div"><b class="vipb">充值中心</b></div>
    <span id="vipxx" class="x"></span>
    <form id="viplogform" method="post">  
      <div class="input-all"><label class="label-sign" for="usernamevip"><input type="text" id="usernamevip" name="usernamevip" required />  <span><b>充值卡号</b></span> </label> </div>
      <input class="sign-inp" type="submit" value="确定" id="vipbut"/>
    </form>
    <button id="vipx"><i class="fa fa-times"></i></button>
    <div class="sign-txt"><a href="/vip/" id="vippa">丨购买充值卡（可选购VIP会员或积分）</a></div> 
    </dialog>
    <dialog id="newdataerr" class="alerterr"></dialog>
    <script src="/style/js/viplog.js" type="text/javascript"></script>
    <script src="/style/js/newdate.js" type="text/javascript"></script>'; 
}
?>