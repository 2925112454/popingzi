document.addEventListener('DOMContentLoaded', function() {
    function msg_alert(text,code){
        if(code == 200){
            alert('<font>(ô‿ô)</font> '+text);
        }else{
            alert('<font>(｡ŏ_ŏ)</font> '+text);
        }
    }
    /* 修改 */
    const navfldialog = document.getElementById('navfldialog');//修改弹出层
    const navfldialogclose = document.getElementById('navfldialogclose');//弹出层关闭按钮
    const serviceli= document.querySelectorAll('.serviceli');
    const navfldialoginput= document.getElementById('navfldialoginput');//输入框
    const navfldialogbut= document.getElementById('navfldialogbut');//确认按钮
    function isNonNegativeInteger(value) {
        if (value === null || value === undefined || typeof value === 'boolean' || typeof value === 'object') {
            return false;
        }
        const strValue = String(value).trim();
        if (strValue === '') {
            return false;
        }
        const integerReg = /^[0-9]+$/;
        if (!integerReg.test(strValue)) {
            return false;
        }
        const numValue = Number(strValue);
        return Number.isInteger(numValue) && numValue > 0;
    }
        // 在函数外部定义定时器变量
        let msgalertTimer = null;
        function msgalert(text, code,value){
            const navfldialogerr = document.getElementById('navfldialogerr');
            if (msgalertTimer) {
                clearTimeout(msgalertTimer);
                msgalertTimer = null;
            }
            if(code == 200){
                navfldialogerr.style.display = 'block';
                navfldialogerr.innerHTML = text;
                navfldialogerr.style.color = 'green';
                const serviceli_div = document.querySelectorAll('.serviceli[data-id="' + navfldialogbut.getAttribute('data-id') + '"]');
                if(serviceli_div){
                    serviceli_div.forEach(function(item) {
                        item.setAttribute('data-txt', value);
                    });
                    const spanElement = serviceli_div[0].querySelector('span');
                    if(spanElement){
                        spanElement.innerHTML = value;
                    }
                }
                msgalertTimer = setTimeout(function(){
                    navfldialogerr.style.display = 'none';
                    navfldialogerr.innerHTML = "";
                    navfldialogerr.style.color = '';
                }, 1500);
                
            }else{
                navfldialogerr.style.display = 'block';
                navfldialogerr.innerHTML = text;
                msgalertTimer = setTimeout(function(){
                    navfldialogerr.style.display = 'none';
                    navfldialogerr.innerHTML = "";
                }, 2000);
            }
        }
        function hasHtmlTag(str) {
            if (str === null || str === undefined || typeof str !== 'string') {
                return false;
            }
            const trimmedStr = str.trim();
            if (trimmedStr === '') {
                return false;
            }
            const htmlTagReg = /<\/?[a-zA-Z0-9]+[^>]*>/gi;
            return htmlTagReg.test(trimmedStr);
        }
    
    if(navfldialog&&navfldialogclose&&serviceli.length>0&&navfldialoginput&&navfldialogbut){
        serviceli. forEach(function(item){
            item.addEventListener('dblclick', function(e) {
                e.preventDefault();
                navfldialog.showModal();
                navfldialog.style.display = 'flex';
                const edit_txt = item.getAttribute('data-txt');
                const edit_id = item.getAttribute('data-id');
                if(edit_txt){
                    navfldialoginput.value = edit_txt;
                }
                if(edit_id){
                    navfldialogbut.setAttribute('data-id',edit_id);
                }
            });
        })
    }
    if(navfldialogclose&&navfldialogbut&&navfldialoginput){
        navfldialogclose.addEventListener('click', function(e) {
            navfldialog.close();
            navfldialog.style.display = 'none';
            navfldialogbut.setAttribute('data-id','');
            navfldialoginput.value = '';
        });
    }
    if(navfldialogbut&&navfldialoginput){
        navfldialogbut.addEventListener('click', function(e) {
            e.preventDefault();
            const new_edit_id = navfldialogbut.getAttribute('data-id');
            const new_edit_txt = navfldialoginput.value;
           if(new_edit_txt){
                if(new_edit_id){
                    if (isNonNegativeInteger(new_edit_id)) {
                        if(hasHtmlTag(new_edit_txt)){
                            msgalert('标签不能包含HTML标签！',500);
                        }else{
                            $.ajax({
                                url: '/subject/admin/tagset.php',
                                type: 'POST',
                                data: {
                                    name:new_edit_txt,
                                    id:new_edit_id
                                },
                                success: function(tagset) {
                                    if(tagset == 200){
                                        msgalert('修改成功!',200,new_edit_txt);
                                    }else if(tagset == 500){
                                        msgalert('操作错误！',500)
                                    }else if(tagset == 800){
                                        msgalert('名称没有改动！',500)
                                    }else{
                                        msgalert('修改失败！',500)
                                    }
                                }
                            });
                        }                        
                    }else{
                        msgalert('参数错误！',500);
                    }                    
                }else{
                    msgalert('参数错误！',500);
                }
           }else{
                msgalert('标签名称不能为空！',500);
           }
        })

    }
    /* 添加 */
    const serviceflinput = document.getElementById('servicefl');//文本框
    const serviceflbut = document.getElementById('servicebtn');
    function add_tag(code,but,input){ 
        if(code == 200||code == 500){
            but.disabled = false;
            but.style.opacity = '1';
            but.innerHTML = '+添加标签';
            input.value = '';
        }else if(code == 600){
            but.disabled = false;
            but.style.opacity = '1';
            but.innerHTML = '+添加标签';
        }else{
            but.disabled = true;
            but.style.opacity = '0.5';
            but.innerHTML = '添加中……';
        }
    }
    if(serviceflinput&&serviceflbut){
        serviceflbut.addEventListener('click', function(e) {
            e.preventDefault();
            add_tag(0,serviceflbut);
            const new_txt_post = serviceflinput.value;
            if(new_txt_post){
                if(hasHtmlTag(new_txt_post)){
                    msg_alert('标签不能包含HTML标签！',600);
                    add_tag(600,serviceflbut);
                }else{
                    $.ajax({
                            url: '/subject/admin/newtag.php',
                            type: 'POST',
                            data: {
                                name:new_txt_post,
                            },
                            success: function(post) {
                                if(post == 200){
                                    window.location.reload();
                                }else if(post == 500){
                                    msg_alert('操作错误！',500);
                                    add_tag(500,serviceflbut,serviceflinput);
                                }else if(post == 800){
                                    msg_alert('标签已存在,请重新输入！',500);
                                    add_tag(500,serviceflbut,serviceflinput);
                                }else{
                                    msg_alert('添加失败！',500);
                                    add_tag(500,serviceflbut,serviceflinput);
                                }
                            },
                            error: function(xhr, status, error) {
                                msg_alert('服务器未响应！',500);
                                add_tag(500,serviceflbut,serviceflinput);
                            }
                        });
                }
            }else{
                msg_alert('标签名称不能为空！',600);
                add_tag(600,serviceflbut);
            }
        })
    }
    /* 删除 */
    const del_tag = document.querySelectorAll('.serfldel_sub');
    if (del_tag.length>0){
        del_tag.forEach(function(item){
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const del_tag_id = item.getAttribute('data-id');
                if (del_tag_id) {
                    if(!isNonNegativeInteger(del_tag_id)){
                         msg_alert('参数错误！',500);
                    }else{
                        if (confirm('删除不可撤回，确定要继续吗？')) {
                            $.ajax({
                                url: '/subject/admin/deltag.php',
                                type: 'POST',
                                data: {
                                    id:del_tag_id,
                                },
                                success: function(del) {
                                    if(del == 200){
                                        const del_tag_item = document.getElementById('subtag_del_'+del_tag_id);
                                        if(del_tag_item){
                                            del_tag_item.remove();
                                        }
                                        const del_tagx = document.querySelectorAll('.serfldel_sub');
                                        if (del_tagx.length<1){
                                            window.location.reload();
                                        }
                                    }else if(del == 500){
                                        msg_alert('操作错误！',500)
                                    }else{
                                        msg_alert('删除失败！',500)
                                    }
                                }
                            });
                        } 
                    }
                }
            })
        })
    }

})