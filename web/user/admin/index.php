<?php
if ($admin==1 && $typeuser==0 && ($allvip==4 || $allvip==3) && !empty($ppzusername)){
  function checkExtension($extensionName) {
    if (extension_loaded($extensionName)) {
        return '<span class="text-green"><i class="fa fa-check" aria-hidden="true"></i>已启用</span>';
    } else {
        return '<span class="text-red"><i class="fa fa-times" aria-hidden="true"></i>未启用</span>';
    }
  }
  // 初始化扩展状态变量
  $Mysqliyes = checkExtension('mysqli');
  $GDyes = checkExtension('gd');
  $Mbstringyes = checkExtension('mbstring');
  $Fileinfoyes = checkExtension('fileinfo');
  $Opensslyes = checkExtension('openssl');
  $Curlyes = checkExtension('curl');
//计算获取网站根目录所占空间
function getDirectorySize($directory) {
  $size = 0;
  $iterator = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS),
      RecursiveIteratorIterator::SELF_FIRST
  );

  foreach ($iterator as $file) {
      if ($file->isFile() && !$file->isLink()) {
          $size += $file->getSize();
      }
  }

  return $size;
}
if ($webmaxsize==0||empty($webmaxsize)){
$websize=0;
$directory = $_SERVER['DOCUMENT_ROOT'];
$sizemb = getDirectorySize($directory);
$size = round($sizemb / 1024 / 1024, 2);
$allsize = 0;
$allsize2 = 0;
$webmaxsizetext="<span class='webmaxsizespan'>你还没配置最大存储空间数额，请点击右侧数值进行设置！----></span>";
}else{
//获取分配的空间大小(后台设置)
$websizes=1073741824 * $webmaxsize;//转字节
$websize=round($webmaxsize);//绝对值
$directory = $_SERVER['DOCUMENT_ROOT'];//获取根目录
$sizemb = getDirectorySize($directory);//获取根目录所占空间大小，单位：字节(B)
$size = round($sizemb / 1024 / 1024, 2);//将单位字节(B)转换为兆(MB)
//计算分配空间和所占空间的百分比
$allsize = round($sizemb / $websizes * 100, 2);
$allsize2 = round(100 - $allsize, 2);//计算剩余空间
$webmaxsizetext="";
}

