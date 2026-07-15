<?php
include $_SERVER['DOCUMENT_ROOT'].'/inc/inc.php';//通用
//获取注册配置
$regifindex_sql = "SELECT * FROM ppz_regif WHERE id=1";
$regifindex_result = mysqli_query($conn,$regifindex_sql);
$regifindexsize = mysqli_num_rows($regifindex_result);
if($regifindexsize==1){
  while($regifindex_row = mysqli_fetch_assoc($regifindex_result)){
    $indexregif=$regifindex_row['regif'];//注册状态：1开启，2关闭
    $indexregoff=$regifindex_row['regoff'];//注册方式：1开放注册，2邀请码注册
    $indexregtext=$regifindex_row['regtext'];//注册协议
  }
}
$time=date("Y年m月d日 H:i:s",time());

echo'
<!DOCTYPE html>
<html>
<meta charset="utf-8">
<title>注册协议 - '.$webtext.'丨'.$webby.'</title>
<meta name="keywords" content="'.$webpass.'" />
<meta name="description" content="'.$webvar.'" />
<link type="text/css" rel="stylesheet" href="/style/css/font-awesome-4.7.0/css/font-awesome.min.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
';
include $_SERVER['DOCUMENT_ROOT'].'/inc/style.php';
echo'
<link rel="icon" href="/favicon.ico"/>
</head>
<body>
<div class="regtxtdivall nocopy">
<div class="regtxtdiv">
    <div class="regtxt-text"><div><span>丨</span>'.$webtext.' - 注册协议</div><div><a href="/"><i class="fa fa-home"></i>返回首页</a> <a onclick="printDiv(\'divId\')"><i class="fa fa-print"></i> 打印</a></div></div>
    <div class="regtxt-row" style="text-align: justify;"  id="divId">'.nl2br($indexregtext).'</div>
</div>
</div>
<script>
function printDiv(divId) {  
    var iframe = document.createElement("iframe");  
    iframe.setAttribute("style", "position:absolute;width:0px;height:0px;");  
    document.body.appendChild(iframe);  
    var div = document.getElementById(divId);  
    var content = div.outerHTML; 
    var iframeDoc = iframe.contentWindow || iframe.contentDocument;  
    if (iframeDoc.document) {  
        iframeDoc = iframeDoc.document;  
    }  
    iframeDoc.open();  
    iframeDoc.write("<!DOCTYPE html><html><head><title></title></head><body>");
    iframeDoc.write("<div style=\"text-align:center; font-size:20px;padding:15px 0px; border-bottom:1px solid #ccc; margin-bottom:15px;\">'.$webtext.' - 注册协议</div>");  
    iframeDoc.write(content);
    iframeDoc.write("<div style=\"text-align:right; padding:15px 0px; border-top:1px solid #ccc; margin-top:30px;\">'.$webtext.'<br/>'.$time.'</div>"); 
    iframeDoc.write("</body></html>");  
    iframeDoc.close();  
    iframe.contentWindow.print();  
     setTimeout(function() {  
        document.body.removeChild(iframe);  
    }, 2000);
}  
</script>';
@include $_SERVER['DOCUMENT_ROOT'].'/wap/index.php';
echo '
<script src="/style/js/alert.js" type="text/javascript"></script>
</body>
</html>
';
?>