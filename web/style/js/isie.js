function isIE() {
    // 首先检查标准的IE内核标识
    const userAgent = navigator.userAgent;
    const isTrident = userAgent.indexOf('Trident') !== -1;
    const isMSIE = userAgent.indexOf('MSIE') !== -1;
    const isEdgeLegacy = userAgent.indexOf('Edge/') !== -1;
    
    // 检查是否存在ActiveXObject（仅IE支持）
    const hasActiveX = typeof window.ActiveXObject !== 'undefined';
    
    // 检测文档模式（兼容模式会模拟旧版本的渲染方式）
    let docMode = 0;
    if (document.documentMode) {
        docMode = document.documentMode;
    }
    
    // 组合所有检测结果
    return isTrident || isMSIE || isEdgeLegacy || hasActiveX || (docMode > 0 && docMode < 11);
}
function checkWebPSupport(callback) {
    const webp = new Image();
    webp.onload = webp.onerror = function() {
        callback(webp.height === 2);
    };
    webp.src = 'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';
}
document.addEventListener('DOMContentLoaded', function() {
if (isIE()) {
    //获取body
    const bodyie = document.getElementsByTagName("body")[0];
    //移除所有标签
    if (bodyie){
        bodyie.innerHTML = "<div style='text-align:center;margin-top:50px;'><h1>您的浏览器版本过低，请升级浏览器！</h1><p><h2>建议使用IE内核以外的浏览器进行访问</h2></p></div>";
    }else{
        //输出提示信息
        document.write("<div style='text-align:center;margin-top:50px;'><h1>您的浏览器版本过低，请升级浏览器！</h1><p><h2>建议使用IE内核以外的浏览器进行访问</h2></p></div>");
    }

}else{
    
        checkWebPSupport(function(isSupported) {
            if (!isSupported) {
            alert('您的浏览器版本过低，建议升级为最新版本！');
            }
        });

}
})