// 记录脚本开始执行的时间  
$startTime = microtime(true);  
$startMemory = memory_get_usage();    
// 执行代码
$a=1;
for($i = 1; $i <=10000; $i++){
  $a++;
}
$peakMemory = memory_get_peak_usage(); // 获取脚本执行期间的内存峰值，单位字节
// 记录脚本结束执行的时间  
$endTime = microtime(true);  
// 计算脚本执行时间（以秒为单位）  
$executionTime = $endTime - $startTime; 
//保留小数点后四位
$executionTimes = round($executionTime,4);
//转换内容峰值的单位为MB
$peakMemorymb = round($peakMemory/1024/1024,2);
// 查询数据库大小  
$sqlsize = "SELECT SUM(data_length + index_length) / 1024 / 1024 AS size_in_mb FROM information_schema.TABLES WHERE table_schema = '$mysql_database';";  
$resultsize = $conn->query($sqlsize);  
if ($resultsize) {  
    $rowsize = $resultsize->fetch_assoc();  
    $mysqlsize =  round($rowsize['size_in_mb'],2) . " MB";  
} else {  
   $mysqlsize =  "查询失败";
}
//获取会员总数量
$vipsize_sql = "SELECT * FROM ppz_newusername";
$vipsize_resulta = mysqli_query($conn,$vipsize_sql); 
$vipsize = mysqli_num_rows($vipsize_resulta); 
//付费会员总数
$vipsizetime_sql = "SELECT * FROM ppz_newusername WHERE uviptime IS NOT NULL AND uviptime > NOW()";//获取会员时间大于当前时间的会员
$vipsizetime_resulta = mysqli_query($conn,$vipsizetime_sql); 
$vipsizetime = mysqli_num_rows($vipsizetime_resulta); 
//计算会员总数和付费会员之间的百分比
$vipsizetime_percent = round(($vipsizetime/$vipsize)*100,2);
//获取文章总数量
$rowsize_sql = "SELECT * FROM ppz_row";
$rowsize_resulta = mysqli_query($conn,$rowsize_sql); 
$rowsize = mysqli_num_rows($rowsize_resulta); 
//获取投稿待审核数量
$subnosize_sql = "SELECT * FROM ppz_row WHERE rowyes = 1";
$subnosize_resulta = mysqli_query($conn,$subnosize_sql); 
$subnosize = mysqli_num_rows($subnosize_resulta); 
if ($subnosize > 0){
  $subsieztext='<div class="wksiezno"><a href="?type=3">待审核：'.$subnosize.' 条</a></div>';
}else{
  $subsieztext='';
}
//获取文章评论总数量
$rowplsize_sql = "SELECT * FROM ppz_commentary";
$rowplsize_resulta = mysqli_query($conn,$rowplsize_sql); 
$rowplsize = mysqli_num_rows($rowplsize_resulta); 
//获取公告评论数量
$ggplsize_sql = "SELECT * FROM ppz_ggcommentary";
$ggplsize_resulta = mysqli_query($conn,$ggplsize_sql); 
$ggplsize = mysqli_num_rows($ggplsize_resulta); 
//计算总评论数量
$plsize = $rowplsize + $ggplsize;
//获取工单总数
$wksize_sql = "SELECT * FROM ppz_work";
$wksize_resulta = mysqli_query($conn,$wksize_sql); 
$wksize = mysqli_num_rows($wksize_resulta); 
//获取未处理工单数量
$wknosize_sql = "SELECT * FROM ppz_work WHERE wkyes = 1";
$wknosize_resulta = mysqli_query($conn,$wknosize_sql); 
$wknosize = mysqli_num_rows($wknosize_resulta);
if ($wknosize > 0){
$wksieztext='<div class="wksiezno"><a href="?type=7">待处理：'.$wknosize.' 条</a></div>';
}else{
  $wksieztext='';
}
//获取性别为男的会员数量
$mansize_sql = "SELECT * FROM ppz_newusername WHERE usex = 1";
$mansize_resulta = mysqli_query($conn,$mansize_sql);
$mansize = mysqli_num_rows($mansize_resulta);//获取性别为男的会员数量
if ($mansize > 0){
$mansizetext=$mansize;
}else{
  $mansizetext=0;
}
//获取性别为女的会员数量
$womansize_sql = "SELECT * FROM ppz_newusername WHERE usex = 2";
$womansize_resulta = mysqli_query($conn,$womansize_sql);
$womansize = mysqli_num_rows($womansize_resulta);//获取性别为女的会员数量
if ($womansize > 0){
$womansizetext=$womansize;
}else{
  $womansizetext=0;
}
// 获取当前时间的年份  
$nowyear = date('Y');  
// 初始化一个数组来存储每年会员的数量  
$yearly_member_counts = array();  
// 循环遍历过去9年（包括当前年份）  
for ($i = 0; $i < 10; $i++) {  
    $year = $nowyear - $i; // 计算年份
    // 构建SQL查询语句  
    $year_sql = "SELECT COUNT(*) as count FROM ppz_newusername WHERE YEAR(utime) = $year";   
    // 执行查询  
    $year_result = mysqli_query($conn, $year_sql);  
    // 检查查询结果  
    if ($year_result) {  
        $row = mysqli_fetch_assoc($year_result);  
        $yearly_member_counts[$year] = $row['count']; // 将结果存入数组  
    } else {  
        $yearly_member_counts[$year] = 0; // 如果查询失败，则假定该年份会员数为0  
    }  
}  

