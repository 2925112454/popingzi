<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';

// 检查用户登录状态
if (empty($ppzusername)) {
    echo 500; // 保持原有输出方式，确保前端兼容性
    exit;
}

include __DIR__.'/conn.php';

// 获取并验证资源ID
$dwid = isset($_POST['dwid']) ? trim($_POST['dwid']) : '';

if (empty($dwid) || !is_numeric($dwid) || $dwid < 1) {
    echo 500; // 保持原有输出方式
    exit;
}

try {
    // 开启事务处理
    $conn->begin_transaction();

    // 查询用户信息
    $stmt = $conn->prepare("SELECT uid,ugold,uviptime,urowyes FROM ppz_newusername WHERE binary uusername = ?");
    $stmt->bind_param("s", $ppzusername);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        $_SESSION["ppzusername"] = "";
        $conn->rollback();
            echo 500;
        exit;
    }

    $user = $result->fetch_assoc();
    $uid = $user['uid'];
    $ugold = $user['ugold'];
    $urowyes = $user['urowyes'];
    $uvip_time = $user['uviptime'];//会员时间

    if(empty($uvip_time)){
        $uvip_time = strtotime("2010-01-01 00:00:01");
    }else{
        $uvip_time = strtotime($uvip_time);
    }

    // 检查是否已购买
    $purchased = !empty($urowyes) ? explode("|", $urowyes) : [];
    if (in_array($dwid, $purchased)) {
        echo 200; 
        exit;
    }

    // 查询资源信息
    $stmt = $conn->prepare("SELECT rowdwgold, rowadmin FROM ppz_row WHERE rowid = ?");
    $stmt->bind_param("i", $dwid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        $conn->rollback();
            echo 500;
        exit;
    }

    $resource = $result->fetch_assoc();
    $wglod = $resource['rowdwgold'];
    $wglod_d = $wglod;
    $wglod_admin = $resource['rowadmin'];

    //查询分成配置
    $stmt = $conn->prepare("SELECT upfcsize,upvipsize FROM ppz_upfile WHERE id = 1");
    $stmt->execute();
    $resultup = $stmt->get_result();
    $upfile = $resultup->fetch_assoc(); // 一次性获取结果
    $upfcsize = $upfile['upfcsize'];//分成比例
    $upvipsize = $upfile['upvipsize'];//vip折扣

    if ($upfcsize >= 0 && $upfcsize <= 100 ) {
        $rmb_bfb = $upfcsize;
    } else {
        $rmb_bfb = 100;
    }

    if ($upvipsize >= 0 && $upvipsize <= 100 ) {
        $vip_bfb = $upvipsize;
    } else {
        $vip_bfb = 0;
    }

        //判断会员时间是否过期
        if($uvip_time > time()){
            $vip_bfb = $vip_bfb;
        }else{
            $vip_bfb = 0;
        }

    if ($wglod <= 0) {
        echo 404;
        exit;
    }

    //折后价
    $wglod = $wglod * (1 - $vip_bfb / 100);
    $wglod = round($wglod,0);

    // 检查积分是否足够
    if ($ugold < $wglod) {
        echo 300;
        exit;
    }

    // 更新用户积分和已购买记录
    $newgold = $ugold - $wglod;

    $newurowyes = empty($urowyes) ? $dwid : "$urowyes|$dwid";

    $stmt = $conn->prepare("UPDATE ppz_newusername SET ugold = ?, urowyes = ? WHERE uid = ?");
    $stmt->bind_param("isi", $newgold, $newurowyes, $uid);
    if (!$stmt->execute()) {
        $conn->rollback();
        echo 500;
        exit;
    }

    // 生成订单号和获取用户IP
    $ordernumber = date("YmdHis") . rand(100000, 999999) . "-" . $uid;
    $userip = $_SERVER['REMOTE_ADDR'];
    $nowtime = date("Y-m-d H:i:s");

    // 处理作者分成
    if (!empty($wglod_admin)&&!empty($rmb_bfb)&&$rmb_bfb>0) {

        $stmt = $conn->prepare("SELECT uid,ustatus FROM ppz_newusername WHERE binary uid = ?");
        $stmt->bind_param("i", $wglod_admin);
        $stmt->execute();
        $result = $stmt->get_result();
        $resourcex = $result->fetch_assoc();
        $admin_if = $resourcex['ustatus'];//身份，1普通会员，2为管理员，3为副站长，4为站长

        if ($result->num_rows == 1 && $admin_if == 1) {
            $newgold_admin = round($wglod_d * $rmb_bfb / 100, 0); // 最终积分数量
            // 更新作者积分
            $stmt = $conn->prepare("UPDATE ppz_newusername SET ugold = ugold + ? WHERE uid = ?");
            $stmt->bind_param("ii", $newgold_admin, $wglod_admin);
            if ($stmt->execute()) {
                // 获取作者当前积分
                $stmt = $conn->prepare("SELECT ugold FROM ppz_newusername WHERE uid = ?");
                $stmt->bind_param("i", $wglod_admin);
                $stmt->execute();
                $result = $stmt->get_result();
                $newgold_admin_user = $result->fetch_assoc()['ugold'];
                if($wglod_admin==$uid){
                    $newgold=$newgold_admin_user;
                }
                // 记录分成日志
                $ordernumberxx = date("YmdHis") . rand(100000, 999999) . "-" . $wglod_admin;
                $newgold_admin_plus = "+" . $newgold_admin;
                
                $logType = '购买分成';
                $stmt = $conn->prepare("INSERT INTO ppz_log (logadmin, logtime, logtype, logrmb, logab, logmun, logrowid, logip) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssssis", $wglod_admin, $nowtime, $logType, $newgold_admin_plus, $newgold_admin_user, $ordernumberxx, $dwid, $userip);
                $stmt->execute();
            }
        }

    }

    if($wglod>0){
        // 记录下载日志
        $wglod_minus = "-" . $wglod;
        $logTypex = '资源购买';
        $stmt = $conn->prepare("INSERT INTO ppz_log (logadmin, logtime, logtype, logrmb, logab, logmun, logrowid, logip) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssis", $uid, $nowtime,$logTypex, $wglod_minus, $newgold, $ordernumber, $dwid, $userip);
        
        if ($stmt->execute()) {
            $conn->commit();
            echo 400;
        } else {
            $conn->rollback();
            echo 500;
        }
        
    }else{
        $conn->commit();
        echo 400;
    }
    
} catch (Exception $e) {
    $conn->rollback();
    echo 500;
} finally {
    $conn->close();
}
?>