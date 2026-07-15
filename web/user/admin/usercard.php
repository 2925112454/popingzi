<?php
if ($admin==1 && $typeuser==8 && ($allvip==4||$allvip==3)  && !empty($ppzusername)){

    if  (!isset($_POST["scard"])){
        $_POST["scard"]="";
    }
    if  (!isset($_GET["k"])){
        $_GET["k"]="";
    }
    if  (!isset($_GET["p"])){
        $_GET["p"]="";
    }
    if  (!isset($_GET["if"])){
        $_GET["if"]="";
    }
    $so=trim($_POST["scard"]);
    $kv=trim($_GET["k"]);
    $num_rec_per_page=20;// 每页显示数量
    $getp=trim($_GET["p"]);//获取GET传参P
    $allif=trim($_GET["if"]);//充值卡分类，1月度会员，2季度会员，3年度会员，4百年会员，5积分充值

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
            $st="充值卡列表";
            $iif=0;
            $gkv='';
        }else{
            $s='<a href="popingzi.php?type=8">返回列表</a>';
            $plavalue=strip_tags($so);
            $plavaluex = '%' . mysqli_real_escape_string($conn, $plavalue) . '%';
            $where="where vvar LIKE '$plavaluex'";
            $st="充值卡搜索";
            $gkv="&k=".$plavalue;
        }
    }else{
            $s='<a href="popingzi.php?type=8">返回列表</a>';
            $plavalue=strip_tags($kv);
            $plavaluex = '%' . mysqli_real_escape_string($conn, $plavalue) . '%';
            $where="where vvar LIKE '$plavaluex'";
            $st="充值卡搜索";
            $gkv="&k=".$plavalue;
    }

    if (empty($allif)){
        $allif=null;
        $alliftext='全部类型';
        $pif='';
    }else{
        $allif=intval($allif);//转换为整数
        if ($allif==1){
            $allif=1;
            $alliftext='月度会员';
            $pif='&if=1';
        }elseif ($allif==2){
            $allif=2;
            $alliftext='季度会员';
            $pif='&if=2';
        }elseif ($allif==3){
            $allif=3;
            $alliftext='年度会员';
            $pif='&if=3';
        }elseif ($allif==4){
            $allif=4;
            $alliftext='百年会员';
            $pif='&if=4';
        }elseif ($allif==5){
            $allif=5;
            $alliftext='积分充值';
            $pif='&if=5';
        }else{
            $allif=null;
            $pif='';
            $alliftext='全部类型';
        }
    }

    echo '
    <div class="user-h1">'.$st.'
        <form id="listformso" method="post" action="popingzi.php?type=8">
        <input type="text" name="scard" placeholder="搜索充值卡号" value="'.$plavalue.'" />
        <button type="submit">搜索</button>'.$s.'
        </form>
    </div>

    <div class="listdivr mab">

       <div><span>筛选：</span><div class="dropdown">
       <button class="dropbtn">'.$alliftext.'<i class="fa fa-sort"></i></button>
       <div class="dropdown-content">
       <a href="?type=8'.$gkv.'">全部类型</a>
           <a href="?type=8&if=1'.$gkv.'">月度会员</a>
           <a href="?type=8&if=2'.$gkv.'">季度会员</a>
           <a href="?type=8&if=3'.$gkv.'">年度会员</a>
           <a href="?type=8&if=4'.$gkv.'">百年会员</a>
           <a href="?type=8&if=5'.$gkv.'">积分充值</a>
       </div>
       </div></div>

            <div><span>操作：</span>
                <div class="dropdown"><button id="newcard" class="dropbtn"><i class="fa fa-plus" aria-hidden="true"></i>添加充值卡</button></div>
                <div class="dropdown"><button id="cardedit" class="dropbtn"><i class="fa fa-wrench" aria-hidden="true"></i>充值卡设置</button></div>
                <div class="dropdown"><button id="cardtext" class="dropbtn"><i class="fa fa-share" aria-hidden="true"></i>充值卡导出</button></div>
            </div>
   </div>
    ';