//获取最新的10个会员信息
$newusersql="SELECT * FROM ppz_newusername WHERE ustatus = 1 ORDER BY uid DESC LIMIT 10";
$newuserresult=mysqli_query($conn,$newusersql);
$newusersize=mysqli_num_rows($newuserresult);
//获取最新的10条文章信息
$newarticlessql="SELECT * FROM ppz_row ORDER BY rowid DESC LIMIT 10";
$newarticlesresult=mysqli_query($conn,$newarticlessql);
$newarticlesize=mysqli_num_rows($newarticlesresult);

  echo '
  <div class="all-div nocopy">
  <div class="all-top">
  <div class="allu all-user"><div class="all-left"><i class="fa fa-user"></i></div><div class="all-right"><div class="all-text">用户总数</div><div class="all-size">'.$vipsize.'</div></div></div>
  <div class="allu all-row">'.$subsieztext.'<div class="all-left"><i class="fa fa-file-text"></i></div><div class="all-right"><div class="all-text">文章总数</div><div class="all-size">'.$rowsize.'</div></div></div>
  <div class="allu all-comm"><div class="all-left"><i class="fa fa-comments"></i></div><div class="all-right"><div class="all-text">评论总数</div><div class="all-size">'.$plsize.'</div></div></div>
  <div class="allu all-case">'.$wksieztext.'<div class="all-left"><i class="fa fa-suitcase"></i></div><div class="all-right"><div class="all-text">工单总数</div><div class="all-size">'.$wksize.'</div></div></div>
  </div>


  <div class="all-center alltop"><div class="all-table-left"><div class="all-table-text-left"><span>丨</span>近七日用户注册统计</div>';
  
// 构造SQL查询语句，获取前7天的会员注册数量  
$sql = "SELECT DATE(utime) AS registration_date, COUNT(*) AS count  
        FROM ppz_newusername  
        WHERE utime >= CURDATE() - INTERVAL 7 DAY  
        GROUP BY registration_date ASC
        ORDER BY registration_date ASC";  
  
$result = $conn->query($sql);  
  
if ($result->num_rows > 0) {  
    // 初始化变量来保存日期和计数  
    $dates = [];  
    $counts = [];  
    $previousCount = 0;  
      
    // 4. 遍历结果集并获取日期和数量  
    while ($row = $result->fetch_assoc()) {  
        $dates[] = $row['registration_date'];  
        $counts[] = $row['count'];  
          
        // 5. 计算箭头方向（如果有前一天的数据）  
        if (isset($previousCount) && $row['count'] > $previousCount) {  
            $directions[] = '↑';  
        } elseif (isset($previousCount) && $row['count'] < $previousCount) {  
            $directions[] = '↓';  
        } else {  
            $directions[] = '-';  
        }  
          
        $previousCount = $row['count']; // 更新前一天的数量  
    }  
      
    // 6. 输出表格  
    echo '<table class="all-bottm-table">';  
    echo '<tr class="all-bottm-table-tr"><th class="all-bottm-table-th">注册日期</th><th class="all-bottm-table-th">注册数量</th><th class="all-bottm-table-th">趋向</th></tr>';  
      
    for ($i = 0; $i < count($dates); $i++) {  
        $arrowColor = ($directions[$i] == '↑') ? 'green' : (($directions[$i] == '↓') ? 'red' : 'black');  
        echo "<tr class='all-bottm-table-tr'>  
                <td class='all-bottm-table-td'>{$dates[$i]}</td>  
                <td class='all-bottm-table-td'>{$counts[$i]}</td>  
                <td class='all-bottm-table-td' style='color:{$arrowColor};'>{$directions[$i]}</td>  
              </tr>";  
    }  
      
    echo '</table>';  
} else {  
    echo "<div class='nullday'>七日内暂无新注册用户</div>";  
}


  echo '</div>
  <div class="all-table-right"><div class="all-table-text-right"><span>丨</span>近七日发布文章统计</div>';
  // 构造SQL查询语句，获取前7天的文章发布数量  
$dayrowsql = "SELECT DATE(rowtime) AS post_date, COUNT(*) AS post_count  
FROM ppz_row  
WHERE rowtime >= CURDATE() - INTERVAL 7 DAY  
GROUP BY post_date  
ORDER BY post_date ASC";  

$dayrowresult = $conn->query($dayrowsql);  

