<?php
ob_start();
if (empty($allnameid) || !isset($allnameid) || !is_numeric($allnameid) || $allnameid < 1 ||
    !isset($myuser) || empty($myuser) || $myuser != 200 ||
    !isset($ppzusername) || empty($ppzusername) ||
    !isset($typeuser) || empty($typeuser) || $typeuser != 9) {
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
//获取工单分类
$type_sql="SELECT * FROM `ppz_workfl` order by id asc";
$type_res=mysqli_query($conn,$type_sql);
echo '<div class="user-h1 myuser">提交工单</div>
<div class="padding_15px">
 <div class="input_group">分类：
    <select id="select">';
        if ($type_res&&mysqli_num_rows($type_res)>0) {
            while ($type_row=mysqli_fetch_assoc($type_res)) {
                echo '<option value="'.$type_row['id'].'">'.$type_row['wkname'].'</option>';
            }
        }else{
            echo '<option value="0">暂无分类</option>';
        }
   echo ' </select>
 </div>
 <div class="input_group">标题：<input id="title" maxlength="" type="text" class="search-input" placeholder="简单描述问题" /><span>*必填</span></div>
 <div class="input_group">附件：<input id="images" type="text" class="search-input" placeholder="输入存在问题的页面地址或者附件地址（可留空）" /><span>图片可上传至<a href="https://imgse.com/" target="_blank">图床</a>；其它文件请用<a href="https://www.lanzout.com/" target="_blank">网盘</a>等方式。</span></div>
 <div class="input_group_column">内容：<textarea maxlength="" placeholder="输入详细的问题描述" id="text"></textarea></div>
 <span class="maxlength">剩余字数：<span id="mun">0</span></span>
 <a id="post" class="btn">提交</a>
</div>
<script src="/style/js/postwork.js" type="text/javascript"></script>
';
ob_end_flush();
?>