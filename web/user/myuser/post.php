<?php
ob_start();
if (empty($allnameid) || !isset($allnameid) || !is_numeric($allnameid) || $allnameid < 1 ||
    !isset($myuser) || empty($myuser) || $myuser != 200 ||
    !isset($ppzusername) || empty($ppzusername) ||
    !isset($typeuser) || empty($typeuser) || $typeuser !=10) {
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
//分类
$link_sql="SELECT linkid,linkname FROM `ppz_link`";
$link_res=mysqli_query($conn,$link_sql);
if ($link_res&&mysqli_num_rows($link_res)>0) {
    while ($link_row=mysqli_fetch_assoc($link_res)) {
        $link_id=$link_row['linkid'];
        $link_name=$link_row['linkname'];
        $link_list.='<option value="'.$link_id.'">'.$link_name.'</option>';
    }
}else{
    $link_list='<option value="0">神秘领域</option>';
}
//默认选择第一个分类的二级分类
if ($link_res&&mysqli_num_rows($link_res)>0) {
    mysqli_data_seek($link_res, 0);
    $first_link_row = mysqli_fetch_assoc($link_res);
    $first_link_id = $first_link_row['linkid'];
    
    $link_sql2="SELECT flid,flname FROM `ppz_fl` where fllinkid=$first_link_id order by flid asc";
    $link_res2=mysqli_query($conn,$link_sql2);
    if ($link_res2&&mysqli_num_rows($link_res2)>0) {
        while ($link_row2=mysqli_fetch_assoc($link_res2)) {
            $link_id2=$link_row2['flid'];
            $link_name2=$link_row2['flname'];
            $link_list2.='<option value="'.$link_id2.'">'.$link_name2.'</option>';
        }            
    }else{
        $link_list2='<option value="0">暂无二级分类</option>';
    }
}else{
    $link_list2='<option value="0">暂无二级分类</option>';
}
//投稿配置
$post_sql="SELECT * FROM `ppz_upfile` where id=1";
$post_res=mysqli_query($conn,$post_sql);
if ($post_res&&mysqli_num_rows($post_res)>0) {
    while ($post_row=mysqli_fetch_assoc($post_res)) {
        $post_off=$post_row['upif'];//投稿开关，0关闭，1开启
        $postimg_off=$post_row['upifimg'];//上传开关，0关闭，1开启
        $post_mime=$post_row['upmime'];//补充说明
        $post_size=$post_row['upsize'];//投稿文件大小限制(KB)
        $post_num=$post_row['upfcsize'];//投稿分成百分比
        $post_num_vip=$post_row['upvipsize'];//vip会员折扣
    }
    function PHPSizeToKB($size_str) {
            $size_str = trim($size_str);
            $last = strtolower($size_str[strlen($size_str)-1]);
            $size = (float) $size_str;
            
            switch($last) {
                case 'g': $size *= 1024 * 1024; break; // GB → KB
                case 'm': $size *= 1024; break;       // MB → KB
                case 'k': break;                     // 已是KB，无需转换
                default: $size /= 1024; break;       // 为字节 → KB
            }
            return round($size, 0); 
    }

    $upload_max_filesize = ini_get('upload_max_filesize');// 获取上传文件的最大大小限制
    $post_max_size = ini_get('post_max_size');// 获取POST请求的最大大小限制
    if(PHPSizeToKB($post_max_size)<PHPSizeToKB($upload_max_filesize)){
        $upload_max_filesize=$post_max_size;
    }
    if($post_size<1||empty($post_size)){
        $up_maxsize='，上传的单个图片大小不能超过：'.$upload_max_filesize.'。';
        $post_size=PHPSizeToKB($upload_max_filesize);
    }else{
        if($post_size>PHPSizeToKB($upload_max_filesize)){
            $up_maxsize="，上传的单个图片大小不能超过：".$upload_max_filesize."。";
            $post_size=PHPSizeToKB($upload_max_filesize);
        }else{
            $up_maxsize="，上传的单个图片大小不能超过：".$post_size."KB。";
            $post_size=$post_size;
        }
    }


    if(empty($post_mime)){
        $post_mime='';
    }else{
        $post_mime='<br/><br/><b>补充说明：</b><br/>'.nl2br($post_mime);
    }

    if($post_num<=0){
        $post_t_v='<div class="messg-not"><p><b>投稿说明：</b></p><p>我为人人，人人为我，无偿投稿，为爱发光！</p></div>';
    }else{
        if($post_num_vip==100){
            $post_t_v_t='；因为VIP会员可免费获得下载内容，所以VIP会员的购买下载将没有提成。';
        }elseif($post_num_vip==0){
            $post_t_v_t='；VIP会员的购买下载不会影响您获得的提成比例。';
        }else{
            $post_t_v_t='；虽然VIP会员购买下载可享受'.$post_num_vip.'%的优惠，但是优惠并不会影响您获得的提成比例；';
        }
       $post_t_v='<div class="post_text"><b>投稿须知：</b><br/>1.用户购买下载内容后，您可获得'.$post_num.'%的积分提成(免费下载除外)'.$post_t_v_t.'<br/>2.投稿内容一旦审核通过并发布，您将失去对投稿内容的删除和编辑权利，若有修改需求请<a href="user.php?type=9">提交工单</a>要求管理进行解决。<br/>3.本站不是储存类网站，您对自己投稿的内容进行购买下载操作，也会被扣除相应积分，同时提成也会生效。<br/>4.仅限上传jpg、png、webp、avif、gif格式的图片文件'.$up_maxsize.$post_mime.'</div>';
    }
    if ($post_off==1) {
        if($postimg_off==1){
            $upimg_bnt='<input type="button" class="tougao-btn" id="imgbtn" value="上传图片"/><input id="imgfile" type="file" accept=".jpg,.jpeg,.gif,.png,.webp,.avif"/>';
            $upimg="on";
        }else{
            $upimg_bnt='';
            $upimg="off";
        }

        echo '
        <script src="/style/tinymce/tinymce.min.js"></script>
        <div class="user-h1 myuser">发布投稿<span><a id="tipsbtn"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>投稿须知（投稿默认同意须知内容）</a></span></div>
        <div class="padding_15px">
            <div class="tougao-item">
                <label class="tougao-title-label">标题 *</label>
                <input type="text" id="post_title" class="tougao-input" value="" required="">
		    </div>
            <div class="tougao-item">
                <label class="tougao-title-label">分类 *</label>
                <div>
                <select id="cat" class="postform">
                    '.$link_list.'
                </select>
                <select id="catfl" class="postform">
                    '.$link_list2.'
                </select></div>
		    </div>
            <div class="tougao-item">
                <label class="tougao-title-label">封面</label>
                <div><input type="text" id="post_img" placeholder="https://" class="tougao-input" value="" required=""/>'.$upimg_bnt.'</div>
		    </div>
            <div class="tougao-item border-bottom margin-bottom-15px">
                <label class="tougao-title-label">正文 *</label>
                <textarea id="post_text" class="tougao-textarea"></textarea>
		    </div>
            <div class="tougao-item">
                <label class="tougao-title-label">下载配置</label>
                <div class="dw_fx"><div class="post_dw"><input type="number" id="post_dw_jf" class="tougao-input" value="0" min="0" max="999999999" required="" /></div>
                <input type="button" class="tougao-btn" id="dwbtn" value="配置下载信息"/></div>
		    </div>
            <a id="post_btn" class="btn">发布</a>
        </div>
        <dialog id="rowdowdialog">
            <div id="rowdowclose"><i class="fa fa-times"></i></div>
            <b>配置下载信息</b>
            <div class="rowdowinput" id="rowdowstyle" style="display: flex;">快速操作：<div class="rowdowstyle"><a class="diydowstyle" id="allnull">一键清空</a></div></div>
            <div class="rowdowinput">网盘名称：<input type="text" name="rowdowname" id="rowdowname" placeholder="如：百度网盘"><a>必填</a></div>
            <div class="rowdowinput">分辨率：<input type="text" name="rowdowpx" id="rowdowpx" placeholder="如：1024x1080px"></div>
            <div class="rowdowinput">下载地址：<input type="text" name="rowdowurl" id="rowdowurl" placeholder="https://"><a>必填</a></div>
            <div class="rowdowinput">提取码：<input type="text" name="rowdowpas" id="rowdowpas"></div>
            <div class="rowdowinput">文件数量：<input type="text" name="rowdowmun" id="rowdowmun" placeholder="如：30P+2V"><a>必填</a></div>
            <div class="rowdowinput">文件大小：<input type="text" name="rowdowsize" id="rowdowsize" placeholder="如：128MB"><a>必填</a></div>
            <div class="rowdowinput">解压密码：<input type="text" name="rowdowzip" id="rowdowzip"></div>
            <div id="rowdowcloseyes">确认</div>
            <span id="rowdowcloseerr"></span>
        </dialog>
        <dialog id="tougaotips" class="user_dialog padding_15px">
        <div id="tipsclose" class="user_dialog_close"><i class="fa fa-times"></i></div>
            '.$post_t_v.'
        </dialog>
        <script type="text/javascript">
        let post_max_size='.$post_size.';
        let upimgoff="'.$upimg.'";
        </script>
        <script type="text/javascript" src="/style/tinymce/tougao.js"></script>
        <script>
            document.getElementById("cat").addEventListener("change", function() {
            const link_id = this.value;
            const catfl = document.getElementById("catfl");           
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "myuser/get_subcategories.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (this.readyState === 4 && this.status === 200) {
                    catfl.innerHTML = this.responseText;
                }
            };
            xhr.send("link_id=" + link_id);
        });
        </script>
        ';
    }else{
        echo '<div class="user-h1 myuser">发布投稿</div>
            <div class="padding_15px">
                <div class="nulldiv">暂未开放投稿</div>
            </div>
        ';
    }    
}else{
  echo '<div class="user-h1 myuser">发布投稿</div>
    <div class="padding_15px">
        <div class="nulldiv">暂未开放投稿</div>
    </div>
    ';
}
ob_end_flush();
?>