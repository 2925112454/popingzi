<?php
// 将响应转换为JSON格式并输出  
header('Content-Type: application/json');
session_start(); // 开始 Session 会话
include $_SERVER['DOCUMENT_ROOT'].'/api/sessionnotice.php'; // SESSION变量

// 安全检查和权限验证
if (empty($ppzusername)) {
    echo json_encode(['code' => 500, 'msg' => '错误操作']);
    exit;
}

// 数据库连接
include $_SERVER['DOCUMENT_ROOT'] . '/inc/conn.php';

try {
    // 验证用户权限
    $stmt = $conn->prepare("SELECT ustatus FROM ppz_newusername WHERE binary uusername = ?");
    $stmt->bind_param("s", $ppzusername);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows !== 1) {
        throw new Exception('错误操作');
    }
    
    $user = $result->fetch_assoc();
    $ustatus = $user['ustatus'];
    
    // 只有站长(4)和副站长(3)有权限
    if ($ustatus != 4 && $ustatus != 3) {
        throw new Exception('错误操作');
    }
    
    $stmt->close();
    
    // 输入验证
    $input = filter_input_array(INPUT_POST, [
        'url' => FILTER_SANITIZE_URL,
        'number' => FILTER_VALIDATE_INT,
        'xml' => FILTER_VALIDATE_INT,
        'txt' => FILTER_VALIDATE_INT,
        'mapnewrow' => FILTER_VALIDATE_INT,
        'maplink' => FILTER_VALIDATE_INT,
        'mapindex' => FILTER_VALIDATE_INT,
        'mapsubject' => FILTER_VALIDATE_INT // 新增话题页参数验证
    ]);
    
    // 确保必要的参数存在
    $url = isset($input['url']) ? trim($input['url']) : '';
    $number = isset($input['number']) ? trim($input['number']) : '';
    $xml = isset($input['xml']) ? trim($input['xml']) : '';
    $txt = isset($input['txt']) ? trim($input['txt']) : '';
    $mapnewrow = isset($input['mapnewrow']) ? trim($input['mapnewrow']) : '';
    $maplink = isset($input['maplink']) ? trim($input['maplink']) : '';
    $mapindex = isset($input['mapindex']) ? trim($input['mapindex']) : '';
    $mapsubject = isset($input['mapsubject']) ? trim($input['mapsubject']) : '';
    
    // 文件路径常量
    define('SITEMAP_XML', $_SERVER['DOCUMENT_ROOT'] . '/sitemap.xml');
    define('SITEMAP_TXT', $_SERVER['DOCUMENT_ROOT'] . '/sitemap.txt');
    
    // 验证输入参数
    if (empty($url) || empty($number) || !is_numeric($number) || 
        $number < 1000 || $number > 50000 || 
        !in_array($xml, [0, 1]) || !in_array($txt, [0, 1])||
        !in_array($mapnewrow, [0, 1])||  !in_array($maplink, [0, 1])||  !in_array($mapindex, [0, 1])||
        !in_array($mapsubject, [0, 1])) { // 新增话题页参数验证
        throw new Exception('参数不正确');
    }
    
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        throw new Exception('网站URL格式错误！');
    }
    
    if ($xml == 0 && $txt == 0) {
        throw new Exception('至少选择一个文件格式！');
    }
    if ($mapnewrow == 0 && $maplink == 0 && $mapindex == 0 && $mapsubject == 0) { // 新增话题页判断
        throw new Exception('至少选择一个页面！');
    }
    
    $nowdate = date('Y-m-d');
    $success = false;
    
    // 检查XML文件更新频率
    if ($xml == 1) {
        if (file_exists(SITEMAP_XML)) {
            $filetime_xml = date('Y-m-d', filemtime(SITEMAP_XML));
            $filesize_xml = filesize(SITEMAP_XML);
            
            if ($filetime_xml >= $nowdate && $filesize_xml > 0) {
                throw new Exception('XML今天已经更新过了，请明天再来！');
            }
        }
    }
    
    // 检查TXT文件更新频率
    if ($txt == 1) {
        if (file_exists(SITEMAP_TXT)) {
            $filetime_txt = date('Y-m-d', filemtime(SITEMAP_TXT));
            $filesize_txt = filesize(SITEMAP_TXT);
            
            if ($filetime_txt >= $nowdate && $filesize_txt > 0) {
                throw new Exception('TXT今天已经更新过了，请明天再来！');
            }
            
            // 清空现有TXT文件
            file_put_contents(SITEMAP_TXT, '');
        }
    }
    $sitemapEntries = 0;//统计生成总数量
    $linkEntries= 0;//统计列表数量
    if($mapindex==1){
        $sitemapEntries = 1;
        $linkEntries= 1;
    }
    
    //获取列表
    $sqlli="select * from ppz_link order by linkid ASC";
    $resultli = $conn->query($sqlli);
    $linkItems = [];
     if ($xml == 1||  $txt == 1) {
        if(mysqli_num_rows($resultli) > 0){
            while ($rowli = mysqli_fetch_assoc($resultli)) {
                $linkItems[] = $rowli;
                if($maplink==1){$sitemapEntries++;$linkEntries++;}
            }
        }
     }

    // 计算剩余可分配条数（总条数 - 列表页 - 首页）
    $remainNumber = $number - $linkEntries;
    $remainNumber = $remainNumber < 0 ? 0 : $remainNumber;
    
    // 初始化文章和话题数量
    $articleNumber = 0;
    $subjectNumber = 0;
    
    // 获取符合条件的话题总数（仅yes=3的话题）
    $subjectTotal = 0;
    if($mapsubject == 1){
        $stmtSubjectCount = $conn->prepare("SELECT COUNT(*) as total FROM ppz_subject WHERE yes = 3");
        $stmtSubjectCount->execute();
        $resultSubjectCount = $stmtSubjectCount->get_result();
        $subjectCountRow = $resultSubjectCount->fetch_assoc();
        $subjectTotal = $subjectCountRow['total'];
        $stmtSubjectCount->close();
    }
    
    // 获取符合条件的文章总数
    $articleTotal = 0;
    if($mapnewrow == 1){
        $stmtArticleCount = $conn->prepare("SELECT COUNT(*) as total FROM ppz_row WHERE rowyes = 4");
        $stmtArticleCount->execute();
        $resultArticleCount = $stmtArticleCount->get_result();
        $articleCountRow = $resultArticleCount->fetch_assoc();
        $articleTotal = $articleCountRow['total'];
        $stmtArticleCount->close();
    }
    
    // 分配文章和话题的数量
    if($mapnewrow == 1 && $mapsubject == 1){
        // 两者都勾选的情况
        $totalAvailable = $articleTotal + $subjectTotal;
        
        if($totalAvailable <= $remainNumber){
            // 总数小于等于剩余条数，全取
            $articleNumber = $articleTotal;
            $subjectNumber = $subjectTotal;
        }else{
            // 总数超过剩余条数，按规则分配
            $half = floor($remainNumber / 2);
            // 先分配话题
            if($subjectTotal <= $half){
                $subjectNumber = $subjectTotal;
                $articleNumber = $remainNumber - $subjectNumber;
            }else{
                $subjectNumber = $half;
                $articleNumber = $remainNumber - $half;
            }
            // 确保文章数量不超过实际总数
            $articleNumber = $articleNumber > $articleTotal ? $articleTotal : $articleNumber;
        }
    }else if($mapnewrow == 1){
        // 只勾选文章
        $articleNumber = $remainNumber;
    }else if($mapsubject == 1){
        // 只勾选话题
        $subjectNumber = $remainNumber;
    }
    
    // 确保数量不超过实际数据量
    $articleNumber = $articleNumber > $articleTotal ? $articleTotal : $articleNumber;
    $subjectNumber = $subjectNumber > $subjectTotal ? $subjectTotal : $subjectNumber;
    $articleNumber = $articleNumber < 0 ? 0 : $articleNumber;
    $subjectNumber = $subjectNumber < 0 ? 0 : $subjectNumber;

    // 初始化XML内容
    $xmlContent = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

    // 处理首页
    if ($xml == 1 && $mapindex == 1) {
        $xmlContent .= "  <url>\n";
        $xmlContent .= "    <loc>" . htmlspecialchars($url, ENT_XML1, 'UTF-8') . "</loc>\n";
        $xmlContent .= "    <lastmod>" . date('Y-m-d\TH:i:sP',time()). "</lastmod>\n";
        $xmlContent .= "    <changefreq>monthly</changefreq>\n";
        $xmlContent .= "    <priority>1.0</priority>\n";
        $xmlContent .= "  </url>\n";
    }
    
    // 处理列表页
    if ($xml == 1 && $maplink == 1 && !empty($linkItems)) {
        foreach ($linkItems as $item) {
            $urlli = $url."/list.php?id=".$item['linkid'];
            $xmlContent .= "  <url>\n";
            $xmlContent .= "    <loc>" . htmlspecialchars($urlli, ENT_XML1, 'UTF-8') . "</loc>\n";
            $xmlContent .= "    <lastmod>" . date('Y-m-d\TH:i:sP', time()) . "</lastmod>\n";
            $xmlContent .= "    <changefreq>monthly</changefreq>\n";
            $xmlContent .= "    <priority>0.6</priority>\n";
            $xmlContent .= "  </url>\n";
        }
    }

    // 处理话题页
    if($mapsubject == 1 && $subjectNumber > 0){
        $stmtSubject = $conn->prepare("SELECT id, time FROM ppz_subject WHERE yes = 3 ORDER BY id DESC LIMIT ?");
        $stmtSubject->bind_param("i", $subjectNumber);
        $stmtSubject->execute();
        $resultSubject = $stmtSubject->get_result();
        
        while ($row = $resultSubject->fetch_assoc()) {
            $subjectId = $row['id'];
            $subjectTime = $row['time'];
            $subjectUrl = rtrim($url, '/') . "/subject/detail.php?id=" . $subjectId;
            
            // 构建XML条目
            if ($xml == 1) {
                $xmlContent .= "  <url>\n";
                $xmlContent .= "    <loc>" . htmlspecialchars($subjectUrl, ENT_XML1, 'UTF-8') . "</loc>\n";
                $xmlContent .= "    <lastmod>" . date('Y-m-d\TH:i:sP', strtotime($subjectTime)) . "</lastmod>\n";
                $xmlContent .= "    <changefreq>daily</changefreq>\n";
                $xmlContent .= "    <priority>0.7</priority>\n";
                $xmlContent .= "  </url>\n";
            }
            
            // 添加到TXT文件
            if ($txt == 1) {
                file_put_contents(SITEMAP_TXT, $subjectUrl . "\n", FILE_APPEND);
            }
            
            $sitemapEntries++;
        }
        $stmtSubject->close();
    }

    // 处理文章
    if($mapnewrow == 1 && $articleNumber > 0){
        $stmtArticle = $conn->prepare("SELECT rowid, rowtexe, rowtime FROM ppz_row WHERE rowyes = 4 ORDER BY rowid DESC LIMIT ?");
        $stmtArticle->bind_param("i", $articleNumber);
        $stmtArticle->execute();
        $resultArticle = $stmtArticle->get_result();
        
        while ($row = $resultArticle->fetch_assoc()) {
            $rowid = $row['rowid'];
            $rowtime = $row['rowtime'];
            $rowurl = rtrim($url, '/') . "/show.php?id=" . $rowid;
            
            // 构建XML条目
            if ($xml == 1) {
                $xmlContent .= "  <url>\n";
                $xmlContent .= "    <loc>" . htmlspecialchars($rowurl, ENT_XML1, 'UTF-8') . "</loc>\n";
                $xmlContent .= "    <lastmod>" . date('Y-m-d\TH:i:sP', strtotime($rowtime)) . "</lastmod>\n";
                $xmlContent .= "    <changefreq>daily</changefreq>\n";
                $xmlContent .= "    <priority>0.8</priority>\n";
                $xmlContent .= "  </url>\n";
            }
            
            // 添加到TXT文件
            if ($txt == 1) {
                file_put_contents(SITEMAP_TXT, $rowurl . "\n", FILE_APPEND);
            }
            
            $sitemapEntries++;
        }
        $stmtArticle->close();
    }

    // 处理列表页TXT
    if ($txt == 1 && $maplink == 1 && !empty($linkItems)) {
        foreach ($linkItems as $itemx) {
            $urllix = $url."/list.php?id=".$itemx['linkid'];
            file_put_contents(SITEMAP_TXT, $urllix . "\n", FILE_APPEND);
        }
    }
    
    // 处理首页TXT
    if ($txt == 1 && $mapindex == 1) {
        file_put_contents(SITEMAP_TXT, $url . "\n", FILE_APPEND);
    }
    
    // 保存XML文件
    if ($xml == 1) {
        $xmlContent .= "</urlset>";
        if (file_put_contents(SITEMAP_XML, $xmlContent) !== false) {
            $success = true;
        }
    }
    
    // 检查TXT文件生成状态
    if ($txt == 1 && $sitemapEntries > 0) {
        $success = true;
    }
    
    if ($success) {
        echo json_encode(['code' => 200, 'msg' => '', 'count' => $sitemapEntries]);
    } else {
        throw new Exception('生成失败');        
    }
    
} catch (Exception $e) {
    // 错误处理
    echo json_encode(['code' => 500, 'msg' => $e->getMessage()]);
} finally {
    // 确保数据库连接关闭
    if (isset($conn)) {
        $conn->close();
    }
}
?>