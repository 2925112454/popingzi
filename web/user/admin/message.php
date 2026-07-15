<?php
if ($admin==1 && $typeuser==6 && ($allvip==4||$allvip==3||$allvip==2)  && !empty($ppzusername)){

if (!isset($_POST["somess"])){
    $_POST["somess"]="";
}
if (!isset($_GET["k"])){ 
    $_GET["k"]="";
}
if (!isset($_GET["p"])){ 
    $_GET["p"]="";
}
    $so=trim($_POST["somess"]);
    $kv=trim($_GET["k"]);
    $num_rec_per_page=20;// 每页显示数量
    $getp=trim($_GET["p"]);//获取GET传参P
    /*判断参数P是否为空，且是否是数字*/
    if (isset($getp) && is_numeric($getp) && $getp>=1 && !empty($getp)){ 
    $pa = $getp;
    } else { 
    $pa=1; 
    }; 
    if (empty($kv)||!isset($kv)){
        if(empty($so)||!isset($so)){
            $s="";
            $plavalue="";
            $where="";
            $st="私信列表";
            $iif=0;
        }else{
            $s='<a href="popingzi.php?type=6">返回列表</a>';
            $plavalue=strip_tags($so);
                if ($so=="%私信%"){
                    $iif=1;
                    $st="私信列表";
                }else if($so=="%通知%"){
                    $iif=2;
                    $st="通知列表";
                }else if($so=="%未读%"){
                    $iif=3;
                    $st="未读私信";
                }else if($so=="%已读%"){
                    $iif=4;
                    $st="已读私信";
                }else{
                    $iif=0;
                    $plavaluex = '%' . mysqli_real_escape_string($conn, $plavalue) . '%';
                    $where="where tertext LIKE '$plavaluex'";
                    $st="私信搜索";
                }
            
        }
    }else{
            $s='<a href="popingzi.php?type=6">返回列表</a>';
            $plavalue=strip_tags($kv);
            if ($kv=="%私信%"){
                $iif=1;
                $st="私信列表";
            }else if($kv=="%通知%"){
                $iif=2;
                $st="通知列表";
            }else if($kv=="%未读%"){
                $iif=3;
                $st="未读私信";
            }else if($kv=="%已读%"){
                $iif=4;
                $st="已读私信";
            }else{
                $iif=0;
                $plavaluex = '%' . mysqli_real_escape_string($conn, $plavalue) . '%';
                $where="where tertext LIKE '$plavaluex'";
                $st="私信搜索";
            }            
    }


    echo '
    <div class="user-h1">'.$st.'
        <form id="listformso" method="post" action="popingzi.php?type=6">
        <input type="text" name="somess" placeholder="搜索关键词 或 筛选(%私信%、%通知%、%已读%、%未读%)" value="'.$plavalue.'" />
        <button type="submit">搜索</button>'.$s.'
        </form>
    </div>
    ';

    if ($iif==1){
        $sqlll = "SELECT * FROM ppz_letter where teruser != 0 and teruser is not null"; // 私信类
   }else if($iif==2){
        $sqlll = "SELECT * FROM ppz_letter where teruser=0";//通知类
   }else if($iif==3){
    $sqlll = "SELECT * FROM ppz_letter where teryes=0 and teruser!= 0";//未读私信
    }else if($iif==4){
        $sqlll = "SELECT * FROM ppz_letter where teryes=1 and teruser!= 0";//未读私信
        }else{
        $sqlll = "SELECT * FROM ppz_letter " .$where;//搜索类
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
       $pageindex='<a class="page-button nocopy" href="?type=6'.$k.'">首页</a>';//首页按钮
   }
   
   if ($p==$total_pages){
       $pagebody='<a class="page-no-button nocopy" >尾页</a>';
   }else{
       $pagebody='<a class="page-button nocopy" href="?type=6&p='.$total_pages.$k.'">尾页</a>';
   }
   
   if ($total_pages>1&&$p<$total_pages){
       $exit=$p+1;
       $pageexit='<a class="page-button nocopy" href="?type=6&p='.$exit.$k.'">下一页</a>';
   }else{
       $pageexit='<a class="page-no-button nocopy" >下一页</a>';
   }
   
   if ($p<=$total_pages&&$p>1){
       $exitup=$p-1;
       $pageup='<a class="page-button nocopy" href="?type=6&p='.$exitup.$k.'">上一页</a>';
   }else{
       $pageup='<a class="page-no-button nocopy" >上一页</a>';
   }

                $rsql = "$sqlll ORDER BY terid desc LIMIT $start_from, $num_rec_per_page";//获取数据库表
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
                            <th width="20%">内容</th>
                            <th width="14%">发送者</th>
                            <th width="20%">接收者</th>
                            <th width="10%">时间</th>
                            <th width="10%">IP</th>
                            <th width="10%">操作</th>
                          </tr>
                        </thead>
                        <tfoot>
                          <tr>
                            <td colspan="8">
                                <div class="clear">
                                  <span class="page-left"><a id="allcheckbox">全选/全不选</a><a id="delallmess">批量删除</a>第'.$p.'页（共'.$total_pages.'页）- 共计：'.$total_recordsll.'条记录</span>
                                  <span class="page-right">
                                  '.$pageindex.$pageup.$pageexit.$pagebody.'
                                  </span>
                              </div></td>
                          </tr>
                        </tfoot>
                        <tbody>            
                    ';
                    while($listr = mysqli_fetch_array($rretval)){
                        $id=$listr["terid"];//id
                        $uname=$listr["tertext"];//内容
                        $uusername=$listr["teradmin"];//发送者
                        $uemail=$listr["teruser"];//接收者
                        $utime=$listr["tertime"];//时间
                        $uviptime=$listr["terip"];//IP
                        $yes=$listr["teryes"];//状态
                        if($yes==1){
                            $yes="<span class='yes'>已读</span>";
                        }else if($yes==0){
                            $yes="<span class='no'>未读</span>";
                        }else{
                            $yes="<span class='no'>未读</span>";
                        }

                        if ($uemail==0||$uemail==""||is_null($uemail)||empty($uemail)){
                            $uemail="<span style='color:#8bc34a'>全员通知</span>";
                        }else{
                            $shousql = "select * from ppz_newusername where uid = $uemail";
                            $shoretval=mysqli_query($conn,$shousql);
                            //判断会员是否存在
                            if (mysqli_num_rows($shoretval) !== 1){ 
                                $uemail="<span style='color:red'>账号异常</span>";
                            }else{
                                while($shor = mysqli_fetch_array($shoretval)){
                                    $uemail=$shor["uname"];
                                    $uemail="<a href='/user.php?id=".$shor["uid"]."' target='_blank'>$uemail</a>";
                                }
                            }
                        }

                        if (is_null($uusername)||empty($uusername)){
                            $uusername="<span style='color:red'>账号异常</span>";
                        }else{
                            $fasql = "select * from ppz_newusername where uid = $uusername";
                            $faretval=mysqli_query($conn,$fasql);
                            if (mysqli_num_rows($faretval) !== 1){ 
                                $uusername="<span style='color:red'>账号异常</span>";
                            }else{
                                while($far = mysqli_fetch_array($faretval)){
                                    $uusername=$far["uname"];
                                    $uusername="<a href='/user.php?id=".$far["uid"]."' target='_blank'>$uusername</a>";
                                }
                            }
                        }

                        if (empty($uviptime)){
                            $uviptime="<span style='color:red'>未知IP</span>";
                        }else{
                            if ($uviptime=="127.0.0.1"||$uviptime=="::1"||$uviptime=="0.0.0.0"||$uviptime=="localhost"||$uviptime=="localhost:8080"||$uviptime=="127"){
                                $uviptime="保留地址";
                            }else{
                                $uviptime='<a href="https://www.ipshudi.com/'.$uviptime.'" target="_blank">'.$uviptime.'</a>';
                            }
                        }
                 
                            echo'
                            <tr class="alt-row"> 
                            <td><input type="checkbox" name="id" value="'.$id.'">
                            </td>
                            <td style="max-width:40px;">'.$yes.'</td>
                            <td style="max-width:100px;"><a id="editnewid'.$id.'" data-eye="'.$uname.'" class="eyemess">'.$uname.'</a></td>
                            <td style="max-width:60px;">'.$uusername.'</td>
                            <td style="max-width:90px;">'.$uemail.'</td> 
                            <td style="max-width:130px;">'.$utime.'</td>
                            <td style="max-width:130px;">'.$uviptime.'</td>
                            <td><a class="editmess" data-t="'.$uname.'" data-i="'.$id.'" title="编辑"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> <a class="delmess" data-d='.$id.' title="删除"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                            </tr>
                            ';
                    }
                    echo '
                    </tbody>
                    </table>
                    </div>
                    <dialog id="eyescon" style="max-width: 500px; min-width: 300px;">
                    <a id="eyesconclose"><i class="fa fa-times" aria-hidden="true"></i></a>
                    <b>私信详情</b>
                    <div id="eyescontext"></div>
                    </dialog>
                    <dialog id="navfldialog"><a id="navfldialogclose"><i class="fa fa-times" aria-hidden="true"></i></a><b>修改私信</b><textarea id="navfldialogtextarea" placeholder="请输入私信内容"></textarea><button id="navfldialogbut" data-yid="">确定</button><span id="navfldialogerr"></span></dialog>
                    <script src="/style/js/messedit.js" type="text/javascript"></script>
                    ';
                }

}else{
    echo "请勿胡搞！";
}
?>