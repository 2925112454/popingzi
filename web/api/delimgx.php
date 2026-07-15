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
                        $uid=$row['uid'];//用户ID
                    }
                    if ($ustatus==4||$ustatus==3||$ustatus==2){
                        if(!isset($_POST['url'])){
                            $_POST['url']="";
                        }
                        if(!isset($_POST['id'])){
                            $_POST['id']="";
                        }
                        $url=$_POST['url'];//获取要删除的图片地址
                        $id=$_POST['id'];//文章id
                        if (is_null($url)||$url==""||$url==" "||is_null($id)||$id==""||$id==" "||empty($url)||empty($id)||!is_numeric($id)){
                            echo 400;
                        }else{

                            $urlt=str_replace("../","/",$url); //替换url中的“../”为“/”
                            $urla=$_SERVER['DOCUMENT_ROOT'].$urlt;//获取要删除的图片绝对路径
                            //判断图片地址开头是否是/upload/或upload/或./upload/或../upload/
                            if (strpos($url, '/upload/') !== false || strpos($url, 'upload/') !== false || strpos($url, './upload/') !== false || strpos($url, '../upload/') !== false) {
                                if(file_exists($urla)){
                                    unlink($urla);//删除图片
                                }
                            }

                            //获取文章信息
                            $rowsqlnew = "select * from ppz_row where binary rowid = $id";
                            $rowretvalnew=mysqli_query($conn,$rowsqlnew);
                            if(mysqli_num_rows($rowretvalnew) !== 1){ 
                                echo 404;
                            }else{
                                $querynew = $conn->query($rowsqlnew);
                                while($rownew = $querynew->fetch_array()){
                                    $rowbigtext=$rownew['rowbigtext'];//内容，以|分割
                                }
                                if (is_null($rowbigtext)||$rowbigtext==""||$rowbigtext==" "||empty($rowbigtext)){
                                    echo 404;
                                }else{
                                    //转为数组
                                    $rowbigtextarr=explode("|",$rowbigtext);
                                    //删除数组中等于$url的元素
                                    $key = array_search($url, $rowbigtextarr);
                                    if ($key !== false) {//如果数组中存在该元素
                                        unset($rowbigtextarr[$key]);//删除该元素
                                        $rowbigtextarr=array_values($rowbigtextarr);//重新索引数组
                                        $rowbigtext=implode("|",$rowbigtextarr);//转为字符串
                                        $sql = "UPDATE ppz_row SET rowbigtext = '$rowbigtext' WHERE rowid = $id";//更新数据库
                                        if ($conn->query($sql) === TRUE) {//执行sql语句
                                            echo 200;
                                        }else{
                                            echo 600;
                                        }
                                        
                                    }
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