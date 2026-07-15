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
                $uid=$row['uid'];//用户id
            }
            if ($vip==3||$vip==4||$vip==2){
                if (!isset($_POST['tif'])){
                    $_POST['tif']="";
                }
                if (!isset($_POST['repid'])){
                    $_POST['repid']="";
                }
                if (!isset($_POST['value'])){
                    $_POST['value']="";
                }
                $if=$_POST['tif'];//1为文章评论，2为公告评论
                $rid=trim($_POST['repid']);//评论id
                $value=trim($_POST['value']);//回复内容
                $value=htmlspecialchars($value);//防止xss攻击
                $value=strip_tags($value);//过滤html标签
                $value=str_replace("\n","",$value);//防止回车换行
                $value=str_replace("\r","",$value);//防止回车换行
                $ip = $_SERVER['REMOTE_ADDR'];//获取ip
                if ($if==1||$if==2){

                    if ($if==1){
                        $table="ppz_commentary";//评论表
                        $reptable="ppz_reply";//回复表
                    }else{
                        $table="ppz_ggcommentary";//公告评论表
                        $reptable="ppz_ggreply";//公告回复表
                    }

                    //判断id是否是正整数
                    if (is_numeric($rid) && $rid > 0) {
                        $rid = (int)$rid;//转换为整数

                        //判断内容是否为空
                        if (!empty($value)){
                            //查询是否有此评论
                            $plsql = "select * from $table where binary plid = $rid";
                            $plretval=mysqli_query($conn,$plsql);
                            if(mysqli_num_rows($plretval) !== 1){
                                echo 404;
                            }else{

                                //添加回复:顺序是评论id，回复人id，回复内容，回复人ip
                                $newsql = "insert into $reptable (repplid,repadmin,reptext,repip) values ($rid,$uid,'$value','$ip')";
                                if ($conn->query($newsql) === TRUE) {
                                    echo 200;
                                }else{
                                    echo 600;
                                     // 输出具体的错误信息以供调试
                                    //echo "Error: " . $newsql . "<br>" . $conn->error;
                                }

                            }
                        }else{
                            echo 500;
                        }
                    }else{
                        echo 500;
                    }

                }else{
                    echo 500;
                }

            }else{
                echo 500;
            }
        }
}
?>