if (empty($where)){
    if ($allif==1||$allif==2||$allif==3||$allif==4||$allif==5){
        $where="where vbin='$allif'";
    }else{
        $where="";
    }
}else{
    if ($allif==1||$allif==2||$allif==3||$allif==4||$allif==5){
        $where=$where." and vbin='$allif'";
    }else{
        $where=$where;
    }
}

$sqlll = "SELECT * FROM ppz_vtime " .$where;//搜索类
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

if ($plavalue!==""&&!empty($plavalue)){
 $k="&k=".$plavalue;
}else{
 $k="";
}

if ($p==1){
 $pageindex='<a class="page-no-button nocopy">首页</a>';//首页按钮
}else{
 $pageindex='<a class="page-button nocopy" href="?type=8'.$k.''.$pif.'">首页</a>';//首页按钮
}

if ($p==$total_pages){
 $pagebody='<a class="page-no-button nocopy" >尾页</a>';
}else{
 $pagebody='<a class="page-button nocopy" href="?type=8&p='.$total_pages.$k.''.$pif.'">尾页</a>';
}

if ($total_pages>1&&$p<$total_pages){
 $exit=$p+1;
 $pageexit='<a class="page-button nocopy" href="?type=8&p='.$exit.$k.''.$pif.'">下一页</a>';
}else{
 $pageexit='<a class="page-no-button nocopy" >下一页</a>';
}

if ($p<=$total_pages&&$p>1){
 $exitup=$p-1;
 $pageup='<a class="page-button nocopy" href="?type=8&p='.$exitup.$k.''.$pif.'">上一页</a>';
}else{
 $pageup='<a class="page-no-button nocopy" >上一页</a>';
}
             $rsql = "$sqlll ORDER BY vid desc LIMIT $start_from, $num_rec_per_page";//获取数据库表
             $rretval=mysqli_query($conn,$rsql);
             if(mysqli_num_rows($rretval) < 1){ 
                 echo '<div class="adminrownull">什么也没有~</div>';
             }else{
                echo '<div class="regtxt-row">
                    <table style="width:100%;" class="regtxt-table">
                        <thead>
                          <tr>
                            <th width="8%">选择</th>
                            <th width="20%">充值卡类型</th>
                            <th width="40%">充值卡号</th>
                            <th width="20%">充值积分</th>
                            <th width="12%">操作</th>
                          </tr>
                        </thead>
                        <tfoot>
                          <tr>
                            <td colspan="5">
                                <div class="clear">
                                  <span class="page-left"><a id="allcardif">全选/全不选</a><a id="delallcard">批量删除</a>第'.$p.'页（共'.$total_pages.'页）- 共计：'.$total_recordsll.'条记录</span>
                                  <span class="page-right">
                                  '.$pageindex.$pageup.$pageexit.$pagebody.'
                                  </span>
                              </div></td>
                          </tr>
                        </tfoot>
                        <tbody>            
                    ';
                    while($listr = mysqli_fetch_array($rretval)){
                        $id=$listr["vid"];//id
                        $vbin=$listr["vbin"];//充值卡类型，1月度会员，2季度会员，3年度会员，4百年会员，5积分充值
                        $vgold=$listr["vgold"];//积分充值数量，1为10,2为20，3为30,4为40，5为50,6为100,7为1000
                        $vvar=$listr["vvar"];//充值卡卡号
                        $newvvar=htmlspecialchars($vvar);

                        if ($vbin==1){
                            $vbintext='月度会员';
                            $vgold='*';
                        }elseif ($vbin==2){
                            $vbintext='季度会员';
                            $vgold='*';
                        }elseif ($vbin==3){
                            $vbintext='年度会员';
                            $vgold='*';
                        }elseif ($vbin==4){
                            $vbintext='百年会员';
                            $vgold='*';
                        }elseif ($vbin==5){
                            $vbintext='积分充值';
                            if ($vgold==1){
                                $vgold=10;
                            }elseif ($vgold==2){
                                $vgold=20;
                            }elseif ($vgold==3){
                                $vgold=30;
                            }elseif ($vgold==4){
                                $vgold=40;
                            }elseif ($vgold==5){
                                $vgold=50;
                            }elseif ($vgold==6){
                               $vgold=100;
                            }elseif ($vgold==7){
                                $vgold=1000;
                             }
                        }else{
                            $vbintext='未知类型';
                            $vgold='*';
                        }

                        if(!empty($vvar)&&$id>0&&!empty($id)&&is_numeric($id)){
                            echo'
                            <tr class="alt-row"> 
                            <td><input type="checkbox" name="cardid" value="'.$id.'">
                            </td>
                            <td>'.$vbintext.'</td>
                            <td id="cardyesid'.$id.'">'.$newvvar.'</td>
                            <td>'.$vgold.'</td>
                            <td><a class="editcard" data-t="'.$vvar.'" data-i="'.$id.'" title="编辑"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> <a class="delcard" data-d='.$id.' title="删除"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                            </tr>
                            ';
                        }else{
                            echo'
                            参数错误！
                            ';
                        }
                        
                    }
                    echo '
                    </tbody>
                    </table>
                    </div>
            ';

             }


