<?php
if ($admin==1 && $typeuser==1 && $allvip==4  && !empty($ppzusername)){
//获取web设置信息
$websz_sql = "SELECT * FROM ppz_web WHERE webid=1";
$websz_resulta = mysqli_query($conn,$websz_sql); 
$webszsize = mysqli_num_rows($websz_resulta);
if ($webszsize > 0){
  while($aweb=mysqli_fetch_assoc($websz_resulta)){
    $webtext=$aweb['webtext'];//网站名称(标题)
    $webpass=$aweb['webpass'];//网站关键词
    $webvar=$aweb['webvar'];//网站简介
    $webfooter=$aweb['webfooter'];//网站底部版权说明
    $webqqurl=$aweb['webqqurl'];//qq链接
    $webwburl=$aweb['webwburl'];//微博链接
    $webqqqurl=$aweb['webqqqurl'];//qq群链接
    $webemail=$aweb['webemail'];//电子邮箱地址
    $webby=$aweb['webby'];//网站标语(副标题)
    $webip=$aweb['webip'];//IP黑名单
    $weblogo=$aweb['weblogo'];//顶部导航栏logo
    $toplogourl=$aweb['toplogourl'];//顶部导航栏logo链接
    $webbutlogo=$aweb['webbutlogo'];//底部logo
    $webnewnet=$aweb['webnewnet'];//底部新媒体账号的二维码图片地址
    $webjifen=$aweb['webjifen'];//签到时的任意积分范围，可是数字获
  }
}
if(empty($webjifen)){
  $webjifen=0;
}
//获取diy自定义配置信息
$diy_sql = "SELECT * FROM ppz_diy WHERE diyid=1";
$diy_resulta = mysqli_query($conn,$diy_sql);
$diysize = mysqli_num_rows($diy_resulta);
if ($diysize > 0){
  while($diy=mysqli_fetch_assoc($diy_resulta)){
    $diyindex=$diy['diyindex'];//首页版面，1为普通，2为小轮播图，3为大轮播图
    $diyday=$diy['diyday'];//自定义白天模式，1为样式1，2为样式2，3为自定义样式
    $diynight=$diy['diynight'];//自定义夜间模式，1为样式1，2为样式2，3为自定义样式
    $day=$diy['day'];//自定义白天模式样式的自定义css样式，自定义白天模式设置为3时生效
    $night=$diy['night'];//自定义夜间模式样式的自定义css样式，自定义夜间模式设置为3时生效
    $Image=$diy['image'];//自定义轮播图，JSON格式，包含图片及超链接；轮播图模式设置为6时生效
    $carousel=$diy['carousel'];//轮播图模式，1为加“热门”内容，2为加"精华"内容，3为加"置顶"内容，4为自动最新内容，5为自动最高阅览量内容，6为自定义内容
  }
  $Imagejson=json_decode($Image,true);//解析JSON
  $newjsonstyle = json_encode($Imagejson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);;//格式化JSON，不转义中文

  if ($diyindex==3){
    $index1='';
    $index2='';
    $index3='checked';
  }else if($diyindex==2){
    $index1='';
    $index2='checked';
    $index3='';
  }else{
    $index1='checked';
    $index2='';
    $index3='';
  }
  if ($diyday==3){
    $day1='';
    $day2='';
    $day3='checked';
  }else if($diyday==2){
    $day1='';
    $day2='checked';
    $day3='';
  }else{
    $day1='checked';
    $day2='';
    $day3='';
  }
  if ($diynight==3){
    $night1='';
    $night2='';
    $night3='checked';
  }else if($diynight==2){
    $night1='';
    $night2='checked';
    $night3='';
  }else{
    $night1='checked';
    $night2='';
    $night3='';
  }
  if($carousel==1){
    $carouseltxt1='selected';
    $carouseltxt2='';
    $carouseltxt3='';
    $carouseltxt4='';
    $carouseltxt5='';
    $carouseltxt6='';
  }else if($carousel==2){
    $carouseltxt1='';
    $carouseltxt2='selected';
    $carouseltxt3='';
    $carouseltxt4='';
    $carouseltxt5='';
    $carouseltxt6='';
  }else if($carousel==3){
    $carouseltxt1='';
    $carouseltxt2='';
    $carouseltxt3='selected';
    $carouseltxt4='';
    $carouseltxt5='';
    $carouseltxt6='';
  }else if($carousel==4){
    $carouseltxt1='';
    $carouseltxt2='';
    $carouseltxt3='';
    $carouseltxt4='selected';
    $carouseltxt5='';
    $carouseltxt6='';
  }else if($carousel==5){
    $carouseltxt1='';
    $carouseltxt2='';
    $carouseltxt3='';
    $carouseltxt4='';
    $carouseltxt5='selected';
    $carouseltxt6='';
  }else if($carousel==6){
    $carouseltxt1='';
    $carouseltxt2='';
    $carouseltxt3='';
    $carouseltxt4='';
    $carouseltxt5='';
    $carouseltxt6='selected';
  }else{
    $carouseltxt1='selected';
    $carouseltxt2='';
    $carouseltxt3='';
    $carouseltxt4='';
    $carouseltxt5='';
    $carouseltxt6='';
  }
}
//获取文件上传策略设置
$upload_sql = "SELECT * FROM ppz_upfile WHERE id=1";
$upload_result = mysqli_query($conn,$upload_sql);
$uploadsize = mysqli_num_rows($upload_result);
if ($uploadsize > 0){
  while($upload=mysqli_fetch_assoc($upload_result)){
    $upload_size=$upload['upsize'];//允许上传文件大小，单位KB
    $upload_mime=$upload['upmime'];//上传文件MIME类型
    $upload_if=$upload['upif'];//投稿开关，0关，1开
    $upload_if_img=$upload['upifimg'];//是否允许会员投稿时上传附件,1为允许，0为不允许
    $upload_fcsize=$upload['upfcsize'];//投稿分成
    $upload_vipsize=$upload['upvipsize'];//vip所享折扣
  }
  if(empty($upload_fcsize)){
    $upload_fcsize=0;
  }
  if(empty($upload_vipsize)){
    $upload_vipsize=0;
  }
  if($upload_if==1){
    $upload_if_txt='checked';
    $upload_if_txtno='';
  }else{
    $upload_if_txt='';
    $upload_if_txtno='checked';
  }
  if($upload_if_img==1){
    $upload_if_txt_img='checked';
    $upload_if_txtno_img='';
  }else{
    $upload_if_txtno_img='checked';
    $upload_if_txt_img='';
  }
}
//获取注册配置
$regif_sql = "SELECT * FROM ppz_regif WHERE id=1";
$regif_result = mysqli_query($conn,$regif_sql);
$regifsize = mysqli_num_rows($regif_result);
if($regifsize==1){
  while($regif_row = mysqli_fetch_assoc($regif_result)){
    $regif=$regif_row['regif'];//注册状态：1开启，2关闭
    $regoff=$regif_row['regoff'];//注册方式：1开放注册，2邀请码注册
    $regtext=$regif_row['regtext'];//注册协议
  }
  if($regif==1){
    $regtxtifyes="checked";
    $regtxtifno="";
  }else{
    $regtxtifyes="";
    $regtxtifno="checked";
  }

  if($regoff==1){
    $regofftxtyes="checked";
    $regofftxtno="";
  }else{
    $regofftxtyes="";
    $regofftxtno="checked";
  }

}
//获取违禁词配置
$fuck_sql="select * from ppz_fuck where id=1";
$fuck_result=mysqli_query($conn,$fuck_sql);
$fucksize = mysqli_num_rows($fuck_result);
if($fucksize==1){
  while($fuckrow = $fuck_result->fetch_array()){
    $fucktext=$fuckrow['fuck'];
  }
}
if(!isset($toplogourl)||empty($toplogourl)){
  $toplogourl='/';
}
echo '
<div class="tabs">  
  <div class="tab active" id="tab1"><div class="tab-title"><i class="fa fa-cog"></i>基本设置</div></div>  
  <div class="tab" id="tab2"><div class="tab-title"><i class="fa fa-info-circle"></i>安全设置</div></div>  
  <div class="tab" id="tab3"><div class="tab-title"><i class="fa fa-magic"></i>主题配色</div></div>
  <div class="tab" id="tab4"><div class="tab-title"><i class="fa fa-upload"></i>投稿策略</div></div>
  <div class="tab" id="tab5"><div class="tab-title"><i class="fa fa-user-plus"></i>注册设置</div></div>
  <div class="tab" id="tab6"><div class="tab-title"><i class="fa fa-exclamation-triangle"></i>敏感词</div></div>
  <div class="tab" id="tab7"><div class="tab-title"><i class="fa fa-suitcase"></i>工单分类</div></div>
</div>  
  
<div class="tab-content">  
  <div class="content active" id="content1">
  <form class="content-form" id="webform" method="post">
  <div class="content-title toplogo">导航栏LOGO：<div class="content-div-img"><input  type="text" id="toplogoimgerr" name="toplogo" value="'.$weblogo.'" /><input class="content-upimg" id="uplogoimg" type="button" value="上传" /></div><span><b>*</b>建议尺寸按：180*34px比例<img id="logoimgnew" src="'.$weblogo.'" /></span></div>
  <div class="content-title">LOGO链接：<input type="text" name="toplogourl" value="'.$toplogourl.'" /><span>点击导航栏LOGO后跳转的链接，默认为网站首页</span></div>
  <div class="content-title toplogo">底部LOGO：<div class="content-div-img"><input id="butlogoimgerr" type="text" name="butlogo" value="'.$webbutlogo.'" /><input class="content-upimg" id="butlogoimg" type="button" value="上传" /></div><span><b>*</b>建议尺寸按：280*35px比例<img id="butlogoimgnew" src="'.$webbutlogo.'" /></span></div>
  <div class="content-title toplogo">新媒体账号：<div class="content-div-img"><input id="upnewnetimgerr" placeholder="https://" type="text" name="newintel" value="'.$webnewnet.'" /><input class="content-upimg" id="upnewnetimg" type="button" value="上传" /></div><span>底部新媒体账号的二维码图片地址</span></div>
  <div class="content-title">网站名称：<input type="text" name="webtext" value="'.$webtext.'" /><span><b>*</b>主标题，如“破瓶子社区”,避免过长！</span></div>
  <div class="content-title">网站标语：<input type="text" name="webtxt" value="'.$webby.'" /><span><b>*</b>副标题，会出现在主标题后面</span></div>
  <div class="content-title">网站关键词：<textarea name="webpas">'.$webpass.'</textarea><span><b>*</b>用于HTML标签的关键词，用于搜索引擎收录。</span></div>
  <div class="content-title">网站描述：<textarea name="webvar">'.$webvar.'</textarea><span><b>*</b>底部描述，也用于HTML标签，便于搜索引擎收录。</span></div>
  <div class="content-title">版权信息：<textarea name="webbut">'.$webfooter.'</textarea><span><b>*</b>底部版权描述、备案号等信息。</span></div>
  <div class="content-title">签到奖励：<input placeholder="0" type="text" name="jifen" value="'.$webjifen.'" /><span>用户签到时获得的随机积分范围，如：1-10（1到10的任意值）</span></div>
  <div class="content-title">QQ客服：<input placeholder="https://" type="text" name="qq" value="'.$webqqurl.'" /><span>添加QQ客服的URL地址。</span></div>
  <div class="content-title">QQ群聊：<input placeholder="https://" type="text" name="zq" value="'.$webqqqurl.'" /><span>添加QQ群聊的URL地址。</span></div>
  <div class="content-title">新浪微博：<input placeholder="https://" type="text" name="wb" value="'.$webwburl.'" /><span>新浪微博URL地址。</span></div>
  <div class="content-title">电子邮箱：<input type="email" placeholder="XXXXX@email.com" name="email" value="'.$webemail.'" /><span>填写完整的电子邮箱账号</span></div>
  <div class="content-btn-div"><button class="content-btn" id="webbtn" type="submit">确认</button></div>
  </form>
  <div class="upload-overlay" id="uploadOverlay">  
  <div class="upload-box">  
  <div class="custom-file-upload" id="dragArea">  
  <input type="file" id="fileUpload" style="display: none;">  
  <button id="fileimgbox" type="button">选择文件</button>  
</div>
      <div class="file-info"><span id="fileInfo">请先点击上方选择要上传的文件</span></div>  
      <button id="closeUploadOverlay"><i class="fa fa-times"></i></button>
      <button id="openUploadOverlay">上传</button>
      <div id="fileerr"></div>
  </div>  
</div>
<script src="/style/js/uptoplogo.js" type="text/javascript"></script>
<script src="/style/js/webset.js" type="text/javascript"></script>
  </div>  
  <div class="content" id="content2">
  <form class="content-form" id="safetyform" method="post">
  <div class="content-title safety"><p>IP黑名单：<span>ip黑名单，多个ip用"|"隔开</span></p><textarea name="safetyip">'.$webip.'</textarea></div>
  <div class="content-btn-div"><button class="content-btn" id="safetybtn" type="submit">确认</button></div>
  </form>
  <script src="/style/js/ipset.js" type="text/javascript"></script>
  </div>  
  <div class="content" id="content3">
  <div class="content-form" id="diyform" method="post">
  <div class="content-title safety"><p>首页版面：<span>网站首页的版面样式</span></p>
    <div class="radio-indexdiy">

    <div class="radio-indexdiy-div">
      <input id="indexdiyradio1" type="radio" name="indexdiy" value="1" '.$index1.'>
      <label for="indexdiyradio1" class="radio-label">  
      <div class="diywh" style="background-image:url(/images/web/006.jpg);">最新发布</div> 
      <span class="radio-custom"></span>  
      </label> 
    </div>

    <div class="radio-indexdiy-div">
      <input id="indexdiyradio2" type="radio" name="indexdiy" value="2" '.$index2.'/>
      <label for="indexdiyradio2" class="radio-label">  
      <div class="diywh" style="background-image:url(/images/web/007.jpg);">轮播小图</div> 
      <span class="radio-custom"><div id="carousel"><a id="carousela"><i class="fa fa-gear"></i>设置轮播图</a></diV></span>  
      </label> 
    </div>

    <div class="radio-indexdiy-div">
      <input id="indexdiyradio3" type="radio" name="indexdiy" value="3" '.$index3.'/>
      <label for="indexdiyradio3" class="radio-label">  
      <div class="diywh" style="background-image:url(/images/web/008.jpg);">轮播大图</div> 
      <span class="radio-custom"><div id="carousel"><a id="bigcarousela"><i class="fa fa-gear"></i>设置轮播图</a></diV></span>  
      </label> 
    </div>
    
    </div>
  </div>
  <div class="content-title safety"><p>白天模式：<span>前端可选择的白天模式</span></p>
          <div class="radio-indexdiy">

          <div class="radio-indexdiy-div">
            <input id="daydiyradio1" type="radio" name="daydiy" value="1" '.$day1.'>
            <label for="daydiyradio1" class="radio-label">  
            <div class="diywh" style="background-image:url(/images/web/003.jpg);">蓝白色系</div> 
            <span class="radio-custom"></span>  
            </label> 
          </div>

          <div class="radio-indexdiy-div">
            <input id="daydiyradio2" type="radio" name="daydiy" value="2" '.$day2.'/>
            <label for="daydiyradio2" class="radio-label">  
            <div class="diywh" style="background-image:url(/images/web/009.jpg);">绿白色系</div> 
            <span class="radio-custom"></span>  
            </label> 
          </div>

          <div class="radio-indexdiy-div">
            <input id="daydiyradio3" type="radio" name="daydiy" value="3" '.$day3.'/>
            <label for="daydiyradio3" class="radio-label">  
            <div class="diywh" style="background-image:url(/images/web/004.jpg);">自定义</div> 
            <span class="radio-custom"><div id="carousel"><a id="daydiya"><i class="fa fa-terminal"></i>CSS编辑</a></diV></span>  
            </label> 
          </div>
          
          </div>
  </div>
  <div class="content-title safety"><p>暗夜模式：<span>前端可选择的暗夜模式</span></p>
          <div class="radio-indexdiy">

          <div class="radio-indexdiy-div">
            <input id="nightradio1" type="radio" name="nightdiy" value="1" '.$night1.'>
            <label for="nightradio1" class="radio-label">  
            <div class="diywh" style="background-image:url(/images/web/001.jpg);">黑灰色系</div> 
            <span class="radio-custom"></span>  
            </label> 
          </div>

          <div class="radio-indexdiy-div">
            <input id="nightradio2" type="radio" name="nightdiy" value="2" '.$night2.'/>
            <label for="nightradio2" class="radio-label">  
            <div class="diywh" style="background-image:url(/images/web/002.jpg);">暗蓝色系</div> 
            <span class="radio-custom"></span>  
            </label> 
          </div>

          <div class="radio-indexdiy-div">
            <input id="nightradio3" type="radio" name="nightdiy" value="3" '.$night3.'/>
            <label for="nightradio3" class="radio-label">  
            <div class="diywh" style="background-image:url(/images/web/005.jpg);">自定义</div> 
            <span class="radio-custom"><div id="carousel"><a id="nighta"><i class="fa fa-terminal"></i>CSS编辑</a></diV></span>  
            </label> 
          </div>
          
          </div>
  </div>
  <div class="content-btn-div"><button class="content-btn" id="diysafetybtn">确认</button></div>
  </div>

  <div id="indexcarousel">
      <div class="indexcarousel-div">
      <button id="closeindexcarousel"><i class="fa fa-times"></i></button>
      <div class="indexcarousel-form">
        <p>轮播图设置</p>
        <div>模式：
          <select id="indexcarouselmode">
            <option value="1" '.$carouseltxt1.'>热门文章</option>
            <option value="2" '.$carouseltxt2.'>精华文章</option>
            <option value="3" '.$carouseltxt3.'>置顶文章</option>
            <option value="4" '.$carouseltxt4.'>最新发布</option>
            <option value="5" '.$carouseltxt5.'>最多阅览</option>
            <option value="6" '.$carouseltxt6.'>自定JSON</option>
          </select>
        </div>
      </div>
      <div id="indexcarousel-div">
        <div class="upcarousel-form" >
           <div><span>自定义JSON（模式为：“自定JSON”时生效）：<i>示例格式：<a id="jsoncss">点击插入示例</a></i></span>
           <textarea id="indexcarouseljson">'.$newjsonstyle.'</textarea>
           </div>
        </div>
        <div class="upcarouseldiv" id="upcarouseldiv"><span id="upcarouselspan"></span><button id="upcarousel" type="button">保存设置</button></div>
      </div>
      </div>
  </div>

  <div id="diydaycss">
      <div class="indexcarousel-div">
      <button id="closediydaycss"><i class="fa fa-times"></i></button>
      <div class="indexcarousel-form"><p>白天CSS设置</p></div>
      <div id="diydaycss-div">
        <div class="upcarousel-form" >
           <div><span>自定义白天模式的CSS样式：<i>示例格式：<a id="daycss">点击插入示例</a></i></span>
           <textarea id="diydaycsscss">'.$day.'</textarea>
           </div>
        </div>
        <div class="upcarouseldiv" id="updiydaycssdiv"><span id="updiydaycssspan"></span><button id="updiydaycss" type="button">保存设置</button></div>
      </div>
      </div>
  </div>

  <div id="diynightcss">
  <div class="indexcarousel-div">
  <button id="closediynightcss"><i class="fa fa-times"></i></button>
  <div class="indexcarousel-form"><p>暗夜CSS设置</p></div>
  <div id="diynightcss-div">
    <div class="upcarousel-form" >
       <div><span>自定义暗夜模式的CSS样式：<i>示例格式：<a id="nightcss">点击插入示例</a></i></span>
       <textarea id="diynightcsscss">'.$night.'</textarea>
       </div>
    </div>
    <div class="upcarouseldiv" id="updiynightcssdiv"><span id="updiynightcssspan"></span><button id="updiynightcss" type="button">保存设置</button></div>
  </div>
  </div>
</div>
  <script src="/style/js/jsoncss.js" type="text/javascript"></script>
  </div>

  <div class="content" id="content4">
    <div class="content-form">
      <div class="upif">
        <div class="upfileradio">  
          投稿开关：  
          <div class="upfilelabel nocopy"><input type="radio" name="notifup" id="notifup-allow" value="1" class="custom-radio" '.$upload_if_txt.'><label  for="notifup-allow">允许</label></div>
          <div class="upfilelabel nocopy"><input type="radio" name="notifup" id="notifup-deny" value="0" class="custom-radio" '.$upload_if_txtno.'><label for="notifup-deny">否</label></div>
          <div class="upif_img">
            上传开关：  
            <div class="upfilelabel nocopy"><input type="radio" name="notifup_img" id="notifup-allow_img" value="1" class="custom-radio" '.$upload_if_txt_img.'><label  for="notifup-allow_img">允许</label></div>
            <div class="upfilelabel nocopy"><input type="radio" name="notifup_img" id="notifup-deny_img" value="0" class="custom-radio" '.$upload_if_txtno_img.'><label for="notifup-deny_img">否</label></div>
          </div>
        </div>
      </div>

      <div class="upif"><span>投稿补充说明：<b>* 补充说明，出现在会员投稿页须知下方；</b></span><textarea id="upyesfiletype" type="text">'.$upload_mime.'</textarea></div>
      <div class="upif"><span>限制附件大小(KB)：<b>如：1024 (1024KB=1MB)</b></span><input placeholder="" type="number" min="0" max="999999999" id="upyesfilesize" value="'.$upload_size.'" /></div>
      <div class="upif"><span>投稿分成(%)：<b>如：70%，投稿人获得70%的积分分成，网站获得剩余的30%</b></span><input placeholder="" type="number" min="0" max="100" id="upyesfcsize" value="'.$upload_fcsize.'" /></div>
      <div class="upif"><span>VIP折扣(%)：<b>如：10%，10%为折扣，VIP会员只需支付剩余90%即可；100%则表示VIP可免费下载付费内容</b></span><input placeholder="" type="number" min="0" max="100" id="upyesvipsize" value="'.$upload_vipsize.'" /></div>
      <div class="content-btn-div"><button class="content-btn" id="upsetbutton">确认</button></div>
    </div>
    <script src="/style/js/uploadif.js" type="text/javascript"></script>
  </div>

  <div class="content" id="content5">
    <div class="content-form">
      <div class="registerif">
        <div class="upfileradio">  
        注册状态：  
        <div class="upfilelabel nocopy"><input type="radio" name="registerif" id="register-allow" value="1" class="custom-radio" '.$regtxtifyes.'><label  for="register-allow">开启</label></div>
        <div class="upfilelabel nocopy"><input type="radio" name="registerif" id="register-deny" value="2" class="custom-radio" '.$regtxtifno.'><label for="register-deny">关闭</label></div>
        </div>
      </div>
      <div class="registerif">
      <div class="upfileradio">  
      注册方式：  
      <div class="upfilelabel nocopy"><input type="radio" name="registeroff" id="registeroff-allow" value="1" class="custom-radio" '.$regofftxtyes.'><label  for="registeroff-allow">开放</label></div>
      <div class="upfilelabel nocopy"><input type="radio" name="registeroff" id="registeroff-deny" value="2" class="custom-radio" '.$regofftxtno.'><label for="registeroff-deny">邀请码</label></div>
      </div>
      </div>
      <div class="registerif grid">
      <span>注册协议：<b>* 用户注册页面所显示的协议内容，不支持HTML标签。</b></span><textarea id="registertextarea" placeholder="请输入注册协议">'.$regtext.'</textarea>
      </div>
      <div class="content-btn-div"><button class="content-btn" id="registerifbutton">确认</button></div>
    </div>
    <script src="/style/js/regset.js" type="text/javascript"></script>
  </div>
  
  <div class="content" id="content6">
    <div class="content-form">
    <div class="content-title safety"><p>设置违禁词：<span>多个违禁词用"|"隔开，如：违禁词1|违禁词2|违禁词3</span></p><textarea id="fucksettext">'.$fucktext.'</textarea></div>
    <div class="content-btn-div"><button class="content-btn" id="fucksetbtn" type="submit">确认</button></div>
    </div>
    <script src="/style/js/fuckset.js" type="text/javascript"></script>
  </div>
</div>
  <div class="content" id="content7">
    <div class="content-form">
      <div class="servicediv"><input placeholder="请输入分类名称" type="text" id="servicefl" /><button class="content-btn" id="servicebtn" type="submit">+添加分类</button></div>
      <ul id="newserviceul" class="serviceul">';
      //获取分类列表
      $sericesql = "select * from ppz_workfl";
      $sericeretval=mysqli_query($conn,$sericesql);
      if(mysqli_num_rows($sericeretval) < 1){
        echo '<li><div class="servicenull">暂无分类</div></li>';
      }else{
        while($sericerow = $sericeretval->fetch_array()){
          echo '<li id="serliid'.$sericerow["id"].'"><div class="serviceli"><span id="newsertxt'.$sericerow["id"].'">'.$sericerow["wkname"].'</span><div class="serfleditdiv"><a title="删除" class="serfldel" data-fid="'.$sericerow["id"].'"><i class="fa fa-trash-o" aria-hidden="true"></i></a><a title="编辑" class="serfledit" data-txt="'.$sericerow["wkname"].'" data-fid="'.$sericerow["id"].'"><i class="fa fa-edit" aria-hidden="true"></i></a></div></div></li>';
        }
      }
      echo '
      </ul>
      <dialog id="navfldialog"><a id="navfldialogclose"><i class="fa fa-times" aria-hidden="true"></i></a><b>修改工单分类</b><input type="text" id="navfldialoginput" placeholder="请输入分类名称"><button id="navfldialogbut" data-yid="">确定</button><span id="navfldialogerr"></span></dialog>
      <script src="/style/js/editservice.js" type="text/javascript"></script>
  </div>
</div>

<script>var filesizekb = 1024;var fileTypex = ["image/jpeg", "image/png", "image/gif"];</script>
<script src="/style/js/tab.js" type="text/javascript"></script>
';
}else{
echo "请勿胡搞！";
}
?>