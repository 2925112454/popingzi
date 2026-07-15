document.addEventListener('DOMContentLoaded', function() {
    function isPositiveIntegerLike(n) {  //自定义判断是否是正整数的函数
        var parsed = Number(n);  
        return !isNaN(parsed) && Number.isInteger(parsed) && parsed > 0;  
    }
    function containsInvisibleCharssx(str) {  
        // 这个正则表达式匹配空格、制表符、换行符和其他常见的不可见字符  
        const regex = /[\s\uFEFF\xA0​\u2000-\u206F\u2070-\u20CF\u2100-\u218F\u2E00-\u2EFF\u3000-\u303F\uFE00-\uFE6F\uFF00-\uFFEF]/g;  
        return regex.test(str);  
    }
    function isURL(str) {
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

    function isValidImageUrl(url) {  
        // 允许的图片后缀名  
        const validExtensions = ['jpg', 'jpeg', 'gif', 'png', 'webp', 'svg','ico','bmp'];  
        // 提取最后一个`.`之后的部分作为扩展名  
        const extension = url.split('.').pop().toLowerCase();  
        // 如果扩展名在允许的列表中，则返回true  
        return validExtensions.includes(extension);  
    }

    var rowform = document.getElementById('wordform');//获取表单
    var newwordsubmit = document.getElementById('newwordsubmit');//获取提交按钮
    if (rowform&&newwordsubmit){
        newwordsubmit.addEventListener('click', function(e) {
            e.preventDefault();//阻止默认事件
            //获取表单数据,按input的name获取
            const newrowhead = rowform.rowhead.value;//标题
            const newrowif = rowform.rowif.value;//分类
            const newrowvip = rowform.rowvip.value;//阅览权限
            const newrowimg = rowform.rowimg.value;//封面
            const newrowcp = rowform.rowcp.value;//版权方
            const newrowcpurl = rowform.rowcpurl.value;//版权方链接
            const newrowtag = rowform.rowtag.value;//标签
            const newrowtop = rowform.rowtop.value;//置顶
            const newrowtext = tinymce.get('rowtext').getContent();//内容

            const newrowdow = rowform.rowdow.value;//下载积分
            const newrowdowname = rowform.rowdowname.value;//网盘名称
            const newrowdowpx = rowform.rowdowpx.value;//分辨率
            const newrowdowurl = rowform.rowdowurl.value;//网盘链接
            const newrowdowpas = rowform.rowdowpas.value;//网盘提取码
            const newrowdowmun = rowform.rowdowmun.value;//文件数量
            const newrowdowsize = rowform.rowdowsize.value;//文件大小
            const newrowdowzip = rowform.rowdowzip.value;//解压密码

            const newrowdowif = rowform.rowdwif.value;//下载权限
            

            if (newrowhead == "") {
                alert("<font>(｡ŏ_ŏ)</font> 标题不能为空！");
                return;
            }

            if (newrowtext == "") {
                alert("<font>(｡ŏ_ŏ)</font> 内容不能为空！");
                return;
            }

            if (!isPositiveIntegerLike(newrowif)&&newrowif!=0&&newrowif!='') {
                alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                return;
            }

            if (!isPositiveIntegerLike(newrowvip) || newrowvip<=0 || newrowvip>3||newrowdowif<1||newrowdowif>3||!isPositiveIntegerLike(newrowdowif)) {//范围1-3
                alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                return;
            }

            if (newrowimg!=''){
                if (!isValidImageUrl(newrowimg)) {
                    alert("<font>(｡ŏ_ŏ)</font> 封面图片格式错误！");
                    return;
                }
            }

            if (newrowtag!=''){
                const newrowtagrep = newrowtag.replace(/，/g, ",");//将标签的中文逗号替换后英文逗号
                const newrowtagarr = newrowtagrep.split(",");//将标签转换为以逗号分割的数组
                for (let i = 0; i < newrowtagarr.length; i++) {
                    if (newrowtagarr[i].length > 20) {
                        alert("<font>(｡ŏ_ŏ)</font> 标签过长！");
                        return;
                    }
                    if (newrowtagarr.indexOf(newrowtagarr[i]) !== i) {
                        alert("<font>(｡ŏ_ŏ)</font> 存在重复标签！");
                        return;
                    }
                    
                }
                // 将标签转换为以逗号分割的数组，并去除空字符串  
                var  outputtagarr = newrowtagarr.filter(item => item.trim() !== "");
                var  outputtag = outputtagarr.join(",");//将数组转换为字符串
            }
            const newrowdowzeo = newrowdow.replace(/^0+/, ""); //去除00123这种数字前面的0

            if (newrowdow!=""&&newrowdowzeo!=0){

                 if(!isPositiveIntegerLike(newrowdow)){
                    alert("<font>(｡ŏ_ŏ)</font> 积分只能是正整数！");
                    return;
                }
                
                if(newrowdow.length > 9){
                    alert("<font>(｡ŏ_ŏ)</font> 积分格式不正确！");
                    rowform.rowdow.value = newrowdowzeo.substring(0,9);
                    return;
                }

                if(newrowdowzeo.length>9||newrowdowzeo>999999999){
                    alert("<font>(｡ŏ_ŏ)</font> 你的下载太贵啦！");
                    return;
                }
            }

            if(newrowtop<1||newrowtop>4||!isPositiveIntegerLike(newrowtop)){
                    alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                    return;
            }
            

            if (newrowcpurl!=""){
                if(!isURL(newrowcpurl)){
                    alert("<font>(｡ŏ_ŏ)</font> 版权链接不正确！");
                    return;
                }
                if (newrowcpurl!=""&&newrowcp==""){
                    alert("<font>(｡ŏ_ŏ)</font> 存在版权链接，请输入版权方名称！");
                    return;
                }
            }

            if (newrowdowname!=''||newrowdowpx!=''||newrowdowurl!=''||newrowdowpas!=''||newrowdowmun!=''||newrowdowsize!=''||newrowdowzip!=''){
                if (newrowdowname==''||newrowdowurl==''||newrowdowmun==''||newrowdowsize==''){
                    alert("<font>(｡ŏ_ŏ)</font> 下载信息配置不正确！");
                    return;
                }
                if (newrowdowurl!=''){
                    if (!isURL(newrowdowurl)){
                        alert("<font>(｡ŏ_ŏ)</font> 下载信息地址不正确！");
                        return;
                    }
                    if (newrowdowurl.indexOf("magnet:")!=-1){
                        alert("<font>(｡ŏ_ŏ)</font> 不能用磁力链接！");
                        return;
                    }                                       
                    if (containsInvisibleCharssx(newrowdowname)){
                        alert("<font>(｡ŏ_ŏ)</font> 下载信息配置不能用空格！");
                    return;
                    }else if (containsInvisibleCharssx(newrowdowurl)){
                        alert("<font>(｡ŏ_ŏ)</font> 下载信息配置不能用空格！");
                    return;
                    }else if(containsInvisibleCharssx(newrowdowmun)){
                        alert("<font>(｡ŏ_ŏ)</font> 下载信息配置不能用空格！");
                    return;
                    }else if(containsInvisibleCharssx(newrowdowsize)){
                        alert("<font>(｡ŏ_ŏ)</font> 下载信息配置不能用空格！");
                    return;
                    }else if(containsInvisibleCharssx(newrowdowpx)){
                        alert("<font>(｡ŏ_ŏ)</font> 下载信息配置不能用空格！");
                    return;
                    }else if(containsInvisibleCharssx(newrowdowpas)){
                        alert("<font>(｡ŏ_ŏ)</font> 下载信息配置不能用空格！");
                    return;
                    }else if(containsInvisibleCharssx(newrowdowzip)){
                        alert("<font>(｡ŏ_ŏ)</font> 下载信息配置不能用空格！");
                    return;
                    }
                }
            }

            if (outputtag){
                var rowtag = outputtag;//标签
            }else{
                var rowtag = "";
            }

            //ajax提交表单
            $.ajax({
                url: '/api/edit.php', // 请求地址
                type: 'POST',   // 请求类型
                data: {
                id: rowid,//id
                tag: rowtag,//标签
                title: newrowhead,//标题
                img:newrowimg,//封面图片
                if: newrowif,//分类
                vip:newrowvip,//阅览权限
                top: newrowtop,//置顶
                cp: newrowcp,//版权方
                cpurl: newrowcpurl,//版权链接
                dow: newrowdow,//文件下载所需积分
                downame: newrowdowname,//网盘名称
                dowurl: newrowdowurl,//下载地址
                dowmun: newrowdowmun,//文件数量
                dowsize: newrowdowsize,//文件大小
                dowpx: newrowdowpx,//分辨率
                dowpas: newrowdowpas,//提取码
                dowzip: newrowdowzip,//解压密码
                text: newrowtext,//内容
                dowif:newrowdowif,//下载权限
                },
                            success: function(edit) { // 成功回调函数
                                if(edit == 500){
                                    alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                                }else if(edit == 200){
                                    alert("<font>(｡ŏ_ŏ)</font> 修改成功，正在跳转！");
                                    setTimeout(function(){
                                        window.location.href = "popingzi.php?type=3";
                                    },2000);
                                }else if(edit == 404){
                                    alert("<font>(｡ŏ_ŏ)</font> 文章不存在！");
                                }else if(edit == 505){
                                    alert("<font>(｡ŏ_ŏ)</font> 版权方不能为空！");
                                }else if(edit == 506){
                                    alert("<font>(｡ŏ_ŏ)</font> 版权地址不正确！");
                                }else if(edit == 600){
                                    alert("<font>(｡ŏ_ŏ)</font> 发布失败！");
                                }else if(edit == 4024){
                                    alert("<font>(｡ŏ_ŏ)</font> 请勿修改摘要html格式！");
                                }else{
                                    alert("<font>(｡ŏ_ŏ)</font> 程序错误！"); 
                                    console.log(edit);
                                }
                            }
              });

        });
    }
})