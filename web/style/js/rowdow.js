const diystyle={
    "百度网盘": {
        "diyname": "百度网盘", //自定义网盘名称
        "diyurl": "https://pan.baidu.com/",//自定义网盘地址
        "diymun": "00P",//自定义文件数量
        "diysize": "00.00MB",//自定义文件大小
        "diypx": "3600X5400",//自定义内容分辨率
        "diypas": "",//自定义提取码
        "diyzip": "tjdige",//自定义解压密码
    },
    "一键清空": {
        "diyname": "", //自定义网盘名称
        "diyurl": "",//自定义网盘地址
        "diymun": "",//自定义文件数量
        "diysize": "",//自定义文件大小
        "diypx": "",//自定义内容分辨率
        "diypas": "",//自定义提取码
        "diyzip": "",//自定义解压密码
    },
}
function countTemplates(obj) {  
    return Object.keys(obj).length;  
} 
const templateCount = countTemplates(diystyle);
const templateNames = Object.keys(diystyle);
let htmlOutput = '';

function containsInvisibleChars(str) {  
    // 这个正则表达式匹配空格、制表符、换行符和其他常见的不可见字符  
    const regex = /[\s\uFEFF\xA0​\u2000-\u206F\u2070-\u20CF\u2100-\u218F\u2E00-\u2EFF\u3000-\u303F\uFE00-\uFE6F\uFF00-\uFFEF]/g;  
    return regex.test(str);  
}

function isURLx(str) {
    // 添加对IPv4和FTP的支持
    var pattern = new RegExp(
        '^(ftp|https?|ftps?)://' + // 添加ftp和ftps协议
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // 域名和扩展
        '((\\d{1,3}\\.){3}\\d{1,3}))' + // IPv4地址
        '(\\:\\d+)?' + // 端口
        '(\\/[-a-z\\d%_.~+]*)*' + // 路径
        '(\\?[;&a-z\\d%_.~+=-]*)?' + // 查询字符串
        '(\\#[-a-z\\d_]*)?$', 'i' // 片段标识符
    );
    return !!pattern.test(str);
}

