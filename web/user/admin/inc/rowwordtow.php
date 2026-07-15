<?php
if ($admin==1 && $typeuser==12 && ($allvip==4||$allvip==3||$allvip==2)  && !is_null($ppzusername) && $typeyes==4){
echo '<div class="user-h1"><span><i class="fa fa-plus-circle"></i>发布公告</span><a href="?type=12"><i class="fa fa-angle-double-left" aria-hidden="true"></i>返回类型</a></div>';
echo '
<div class="newword">
    <form method="post" id="wordform">
        <div class="newword-title"><span class="letter1em">标题：</span><input type="text" name="rowhead" id="rowhead" /></div>
        <div class="newword-title"><span class="letter1em">封面：</span><input type="text" name="rowimg" id="rowimg" /><a id="newworduploadimg">上传</a></div>
        <textarea name="rowtext" id="rowtext"></textarea>
        <div class="newword-title"><span class="letter1em">置顶：</span>
        <input type="radio" id="nawrowtop" name="rowtop" value="1" checked /><label for="nawrowtop">不置顶</label>
        <input type="radio" id="rowtop" name="rowtop" value="2" /><label for="rowtop">置顶</label>
        </div>
        <div class="newword-title3"><button id="newwordsubmit">提交</button></div>
    </form>
    <script src="/style/js/selecttow.js" type="text/javascript"></script>
    <script src="/style/js/rowformtow.js" type="text/javascript"></script>
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
}else{
    echo "请勿胡搞！";
}
?>