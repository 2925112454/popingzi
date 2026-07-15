<footer>
<div class="footer-div">
<div class="footer-div-h">
    <p><img src="<?php echo $webbutlogo;?>" /></p>
<p><?php echo $webvar;?></p>
<p><?php echo $webfooter;?></p>
</div>
<div class="footer-div-l">
<h3>联系我们</h3>
<div class="link">
    <?php
    if ((empty($webqqurl)&&empty($webqqqurl)&&empty($webwburl)&&empty($webemail))){
        echo '<p>暂无联系方式</p>';
    }else{

        if (!empty($webqqurl)){
            echo '<a href="'.$webqqurl.'" target="_blank">QQ客服</a>';
        };
        if (!empty($webqqqurl)){
            echo '<a href="'.$webqqqurl.'" target="_blank">QQ群聊</a>';
        };
        if (!empty($webwburl)){
            echo '<a href="'.$webwburl.'" target="_blank">新浪微博</a>';
        };
        if (!empty($webemail)){
            echo '<a href="mailto:'.$webemail.'" target="_blank">电子邮箱</a>';
        };

    }

    ?>
</div>
</div>
<div class="footer-div-c"><h3>新媒体账号</h3>
<img src="<?php echo $newnetewm;?>" />
</div>
<div class="footer-div-r"><h3>最新用户</h3>
<div class="link-vip">
<?php
$aasql = "select * from ppz_newusername ORDER BY uid desc LIMIT 10";//获取最新用户
$aaretval=mysqli_query($conn,$aasql);
if(mysqli_num_rows($aaretval) < 1){
    echo "暂无用户";
}else{

    $aaquery = $conn->query($aasql);
        while($aarow = $aaquery->fetch_array()){
        $aid=$aarow['uid'];//获取用户id
        $aname=$aarow['uname'];//获取昵称
        $nullaimg=$aarow['uimg'];//获取用户头像
        if (is_null($nullaimg) || $nullaimg == ""){
            $aimg="/images/web/default.jpg";
        }else{
            $aimg=$nullaimg;
        }
        echo '<a title="'.$aname.'" href="/user.php?id='.$aid.'" target="_blank"><img src="'.$aimg.'" /></a>';
        };
}
mysqli_close($conn); //关闭数据库连接
?>
</div>
</div>
</div>
</footer>


<div id="backToTop" title="返回顶部"><i class="fa fa-chevron-up"></i></div>
<script src="/style/js/title.js" type="text/javascript"></script>
<script src="/style/js/style.js" type="text/javascript"></script>
<script src="/style/js/isie.js" type="text/javascript"></script>