document.addEventListener('DOMContentLoaded', function() {
    const sub_comment_form = document.getElementById('sub_comment');//评论表单
    function message(text,code,dom) {
        if(code == 200){
            alert('<font>(ô‿ô)</font> '+text);
            if(dom){
                dom.value="";
            }
            setTimeout(function(){
                window.location.reload();
            }, 2000);
        }else if(code == 404){
            alert('<font>(ಠ.̫.̫ ಠ)</font> '+text);
            if(dom){
                dom.value="";
            }
        }else{
            alert('<font>(ಠ.̫.̫ ಠ)</font> '+text);
        }
    }

    function isEmpty(value) {
        if (value == null || value === '') {
            return true;
        }
        if (typeof value !== 'string') {
            return false;
        }
        let len = value.length;
        let start = 0;
        while (start < len && value.charCodeAt(start) <= 32) {
            start++;
        }
        let end = len;
        while (end > start && value.charCodeAt(end - 1) <= 32) {
            end--;
        }
        return start === end;
    }

    function isPositiveInteger(int) {
        if (int == null) {
            return false;
        }
        if (typeof int === 'number') {
            return Number.isInteger(int) && int > 0;
        }
        if (typeof int === 'string') {
            return /^[1-9]\d*$/.test(int);
        }
        return false;
    }

    function getEmojiByCode(codeStr) {
        try {
            const codes = codeStr.split(' ').map(code => parseInt(code, 16));
            return String.fromCodePoint(...codes);
        } catch (e) {
            return '😊'; // 异常时返回默认表情
        }
    }

    function insertEmojiSafely(input, emojiCode) {
        try {
            const emoji = getEmojiByCode(emojiCode);
            const start = input.selectionStart;
            const end = input.selectionEnd;
            const val = input.value;
            input.value = val.substring(0, start) + emoji + val.substring(end);
            const newPos = start + emoji.length;
            input.selectionStart = newPos;
            input.selectionEnd = newPos;
            input.focus();
            
            // 核心改动：插入表情后手动触发keyup事件，更新字数统计
            const keyupEvent = new Event('keyup', {
                bubbles: true,
                cancelable: true,
            });
            input.dispatchEvent(keyupEvent);
            
        } catch (e) {
            input.value += '😊';
            // 异常情况也触发keyup
            input.dispatchEvent(new Event('keyup', { bubbles: true, cancelable: true }));
        }
    }

    // 原有评论表情面板初始化
    function initEmojiPanel() {
        if (!sub_comment_form) return;
        const commentInput = sub_comment_form.elements.comment;
        const emojiBtn = document.createElement('button');
        emojiBtn.type = 'button';
        emojiBtn.innerText = '😊表情';
        emojiBtn.style.cssText = `
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            background: var(--button-color);
            cursor: pointer;
            font-size: 14px;
            font-family: "Microsoft Yahei", sans-serif;
            color: var(--button-font-color);
        `;

        // 2. 创建表情面板（强制Emoji渲染字体）
        const emojiPanel = document.createElement('div');
        emojiPanel.style.cssText = `
            position: absolute;
            min-width: 412px;
            max-width: 580px;
            top: 100%;
            right: 0px;
            margin-top: 5px;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            background: var(--heder-color);
            box-shadow: rgb(0 0 0 / 10%) 0px 2px 8px;
            z-index: 99;
            flex-wrap: wrap;
            gap: 4px;
            font-family: "Segoe UI Emoji", "Apple Color Emoji", "Microsoft Yahei", sans-serif;
            display: none;
        `;
        emojiPanel.className = 'emoji-panel';

        // 3. 生成表情项（基于编码映射）
        if(emojiMap&&emojiMap.length>0){
            emojiMap.forEach(([showChar, code]) => {
                const emojiItem = document.createElement('span');
                emojiItem.innerText = showChar;
                emojiItem.style.cssText = `
                    display: inline-block; padding: 4px; font-size: 20px;
                    cursor: pointer; border-radius: 4px; user-select: none;
                    border: 1px solid transparent;
                `;
                // 鼠标悬浮效果
                emojiItem.addEventListener('mouseover', () => {
                    emojiItem.style.background = 'var(--button-color)';
                    emojiItem.style.border = '1px solid var(--border-color)';
                });

                emojiItem.addEventListener('mouseout', () => {
                    emojiItem.style.background = 'transparent';
                    emojiItem.style.border = '1px solid transparent';
                });
                // 点击插入（传编码而非字符）
                emojiItem.onclick = () => insertEmojiSafely(commentInput, code);
                emojiPanel.appendChild(emojiItem);
            });
        }else{
            const newemoji=[["😚","1F61A"],["😜","1F61C"],["😍","1F60D"],["😄","1F604"],["🥳","1F973"],["😱","1F631"],["🤬","1F92C"],["🤮","1F92E"],["👍","1F44D"],["👎","1F44E"]];//兜底表情
            newemoji.forEach(([showChar, code]) => {
                const emojiItem = document.createElement('span');
                emojiItem.innerText = showChar;
                emojiItem.style.cssText = `
                    display: inline-block; padding: 4px; font-size: 20px;
                    cursor: pointer; border-radius: 4px; user-select: none;
                    border: 1px solid transparent;
                `;
                // 鼠标悬浮效果
                emojiItem.addEventListener('mouseover', () => {
                    emojiItem.style.background = 'var(--button-color)';
                    emojiItem.style.border = '1px solid var(--border-color)';
                });

                emojiItem.addEventListener('mouseout', () => {
                    emojiItem.style.background = 'transparent';
                    emojiItem.style.border = '1px solid transparent';
                });
                // 点击插入（传编码而非字符）
                emojiItem.onclick = () => insertEmojiSafely(commentInput, code);
                emojiPanel.appendChild(emojiItem);
            });
        }
        

        // 4. 容器布局
        const wrapper = document.createElement('div');
        wrapper.style.position = 'absolute';
        wrapper.style.bottom = '0px';
        wrapper.style.right = '90px';
        commentInput.parentNode.insertBefore(wrapper, commentInput);
        wrapper.appendChild(emojiBtn);
        wrapper.appendChild(emojiPanel);

        // 5. 面板显隐控制
        emojiBtn.onclick = (e) => {
            e.stopPropagation();
            emojiPanel.style.display = emojiPanel.style.display === 'flex' ? 'none' : 'flex';
        };
        document.onclick = (e) => {
            if (!wrapper.contains(e.target)) emojiPanel.style.display = 'none';
        };
    }

    // 回复表单专属表情面板初始化
    function initReplyEmojiPanel(replyInput) {
        if (!replyInput) return;
        
        const emojiBtn = document.createElement('button');
        emojiBtn.type = 'button';
        emojiBtn.innerText = '😊表情';
        // 回复按钮样式
        emojiBtn.style.cssText = `
            padding: 5.5px 10px;
            border: 1px solid var(--border-color);
            border-radius: 3px;
            background: var(--button-color);
            cursor: pointer;
            font-size: 12px;
            font-family: "Microsoft Yahei", sans-serif;
            color: var(--button-font-color);
        `;

        // 回复表情面板样式
        const emojiPanel = document.createElement('div');
        emojiPanel.style.cssText = `
            position: absolute;
            min-width: 286px;
            max-width: 400px;
            top: 100%;
            right: 0px;
            margin-top: 5px;
            padding: 8px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            background: var(--heder-color);
            box-shadow: rgb(0 0 0 / 8%) 0px 2px 6px;
            z-index: 999;
            flex-wrap: wrap;
            gap: 3px;
            font-family: "Segoe UI Emoji", "Apple Color Emoji", "Microsoft Yahei", sans-serif;
            display: none;
        `;
        emojiPanel.className = 'reply-emoji-panel';

        // 生成回复表情项
        if(emojiMap&&emojiMap.length>0){ 
            emojiMap.forEach(([showChar, code]) => {
                const emojiItem = document.createElement('span');
                emojiItem.innerText = showChar;
                emojiItem.style.cssText = `
                    display: inline-block; padding: 3px; font-size: 18px;
                    cursor: pointer; border-radius: 3px; user-select: none;
                    border: 1px solid transparent;
                `;
                emojiItem.addEventListener('mouseover', () => {
                    emojiItem.style.background = 'var(--button-color)';
                    emojiItem.style.border = '1px solid var(--border-color)';
                });
                emojiItem.addEventListener('mouseout', () => {
                    emojiItem.style.background = 'transparent';
                    emojiItem.style.border = '1px solid transparent';
                });
                emojiItem.onclick = () => insertEmojiSafely(replyInput, code);
                emojiPanel.appendChild(emojiItem);
            });
        }else{
            const newemoji=[["😚","1F61A"],["😜","1F61C"],["😍","1F60D"],["😄","1F604"],["🥳","1F973"],["😱","1F631"],["🤬","1F92C"],["🤮","1F92E"],["👍","1F44D"],["👎","1F44E"]];//兜底表情
            newemoji.forEach(([showChar, code]) => {
                const emojiItem = document.createElement('span');
                emojiItem.innerText = showChar;
                emojiItem.style.cssText = `
                    display: inline-block; padding: 3px; font-size: 18px;
                    cursor: pointer; border-radius: 3px; user-select: none;
                    border: 1px solid transparent;
                `;
                emojiItem.addEventListener('mouseover', () => {
                    emojiItem.style.background = 'var(--button-color)';
                    emojiItem.style.border = '1px solid var(--border-color)';
                });
                emojiItem.addEventListener('mouseout', () => {
                    emojiItem.style.background = 'transparent';
                    emojiItem.style.border = '1px solid transparent';
                });
                emojiItem.onclick = () => insertEmojiSafely(replyInput, code);
                emojiPanel.appendChild(emojiItem);
            });
        }

        // 回复表情容器布局
        const wrapper = document.createElement('div');
        wrapper.style.position = 'absolute';
        wrapper.style.display = 'inline-block';
        wrapper.style.bottom = '0px';
        wrapper.style.right = '70px';
        // 插入到回复输入框后方
        replyInput.parentNode.insertBefore(wrapper, replyInput.nextSibling);
        wrapper.appendChild(emojiBtn);
        wrapper.appendChild(emojiPanel);

        // 回复面板显隐控制
        emojiBtn.onclick = (e) => {
            e.stopPropagation();
            emojiPanel.style.display = emojiPanel.style.display === 'flex' ? 'none' : 'flex';
        };
        // 独立的点击关闭事件，避免和评论面板冲突
        const closePanel = (e) => {
            if (!wrapper.contains(e.target)) {
                emojiPanel.style.display = 'none';
            }
        };
        document.addEventListener('click', closePanel);
        // 防止内存泄漏
        replyInput.addEventListener('destroy', () => {
            document.removeEventListener('click', closePanel);
        });
    }

    // 初始化评论表情面板
    initEmojiPanel();
    
    const reply_forms = document.querySelectorAll('.reply-form');
    reply_forms.forEach(form => {
        const replyInput = form.elements.reply_text;
        if (replyInput) {
            initReplyEmojiPanel(replyInput);
        }
    });

    //评论提交逻辑    
    if(sub_comment_form){
        sub_comment_form.addEventListener('submit', function(e) {
            e.preventDefault();
            const comment_content = sub_comment_form.elements.comment.value.trim();//评论内容
            const comment_sub_id = sub_comment_form.elements.subid.value;//文章id
            if(comment_content.length < 1 || isEmpty(comment_content)){
                message('请输入评论内容！',404,sub_comment_form.elements.comment);
            }else{
                if(comment_content.length > 320){
                    message('评论内容过长！',500);
                }else{
                    if(comment_sub_id<0||isEmpty(comment_sub_id)||!isPositiveInteger(comment_sub_id)){
                        message('非法操作！',500);
                    }else{
                        $.ajax({
                            url: 'newsub.php',
                            type: 'POST',
                            data: {
                                subid:comment_sub_id,
                                content:comment_content
                            },
                                        success: function(newcomment) { // 成功回调函数
                                            if(newcomment == 200){
                                               message('评论成功，正在刷新页面！',200);                              
                                            }else if(newcomment == 500){
                                               message('非法操作！',500);
                                            }else if(newcomment == 800){
                                               message('请文明评论！',500);
                                            }else if(newcomment == 900){
                                               message('请1分钟后再试！',500);
                                            }else{
                                               message('评论失败！',500);
                                            }
                                        }
                    
                        });
                    }
                }
            }

        })
    }
    
    // 回复提交逻辑
    const reply_form = document.querySelectorAll('.reply-form');//所有的回复表单
    if(reply_form.length > 0){
        reply_form.forEach(function(form){
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const reply_content = form.elements.reply_text.value.trim();//回复内容
                const reply_id = form.elements.parent_id.value;//评论id
                const reply_sub_id = form.elements.subid.value;//文章id
                const parent_id = form.getAttribute('data-parent-id');//对应form的data-parent-id属性
                if(reply_content.length < 1 || isEmpty(reply_content)){
                    message('请输入回复内容！',404,form.elements.reply_text);
                }else{
                    if(reply_content.length > 150){
                        message('回复内容过长！',500);
                    }else{
                        if(reply_id<0||isEmpty(reply_id)||!isPositiveInteger(reply_id)||reply_sub_id<0||isEmpty(reply_sub_id)||!isPositiveInteger(reply_sub_id)||reply_id!=parent_id){
                            message('非法操作！',500);
                        }else{
                           $.ajax({
                            url: 'newsub.php',
                            type: 'POST',
                            data: {
                                subid:reply_sub_id,
                                content:reply_content,
                                type:reply_id,
                            },
                                        success: function(reply) { // 成功回调函数
                                            if(reply == 200){
                                               message('回复成功，正在刷新页面！',200);                              
                                            }else if(reply == 500){
                                               message('非法操作！',500);
                                            }else if(reply == 800){
                                               message('请文明回复！',500);
                                            }else if(reply == 900){
                                               message('请1分钟后再试！',500);
                                            }else{
                                               message('回复失败！',500);
                                            }
                                        }
                    
                        });
                        }
                    }
                }
            })
        })
    }

    //点赞提交逻辑
    const like_but = document.querySelectorAll('.like');//所有的点赞按钮
    if(like_but.length > 0){
        like_but.forEach(function(but){
            but.addEventListener('click', function(e) {
                e.preventDefault();
                const like_id = but.getAttribute('date-id');//评论id
                if(like_id<0||isEmpty(like_id)||!isPositiveInteger(like_id)){
                    message('非法操作！',500);
                }else{
                    let like_mun_text=0;
                    const like_mun = but.querySelector('.likemun');//获取点赞按钮的子元素的值 
                        $.ajax({
                            url: 'like.php',
                            type: 'POST',
                            data: {
                                id:like_id
                            },
                                        success: function(like) { // 成功回调函数
                                            if(like == 200){
                                                if(like_mun){
                                                    like_mun_text = like_mun.textContent.trim();
                                                    like_mun.textContent = Number(like_mun_text) + 1;
                                                }                                                                           
                                            }else if(like == 202){
                                               if(like_mun){
                                                    like_mun_text = like_mun.textContent.trim();
                                                    like_mun.textContent = Number(like_mun_text) - 1;
                                                }
                                            }else if(like == 500){
                                               message('非法操作！',202);
                                            }else{
                                               message('点赞失败！',500);
                                            }
                                        }
                    
                        });        
                }               
            })
        })
    }

