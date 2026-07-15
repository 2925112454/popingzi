<?php
@include $_SERVER['DOCUMENT_ROOT'].'/inc/conn.php';//连接数据库
@include $_SERVER['DOCUMENT_ROOT'].'/inc/session.php';//引入session变量
$websql = "select * from ppz_web where webid = 1";//获取网站信息
$webretval=mysqli_query($conn,$websql);
if(mysqli_num_rows($webretval) !== 1){
}else{
$webquery = $conn->query($websql);
    while($web = $webquery->fetch_array()){
      $webtext=$web['webtext'];//网站标题
      $webpass=$web['webpass'];//关键词
      $webvar=$web['webvar'];//简介
      $webfooter=$web['webfooter'];//版权说明
      $webqqurl=$web['webqqurl'];//QQ链接
      $webwburl=$web['webwburl'];//微博链接
      $webqqqurl=$web['webqqqurl'];//QQ群链接
      $webemail=$web['webemail'];//邮箱
      $webby=$web['webby'];//标语
      $webip=$web['webip'];//拉黑ip名单
      $weblogo=$web['weblogo'];//logo
      $webtoplogourl=$web['toplogourl'];
      $webbutlogo=$web['webbutlogo'];//底部logo
      $newnetewm=$web['webnewnet'];//底部新媒体二维码
      $webmaxsize=$web['webmaxsize'];//服务器被分配的最大储存空间
    };
};
//安全验证策略
if (!empty($ppzusername)){
  $fuusqler = "select * from ppz_newusername where binary uusername = $ppzusername";//获取登录信息
  $fuuretvaler=mysqli_query($conn,$fuusqler);
  if(mysqli_num_rows($fuuretvaler) !== 1){
    $_SESSION = array();
    session_destroy();
    header("Location:/");    
  }else{
    $fuuqueryer = $conn->query($fuusqler);
    while($fuuer = $fuuqueryer->fetch_array()){
      $fuulogintimeer = $fuuer['ulogintime'];//从数据库最近登录时间
      $fuuloginname = $fuuer['uname'];//通用会员昵称
      $fuuiper = $fuuer['uip'];//注册ip
      $allnameid=$fuuer['uid'];//通用会员id
      $allvip=$fuuer['ustatus'];//通用会员身份；1普通会员，2为管理员，3为副站长，4为站长
      $allviptime=$fuuer['uviptime'];//通用VIP会员时间
      $imghed=$fuuer['uimg'];//头像
    }
 
  if (empty($imghed)){ //如果用户没有上传头像,则使用默认头像
      $hedimg = "/images/web/default.jpg";
  }else{
      $hedimg = $imghed; //如果用户上传了头像，则使用上传的头像
  }

    //判断最近登录时间距离现在相差是否超过3小时
    if (time() - strtotime($fuulogintimeer) > 10800){
        $_SESSION = array();
        session_destroy();
        header("Location:/");
    }else{
      if(!isset($_SESSION["logintime"])){
        $_SESSION["logintime"]="";
      }
      $cklogintime = $_SESSION["logintime"];//登录时间戳SESSION
      if (empty($fuulogintimeer)||$cklogintime !== strtotime($fuulogintimeer)||empty($cklogintime)||!isset($cklogintime)){
        $_SESSION = array();
        session_destroy();
        header("Location:/");
      }else{

        if (!empty($webip)){
          $nowip = $_SERVER['REMOTE_ADDR'];//获取用户客户端ip
          $webiparr= explode("|",$webip);//转换ip黑名单为|分割的数组
          if (in_array($nowip,$webiparr)||in_array($fuuiper,$webiparr)){
            $_SESSION = array();
            session_destroy();
            header("Location:/");
            exit();
          }
        }


      }

    }

  }
}
//获取网站自定义信息
$webcustomsql = "select * from ppz_diy where diyid = 1";
$webcustomretval=mysqli_query($conn,$webcustomsql);
if(mysqli_num_rows($webcustomretval) !== 1){
  $indexflex=1;
  $indeximage="";
  $indexcarousel=0;
}else{
    $webcustomquery = $conn->query($webcustomsql);
    while($webcustom = $webcustomquery->fetch_array()){
      $indexflex=$webcustom['diyindex'];//首页版面,1：默认版面，2：小轮播图，3：大轮播图
      $indeximage=$webcustom['image'];//自定义轮播图JSON,轮播图模式为6时有效
      $indexcarousel=$webcustom['carousel'];  //轮播图模式：1为加“热门”内容，2为加"精华"内容，3为加"置顶"内容，4为自动最新内容，5为自动最高阅览量内容，6为自定义内容
    }
}
$set_off=0;
//话题配置
$subsetsql = "select * from ppz_subset where set_id = 1";
$subsetretval=mysqli_query($conn,$subsetsql);
if(mysqli_num_rows($subsetretval) !== 1){
        $subsettext='<li class="menu-li"><a href="/anct.php"><i class="fa fa-question-circle"></i>平台公告</a></li>';
        $set_title="话题";
        $set_tag="话题";
}else{
        while($subset = mysqli_fetch_array($subsetretval)){
            $set_title = $subset['set_title'];// 话题标题
            $set_off = $subset['set_off'];// 话题开关,0：关闭，1：开启
            $set_tag = $subset['set_tag'];//话题子标签名称
            $set_mun = $subset['set_mun'];//每个会员一天最多可发表的数量
        }

        if(empty($set_title)){
            $set_title="话题";
        }else{
            $set_title=$set_title;
        }

        if(empty($set_tag)){
            $set_tag="话题";
        }else{
            $set_tag=$set_tag;
        }

        if(empty($set_mun)||$set_mun<0){
          $set_mun=0;
        }else{
          $set_mun=$set_mun;
        }

        
}
?>