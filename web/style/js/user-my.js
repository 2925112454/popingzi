document.addEventListener('DOMContentLoaded', function() {
    const user_name = document.getElementById("user_name");//昵称文本框
    const user_a_name = document.getElementById("user_a_name");//修改昵称按钮
    const user_email = document.getElementById("user_email");//邮箱
    const user_a_email = document.getElementById("user_a_email");//修改邮箱按钮
    const user_tel = document.getElementById("user_tel");//手机号
    const user_a_tel = document.getElementById("user_a_tel");//修改手机号按钮
    const user_sex = document.getElementById("user_sex");//性别
    const user_a_sex = document.getElementById("user_a_sex");//修改性别按钮
    const user_url = document.getElementById("user_url");//网址
    const user_a_url = document.getElementById("user_a_url");//修改网址按钮
    const user_text = document.getElementById("user_text");//简介
    const user_a_text = document.getElementById("user_a_text");//修改简介按钮
    const user_edit_img = document.getElementById("user_edit_img");//修改头像按钮
    const user_pass = document.getElementById("user_pass");//修改密码按钮
    const user_pass_dialog = document.getElementById("user_pass_dialog");//修改密码对话框
    const user_dialog_pass_close = document.getElementById("user_dialog_pass_close");//关闭修改密码对话框
    const user_dialog_err=document.getElementById("user_dialog_err");//错误提示
    const user_pass_but=document.getElementById("user_pass_but");///提交按钮

    const user_yan_email = document.getElementById("user_yan_email");//验证邮箱按钮
    const user_email_dialog = document.getElementById("user_email_dialog");//验证邮箱对话框
    const user_dialog_email_close = document.getElementById("user_dialog_email_close");//关闭验证邮箱对话框

    const user_yan_tel = document.getElementById("user_yan_tel");//验证手机按钮
    const user_tel_dialog = document.getElementById("user_tel_dialog");//验证手机对话框
    const user_dialog_tel_close = document.getElementById("user_dialog_tel_close");//关闭验证手机对话框

    function escapeHtml(html) {
        return html.replace(/[&<>"']/g, function (match) {
            switch (match) {
                case '&': return '&amp;';
                case '<': return '&lt;';
                case '>': return '&gt;';
                case '"': return '&quot;';
                case "'": return '&#039;';
                default: return match;
            }
        });
    }

function editpost(value, type) {
    if (!type) return;

    const formData = new FormData();
    formData.append('type', type);

    // 如果是图片（Blob），添加到 formdata
    if (type === 'img' && value instanceof Blob) {
        const ext = value.type.split('/').pop();
        formData.append('avatar', value,'avatar'+'.'+ ext);
    } else {
        formData.append('value', value);
    }

    $.ajax({
        url: 'edit/', // 请求地址
        type: 'POST', // 请求类型
        dataType: 'json',
        data: formData,
        processData: false,
        contentType: false,
        success: function(edit) { // 成功回调函数
            if (edit.code == 200) {
                if (type == "img") {
                    //刷新页面
                    location.reload();
                }
            } else if (edit.code == 500) {
                alert("<font>(｡ŏ_ŏ)</font> " + edit.msg);
            } else {
                alert("<font>(｡ŏ_ŏ)</font> 服务器出现错误！");
            }
        },
        error: function(xhr, status, error) {
            console.error("请求失败:", error);
            alert("<font>(｡ŏ_ŏ)</font> 网络请求失败，请重试");
        }
    });
}

    //函数：双击文本框进行编辑
    function user_edit_name(e,type,text){
        const inputname = e;
        const inputtype = type;//文本框类型
        let inputtext = text;//原始内容

        // 特殊处理：如果是性别字段，使用下拉选择
        if(inputtype === "sex") {
            // 创建下拉选择框
            const select = document.createElement('select');
            select.className = 'form-select';
            
            // 添加选项
            const options = ['帅哥', '美女'];
            options.forEach(optionText => {
                const option = document.createElement('option');
                option.value = optionText;
                option.textContent = optionText;
                if(optionText === inputtext) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
            
            // 替换原来的内容
            inputname.innerHTML = '';
            inputname.appendChild(select);
            
            // 自动聚焦
            select.focus();
            
            // 失去焦点时处理
            select.onblur = function() {
                const selectedValue = select.value;
                inputname.innerHTML = selectedValue;
                
                // 处理性别值（1代表帅哥，2代表美女）
                let inputtext_now = selectedValue === "帅哥" ? "1" : "2";
                //判断是否修改
                if(select.value != inputtext) {
                    editpost(inputtext_now,inputtype);
                }
                
                // 恢复不可编辑状态
                inputname.contentEditable = false;
            };
            
            // 选择项变化时也触发
            select.onchange = function() {
                select.blur();
            };
            
            return;
        }

        // 其他字段保持原来的处理方式
        const inputname_textx = inputname.innerText.replace(/\s+/g, "");
        const inputtext_newtextx = inputname_textx.replace(/<\/?[^>]*>/g,'');
        const inputtext_newtext2xx = inputtext_newtextx.replace(/[\r\n]/g,"");
        const inputtext_nowxx = escapeHtml(inputtext_newtext2xx);

        inputname.innerHTML = inputtext_nowxx;

        if(inputtype == "email"){
            if(inputname.innerText=="未填写邮箱"){
                inputname.innerHTML = "";
            }
        }else if(inputtype == "tel"){
            if(inputname.innerText=="未填写手机"){
                inputname.innerHTML = "";
            }
        }else if(inputtype == "url"){
            if(inputname.innerText=="未填写网址"){
                inputname.innerHTML = "https://";
            }
        }else if(inputtype == "text"){
            if(inputname.innerText=="未填写简介"){
                inputname.innerHTML = "";
            }
        }

        inputname.contentEditable = true;
        inputname.focus();

        // 将光标定位到文本末尾
        const range = document.createRange(); // 创建一个范围对象
        const selection = window.getSelection(); // 获取当前选区

        range.selectNodeContents(inputname); // 选择整个元素的内容
        range.collapse(false); // 将范围折叠到末尾（false 表示 collapse 到末尾）

        selection.removeAllRanges(); // 移除所有已有的选区
        selection.addRange(range); // 添加新的范围

        document.addEventListener('paste', function (e) {
            const activeElement = document.activeElement;
            // 确保当前焦点在一个 contentEditable 区域内或输入框中
            if (activeElement && (activeElement.contentEditable === 'true' || 
                ['INPUT', 'TEXTAREA'].includes(activeElement.tagName))) {
                e.preventDefault(); // 阻止默认粘贴行为
                // 获取剪贴板中的纯文本
                const clipboardText = (e.clipboardData || window.clipboardData).getData('text');
                // 插入纯文本到光标位置
                document.execCommand('insertText', false, clipboardText);
            }
        });

        inputname.onblur = function(){
            inputname.contentEditable = false;

            if(inputname.innerText == inputtext){
                return;
            }

            //判断类型是否匹配
            if(inputtype == "name"){
                if(inputname.innerText.length > 12){
                    alert("<font>(｡ŏ_ŏ)</font> 昵称不能超过12个字符！");
                    inputname.innerHTML = inputtext;
                    return;
                }
                if(inputname.innerText.length < 1){
                    alert("<font>(｡ŏ_ŏ)</font> 昵称不能为空！");
                    inputname.innerHTML = inputtext;
                    return;
                }
            }

            if(inputtype == "email"){
                if(inputname.innerText.length > 0){
                    // 验证邮箱格式
                    if(!/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/.test(inputname.innerText)){
                        alert("<font>(｡ŏ_ŏ)</font> 邮箱不正确！");
                        inputname.innerHTML = inputtext;
                        return;
                    }
                }else{
                    alert("<font>(｡ŏ_ŏ)</font> 邮箱不能为空！");
                    inputname.innerHTML =inputtext;
                    return;
                }
            }

            if(inputtype == "tel"){
                if(inputname.innerText.length > 0){
                    // 验证手机格式
                    if(!/^1[3456789]\d{9}$/.test(inputname.innerText)){
                        alert("<font>(｡ŏ_ŏ)</font> 手机号码不正确！");
                        inputname.innerHTML = inputtext;
                        return;
                    }
                }else{
                    inputname.innerHTML ='未填写手机';
                }
            }

            if(inputtype == "url"){
                if(inputname.innerText.length > 0){
                    //验证网址格式(只能是http或https)
                    if(!/^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)$/.test(inputname.innerText)){
                        alert("<font>(｡ŏ_ŏ)</font> 网址格式不正确！");
                        inputname.innerHTML = inputtext;
                        return;
                    }
                }else{
                    inputname.innerHTML ='未填写网址';
                }
            }

            if(inputtype == "text"){
                if(inputname.innerText.length > 0){
                    if(inputname.innerText.length > 120){
                        alert("<font>(｡ŏ_ŏ)</font> 简介不能超过120个字符！");
                        inputname.innerHTML = inputname.innerText.substring(0, 120);
                        return;
                    }
                }else{
                    inputname.innerHTML ='未填写简介';
                }
            }

            const inputname_text = inputname.innerText.replace(/\s+/g, "");
            const inputtext_newtext = inputname_text.replace(/<\/?[^>]*>/g,'');
            const inputtext_newtext2 = inputtext_newtext.replace(/[\r\n]/g,"");
            let inputtext_now = escapeHtml(inputtext_newtext2);

            //将“未填写简介、未填写网址、未填写手机、未填写邮箱”等转换为空值
            if(inputname_text=="未填写简介"||inputname_text=="未填写网址"||inputname_text=="未填写手机"||inputname_text=="未填写邮箱"){
                inputtext_now = "";
            }
            editpost(inputtext_now,inputtype);
        } 
    }
    
    //绑定事件
    if(user_name&&user_a_name&&user_email&&user_a_email&&user_tel&&user_a_tel&&user_sex&&user_a_sex&&user_url&&user_a_url&&user_text&&user_a_text){
        user_name.ondblclick = (e) => user_edit_name(user_name,"name",user_name.innerText);
        user_a_name.onclick = (e) => user_edit_name(user_name,"name",user_name.innerText);
        user_email.ondblclick = (e) => user_edit_name(user_email,"email",user_email.innerText);
        user_a_email.onclick = (e) => user_edit_name(user_email,"email",user_email.innerText);
        user_tel.ondblclick = (e) => user_edit_name(user_tel,"tel",user_tel.innerText);
        user_a_tel.onclick = (e) => user_edit_name(user_tel,"tel",user_tel.innerText);
        user_sex.ondblclick = (e) => user_edit_name(user_sex,"sex",user_sex.innerText);
        user_a_sex.onclick = (e) => user_edit_name(user_sex,"sex",user_sex.innerText);
        user_url.ondblclick = (e) => user_edit_name(user_url,"url",user_url.innerText);
        user_a_url.onclick = (e) => user_edit_name(user_url,"url",user_url.innerText);
        user_text.ondblclick = (e) => user_edit_name(user_text,"text",user_text.innerText);
        user_a_text.onclick = (e) => user_edit_name(user_text,"text",user_text.innerText);
    }

if (user_edit_img) {
    user_edit_img.addEventListener('click', function (e) {
        e.preventDefault();

        let hederimg = "";

        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = 'image/png,image/jpeg,image/gif';
        fileInput.style.display = 'none';

        fileInput.addEventListener('change', async function () {
            const file = fileInput.files[0];
            if (!file) return;

            try {
                if (file.type === 'image/gif') {
                    // ✅ GIF：直接读取 Data URL
                    hederimg = await new Promise((resolve, reject) => {
                        const reader = new FileReader();
                        reader.onload = function (event) {
                            resolve(event.target.result);
                        };
                        reader.onerror = reject;
                        reader.readAsDataURL(file);
                    });
                } else {
                    // ✅ 处理非 GIF 图片
                    hederimg = await new Promise((resolve, reject) => {
                        const img = new Image();
                        const reader = new FileReader();

                        reader.onload = function (event) {
                            img.src = event.target.result;

                            img.onload = function () {
                                let croppedImage;

                                // 判断是否需要裁剪为正方形
                                if (img.width !== img.height) {
                                    const size = Math.min(img.width, img.height);
                                    const canvas = document.createElement('canvas');
                                    const ctx = canvas.getContext('2d');

                                    canvas.width = size;
                                    canvas.height = size;
                                    
                                    // 不填充背景色，保留透明通道
                                    ctx.drawImage(
                                        img,
                                        (img.width - size) / 2,
                                        (img.height - size) / 2,
                                        size,
                                        size,
                                        0,
                                        0,
                                        size,
                                        size
                                    );

                                    // 根据原始文件类型决定输出格式
                                    if (file.type === 'image/png') {
                                        croppedImage = canvas.toDataURL('image/webp', 1.0);
                                    } else if (file.type === 'image/jpeg') {
                                        croppedImage = canvas.toDataURL('image/webp', 0.8);
                                    }
                                } else {
                                    // 已是正方形
                                    const canvas = document.createElement('canvas');
                                    const ctx = canvas.getContext('2d');
                                    canvas.width = img.width;
                                    canvas.height = img.height;
                                    // 如果是 PNG 并且你想保留透明通道，请不要填充背景色
                                    if (file.type === 'image/png') {
                                          ctx.drawImage(img, 0, 0);
                                        croppedImage = canvas.toDataURL('image/webp', 1.0); // ✅ 强制转为无损 WebP
                                    } else {
                                        // JPG 等非透明图片需要白底填充后再转 WebP
                                        ctx.fillStyle = "#fff";
                                        ctx.fillRect(0, 0, canvas.width, canvas.height);
                                        ctx.drawImage(img, 0, 0);
                                        croppedImage = canvas.toDataURL('image/webp', 0.8); // 有损 WebP
                                    }
                                }

                                // 检查是否需要缩放到 240x240
                                const finalImg = new Image();
                                finalImg.src = croppedImage;
                                

                                finalImg.onload = function () {
                                    let targetWidth = finalImg.width;
                                    let targetHeight = finalImg.height;

                                    if (targetWidth > 240 || targetHeight > 240) {
                                        const canvas = document.createElement('canvas');
                                        const ctx = canvas.getContext('2d');
                                        canvas.width = 240;
                                        canvas.height = 240;

                                        // 只在处理 JPEG 时填充背景色
                                        if (file.type !== 'image/png') {
                                            ctx.fillStyle = "#fff"; // JPG 背景填充白底
                                            ctx.fillRect(0, 0, canvas.width, canvas.height);
                                        }
                                        
                                        ctx.drawImage(finalImg, 0, 0, 240, 240);

                                        // 根据原图类型选择输出格式
                                        if (file.type === 'image/png') {
                                            // 明确指定支持透明度的 WebP 格式
                                            resolve(canvas.toDataURL('image/webp', 1.0));
                                        } else {
                                            resolve(canvas.toDataURL('image/webp', 0.8));
                                        }
                                    } else {
                                        resolve(croppedImage);
                                    }
                                };
                            };
                        };

                        reader.onerror = reject;
                        reader.readAsDataURL(file);
                    });
                }

                // ✅ 将 Base64 数据 URL 转换为 Blob 对象
                    function dataURLtoBlob(dataURL) {
                        const arr = dataURL.split(',');
                        const mime = arr[0].match(/:(.*?);/)[1]; // 提取 MIME 类型
                        const bstr = atob(arr[1]); // 解码 base64 字符串
                        let n = bstr.length;
                        const u8arr = new Uint8Array(n);
                        while (n--) {
                            u8arr[n] = bstr.charCodeAt(n); // 将字符转为二进制字节
                        }
                        return new Blob([u8arr], { type: mime }); // ✅ 返回 Blob 对象
                    }

                    // ✅将裁剪后的图片转换为 Blob
                    hederimg_blob = dataURLtoBlob(hederimg);
                    editpost(hederimg_blob,"img");
            } catch (err) {
                console.error("处理图片失败", err);
            }
        });

        document.body.appendChild(fileInput);
        fileInput.click();
        document.body.removeChild(fileInput);
    });
}

if(user_pass_dialog&&user_pass){
    user_pass.addEventListener("click", function () {
        user_pass_dialog.showModal();
    });
    user_dialog_pass_close.addEventListener("click", function () {
        user_pass_dialog.close();
    });
    if(user_dialog_err&&user_pass_but){
        function user_pass_but_err(text,color=""){
                user_dialog_err.innerHTML = text;
                user_dialog_err.style.display = "block";
                user_dialog_err.style.color = color;
                setTimeout(function () {
                    user_dialog_err.innerHTML = "";
                    user_dialog_err.style.display = "none";
                    user_dialog_err.style.color = '';
                }, 2000);
        }

        user_pass_but.addEventListener("click", function () {
            const user_pass_y = document.getElementById("user_pass_y");//原始密码
            const user_pass_new = document.getElementById("user_pass_new");//新密码
            const user_pass_new_two = document.getElementById("user_pass_new_two");//新密码确认
            if(user_pass_y.value&&user_pass_new.value&&user_pass_new_two.value){
                if(user_pass_new.value==user_pass_new_two.value){
                    //判断密码是否是纯数字
                    if(/^\d+$/.test(user_pass_new.value)||/^\d+$/.test(user_pass_new_two.value)||/^\d+$/.test(user_pass_y.value)){
                        user_pass_but_err('密码不能是纯数字',"")
                    }else{
                        if(user_pass_new.value.length<6||user_pass_new_two.value.length<6||user_pass_y.value.length<6){
                            user_pass_but_err('密码不能少于6位',"")
                        }else{
                            $.ajax({
                                url: 'edit/pass.php', // 请求地址
                                type: 'POST', // 请求类型
                                dataType: 'json',
                                data: {
                                    pass:user_pass_y.value,
                                    newpass:user_pass_new_two.value,
                                },
                                success: function(pass) { // 成功回调函数
                                    if (pass.code == 200) {
                                        user_pass_but_err('修改成功，请重新登录！',"#8bc34a")
                                        setTimeout(function(){
                                            window.location.reload();
                                        },3000)
                                    } else if (pass.code == 500) {
                                        user_pass_but_err(pass.msg,"")
                                    } else {
                                        user_pass_but_err('服务器出错',"")
                                    }
                                },
                            });
                        }
                    }
                }else{
                    user_pass_but_err('两次密码不一致',"")
                }

            }else{
                user_pass_but_err('密码不能为空',"")
            }
        })
    }
}
    
if(user_yan_email&&user_email_dialog&&user_dialog_email_close){
    const goemailcode = document.getElementById("goemailcode");//获取验证码的消息提示
    const user_email_but = document.getElementById("user_email_but");//提交按钮
    const user_email_code = document.getElementById("user_email_y");///验证码input
    const user_dialog_email_err = document.getElementById("user_dialog_email_err");//错误提示
    user_yan_email.addEventListener("click", function () {
        user_email_dialog.showModal();
        if(goemailcode){
            goemailcode.innerHTML = "发送验证码中...";
            $.ajax({
                url: 'edit/email.php', // 请求地址
                type: 'POST', // 请求类型
                dataType: 'json',
                success: function(email) { // 成功回调函数
                    if (email.code == 200) {
                        goemailcode.innerHTML = "验证码已发送，请注意查收！";
                    }else if (email.code == 500) {
                        goemailcode.innerHTML = email.msg;
                    } else {
                        goemailcode.innerHTML = "服务器出错";
                    }
                }
            })
        }
    });
    user_dialog_email_close.addEventListener("click", function () {
        user_email_dialog.close();
    });

    if(user_email_but&&user_email_code&&user_dialog_email_err){
        user_email_but.addEventListener("click", function () {
            if(user_email_code.value){
                //判断是否超过6位
                if(user_email_code.value.length<6||user_email_code.value.length>6){
                    user_dialog_email_err.innerHTML = "验证码不正确";
                    user_dialog_email_err.style.display = "block";
                    setTimeout(function () {
                        user_dialog_email_err.style.display = "";
                        user_dialog_email_err.innerHTML = "";
                    }, 2000);
                }else{
                    $.ajax({
                        url: 'edit/code.php', // 请求地址
                        type: 'POST', // 请求类型
                        dataType: 'json',
                        data: {
                            code:user_email_code.value,
                            type:"email",
                        },
                        success: function(emailcode) { // 成功回调函数
                            if (emailcode.code == 200) {
                                window.location.reload();
                            }else if (emailcode.code == 500) {
                                user_dialog_email_err.innerHTML = emailcode.msg;
                                user_dialog_email_err.style.display = "block";
                                setTimeout(function () {
                                    user_dialog_email_err.style.display = "";
                                    user_dialog_email_err.innerHTML = "";
                                }, 2000);
                            } else {
                            user_dialog_email_err.innerHTML = "服务器出错";
                            user_dialog_email_err.style.display = "block";
                            setTimeout(function () {
                                user_dialog_email_err.innerHTML = "";
                                user_dialog_email_err.style.display = "";
                            }, 2000);
                            }
                        }
                    })
                }                
            }else{
                user_dialog_email_err.innerHTML = "验证码不能为空";
                user_dialog_email_err.style.display = "block";
                setTimeout(function () {
                    user_dialog_email_err.style.display = "";
                    user_dialog_email_err.innerHTML = "";
                }, 2000);
            }
        })
    }
}


if(user_yan_tel&&user_tel_dialog&&user_dialog_tel_close){
    const gotelcode = document.getElementById("gotelcode");//获取验证码的消息提示
    const user_tel_but = document.getElementById("user_tel_but");//提交按钮
    const user_tel_code = document.getElementById("user_tel_y");///验证码input
    const user_dialog_tel_err = document.getElementById("user_dialog_tel_err");//错误提示
    user_yan_tel.addEventListener("click", function () {
        user_tel_dialog.showModal();
        if(gotelcode){
            gotelcode.innerHTML = "发送验证码中...";
            $.ajax({
                url: 'edit/tel.php', // 请求地址
                type: 'POST', // 请求类型
                dataType: 'json',
                success: function(tel) { // 成功回调函数
                    if (tel.code == 200) {
                        gotelcode.innerHTML = "验证码已发送，请注意查收！";
                    }else if (tel.code == 500) {
                        gotelcode.innerHTML = tel.msg;
                    } else {
                        gotelcode.innerHTML = "服务器出错";
                    }
                }
            })
        }
    });
    user_dialog_tel_close.addEventListener("click", function () {
        user_tel_dialog.close();
    });

    if(user_tel_but&&user_tel_code&&user_dialog_tel_err){
        user_tel_but.addEventListener("click", function () {
            if(user_tel_code.value){
                //判断是否超过6位
                if(user_tel_code.value.length<6||user_tel_code.value.length>6){
                    user_dialog_tel_err.innerHTML = "验证码不正确";
                    user_dialog_tel_err.style.display = "block";
                    setTimeout(function () {
                        user_dialog_tel_err.style.display = "";
                        user_dialog_tel_err.innerHTML = "";
                    }, 2000);
                }else{
                    $.ajax({
                        url: 'edit/code.php', // 请求地址
                        type: 'POST', // 请求类型
                        dataType: 'json',
                        data: {
                            code:user_tel_code.value,
                            type:"tel",
                        },
                        success: function(telcode) { // 成功回调函数
                            if (telcode.code == 200) {
                                window.location.reload();
                            }else if (telcode.code == 500) {
                                user_dialog_tel_err.innerHTML = telcode.msg;
                                user_dialog_tel_err.style.display = "block";
                                setTimeout(function () {
                                    user_dialog_tel_err.style.display = "";
                                    user_dialog_tel_err.innerHTML = "";
                                }, 2000);
                            } else {
                            user_dialog_tel_err.innerHTML = "服务器出错";
                            user_dialog_tel_err.style.display = "block";
                            setTimeout(function () {
                                user_dialog_tel_err.innerHTML = "";
                                user_dialog_tel_err.style.display = "";
                            }, 2000);
                            }
                        }
                    })
                }                
            }else{
                user_dialog_tel_err.innerHTML = "验证码不能为空";
                user_dialog_tel_err.style.display = "block";
                setTimeout(function () {
                    user_dialog_tel_err.style.display = "";
                    user_dialog_tel_err.innerHTML = "";
                }, 2000);
            }
        })
    }
}


});    