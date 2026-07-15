<?php
if ($admin==1 && $typeuser==12 && ($allvip==4||$allvip==3||$allvip==2)  && !is_null($ppzusername) && $typeyes==3){
echo '<script src="/style/videojs/hls.js"></script>';
echo '<div class="user-h1"><span><i class="fa fa-plus-circle"></i>发布视频</span><a href="?type=12"><i class="fa fa-angle-double-left" aria-hidden="true"></i>返回类型</a></div>';
echo '
<div class="newword">
    <form method="post" id="wordform">
        <div class="newword-title"><span class="letter1em">标题：</span><input type="text" name="rowhead" id="rowhead" /></div>
            <div class="newword-title">
                <div class="newword-title2"><span class="letter1em">分类：</span>
                <select name="rowtype" id="rowtype">';
                    //链接列表sql
                    $linksqlyb="select * from ppz_link order by linkid asc";
                    $linkretvalyb=mysqli_query($conn,$linksqlyb);
                    if(mysqli_num_rows($linkretvalyb) > 0){
                        while($linkrowyb = $linkretvalyb->fetch_array()){
                            $newlinkid=$linkrowyb['linkid'];//列表id
                            $newlinkname=$linkrowyb['linkname'];//列表名称
                            echo '<option value="'.$newlinkid.'">'.$newlinkname.'</option>';
                        }
                    }else{
                        echo '<option value="">请先设置列表</option>';
                    }
echo '</select>
                <select name="rowif" id="rowif"></select>
                </div>
                <div class="newword-title2">阅览权限：
                        <select name="rowvip" id="rowvip">
                        <option value="1">所有人可见</option>
                        <option value="2">登录可见</option>
                        <option value="3">VIP可见</option>
                    </select>
                </div>
            </div>
        <div class="newword-title"><span class="letter1em">封面：</span><input type="text" name="rowimg" id="rowimg" /><a id="newworduploadimg">上传</a><i>* 若不上传封面，则自动以内容里的第一张图片作为封面。</i></div>
        <div class="newword-title">
        <div class="newword-title2">版权方：<input type="text" name="rowcp" id="rowcp" /></div>
        <div class="newword-title2">版权链接：<input type="text" name="rowcpurl" id="rowcpurl" /></div>
        </div>
        <div class="newword-title"><span class="letter1em">标签：</span><input type="text" name="rowtag" id="rowtag" /><i>* 多个标签可用逗号隔开。</i></div>
        <div class="newword-title"><span class="letter1em">下载：</span><div class="rowdowdiv"><input type="text" name="rowdow" id="rowdow" maxlength="9" value="0"/><span class="spanfloat">积分</span></div>
        <div class="newword-title5">下载权限：
                        <select name="rowdwif" id="rowdwif">
                        <option value="1">注册会员及以上</option>
                        <option value="2">VIP会员及以上</option>
                        <option value="3">管理员及以上</option>
                    </select>
                </div>
        <a class="nocopy" id="rowdowdiva"><i class="fa fa-download"></i>配置下载信息</a><i>* 添加第三方文件下载地址等信息。</i></div>
        <dialog id="rowdowdialog">
            <div id="rowdowclose"><i class="fa fa-times"></i></div>
            <b>配置下载信息</b>
            <div class="rowdowinput" id="rowdowstyle"></div>
            <div class="rowdowinput">网盘名称：<input type="text" name="rowdowname" id="rowdowname" placeholder="如：百度网盘"/><a>必填</a></div>
            <div class="rowdowinput">分辨率：<input type="text" name="rowdowpx" id="rowdowpx" placeholder="如：1024x1080px"/></div>
            <div class="rowdowinput">下载地址：<input type="text" name="rowdowurl" id="rowdowurl" placeholder="https://"/><a>必填</a></div>
            <div class="rowdowinput">提取码：<input type="text" name="rowdowpas" id="rowdowpas" /></div>
            <div class="rowdowinput">文件数量：<input type="text" name="rowdowmun" id="rowdowmun" placeholder="如：30P+2V"/><a>必填</a></div>
            <div class="rowdowinput">文件大小：<input type="text" name="rowdowsize" id="rowdowsize" placeholder="如：128MB"/><a>必填</a></div>
            <div class="rowdowinput">解压密码：<input type="text" name="rowdowzip" id="rowdowzip" /></div>
            <div id="rowdowcloseyes">确认</div>
            <span id="rowdowcloseerr"></span>
        </dialog>
        <script src="/style/js/rowdow.js" type="text/javascript"></script>
        <div class="newword-title"><span class="letter1em">视频：</span><input type="text" name="rowimgarr" id="rowimgarr" /><a id="newarrimg"><i class="fa fa-plus"></i>添加视频</a><i>* 填写url地址添加视频，支持mp4、m3u8、3gp、webm、ogg视频。</i></div>
        <div class="newimageall" id="xcdesss"><div id="percentage"></div><div class="divvvvimg"><span class="letter1em">预览：<i>只可填写地址添加，不可上传；支持mp4、ogg、webm、m3u8、mp3、wav格式文件。</i></span></div><div id="imgeye"></div></div>
        <textarea name="rowtext" id="rowtext"></textarea>
        <div class="newword-title"><span class="letter1em">置顶：</span>
        <input type="radio" id="nawrowtop" name="rowtop" value="1" checked /><label for="nawrowtop">无</label>
        <input type="radio" id="rowtop" name="rowtop" value="2" /><label for="rowtop">置顶</label>
        <input type="radio" id="rowtop2" name="rowtop" value="3" /><label for="rowtop2">热门</label>
        <input type="radio" id="rowtop3" name="rowtop" value="4" /><label for="rowtop3">精华</label>
            <div class="imgtexttop"><span>摘要显示位置：</span>
            <input type="radio" id="imgtexttop" name="imgtexttop" value="2" /><label for="imgtexttop">内容上方</label>
            <input type="radio" id="imgtextbottom" name="imgtexttop" value="1" checked /><label for="imgtextbottom">内容下方</label></div>
        </div>
        
        <div class="newword-title3"><button id="newwordsubmit">提交</button></div>
    </form>
    <script src="/style/js/arrvideo.js" type="text/javascript"></script>
</div>';
echo '
<div class="upload-overlay" id="uploadOverlay">  
<div class="upload-box">  
<div class="custom-file-upload" id="dragArea">  
<input type="file" id="fileUpload" style="display: none;">  
<button id="fileimgbox" type="button"><i class="fa fa-plus"></i></button>  
</div>
<div class="file-info"><span id="fileInfo" style="display: flex;">请先点击上方选择要上传的文件</span></div>  
<button id="closeUploadOverlay"><i class="fa fa-times"></i></button>
<button id="openUploadOverlay">上传</button>
<div id="fileerr" style="display: none;"></div>
</div>  
</div>
';
if(mysqli_num_rows($linkretvalyb) >= 0){
    echo '<script src="/style/js/select.js" type="text/javascript"></script>';
}
}else{
    echo "请勿胡搞！";
}
?>