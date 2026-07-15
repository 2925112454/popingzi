<?php
if ($admin==1 && $typeuser==4 && ($allvip==4||$allvip==3)  && !empty($ppzusername)){
    if(!isset($_GET["sid"])){
        $_GET["sid"]="";   
    }
    if (!isset($_POST["us"])){
        $_POST["us"]="";
    }
    if (!isset($_GET["k"])){
        $_GET["k"]="";
    }
    if  (!isset($_GET["p"])){
        $_GET["p"]="";
    }

    $listid=trim($_GET["sid"]);//文章id
    $listid=intval($listid);    //转换为数字
    $so=trim($_POST["us"]);
    $kv=trim($_GET["k"]);
    $num_rec_per_page=20;// 每页显示数量
    $getp=trim($_GET["p"]);//获取GET传参P

    /*判断参数P是否为空，且是否是数字*/
    if (isset($getp) && is_numeric($getp) && $getp>=1 ){ 
    $pa = $_GET["p"];
    } else { 
    $pa=1; 
    }; 


if (empty($kv)){
    if(empty($so)){
        $s="";
        $plavalue="";
        $where="";
        $st="用户列表";
    }else{
        $s='<a href="popingzi.php?type=4">返回列表</a>';
        $plavalue=strip_tags($so);
        if ($so==="%正常%"){
            $where="where uban=1";
        }else if( $so==="%封禁%" ){
            $where="where uban=2";
        }else if( $so==="%普通用户%" ){
            $where="where (uviptime IS NULL OR uviptime < NOW())";
        }else if( $so==="%VIP会员%" ){
             $where="where uviptime IS NOT NULL AND uviptime >= NOW()";
        }else if(filter_var($so, FILTER_VALIDATE_EMAIL)){
            $where="where uemail='$plavalue'";
        }else{
            // 判断是否为数字
            if(is_numeric($plavalue)&&$plavalue>0){
                $where="where uusername='$plavalue'";
            }else{
                $where="where uname='$plavalue'";
            }
        }
        $st="筛选用户";
    }
}else{
        $s='<a href="popingzi.php?type=4">返回列表</a>';
        $plavalue=strip_tags($kv);
        if ($kv==="%正常%"){
            $where="where uban=1";
        }else if( $kv==="%封禁%" ){
            $where="where uban=2";
        }else if( $kv==="%普通用户%" ){
            $where="where (uviptime IS NULL OR uviptime < NOW())";
        }else if( $kv==="%VIP会员%" ){
             $where="where uviptime IS NOT NULL AND uviptime >= NOW()";
        }else if(filter_var($kv, FILTER_VALIDATE_EMAIL)){
            $where="where uemail='$plavalue'";
        }else{
           // 判断是否为数字
           if(is_numeric($plavalue)&&$plavalue>0){
                $where="where uusername='$plavalue'";
            }else{
                $where="where uname='$plavalue'";
            }
        }
        $st="筛选用户";
}

    
    if(empty($listid)||!is_numeric($listid)||$listid<1||!is_int($listid)||!ctype_digit($_GET["sid"])){

        $sqlll = "SELECT * FROM ppz_newusername $where"; //链接数据表
        $rs_result = mysqli_query($conn,$sqlll); //查询数据
        $total_records = mysqli_num_rows($rs_result);  // 统计数据总数
        $total_pages = ceil($total_records / $num_rec_per_page);  // 计算总页数
        
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
            $pageindex='<a class="page-button nocopy" href="?type=4'.$k.'">首页</a>';//首页按钮
        }
        
        if ($p==$total_pages){
            $pagebody='<a class="page-no-button nocopy" >尾页</a>';
        }else{
            $pagebody='<a class="page-button nocopy" href="?type=4&p='.$total_pages.$k.'">尾页</a>';
        }
        
        if ($total_pages>1&&$p<$total_pages){
            $exit=$p+1;
            $pageexit='<a class="page-button nocopy" href="?type=4&p='.$exit.$k.'">下一页</a>';
        }else{
            $pageexit='<a class="page-no-button nocopy" >下一页</a>';
        }
        
        if ($p<=$total_pages&&$p>1){
            $exitup=$p-1;
            $pageup='<a class="page-button nocopy" href="?type=4&p='.$exitup.$k.'">上一页</a>';
        }else{
            $pageup='<a class="page-no-button nocopy" >上一页</a>';
        }
        
        
        
        
                echo '
                <div class="user-h1">'.$st.'
                    <form id="listformso" method="post" action="popingzi.php?type=4">
                    <input type="text" style="min-width:500px;" name="us" placeholder="支持账号、昵称、邮箱、状态(%正常%、%封禁%、%VIP会员%、%普通用户%)" value="'.$plavalue.'" />
                    <button type="submit">搜索</button>'.$s.'       
                    </form>
                </div>
                ';
                
                $rsql = "select * from ppz_newusername $where ORDER BY uid desc LIMIT $start_from, $num_rec_per_page";//获取会员数据库表
                $rretval=mysqli_query($conn,$rsql);
                if(mysqli_num_rows($rretval) < 1){ 
                    echo '<div class="adminrownull">什么也没有~</div>';
                }else{
                    echo '<div class="regtxt-row">
                    <table class="regtxt-table">
                        <thead>
                          <tr>
                            <th width="8%">选择</th>
                            <th width="8%">状态</th>
                            <th width="20%">昵称</th>
                            <th width="14%">账号</th>
                            <th width="20%">邮箱</th>
                            <th width="10%">积分</th>
                            <th width="10%">注册时间</th>
                            <th width="10%">操作</th>
                          </tr>
                        </thead>
                        <tfoot>
                          <tr>
                            <td colspan="8">
                                <div class="clear">
                                  <span class="page-left"><a id="allcheckbox">全选/全不选</a><a id="allcheckboxdel">批量删除</a><a id="allcheckboxexe">封禁/解封</a>第'.$p.'页（共'.$total_pages.'页）- 共计：'.$total_records.'条记录</span>
                                  <span class="page-right">
                                  '.$pageindex.$pageup.$pageexit.$pagebody.'
                                  </span>
                              </div></td>
                          </tr>
                        </tfoot>
                        <tbody>            
                    ';
                    while($listr = mysqli_fetch_array($rretval)){
                        $id=$listr["uid"];//id
                        $uname=$listr["uname"];//昵称
                        $uusername=$listr["uusername"];//账号
                        $uemail=$listr["uemail"];//邮箱
                        $utime=$listr["utime"];//注册时间
                        $uviptime=$listr["uviptime"];//会员时间
                        $yes=$listr["uban"];//状态
                        $ugold=$listr["ugold"];//积分
                        $ustatus=$listr["ustatus"];//身份，1普通会员，2为管理员，3为副站长，4为站长
                        if($yes==1){
                            $yes="<span class='yes'>正常</span>";
                        }else if($yes==2){
                            $yes="<span class='no'>封禁</span>";
                        }else{
                            $yes="<span class='no'>异常状态</span>";
                        }

                        if ($ustatus==1){
                            if (!empty($uviptime)&&strtotime($uviptime)>=time()&&$uviptime!==""&&$uviptime!=="0"){
                                $ustatus="<span class='vipyestop'>VIP</span>";
                            }else{
                                $ustatus="";
                            }                            
                        }else if($ustatus==2){
                            $ustatus="<span class='adminyes'>管理员</span>";
                        }else if($ustatus==3){
                            $ustatus="<span class='vipadminyes'>副站长</span>";
                        }else if($ustatus==4){
                            $ustatus="<span class='vipadminyes'>站长</span>";
                        }
        
                            echo'
                            <tr class="alt-row"> 
                            <td><input type="checkbox" name="id" value="'.$id.'">
                            </td>
                            <td style="max-width:40px;">'.$yes.'</td>
                            <td style="max-width:100px;"><a href="/user.php?id='.$id.'" target="_blank">'.$ustatus.''.$uname.'</a></td>
                            <td style="max-width:60px;">'.$uusername.'</td>
                            <td style="max-width:90px;">'.$uemail.'</td> 
                            <td style="max-width:130px;">'.$ugold.'</td>
                            <td style="max-width:130px;">'.$utime.'</td>
                            <td><a href="?type=4&sid='.$id.'" title="编辑"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> <a class="udel" data-d='.$id.' title="删除"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                            </tr>
                            ';
                    }
                    echo '
                    </tbody>
                    </table>
                    </div>
                    <div id="customMenu">
                        <div id="menuCheck">封禁用户</div>
                        <div id="menuCheckx">解封用户</div>
                    </div>
                    <script src="/style/js/checkboxuser.js" type="text/javascript"></script>
                    <script src="/style/js/userrightclick.js" type="text/javascript"></script>
                    ';
        
        
                };
        
            }else{
                /*编辑会员页面*/
                
                //获取会员信息
                $sqluser = "select * from ppz_newusername where uid='$listid'";
                $retvaluser=mysqli_query($conn,$sqluser);
                
                //判断会员是否存在
                if (mysqli_num_rows($retvaluser)!==1){
                    echo '<div class="user-h1">该用户不存在~<a href="?type=4"><i class="fa fa-angle-double-left" aria-hidden="true"></i>返回列表</a></div>';
                    echo '<div class="adminrownull">空空如也~</div>';
                }else{

                    while($newu = mysqli_fetch_array($retvaluser)){
                        $eituid=$newu["uid"];//id
                        $eituname=$newu["uname"];//昵称
                        $eituusername=$newu["uusername"];//账号
                        $eituemail=$newu["uemail"];//邮箱
                        $eitutime=$newu["utime"];//注册时间
                        $eituviptime=$newu["uviptime"];//会员时间
                        $eityes=$newu["uban"];//状态,1为正常，2为封禁
                        $eitugold=$newu["ugold"];//积分
                        $eitustatus=$newu["ustatus"];//身份，1普通会员，2为管理员，3为副站长，4为站长
                        $eituimg=$newu["uimg"];//头像
                        $eitucollect=$newu["ucollect"];//收藏列表
                        $eitutel=$newu["utel"];//手机号
                        $eitupersonal=$newu["upersonal"];//简介
                        $eitusex=$newu["usex"];//性别，1为男，2为女
                        $eituurl=$newu["uurl"];//网址
                        $eituip=$newu["uip"];//IP地址
                        $eiturowyes=$newu["urowyes"];//购买列表
                        $eitutelyes=$newu["utelyes"];//手机验证状态，1为未验证，2为已验证
                        $eituemailyes=$newu["uemailyes"];//邮箱验证状态，1为未验证，2为已验证                      
                    }

if($allvip<=$eitustatus && $allvip!=4 && $allnameid!=$eituid){
echo '
<div class="user-h1">无权操作~
<a href="?type=4"><i class="fa fa-angle-double-left" aria-hidden="true"></i>返回列表</a>
</div>
<div class="adminrownull">您无权修改该会员信息~</div>';
}else{

                    if($yes==1){
                        $yes="<span class='yes'>正常</span>";
                    }else if($yes==2){
                        $yes="<span class='no'>封禁</span>";
                    }else{
                        $yes="<span class='no'>异常状态</span>";
                    }

                    if ($eitutelyes==2){
                        $telyes="checked";
                        $telno="";
                    }else{
                        $telno="checked";
                        $telyes="";
                    }

                    if ($eituemailyes==2){
                        $emailyes="checked";
                        $emailno="";
                    }else{
                        $emailno="checked";
                        $emailyes="";
                    }
                    $disabled="";
                    $vip1="";
                    $vipzz="";
                    $vip2="";
                    $vip3="";
                    if ($eitustatus==1){
                        $vip1="selected";
                    }else if($eitustatus==2){
                        $vip2="selected";
                    }else if ($eitustatus==3){
                        $vip3="selected";
                        if ($allvip!=4){$disabled='disabled';$vipzz="<option selected value='3'>副站长</option>";}
                    }else if ($eitustatus==4){
                        $vipzz="<option selected value='4'>站长</option>";
                        $disabled='disabled';
                    }else{
                        $vip1="selected";
                    }

                    if ($eitusex==2){
                        $sex1="";
                        $sex2="selected";
                    }else{
                        $sex1="selected";
                        $sex2="";
                    }

                    if ($eityes==2){
                        $fj1="";
                        $fj2='selected';
                    }else{
                        $fj1='selected';
                        $fj2="";
                    }

                    if($allvip==4){
                        $vipzx="<option $vip3 value='3'>副站长</option>";
                    }else{
                        $vipzx="";
                    }

                    echo '<div class="user-h1">编辑用户：'.$eituname.'<a href="?type=4"><i class="fa fa-angle-double-left" aria-hidden="true"></i>返回列表</a></div>';
                    echo '
                    <div class="newword">
                        <form method="post" id="userform">

                        <div class="newword-title"><span class="letter">头像：</span><input style="width: 50%;" type="text" name="userimg" id="rowimg" value="'.$eituimg.'" /><a id="newworduploadimg">上传</a><a id="xxxjpg">检查头像</a><i>* 留空则使用默认头像。</i></div>

                        <div class="newword-title">
                            <div class="newword-title2" style="width:46%;">时间：<input style="width: 66%;" type="datetime-local" name="newtime" value="'.$eitutime.'" /></div>
                            <div class="newword-title2" style="width:46%;">会员时间：<input style="width: 66%;" type="datetime-local" name="viptime"   value="'.$eituviptime.'" /></div>
                        </div>

                        <div class="newword-title">
                            <div class="newword-title2">昵称：<input type="text" name="name" value="'.$eituname.'" /></div>
                            <div class="newword-title2">账号：<input type="text" maxlength="11" name="user" value="'.$eituusername.'" /></div>
                            <div class="newword-title2">邮箱：<input type="email" name="email" value="'.$eituemail.'" /></div>
                        </div>

                        <div class="newword-title">
                            <div class="newword-title2">手机：<input type="text" maxlength="11" name="tel" value="'.$eitutel.'" /></div>
                            <div class="newword-title2">网址：<input type="url" placeholder="https://" name="url"   value="'.$eituurl.'" /></div>
                            <div class="newword-title2">积分：<input type="text" name="gold"  maxlength="9" value="'.$eitugold.'" /></div>
                        </div>


                                <div class="newword-title">

                                    <div class="newword-title2"><span class="letter">状态：</span>
                                    <select name="userif" >
                                    <option value="1" '.$fj1.'>正常</option>
                                    <option value="2" '.$fj2.'>封禁</option>
                                    </select>
                                    </div>

                                    <div class="newword-title2"><span class="letter">性别：</span>
                                    <select name="sexif" >
                                    <option value="1" '.$sex1.'>帅哥</option>
                                    <option value="2" '.$sex2.'>美女</option>
                                    </select>
                                    </div>

                                    <div class="newword-title2"><span class="letter">身份：</span>
                                    <select name="vipif" '.$disabled.'>
                                    <option value="1" '.$vip1.'>普通用户</option>
                                    <option value="2" '.$vip2.'>管理员</option>
                                    '.$vipzx.'
                                    '.$vipzz.'
                                    </select>
                                    </div>
                                    
                                    <div class="newword-title2" style="width:38%;">IP地址：<input style="width:63%;" type="text" name="ip"   value="'.$eituip.'" /></div>
                                </div>                          

                                <div class="newword-title"><span class="letter">简介：</span><input maxlength="240" placeholder="简介不能超过240个字" type="text" name="ict" class="rowhead"  value="'.$eitupersonal.'" /></div>
                                <div class="newword-title"><span class="letter">购买：</span><input type="text" placeholder="填写文章ID，多个请以“|”分割" title="填写文章ID，多个请以“|”分割" name="pcl" class="rowhead"  value="'.$eiturowyes.'" /></div>
                                <div class="newword-title"><span class="letter">收藏：</span><input type="text" placeholder="填写文章ID，多个请以“|”分割" title="填写文章ID，多个请以“|”分割" name="cl" class="rowhead"  value="'.$eitucollect.'" /></div>
                            <div class="newword-title">
                            <div class="newword-title4"><span class="letter">手机验证状态：</span>
                            <input type="radio" id="telno" name="telif" value="1" '.$telno.' /><label for="telno">未验证</label>
                            <input type="radio" id="telyes"  name="telif" value="2" '.$telyes.'/><label for="telyes">已验证</label>
                            </div>
                            <div class="newword-title4"><span class="letter">邮箱验证状态：</span>
                            <input type="radio" id="emilno" name="emilif" value="1" '.$emailno.' /><label for="emilno">未验证</label>
                            <input type="radio" id="emilyes" name="emilif" value="2" '.$emailyes.'/><label for="emilyes">已验证</label>
                            </div>
                            </div>
                            <div class="newword-title3"><button id="newusersubmit">提交</button></div>
                        </form>
                    </div>
                    ';
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
                    <dialog id="xxxjpgdia" class="xxxjpgdia"></dialog>
                    <script src="/style/js/edituser.js" type="text/javascript"></script>
                    <script src="/style/js/select.js" type="text/javascript"></script>
                    ';
                }

            }



            }




}else{
    echo "请勿胡搞！";
}
?>