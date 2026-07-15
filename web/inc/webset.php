<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION
if (empty($ppzusername)){//判断是否登录
    echo 500; 
}else{
    include __DIR__.'/conn.php';//连接数据库
    $sql = "select * from ppz_newusername where binary uusername = $ppzusername";//获取登录会员信息
    $retval=mysqli_query($conn,$sql);
    if(mysqli_num_rows($retval) !== 1){ 
        echo 500;
    }else{
        $query = $conn->query($sql);
        while($row = $query->fetch_array()){
            $vip=$row['ustatus'];//身份，1普通会员，2为管理员，3为副站长，4为站长
        }
        if($vip==4){//判断是否为站长
            if (!isset($_POST['toplogo'])){
                $_POST['toplogo']="";
            }
            if (!isset($_POST['butlogo'])){
                $_POST['butlogo']="";
            }
            if (!isset($_POST['newintel'])){
                $_POST['newintel']="";
            }
            if (!isset($_POST['webtext'])){
                $_POST['webtext']="";
            }
            if (!isset($_POST['webtxt'])){
                $_POST['webtxt']="";
            }
            if (!isset($_POST['webpas'])){
                $_POST['webpas']="";
            }
            if (!isset($_POST['webbut'])){
                $_POST['webbut']="";
            }
            if (!isset($_POST['qq'])){
                $_POST['qq']="";
            }
            if (!isset($_POST['zq'])){
                $_POST['zq']="";
            }
            if (!isset($_POST['wb'])){
                $_POST['wb']="";
            }
            if (!isset($_POST['email'])){
                $_POST['email']="";
            }
            if (!isset($_POST['toplogourl'])){
                $_POST['toplogourl']="";
            }
            if (!isset($_POST['jifen'])){
                $_POST['jifen']="";
            }
            $toplogo=trim($_POST['toplogo']);//导航栏LOGO
            $toplogourl=trim($_POST['toplogourl']);
            $butlogo=trim($_POST['butlogo']);//底部LOGO
            $newintel=trim($_POST['newintel']);//新媒体账号
            $webtext=trim($_POST['webtext']);//网站标题
            $webtxt=trim($_POST['webtxt']);//网站副标题
            $webpas=trim($_POST['webpas']);//网站关键词
            $webvar=trim($_POST['webvar']);//网站描述
            $webbut=trim($_POST['webbut']);//网站版权信息
            $qq=trim($_POST['qq']);//客服QQ
            $zq=trim($_POST['zq']);//客服QQ群
            $wb=trim($_POST['wb']);//客服微博
            $email=trim($_POST['email']);//客服邮箱
            $jifen=trim($_POST['jifen']);//签到奖励的积分范围可是纯数字或者两个数字中间用-隔开的范围
            if (empty($toplogo)||empty($butlogo)){
                echo 403;
            }else{
                if(empty($webtext)||empty($webtxt)||empty($webpas)||empty($webvar)||empty($webbut)){
                    echo 404;
                }else{

                    if(!empty($qq)){
                        if(!preg_match("/^http(s)?:\\/\\/.+/",$qq)){
                            echo 402;
                            exit();
                        }
                    }
                    if(!empty($zq)){
                        if(!preg_match("/^http(s)?:\\/\\/.+/",$zq)){
                            echo 402;
                            exit();
                        }
                    }
                    if(!empty($wb)){
                        if(!preg_match("/^http(s)?:\\/\\/.+/",$wb)){
                            echo 402;
                            exit();
                        }
                    }
                    if(!empty($email)){
                        if(!preg_match("/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/",$email)){
                            echo 401;
                            exit();
                        }
                    }

                    //判断积分是否是合法格式
                    if (!empty($jifen)) {
                        if (!preg_match("/^[0-9]+(-[0-9]+)?$/", $jifen)) {
                            echo 405;
                            exit();
                        }
                    }
                    function is_http_or_https_url($url) {
                        return substr($url, 0, 7) === 'http://' || substr($url, 0, 8) === 'https://';
                    }

                    if (!empty($toplogourl)){
                        //判断toplogourl是否只有//
                        if ($toplogourl=="//"||$toplogourl=="///"||$toplogourl=="////"||$toplogourl=="/////"||$toplogourl=="//////"||$toplogourl=="///////"||$toplogourl=="////////"||$toplogourl=="/////////"||$toplogourl=="//////////"||$toplogourl=="///////////"||$toplogourl=="////////////"){
                            $toplogourl="/";
                        }
                        if (is_http_or_https_url($toplogourl)){
                            //判断链接是否有效
                            if (filter_var($toplogourl, FILTER_VALIDATE_URL)) {
                                $toplogourl=$toplogourl;
                            }else{
                                $toplogourl="/";
                            }
                            
                        }
                    }else{
                        $toplogourl="/";
                    } 
                
                    if(empty($jifen)){
                        $jifen=0;
                    }

                    //链接数据库ppz_web进行修改
                    $newsql = "update ppz_web set weblogo='$toplogo',toplogourl='$toplogourl',webbutlogo='$butlogo',webnewnet='$newintel',webtext='$webtext',webby='$webtxt',webpass='$webpas',webvar='$webvar',webfooter='$webbut',webqqurl='$qq',webqqqurl='$zq',webwburl='$wb',webemail='$email',webjifen='$jifen' where webid=1";
                    if(mysqli_query($conn,$newsql)){
                        echo 200;
                    }else{
                        echo 500;
                    }

                }
            }
           
        }else{
            echo 500;
        }

    }
}
?>