document.addEventListener('DOMContentLoaded', function() {

    var rowdowdiva=document.getElementById('rowdowdiva');//按钮
    var rowdowdialog=document.getElementById('rowdowdialog');//dialog弹出框
    var rowdowclose=document.getElementById('rowdowclose');//关闭按钮
    var rowdowcloseyes=document.getElementById('rowdowcloseyes');//确定按钮
    var rowdowcloseerr=document.getElementById('rowdowcloseerr');//错误提示框
    var rowdowstyle=document.getElementById('rowdowstyle');//快速插入框
    //以下为输入框获取
    const rowdownameinput=document.getElementById('rowdowname');//网盘名称
    const rowdowurlinput=document.getElementById('rowdowurl');//网盘地址
    const rowdowmuninput=document.getElementById('rowdowmun');//文件数量
    const rowdowsizeinput=document.getElementById('rowdowsize');//文件大小
    const rowdowpxinput=document.getElementById('rowdowpx');//内容分辨率
    const rowdowpasinput=document.getElementById('rowdowpas');//提取码
    const rowdowzipinput=document.getElementById('rowdowzip');//解压密码

    function setTimeouterr(text){//自定义错误提示函数
        rowdowcloseerr.innerHTML=text;
        rowdowcloseerr.style.cssText="display:block;";
        setTimeout(function(){
            rowdowcloseerr.innerHTML='';
            rowdowcloseerr.style.cssText="display:none;";
        },2000);
    }

    if (rowdowdiva&&rowdowdialog&&rowdowclose&&rowdowcloseyes){
        const bodyhiddenx=document.getElementsByTagName('body')[0];
        //点击按钮，打开弹出框
        rowdowdiva.onclick=function(){
            rowdowdialog.showModal();
            bodyhiddenx.style.cssText="overflow:hidden;";
            rowdowdialog.style.cssText="display:flex;";
            if(rowdowstyle){
                //判断是否有模板
                if(templateCount>0){
                    rowdowstyle.style.cssText="display:flex;";
                    templateNames.forEach(function(templateName) {  
                        htmlOutput += `<a class="diydowstyle" data-name="${templateName}">${templateName}</a>`;  
                    });
                    rowdowstyle.innerHTML='模板插入：<div class="rowdowstyle">'+htmlOutput+'</div>';  
                    const diydowstyle=document.getElementsByClassName('diydowstyle');
                        //点击模板插入
                        for (let i = 0; i < diydowstyle.length; i++) {
                            diydowstyle[i].onclick = function () {
                                const diydowstyleName = this.getAttribute('data-name');
                                //将模板插入对应的输入框
                                rowdownameinput.value=diystyle[diydowstyleName].diyname;
                                rowdowurlinput.value=diystyle[diydowstyleName].diyurl;
                                rowdowmuninput.value=diystyle[diydowstyleName].diymun;
                                rowdowsizeinput.value=diystyle[diydowstyleName].diysize;
                                rowdowpxinput.value=diystyle[diydowstyleName].diypx;
                                rowdowpasinput.value=diystyle[diydowstyleName].diypas;
                                rowdowzipinput.value=diystyle[diydowstyleName].diyzip;
                            }
                        }
                }
            }
        }
        //关闭弹出框
        rowdowclose.onclick=function(){
            const rowdownamex=rowdownameinput.value;
            const rowdowurlx=rowdowurlinput.value;
            const rowdowmunx=rowdowmuninput.value;
            const rowdowsizex=rowdowsizeinput.value;
            const rowdowpxx=rowdowpxinput.value;
            const rowdowpasx=rowdowpasinput.value;
            const rowdowzipx=rowdowzipinput.value;
            if(rowdownamex!=''||rowdowurlx!=''||rowdowmunx!=''||rowdowsizex!=''||rowdowpxx!=''||rowdowpasx!=''||rowdowzipx!=''){
                if (rowdownamex!=''&&rowdowurlx!=''&&rowdowmunx!=''&&rowdowsizex!=''){
                         if (isURLx(rowdowurlx)){
                                if (containsInvisibleChars(rowdownamex)){
                                    setTimeouterr('[网盘名称] 不能有空格等不可见字符');
                                }else if (containsInvisibleChars(rowdowurlx)){
                                    setTimeouterr('[下载地址] 不能有空格等不可见字符');
                                }else if(containsInvisibleChars(rowdowmunx)){
                                    setTimeouterr('[文件数量] 不能有空格等不可见字符');
                                }else if(containsInvisibleChars(rowdowsizex)){
                                    setTimeouterr('[文件大小] 不能有空格等不可见字符');
                                }else if(containsInvisibleChars(rowdowpxx)){
                                    setTimeouterr('[分辨率] 不能有空格等不可见字符');
                                }else if(containsInvisibleChars(rowdowpasx)){
                                    setTimeouterr('[提取码] 不能有空格等不可见字符');
                                }else if(containsInvisibleChars(rowdowzipx)){
                                    setTimeouterr('[解压密码] 不能有空格等不可见字符');
                                }else{
                                    rowdowdialog.close();
                                    bodyhiddenx.style.cssText="overflow:auto;";
                                    rowdowdialog.style.cssText="display:none;";
                                    if(rowdowstyle){
                                        rowdowstyle.style.cssText="display:none;";
                                        rowdowstyle.innerHTML='';
                                        htmlOutput='';   
                                    }
                                }

                        }else{
                            setTimeouterr('下载地址不正确');
                        }
                }else{
                  setTimeouterr('必填项不能为空');
                }
            }else{
                    rowdowdialog.close();
                    bodyhiddenx.style.cssText="overflow:auto;";
                    rowdowdialog.style.cssText="display:none;";
                    if(rowdowstyle){
                        rowdowstyle.style.cssText="display:none;";
                        rowdowstyle.innerHTML='';
                        htmlOutput=''; 
                    } 
            }
            
        }
        //点击确定按钮
        rowdowcloseyes.onclick=function(){

            const rowdowname=rowdownameinput.value;
            const rowdowurl=rowdowurlinput.value;
            const rowdowmun=rowdowmuninput.value;
            const rowdowsize=rowdowsizeinput.value;
            const rowdowpx=rowdowpxinput.value;
            const rowdowpas=rowdowpasinput.value;
            const rowdowzip=rowdowzipinput.value;

            if(rowdowname!=''||rowdowurl!=''||rowdowmun!=''||rowdowsize!=''||rowdowpx!=''||rowdowpas!=''||rowdowzip!=''){
                if (rowdowname!=''&&rowdowurl!=''&&rowdowmun!=''&&rowdowsize!=''){
                    //判断rowdowurl是否是网址
                    if (isURLx(rowdowurl)){
                                if (containsInvisibleChars(rowdowname)){
                                    setTimeouterr('[网盘名称] 不能有空格等不可见字符');
                                }else if (containsInvisibleChars(rowdowurl)){
                                    setTimeouterr('[下载地址] 不能有空格等不可见字符');
                                }else if(containsInvisibleChars(rowdowmun)){
                                    setTimeouterr('[文件数量] 不能有空格等不可见字符');
                                }else if(containsInvisibleChars(rowdowsize)){
                                    setTimeouterr('[文件大小] 不能有空格等不可见字符');
                                }else if(containsInvisibleChars(rowdowpx)){
                                    setTimeouterr('[分辨率] 不能有空格等不可见字符');
                                }else if(containsInvisibleChars(rowdowpas)){
                                    setTimeouterr('[提取码] 不能有空格等不可见字符');
                                }else if(containsInvisibleChars(rowdowzip)){
                                    setTimeouterr('[解压密码] 不能有空格等不可见字符');
                                }else{
                                    rowdowdialog.close();
                                    bodyhiddenx.style.cssText="overflow:auto;";
                                    rowdowdialog.style.cssText="display:none;";
                                    if(rowdowstyle){
                                        rowdowstyle.style.cssText="display:none;";
                                        rowdowstyle.innerHTML='';
                                        htmlOutput='';   
                                    }
                                }

                      }else{
                        setTimeouterr('下载地址不正确');
                    }
                }else{
                    setTimeouterr('必填项不能为空');
                }
            }else{
                rowdowdialog.close();
                bodyhiddenx.style.cssText="overflow:auto;";
                rowdowdialog.style.cssText="display:none;";
                if(rowdowstyle){
                    rowdowstyle.style.cssText="display:none;";
                    rowdowstyle.innerHTML='';
                    htmlOutput='';
                }
            }

        }
    }
})