//获取充值卡配置信息
$cardsetsql = "SELECT * FROM ppz_cardset WHERE setid = 1";
$cardseter = mysqli_query($conn, $cardsetsql);
if ($cardseter->num_rows == 1) {
    while ($cardsetrow = mysqli_fetch_assoc($cardseter)) {
        $setrmbyue = $cardsetrow['setrmbyue'];//月度价格
        $seturlyue = $cardsetrow['seturlyue'];//月度购买地址
        $setrmbji = $cardsetrow['setrmbji'];//季度价格
        $seturlji = $cardsetrow['seturlji'];//季度购买地址
        $setrmbnian = $cardsetrow['setrmbnian'];//年度价格
        $seturlnian = $cardsetrow['seturlnian'];//年度购买地址
        $setrmbbai = $cardsetrow['setrmbbai'];//百年价格
        $seturlbai = $cardsetrow['seturlbai'];//百年购买地址
        $setrmbshi = $cardsetrow['setrmbshi'];//10积分价格
        $seturlshi = $cardsetrow['seturlshi'];//10积分购买地址
        $setrmber = $cardsetrow['setrmber'];//20积分价格
        $seturler = $cardsetrow['seturler'];//20积分购买地址
        $setrmbsan = $cardsetrow['setrmbsan'];//30积分价格
        $seturlsan = $cardsetrow['seturlsan'];//30积分购买地址
        $setrmbsi = $cardsetrow['setrmbsi'];//40积分价格
        $seturlsi = $cardsetrow['seturlsi'];//40积分购买地址
        $setrmbwu = $cardsetrow['setrmbwu'];//50积分价格
        $seturlwu = $cardsetrow['seturlwu'];//50积分购买地址
        $setrmbyi = $cardsetrow['setrmbyi'];//100积分价格
        $seturlyi = $cardsetrow['seturlyi'];//100积分购买地址
        $setrmbqian = $cardsetrow['setrmbqian'];//1000积分价格
        $seturlqian = $cardsetrow['seturlqian'];//1000积分购买地址
    }   
}


             echo '
                <dialog id="navfldialog" class="navfldialog">
        <a id="navfldialogclose"><i class="fa fa-times" aria-hidden="true"></i></a>
        <b>新增充值卡</b>
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
            
            <div class="upfileradio upfileradios">  
                分类：
                <div class="upfilelabel nocopy"><input type="radio" name="notifupx" id="notifup-yue" value="1" class="custom-radio" checked><label for="notifup-yue">月度会员</label></div>
                <div class="upfilelabel nocopy"><input type="radio" name="notifupx" id="notifup-ji" value="2" class="custom-radio"><label for="notifup-ji">季度会员</label></div>
                <div class="upfilelabel nocopy"><input type="radio" name="notifupx" id="notifup-nian" value="3" class="custom-radio"><label for="notifup-nian">年度会员</label></div>
                <div class="upfilelabel nocopy"><input type="radio" name="notifupx" id="notifup-bainian" value="4" class="custom-radio"><label for="notifup-bainian">百年会员</label></div>
                <div class="upfilelabel nocopy"><input type="radio" name="notifupx" id="notifup-jifen" value="5" class="custom-radio"><label for="notifup-jifen">积分充值</label></div>
            </div>

                <div id="jifennot" class="upfileradio upfileradios">积分：
                    <div class="upfilelabel nocopy"><input type="radio" name="notifups" id="notifup-a" value="1" class="custom-radio" checked><label for="notifup-a">10</label></div>
                <div class="upfilelabel nocopy"><input type="radio" name="notifups" id="notifup-b" value="2" class="custom-radio"><label for="notifup-b">20</label></div>
                <div class="upfilelabel nocopy"><input type="radio" name="notifups" id="notifup-c" value="3" class="custom-radio"><label for="notifup-c">30</label></div>
                <div class="upfilelabel nocopy"><input type="radio" name="notifups" id="notifup-d" value="4" class="custom-radio"><label for="notifup-d">40</label></div>
                <div class="upfilelabel nocopy"><input type="radio" name="notifups" id="notifup-e" value="5" class="custom-radio"><label for="notifup-e">50</label></div>
                <div class="upfilelabel nocopy"><input type="radio" name="notifups" id="notifup-f" value="6" class="custom-radio"><label for="notifup-f">100</label></div>
                <div class="upfilelabel nocopy"><input type="radio" name="notifups" id="notifup-g" value="7" class="custom-radio"><label for="notifup-g">1000</label></div>
                </div>
            <div><button id="navfldialogbutnew">生成</button><button id="nullcard">清空预览</button><button id="copycard">复制</button><button id="cardfile">导出</button></div>


                <textarea id="navfldialogtextarear" placeholder="预览窗口，请生成随机充值卡……" readonly></textarea>
            <button id="navfldialogbut"><i class="fa fa-plus" aria-hidden="true"></i>添加至数据库</button>
            <span id="navfldialogerr"></span>
        </dialog>

                <dialog id="navfldialogx" class="navfldialog">
                <a id="navfldialogclosex"><i class="fa fa-times" aria-hidden="true"></i></a>
                <b>导出充值卡</b>
                    <div class="upfileradio upfileradios">  
                        导出：
                        <div class="upfilelabel nocopy"><input type="checkbox" name="notifupdc" id="notifup-dyue" value="1" class="custom-radio" checked><label for="notifup-dyue">月度会员</label></div>
                        <div class="upfilelabel nocopy"><input type="checkbox" name="notifupdc" id="notifup-dji" value="2" class="custom-radio" ><label for="notifup-dji">季度会员</label></div>
                        <div class="upfilelabel nocopy"><input type="checkbox" name="notifupdc" id="notifup-dnian" value="3" class="custom-radio" ><label for="notifup-dnian">年度会员</label></div>
                        <div class="upfilelabel nocopy"><input type="checkbox" name="notifupdc" id="notifup-dbai" value="4" class="custom-radio"><label for="notifup-dbai">百年会员</label></div>
                        <div class="upfilelabel nocopy"><input type="checkbox" name="notifupdc" id="notifup-dfen" value="5" class="custom-radio"><label for="notifup-dfen">积分充值</label></div>
                    </div>

                        <div id="jifennotx" class="upfileradio upfileradios">积分：
                            <div class="upfilelabel nocopy"><input type="checkbox" name="notifupsd" id="notifup-ad" value="1" class="custom-radio" checked><label for="notifup-ad">10</label></div>
                        <div class="upfilelabel nocopy"><input type="checkbox" name="notifupsd" id="notifup-bd" value="2" class="custom-radio"><label for="notifup-bd">20</label></div>
                        <div class="upfilelabel nocopy"><input type="checkbox" name="notifupsd" id="notifup-cd" value="3" class="custom-radio"><label for="notifup-cd">30</label></div>
                        <div class="upfilelabel nocopy"><input type="checkbox" name="notifupsd" id="notifup-dd" value="4" class="custom-radio"><label for="notifup-dd">40</label></div>
                        <div class="upfilelabel nocopy"><input type="checkbox" name="notifupsd" id="notifup-ed" value="5" class="custom-radio"><label for="notifup-ed">50</label></div>
                        <div class="upfilelabel nocopy"><input type="checkbox" name="notifupsd" id="notifup-fd" value="6" class="custom-radio"><label for="notifup-fd">100</label></div>
                        <div class="upfilelabel nocopy"><input type="checkbox" name="notifupsd" id="notifup-gd" value="7" class="custom-radio"><label for="notifup-gd">1000</label></div>
                        </div>

                    <div><button id="huoqucard">获取</button><button id="cardall">全选/全不选</button><button id="copycardx">复制</button><button id="cardfilex">导出</button></div>

                        <textarea id="navfldialogtextarearx" placeholder="预览窗口，请先获取数据……" readonly></textarea>
                    <span id="navfldialogerrx"></span>
                </dialog>


                
                <dialog id="cardset" class="cardsetnavfldialog">
                <a id="cardsetclose"><i class="fa fa-times" aria-hidden="true"></i></a>
                <b>充值卡信息配置</b>

                        <div class="upfileradio upfileradios">  
                        <span class="cardsettit">月度会员<i class="fa fa-caret-right" aria-hidden="true"></i></span>
                            <div class="upfilelabel nocopy">
                            购买地址：<input type="text" value="'.$seturlyue.'" class="ipturl" id="yueurlipt" />
                            价格(元)：<input type="number" value="'.$setrmbyue.'" min="0" max="999999999" class="iptrmb" id="yuermbipt" />
                            </div>
                        </div>

                        <div class="upfileradio upfileradios">  
                        <span class="cardsettit">季度会员<i class="fa fa-caret-right" aria-hidden="true"></i></span>
                            <div class="upfilelabel nocopy">
                            购买地址：<input type="text" value="'.$seturlji.'" class="ipturl" id="jiurlipt" />
                            价格(元)：<input type="number" value="'.$setrmbji.'" min="0" max="999999999" class="iptrmb" id="jirmbipt" />
                            </div>
                        </div>

                        <div class="upfileradio upfileradios">  
                        <span class="cardsettit">年度会员<i class="fa fa-caret-right" aria-hidden="true"></i></span>
                            <div class="upfilelabel nocopy">
                            购买地址：<input type="text" value="'.$seturlnian.'" class="ipturl" id="nianurlipt" />
                            价格(元)：<input type="number" value="'.$setrmbnian.'" min="0" max="999999999" class="iptrmb" id="nianrmbipt" />
                            </div>
                        </div>

                        <div class="upfileradio upfileradios">  
                        <span class="cardsettit">百年会员<i class="fa fa-caret-right" aria-hidden="true"></i></span>
                            <div class="upfilelabel nocopy">
                            购买地址：<input type="text" value="'.$seturlbai.'" class="ipturl" id="baiurlipt" />
                            价格(元)：<input type="number" value="'.$setrmbbai.'" min="0" max="999999999" class="iptrmb" id="bairmbipt" />
                            </div>
                        </div>

                        <div class="upfileradio upfileradios">  
                        <span class="cardsettit">10积分<i class="fa fa-caret-right" aria-hidden="true"></i></span>
                            <div class="upfilelabel nocopy">
                            购买地址：<input type="text" value="'.$seturlshi.'" class="ipturl" id="shiurlipt" />
                            价格(元)：<input type="number" value="'.$setrmbshi.'" min="0" max="999999999" class="iptrmb" id="shirmbipt" />
                            </div>
                        </div>

                        <div class="upfileradio upfileradios">  
                        <span class="cardsettit">20积分<i class="fa fa-caret-right" aria-hidden="true"></i></span>
                            <div class="upfilelabel nocopy">
                            购买地址：<input type="text" value="'.$seturler.'" class="ipturl" id="erurlipt" />
                            价格(元)：<input type="number" value="'.$setrmber.'" min="0" max="999999999" class="iptrmb" id="errmbipt" />
                            </div>
                        </div>

                        <div class="upfileradio upfileradios">  
                        <span class="cardsettit">30积分<i class="fa fa-caret-right" aria-hidden="true"></i></span>
                            <div class="upfilelabel nocopy">
                            购买地址：<input type="text" value="'.$seturlsan.'" class="ipturl" id="sanurlipt" />
                            价格(元)：<input type="number" value="'.$setrmbsan.'" min="0" max="999999999" class="iptrmb" id="sanrmbipt" />
                            </div>
                        </div>

                        <div class="upfileradio upfileradios">  
                        <span class="cardsettit">40积分<i class="fa fa-caret-right" aria-hidden="true"></i></span>
                            <div class="upfilelabel nocopy">
                            购买地址：<input type="text" value="'.$seturlsi.'" class="ipturl" id="siurlipt" />
                            价格(元)：<input type="number" value="'.$setrmbsi.'" min="0" max="999999999" class="iptrmb" id="sirmbipt" />
                            </div>
                        </div>

                        <div class="upfileradio upfileradios">  
                        <span class="cardsettit">50积分<i class="fa fa-caret-right" aria-hidden="true"></i></span>
                            <div class="upfilelabel nocopy">
                            购买地址：<input type="text" value="'.$seturlwu.'" class="ipturl" id="wuurlipt" />
                            价格(元)：<input type="number" value="'.$setrmbwu.'" min="0" max="999999999" class="iptrmb" id="wurmbipt" />
                            </div>
                        </div>

                        <div class="upfileradio upfileradios">  
                        <span class="cardsettit">100积分<i class="fa fa-caret-right" aria-hidden="true"></i></span>
                            <div class="upfilelabel nocopy">
                            购买地址：<input type="text" value="'.$seturlyi.'" class="ipturl" id="yiurlipt" />
                            价格(元)：<input type="number" value="'.$setrmbyi.'" min="0" max="999999999" class="iptrmb" id="yirmbipt" />
                            </div>
                        </div>

                        <div class="upfileradio upfileradios">  
                        <span class="cardsettit">1000积分<i class="fa fa-caret-right" aria-hidden="true"></i></span>
                            <div class="upfilelabel nocopy">
                            购买地址：<input type="text" value="'.$seturlqian.'" class="ipturl" id="qianurlipt" />
                            价格(元)：<input type="number" value="'.$setrmbqian.'" min="0" max="999999999" class="iptrmb" id="qianrmbipt" />
                            </div>
                        </div>
                        
                <button id="cardsetbut">确认修改</button>
                    <span id="cardseterr"></span>
                </dialog>


                <dialog id="carddialog">
                    <a id="carddialogclose"><i class="fa fa-times" aria-hidden="true"></i></a>
                    <b>修改充值卡号</b>
                    <input type="text" id="carddialoginput" placeholder="请输入卡号">
                    <button id="carddialogbut">确定</button>
                    <span id="carddialogerr"></span>
                </dialog>




<script src="/style/js/usercard.js" type="text/javascript"></script>
             
             ';

}else{
    echo "请勿胡搞！";
}
?>