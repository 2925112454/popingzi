<?php
if ($admin==1 && $typeuser==5 && ($allvip==4||$allvip==3||$allvip==2)  && !empty($ppzusername)){
if(!isset($_POST["uscom"])){
    $_POST["uscom"]="";
}
if (!isset($_GET["k"])){
    $_GET["k"]="";
}
if (!isset($_GET["p"])){
    $_GET["p"]="";
}
if (!isset($_GET["sid"])){
    $_GET["sid"]="";
}
if (!isset($_GET["if"])){
    $_GET["if"]="";
}
    $so=trim($_POST["uscom"]);
    $kv=trim($_GET["k"]);
    $num_rec_per_page=20;// 每页显示数量
    $getp=trim($_GET["p"]);//获取GET传参P
    $uuid=trim($_GET["sid"]);//获取GET传参id
    $uif=trim($_GET["if"]);//获取GET传参id
    /*判断参数P是否为空，且是否是数字*/
    if (isset($getp) && is_numeric($getp) && $getp>=1 ){ 
    $pa = $_GET["p"];
    } else { 
    $pa=1; 
    }; 
    if ($kv==""||is_null($kv)||empty($kv)||!isset($kv)){
        if($so==""||is_null($so)||empty($so)||!isset($so)){
            $s="";
            $plavalue="";
            $where="";
            $st="评论列表";
            $iif=0;
        }else{
            $s='<a href="popingzi.php?type=5">返回列表</a>';
            $plavalue=strip_tags($so);
                if ($so=="%文章评论%"){
                    $iif=1;
                    $st="文章评论列表";
                }else if($so=="%公告评论%"){
                    $iif=2;
                    $st="公告评论列表";
                }else{
                    $iif=0;
                    $plavaluex = '%' . mysqli_real_escape_string($conn, $plavalue) . '%';
                    $where="where plbigtext LIKE '$plavaluex'";
                    $st="评论搜索";
                }
            
        }
    }else{
            $s='<a href="popingzi.php?type=5">返回列表</a>';
            $plavalue=strip_tags($kv);
            if ($kv=="%文章评论%"){
                $iif=1;
                $st="文章评论列表";
            }else if($kv=="%公告评论%"){
                $iif=2;
                $st="公告评论列表";
            }else{
                $iif=0;
                $plavaluex = '%' . mysqli_real_escape_string($conn, $plavalue) . '%';
                $where="where plbigtext LIKE '$plavaluex'";
                $st="评论搜索";
            }            
    }

    if (is_null($uuid)||empty($uuid)||!is_numeric($uuid)){

    echo '
    <div class="user-h1">'.$st.'
        <form id="listformso" method="post" action="popingzi.php?type=5">
        <input type="text" name="uscom" placeholder="搜索关键词 或 筛选(%文章评论%、%公告评论%)" value="'.$plavalue.'" />
        <button type="submit">搜索</button>'.$s.'
        </form>
    </div>
    ';

       

if ($iif==1){
     // SQL 查询语句
     $sqlll = "SELECT plid FROM ppz_commentary";
}else if($iif==2){
     // SQL 查询语句
     $sqlll = "SELECT plid FROM ppz_ggcommentary";
}else{
     // SQL 查询语句
     $sqlll = "SELECT plid FROM ppz_commentary $where
     UNION ALL
     SELECT plid FROM ppz_ggcommentary $where
     ";
} 

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
            $pageindex='<a class="page-button nocopy" href="?type=5'.$k.'">首页</a>';//首页按钮
        }
        
        if ($p==$total_pages){
            $pagebody='<a class="page-no-button nocopy" >尾页</a>';
        }else{
            $pagebody='<a class="page-button nocopy" href="?type=5&p='.$total_pages.$k.'">尾页</a>';
        }
        
        if ($total_pages>1&&$p<$total_pages){
            $exit=$p+1;
            $pageexit='<a class="page-button nocopy" href="?type=5&p='.$exit.$k.'">下一页</a>';
        }else{
            $pageexit='<a class="page-no-button nocopy" >下一页</a>';
        }
        
        if ($p<=$total_pages&&$p>1){
            $exitup=$p-1;
            $pageup='<a class="page-button nocopy" href="?type=5&p='.$exitup.$k.'">上一页</a>';
        }else{
            $pageup='<a class="page-no-button nocopy" >上一页</a>';
        }

if ($iif==1){
    $rsql = "SELECT plid AS plid, plbigtext AS plbigtext, plip AS plip, pltime AS pltime, plrowid AS plrowid,pladmin AS pladmin,pltop AS pltop,1 AS plif FROM ppz_commentary
    ORDER BY pltime desc
    LIMIT $start_from, $num_rec_per_page";
}else if($iif==2){
    $rsql = "SELECT plid AS plid, plbigtext AS plbigtext, plip AS plip, pltime AS pltime, plrowid AS plrowid,pladmin AS pladmin,pltop AS pltop,2 AS plif FROM ppz_ggcommentary
    ORDER BY pltime desc
    LIMIT $start_from, $num_rec_per_page";
}else{
    $rsql = "SELECT plid AS plid, plbigtext AS plbigtext, plip AS plip, pltime AS pltime, plrowid AS plrowid,pladmin AS pladmin,pltop AS pltop,1 AS plif FROM ppz_commentary $where
    UNION ALL
    SELECT plid AS plid, plbigtext AS plbigtext, plip AS plip, pltime AS pltime, plrowid AS plrowid,pladmin AS pladmin,pltop AS pltop,2 AS plif FROM ppz_ggcommentary $where
    ORDER BY pltime desc
    LIMIT $start_from, $num_rec_per_page";//获取数据库表
}
if ($iif==1||$iif==2){
    $newth='
    <th width="8%">选择</th>
    <th width="30%">评论</th>
    <th width="15%">发布</th>
    <th width="15%">IP地址</th>
    <th width="22%">时间</th>
    <th width="10%">回复</th>
    <th width="10%">操作</th>
    ';
    $colspan=7;
    $newspan='<a id="allcheckbox">全选/全不选</a><a id="allcheckboxdel">批量删除</a>';
}else{
    $newth='
    <th width="38%">评论</th>
    <th width="15%">发布</th>
    <th width="15%">IP地址</th>
    <th width="22%">时间</th>
    <th width="10%">回复</th>
    <th width="10%">操作</th>
    ';
    $colspan=6;
    $newspan='';
}       
        $rretval=mysqli_query($conn,$rsql);
        if(mysqli_num_rows($rretval) < 1){ 
            echo '<div class="adminrownull">什么也没有~</div>';
        }else{
            echo '<div class="regtxt-row">
                    <table class="regtxt-table">
                        <thead>
                          <tr>
                            '.$newth.'
                          </tr>
                        </thead>
                        <tfoot>
                          <tr>
                            <td colspan="'.$colspan.'">
                                <div class="clear">
                                  <span class="page-left">'.$newspan.'第'.$p.'页（共'.$total_pages.'页）- 共计：'.$total_recordsll.'条评论</span>
                                  <span class="page-right">
                                  '.$pageindex.$pageup.$pageexit.$pagebody.'
                                  </span>
                              </div></td>
                          </tr>
                        </tfoot>
                        <tbody>            
                    ';
                    while($listr = mysqli_fetch_array($rretval)){
                        $id=$listr["plid"];//id
                        $plbigtext=$listr["plbigtext"];//内容
                        $plip=$listr["plip"];//ip
                        $pltime=$listr["pltime"];//评论时间
                        $plrowid=$listr["plrowid"];//评论来源
                        $plid=$listr["pladmin"];//评论者id
                        $pltop=$listr["pltop"];//点赞者数组
                        $plif=$listr["plif"];

                        if ($plif==1){
                            $pliftext="";
                            $eye="href='?type=5&sid=$id'";
                            $name="dpl";
                        }else if($plif==2){
                            $pliftext="来自公告";
                            $eye="href='?type=5&sid=$id&if=2'";
                            $name="dgg";
                        }else{
                            $pliftext="";
                            $eye="href='?type=5&sid=$id'";
                            $name="dpl";
                        }

                        $pltop=explode("|",$pltop);
                        $pltop=array_filter($pltop);
                        $pltop=count($pltop);
    
                        if (empty($pltop)){
                            $pltop=0;
                        }

                        //获取评论者信息
                        $sql_user = "SELECT * FROM ppz_newusername WHERE uid='$plid'";
                        $retval_user=mysqli_query($conn,$sql_user);
                        //判断评论者是否存在
                        if(mysqli_num_rows($retval_user) < 1){ 
                            $plidx = "<span style='color:red;'>用户不存在</span>";
                        }else{
                            // 输出数据
                            while($row_user = mysqli_fetch_array($retval_user)){
                                $plidx=$row_user["uname"];//昵称
                            }
                        }
                        

if ($plif==2){
                        //获取公告回复数量
                        $sql_hfsize = "SELECT * FROM ppz_ggreply WHERE repplid='$id'";
                        $retval_hfsize=mysqli_query($conn,$sql_hfsize);
                        $hfsize=mysqli_num_rows($retval_hfsize);
                        if ($hfsize<=0){
                            $hfsize="0";
                        }else{
                            $hfsize=$hfsize;
                        }
}else{
                        //获取回复数量
                        $sql_hfsize = "SELECT * FROM ppz_reply WHERE repplid='$id'";
                        $retval_hfsize=mysqli_query($conn,$sql_hfsize);
                        $hfsize=mysqli_num_rows($retval_hfsize);
                        if ($hfsize<=0){
                            $hfsize="0";
                        }else{
                            $hfsize=$hfsize;
                        }
}


if ($hfsize>999999){
    $hfsizex="100W+";
}else{
    $hfsizex=$hfsize;
}

if ($plip=="127.0.0.1"||$plip=="::1"){
    $newplip="本机";
}else{
    $newplip='<a href="https://www.ipshudi.com/'.$plip.'" target="_blank">'.$plip.'</a>';
}

if ($iif==1){
    $newtd='<td><input type="checkbox" name="allid" value="'.$id.' "></td>';
    $newjs='<script src="/style/js/checkboxcomment.js" type="text/javascript"></script>';
}elseif ($iif==2){
    $newtd='<td><input type="checkbox" name="allid" value="'.$id.' "></td>';
    $newjs='<script src="/style/js/checkboxggcomment.js" type="text/javascript"></script>';
}else{
    $newtd="";
    $newjs="";
}
                            echo'
                            <tr class="alt-row"> 
                            '.$newtd.'
                            <td style="max-width:200px;"><a data-plif="'.$plif.'" data-rowid="'.$plrowid.'" data-txt="'.$plbigtext.'" data-top="'.$pltop.'" data-admin="'.$plidx.'" data-hf="'.$hfsize.'" title="'.$pliftext.'" class="eyeplal">'.$plbigtext.'</a></td>
                            <td style="max-width:60px;"><a href="/user.php?id='.$plid.'" target="_blank">'.$plidx.'</a></td>
                            <td style="max-width:90px;">'.$newplip.'</td> 
                            <td style="max-width:130px;">'.$pltime.'</td>
                            <td style="max-width:40px;">'.$hfsizex.'</td>
                            <td><a '.$eye.' title="编辑"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> <a class="'.$name.'" data-d='.$id.' title="删除"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                            </tr>
                            ';
                    }
                    echo '
                    </tbody>
                    </table>
                    </div>
                    <dialog id="eyescomment" style="max-width:500px;min-width: 300px;">
                    <a id="eyescommentclose"><i class="fa fa-times" aria-hidden="true"></i></a>
                    <b>评论详情<a title="来源" id="commlink" target="_blank"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></a></b>
                    <div id="eyescommenttext"></div>
                        <div class="eyescommentsize">
                        <div class="eyescommentico" title="发布者"><i class="fa fa-user-circle" aria-hidden="true"></i><span id="commadmin"></span></div>
                        <div class="eyescommentico" title="回复数"><i class="fa fa-comments" aria-hidden="true"></i><span id="commhf"></span></div>
                        <div class="eyescommentico" title="点赞数"><i class="fa fa-thumbs-up" aria-hidden="true"></i><span id="commtop"></span></div>                       
                        </div>
                    </dialog>
                    <script src="/style/js/commentsye.js" type="text/javascript"></script>
                    '.$newjs.'
                    ';

        }

    }else{
        echo '<div class="user-h1">编辑评论：<a href="?type=5"><i class="fa fa-angle-double-left" aria-hidden="true"></i>返回列表</a></div>';
            //判断uuid是否是正整数
            if (ctype_digit($uuid) && $uuid > 0) {
                if ($uif==2){
                    $consql = "SELECT * FROM ppz_ggcommentary WHERE plid='$uuid'";
                }else{
                    $uif=1;
                    $consql = "SELECT * FROM ppz_commentary WHERE plid='$uuid'";
                }
                $conretval=mysqli_query($conn,$consql);
                if(mysqli_num_rows($conretval) < 1){
                    echo '<div class="adminrownull">评论不存在~</div>';
                }else{
                    while($row_con = mysqli_fetch_array($conretval)){
                        $pv=$row_con["plbigtext"];
                        $pid=$row_con["plid"];
                    }
                    $pvsize=mb_strlen($pv,'UTF8');//获取pv中的字符数
                    echo '<div class="regtxt-row flexcolumn">
                            <textarea name="plv" id="plv">'.$pv.'</textarea>
                            <div class="plv-foot flexspace-between">
                            <span>字(符)数：<span id="pvsize">'.$pvsize.'</span></span>
                            <a id="conpost" data-pif="'.$uif.'" data-pid="'.$pid.'">修改</a>
                            </div>';

                          $pagesize=10;//每页显示的回复数量
                          $page=$_GET['p'];//获取页码
                          if(is_null($page)||$page<1||!is_numeric($page)||!ctype_digit($page)){
                              $page=1;
                          }else{
                              $page=intval($page);//转为整数
                          }
                          $hfcfcfxstart=($page-1)*$pagesize;
                          if ($uif==2){
                                $sql_hfcfx = "SELECT * FROM ppz_ggreply WHERE repplid='$pid'";
                            }else{
                                $sql_hfcfx = "SELECT * FROM ppz_reply WHERE repplid='$pid'";
                            }
                            $retval_hfcfx=mysqli_query($conn,$sql_hfcfx);
                            $hfcfcfxsize=mysqli_num_rows($retval_hfcfx);
                            if ($hfcfcfxsize>0){
                                $hfcfcfxpagesize=ceil($hfcfcfxsize/$pagesize);//计算总页数
                                $textalldel='<div id="delallrep" data-if="'.$uif.'" data-all="'.$uuid.'">清空全部回复</div>';
                            }else{
                                $hfcfcfxpagesize=1;
                                $textalldel="";
                            }

                            // 获取回复内容
                            if ($uif==2){
                                $classif="ggcondel";
                                $sql_hfcf = "SELECT * FROM ppz_ggreply WHERE repplid='$pid' order by repid DESC LIMIT $hfcfcfxstart,$pagesize";
                            }else{
                                $classif="plcondel";
                                $sql_hfcf = "SELECT * FROM ppz_reply WHERE repplid='$pid' order by repid DESC LIMIT $hfcfcfxstart,$pagesize";
                            }

                            $retval_hfcf=mysqli_query($conn,$sql_hfcf);
                            $hfcfsize=mysqli_num_rows($retval_hfcf);


                            echo '<div id="ahf" class="conhf">回复('.$hfcfcfxsize.')：</div>
                            <ul class="conhfv flexcolumn">';
                            if ($hfcfsize>0){
                                while($row_hfcf = mysqli_fetch_array($retval_hfcf)){
                                    $hfcfid=$row_hfcf["repid"];//回复id
                                    $hfcftext=$row_hfcf["reptext"];//回复内容
                                    $hfcfadmin=$row_hfcf["repadmin"];//回复人id
                                    $hfcftime=$row_hfcf["reptime"];//回复时间
                                    $hfcfip=$row_hfcf["repip"];//回复ip
                                    //获取回复人信息
                                    $sql_hfcfadmin="SELECT * FROM ppz_newusername WHERE uid='$hfcfadmin'";
                                    $retval_hfcfadmin=mysqli_query($conn,$sql_hfcfadmin);
                                    //判断回复人是否存在
                                    if (mysqli_num_rows($retval_hfcfadmin) > 0) {
                                        while($row_hfcfadmin = mysqli_fetch_array($retval_hfcfadmin)){
                                            $hfcfadminname=$row_hfcfadmin["uname"];//回复人昵称
                                            $hfcfadmin=$row_hfcfadmin["uid"];//回复人id
                                            $hfcfadminimg=$row_hfcfadmin["uimg"];//回复人头像
                                        }
                                    }else{
                                        $hfcfadminname="<span style='color:red'>*用户不存在*</span>";
                                        $hfcfadmin=0;
                                        $hfcfadminimg="";
                                    }
                                    if (is_null($hfcfadminimg)||$hfcfadminimg==""){
                                        $hfcfadminimgx="/images/web/default.jpg";
                                    }else{
                                        $hfcfadminimgx=$hfcfadminimg;
                                    }

                                    echo '<li class="flexspace-between"><span data-time="'.$hfcftime.'" data-ip="'.$hfcfip.'" data-con="'.$hfcftext.'" data-name="'.$hfcfadminname.'" data-aid="'.$hfcfadmin.'" class="flexspace-between-text"><img class="conimg" src="'.$hfcfadminimgx.'" />'.$hfcfadminname.'：'.$hfcftext.'</span><a class="'.$classif.'" data-hfdid="'.$hfcfid.'"><i class="fa fa-trash"></i></a></li>';
                                }
                            }else{
                                echo '<div class="conhfnull">暂无回复</div>';
                            }

                            if($uif==2){
                                $uifurl="?type=5&sid=".$uuid."&if=2";
                                $exiturl="/anctshow.php?id=".$uuid."";
                            }else{
                                $uifurl="?type=5&sid=".$uuid."";
                                $exiturl="/show.php?id=".$uuid."";
                            }

                            if ($page<=1){
                                $uppagetext="conupno";
                                $uppageurl="";
                            }else{
                                $uppagetext="conup";
                                $uppageurl=$page-1;
                            }

                            if ($page>=$hfcfcfxpagesize){
                                $bupagetext="conupno";
                                $bupageurl="";
                            }else{
                                $bupagetext="conup";
                                $bupageurl=$page+1;
                            }

                            echo '
                            </ul>
                            <div class="pagecon flexspace-between">
                            <span class="pageconall">第'.$page.'页 / 共'.$hfcfcfxpagesize.'页 '.$textalldel.'<a id="quickly">快速回复</a></span>
                            <div class="condiv">
                            <a class="'.$uppagetext.'" href="'.$uifurl.'&p=1#ahf">首页</a>
                            <a class="'.$uppagetext.'" href="'.$uifurl.'&p='.$uppageurl.'#ahf">上一页</a>
                            <a class="'.$bupagetext.'" href="'.$uifurl.'&p='.$bupageurl.'#ahf">下一页</a>
                            <a class="'.$bupagetext.'" href="'.$uifurl.'&p='.$hfcfcfxpagesize.'#ahf">尾页</a>
                            </div>
                            </div>
                        </div>

                    <dialog id="eyescon" style="max-width: 500px; min-width: 300px;">
                    <a id="eyesconclose"><i class="fa fa-times" aria-hidden="true"></i></a>
                    <b>回复详情</b>
                    <div id="eyescontext"></div>
                        <span class="timeandip"><span id="datehftime"></span><a id="datehfip" target="_blank"></a></span>
                        <a id="conadmintext" target="_blank"></a>
                    </dialog>
                    <dialog id="eyequickly" style="width: 350px;">
                    <a id="eyequicklyclose"><i class="fa fa-times" aria-hidden="true"></i></a>
                    <b>快速回复</b>
                    <input maxlength="90" type="text" id="quicklytext" placeholder="请输入回复内容" />
                    <a id="quicklybtn" data-tif="'.$uif.'" data-repid="'.$uuid.'">回复</a>
                    <span id="quicklytextnull"></span>
                    </dialog>
                    <script src="/style/js/conns.js" type="text/javascript"></script>
                    ';
                }
                
            }else{
                echo '<div class="adminrownull">错误参数~</div>';
            }



    }


}else{
    echo "请勿胡搞！";
}
?>