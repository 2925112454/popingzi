document.addEventListener('DOMContentLoaded', function () {
    function isPositiveIntegerLike(n) {  //自定义判断是否是正整数的函数
        var parsed = Number(n);  
        return !isNaN(parsed) && Number.isInteger(parsed) && parsed > 0;  
    }

if(rowid&&isPositiveIntegerLike(rowid)){
    let arrimgaaa=[];
    function checkHtmlTags(html) {
        // 定义允许的html标签
        const allowedTags = ['p', 'strong', 'a', 'b', 'br'];
        // 构建正则表达式
        const tagPattern = new RegExp(`<(\\/?)(${allowedTags.join('|')})( [^>]*)?>`, 'gi');
        // 查找所有标签
        const tags = html.match(tagPattern);
        // 如果没有标签，直接返回 true
        if (!tags) {
            return true;
        }
        // 检查是否有不允许的标签
        for (let tag of tags) {
            const tagName = tag.replace(/<\/?|\/?>/g, '').split(' ')[0];
            if (!allowedTags.includes(tagName)) {
                return false;
            }
        }
        // 检查是否有不允许的内容
        const cleanedText = html.replace(tagPattern, '');
        if (/<[^>]+>/.test(cleanedText)) {
            return false;
        }
        return true;
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
        const validExtensions = ['jpg', 'jpeg', 'gif', 'png', 'webp', 'svg','bmp','ico'];  
        // 提取最后一个`.`之后的部分作为扩展名  
        const extension = url.split('.').pop().toLowerCase();  
        // 如果扩展名在允许的列表中，则返回true  
        return validExtensions.includes(extension);  
    }
    if(imgarr){
        //imgarr转为逗号分割的数组
        const eind= imgarr.split(',');
        arrimgaaa=eind;
        const imgeye = document.getElementById('imgeye'); // 预览图片div 
        const xcdesss = document.getElementById('xcdesss'); //预览图片主div,主要负责显示和隐藏

        if (eind.length > 0) {
                upyesx = 0;
                xcdesss.style.display = 'block';
                // 遍历图片地址数组并创建<img>元素插入到页面中
                // 使用DocumentFragment提高性能
                var fragment = document.createDocumentFragment();

                eind.forEach(function (imgUrl) {

                    // 创建div元素并设置属性
                    var divElement = document.createElement('div');
                    divElement.setAttribute('data-img', imgUrl);
                    divElement.className = "eyeimgall";
                    divElement.style.backgroundImage = 'url(' + imgUrl + ')';
                    divElement.style.backgroundSize = 'cover';
                    divElement.style.backgroundPosition = 'center center';
                    divElement.draggable = true;

                    // 创建删除链接
                    var deleteLink = document.createElement('a');
                    deleteLink.className = "detpostimgall";
                    deleteLink.setAttribute('data-img', imgUrl);
                    var deleteIcon = document.createElement('i');
                    deleteIcon.className = "fa fa-trash";
                    // 组装元素
                    deleteLink.appendChild(deleteIcon);
                    divElement.appendChild(deleteLink);


                    // 将新div添加到fragment
                    fragment.appendChild(divElement);

                });
                // 一次性将fragment内容添加到imgeye
                imgeye.appendChild(fragment);


            /** 拖动 **/

            // 创建“设为封面”元素并添加到body
            const setAsCoverElement = document.createElement('div');
            setAsCoverElement.id = 'setAsCover';
            setAsCoverElement.textContent = '设为封面';
            setAsCoverElement.style.display = 'none';
            document.body.appendChild(setAsCoverElement);
            let isDroppedOnCover = false; // 标志变量

            // 监听拖动事件
            document.addEventListener('dragstart', function (event) {
                if (event.target.classList.contains('eyeimgall')) {
                    setAsCoverElement.style.display = 'block';
                    event.dataTransfer.setData('text/plain', event.target.dataset.img);
                    event.dataTransfer.effectAllowed = 'move';
                    isDroppedOnCover = false; // 重置标志变量
                }
            });

            document.addEventListener('dragend', function (event) {
                setAsCoverElement.style.display = 'none';
                setAsCoverElement.style.backgroundColor = '#000'; // 恢复背景颜色
                if (!isDroppedOnCover) {
                    event.target.classList.remove('dragging'); // 移除拖动效果
                    updateOrder(); // 更新排序
                }
            });

            setAsCoverElement.addEventListener('dragover', function (event) {
                event.preventDefault();
            });
            setAsCoverElement.addEventListener('dragenter', function (event) {
                setAsCoverElement.style.backgroundColor = 'var(--hover-color)'; // 更改背景颜色
            });

            setAsCoverElement.addEventListener('dragleave', function (event) {
                setAsCoverElement.style.backgroundColor = '#000'; // 恢复背景颜色
            });
            setAsCoverElement.addEventListener('drop', function (event) {
                event.preventDefault();
                const imgUrl = event.dataTransfer.getData('text/plain');
                const inputElement = document.getElementById('rowimg');
                if (inputElement) {
                    inputElement.value = imgUrl;
                }
                setAsCoverElement.style.display = 'none';
                isDroppedOnCover = true; // 更新标志变量
            });

            // 阻止默认拖放行为
            document.addEventListener('dragover', function (event) {
                event.preventDefault();
                event.dataTransfer.dropEffect = 'move';
            });
            document.addEventListener('drop', function (event) {
                event.preventDefault();
                const target = event.target.closest('.eyeimgall');
                if (target) {
                    const draggedItem = document.querySelector('.eyeimgall.dragging');
                    if (draggedItem && draggedItem !== target) {
                        const targetIndex = parseInt(target.dataset.index, 10);
                        const draggedIndex = parseInt(draggedItem.dataset.index, 10);
                        // 移动元素
                        imgeye.insertBefore(draggedItem, target);
                        // 更新数据模型
                        draggedItem.dataset.index = targetIndex.toString();
                        target.dataset.index = draggedIndex.toString();
                    }
                }
            });

            // 为所有可拖动元素添加拖动效果
            const draggableItems = document.querySelectorAll('.eyeimgall');
            draggableItems.forEach(function (item) {
                item.addEventListener('dragstart', function (event) {
                    event.dataTransfer.effectAllowed = 'move';
                    item.classList.add('dragging');
                });
                item.addEventListener('dragend', function (event) {
                    event.currentTarget.classList.remove('dragging');
                });
            });



function updateOrder() {
    // 更新 eind 数组以反映新的排序顺序
    const newOrder = Array.from(imgeye.children).map(function (element) {
        return element.getAttribute('data-img');
    });
    eind.splice(0, eind.length, ...newOrder);
    // 输出新的排序顺序为|分隔的字符串
    const sortedImages = newOrder.join('|');
    $.ajax({
        url: '/api/newli.php', // 请求地址
        type: 'POST',   // 请求类型
        data: {
            new: sortedImages,//新顺序
            id: rowid,//文章id
        },
        success: function (newli) { // 成功回调函数
            if (newli == 500) {
                alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
            } else if (newli == 200) {
                console.log("相册顺序更改成功!~");
            } else {
                alert("<font>(｡ŏ_ŏ)</font> 程序错误！");
                console.log(newli);
            }
        }
    });
}

/**拖动结束**/

        }


        const detpostimgallloaaa = document.querySelectorAll('.detpostimgall');//所有删除按钮
        detpostimgallloaaa.forEach(function (det) {
            det.addEventListener('click', function (e) {
                const edataimg = det.getAttribute('data-img');
                if (edataimg) {
                    if (confirm("确定删除该图片吗？")) {
                        postdelimgd(edataimg);
                    }
                }
            });
        });



        function postdelimgd(imgurl) {


            if (arrimgaaa.length > 1){
                //判断路径结尾后缀是否是图片后缀
                if (imgurl.endsWith('.jpg') || imgurl.endsWith('.jpeg') || imgurl.endsWith('.png') || imgurl.endsWith('.gif') || imgurl.endsWith('.bmp') || imgurl.endsWith('.webp')|| imgurl.endsWith('.ico')) {
                    //判断图片地址开头是否是/upload/或upload/或./upload/或../upload/
                    
                        $.ajax({
                            url: '/api/delimgx.php', // 请求地址
                            type: 'POST',   // 请求类型
                            data: {
                                url: imgurl,//图片地址
                                id: rowid,//文章id
                            },
                            success: function (postdel) { // 成功回调函数
                                if (postdel == 500) {
                                    alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                                } else if (postdel == 200) {
                                    //删除成功,获取所有类名为eyeimgall的div,并移除data-img等于imgurl的div
                                    const eyeimgallert = document.querySelectorAll('.eyeimgall');
                                    eyeimgallert.forEach(function (eye) {
                                        if (eye.getAttribute('data-img') == imgurl) {
                                            eye.remove();
                                        }
                                    });
                                    if (eyeimgallert.length < 1) {
                                        upyesx = 1;
                                        xcdesss.style.display = 'none';
                                        imgeye.innerHTML = '';
                                        document.getElementById('arrimgfileInput').value = '';
                                    }
                                    //删除arrimgaaa数组中的对应项
                                    arrimgaaa.splice(arrimgaaa.indexOf(imgurl), 1);                                    
                                } else if (postdel == 404) {
                                    alert("<font>(｡ŏ_ŏ)</font> 相册不存在！");
                                } else if (postdel == 400) {
                                    alert("<font>(｡ŏ_ŏ)</font> 错误参数！");
                                } else if (postdel == 600) {
                                    alert("<font>(｡ŏ_ŏ)</font> 删除失败！");
                                } else {
                                    alert("<font>(｡ŏ_ŏ)</font> 程序错误！");
                                    console.log(postdel);
                                }
                            }
                        });
                    
                } else {
                    alert("<font>(｡ŏ_ŏ)</font> 图片格式错误！");
                }
            }else{
                if (confirm("这是最后一张图片，删除就是删除整篇文章，确认这么做？")) {
                    if (isPositiveIntegerLike(rowid)){
                        $.ajax({
                            url: '/inc/alldelrow.php', // 请求地址
                            type: 'POST',   // 请求类型
                            data: {
                                ids:rowid,//id
                            },
                                    success: function(alldelrow) { // 成功回调函数
                                        if(alldelrow == 500){
                                            alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                                    
                                        }else if(alldelrow == 200){
                                            //跳转页面
                                            window.location.href = "popingzi.php?type=3";
                                        }else if(alldelrow == 404){
                                            alert("<font>(｡ŏ_ŏ)</font> 文章不存在！");
                                        }else if(alldelrow == 600){
                                            alert("<font>(｡ŏ_ŏ)</font> 删除失败！");
                                        }else{
                                            alert("<font>(｡ŏ_ŏ)</font> 服务器出现错误！");
                                            console.log(alldelrow);
                                        }
                                    }
                    
                        });
                    }else{
                        alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                    }
                    
                }
            }

            
        }

    }

    const newwordsubmit=document.getElementById('newwordsubmit');//提交按钮
    const wordform=document.getElementById('wordform');//表单
    if (newwordsubmit&&wordform) {
        newwordsubmit.addEventListener('click', function (e) {
            e.preventDefault();//阻止表单提交
            //获取表单数据,按input的name获取
            const newrowhead = wordform.rowhead.value;//标题
            const newrowif = wordform.rowif.value;//分类
            const newrowvip = wordform.rowvip.value;//阅览权限
            const newrowimg = wordform.rowimg.value;//封面
            const newrowcp = wordform.rowcp.value;//版权方
            const newrowcpurl = wordform.rowcpurl.value;//版权方链接
            const newrowtag = wordform.rowtag.value;//标签
            const newrowtop = wordform.rowtop.value;//置顶
            const newrowtext = tinymce.get('rowtext').getContent();//摘要

            const newrowdow = wordform.rowdow.value;//下载积分
            const newrowdowname = wordform.rowdowname.value;//网盘名称
            const newrowdowpx = wordform.rowdowpx.value;//分辨率
            const newrowdowurl = wordform.rowdowurl.value;//网盘链接
            const newrowdowpas = wordform.rowdowpas.value;//网盘提取码
            const newrowdowmun = wordform.rowdowmun.value;//文件数量
            const newrowdowsize = wordform.rowdowsize.value;//文件大小
            const newrowdowzip = wordform.rowdowzip.value;//解压密码
            const imageseye = wordform.imageseye.value;//游客可见
            const vipimageseye = wordform.vipimageseye.value;//登录可见
            const imgtexttop = wordform.imgtexttop.value;//摘要显示位置

            const newrowdowif = wordform.rowdwif.value;//下载权限

            if (newrowhead == "") {
                alert("<font>(｡ŏ_ŏ)</font> 标题不能为空！");
                return;
            }

            if (!isPositiveIntegerLike(newrowif)&&newrowif!=0&&newrowif!='') {
                alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                return;
            }

            if (!isPositiveIntegerLike(newrowvip) || !isPositiveIntegerLike(imgtexttop) || imgtexttop<1 || imgtexttop >2 || newrowvip<=0 || newrowvip>3||newrowdowif<1||newrowdowif>3||!isPositiveIntegerLike(newrowdowif)) {//范围1-3
                alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                return;
            }

            if (newrowimg!=''){
                if (!isValidImageUrl(newrowimg)) {
                    alert("<font>(｡ŏ_ŏ)</font> 封面图片格式错误！");
                    return;
                }
            }

            if(newrowtext){
                if (!checkHtmlTags(newrowtext)){
                    alert("<font>(｡ŏ_ŏ)</font> 请勿修改摘要html格式！");
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

            if(!isPositiveIntegerLike(newrowtop)&&(newrowtop<=0||newrowtop>4)){
                    alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                    return;
            }

            if (newrowcpurl!=""){
                if(!isURL(newrowcpurl)){
                    alert("<font>(｡ŏ_ŏ)</font> 版权链接不正确！");
                    return;
                }
                if (newrowcpurl&&(newrowcp===""||newrowcp=='')){
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
            if (imgarr){
                   if (arrimgaaa.length<1){
                    alert("<font>(｡ŏ_ŏ)</font> 相册为空，请删除文章后重新发布！");
                    return;
                }
            }else{
                alert("<font>(｡ŏ_ŏ)</font> 相册为空，请删除文章后重新发布！");
                return;
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
                content: newrowtext,//摘要
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
                dowif:newrowdowif,//下载权限
                imageseye:imageseye,//游客可见
                vipimageseye:vipimageseye,//会员可见
                txttop:imgtexttop,//摘要位置
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
}else{
    alert("<font>(｡ŏ_ŏ)</font> 错误参数！");
}
});