if ($dayrowresult->num_rows > 0) {  
// 初始化变量来保存日期和计数  
$postDates = [];  
$postCounts = [];  
$previousCount = 0;  
$directions = [];  

// 遍历结果集并获取日期和数量  
while ($dayrow = $dayrowresult->fetch_assoc()) {  
$postDates[] = $dayrow['post_date'];  
$postCounts[] = $dayrow['post_count'];  

// 计算箭头方向（如果有前一天的数据）  
if (isset($previousCount) && $dayrow['post_count'] > $previousCount) {  
    $directions[] = '↑';  
} elseif (isset($previousCount) && $dayrow['post_count'] < $previousCount) {  
    $directions[] = '↓';  
} else {  
    $directions[] = '-';  
}  

$previousCount = $dayrow['post_count']; // 更新前一天的数量  
}  

// 输出表格  
echo '<table class="all-bottm-table">';  
echo '<tr class="all-bottm-table-tr"><th class="all-bottm-table-th">发布日期</th><th class="all-bottm-table-th">发布数量</th><th class="all-bottm-table-th">趋向</th></tr>';  

for ($i = 0; $i < count($postDates); $i++) {  
$arrowColor = ($directions[$i] == '↑') ? 'green' : (($directions[$i] == '↓') ? 'red' : 'black');  
echo "<tr class='all-bottm-table-tr'>  
        <td class='all-bottm-table-td'>{$postDates[$i]}</td>  
        <td class='all-bottm-table-td'>{$postCounts[$i]}</td>  
        <td class='all-bottm-table-td' style='color:{$arrowColor};'>{$directions[$i]}</td>  
      </tr>";  
}  

echo '</table>';  
} else {  
echo "<div class='nullday'>七日内暂无新增文章</div>";  
}
$uploadMax = ini_get('upload_max_filesize');  // 获取上传文件大小的最大值
$postMaxSize = ini_get('post_max_size');// 获取POST请求的最大值

// 判断HTTPS是否启用及证书安全性
$isHttps = false;
$isCertValid = false; // 证书是否安全
$httpsStatus = '';
$sslCertInfo = [];
$domain = $_SERVER['SERVER_NAME'];

