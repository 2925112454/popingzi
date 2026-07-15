document.addEventListener('DOMContentLoaded', function () {

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
        const validExtensions = ['jpg', 'jpeg', 'gif', 'png', 'webp', 'svg','bmp','ico','avif'];  
        // 提取最后一个`.`之后的部分作为扩展名
        const extension = url.split('.').pop().toLowerCase();  
        // 如果扩展名在允许的列表中，则返回true  
        return validExtensions.includes(extension);  
    }
    function isImagePathByRegex(path) {
        const imageExtensionsRegex = /\.(jpg|jpeg|png|gif|bmp|webp|ico|avif)$/i;
        return imageExtensionsRegex.test(path);
    }
    var arrinput = document.getElementById('rowimgarr');//图片地址input，填入地址则从地址添加图片，地址没有则触发本地上传
    var arrbutton = document.getElementById('newarrimg');//添加图片按钮
    var arrimgfileInput = document.getElementById('arrimgfileInput');//隐藏的input元素（file类型input）
    var imgeye = document.getElementById('imgeye'); // 预览图片div 
    var xcdesss = document.getElementById('xcdesss'); //预览图片主div,主要负责显示和隐藏
    let rowtextareaarr = [];//存储外链图片
    rowtextareaarr = [...new Set(rowtextareaarr)];
    let previewedImages = new Set();//存储待上传预览的图片
    let newarrfile = new Map();//存储待上传的图片集合
    let upyes = 1;
    let upyesx = 1;
    let yesxxx = 0;
    let eindStr = localStorage.getItem('rowtextareaarr');
    if (eindStr != null && eindStr != 'null' && eindStr != '' && eindStr != undefined) {
        eindStr = eindStr.trim().replace(/^['"]|['"]$/g, '');
        var eind = eindStr.split(',');
        if (eind.length > 0) {
            //弹出确认框
            var confirmResult = confirm("检测到上次未完成的相册，是否继续使用？");
            if (confirmResult) {
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

                    //创建一个divElement的子元素div:<div class="webimg">服务器图片</div>
                    var webimg = document.createElement('div');
                    webimg.className = "webimg";
                    webimg.innerHTML = "已上传";

                    // 组装元素
                    deleteLink.appendChild(deleteIcon);
                    divElement.appendChild(deleteLink);
                    divElement.appendChild(webimg);

                    // 将新div添加到fragment
                    fragment.appendChild(divElement);

                });
                // 一次性将fragment内容添加到imgeye
                imgeye.appendChild(fragment);

/**拖动**/
// 创建“设为封面”元素并添加到body
const setAsCoverElement = document.createElement('div');
setAsCoverElement.id = 'setAsCover';
setAsCoverElement.textContent = '设为封面';
setAsCoverElement.style.display = 'none';
document.body.appendChild(setAsCoverElement);
let isDroppedOnCover = false; // 标志变量
// 监听拖动事件
document.addEventListener('dragstart', function(event) {
    if (event.target.classList.contains('eyeimgall')) {
        setAsCoverElement.style.display = 'block';
        event.dataTransfer.setData('text/plain', event.target.dataset.img);
        isDroppedOnCover = false; // 重置标志变量
    }
});

document.addEventListener('dragend', function(event) {
    setAsCoverElement.style.display = 'none';
    setAsCoverElement.style.backgroundColor = '#000'; // 恢复背景颜色
    if (!isDroppedOnCover) {
        event.target.classList.remove('dragging');//移除拖动效果
    }
});

setAsCoverElement.addEventListener('dragover', function(event) {
    event.preventDefault();
});
setAsCoverElement.addEventListener('dragenter', function(event) {
    setAsCoverElement.style.backgroundColor = 'var(--hover-color)'; // 更改背景颜色
});

setAsCoverElement.addEventListener('dragleave', function(event) {
    setAsCoverElement.style.backgroundColor = '#000'; // 恢复背景颜色
});
setAsCoverElement.addEventListener('drop', function(event) {
    event.preventDefault();
    const imgUrl = event.dataTransfer.getData('text/plain');
    const inputElement = document.getElementById('rowimg');
    if (inputElement) {
        inputElement.value = imgUrl;
    }
    setAsCoverElement.style.display = 'none';
    isDroppedOnCover = true; // 更新标志变量

});
//阻止默认拖放行为
document.addEventListener('dragover', function(event) {
    event.preventDefault();
});
document.addEventListener('drop', function(event) {
    event.preventDefault();
});

/**拖动结束**/
            } else {
                //读取rowtextareaarr
                const delneweindStr = localStorage.getItem('rowtextareaarr');
                //转换为数组
                if(delneweindStr){
                    const delneweindStrarr = delneweindStr.split(',');
                    if (delneweindStrarr.length > 0) {
                        for (let i = 0; i < delneweindStrarr.length; i++) {
                            postdelimg(delneweindStrarr[i]);//删除图片
                            if (i == delneweindStrarr.length - 1) {//如果最后一个
                                localStorage.removeItem('rowtextareaarr');//删除本地存储
                                upyes = 0;
                            }else{
                                localStorage.setItem('rowtextareaarr', delneweindStrarr.slice(i + 1).join(','));//更新本地存储
                            }
                        }
                    }

                }

            }
        }
    }

    if (arrinput && arrbutton && arrimgfileInput) {

        var allowMime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp','image/avif'];//允许上传的文件mime类型
        var allowmaxsize = 1024;//允许上传的单个文件最大文件大小，单位为KB
        var allowmaxnum = 20;//允许上传的最大文件数量
        var allmaxsize = 20;//允许上传的总文件大小，单位为MB

        arrbutton.addEventListener('click', function (e) {
            e.preventDefault();//阻止默认事件
            if (arrinput.value == '' || arrinput.value == null || arrinput.value == undefined || arrinput.value == ' ') {//如果没有输入地址则触发本地上传
                arrimgfileInput.click();//触发点击事件
                yesxxx = 2;
            } else {
                yesxxx = 1;
                if (isImagePathByRegex(arrinput.value)) {//如果输入的是图片地址
                    //将图片输出到预览
                    xcdesss.style.display = 'block';
                    if (!rowtextareaarr.includes(arrinput.value)) {
                        //如果没有则添加
                        rowtextareaarr.push(arrinput.value);
                        imgeye.innerHTML += '<div data-img="' + arrinput.value + '" class="eyeimgall" style="background-image: url(' + arrinput.value + ');background-size: cover;background-position: center center;"><a class="detjsonimgall" data-img="' + arrinput.value + '"><i class="fa fa-trash"></i></a></div>';
                    }
                    arrinput.value = '';
                    var deyeimgall = document.querySelectorAll('.detimgall');//全部删除按钮
                    if (deyeimgall.length > 0) {
                        //按钮点击事件
                        deyeimgall.forEach(function (element) {
                            element.addEventListener('click', function (e) {
                                const edata = element.getAttribute('data-file-key');
                                removeImage(edata);
                            })
                        });

                    }

                    //获取所有删除按钮
                    const detjsonimgallrr = document.querySelectorAll('.detjsonimgall');
                    if (detjsonimgallrr.length > 0) {
                        //按钮点击事件
                        detjsonimgallrr.forEach(function (det) {
                            det.addEventListener('click', function (e) {
                                const edata = det.getAttribute('data-img');
                                if (edata) {
                                    //从rowtextareaarr移除edata
                                    rowtextareaarr.splice(rowtextareaarr.indexOf(edata), 1);
                                    //找到所有类名为eyeimgall的div
                                    const divs = document.querySelectorAll('.eyeimgall');
                                    //找到所有divs里data-img属性等于edata的div并执行删除
                                    divs.forEach(function (div) {
                                        if (div.getAttribute('data-img') === edata) {
                                            div.remove();//删除div
                                        }
                                    });
                                    //判断是否删除了最后一张图片，是则隐藏预览图片div 
                                    if (newarrfile.size < 1 && rowtextareaarr.length < 1 && upyes == 1 && upyesx != 0) {
                                        xcdesss.style.display = 'none';
                                        imgeye.innerHTML = '';
                                        document.getElementById('arrimgfileInput').value = '';
                                    }
                                }
                            })
                        });

                    }

                } else {
                    alert('<font>(｡ŏ_ŏ)</font> 图片路径不正确！');
                }
                if (yesxxx==1){
                    const detpostimgallloaaa = document.querySelectorAll('.detpostimgall');//所有删除按钮
                    detpostimgallloaaa.forEach(function (det) {
                        det.addEventListener('click', function (e) {
                            const edataimg = det.getAttribute('data-img');
                            if (edataimg) {
                                if (confirm("确定删除已上传图片吗？")) {
                                    postdelimg(edataimg);
                                }
                            }
                        });
                    });
                }

            }
        });
        arrimgfileInput.addEventListener('change', function (e) {
            var arrfile = e.target.files;
            if (arrfile.length > allowmaxnum) {
                alert('<font>(｡ŏ_ŏ)</font> 单次最多上传' + allowmaxnum + '个文件');
                return;
            }
            //超过总文件大小限制 
            var allsizemax = 0;
            for (var i = 0; i < arrfile.length; i++) {
                var filei = arrfile[i];
                allsizemax += filei.size;
                if (allsizemax > allmaxsize * 1024 * 1024) {
                    alert('<font>(｡ŏ_ŏ)</font> 总大小不能超过' + allmaxsize + 'MB');
                    return;
                }
            }
            if (arrfile.length > 0) {
                for (var i = 0; i < arrfile.length; i++) {
                    var filei = arrfile[i];
                    if (allowMime.indexOf(filei.type) > -1 && filei.type != '' && filei.type != null && filei.type != undefined && filei.type != 'application/octet-stream' && filei.size <= allowmaxsize * 1024) {
                        newarrfile.set(filei.name, filei);//使用文件名作为唯一标识符
                    }
                }
                if (newarrfile.size > 0) {
                    yesxxx=0;
                    xcdesss.style.display = 'flex';
                    // 遍历文件数组  
                    let index = 0;
                    newarrfile.forEach((filei, key) => {
                        // 检查此图片是否已被预览过
                        if (!previewedImages.has(key)) {
                            previewedImages.add(key); // 添加到已预览图片集合中
                            var reader = new FileReader();
                            // 闭包
                            (function (fileKey) {
                                reader.readAsDataURL(filei);
                                reader.onload = function (e) {
                                    // 创建图片容器  
                                    var imgContainer = document.createElement('div');
                                    imgContainer.className = 'eyeimgall';
                                    imgContainer.id = 'detximgall' + fileKey;
                                    imgContainer.style.backgroundImage = 'url(' + e.target.result + ')';
                                    imgContainer.style.backgroundSize = 'cover';
                                    imgContainer.style.backgroundPosition = 'center';
                                    // 创建删除链接  
                                    var deleteLink = document.createElement('a');
                                    deleteLink.className = 'detimgall';
                                    deleteLink.innerHTML = '<i class="fa fa-trash"></i>';
                                    deleteLink.dataset.fileKey = fileKey; // 存储文件唯一标识符  
                                    // 为删除链接添加点击事件监听器  
                                    deleteLink.addEventListener('click', function (event) {
                                        event.preventDefault(); // 阻止默认行为  
                                        removeImage(fileKey); // 调用删除函数  
                                    });
                                    // 将删除链接添加到图片容器中  
                                    imgContainer.appendChild(deleteLink);
                                    // 将图片容器添加到预览图片的div中  
                                    imgeye.appendChild(imgContainer);
                                };
                            })(key); // 立即执行函数表达式传递唯一标识符
                        }
                        index++;
                    });
                }
            }          
            e.target.value = ''; // 清理文件选择器的值
            document.getElementById('arrimgfileInput').value = '';
        });
        // 删除图片的函数  
        function removeImage(fileKey) {
            if (newarrfile.size > 0) {
                // 从已预览图片集合中移除
                previewedImages.delete(fileKey);
                const divdel = document.getElementById('detximgall' + fileKey);//对应的div
                //移出对应的div
                if (divdel) { divdel.remove(); }
                //从Map中移除文件对象
                if (newarrfile.has(fileKey)) {
                    newarrfile.delete(fileKey);
                }
                //判断是否删除了最后一张图片，是则隐藏预览图片div 
                if (newarrfile.size < 1 && rowtextareaarr.length < 1 && upyes == 1 && (localStorage.getItem('rowtextareaarr') == null || localStorage.getItem('rowtextareaarr') == '' || localStorage.getItem('rowtextareaarr') == '[]' || localStorage.getItem('rowtextareaarr') == undefined)) {
                    xcdesss.style.display = 'none';
                    imgeye.innerHTML = '';
                    document.getElementById('arrimgfileInput').value = '';
                }

            }


        }

        var upbutton = document.getElementById('upbutton');//上传图片按钮
        if (upbutton) {
            upbutton.onclick = function () {
                if (newarrfile.size > 0) {
                    // 使用jQuery的$.ajax进行文件上传
                    var xhr;
                    var Arrayup = Array.from(newarrfile.values());//获取所有待上传的图片
                    var formDatax = new FormData();
                    Arrayup.forEach(file => formDatax.append('files[]', file));
                    $.ajax({
                        type: "POST",
                        url: "/api/uploadimg.php",
                        data: formDatax, // 直接传递formData对象  
                        contentType: false, // 不设置内容类型  
                        processData: false, // 不处理发送的数据  
                        dataType: 'json', // 指定返回的数据类型为JSON
                        xhr: function () {
                            xhr = new window.XMLHttpRequest();
                            // 进度事件处理
                            xhr.upload.addEventListener("progress", function (event) {
                                if (event.lengthComputable) {
                                    var percentComplete = Math.round((event.loaded / event.total) * 100);
                                    if(percentComplete==100){
                                        updateProgress(50);
                                    }else{
                                        updateProgress(0);
                                    }
                                }
                            }, false);
                            return xhr;
                        },
                        success: function (upx) {
                            if (upx.error == 500) {
                                alert('<font>(｡ŏ_ŏ)</font> 错误操作！');
                                newarrfile.clear();
                                document.getElementById('arrimgfileInput').value = '';
                            } else if (upx.error == 200) {
                                updateProgress(100);
                                var veindStr = localStorage.getItem('rowtextareaarr');
                                upyes = 0;
                                yesxxx = 2;
                                //获取所有删除按钮
                                var detimgalls = document.getElementsByClassName('detimgall');
                                for (var i = 0; i < detimgalls.length; i++) {
                                    detimgalls[i].innerHTML = '<i class="fa fa-check"></i>';
                                    detimgalls[i].classList.add('upyes');
                                    detimgalls[i].onclick = function () {
                                        return false;
                                    }
                                }
                                if (veindStr == '' || veindStr == null || veindStr == 'null' || veindStr == 'undefined' || veindStr == ' ' || veindStr == undefined) {
                                    localStorage.setItem('rowtextareaarr', upx.msg);
                                } else {
                                    if (localStorage.getItem('rowtextareaarr') != null && localStorage.getItem('rowtextareaarr') != '' && localStorage.getItem('rowtextareaarr') != 'null') {
                                        localStorage.setItem('rowtextareaarr', localStorage.getItem('rowtextareaarr') + ',' + upx.msg);
                                    } else {
                                        localStorage.setItem('rowtextareaarr', upx.msg);
                                    }
                                }
                                newarrfile.clear();
                                document.getElementById('arrimgfileInput').value = '';
                                //清理预览图片并重新显示
                                if (rowtextareaarr == '' || rowtextareaarr == null || rowtextareaarr == 'null' || rowtextareaarr == 'undefined' || rowtextareaarr == ' ' || rowtextareaarr == undefined) {
                                    var mewimgaer = localStorage.getItem('rowtextareaarr');
                                } else {
                                    var mewimgaer = localStorage.getItem('rowtextareaarr') + ',' + rowtextareaarr;
                                }
                                if (mewimgaer && Array.isArray(mewimgaer.split(','))) { // 简化条件判断
                                    const mewimgaerarrxcv = mewimgaer.split(',');
                                    imgeye.innerHTML = ''; // 清空之前的内容

                                    // 使用DocumentFragment提高性能
                                    const fragment = document.createDocumentFragment();

                                    mewimgaerarrxcv.forEach(imgUrl => {
                                        //判断链接是相对路径还是第三方https或http
                                        if (imgUrl.startsWith('http://') || imgUrl.startsWith('https://')) {
                                            if (imgUrl) { // 确保imgUrl不是空字符串
                                                // 使用模板字符串创建HTML片段
                                                const divElement = `
                                                                    <div data-img="${imgUrl}" class="eyeimgall" 
                                                                         style="background-image: url(${imgUrl}); background-size: cover; background-position: center center;">
                                                                        <a class="detjsonimgall" data-img="${imgUrl}">
                                                                            <i class="fa fa-trash"></i>
                                                                        </a>
                                                                    </div>
                                                                `;
                                                // 将字符串转换为真实DOM元素并添加到fragment
                                                fragment.appendChild(htmlToElement(divElement));
                                            }
                                        } else {
                                            if (imgUrl) { // 确保imgUrl不是空字符串
                                                // 使用模板字符串创建HTML片段
                                                const divElement = `
                                                                        <div data-img="${imgUrl}" draggable="true" class="eyeimgall" 
                                                                             style="background-image: url(${imgUrl}); background-size: cover; background-position: center center;">
                                                                            <a class="detpostimgall" data-img="${imgUrl}">
                                                                                <i class="fa fa-trash"></i>
                                                                            </a>
                                                                            <div class="webimg">已上传</div>
                                                                        </div>
                                                                    `;
                                                // 将字符串转换为真实DOM元素并添加到fragment
                                                fragment.appendChild(htmlToElement(divElement));

/**拖动**/
// 创建“设为封面”元素并添加到body
const setAsCoverElement = document.createElement('div');
setAsCoverElement.id = 'setAsCover';
setAsCoverElement.textContent = '设为封面';
setAsCoverElement.style.display = 'none';
document.body.appendChild(setAsCoverElement);
let isDroppedOnCover = false; // 标志变量
// 监听拖动事件
document.addEventListener('dragstart', function(event) {
    if (event.target.classList.contains('eyeimgall')) {
        setAsCoverElement.style.display = 'block';
        event.dataTransfer.setData('text/plain', event.target.dataset.img);
        isDroppedOnCover = false; // 重置标志变量
    }
});

document.addEventListener('dragend', function(event) {
    setAsCoverElement.style.display = 'none';
    setAsCoverElement.style.backgroundColor = '#000'; // 恢复背景颜色
    if (!isDroppedOnCover) {
        event.target.classList.remove('dragging');//移除拖动效果
    }
});

setAsCoverElement.addEventListener('dragover', function(event) {
    event.preventDefault();
});
setAsCoverElement.addEventListener('dragenter', function(event) {
    setAsCoverElement.style.backgroundColor = 'var(--hover-color)'; // 更改背景颜色
});

setAsCoverElement.addEventListener('dragleave', function(event) {
    setAsCoverElement.style.backgroundColor = '#000'; // 恢复背景颜色
});
setAsCoverElement.addEventListener('drop', function(event) {
    event.preventDefault();
    const imgUrl = event.dataTransfer.getData('text/plain');
    const inputElement = document.getElementById('rowimg');
    if (inputElement) {
        inputElement.value = imgUrl;
    }
    setAsCoverElement.style.display = 'none';
    isDroppedOnCover = true; // 更新标志变量

});
//阻止默认拖放行为
document.addEventListener('dragover', function(event) {
    event.preventDefault();
});
document.addEventListener('drop', function(event) {
    event.preventDefault();
});

/**拖动结束**/
                                            }
                                        }

                                    });

                                    // 一次性将fragment内容添加到imgeye
                                    imgeye.appendChild(fragment);
                                }

                                // 辅助函数，将HTML字符串转换为DOM元素
                                function htmlToElement(html) {
                                    const template = document.createElement('template');
                                    html = html.trim(); // Never return a text node of whitespace as the result
                                    template.innerHTML = html;
                                    return template.content.firstChild;
                                }

                                //获取所有删除按钮
                                const detjsonimgall = document.querySelectorAll('.detjsonimgall');
                                if (detjsonimgall.length > 0) {
                                    //按钮点击事件
                                    detjsonimgall.forEach(function (det) {
                                        det.addEventListener('click', function (e) {
                                            const edata = det.getAttribute('data-img');
                                            if (edata) {
                                                //从rowtextareaarr移除edata
                                                rowtextareaarr.splice(rowtextareaarr.indexOf(edata), 1);
                                                //找到所有类名为eyeimgall的div
                                                const divs = document.querySelectorAll('.eyeimgall');
                                                //找到所有divs里data-img属性等于edata的div并执行删除
                                                divs.forEach(function (div) {
                                                    if (div.getAttribute('data-img') === edata) {
                                                        div.remove();//删除div
                                                    }
                                                });
                                                //判断是否删除了最后一张图片，是则隐藏预览图片div 
                                                if (newarrfile.size < 1 && rowtextareaarr.length < 1 && upyes == 1 && upyesx != 0 && (localStorage.getItem('rowtextareaarr') == null || localStorage.getItem('rowtextareaarr') == '' || localStorage.getItem('rowtextareaarr') == '[]' || localStorage.getItem('rowtextareaarr') == undefined)) {
                                                    xcdesss.style.display = 'none';
                                                    imgeye.innerHTML = '';
                                                    document.getElementById('arrimgfileInput').value = '';
                                                }
                                            }
                                        })
                                    });

                                }

                                            if(yesxxx==2){
                                                const detpostimgallvc = document.querySelectorAll('.detpostimgall');//所有删除按钮
                                                detpostimgallvc.forEach(function (det) {
                                                    det.addEventListener('click', function (e) {
                                                        const edataimg = det.getAttribute('data-img');
                                                        if (edataimg) {
                                                            if (confirm("确定删除已上传图片吗？")) {
                                                                postdelimg(edataimg);
                                                            }
                                                        }
                                                    });
                                                });
                                            }
                                //清空previewedImages的set
                                previewedImages.clear();

                            } else if (upx.error == 600) {
                                updateProgress(0);
                                alert('<font>(｡ŏ_ŏ)</font> 上传失败！');
                                newarrfile.clear();
                                document.getElementById('arrimgfileInput').value = '';
                            } else if (upx.error == 404) {
                                updateProgress(0);
                                alert('<font>(｡ŏ_ŏ)</font> 没有上传图片！');
                                newarrfile.clear();
                                document.getElementById('arrimgfileInput').value = '';
                            } else if (upx.error == 400) {
                                updateProgress(0);
                                alert('<font>(｡ŏ_ŏ)</font> 含有非法图片！');
                                newarrfile.clear();
                                document.getElementById('arrimgfileInput').value = '';
                            } else if (upx.error == 501) {
                                updateProgress(0);
                                alert('<font>(｡ŏ_ŏ)</font> 单次上传不能超过50张！');
                                newarrfile.clear();
                                document.getElementById('arrimgfileInput').value = '';
                            } else if (upx.error == 502) {
                                updateProgress(0);
                                alert('<font>(｡ŏ_ŏ)</font> 单次上传不能超过25M！');
                                newarrfile.clear();
                                document.getElementById('arrimgfileInput').value = '';
                            }else if (upx.error == 503) {
                                updateProgress(0);
                                alert('<font>(｡ŏ_ŏ)</font> 存在超限制的图片！');
                                newarrfile.clear();
                                document.getElementById('arrimgfileInput').value = '';
                            } else if (upx.error == 504) {
                                updateProgress(0);
                                alert('<font>(｡ŏ_ŏ)</font> 图片格式错误！');
                                newarrfile.clear();
                                document.getElementById('arrimgfileInput').value = '';
                            } else {
                                updateProgress(0);
                                alert('<font>(｡ŏ_ŏ)</font> 程序错误！');
                            }

                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            alert('<font>(｡ŏ_ŏ)</font> 错误请求：上传失败！');
                            console.log(jqXHR.responseText);
                        }
                    })
                    // 假进度条
                    function updateProgress(percentComplete) {
                        var percentage = document.getElementById('percentage');//上传进度条
                        if (!percentage) return;
                
                        // 清除之前的定时器
                        clearInterval(window.progressInterval);
                
                        if (percentComplete === 100) {
                            // 直接显示 100% 并在 1 秒后隐藏进度条
                            percentage.style.display = 'flex';
                            percentage.style.width = '100%';
                            percentage.innerHTML = "<span>100%</span>";
                            setTimeout(function () {
                                percentage.style.display = 'none';
                                percentage.innerHTML = "";
                                percentage.style.width = "";
                            }, 1000);
                        } else if (percentComplete === 0) {
                            // 从当前进度条位置逐步减少到0

                            var currentProgress = parseInt(percentage.style.width) || 0;
                            window.progressInterval = setInterval(function () {
                                if (currentProgress > 0) {
                                    currentProgress--;
                                    percentage.style.width = currentProgress + '%';
                                    percentage.innerHTML = "<span>" + currentProgress + "%</span>";
                                } else {
                                    clearInterval(window.progressInterval);
                                    percentage.style.display = 'none';
                                    percentage.innerHTML = "";
                                    percentage.style.width = "";
                                }
                            }, 20); 

                        } else {
                            // 获取当前进度
                            var currentProgress = parseInt(percentage.style.width) || 0;
                            if (currentProgress > percentComplete) {
                                currentProgress = percentComplete; // 如果当前进度大于目标进度，重置为目标进度
                            }
                
                            // 开始虚假进度动画
                            window.progressInterval = setInterval(function () {
                                if (currentProgress < 100) {
                                    let speed;
                                    if (currentProgress < 80) {
                                        speed = 200; // 前面 80% 每 200ms 增加 1%
                                    } else if (currentProgress < 99) {
                                        speed = 2000; // 超过 80% 且未到 99% 每 2000ms 增加 1%
                                    } else {
                                        clearInterval(window.progressInterval); // 到达 99% 停止动画
                                        return;
                                    }
                
                                    currentProgress++;
                                    percentage.style.display = 'flex';
                                    percentage.style.width = currentProgress + '%';
                                    percentage.innerHTML = "<span>" + currentProgress + "%</span>";
                
                                    // 如果动画还没完就返回 100 了，加快速度到达 100%
                                    if (percentComplete === 100) {
                                        clearInterval(window.progressInterval);
                                        var fastInterval = setInterval(function () {
                                            if (currentProgress < 100) {
                                                currentProgress++;
                                                percentage.style.width = currentProgress + '%';
                                                percentage.innerHTML = "<span>" + currentProgress + "%</span>";
                                            } else {
                                                clearInterval(fastInterval);
                                                setTimeout(function () {
                                                    percentage.style.display = 'none';
                                                    percentage.innerHTML = "";
                                                    percentage.style.width = "";
                                                }, 1000);
                                            }
                                        }, 50); // 加快速度，每 50ms 增加 1%
                                    }
                                } else {
                                    clearInterval(window.progressInterval); // 达到 100%，清除定时器
                                }
                            }, 200); // 初始速度为每 200ms 增加 1%
                        }
                    }
                




                } else {
                    alert('<font>(｡ŏ_ŏ)</font> 没有待上传图片！');
                    newarrfile.clear();
                    arrimgfileInput.value = '';
                }
            }
        }

    }
    if (yesxxx == 0) {
        const detpostimgall = document.querySelectorAll('.detpostimgall');//所有删除按钮
        detpostimgall.forEach(function (det) {
            det.addEventListener('click', function (e) {
                const edataimg = det.getAttribute('data-img');
                if (edataimg) {
                    if (confirm("确定删除已上传图片吗？")) {
                        postdelimg(edataimg);
                    }
                }
            });
        });
    }

    //服务器图片删除函数(超过限制，不展示)
    function postdelimg(imgurl) {
        //判断图片地址是否是http或https开头
        if (imgurl.startsWith('http://') || imgurl.startsWith('https://')) {
            alert("<font>(｡ŏ_ŏ)</font> 远程图片无法删除！");//提示
        } else {
            //判断路径结尾后缀是否是图片后缀
            if (imgurl.endsWith('.jpg') || imgurl.endsWith('.jpeg') || imgurl.endsWith('.png') || imgurl.endsWith('.gif') || imgurl.endsWith('.bmp') || imgurl.endsWith('.webp')|| imgurl.endsWith('.ico')|| imgurl.endsWith('.avif')) {
                //判断图片地址开头是否是/upload/或upload/或./upload/或../upload/
                if (imgurl.startsWith('/upload/') || imgurl.startsWith('upload/') || imgurl.startsWith('./upload/') || imgurl.startsWith('../upload/')) {
                    $.ajax({
                        url: '/api/delimg.php', // 请求地址
                        type: 'POST',   // 请求类型
                        data: {
                            url: imgurl,//图片地址
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
                                if (newarrfile.size < 1 && rowtextareaarr.length < 1 && eyeimgallert.length <= 1) {
                                    upyesx = 1;
                                    xcdesss.style.display = 'none';
                                    imgeye.innerHTML = '';
                                    document.getElementById('arrimgfileInput').value = '';
                                }
                                const rowtextareaarrnew = localStorage.getItem('rowtextareaarr');
                                if (rowtextareaarrnew) {
                                    // 使用逗号分割字符串得到数组
                                    const datarowtextareaarrnew = rowtextareaarrnew.split(',').map(item => item.trim()); // 去除前后空白
                                    // 去除数组里等于imgurl的值
                                    datarowtextareaarrnew.forEach((item, index) => {
                                        if (item === imgurl) {
                                            datarowtextareaarrnew.splice(index, 1);
                                        }
                                    });
                                    // 重新组合为以逗号分隔的字符串
                                    const rowtextareaarrnewStr = datarowtextareaarrnew.join(',');
                                    // 重新赋值给localStorage
                                    localStorage.setItem("rowtextareaarr", rowtextareaarrnewStr);
                                }
                            } else if (postdel == 404) {
                                alert("<font>(｡ŏ_ŏ)</font> 图片不存在！");
                                const rowtextareaarrnew = localStorage.getItem('rowtextareaarr');
                                if (rowtextareaarrnew) {
                                    // 使用逗号分割字符串得到数组
                                    const datarowtextareaarrnew = rowtextareaarrnew.split(',').map(item => item.trim()); // 去除前后空白
                                    // 去除数组里等于imgurl的值
                                    datarowtextareaarrnew.forEach((item, index) => {
                                        if (item === imgurl) {
                                            datarowtextareaarrnew.splice(index, 1);
                                        }
                                    });
                                    // 重新组合为以逗号分隔的字符串
                                    const rowtextareaarrnewStr = datarowtextareaarrnew.join(',');
                                    // 重新赋值给localStorage
                                    localStorage.setItem("rowtextareaarr", rowtextareaarrnewStr);
                                }
                            } else if (postdel == 400) {
                                alert("<font>(｡ŏ_ŏ)</font> 不能删除别人的图片！");
                            } else if (postdel == 600) {
                                alert("<font>(｡ŏ_ŏ)</font> 删除失败！");
                            } else {
                                alert("<font>(｡ŏ_ŏ)</font> 程序错误！");
                            }
                        }
                    });
                } else {
                    alert("<font>(｡ŏ_ŏ)</font> 图片地址错误！");
                }
            } else {
                alert("<font>(｡ŏ_ŏ)</font> 图片格式错误！");
            }
        }
    }
