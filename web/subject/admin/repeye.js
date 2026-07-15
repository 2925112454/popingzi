document.addEventListener('DOMContentLoaded', function() {
    function msg_alert(text,code){
        if(code == 200){
            alert('<font>(ô‿ô)</font> '+text);
        }else{
            alert('<font>(｡ŏ_ŏ)</font> '+text);
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
    function isint(value) {
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
    function newbut(text,code,but) {
        const nowbut = '确认修改';
        if(but){
            if(code==600){
                but.innerHTML = nowbut;
                but.style.opacity = '1';
                but.style.cursor = 'pointer';
                but.style.pointerEvents = 'auto';
            }else{
                but.innerHTML = text;
                but.style.opacity = '0.5';
                but.style.cursor = 'not-allowed';
                but.style.pointerEvents = 'none';
            }
        }
    }
/* 查看评论 */
const eyescomment = document.getElementById('eyescomment');//dialog
const eyescommentclose = document.getElementById('eyescommentclose');//close
const subeyescommenttext = document.getElementById('subeyescommenttext');//text
const eyes_rep = document.querySelectorAll('.eyes_rep');
if(eyes_rep&&eyes_rep.length>0&&eyescommentclose&&eyescomment&&subeyescommenttext){
    eyes_rep.forEach(function(eyes_rep) {
        eyes_rep.addEventListener('click', function() {
            const repcommenttext = eyes_rep.getAttribute('data-text');
            if(repcommenttext){
                eyescomment.showModal();
                subeyescommenttext.innerHTML = repcommenttext;
                eyescomment.style.display = 'flex';
            }
        })
    })
}
if(eyescommentclose&&eyescomment){
    eyescommentclose.addEventListener('click', function() {
        eyescomment.style.display = 'none';
        eyescomment.close();
        if(subeyescommenttext){
            subeyescommenttext.innerHTML = '';
        }
    })
}
/* 编辑事件 */
const rep_edit = document.querySelectorAll('.rep_edit');//编辑按钮
const rep_edit_dialog = document.getElementById('editrep');//dialog
const rep_edit_close = document.getElementById('editrepclose');//关闭
const rep_edit_text = document.getElementById('editreptext');//输入框
const rep_edit_but = document.getElementById('editrepsubmit');//提交按钮
const rep_edit_msg = document.getElementById('editrepmsg');//提示

if(rep_edit&&rep_edit.length>0&&rep_edit_dialog&&rep_edit_close&&rep_edit_text&&rep_edit_but&&rep_edit_msg){
    /* 输出标签列表 */
    function generateEmojiHtml() {
        let html = '';
        if(emojiMap&&emojiMap.length>0) {
            emojiMap.forEach(item => {
                html += `<span class="copyemoji">${item[0]}</span>`;
            });
        }else{
            const newemoji=[["😚","1F61A"],["😜","1F61C"],["😍","1F60D"],["😄","1F604"],["🥳","1F973"],["😱","1F631"],["🤬","1F92C"],["🤮","1F92E"],["👍","1F44D"],["👎","1F44E"]];//兜底表情
            newemoji.forEach(function([itemx,code]) {
                html += `<span class="copyemoji">${itemx}</span>`;
            });
        }        
        return html;
    }

    
    let timesetTimeout = null;
    function editrep_msg(text,code,demo){
        if(demo){
            if (timesetTimeout) {
                clearTimeout(timesetTimeout);
                timesetTimeout = null;
            }
            if(code==200){
                demo.innerHTML = '<i class="fa fa-check-circle" aria-hidden="true"></i>'+text;
                demo.style.display = 'flex';
                demo.style.color = 'green';
                timesetTimeout = setTimeout(function() {
                     window.location.reload();
                }, 2000);
            }else if(code==600){
                demo.innerHTML = '';
                demo.style.display = 'none';
                demo.style.color = '';
                timesetTimeout = null;
            }else{
                demo.innerHTML = '<i class="fa fa-info-circle" aria-hidden="true"></i>'+text;
                demo.style.display = 'flex';
                timesetTimeout = setTimeout(function() {
                    demo.style.display = 'none';
                    demo.innerHTML = '';
                    demo.style.color = '';
                }, 3000);
            }
        }
    }
    let nowtext = '';
    rep_edit.forEach(function(rep_edit) {
        rep_edit.addEventListener('click', function() {
            const rep_edit_id = rep_edit.getAttribute('data-id');//评论ID
            const rep_edit_v = rep_edit.getAttribute('data-text');//评论内容
            if(rep_edit_id&&rep_edit_v){
                rep_edit_dialog.showModal();
                rep_edit_dialog.style.display = 'flex';
                rep_edit_text.value = rep_edit_v;
                rep_edit_but.setAttribute('data-id',rep_edit_id);
                nowtext=rep_edit_v;
            }
        })
    })
    rep_edit_but.addEventListener('click', function() {
        const rep_edit_id = rep_edit_but.getAttribute('data-id');//评论ID
        const rep_edit_v = rep_edit_text.value;//评论内容
        if(rep_edit_id&&isint(rep_edit_id)){
            
            if(rep_edit_v){
                if(nowtext==rep_edit_v){
                    editrep_msg('评论没有改动！',500,rep_edit_msg);
                }else{
                    if(hasHtmlTag(rep_edit_v)){
                        editrep_msg('请勿使用HTML标签！',500,rep_edit_msg);
                    }else{
                        newbut('正在提交',200,rep_edit_but);//初始化提交按钮
                        editrep_msg('',600,rep_edit_msg);//初始化提示
                        $.ajax({
                            url: '/subject/admin/newrep.php',
                            type: 'POST',
                            data: {
                                id:rep_edit_id,
                                text:rep_edit_v
                            },
                            success: function(newrep) {
                                if(newrep == 200){
                                    editrep_msg('修改成功,正在刷新页面……',200,rep_edit_msg);
                                }else if(newrep == 500){
                                    editrep_msg('操作错误！',500,rep_edit_msg);
                                    newbut('刷新页面后再试~',200,rep_edit_but)
                                }else if(newrep == 800){
                                    editrep_msg('评论没有改动！',500,rep_edit_msg);
                                    newbut('确认修改',600,rep_edit_but)
                                }else if(newrep == 150){
                                    editrep_msg('不能超150字符！',500,rep_edit_msg);
                                    newbut('确认修改',600,rep_edit_but)
                                }else if(newrep == 320){
                                    editrep_msg('不能超320字符！',500,rep_edit_msg);
                                    newbut('确认修改',600,rep_edit_but)
                                }else{
                                    editrep_msg('修改失败！',500,rep_edit_msg);
                                    newbut('确认修改',600,rep_edit_but)
                                }
                            },
                            error: function(xhr, status, error) {
                                editrep_msg('服务器无响应！',500,rep_edit_msg);
                                newbut('确认修改',600,rep_edit_but)
                            }
                        });

                    }
                }                
            }else{
                editrep_msg('评论不能为空！',500,rep_edit_msg);
            }
        }else{
            editrep_msg('参数错误！',500,rep_edit_msg);
        }
    })
    const emojibox = document.getElementById('emojibox');
    let lastSelectionPos = 0;
    let lastSelectionEnd = 0;
    if (emojibox) {
        const emojiHtml = generateEmojiHtml();
        if (emojiHtml) {
            emojibox.innerHTML = emojiHtml;
            emojibox.style.display = 'flex';

            if (rep_edit_text) {
                lastSelectionPos = rep_edit_text.selectionStart ?? rep_edit_text.value.length;
                lastSelectionEnd = rep_edit_text.selectionEnd ?? rep_edit_text.value.length;
                rep_edit_text.addEventListener('input', function() {
                    lastSelectionPos = this.selectionStart ?? this.value.length;
                    lastSelectionEnd = this.selectionEnd ?? this.value.length;
                });
                rep_edit_text.addEventListener('selectionchange', function() {
                    lastSelectionPos = this.selectionStart ?? this.value.length;
                    lastSelectionEnd = this.selectionEnd ?? this.value.length;
                });
                rep_edit_text.addEventListener('blur', function() {
                    lastSelectionPos = this.selectionStart ?? this.value.length;
                    lastSelectionEnd = this.selectionEnd ?? this.value.length;
                });
                rep_edit_text.addEventListener('focus', function() {
                    lastSelectionPos = this.selectionStart ?? this.value.length;
                    lastSelectionEnd = this.selectionEnd ?? this.value.length;
                });
                rep_edit_text.addEventListener('keydown', function(e) {
                    if (e.key === 'Delete' || e.key === 'Backspace') {
                        queueMicrotask(() => {
                            lastSelectionPos = this.selectionStart ?? this.value.length;
                            lastSelectionEnd = this.selectionEnd ?? this.value.length;
                        });
                    }
                });
                emojibox.addEventListener('click', async function(e) {
                    const target = e.target;
                    if (!target.classList.contains('copyemoji')) return;

                    const emoji = target.textContent;
                    if (!emoji) return;

                    try {
                        await navigator.clipboard.writeText(emoji);
                        const selStart = rep_edit_text.selectionStart ?? lastSelectionPos;
                        const selEnd = rep_edit_text.selectionEnd ?? lastSelectionEnd;
                        const currentValue = rep_edit_text.value;
                        let newText;
                        let newCursorPos;
                        if (selStart !== selEnd) {
                            newText = currentValue.slice(0, selStart) + emoji + currentValue.slice(selEnd);
                            newCursorPos = selStart + emoji.length;
                        } else {
                            newText = currentValue.slice(0, selStart) + emoji + currentValue.slice(selStart);
                            newCursorPos = selStart + emoji.length;
                        }

                        rep_edit_text.value = newText;
                        lastSelectionPos = newCursorPos;
                        lastSelectionEnd = newCursorPos;
                        rep_edit_text.focus();
                        rep_edit_text.setSelectionRange(newCursorPos, newCursorPos);

                    } catch (err) {
                        editrep_msg('表情插入失败！', 500, rep_edit_msg);
                    }
                });
            }
        }
    }
}
if(rep_edit_close&&rep_edit_dialog){
    rep_edit_close.addEventListener('click', function() {
        rep_edit_dialog.style.display = 'none';
        rep_edit_dialog.close();
        if(rep_edit_text){
            rep_edit_text.value = '';
        }
        if(rep_edit_but){
            rep_edit_but.removeAttribute('data-id');
        }
        if(rep_edit_msg){
            rep_edit_msg.innerHTML = '';
        }
    })
}
/* 评论删除 */
const rep_del_but = document.querySelectorAll('.rep_del');
if(rep_del_but&&rep_del_but.length>0){
    rep_del_but.forEach(function(but){
        but.addEventListener('click', function() {
            const rep_del_id = but.getAttribute('data-d');
            if(rep_del_id&&isint(rep_del_id)){
                const confirmDelete = prompt('危险操作，请输入“确定删除”进行确认？');
                if (confirmDelete=="确定删除") {
                    $.ajax({
                            url: '/subject/admin/alldel_rep.php',
                            type: 'POST',
                            data: {
                                ids:rep_del_id,
                            },
                            success: function(del) {
                                if(del == 200){
                                    msg_alert('删除成功，正在刷新页面……',200);
                                    setTimeout(function(){
                                       window.location.reload();
                                    },1000);
                                }else{
                                    msg_alert('删除失败！',500);
                                }
                            },
                            error: function(xhr, status, error) {
                                msg_alert('服务器无响应！',500);
                            }
                        });
                }
            }else{
                msg_alert('参数错误！',500);
            }
        })
    })
}
/* 批量评论删除 */allcheckboxdel
const rep_del_all = document.getElementById('allcheckboxdel');
if(rep_del_all){
    rep_del_all.addEventListener('click', function() {
        // 获取所有已选复选框
        const rep_del_checkboxes = document.querySelectorAll('input[type="checkbox"][name="repid"]:checked');
        if (rep_del_checkboxes.length > 0) {
            let isValid = true;
            rep_del_checkboxes.forEach(function(checkbox) {
                if (!isint(checkbox.value)) {
                    isValid = false;
                }                
            });
            if (isValid) {
                const confirmDelete = prompt('危险操作，请输入“确定删除”进行确认？');
                if (confirmDelete=="确定删除") {
                    const rep_del_ids = Array.from(rep_del_checkboxes).map(checkbox => checkbox.value).join(',');
                    if(rep_del_ids){
                        $.ajax({
                            url: '/subject/admin/alldel_rep.php',
                            type: 'POST',
                            data: {
                                ids:rep_del_ids,
                            },
                            success: function(del) {
                                if(del == 200){
                                    msg_alert('删除成功，正在刷新页面……',200);
                                    setTimeout(function(){
                                       window.location.reload();
                                    },1000);
                                }else{
                                    msg_alert('删除失败！',500);
                                }
                            },
                            error: function(xhr, status, error) {
                                msg_alert('服务器无响应！',500);
                            }
                        });
                    }
                }
            }else{
                msg_alert('参数错误！',500);
            }
        }else{
            msg_alert('请选择要删除的项！',500);
        }
       
    })
}
/* 全选、反选 */
    const checkBoxes = document.querySelectorAll('input[type="checkbox"][name="repid"]');
    const allcheckbox = document.getElementById('allcheckbox');
    if(allcheckbox&&checkBoxes.length>0){
        allcheckbox.addEventListener('click', function() {
            const checkedCount = Array.from(checkBoxes).filter(checkbox => checkbox.checked).length;
            const isAllChecked = checkedCount === checkBoxes.length;
            const targetChecked = !isAllChecked;         
            checkBoxes.forEach(checkbox => {
                checkbox.checked = targetChecked;
            });
            this.checked = targetChecked;
        });
        checkBoxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checkedCount = Array.from(checkBoxes).filter(cb => cb.checked).length;
                allcheckbox.checked = checkedCount === checkBoxes.length;
            });
        });
    }
})