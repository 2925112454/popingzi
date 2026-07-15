let listevadeoarr = []; // 视频列表
if(videoarr){
    listevadeoarr=videoarr.split(',');
}
document.addEventListener('DOMContentLoaded', function () {
    const newarrvideo = document.getElementById('newarrimg');
    const rowvideoarr = document.getElementById('rowimgarr');
    const xcdesssvideo = document.getElementById('xcdesss');
    const videoeye = document.getElementById('imgeye');
    const suffix = /\.(mp4|m3u8|3gp|webm|ogg|mp3|wav)?$/i;

    if (listevadeoarr.length > 0){
            xcdesssvideo.style.display = 'flex';
            //将vadeoarr中的视频输出到预览
            listevadeoarr.forEach(function(videoUrl) {
                var videobox = document.createElement('div');
                videobox.className = "videobox";
                videobox.dataset.video = videoUrl;
                videobox.draggable = true;
                videobox.innerHTML = `<video controls class="video-item" src="${videoUrl}" type="application/x-mpegURL"></video><a class="deletevideo" data-d="${videoUrl}"><i class="fa fa-trash"></i></a>`;
                videoeye.appendChild(videobox);
                initializeNewVideo(videobox.querySelector('.video-item'));
            });
    }

    if (newarrvideo && rowvideoarr && xcdesssvideo && videoeye) {
        newarrvideo.addEventListener('click', function (e) {
            const rowvideoarrvalue = rowvideoarr.value.trim();//获取输入框的值
        if (rowvideoarrvalue) {

         // 检查视频地址是否已存在于数组中
        if (listevadeoarr.includes(rowvideoarrvalue)) {
            alert('<font>(｡ŏ_ŏ)</font> 请勿重复添加！');
            return;
        }
        if (!suffix.test(rowvideoarrvalue)) {
            //判断开头是否是http或者https开头
            if (!rowvideoarrvalue.startsWith('http://') && !rowvideoarrvalue.startsWith('https://')) {
                alert('<font>(｡ŏ_ŏ)</font> 请输入有效的视频地址!');
                return;
            }
        }

                xcdesssvideo.style.display = 'flex';
                var videobox = document.createElement('div');
                videobox.className = "videobox";
                videobox.dataset.video = rowvideoarrvalue;
                videobox.draggable = true;
                videobox.innerHTML = `<video controls class="video-item" src="${rowvideoarrvalue}" type="application/x-mpegURL"></video><a class="deletevideo" data-d="${rowvideoarrvalue}"><i class="fa fa-trash"></i></a>`;     
                videoeye.appendChild(videobox);
                // 初始化新添加的视频
                initializeNewVideo(videobox.querySelector('.video-item'));
                // 存储视频地址
                listevadeoarr.push(rowvideoarrvalue);
                rowvideoarr.value='';
            } else {
                alert('<font>(｡ŏ_ŏ)</font> 请输入有效的视频地址!');
            }
        });
        //删除视频
        videoeye.addEventListener('click', function(e) {
            if (e.target.closest('.deletevideo')) {
                e.preventDefault();
                var videoItem = e.target.closest('.videobox').querySelector('.video-item');
                cleanupVideo(videoItem);
                var videobox = e.target.closest('.videobox');
                const edata = e.target.closest('.deletevideo').getAttribute('data-d');
                deletevideoxx(edata);
                videobox.remove();
            }
        });
    }

/**********拖动改变顺序功能实现模块*************/

// 添加拖放事件监听器
videoeye.addEventListener('dragstart', handleDragStart);
videoeye.addEventListener('dragover', handleDragOver);
videoeye.addEventListener('drop', handleDrop);
videoeye.addEventListener('dragend', handleDragEnd);

// 处理函数定义
function handleDragStart(e) {
    e.dataTransfer.setData('text/plain', e.target.dataset.video);
    e.target.classList.add('dragging');
}

function handleDragOver(e) {
    e.preventDefault(); // 必须阻止以允许drop
}

function handleDrop(e) {
    e.preventDefault();
    const draggedVideoId = e.dataTransfer.getData('text/plain');
    const draggedBox = document.querySelector(`[data-video="${draggedVideoId}"]`);
    const targetBox = e.target.closest('.videobox');

    if (draggedBox && targetBox && draggedBox !== targetBox) {
        const boxes = Array.from(videoeye.children);
        const draggedIndex = boxes.indexOf(draggedBox);
        const targetIndex = boxes.indexOf(targetBox);
        // 更新DOM顺序
        videoeye.insertBefore(draggedBox, targetIndex > draggedIndex ? targetBox.nextSibling : targetBox);
        // 更新vadeoarr数组顺序
        updateArrayOrder();
    }
    handleDragEnd(e); // 清理拖动结束状态
}

function handleDragEnd(e) {
    e.target.classList.remove('dragging');
}
function updateArrayOrder() {
    const boxes = Array.from(videoeye.children);
    listevadeoarr = boxes.map(box => box.dataset.video);
}

/**********拖动改变顺序功能实现模块*************/
    function initializeNewVideo(videoElement) {
        if (videoElement.src.endsWith('.m3u8') && Hls.isSupported()) {
            var hls = new Hls();
            videoElement.hlsInstance = hls;
            hls.loadSource(videoElement.src);
            hls.attachMedia(videoElement);
            hls.on(Hls.Events.ERROR, function(event, data) {
                console.error('HLS error:', data.details);
            });
        } else if (videoElement.canPlayType('application/vnd.apple.mpegurl')) {
            videoElement.addEventListener('loadedmetadata', function () {
                videoElement.play();
            });
        }
    
        // 播放事件监听器
        videoElement.addEventListener('play', function () {
            stopAllOtherVideos(videoElement);
        });
    }
    function stopAllOtherVideos(currentVideo) {
        const allVideos = document.querySelectorAll('.video-item');
        allVideos.forEach(video => {
            if (video !== currentVideo) {
                video.pause();
            }
        });
    }
    
    function cleanupVideo(videoElement) {
        var hls = videoElement.hlsInstance;
        if (hls) {
            hls.stopLoad();
            hls.detachMedia();
            hls.destroy();
            delete videoElement.hlsInstance;
        }
    }
    // 删除函数
function deletevideoxx(value) {
    // 获取触发事件的 videobox 元素
    const clickedBox = document.querySelector(`[data-video="${value}"]`);
    if (clickedBox) {
        const videobox = clickedBox.closest('.videobox');
        cleanupVideo(videobox.querySelector('.video-item'));
        videobox.remove();

        // 从 vadeoarr 数组中移除匹配的值
        const index = listevadeoarr.indexOf(value);
        if (index > -1) {
            listevadeoarr.splice(index, 1);
        }

        // 检查是否还有其他视频
        const remainingVideos = document.querySelectorAll('.videobox');
        if (remainingVideos.length < 1|| listevadeoarr.length < 1) {
            document.getElementById('xcdesss').style.display = 'none';
            document.getElementById('imgeye').innerHTML = '';
            listevadeoarr = [];
            localStorage.removeItem('video');
        }
    }
}
//提交
function isPositiveIntegerLike(n) {  //自定义判断是否是正整数的函数
    var parsed = Number(n);  
    return !isNaN(parsed) && Number.isInteger(parsed) && parsed > 0;  
}

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

function containsInvisibleCharssx(str) {  
    // 这个正则表达式匹配空格、制表符、换行符和其他常见的不可见字符  
    const regex = /[\s\uFEFF\xA0​\u2000-\u206F\u2070-\u20CF\u2100-\u218F\u2E00-\u2EFF\u3000-\u303F\uFE00-\uFE6F\uFF00-\uFFEF]/g;  
    return regex.test(str);  
}

const submitButton = document.getElementById('newwordsubmit');//获取提交按钮
const videoform=document.getElementById('wordform');//表单
if (submitButton&&videoform){
    submitButton.addEventListener('click', function (e) {
        e.preventDefault();
        //获取表单数据,按input的name获取
        const newrowhead = videoform.rowhead.value;//标题
        const newrowif = videoform.rowif.value;//分类
        const newrowvip = videoform.rowvip.value;//阅览权限
        const newrowimg = videoform.rowimg.value;//封面
        const newrowcp = videoform.rowcp.value;//版权方
        const newrowcpurl = videoform.rowcpurl.value;//版权方链接
        const newrowtag = videoform.rowtag.value;//标签
        const newrowtop = videoform.rowtop.value;//置顶
        const newrowtext = tinymce.get('rowtext').getContent();//摘要

        const newrowdow = videoform.rowdow.value;//下载积分
        const newrowdowname = videoform.rowdowname.value;//网盘名称
        const newrowdowpx = videoform.rowdowpx.value;//分辨率
        const newrowdowurl = videoform.rowdowurl.value;//网盘链接
        const newrowdowpas = videoform.rowdowpas.value;//网盘提取码
        const newrowdowmun = videoform.rowdowmun.value;//文件数量
        const newrowdowsize = videoform.rowdowsize.value;//文件大小
        const newrowdowzip = videoform.rowdowzip.value;//解压密码
        const imgtexttop = videoform.imgtexttop.value;//摘要显示位置

        const newrowdowif = videoform.rowdwif.value;//下载权限

        if (newrowhead == "") {
            alert("<font>(｡ŏ_ŏ)</font> 标题不能为空！");
            return;
        }

        if (listevadeoarr.length < 1) {
            alert('<font>(｡ŏ_ŏ)</font> 视频不能为空!');
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

        if (outputtag){
            var rowtag = outputtag;//标签
        }else{
            var rowtag = "";
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
        const vadeoarrtext= listevadeoarr.join(",");//将数组转换为字符串

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
                                text: vadeoarrtext,//内容
                                dowif:newrowdowif,//下载权限
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


    })
}

})