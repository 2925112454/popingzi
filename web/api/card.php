<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量

// 判断是否登录
if (empty($ppzusername)) {
    echo json_encode(array('err' => 500));
    exit;
}

include $_SERVER['DOCUMENT_ROOT'] . '/inc/conn.php'; // 链接数据库

// 获取登录会员信息
$stmt = $conn->prepare("SELECT * FROM ppz_newusername WHERE binary uusername = ?");
$stmt->bind_param("s", $ppzusername);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo json_encode(array('err' => 500));
    exit;
}

$row = $result->fetch_assoc();
$ustatus = $row['ustatus']; // 身份，1普通会员，2为管理员，3为副站长，4为站长；

if ($ustatus != 4 && $ustatus != 3) {
    echo json_encode(array('err' => 500));
    exit;
}

// 获取 POST 数据
$type = isset($_POST['type']) ? $_POST['type'] : '';
$gold = isset($_POST['gold']) ? $_POST['gold'] : '';

// 验证输入
if (empty($type) || empty($gold)) {
    echo json_encode(array('err' => 500));
    exit;
}

$typeArray = explode(",", $type);
$goldArray = explode(",", $gold);

// 验证 type 和 gold 的值是否符合规定
$validTypes = [1, 2, 3, 4, 5];
$validGolds = [1, 2, 3, 4, 5, 6, 7];

$vvarArray = []; // 用于存储所有符合条件的充值卡号

foreach ($typeArray as $index => $typeValue) {
    if (!in_array($typeValue, $validTypes)) {
        echo json_encode(array('err' => 500));
        exit;
    }

    if ($typeValue == 5) {
        // 遍历 goldArray 中的所有值
        foreach ($goldArray as $goldValue) {
            if (!in_array($goldValue, $validGolds)) {
                continue; // 跳过无效的 gold 值
            }

            $stmt = $conn->prepare("SELECT * FROM ppz_vtime WHERE binary vbin = ? AND vgold = ?");
            $stmt->bind_param("ss", $typeValue, $goldValue);
            $stmt->execute();
            $newResult = $stmt->get_result();

            if ($newResult->num_rows > 0) {
                while ($newRow = $newResult->fetch_assoc()) {
                    $vvarArray[] = $newRow['vvar']; // 收集所有符合条件的充值卡号
                }
            }
        }
    } else {
        $stmt = $conn->prepare("SELECT * FROM ppz_vtime WHERE binary vbin = ? AND vgold = ?");
        $goldValue = 1;
        $stmt->bind_param("ss", $typeValue, $goldValue);
        $stmt->execute();
        $newResult = $stmt->get_result();

        if ($newResult->num_rows > 0) {
            while ($newRow = $newResult->fetch_assoc()) {
                $vvarArray[] = $newRow['vvar']; // 收集所有符合条件的充值卡号
            }
        }
    }
}

if (empty($vvarArray)) {
    echo json_encode(array('err' => 600));
    exit;
}

echo json_encode(array('err' => 200, 'card' => $vvarArray));
exit;
?>