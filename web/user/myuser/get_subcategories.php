<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION
if (!isset($ppzusername) || empty($ppzusername)) {
    echo '<option value="0">错误参数</option>';
    die(); 
}
// 连接数据库
include $_SERVER['DOCUMENT_ROOT'].'/inc/conn.php';
$link_id = isset($_POST['link_id']) ? intval($_POST['link_id']) : 0;
if ($link_id > 0) {
    // 查询对应二级分类
    $sql = "SELECT flid,flname FROM `ppz_fl` WHERE fllinkid = $link_id ORDER BY flid ASC";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $options = '';
        while ($row = mysqli_fetch_assoc($result)) {
            $options .= '<option value="'.$row['flid'].'">'.$row['flname'].'</option>';
        }
        echo $options;
    } else {
        echo '<option value="0">神秘领域</option>';
    }
} else {
    echo '<option value="0">神秘领域</option>';
}
mysqli_close($conn);
?>