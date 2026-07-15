<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
if (empty($ppzusername)){//判断是否登录
    echo 500;
}else{
    include __DIR__.'/conn.php';//链接数据库
    $rowsql = "select * from ppz_newusername where binary uusername = $ppzusername";//获取登录会员信息
    $rowretval=mysqli_query($conn,$rowsql);
    if(mysqli_num_rows($rowretval) !== 1){ 
        echo 500;
    }else{
                $query = $conn->query($rowsql);
                while($row = $query->fetch_array()){
                    $ustatus=$row['ustatus'];//身份，1普通会员，2为管理员，3为副站长，4为站长；此处为LOGO上传操作，仅限站长身份操作
                    $uid=$row['uid'];//会员ID
                }
                if ($ustatus==4||$ustatus==3||$ustatus==2){
                    if (!isset($_POST['tag'])){
                        $_POST['tag']="";
                    }
                    if (!isset($_POST['title'])){
                        $_POST['title']="";
                    }
                    if (!isset($_POST['img'])){
                        $_POST['img']="";
                    }
                    if (!isset($_POST['if'])){
                        $_POST['if']="";
                    }
                    if (!isset($_POST['cp'])){
                        $_POST['cp']="";
                    }
                    if (!isset($_POST['cpurl'])){
                        $_POST['cpurl']="";
                    }
                    if (!isset($_POST['vip'])){
                        $_POST['vip']="";
                    }
                    if (!isset($_POST['content'])){
                        $_POST['content']="";
                    }
                    if (!isset($_POST['top'])){
                        $_POST['top']="";
                    }
                    if (!isset($_POST['dow'])){
                        $_POST['dow']="";
                    }
                    if (!isset($_POST['downame'])){
                        $_POST['downame']="";
                    }
                    if (!isset($_POST['dowurl'])){
                        $_POST['dowurl']="";
                    }
                    if (!isset($_POST['dowpas'])){
                        $_POST['dowpas']="";
                    }
                    if (!isset($_POST['dowmun'])){
                        $_POST['dowmun']="";
                    }
                    if (!isset($_POST['dowsize'])){
                        $_POST['dowsize']="";
                    }
                    if (!isset($_POST['dowzip'])){
                        $_POST['dowzip']="";
                    }
                    if (!isset($_POST['dowpx'])){
                        $_POST['dowpx']="";
                    }
                    if (!isset($_POST['dowif'])){
                        $_POST['dowif']="";
                    }
                    $tag=trim(strip_tags($_POST['tag']));//标签
                    $title=trim(strip_tags($_POST['title']));//标题
                    $img=trim(str_replace(" ","%20",$_POST['img']));//封面图片
                    $ifx=trim($_POST['if']);//分类id
                    if (empty($ifx)||$ifx<1||!is_numeric($ifx)){
                        $if=0;
                    }else{
                        $if=$ifx;
                    }
                    $cp=trim(strip_tags($_POST['cp']));//版权方
                    $cpurl=trim($_POST['cpurl']);//版权链接
                    $vip=trim($_POST['vip']);//权限
                    $content=trim($_POST['content']);//内容
                    $top=trim($_POST['top']);//置顶
                    $dow=trim($_POST['dow']);//下载积分
                    $downame=trim(strip_tags($_POST['downame']));//网盘名称
                    $dowurl=trim($_POST['dowurl']);//下载地址
                    $dowpas=trim(strip_tags($_POST['dowpas']));//提取码
                    $dowmun=trim(strip_tags($_POST['dowmun']));//文件数量
                    $dowsize=trim(strip_tags($_POST['dowsize']));//文件大小
                    $dowzip=trim($_POST['dowzip']);//解压密码
                    $dowpx=trim(strip_tags($_POST['dowpx']));//分辨率
                    $dowif=intval(trim($_POST['dowif']));//下载权限

                    if (empty($dowif)||($dowif!==1&&$dowif!==2&&$dowif!==3)){
                        echo 500;
                        exit();
                    }

                    //判断标题或内容是否为空
                    if (empty($title)||empty($content)){
                        echo 404;
                        exit();
                    }
                    //判断标题是否超过120字，utf-8
                    if (mb_strlen($title,'utf-8')>120){
                        echo 403;
                        exit();
                    }
                    //判断封面图片是否是有效的url和后缀
                    if (!empty($img)){
                        $typeimg=['jpg', 'jpeg', 'gif', 'png', 'webp', 'svg','avif'];//允许的图片后缀
                        if (!in_array(pathinfo($img, PATHINFO_EXTENSION), $typeimg)){//判断是否是有效的后缀
                            echo 401;
                            exit();
                        }
                    }
                    //判断分类id是否为空且是否是正整数
                    if (is_null($if)||!is_numeric($if)||$if<0||$if===""){
                        echo 500;
                        exit();
                    }
                    //判断阅读权限是否是数字1/2/3其中一个
                    if (empty($vip)||!is_numeric($vip)||($vip!=1&&$vip!=2&&$vip!=3)){
                        echo 500;
                        exit();
                    }
                    //判断下载积分是否是数字，且不能是负数
                    if (!is_null($dow)&&$dow!==""){
                        if (!is_numeric($dow)||$dow<0){
                            echo 500;
                            exit();
                        }
                    }
                    //若存在版权链接，判断版权方是否为空
                    if (!empty($cpurl)){
                        if (empty($cp)){
                            echo 402;
                            exit();
                        }
                        //判断链接是否是有效的url
                        if (!filter_var($cpurl, FILTER_VALIDATE_URL)) {
                            echo 4022;
                            exit();
                        }
                    }
                    //标签处理
                    if (!empty($tag)){
                        //将中文逗号替换为英文逗号
                        $entag=str_replace("，", ",", $tag);
                        //将其转换为数组
                        $tagarr=explode(",", $entag);
                        //去除数组中的空值和重复元素
                        $newtagarr=array_unique(array_filter($tagarr));
                        //将数组重新转换为字符串
                        $newtag=implode(",", $newtagarr);
                    }else{
                        $newtag="";
                    }
                    //判断置顶是否是数字1、2、3、4其中一个
                    if (empty($top)||!is_numeric($top)||($top!=1&&$top!=2&&$top!=3&&$top!=4)){
                        echo 500;
                        exit();
                    }
                    //判断下载信息中，任意一项是否不为空
                    if (!empty($downame)||!empty($dowurl)||(!is_null($dowpas)&&$dowpas!="")||(!is_null($dowmun)&&$dowmun!="")||(!is_null($dowsize)&&$dowsize!="")||(!is_null($dowzip)&&$dowzip!="")||(!is_null($dowpx)&&$dowpx!="")){
                        //判断网盘名称、下载地址、文件数量、文件大小是否为空
                        if (empty($downame)&&empty($dowurl)&&(is_null($dowmun)||$dowmun=="")&&(is_null($dowsize)||$dowsize=="")){
                            $dowerr=0;
                        }else{
                            //判断下载地址是否是有效url
                            if (!filter_var($dowurl, FILTER_VALIDATE_URL)) {
                                $dowerr=0;
                            }else{
                                $dowerr=1;
                            }
                            
                        }
                    }else{
                        $dowerr=2;//全部为空
                    }
                    if ($dowerr==0){
                        echo 4023;
                        exit();
                    }
                        if ($dowerr==2){
                            $dowarr="";
                        }else{
                            if ($dowerr==1){
                                $dowarr=$downame.",".$dowurl.",".$dowmun.",".$dowsize.",".$dowzip.",".$dowpas.",".$dowpx;//下载信息拼接
                           }else{
                               $dowarr="";
                           }
                        }

                    //用预处理插入Mysql的ppz_row表，rowtexe字段=标题，rowbigtext字段=内容，rowtop字段=置顶，rowfl字段=分类，rowtag字段=标签，rowadmin字段=会员id,rowcp字段=版权方，rowcpurl字段=版权链接，rowdw字段=下载信息拼接，rowdwgold字段=下载积分，rowvip字段=阅读权限，rowif字段=1，rowimg字段=封面图片，rowyes字段=4
                    $rowyes=4;
                    $rowif=1;
                    $newsql = "INSERT INTO ppz_row (rowtexe, rowbigtext, rowtop, rowfl, rowtag, rowadmin, rowcp, rowcpurl, rowdw, rowdwgold, rowvip, rowif, rowimg, rowyes,rowdwif) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";
                    $stmt = mysqli_prepare($conn, $newsql);//预处理
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "sssssssssssssss", $title, $content, $top, $if, $newtag, $uid, $cp, $cpurl, $dowarr, $dow, $vip,$rowif, $img,$rowyes,$dowif);
                        if (mysqli_stmt_execute($stmt)) {
                            echo 200;
                        }else{
                            echo 500;
                        }
                    }else{
                        echo 600;
                    }
                    mysqli_stmt_close($stmt);


                }else{
                    echo 500;
                }

    }
    mysqli_close($conn);
}
?>