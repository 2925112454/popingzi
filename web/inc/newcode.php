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
                    if (!isset($_POST['code'])){
                        $_POST['code']="";      
                    }
                    $code=trim($_POST['code']);//邀请码
                    if(!empty($code)){
                        $codearr = explode(",", $code);//将字符串转换为数组
                        $codearr = array_unique($codearr);//去重
                        $codearr = array_filter($codearr, function ($value) {
                            return !empty($value);//去除空值和空字符串
                        });
                        if (count($codearr) > 0 && count($codearr) <= 1000) {
                            // 储存重复项
                            $repeatarr = array();
                            foreach ($codearr as $cardnum) {
                                // 判断数据库中是否有重复数据
                                $cardsql = "SELECT * FROM ppz_code WHERE binary invitecode = ?";
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
                            }else{
                                // 开始事务
                                mysqli_begin_transaction($conn);

                                $success = true;
                                foreach ($codearr as $cardnum) {
                                    // 插入数据库
                                    $newsql = "INSERT INTO ppz_code(invitecode) VALUES (?)";
                                    $newstmt = mysqli_prepare($conn, $newsql);
                                    mysqli_stmt_bind_param($newstmt, "s", $cardnum);
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

                        }else{
                            echo json_encode(["err" => 500]);
                        }
                    }else{
                        echo json_encode(["err" => 500]);
                    }
                }else{
                    echo json_encode(["err" => 500]);
                }
            }
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
    }
?>