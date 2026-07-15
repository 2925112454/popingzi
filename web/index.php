<?php
@include $_SERVER['DOCUMENT_ROOT'].'/inc/inc.php';//通用
$indexone=200;
function safeInclude($filePath) {
    if (!file_exists($filePath)) {
        header("HTTP/1.1 404 Not Found");
        exit;
    }
}
if($indexflex==1){
    @include __DIR__.'/api/index_1.php';
    safeInclude(__DIR__.'/api/index_1.php');
}elseif($indexflex==2){
    @include __DIR__.'/api/index_2.php';
    safeInclude(__DIR__.'/api/index_2.php');
}elseif($indexflex==3){
    @include __DIR__.'/api/index_3.php';
    safeInclude(__DIR__.'/api/index_3.php');
}else{
    safeInclude(__DIR__.'/api/index_1.php');
}
?>