$(function() {
    
    //显示/隐藏回复表单
    $('.comment-reply-btn, .reply-reply-btn').click(function() {
        const replyTo = $(this).attr('data-reply-to');
        const $replyForm = $('#reply-form-' + replyTo);
        if ($replyForm.is(':visible')) {
            $replyForm.hide();
        } else {
            $('.reply-form-wrap').hide();
            $replyForm.show();
            $replyForm.find('textarea').focus();
        }
    });

    //回复的字数统计
    $('.reply-textarea').keyup(function() {
        const maxLen = $(this).data('max-length') || 150;
        const currLen = $(this).val().length;
        const remainLen = maxLen - currLen;
        $(this).siblings('div').find('.reply-msg').text('剩余字数：' + remainLen);
        if (remainLen < 0) {
            $(this).val($(this).val().substring(0, maxLen));
            $(this).siblings('div').find('.reply-msg').text('剩余字数：0');
        }
    });
    
    //父评论的字数统计
    $('.sub-comment-textarea').keyup(function() {
        const maxLen = $(this).data('max-length') || 320;
        const currLen = $(this).val().length;
        const remainLen = maxLen - currLen;
        $(this).siblings('div').find('#sub_comment_msg').text('剩余字数：' + remainLen);
        if (remainLen < 0) {
            $(this).val($(this).val().substring(0, maxLen));
            $(this).siblings('div').find('#sub_comment_msg').text('剩余字数：0');
        }
    });
});

});