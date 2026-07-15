<?php
if ($admin==1 && $typeuser==15 && ($allvip==4||$allvip==3)  && !empty($ppzusername)){

    if (!isset($_POST["regcode"])){
        $_POST["regcode"]="";
    }
    if (!isset($_GET["k"])){ 
        $_GET["k"]="";
    }
    if (!isset($_GET["p"])){ 
        $_GET["p"]="";
    }

    $so=trim($_POST["regcode"]);
    $kv=trim($_GET["k"]);
    $num_rec_per_page=20;// 每页显示数量
    $getp=trim($_GET["p"]);//获取GET传参P

    /*判断参数P是否为空，且是否是数字*/
    if (isset($getp) && is_numeric($getp) && $getp>=1 ){ 
        $pa = trim($_GET["p"]);
    } else { 
        $pa=1; 
    }; 

    if (empty($kv)||!isset($kv)){
        if(empty($so)||!isset($so)){
            $s="";
            $plavalue="";
            $where="";
            $st="邀请码列表";
            $iif=0;
            $gkv='';
        }else{
            $s='<a href="popingzi.php?type=15">返回列表</a>';
            $plavalue=strip_tags($so);
            $plavaluex = '%' . mysqli_real_escape_string($conn, $plavalue) . '%';
            $where="where invitecode LIKE '$plavaluex'";
            $st="邀请码搜索";
            $gkv="&k=".$plavalue;
        }
    }else{
            $s='<a href="popingzi.php?type=15">返回列表</a>';
            $plavalue=strip_tags($kv);
            $plavaluex = '%' . mysqli_real_escape_string($conn, $plavalue) . '%';
            $where="where invitecode LIKE '$plavaluex'";
            $st="邀请码搜索";
            $gkv="&k=".$plavalue;
    }

    echo '
    <div class="user-h1">'.$st.'
        <form id="listformso" method="post" action="popingzi.php?type=15">
        <input type="text" name="regcode" placeholder="搜索邀请码" value="'.$plavalue.'" />
        <button type="submit">搜索</button>'.$s.'
        </form>
    </div>

    <div class="listdivr mab">
            <div><span>操作：</span>
                <div class="dropdown"><button id="newcode" class="dropbtn"><i class="fa fa-plus" aria-hidden="true"></i>添加邀请码</button></div>
                <div class="dropdown"><button id="codeedit" class="dropbtn"><i class="fa fa-wrench" aria-hidden="true"></i>邀请码设置</button></div>
                <div class="dropdown"><button id="codetext" class="dropbtn"><i class="fa fa-share" aria-hidden="true"></i>邀请码导出</button></div>
            </div>
   </div>';

$sqlll = "SELECT * FROM ppz_code " .$where;//搜索类
// 执行SQL查询
$rs_resultll = mysqli_query($conn, $sqlll);
// 获取数据总数
$total_recordsll = mysqli_num_rows($rs_resultll);
// 计算总页数
$total_pages = ceil($total_recordsll / $num_rec_per_page);
if ($total_pages < $pa){
 $p=1;
 }else{
 $p=$pa; 
}
$start_from = ($p-1) * $num_rec_per_page;

if ($plavalue!==""&&!is_null($plavalue)){
 $k="&k=".$plavalue;
}else{
 $k="";
}

if ($p==1){
 $pageindex='<a class="page-no-button nocopy">首页</a>';//首页按钮
}else{
 $pageindex='<a class="page-button nocopy" href="?type=15'.$k.'">首页</a>';//首页按钮
}

if ($p==$total_pages){
 $pagebody='<a class="page-no-button nocopy" >尾页</a>';
}else{
 $pagebody='<a class="page-button nocopy" href="?type=15&p='.$total_pages.$k.'">尾页</a>';
}

if ($total_pages>1&&$p<$total_pages){
 $exit=$p+1;
 $pageexit='<a class="page-button nocopy" href="?type=15&p='.$exit.$k.'">下一页</a>';
}else{
 $pageexit='<a class="page-no-button nocopy" >下一页</a>';
}

if ($p<=$total_pages&&$p>1){
 $exitup=$p-1;
 $pageup='<a class="page-button nocopy" href="?type=15&p='.$exitup.$k.'">上一页</a>';
}else{
 $pageup='<a class="page-no-button nocopy" >上一页</a>';
}

            $rsql = "$sqlll ORDER BY id desc LIMIT $start_from, $num_rec_per_page";//获取数据库表
             $rretval=mysqli_query($conn,$rsql);
             if(mysqli_num_rows($rretval) < 1){ 
                 echo '<div class="adminrownull">什么也没有~</div>';
             }else{
                echo '
                    <div class="regtxt-row">
                    <table style="width:100%;" class="regtxt-table">
                        <thead>
                          <tr>
                            <th width="8%">选择</th>
                            <th width="80%">邀请码</th>
                            <th width="12%">操作</th>
                          </tr>
                        </thead>
                        <tfoot>
                          <tr>
                            <td colspan="3">
                                <div class="clear">
                                  <span class="page-left"><a id="allcode">全选/全不选</a><a id="delallcode">批量删除</a>第'.$p.'页（共'.$total_pages.'页）- 共计：'.$total_recordsll.'条记录</span>
                                  <span class="page-right">
                                  '.$pageindex.$pageup.$pageexit.$pagebody.'
                                  </span>
                              </div></td>
                          </tr>
                        </tfoot>
                        <tbody>';

                        while($listr = mysqli_fetch_array($rretval)){
                            $id=$listr['id'];
                            $code=$listr["invitecode"];//邀请码
                            $newcode=htmlspecialchars($code);
                            echo '
                            <tr class="alt-row"> 
                            <td><input type="checkbox" name="codeid" value="'.$id.'">
                            </td>
                            <td id="codeid'.$id.'">'.$newcode.'</td>
                            <td><a class="editcode" data-t="'.$code.'" data-i="'.$id.'" title="编辑"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> <a class="delcode" data-d='.$id.' title="删除"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                            </tr>
                            ';
                        }

             }

            echo '
                    </tbody>
                    </table>
                    </div>
            ';

//获取邀请码配置信息
$codesetsql = "SELECT * FROM ppz_codeset WHERE setid = 1";
$codeseter = mysqli_query($conn, $codesetsql);
if ($codeseter->num_rows == 1) {
    while ($coderow = mysqli_fetch_assoc($codeseter)) {
        $seturl = $coderow['seturl'];//邀请码获取地址
        $setrmb = $coderow['setrmb'];//邀请码获取价格
        $settext = $coderow['settext'];///邀请码获取说明
    }
}

echo '
        <dialog id="navfldialog" class="navfldialog">
        <a id="navfldialogclose"><i class="fa fa-times" aria-hidden="true"></i></a>
        <b>新增邀请码</b>
            <div class="upfileradio upfileradios">  
                随机：
                <div class="upfilelabel nocopy"><input type="checkbox" name="notifup" id="notifup-allow" value="1" class="custom-radio" checked><label for="notifup-allow">数字</label></div>
                <div class="upfilelabel nocopy"><input type="checkbox" name="notifup" id="notifup-deny" value="2" class="custom-radio" checked><label for="notifup-deny">大写字母</label></div>
                <div class="upfilelabel nocopy"><input type="checkbox" name="notifup" id="notifup-denyd" value="3" class="custom-radio" checked><label for="notifup-denyd">小写字母</label></div>
                <div class="upfilelabel nocopy"><input type="checkbox" name="notifup" id="notifup-denyx" value="4" class="custom-radio"><label for="notifup-denyx">特殊字符</label></div>
            </div>

                <div class="upfileradio upfileradios">前缀：
                    <div class="upfilelabel nocopy"><input type="text" id="qianzhui" /></div>
                    <div class="upfilelabel nocopy">位数：<input type="number" id="weishu" min="1" max="64" value="12" /></div>
                    <div class="upfilelabel nocopy">条数：<input type="number" id="tiaoshu" min="1" max="1000" value="50" /></div>
                </div>
            
            <div><button id="navfldialogbutnew">生成邀请码</button><button id="nullcard">清空预览</button><button id="copycard">复制</button><button id="cardfile">导出</button></div>


                <textarea id="navfldialogtextarear" placeholder="预览窗口，请生成随机邀请码……" readonly></textarea>
            <button id="navfldialogbut"><i class="fa fa-plus" aria-hidden="true"></i>添加至数据库</button>
            <span id="navfldialogerr"></span>
        </dialog>

                <dialog id="navfldialogx" class="navfldialog">
                <a id="navfldialogclosex"><i class="fa fa-times" aria-hidden="true"></i></a>
                <b>导出邀请码</b>
                        <div><button id="huoqucard">获取邀请码</button><button id="copycardx">复制</button><button id="cardfilex">导出</button></div>
                        <textarea id="navfldialogtextarearx" placeholder="预览窗口，请先获取数据……" readonly></textarea>
                    <span id="navfldialogerrx"></span>
                </dialog>


                
                    <dialog id="cardset" class="cardsetnavfldialog">
                        <a id="cardsetclose"><i class="fa fa-times" aria-hidden="true"></i></a>
                        <b>邀请码信息配置</b>
                                <div class="upfileradio upfileradios">  
                                    <div class="upfilelabel nocopy">
                                    获取地址：<input type="text" value="'.$seturl.'" class="ipturl" id="codeurl" />
                                    价格(元)：<input type="number" value="'.$setrmb.'" min="0" max="999999999" class="iptrmb" id="codermb" />
                                    </div>
                                </div>

                                <div class="upfileradio upfileradios">  
                                    <div class="upfilelabelx nocopy">
                                    说明：<textarea id="codetextval" class="codetext">'.$settext.'</textarea>
                                    </div>
                                </div>
                        <button id="cardsetbut">确认修改</button>
                            <span id="cardseterr"></span>
                    </dialog>


                        <dialog id="carddialog">
                            <a id="carddialogclose"><i class="fa fa-times" aria-hidden="true"></i></a>
                            <b>修改邀请码</b>
                            <input type="text" id="carddialoginput" placeholder="请输入邀请码">
                            <button id="carddialogbut">确定</button>
                            <span id="carddialogerr"></span>
                        </dialog>




<script src="/style/js/usercode.js" type="text/javascript"></script>
             
             ';



}else{
    echo "请勿胡搞！";
}
?>