<?php
if(!isset($ADS)){
    $ASD=0;
}
if(!isset($indexads)){
    $indexads="";
}
if(!isset($newtime)||is_null($newtime)){
    $newtime=0;
}
if(!isset($ADSPAGE)||empty($ADSPAGE)){
    $ADSPAGE=[];
}
if($ADS==200){

    $adson_hf="";//横幅广告位
    $adson_rowhf="";//内容页横幅广告位
    $adson_yxj="";//右下角弹窗
    $adson_ybl="";//右边栏广告
    $adson_left="";//左侧悬浮广告
    $adson_right="";//右侧悬浮广告
    $adson_js="";//自定义js广告

    /* 横幅广告位 */
    $alladssetsql_hf="SELECT * FROM `ppz_ads` WHERE `aid`=1";
    $alladssetresult_hf=mysqli_query($conn,$alladssetsql_hf);
    if(mysqli_num_rows($alladssetresult_hf)==1){
        while($alladssetrow_hf=mysqli_fetch_assoc($alladssetresult_hf)){
            $hf_adsimg=$alladssetrow_hf['aimg'];
            $hf_adsurl=$alladssetrow_hf['aurl'];
            $hf_off=$alladssetrow_hf['ayes'];
            $hf_vip=$alladssetrow_hf['avip'];//vip会员免广告，0为不免，1为免
            $hf_ip=$alladssetrow_hf['aeye'];//展示区域，1为首页，2为列表页，3为内容页，4为搜索页
            $hf_time=$alladssetrow_hf['atime'];//广告有效期
        }
        if($hf_off==1&&!empty($hf_adsimg)&&!empty($hf_ip)){
            $hf_ip_arr=explode(",",$hf_ip);
            // 判断当前页面是否在展示区域中
            if(in_array($ADSPAGE,$hf_ip_arr)){
                //判断是否开启了vip免广告
                if($hf_vip==1&&($newtime>0)){
                    $adson_hf="";
                }else{
                    $hf_time=strtotime($hf_time);//转换为时间戳
                    //  判断广告有效期
                    if(time()>$hf_time){
                        $adson_hf="";
                    }else{
                        $hf_adsimg=htmlspecialchars_decode($hf_adsimg);
                        //判断$hf_adsimg是否是js引入文件，比如“<script src="/upload/ads-images/ads.js" type="text/javascript"></script>”
                        if(preg_match('/^<script.*?>.*?<\/script>$/', $hf_adsimg)) {
                            $adson_hf = '<div class="indexdas adsclass">'.$hf_adsimg.'</div>';
                        }else{
                            $hf_adsimg=htmlspecialchars($hf_adsimg);
                            $adson_hf='<div class="indexdas adsclass" style="background-image:url('.$hf_adsimg.');"><a href="'.$hf_adsurl.'" target="_blank"><div class="adstexe">ADS</div></a></div>';
                        }

                    }
                }
            }            
        }else{
            $adson_hf="";
        }
    }
    /* 内容页横幅广告位 */
    $alladssetsql_rowhf="SELECT * FROM `ppz_ads` WHERE `aid`=2";
    $alladssetresult_rowhf=mysqli_query($conn,$alladssetsql_rowhf);
    if(mysqli_num_rows($alladssetresult_rowhf)==1){
        while($alladssetrow_rowhf=mysqli_fetch_assoc($alladssetresult_rowhf)){
            $rowhf_adsimg=$alladssetrow_rowhf['aimg'];
            $rowhf_adsurl=$alladssetrow_rowhf['aurl'];
            $rowhf_off=$alladssetrow_rowhf['ayes'];
            $rowhf_vip=$alladssetrow_rowhf['avip'];//vip会员免广告，0为不免，1为免
            $rowhf_ip=$alladssetrow_rowhf['aeye'];//展示区域，3为内容页
            $rowhf_time=$alladssetrow_rowhf['atime'];//广告有效期
        }
        if($rowhf_off==1&&!empty($rowhf_adsimg)&&!empty($rowhf_ip)){
            $rowhf_ip_arr=explode(",",$rowhf_ip);
            // 判断当前页面是否在展示区域中
            if(in_array($ADSPAGE,$rowhf_ip_arr)){
                //判断是否开启了vip免广告
                if($rowhf_vip==1&&($newtime>0)){
                    $adson_rowhf="";
                }else{
                    $rowhf_time=strtotime($rowhf_time);//转换为时间戳
                    //  判断广告有效期
                    if(time()>$rowhf_time){
                        $adson_rowhf="";
                    }else{
                        $rowhf_adsimg=htmlspecialchars_decode($rowhf_adsimg);
                        //判断$rowhf_adsimg是否是js引入文件，比如“<script src="/upload/ads-images/ads.js" type="text/javascript"></script>”
                        if(preg_match('/^<script.*?>.*?<\/script>$/', $rowhf_adsimg)) {
                            $adson_rowhf = '<div class="rowhfdas adsclass">'.$rowhf_adsimg.'</div>';
                        }else{
                            $rowhf_adsimg=htmlspecialchars($rowhf_adsimg);
                            $adson_rowhf='<div class="rowhfdas adsclass" style="background-image:url('.$rowhf_adsimg.');"><a href="'.$rowhf_adsurl.'" target="_blank"><div class="adstexe">ADS</div></a></div>';
                        }

                    }
                }
            }            
        }else{
            $adson_rowhf="";
        }
    }
    /* 右下角广告位 */
    $alladssetsql_yxj="SELECT * FROM `ppz_ads` WHERE `aid`=3";
    $alladssetresult_yxj=mysqli_query($conn,$alladssetsql_yxj);
    if(mysqli_num_rows($alladssetresult_yxj)==1){
        while($alladssetrow_yxj=mysqli_fetch_assoc($alladssetresult_yxj)){
            $yxj_adsimg=$alladssetrow_yxj['aimg'];
            $yxj_adsurl=$alladssetrow_yxj['aurl'];
            $yxj_off=$alladssetrow_yxj['ayes'];
            $yxj_vip=$alladssetrow_yxj['avip'];//vip会员免广告，0为不免，1为免
            $yxj_ip=$alladssetrow_yxj['aeye'];//展示区域，1为首页，2为列表页，3为内容页，4为搜索页
            $yxj_time=$alladssetrow_yxj['atime'];//广告有效期
        }
        if($yxj_off==1&&!empty($yxj_adsimg)&&!empty($yxj_ip)){
            $yxj_ip_arr=explode(",",$yxj_ip);
            // 判断当前页面是否在展示区域中
            if(in_array($ADSPAGE,$yxj_ip_arr)){
                //判断是否开启了vip免广告
                if($yxj_vip==1&&($newtime>0)){
                    $adson_yxj="";
                }else{
                    $yxj_time=strtotime($yxj_time);//转换为时间戳
                    //  判断广告有效期
                    if(time()>$yxj_time){
                        $adson_yxj="";
                    }else{
                        $yxj_adsimg=htmlspecialchars_decode($yxj_adsimg);
                        //判断$yxj_adsimg是否是js引入文件，比如“<script src="/upload/ads-images/ads.js" type="text/javascript"></script>”
                        if(preg_match('/^<script.*?>.*?<\/script>$/', $yxj_adsimg)) {
                            $adson_yxj = '<div class="yxjdasdiv" id="yxjdasdiv"><a id="adsclose"><i class="fa fa-times" aria-hidden="true"></i></a>'.$yxj_adsimg.'</div><script src="/style/js/adsyxj.js" type="text/javascript"></script>';
                        }else{
                            $yxj_adsimg=htmlspecialchars($yxj_adsimg);
                            $adson_yxj='<div class="yxjdasdiv" id="yxjdasdiv"><a id="adsclose"><i class="fa fa-times" aria-hidden="true"></i></a><div class="yxjdas adsclass" style="background-image:url('.$yxj_adsimg.');"><a href="'.$yxj_adsurl.'" target="_blank"><div class="adstexe">ADS</div></a></div></div><script src="/style/js/adsyxj.js" type="text/javascript"></script>';
                        }

                    }
                }
            }            
        }else{
            $adson_yxj="";
        }
    }
    /* 右边栏广告位 */
    $alladssetsql_ybl="SELECT * FROM `ppz_ads` WHERE `aid`=4";
    $alladssetresult_ybl=mysqli_query($conn,$alladssetsql_ybl);
    if(mysqli_num_rows($alladssetresult_ybl)==1){
        while($alladssetrow_ybl=mysqli_fetch_assoc($alladssetresult_ybl)){
            $ybl_adsimg=$alladssetrow_ybl['aimg'];
            $ybl_adsurl=$alladssetrow_ybl['aurl'];
            $ybl_off=$alladssetrow_ybl['ayes'];
            $ybl_vip=$alladssetrow_ybl['avip'];//vip会员免广告，0为不免，1为免
            $ybl_ip=$alladssetrow_ybl['aeye'];//展示区域，1为首页，2为列表页，3为内容页，4为搜索页
            $ybl_time=$alladssetrow_ybl['atime'];//广告有效期
        }
        if($ybl_off==1&&!empty($ybl_adsimg)&&!empty($ybl_ip)){
            $ybl_ip_arr=explode(",",$ybl_ip);
            // 判断当前页面是否在展示区域中
            if(in_array($ADSPAGE,$ybl_ip_arr)){
                //判断是否开启了vip免广告
                if($ybl_vip==1&&($newtime>0)){
                    $adson_ybl="";
                }else{
                    $ybl_time=strtotime($ybl_time);//转换为时间戳
                    //  判断广告有效期
                    if(time()>$ybl_time){
                        $adson_ybl="";
                    }else{
                        $ybl_adsimg=htmlspecialchars_decode($ybl_adsimg);
                        //判断$ybl_adsimg是否是js引入文件，比如“<script src="/upload/ads-images/ads.js" type="text/javascript"></script>”
                        if(preg_match('/^<script.*?>.*?<\/script>$/', $ybl_adsimg)) {
                            $adson_ybl = '<div class="body-right-top"><div class="hot-title">赞助广告</div><div class="hot-img">'.$ybl_adsimg.'</div></div>';
                        }else{
                            $ybl_adsimg=htmlspecialchars($ybl_adsimg);
                            $adson_ybl='
                            <div class="body-right-top">
                                <div class="hot-title">赞助广告</div>
                                <div class="hot-img"><a href="'.$ybl_adsurl.'" target="_blank"><img src="'.$ybl_adsimg.'" /></a></div>
                            </div>
                            ';
                        }

                    }
                }
            }            
        }else{
            $adson_ybl="";
        }
    }
    /* 左侧悬浮广告位 */
    $alladssetsql_left="SELECT * FROM `ppz_ads` WHERE `aid`=5";
    $alladssetresult_left=mysqli_query($conn,$alladssetsql_left);
    if(mysqli_num_rows($alladssetresult_left)==1){
        while($alladssetrow_left=mysqli_fetch_assoc($alladssetresult_left)){
            $left_adsimg=$alladssetrow_left['aimg'];
            $left_adsurl=$alladssetrow_left['aurl'];
            $left_off=$alladssetrow_left['ayes'];
            $left_vip=$alladssetrow_left['avip'];//vip会员免广告，0为不免，1为免
            $left_ip=$alladssetrow_left['aeye'];//展示区域，1为首页，2为列表页，3为内容页，4为搜索页
            $left_time=$alladssetrow_left['atime'];//广告有效期
        }
        if($left_off==1&&!empty($left_adsimg)&&!empty($left_ip)){
            $left_ip_arr=explode(",",$left_ip);
            // 判断当前页面是否在展示区域中
            if(in_array($ADSPAGE,$left_ip_arr)){
                //判断是否开启了vip免广告
                if($left_vip==1&&($newtime>0)){
                    $adson_left="";
                }else{
                    $left_time=strtotime($left_time);//转换为时间戳
                    //  判断广告有效期
                    if(time()>$left_time){
                        $adson_left="";
                    }else{
                        $left_adsimg=htmlspecialchars_decode($left_adsimg);
                        //判断$left_adsimg是否是js引入文件，比如“<script src="/upload/ads-images/ads.js" type="text/javascript"></script>”
                        if(preg_match('/^<script.*?>.*?<\/script>$/', $left_adsimg)) {
                            $adson_left = '<div class="adslrzero" id="leftads"><a id="leftadsclose"><i class="fa fa-times" aria-hidden="true"></i></a><div class="leftdas">'.$left_adsimg.'</div></div><script src="/style/js/adsfloat.js" type="text/javascript"></script>';
                        }else{
                            $left_adsimg=htmlspecialchars($left_adsimg);
                            $adson_left='<div class="adslrzero" id="leftads"><a id="leftadsclose"><i class="fa fa-times" aria-hidden="true"></i></a><div class="leftdas adsclass" style="background-image:url('.$left_adsimg.');"><a href="'.$left_adsurl.'" target="_blank"><div class="adstexe">ADS</div></a></div></div><script src="/style/js/adsfloat.js" type="text/javascript"></script>';
                        }

                    }
                }
            }            
        }else{
            $adson_left="";
        }
    }
    /* 右侧悬浮广告位 */
    $alladssetsql_right="SELECT * FROM `ppz_ads` WHERE `aid`=6";
    $alladssetresult_right=mysqli_query($conn,$alladssetsql_right);
    if(mysqli_num_rows($alladssetresult_right)==1){
        while($alladssetrow_right=mysqli_fetch_assoc($alladssetresult_right)){
            $right_adsimg=$alladssetrow_right['aimg'];
            $right_adsurl=$alladssetrow_right['aurl'];
            $right_off=$alladssetrow_right['ayes'];
            $right_vip=$alladssetrow_right['avip'];//vip会员免广告，0为不免，1为免
            $right_ip=$alladssetrow_right['aeye'];//展示区域，1为首页，2为列表页，3为内容页，4为搜索页
            $right_time=$alladssetrow_right['atime'];//广告有效期
        }
        if($right_off==1&&!empty($right_adsimg)&&!empty($right_ip)){
            $right_ip_arr=explode(",",$right_ip);
            // 判断当前页面是否在展示区域中
            if(in_array($ADSPAGE,$right_ip_arr)){
                //判断是否开启了vip免广告
                if($right_vip==1&&($newtime>0)){
                    $adson_right="";
                }else{
                    $right_time=strtotime($right_time);//转换为时间戳
                    //  判断广告有效期
                    if(time()>$right_time){
                        $adson_right="";
                    }else{
                        $right_adsimg=htmlspecialchars_decode($right_adsimg);
                        //判断$right_adsimg是否是js引入文件，比如“<script src="/upload/ads-images/ads.js" type="text/javascript"></script>”
                        if(preg_match('/^<script.*?>.*?<\/script>$/', $right_adsimg)) {
                            $adson_right = '<div class="adslrzero" id="rightads"><a id="rightadsclose"><i class="fa fa-times" aria-hidden="true"></i></a><div class="rightdas adsclass">'.$right_adsimg.'</div></div><script src="/style/js/adsfloat.js" type="text/javascript"></script>';
                        }else{
                            $right_adsimg=htmlspecialchars($right_adsimg);
                            $adson_right='<div class="adslrzero" id="rightads"><a id="rightadsclose"><i class="fa fa-times" aria-hidden="true"></i></a><div class="rightdas adsclass" style="background-image:url('.$right_adsimg.');"><a href="'.$right_adsurl.'" target="_blank"><div class="adstexe">ADS</div></a></div></div><script src="/style/js/adsfloat.js" type="text/javascript"></script>';
                        }

                    }
                }
            }            
        }else{
            $adson_right="";
        }
    }
    /* 自定义JS广告位 */
    $alladssetsql_js="SELECT * FROM `ppz_ads` WHERE `aid`=7";
    $alladssetresult_js=mysqli_query($conn,$alladssetsql_js);
    if(mysqli_num_rows($alladssetresult_js)==1){
        while($alladssetrow_js=mysqli_fetch_assoc($alladssetresult_js)){
            $js_off=$alladssetrow_js['ayes'];
            $js_vip=$alladssetrow_js['avip'];//vip会员免广告，0为不免，1为免
            $js_ip=$alladssetrow_js['aeye'];//展示区域，1为首页，2为列表页，3为内容页，4为搜索页
            $js_time=$alladssetrow_js['atime'];//广告有效期
            $js_jscode=$alladssetrow_js['ajs'];//自定义js代码
        }
        if($js_off==1&&!empty($js_jscode)&&!empty($js_ip)){
            $js_ip_arr=explode(",",$js_ip);
            // 判断当前页面是否在展示区域中
            if(in_array($ADSPAGE,$js_ip_arr)){
                //判断是否开启了vip免广告
                if($js_vip==1&&($newtime>0)){
                    $adson_js="";
                }else{
                    $js_time=strtotime($js_time);//转换为时间戳
                    //  判断广告有效期
                    if(time()>$js_time){
                        $adson_js="";
                    }else{
                        if(preg_match('/^<script.*?>.*?<\/script>$/', $js_jscode)) {
                            $adson_js = '';
                        }else{
                            $adson_js='<script type="text/javascript">'.$js_jscode.'</script>';
                        }

                    }
                }
            }            
        }else{
            $adson_js="";
        }
    }
}else{
    header("HTTP/1.1 404 Not Found");
}
?>