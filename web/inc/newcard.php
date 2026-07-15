<?php
    session_start(); // 开始 Session 会话
    include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
    if (empty($ppzusername)) { // 判断是否登录
        echo json_encode(["err" => 500]);
        exit;
    } else {
        include __DIR__.'/conn.php'; // 连接数据库
        $sql = "SELECT * FROM ppz_newusername WHERE binary uusername = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $ppzusername);
        mysqli_stmt_execute($stmt);
        $retval = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($retval) !== 1) { 
            echo json_encode(["err" => 500]);
            exit;
        } else {
            $row = mysqli_fetch_array($retval);
            $vip = $row['ustatus']; // 身份，1普通会员，2为管理员，3为副站长，4为站长

            if ($vip == 4 || $vip == 3) {
                if(!isset($_POST['type'])){
                    $_POST['type']="";
                }
                if(!isset($_POST['card'])){
                    $_POST['card']="";
                }
                if(!isset($_POST['gold'])){
                    $_POST['gold']="";
                }
                $type = intval($_POST['type']); // 类型，1为月度会员充值卡，2为季度会员充值卡，3为年度会员充值卡，4为百年会员充值卡，5为积分充值卡
                $card = trim($_POST['card']); // 卡号，英文逗号分隔的字符串
                $gold = intval($_POST['gold']); // 积分，类型为5时有效；1为默认10积分，2为20积分，3为30积分，4为40积分，5为50积分，6为100积分,7为1000积分

                if (!empty($type) && !empty($card) && !empty($gold) && ($type == 1 || $type == 2 || $type == 3 || $type == 4 || $type == 5) && ($gold == 1 || $gold == 2 || $gold == 3 || $gold == 4 || $gold == 5 || $gold == 6 || $gold == 7)) {
                    // 将字符串转换为数组
                    $cardarr = explode(",", $card);
                    $cardarr = array_unique($cardarr); // 去重
                    $cardarr = array_filter($cardarr, function ($value) {
                        return !empty($value);// 去除空值和空字符串
                    });


                    if($type==5){
                        $newgold=$gold;
                    }else{
                        $newgold=1;
                    }

                    if (count($cardarr) > 0 && count($cardarr) <= 1000) { // 判断数组长度

                        // 储存重复项
                        $repeatarr = array();

                        foreach ($cardarr as $cardnum) {
                            // 判断数据库中是否有重复数据
                            $cardsql = "SELECT * FROM ppz_vtime WHERE binary vvar = ?";
                            $cardstmt = mysqli_prepare($conn, $cardsql);
                            mysqli_stmt_bind_param($cardstmt, "s", $cardnum);
                            mysqli_stmt_execute($cardstmt);
                            $cardretval = mysqli_stmt_get_result($cardstmt);

                            if (mysqli_num_rows($cardretval) !== 0) {
                                array_push($repeatarr, $cardnum);
                            }
                            mysqli_stmt_close($cardstmt);
                        }

                        if (count($repeatarr) > 0) {
                            // 获取重复的条数
                            $repeatnum = count($repeatarr);
                            // 输出 JSON
                            echo json_encode(["err" => 800, "repeat" => $repeatarr, "repeatnum" => $repeatnum]);
                            exit;
                        } else {
                            // 开始事务
                            mysqli_begin_transaction($conn);

                            $success = true;
                            foreach ($cardarr as $cardnum) {
                                // 插入数据库
                                $newsql = "INSERT INTO ppz_vtime(vbin, vvar, vgold) VALUES (?, ?, ?)";
                                $newstmt = mysqli_prepare($conn, $newsql);
                                mysqli_stmt_bind_param($newstmt, "iss", $type, $cardnum, $newgold);
                                if (!mysqli_stmt_execute($newstmt)) {
                                    $success = false;
                                    break;
                                }
                                mysqli_stmt_close($newstmt);
                            }

                            if ($success) {
                                mysqli_commit($conn);
                                echo json_encode(["err" => 200]);
                            } else {
                                mysqli_rollback($conn);
                                echo json_encode(["err" => 600]);
                            }

                        }
                    } else {
                        echo json_encode(["err" => 500]);
                    }
                } else {
                    echo json_encode(["err" => 500]);
                }
            } else {
                echo json_encode(["err" => 500]);
            }
        }
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
?>