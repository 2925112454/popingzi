<?php
    session_start(); // 开始 Session 会话
    include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
    if (empty($ppzusername)){//判断是否登录
        echo 500;
    }else{
        include $_SERVER['DOCUMENT_ROOT'].'/inc/conn.php';//链接数据库
        $rowsql = "select * from ppz_newusername where binary uusername = $ppzusername";//获取登录会员信息
        $rowretval=mysqli_query($conn,$rowsql);
        if(mysqli_num_rows($rowretval) !== 1){ 
            echo 500;
        }else{
            $query = $conn->query($rowsql);
            while($row = $query->fetch_array()){
                $ustatus=$row['ustatus'];//身份，1普通会员，2为管理员，3为副站长，4为站长；此处为LOGO上传操作，仅限站长身份操作
            }
            if ($ustatus==4||$ustatus==3||$ustatus==2){
                if(!isset($_POST['id'])){
                    $_POST['id']="";
                }
                $id=trim($_POST['id']);//文章id
                if (empty($id)||!is_numeric($id)||$id<1){
                    echo 500;
                }else{
                    $sqlc = "select * from ppz_row where rowid=$id";
                    $retvalc=mysqli_query($conn,$sqlc);
                    if(mysqli_num_rows($retvalc) !== 1){ 
                        echo 404;
                    }else{

                        $queryr = $conn->query($sqlc);
                        while($rowr = $queryr->fetch_array()){
                            $rowif=$rowr['rowif'];//文章类型，1图文，2相册，3视频
                        }

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
                        if (!isset($_POST['vip'])){
                            $_POST['vip']="";
                        }
                        if (!isset($_POST['content'])){
                            $_POST['content']="";
                        }
                        if (!isset($_POST['top'])){
                            $_POST['top']="";
                        }
                        if (!isset($_POST['cp'])){
                            $_POST['cp']="";
                        }
                        if (!isset($_POST['cpurl'])){
                            $_POST['cpurl']="";
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
                        if (!isset($_POST['dowmun'])){
                            $_POST['dowmun']="";
                        }
                        if (!isset($_POST['dowsize'])){
                            $_POST['dowsize']="";
                        }
                        if (!isset($_POST['dowpx'])){
                            $_POST['dowpx']="";
                        }
                        if (!isset($_POST['dowpas'])){
                            $_POST['dowpas']="";
                        }
                        if (!isset($_POST['dowzip'])){
                            $_POST['dowzip']="";
                        }
                        if (!isset($_POST['text'])){
                            $_POST['text']="";
                        }
                        if (!isset($_POST['dowif'])){
                            $_POST['dowif']="";
                        }
                        if(!isset($_POST['imageseye'])||is_null($_POST['imageseye'])||!is_numeric($_POST['imageseye'])||$_POST['imageseye']<1||$_POST['imageseye']>999999999){
                            $_POST['imageseye']=0;
                        }
                        if(!isset($_POST['vipimageseye'])||is_null($_POST['vipimageseye'])||!is_numeric($_POST['vipimageseye'])||$_POST['vipimageseye']<1||$_POST['vipimageseye']>999999999){
                            $_POST['vipimageseye']=0;
                        }
                        if (!isset($_POST['txttop'])){
                            $_POST['txttop']=1;
                        }

                        $tag=trim($_POST['tag']);//文章标签
                        $title=trim($_POST['title']);//文章标题
                        $img=trim($_POST['img']);//文章封面
                        $if=trim($_POST['if']);//文章分类
                        $vip=trim($_POST['vip']);//阅读权限
                        $content=$_POST['content'];//文章摘要
                        $top=trim($_POST['top']);//是否置顶
                        $cp=trim($_POST['cp']);//版权方名称
                        $cpurl=trim($_POST['cpurl']);//版权方地址
                        $dow=trim($_POST['dow']);//下载所需积分
                        $downame=trim($_POST['downame']);//网盘名称
                        $dowurl=trim($_POST['dowurl']);//网盘地址
                        $dowmun=trim($_POST['dowmun']);//文件数量
                        $dowsize=trim($_POST['dowsize']);//文件大小
                        $dowpx=trim($_POST['dowpx']);//文件分辨率
                        $dowpas=$_POST['dowpas'];//提取码
                        $dowzip=$_POST['dowzip'];//压缩密码
                        $text=$_POST['text'];//文章内容
                        $dowif=trim(intval($_POST['dowif']));//下载权限
                        $imageseye=intval($_POST['imageseye']);//游客可见
                        $vipimageseye=intval($_POST['vipimageseye']);//登录可见
                        $txttop=trim(intval($_POST['txttop']));

                        if(empty($if)||!is_numeric($if)||$if<1){
                            $if=0;
                        }

                    if (empty($dowif)||($dowif!=1&&$dowif!=2&&$dowif!=3)||empty($txttop)||($txttop!=1&&$txttop!=2)){
                        echo 500;
                        exit();
                    }
                       
                        //若标题为空
                        if (empty($title)){
                            echo 500;
                            exit();
                        }

                        //若分类不是正整数
                        if (!is_numeric($if)||$if<0){
                            echo 500;
                            exit();
                        }

                        //若阅读权限不是正整数且不为1/2/3任意一个
                        if (empty($vip)||!is_numeric($vip)||($vip!=1&&$vip!=2&&$vip!=3)){
                            echo 500;
                            exit();
                        }

                        //若是否置顶不是正整数，且不是1/2/3/4任意一个
                        if (empty($top)||!is_numeric($top)||($top!=1&&$top!=2&&$top!=3&&$top!=4)){
                            echo 500;
                            exit();
                        }

                        //若版权方地址不为空，则判断是否为网址
                        if (!empty($cpurl)){
                            if (preg_match("/^http(s)?:\\/\\/.+/",$cpurl)){
                                if (empty($cp)){
                                    echo 505;
                                    exit();
                                }
                            }else{
                                echo 506;
                                exit();
                            }
                        }

                        if (!empty($downame)||!empty($dowurl)||!empty($dowmun)||!empty($dowsize)){
                            if (empty($downame)||empty($dowurl)||is_null($dowmun)||is_null($dowsize)||$downame===""||$dowurl===""||$dowmun===""||$dowsize===""){
                                $dowarr="";
                            }else{
                                //将网盘信息拼凑起来，
                                $dowarr=$downame.",".$dowurl.",".$dowmun.",".$dowsize.",".$dowzip.",".$dowpas.",".$dowpx;
                            }
                        }else{
                            $dowarr="";
                        }

                        if (!empty($dowurl)){
                            if (!preg_match("/^http(s)?:\\/\\/.+/",$dowurl)){
                                echo 500;
                                exit();
                            }
                        }

                        //判断积分
                        if (!empty($dow)){
                            if (!is_numeric($dow)||$dow<0||$dow>999999999){
                                echo 500;
                                exit();
                            }
                        }

                        //判断封面
                        if (!empty($img)){
                            //判断url地址后缀是否是图片后缀
                            if (!preg_match("/.(jpg|jpeg|png|gif|bmp|webp|svg|ico)$/i",$img)){
                                echo 500;
                                exit();
                            }
                        }

                        //判断标签
                        if (!empty($tag)){
                           //将标签转换为逗号分割的数组
                           $tags=explode(",",$tag);
                           $tags=array_unique($tags);//去重
                           $tags=array_filter($tags);//去除空项
                           //转换为字符串
                           $tag=implode(",",$tags);
                        }


                        if ($rowif==1){
                            if(empty($text)){
                                echo 500;
                                exit();
                            }
                            $content="";
                            // 使用预处理语句
                            $updatesql = "UPDATE ppz_row SET 
                            rowtexe = ?, 
                            rowbigtext = ?, 
                            rowtop = ?, 
                            rowfl = ?, 
                            rowtag = ?, 
                            rowtime = date_format(now(), '%Y-%m-%d %H:%i:%s'), 
                            rowcp = ?, 
                            rowcpurl = ?, 
                            rowdw = ?, 
                            rowdwgold = ?, 
                            rowvip = ?, 
                            rowimg = ?, 
                            videotext = ?,
                            rowdwif = ?
                            WHERE rowid = ?";

                            $stmt = $conn->prepare($updatesql);
                            // 绑定参数
                            $stmt->bind_param("ssssssssssssss", 
                                $title, 
                                $text, 
                                $top, 
                                $if, 
                                $tag, 
                                $cp, 
                                $cpurl, 
                                $dowarr, 
                                $dow, 
                                $vip, 
                                $img, 
                                $content,
                                $dowif,
                                $id);

                            // 执行更新
                            if ($stmt->execute()) {
                            echo 200;
                            } else {
                            echo 600;
                            }
                            // 关闭预处理语句
                            $stmt->close();

                        }else if ($rowif==2){
                            // 使用预处理语句
                            $updatesql = "UPDATE ppz_row SET 
                            rowtexe = ?, 
                            rowtop = ?, 
                            rowfl = ?, 
                            rowtag = ?, 
                            rowtime = date_format(now(), '%Y-%m-%d %H:%i:%s'), 
                            rowcp = ?, 
                            rowcpurl = ?, 
                            rowdw = ?, 
                            rowdwgold = ?, 
                            rowvip = ?, 
                            rowimg = ?, 
                            videotext = ?,
                            rowdwif = ?,
                            vorimg = ?,
                            vorimg_log = ?,
                            videotexttop = ?
                            WHERE rowid = ?";

                            $stmt = $conn->prepare($updatesql);
                            // 绑定参数
                            $stmt->bind_param("ssssssssssssssss", 
                                $title, 
                                $top, 
                                $if, 
                                $tag, 
                                $cp, 
                                $cpurl, 
                                $dowarr, 
                                $dow, 
                                $vip, 
                                $img, 
                                $content, 
                                $dowif,
                                $imageseye,
                                $vipimageseye,
                                $txttop,
                                $id);

                            // 执行更新
                            if ($stmt->execute()) {
                            echo 200;
                            } else {
                            echo 600;
                            }
                            // 关闭预处理语句
                            $stmt->close();    

                        }else if ($rowif==3){
                            if(!empty($text)){
                                if ($rowif==2||$rowif==3){
                                    $text=explode(",",$text);
                                    $text=array_unique($text);
                                    $text=array_filter($text);
                                    $text=implode("|",$text);//转换为字符串
                                }  
                            } 



                            // 使用预处理语句
                            $updatesql = "UPDATE ppz_row SET 
                            rowtexe = ?, 
                            rowbigtext = ?, 
                            rowtop = ?, 
                            rowfl = ?, 
                            rowtag = ?, 
                            rowtime = date_format(now(), '%Y-%m-%d %H:%i:%s'), 
                            rowcp = ?, 
                            rowcpurl = ?, 
                            rowdw = ?, 
                            rowdwgold = ?, 
                            rowvip = ?, 
                            rowimg = ?, 
                            videotext = ?,
                            rowdwif = ?,
                            videotexttop = ?
                            WHERE rowid = ?";

                            $stmt = $conn->prepare($updatesql);
                            // 绑定参数
                            $stmt->bind_param("sssssssssssssss", 
                                $title, 
                                $text, 
                                $top, 
                                $if, 
                                $tag, 
                                $cp, 
                                $cpurl, 
                                $dowarr, 
                                $dow, 
                                $vip, 
                                $img, 
                                $content, 
                                $dowif,
                                $txttop,
                                $id);

                            // 执行更新
                            if ($stmt->execute()) {
                            echo 200;
                            } else {
                            echo 600;
                            }
                            // 关闭预处理语句
                            $stmt->close();



                        }else{
                            echo 500;
                            exit();
                        }


                                            

                    }
                }
            }else{
                echo 500;
            }
        }
     $conn->close();
    }
?>