if (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
    || $_SERVER['SERVER_PORT'] == 443
) {
    $isHttps = true;
    
    // 1. 先验证证书是否受信任（核心修复点）
    $isCertTrusted = false;
    $contextTrust = stream_context_create([
        'ssl' => [
            'verify_peer' => true,      // 验证证书
            'verify_peer_name' => true, // 验证域名
            'allow_self_signed' => false // 禁止自签名证书
        ]
    ]);
    // 尝试连接（验证信任链）
    $fpTrusted = @stream_socket_client("ssl://{$domain}:443", $errno, $errstr, 5, STREAM_CLIENT_CONNECT, $contextTrust);
    if ($fpTrusted) {
        $isCertTrusted = true;
        fclose($fpTrusted);
    }
    
    // 2. 再获取证书详情（有效期、域名）
    $context = stream_context_create([
        'ssl' => [
            'capture_peer_cert' => true,
            'verify_peer' => false, 
            'verify_peer_name' => false
        ]
    ]);
    $fp = @stream_socket_client("ssl://{$domain}:443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
    $isTimeAndDomainValid = false; // 有效期+域名是否有效
    if ($fp) {
        $params = stream_context_get_params($fp);
        if (!empty($params['options']['ssl']['peer_certificate'])) {
            $cert = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
            if ($cert) {
                // 验证有效期
                $nowTime = time();
                $validFrom = $cert['validFrom_time_t'];
                $validTo = $cert['validTo_time_t'];
                $isTimeValid = ($nowTime >= $validFrom && $nowTime <= $validTo);
                
                // 验证域名匹配
                $isDomainValid = false;
                $certDomains = [];
                if (!empty($cert['extensions']['subjectAltName'])) {
                    $san = $cert['extensions']['subjectAltName'];
                    preg_match_all('/DNS:([^\s,]+)/', $san, $matches);
                    if (!empty($matches[1])) {
                        $certDomains = $matches[1];
                    }
                }
                if (empty($certDomains) && !empty($cert['subject']['CN'])) {
                    $certDomains[] = $cert['subject']['CN'];
                }
                foreach ($certDomains as $certDomain) {
                    $certDomain = strtolower(trim($certDomain));
                    $currentDomain = strtolower($domain);
                    if (strpos($certDomain, '*') === 0) {
                        $wildcardDomain = ltrim($certDomain, '*');
                        if (substr($currentDomain, -strlen($wildcardDomain)) === $wildcardDomain) {
                            $isDomainValid = true;
                            break;
                        }
                    } elseif ($certDomain === $currentDomain) {
                        $isDomainValid = true;
                        break;
                    }
                }
                
                $isTimeAndDomainValid = $isTimeValid && $isDomainValid;
                
                // 解析证书信息
                $issuerCN = isset($cert['issuer']['CN']) ? $cert['issuer']['CN'] : '';
                $issuerO = isset($cert['issuer']['O']) ? $cert['issuer']['O'] : '';
                // 拼接显示：优先CN，有O则补充(O名称)
                if (!empty($issuerCN) && !empty($issuerO)) {
                    $issuer = $issuerCN . '（' . $issuerO . '）';
                } elseif (!empty($issuerCN)) {
                    $issuer = $issuerCN;
                } elseif (!empty($issuerO)) {
                    $issuer = $issuerO;
                } else {
                    $issuer = '未知';
                }
                
                $sslCertInfo = [
                    'issuer' => $issuer,
                    'validFrom' => date('Y-m-d H:i:s', $validFrom),
                    'validTo' => date('Y-m-d H:i:s', $validTo)
                ];
            }
        }
        fclose($fp);
    }
    
    // 3. 最终判定：信任链+有效期+域名 都有效才算安全
    $isCertValid = $isCertTrusted && $isTimeAndDomainValid;
    
    // 设置显示状态
    if ($isCertValid) {
        $httpsStatus = '<span class="text-green"><i class="fa fa-check" aria-hidden="true"></i>已启用</span>';
    } else {
        $httpsStatus = '<span class="text-red"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>不安全</span>';
    }
    
    $sslIssuer = isset($sslCertInfo['issuer']) ? $sslCertInfo['issuer'] : '获取失败';
    $sslValidFrom = isset($sslCertInfo['validFrom']) ? $sslCertInfo['validFrom'] : '获取失败';
    $sslValidTo = isset($sslCertInfo['validTo']) ? $sslCertInfo['validTo'] : '获取失败';
} else {
    $isHttps = false;
    $httpsStatus = '<span class="text-red"><i class="fa fa-times" aria-hidden="true"></i>未启用</span>';
    $sslIssuer = '-';
    $sslValidFrom = '-';
    $sslValidTo = '-';
}

  echo '</div></div>

  <div class="all-center"><div class="all-width" style="width:'.$vipsizetime_percent.'%;"></div><div class="all-vip" >VIP会员 [占比：'.$vipsizetime_percent.'%]</div><div class="all-vipsize Goclick" data-url="/user/popingzi.php?type=4&k=%VIP会员%">'.$vipsizetime.'个</div></div>
  <div class="all-center"><div class="all-width gb" style="width:'.$allsize.'%;"></div><div class="all-vip">空间容量 [ 剩余：'.$allsize2.' % ]</div>'.$webmaxsizetext.'<div id="webmaxsize" class="all-vipsize" data-max="'.$websize.'">'.$size.'MB / '.$websize.'GB</div></div>
  <dialog id="webmaxsizedialog"><a id="webmaxsizeclose"><i class="fa fa-times" aria-hidden="true"></i></a><b>设置最大储存空间数值</b><input type="number" min="1" max="999999999" maxlength="9" id="webmaxsizeinput" placeholder="0"><i class="maxsizegb">GB</i><button id="webmaxsizebut">保存</button><span class="maxnot" id="webmaxsizeerr">此设置仅作为空间统计使用</span></dialog>
  <script src="/style/js/webmaxsize.js"></script>
  <div class="all-bottom">
  <table>
  <tr>  
        <td><span>服务器软件：</span>'. $_SERVER['SERVER_SOFTWARE'] .'</td>  
</tr>
<tr> 
<td><span>PHP版本：</span>'.PHP_VERSION.'</td>  
<td><span>操作系统：</span>'.PHP_OS.'</td>  
</tr>
<tr>  
    <td><span>服务器IP：</span>'.$_SERVER['SERVER_ADDR'].'</td>  
    <td><span>服务器域名：</span>'.$_SERVER['SERVER_NAME'].'</td>  
</tr>
<tr>  
    <td><span>脚本执行时间：</span>'.$executionTimes.'s</td>  
    <td><span>服务器时间：</span><span id="webtime"></span></td>  
</tr>
<tr>  
    <td><span>Mysql版本：</span>'.$conn->server_info.'</td>  
    <td><span>内存峰值：</span>'.$peakMemorymb.'MB</td>  
</tr>
<tr>  
    <td><span>Mysql大小：</span>'.$mysqlsize.'</td>  
    <td><span>内存使用情况：</span>'.round(memory_get_usage() / (1024 * 1024), 2).'MB</td>  
</tr>
<tr>  <td><span>POST请求最大值：</span>'.$postMaxSize.'</td> 
    <td><span>内存限制：</span>'.ini_get('memory_limit').'</td>
     
</tr>
<tr>  
    <td><span>Mysqli扩展：</span>'.$Mysqliyes.'</td>  
    <td><span>GD扩展：</span>'.$GDyes.'</td>
</tr>
<tr>
    <td><span>Mbstring扩展：</span>'.$Mbstringyes.'</td>
    <td><span>Fileinfo扩展：</span>'.$Fileinfoyes.'</td>  
</tr>
<tr>
    <td><span>Openssl扩展：</span>'.$Opensslyes.'</td>
    <td><span>Curl扩展：</span>'.$Curlyes.'</td>  
</tr>
<tr>
    <td><span>Https安全传输：</span>'.$httpsStatus.'</td>
    <td><span>SSL颁发组织：</span>'.$sslIssuer.'</td>
</tr>
<tr>
    <td><span>SSL颁发日期：</span>'.$sslValidFrom.'</td>
    <td><span>SSL截止日期：</span>'.$sslValidTo.'</td>
</tr>
<tr>
    <td><span>上传目录权限：</span><span id="uploadisphpetext"><a id="uploadisphpe">点击进行查询</a></span></td>
     <td><span>服务器端口：</span>'.$_SERVER['SERVER_PORT'].'</td>
</tr>
<tr>
  <td><span>上传文件最大值：</span>'.$uploadMax.'</td>  
    
    <td><span>服务器协议：</span>'. $_SERVER['SERVER_PROTOCOL'].'</td>
</tr>
<tr>
    <td><span>文件结构检测：</span><span id="uploadisphpetextfile"><a id="uploadisphpefile">点击进行扫描</a></span></td>
</tr>
<script src="/style/js/uploadisphpe.js" type="text/javascript"></script>
<script src="/style/js/uploadisphpefile.js" type="text/javascript"></script>
  </table>
  </div>

  <div class="all-bottom bottomgrid">
    <div class="all-bottom-sex">  
            <div class="all-bottom-sex-text">用户性别比例</div>
            <script src="/style/js/d3/d3js.org_d3.v6.min.js"></script>
            <svg id="pie-chart" width="200" height="200"></svg> 
            <script>var sexman ='.$mansizetext.';var sexgirl = '.$womansizetext.';</script>
            <script src="/style/js/svgsex.js" type="text/javascript"></script>
    </div>
            <div class="all-bottom-yaer">  
            <div class="all-bottom-sex-text">近10年注册用户数量</div>
            <div class="all-bottom-yaer-show"><div id="chart" class="chart"></div></div>
            <script>var toyear = {';foreach ($yearly_member_counts as $year => $count) {echo ''.$year.':'.$count.',';}echo '};</script>
            <script src="/style/js/svgyaer.js" type="text/javascript"></script>
            </div>
  </div>

  <div class="all-bottom towgrid">
    <div class="all-bottom-left">
     <div class="all-bottom-left-text">最新用户<a href="?type=4">查看全部</a></div>
      <ul>';
      if ($newusersize>0){
        while($newuserrow=mysqli_fetch_assoc($newuserresult)){
          $newuserid=$newuserrow["uid"];//id
          $newusername=$newuserrow["uname"];//昵称
          $newusernamesize=$newuserrow["uusername"];//账号
          $newuserimg=$newuserrow["uimg"];//头像
        if (is_null($newuserimg) || $newuserimg==""){
            $userimg="/images/web/default.jpg";
        }else{
            $userimg=$newuserimg;
        }
        echo '<li><a class="all-bottom-a" href="?type=4&sid='.$newuserid.'"><img src="'.$userimg.'" alt="" />['.$newusernamesize.'] '.$newusername.'</a><div class="all-bottom-left-delete"><a href="/user.php?id='.$newuserid.'" target="_blank">查看</a><a href="?type=4&sid='.$newuserid.'">编辑</a></div></li>';
        }
      }else{
        echo '<li><a class="all-bottom-a" href="?type=4&uid=1"><img src="/images/web/default.jpg" alt="" />暂无用户注册</a><div class="all-bottom-left-delete"><a>查看</a><a>编辑</a></div></li>';
      }
    echo '
      </ul>
    </div>
    <div class="all-bottom-right">
    <div class="all-bottom-left-text">最新文章<a href="?type=3">查看全部</a></div>
      <ul>';
      if ($newarticlesize>0){
        while($nerow=mysqli_fetch_assoc($newarticlesresult)){
          $nowrowid=$nerow["rowid"];//id
          $nowrowtext=$nerow["rowtexe"];//标题
          $nowrowyes=$nerow["rowyes"];//审核状态，1待审核，2未通过，3待修改，4已通过
          $nowrowfl=$nerow["rowfl"];//分类
          
          $nowrowflsql="SELECT * FROM ppz_fl WHERE flid=$nowrowfl";//查询分类
          $nowrowflresult=mysqli_query($conn,$nowrowflsql);
          if(mysqli_num_rows($nowrowflresult)>0){//判断是否含有分类
            while($nowrowflrow=mysqli_fetch_assoc($nowrowflresult)){
              $nowrowflname=$nowrowflrow["flname"];//分类名称
              $nowrowfllingkid=$nowrowflrow["fllinkid"];//所属的列表id
              $nowrowlinksql="SELECT * FROM ppz_link WHERE linkid=$nowrowfllingkid";//查询列表
              $nowrowlinkresult=mysqli_query($conn,$nowrowlinksql);
              if(mysqli_num_rows($nowrowlinkresult)>0){//判断是否含有列表
                while($nowrowlinkrow=mysqli_fetch_assoc($nowrowlinkresult)){
                  $nowrowlinkname=$nowrowlinkrow["linkname"];//列表名称
                }
              }else{
                $nowrowlinkname="未知列表";
              }
            }
          }else{
            $nowrowflname="未知分类";
          }
          

          $nowrowyestext="";
          $nowrowyesurl='id="errLink"';

          if ($nowrowyes==1){
            $nowrowyestext="<i>待审核</i>";
            $nowrowyesurl='id="adminLink"';
          }else if($nowrowyes==2){
            $nowrowyestext="<i>未通过</i>";
            $nowrowyesurl='id="adminLink"';
          }else if($nowrowyes==3){
            $nowrowyestext="<i>已撤销</i>";
            $nowrowyesurl='id="adminLink"';
          }else if($nowrowyes==4){
            $nowrowyesurl='href="/show.php?id='.$nowrowid.'"';
          }else{
            $nowrowyestext="";
            $nowrowyesurl='id="errLink"';
          }
      echo '<li title="分类：'.$nowrowflname.'"><a class="all-bottom-a" href="?type=3&sid='.$nowrowid.'">'.$nowrowyestext.'['.$nowrowlinkname.'] '.$nowrowtext.'</a><div class="all-bottom-left-delete"><a '.$nowrowyesurl.' target="_blank">查看</a><a href="?type=3&sid='.$nowrowid.'">编辑</a></div></li>';
        }
      }else{
        echo '<li><a class="all-bottom-a">暂无文章投稿</a><div class="all-bottom-left-delete"><a>查看</a><a>编辑</a></div></li>';
      }
echo '</ul>
    </div>
  </div>

  </div>
  </div>
  <script type="text/javascript">let nowtime="'.date('Y-m-d H:i:s').'";</script>
  <script src="/style/js/webtime.js" type="text/javascript"></script>
  <script src="/style/js/Goclick.js" type="text/javascript"></script>
  ';
}else{
header("HTTP/1.1 404 Not Found");//404错误
}
?>