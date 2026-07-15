<?php
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php';//SESSION变量
if (empty($ppzusername)) { // 判断是否登录
    echo 500;
} else {
    include __DIR__.'/conn.php'; // 连接数据库

    // 使用预处理语句防止SQL注入
    $stmt = $conn->prepare("SELECT * FROM ppz_newusername WHERE binary uusername = ?");
    $stmt->bind_param("s", $ppzusername);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        echo 500;
    } else {
        $row = $result->fetch_assoc();
        $vip = $row['ustatus']; // 身份，1普通会员，2为管理员，3为副站长，4为站长

        if ($vip >= 2 && $vip <= 4) {
            if(!isset($_POST["ids"])){
                $_POST["ids"]="";
            }
            $ids = $_POST["ids"]; // 文章id数组(英文逗号分割的字符串)

            if (empty($ids)) {
                echo 500;
            } else {
                $idsarr = array_unique(explode(",", $ids)); // ids转换为数组

                // 使用事务保证数据一致性
                $conn->autocommit(FALSE);
                try {
                    foreach ($idsarr as $id) {
                        if (!is_numeric($id) || $id < 1) {
                            throw new Exception('Invalid ID');
                        }

                        // 使用预处理语句
                        $stmt = $conn->prepare("SELECT * FROM ppz_row WHERE rowid = ?");
                        $stmt->bind_param("i", $id);
                        $stmt->execute();
                        $rowd = $stmt->get_result()->fetch_assoc();
                        $stmt->close();

                        if (!$rowd) {
                            throw new Exception('Article not found');
                        }

                        // 删除封面和相关文件
                        $rowimg = $rowd['rowimg'];
                        if ($rowimg && strpos($rowimg, "http://") === false && strpos($rowimg, "https://") === false) {
                            $rowimage_url = str_replace("../", "/", $rowimg);//将“../”替换为“/”
                            $rowimgx = $_SERVER['DOCUMENT_ROOT'] . $rowimage_url;
                            if (@file_exists($rowimgx)) {
                                unlink($rowimgx);
                            }
                        }

                        // 删除图片或视频或音频
                        if ($rowd['rowif'] == 1) {
                            // 提取图片地址并删除
                            $pattern = '/<img[^>]+src="([^"]+)"/i';
                            preg_match_all($pattern, $rowd['rowbigtext'], $matches);
                            $image_urls = $matches[1];//转换数组
                            $image_urls = array_unique($image_urls);//去重
                            foreach ($image_urls as $image_url) {
                                $image_url = str_replace("../", "/", $image_url);//将“../”替换为“/”
                                if (@file_exists($_SERVER['DOCUMENT_ROOT'] . $image_url)) {
                                    unlink($_SERVER['DOCUMENT_ROOT'] . $image_url);
                                }
                            }
                        } else {
                            $image_urlsx = explode("|", $rowd['rowbigtext']);
                            foreach ($image_urlsx as $image_url) {
                                if (strpos($image_url, "http://") === false && strpos($image_url, "https://") === false) {
                                    if (@file_exists($_SERVER['DOCUMENT_ROOT'] . $image_url)) {
                                        unlink($_SERVER['DOCUMENT_ROOT'] . $image_url);
                                    }
                                }
                            }
                        }

                        // 删除文章下的所有评论
                        $stmt = $conn->prepare("SELECT * FROM ppz_commentary WHERE binary plrowid = ?");
                        $stmt->bind_param("i", $id);
                        $stmt->execute();
                        $comments = $stmt->get_result();
                        $comment_ids = [];
                        while ($comment = $comments->fetch_assoc()) {
                            $comment_ids[] = $comment['plid'];
                        }
                        $stmt->close();

                        if (!empty($comment_ids)) {
                            $dplsql = "DELETE FROM ppz_commentary WHERE plid IN (" . implode(",", $comment_ids) . ")";
                            $conn->query($dplsql);

                            $reply_ids = [];
                            $stmt = $conn->prepare("SELECT * FROM ppz_reply WHERE repid IN (" . implode(",", $comment_ids) . ")");
                            $stmt->execute();
                            $replies = $stmt->get_result();
                            while ($reply = $replies->fetch_assoc()) {
                                $reply_ids[] = $reply['repid'];
                            }
                            $stmt->close();

                            if (!empty($reply_ids)) {
                                $dhfsql = "DELETE FROM ppz_reply WHERE repid IN (" . implode(",", $reply_ids) . ")";
                                $conn->query($dhfsql);
                            }
                        }

                        // 删除数据库信息
                        $sqlnew = "DELETE FROM ppz_row WHERE rowid = ?";
                        $stmt = $conn->prepare($sqlnew);
                        $stmt->bind_param("i", $id);
                        $stmt->execute();
                        $stmt->close();
                    }

                    // 提交事务
                    $conn->commit();
                    echo 200;
                } catch (Exception $e) {
                    // 回滚事务
                    $conn->rollback();
                    echo 500;
                } finally {
                    $conn->autocommit(TRUE);
                }
            }
        } else {
            echo 500;
        }
    }
    mysqli_close($conn); // 关闭数据库连接
}
?>