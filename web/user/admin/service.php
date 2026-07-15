<?php
if ($admin==1 && $typeuser==7 && ($allvip==4||$allvip==3||$allvip==2)  && !empty($ppzusername)){
    if (!isset($_POST["ks"])){
        $_POST["ks"]="";
    }
    if (!isset($_GET["k"])){ 
        $_GET["k"]="";
    }
    if (!isset($_GET["p"])){ 
        $_GET["p"]="";
    }
    if (!isset($_GET["fl"])){ 
        $_GET["fl"]="";
    }
    if (!isset($_GET["if"])){ 
        $_GET["if"]="";
    }
    $so=trim($_POST["ks"]);
    $kv=trim($_GET["k"]);
    $num_rec_per_page=20;// 每页显示数量
    $getp=trim($_GET["p"]);//获取GET传参P
    $allfl=trim($_GET["fl"]);//获取筛选分类id
    $allif=trim($_GET["if"]);//获取筛选状态,1待处理，2已处理，3已回复

    if (empty($allfl)){
        $allfl=null;
        $allfltext='全部分类';
        $pfl='';
    }else{
        $allfl=intval($allfl);//转换为整数
        $sqlsfl="SELECT * FROM ppz_workfl WHERE id='$allfl'";
        $resultfl = mysqli_query($conn, $sqlsfl);
        if (mysqli_num_rows($resultfl) == 1) {
            while ($rowfl = mysqli_fetch_array($resultfl)) {
                $allfltext= $rowfl["wkname"];
            }
            $pfl='&fl='.$allfl;
        }else{
            $allfl=null;
            $allfltext='分类异常';
            $pfl='';
        }
    }

    if (empty($allif)){
        $allif=null;
        $alliftext='全部状态';
        $pif='';
    }else{
        $allif=intval($allif);//转换为整数
        if ($allif==1){
            $allif=1;
            $alliftext='待处理';
            $pif='&if=1';
        }elseif ($allif==2){
            $allif=2;
            $alliftext='已处理';
            $pif='&if=2';
        }elseif ($allif==3){
            $allif=3;
            $alliftext='已回复';
            $pif='&if=3';
        }else{
            $allif=null;
            $pif='';
        }
    }
    

    /*判断参数P是否为空，且是否是数字*/
    if (isset($getp) && is_numeric($getp) && $getp>=1 ){ 
    $pa = $_GET["p"];
    } else { 
    $pa=1; 
    }; 
    if (empty($kv)||!isset($kv)){
        if(empty($so)||!isset($so)){
            $s="";
            $plavalue="";
            $where="";
            $st="工单管理";
        }else{
            $s='<a href="popingzi.php?type=7">返回列表</a>';
            $plavalue=strip_tags($so);
            $plavaluex = '%' . mysqli_real_escape_string($conn, $plavalue) . '%';
            $where="where wktext LIKE '$plavaluex'";
            $st="工单搜索";
            
        }
    }else{
            $s='<a href="popingzi.php?type=7">返回列表</a>';
            $plavalue=strip_tags($kv);
            $plavaluex = '%' . mysqli_real_escape_string($conn, $plavalue) . '%';
            $where="where wktext LIKE '$plavaluex'";
            $st="工单搜索";           
    }

if (empty($where)){
    if (empty($allfl)||empty($allfl)||$allfl<1){
       if (empty($allif)||empty($allif)||$allif<1||$allif>3){
            $wheret="";
        }else{
            if ($allif==3){
                $wheret='where wkhf is not null and wkhf !=""';
            }else{
                $wheret='where wkyes='.$allif;
            }
        }
    }else{
        if (empty($allif)||empty($allif)||$allif<1||$allif>3){
            $wheret="where wkfl=".$allfl;
        }else{
            if ($allif==3){
                $wheret='where wkhf is not null and wkhf !="" and wkfl='.$allfl.'';
            }else{
                $wheret='where wkyes='.$allif.' and wkfl='.$allfl.'';
            }
            
        }
    }
}else{
    if (empty($allfl)||empty($allfl)||$allfl<1){
        if (empty($allif)||empty($allif)||$allif<1||$allif>3){
             $wheret=$where;
         }else{
             if ($allif==3){
                $wheret=$where.'and wkhf is not null and wkhf !=""';
             }else{
                $wheret=$where.'and wkyes='.$allif;
             }             
         }
     }else{
         if (empty($allif)||empty($allif)||$allif<1||$allif>3){
             $wheret=$where."and wkfl=".$allfl;
         }else{
             if ($allif==3){
                $wheret=$where.'and wkhf is not null and wkhf !=""  and wkfl='.$allfl.'';
             }else{
                $wheret=$where.'and wkyes='.$allif.' and wkfl='.$allfl.'';
             }
            
         }
     }
}

    

    echo '
    <div class="user-h1">'.$st.'
        <form id="listformso" method="post" action="popingzi.php?type=7">
        <input type="text" name="ks" placeholder="搜索工单标题" value="'.$plavalue.'" />
        <button type="submit">搜索</button>'.$s.'
        </form>
    </div>';

    if (empty($kv)&&empty($so)){
        echo '<div class="listdivr ma">
        <div class="dropdown">
       <button class="dropbtn">'.$allfltext.'<i class="fa fa-sort"></i></button>
       <div class="dropdown-content">
       <a href="?type=7&if='.$allif.'">全部分类</a>
       ';
       
       // 全部分类
       $sqllflall = "SELECT * FROM ppz_workfl";
       $resultlflall = mysqli_query($conn, $sqllflall);
       if (mysqli_num_rows($resultlflall) > 0) {
           while ($rowlflall = mysqli_fetch_array($resultlflall)) {
               $idlflall = $rowlflall["id"];
               $wktextlflall = $rowlflall["wkname"];
               echo '<a href="?type=7&fl='.$idlflall.'&if='.$allif.'">'.$wktextlflall.'</a>';
           }
       }else{
           echo '<a href="?type=7&if='.$allif.'">全部分类</a>';
       }

echo '</div>
       </div>

       <div class="dropdown">
       <button class="dropbtn">'.$alliftext.'<i class="fa fa-sort"></i></button>
       <div class="dropdown-content">
       <a href="?type=7&fl='.$allfl.'">全部状态</a>
           <a href="?type=7&fl='.$allfl.'&if=1">待处理</a>
           <a href="?type=7&fl='.$allfl.'&if=2">已处理</a>
           <a href="?type=7&fl='.$allfl.'&if=3">已回复</a>
       </div>
       </div>
   </div>
';
    }





    $sqlll = "SELECT * FROM ppz_work " .$wheret;//搜索类
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

if (!empty($plavalue)){
    $k="&k=".$plavalue;
}else{
    $k="";
}

if ($p==1){
    $pageindex='<a class="page-no-button nocopy">首页</a>';//首页按钮
}else{
    $pageindex='<a class="page-button nocopy" href="?type=7'.$k.''.$pif.''.$pfl.'">首页</a>';//首页按钮
}

if ($p==$total_pages){
    $pagebody='<a class="page-no-button nocopy" >尾页</a>';
}else{
    $pagebody='<a class="page-button nocopy" href="?type=7&p='.$total_pages.$k.''.$pif.''.$pfl.'">尾页</a>';
}

if ($total_pages>1&&$p<$total_pages){
    $exit=$p+1;
    $pageexit='<a class="page-button nocopy" href="?type=7&p='.$exit.$k.''.$pif.''.$pfl.'">下一页</a>';
}else{
    $pageexit='<a class="page-no-button nocopy" >下一页</a>';
}

if ($p<=$total_pages&&$p>1){
    $exitup=$p-1;
    $pageup='<a class="page-button nocopy" href="?type=7&p='.$exitup.$k.''.$pif.''.$pfl.'">上一页</a>';
}else{
    $pageup='<a class="page-no-button nocopy" >上一页</a>';
}
                $rsql = "$sqlll ORDER BY id desc LIMIT $start_from, $num_rec_per_page";//获取数据库表
                $rretval=mysqli_query($conn,$rsql);
                if(mysqli_num_rows($rretval) < 1){ 
                    echo '<div class="adminrownull">什么也没有~</div>';
                }else{
                    echo '<div class="regtxt-row">
                    <table class="regtxt-table">
                        <thead>
                          <tr>
                            <th width="8%">选择</th>
                            <th width="8%">分类</th>
                            <th width="8%">状态</th>
                            <th width="36%">标题</th>
                            <th width="20%">提交人</th>
                            <th width="10%">时间</th>
                            <th width="10%">操作</th>
                          </tr>
                        </thead>
                        <tfoot>
                          <tr>
                            <td colspan="7">
                                <div class="clear">
                                  <span class="page-left"><a id="allcheckbox">全选/全不选</a><a id="delallmess">批量删除</a><a id="allservice">批量状态</a>第'.$p.'页（共'.$total_pages.'页）- 共计：'.$total_recordsll.'条记录</span>
                                  <span class="page-right">
                                  '.$pageindex.$pageup.$pageexit.$pagebody.'
                                  </span>
                              </div></td>
                          </tr>
                        </tfoot>
                        <tbody>            
                    ';
                    while($listr = mysqli_fetch_array($rretval)){
                        $id=$listr["id"];//获取ID
                        $wktext=$listr["wktext"];//标题
                        $wkword=$listr["wkword"];//内容
                        $wkadmin=$listr["wkadmin"];//提交人
                        $wkimg=$listr["wkimg"];//附件
                        $wkyes=$listr["wkyes"];//状态,1为待处理，2为已处理
                        $wkfl=$listr["wkfl"];//分类
                        $wktime=$listr["wktime"];//时间
                        $wkhf=$listr["wkhf"];//管理员回复


                        //状态
                        if(empty($wkhf)){
                            if($wkyes==1){
                                $wkyes='<span>未回复</span><span class="no">待处理</span>';
                            }else{
                                $wkyes='<span class="yes">已处理</span><span>未回复</span>';
                            }
                        }else{
                            if($wkyes==1){
                                $wkyes='<span class="yes">已回复</span><span class="no">待处理</span>';
                            }else{
                                $wkyes='<span class="yes">已处理</span><span class="yes">已回复</span>';
                            }
                        }

                        //分类
                        if(empty($wkfl)||!filter_var($wkfl, FILTER_VALIDATE_INT)){ //判断分类是否为空或者不是数字
                            $wkfl='<span class="no">未知分类</span>';
                        }else{
                            $wkflsql= "SELECT * FROM ppz_workfl WHERE id = '$wkfl'";
                            $wkflretval=mysqli_query($conn,$wkflsql);
                            if(mysqli_num_rows($wkflretval) !== 1){
                                $wkfl='<span class="no">分类异常</span>';
                            }else{
                                while($wkflr = mysqli_fetch_array($wkflretval)){
                                    $wkfl=$wkflr["wkname"];
                                }
                            }
                        }

                        //附件地址
                        if(empty($wkimg)){
                            $wkimg=null;
                        }else{
                            $wkimg=$wkimg;
                        }
                        
                        //提交人
                        if(empty($wkadmin)){
                            $wkadmin='<span class="no">未知会员</span>';
                        }else{
                            $wkadminsql= "SELECT * FROM ppz_newusername WHERE uid = '$wkadmin'";
                            $wkadminretval=mysqli_query($conn,$wkadminsql);
                            if(mysqli_num_rows($wkadminretval) !== 1){
                                $wkadmin='<span class="no">会员异常</span>';
                            }else{
                                while($wkadminr = mysqli_fetch_array($wkadminretval)){
                                    $wkadmin=$wkadminr["uname"];//提交人昵称
                                    $wkadminid=$wkadminr["uid"];//提交人ID
                                }
                            }
                        }

                        echo'
                        <tr class="alt-row"> 
                        <td><input type="checkbox" name="seid" value="'.$id.'">
                        </td>
                        <td style="max-width:40px;">'.$wkfl.'</td>
                        <td style="max-width:40px;">'.$wkyes.'</td>
                        <td style="max-width:100px;"><a data-link="'.$wkimg.'" data-eye="'.$wkword.'" class="eyemess">'.$wktext.'</a></td>
                        <td style="max-width:130px;"><a href="/user.php?id='.$wkadminid.'" target="_blank">'.$wkadmin.'</a></td>
                        <td style="max-width:130px;">'.$wktime.'</td>
                        <td><a class="editmess" data-hft="'.$wkhf.'" data-zt="'.$listr["wkyes"].'" data-seid="'.$id.'" title="编辑"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> <a class="delmess" data-d='.$id.' title="删除"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                        </tr>
                        ';

                    }

                    echo '
                    </tbody>
                    </table>
                    </div>
                    <dialog id="eyescon" style="max-width: 500px; min-width: 300px;">
                    <a id="eyesconclose"><i class="fa fa-times" aria-hidden="true"></i></a>
                    <b>工单内容</b>
                    <div id="eyescontext"></div>
                    <a id="eyelink" target="_blank"><i class="fa fa-link" aria-hidden="true"></i>查看附件</a>
                    </dialog>
                    <dialog id="navfldialog"><a id="navfldialogclose"><i class="fa fa-times" aria-hidden="true"></i></a><b>处理工单</b><textarea id="navfldialogtextarea" placeholder="请输入回复内容"></textarea>
                    <div class="upfileradio upfileradiox">  
          状态：
          <div class="upfilelabel nocopy"><input type="radio" name="notifup" id="notifup-allow" value="1" class="custom-radio"><label for="notifup-allow">待处理</label></div>
          <div class="upfilelabel nocopy"><input type="radio" name="notifup" id="notifup-deny" value="2" class="custom-radio"><label for="notifup-deny">已处理</label></div>
        </div>
        
                    <button id="navfldialogbut" data-yid="">确定</button><span id="navfldialogerr"></span></dialog>
                    <div id="customMenu">
                        <div id="menuCheck">已处理</div>
                        <div id="menuCheckx">待处理</div>
                    </div>
                    <script src="/style/js/serviceedit.js" type="text/javascript"></script>
                    <script src="/style/js/servicerightclick.js" type="text/javascript"></script>
                    ';
                }

}else{
    echo "请勿胡搞！";
}
?>