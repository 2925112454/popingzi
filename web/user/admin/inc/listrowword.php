<?php
if ($adminyeslist===200 && ($allvip==4||$allvip==3||$allvip==2)  && !is_null($ppzusername) && $rlit_if==1){
    echo '
    <div class="newword">
        <form method="post" id="wordform">
            <div class="newword-title"><span class="letter1em">标题：</span><input type="text" name="rowhead" id="rowhead"  value="'.$rlit_tite.'" /></div>
                <div class="newword-title">
                    <div class="newword-title2"><span class="letter1em">分类：</span>
                    <select name="rowtype" id="rowtype">';

                            //链接列表sql
                            $linksqlyb="select * from ppz_link";
                            $linkretvalyb=mysqli_query($conn,$linksqlyb);
                            if(mysqli_num_rows($linkretvalyb) > 0){
                                while($linkrowyb = $linkretvalyb->fetch_array()){
                                    $newlinkid=$linkrowyb['linkid'];//列表id
                                    $newlinkname=$linkrowyb['linkname'];//列表名称
                                    if ($listfllinkid==$newlinkid && !is_null($listfllinkid)&& $listfllinkid!=''&&$listfllinkid>0){
                                        echo '<option value="'.$newlinkid.'" selected>'.$newlinkname.'</option>';
                                    }else{
                                        echo '<option value="'.$newlinkid.'">'.$newlinkname.'</option>';
                                    }                                    
                                }
                            }else{
                                echo '<option value="">请先设置列表</option>';
                            }

                        
    echo '</select>
                    <select name="rowif" id="rowif"></select>
                    </div>
                    <div class="newword-title2">阅览权限：
                            <select name="rowvip" id="rowvip">
                            <option value="1" '.$rlit_viptxt1.'>所有人可见</option>
                            <option value="2" '.$rlit_viptxt2.'>登录可见</option>
                            <option value="3" '.$rlit_viptxt3.'>VIP可见</option>
                        </select>
                    </div>
                </div>
            <div class="newword-title"><span class="letter1em">封面：</span><input type="text" name="rowimg" id="rowimg" value="'.$rlit_img.'" /><a id="newworduploadimg">上传</a><i>* 若不上传封面，则自动以内容里的第一张图片作为封面。</i></div>
            <div class="newword-title">
            <div class="newword-title2">版权方：<input type="text" name="rowcp" id="rowcp" value="'.$rlit_cp.'" /></div>
            <div class="newword-title2">版权链接：<input type="text" name="rowcpurl" id="rowcpurl" value="'.$rlit_cpurl.'" /></div>
            </div>
            <div class="newword-title"><span class="letter1em">标签：</span><input type="text" name="rowtag" id="rowtag" value="'.$rlit_tag.'" /><i>* 多个标签可用逗号隔开。</i></div>
            <div class="newword-title"><span class="letter1em">下载：</span><div class="rowdowdiv"><input type="text" name="rowdow" id="rowdow" maxlength="9" value="'.$rlit_dwg.'"/><span class="spanfloat">积分</span></div>
            <div class="newword-title5">下载权限：
                        <select name="rowdwif" id="rowdwif">
                        <option value="1" '.$dowif1.'>注册会员及以上</option>
                        <option value="2" '.$dowif2.'>VIP会员及以上</option>
                        <option value="3" '.$dowif3.'>管理员及以上</option>
                    </select>
                </div>
            <a class="nocopy" id="rowdowdiva"><i class="fa fa-download"></i>配置下载信息</a><i>* 添加第三方文件下载地址等信息。</i></div>
            <dialog id="rowdowdialog">
                <div id="rowdowclose"><i class="fa fa-times"></i></div>
                <b>配置下载信息</b>
                <div class="rowdowinput" id="rowdowstyle"></div>
                <div class="rowdowinput">网盘名称：<input type="text" name="rowdowname" id="rowdowname" placeholder="如：百度网盘" value="'.$rlit_dwname.'"/><a>必填</a></div>
                <div class="rowdowinput">分辨率：<input type="text" name="rowdowpx" id="rowdowpx" placeholder="如：1024x1080px" value="'.$rlit_dwfx.'"/></div>
                <div class="rowdowinput">下载地址：<input type="text" name="rowdowurl" id="rowdowurl" placeholder="https://" value="'.$rlit_dwurl.'"/><a>必填</a></div>
                <div class="rowdowinput">提取码：<input type="text" name="rowdowpas" id="rowdowpas" value="'.$rlit_dwxt.'"/></div>
                <div class="rowdowinput">文件数量：<input type="text" name="rowdowmun" id="rowdowmun" placeholder="如：30P+2V" value="'.$rlit_dwnum.'"/><a>必填</a></div>
                <div class="rowdowinput">文件大小：<input type="text" name="rowdowsize" id="rowdowsize" placeholder="如：128MB" value="'.$rlit_dwsz.'"/><a>必填</a></div>
                <div class="rowdowinput">解压密码：<input type="text" name="rowdowzip" id="rowdowzip" value="'.$rlit_dwpwd.'"/></div>
                <div id="rowdowcloseyes">确认</div>
                <span id="rowdowcloseerr"></span>
            </dialog>
            <script src="/style/js/rowdow.js" type="text/javascript"></script>
            <textarea name="rowtext" id="rowtext">'.$rlit_text.'</textarea>
            <div class="newword-title"><span class="letter1em">置顶：</span>
            <input type="radio" id="nawrowtop" name="rowtop" value="1" '.$rlit_top1.' /><label for="nawrowtop">无</label>
            <input type="radio" id="rowtop" name="rowtop" value="2" '.$rlit_top2.'/><label for="rowtop">置顶</label>
            <input type="radio" id="rowtop2" name="rowtop" value="3" '.$rlit_top3.'/><label for="rowtop2">热门</label>
            <input type="radio" id="rowtop3" name="rowtop" value="4" '.$rlit_top4.'/><label for="rowtop3">精华</label>
            </div>
            <div class="newword-title3"><button id="newwordsubmit">提交</button></div>
        </form>
        <script type="text/javascript"> var rowid="'.$listid.'";</script>
        <script src="/style/js/editform.js" type="text/javascript"></script>
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
        echo '<script type="text/javascript">var listfllinkid="'.$listfllinkid.'";var listflid="'.$rlit_fl.'";</script>';
        echo '<script src="/style/js/select.js" type="text/javascript"></script>';
    }
}
?>