/***提交**/
var imgallsubmit=document.getElementById('newwordsubmit');//提交按钮
var wordform=document.getElementById('wordform');//表单
if(imgallsubmit&&wordform){
    imgallsubmit.addEventListener('click', function(e) {
        e.preventDefault();//阻止默认提交

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

                    const newrowdowif = wordform.rowdwif.value;//下载权限
                    const imgtexttop = wordform.imgtexttop.value;//摘要显示位置

                    if (newrowhead == "") {
                        alert("<font>(｡ŏ_ŏ)</font> 标题不能为空！");
                        return;
                    }
        
                    if (rowtextareaarr!=""&&rowtextareaarr!=null&&rowtextareaarr!='null'&&rowtextareaarr!=undefined){
                        if (localStorage.getItem('rowtextareaarr')!=null&&localStorage.getItem('rowtextareaarr')!=""&&localStorage.getItem('rowtextareaarr')!='null'&&localStorage.getItem('rowtextareaarr')!=undefined){
                            var rowtextarea = rowtextareaarr+','+localStorage.getItem('rowtextareaarr');
                        }else{
                            var rowtextarea = rowtextareaarr.join(',');
                        }                        
                    }else{
                        var rowtextarea = localStorage.getItem('rowtextareaarr');
                    }

                    if (rowtextarea==""||rowtextarea==null||rowtextarea==undefined){
                        alert("<font>(｡ŏ_ŏ)</font> 相册不能为空！");
                        return;
                    }
                    const rowtextareaif = rowtextarea.split(',').map(item => item.trim());
                    //判断每一个数组内容的后缀是否是图片
                    for (let i = 0; i < rowtextareaif.length; i++) {

                        if (!isValidImageUrl(rowtextareaif[i])) {
                            alert("<font>(｡ŏ_ŏ)</font> 相册图片格式错误！");
                            return;
                        }

                        if (!rowtextareaif[i].startsWith('http://') && !rowtextareaif[i].startsWith('https://') && !rowtextareaif[i].startsWith('/upload/') && !rowtextareaif[i].startsWith('upload/') && !rowtextareaif[i].startsWith('./upload/') && !rowtextareaif[i].startsWith('../upload/')) {
                            alert("<font>(｡ŏ_ŏ)</font> 相册路径有错误！");
                            return;
                        }
                    }
        
                    if (!isPositiveIntegerLike(newrowif)&&newrowif!=0&&newrowif!='') {
                        alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                        return;
                    }
        
                    if (!isPositiveIntegerLike(newrowvip) || !isPositiveIntegerLike(imgtexttop) || imgtexttop<1 || imgtexttop>2 || newrowvip<=0 || newrowvip>3||newrowdowif<1||newrowdowif>3||!isPositiveIntegerLike(newrowdowif)) {//范围1-3
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
                    function resetSubmitBtn() {
                        newwordsubmit.style.pointerEvents = "auto";
                        newwordsubmit.style.opacity = 1;
                        newwordsubmit.textContent = "提交";
                    }
                    imgallsubmit.style.pointerEvents="none";
                    imgallsubmit.style.opacity=0.5;
                    imgallsubmit.textContent="正在提交...";
            //ajax提交表单
            $.ajax({
                url: '/api/upimgall.php', // 请求地址
                type: 'POST',   // 请求类型
                data: {
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
                text: rowtextarea,//相册
                dowif:newrowdowif,//下载权限
                imageseye:imageseye,//游客可见
                vipimageseye:vipimageseye,//会员可见
                txttop:imgtexttop,//摘要位置
                },
                            success: function(all) { // 成功回调函数
                                if(all == 500){
                                    alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                                }else if(all == 200){
                                    //更新localStorage
                                    localStorage.setItem("rowtextareaarr","");
                                    //彻底删除localStorage
                                    localStorage.removeItem("rowtextareaarr");
                                    alert("<font>(｡ŏ_ŏ)</font> 发布成功，正在跳转！");
                                    setTimeout(function(){
                                        window.location.href = "popingzi.php?type=3";
                                    },2000);
                                }else if(all == 404){
                                    alert("<font>(｡ŏ_ŏ)</font> 标题或相册不能为空！");
                                    resetSubmitBtn();
                                }else if(all == 400){
                                    alert("<font>(｡ŏ_ŏ)</font> 标签格式不正确！");
                                    resetSubmitBtn();
                                }else if(all == 405){
                                    alert("<font>(｡ŏ_ŏ)</font> 相册存在非法图片！");
                                    resetSubmitBtn();
                                }else if(all == 401){
                                    alert("<font>(｡ŏ_ŏ)</font> 封面图片格式不正确！");
                                    resetSubmitBtn();
                                }else if(all == 402){
                                    alert("<font>(｡ŏ_ŏ)</font> 存在版权链接，请输入版权方名称！");
                                    resetSubmitBtn();
                                }else if(all == 4022){
                                    alert("<font>(｡ŏ_ŏ)</font> 版权链接不正确！");
                                    resetSubmitBtn();
                                }else if(all == 4023){
                                    alert("<font>(｡ŏ_ŏ)</font> 下载地址不正确！");
                                    resetSubmitBtn();
                                }else if(all == 403){
                                    alert("<font>(｡ŏ_ŏ)</font> 标题不能超过120字符！");
                                    resetSubmitBtn();
                                }else if(all == 600){
                                    alert("<font>(｡ŏ_ŏ)</font> 发布失败！");
                                    resetSubmitBtn();
                                }else if(all == 4024){
                                    alert("<font>(｡ŏ_ŏ)</font> 请勿修改摘要html格式！");
                                    resetSubmitBtn();
                                }else{
                                    alert("<font>(｡ŏ_ŏ)</font> 程序错误！"); 
                                    resetSubmitBtn();
                                }
                            }
              });
    });
}
});
