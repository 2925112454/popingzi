<?php
header('Content-Type: application/json');
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION
if (empty($ppzusername)){//判断是否登录
    $json[]=array('id' => '','name' => '错误操作');
}else{
    include __DIR__.'/conn.php';//连接数据库
    $sql = "select * from ppz_newusername where binary uusername = '$ppzusername'";//获取登录会员信息
    $retval=mysqli_query($conn,$sql);
    if(mysqli_num_rows($retval) !== 1){ 
        $json[]=array('id' => '','name' => '错误操作');
    }else{
        $query = $conn->query($sql);
        while($row = $query->fetch_array()){
            $vip=$row['ustatus'];//身份，1普通会员，2为管理员，3为副站长，4为站长
        }
        if($vip==4||$vip==3||$vip==2){
            $id=trim($_POST['id']);
            if(!empty($id)&&$id>0&&is_numeric($id)){
                // 检查请求的一级目录是否存在
                $sqlCheckLink = "SELECT linkid FROM ppz_link WHERE linkid = $id";
                $resultCheckLink = mysqli_query($conn, $sqlCheckLink);
                if(mysqli_num_rows($resultCheckLink) > 0) {
                    $sqlfl="SELECT * FROM ppz_fl WHERE fllinkid=$id order by flid asc";
                    $resultfl=mysqli_query($conn,$sqlfl);
                    if(mysqli_num_rows($resultfl)>0){
                        $json = array();
                        while($rowfl = $resultfl->fetch_array()){
                            $json[] = array('id' => $rowfl['flid'], 'name' => $rowfl['flname']);
                        }
                    } else {
                        $json[]=array('id' => '','name' => '暂无分类');
                    }
                } else {
                    // 请求的一级目录不存在，查找第一个存在的一级目录
                    $sqllink="SELECT linkid FROM ppz_link order by linkid asc";
                    $resultlink=mysqli_query($conn,$sqllink);
                    if(mysqli_num_rows($resultlink)>0){
                        // 获取第一个linkid
                        while($rowlink = $resultlink->fetch_array()){
                            $linkid=$rowlink['linkid'];
                            break;
                        }
                        $sqlfltwo="SELECT * FROM ppz_fl WHERE fllinkid=$linkid order by flid asc";
                        $resultfltwo=mysqli_query($conn,$sqlfltwo);
                        if(mysqli_num_rows($resultfltwo)>0){
                            $json = array();
                            while($rowfltwo = $resultfltwo->fetch_array()){
                                $json[] = array('id' => $rowfltwo['flid'], 'name' => $rowfltwo['flname']);
                            }
                        } else {
                            $json[]=array('id' => '','name' => '暂无分类');
                        }
                    } else {
                        $json[]=array('id' => '','name' => '暂无分类');
                    }
                }
            } else {
                $json[]=array('id' => '','name' => '错误操作');
            }
        } else {
            $json[]=array('id' => '','name' => '错误操作');
        }
    }
    mysqli_close($conn);
}
echo json_encode($json);
?>