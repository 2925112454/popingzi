document.addEventListener('DOMContentLoaded', function() {

    function isAllPositiveIntegers(input) {
            let arr;
            if (typeof input === 'string') {
                arr = input.split(',').map(item => item.trim());
            } else if (Array.isArray(input)) {
                arr = input.map(item => String(item).trim());
            } else {
                return false;
            }
            for (const item of arr) {
                if (item === '') {
                    return false;
                }
                const isPositiveInt = /^[1-9]\d*$/.test(item);
                if (!isPositiveInt) {
                    return false;
                }
            }
            return arr.length > 0 && true;
    }
    function isValidFormat(str) {
        if (typeof str !== 'string') return false;
        const arr = str.split(',').map(item => item.trim());
        for (const item of arr) {
            if (item === '') return false;
            const isValidItem = /^([1-9]\d*|\{[1-9]\d*\})$/.test(item);
            if (!isValidItem) {
                return false;
            }
        }
        return arr.length > 0;
    }
    function msg(msgt, demo, type) {
        if (demo.timer) {
            clearTimeout(demo.timer);
        }
        demo.innerHTML = msgt;
        if (type === 200) {
            demo.style.color = "green";
            demo.style.border="1px solid green";
            demo.timer = setTimeout(function() {
                window.location.href = "/subject/?mine=1";    
            }, 3000);

        }
        demo.style.display = "block";
        demo.timer = setTimeout(function() {
            demo.style.display = "none";
            demo.style.color = "";
            demo.style.border= "";
            demo.innerHTML = "";
            demo.timer = null;
        }, 3000);
    }

    function isPositiveInteger(value) {
        if (value === null || value === undefined || isNaN(value) || !isFinite(value)) {
            return false;
        }
        const num = Number(value);
        return Number.isInteger(num) && num > 0;
    }

    function getContent() {
        return tinymce.get('sub_textarea').getContent();
    }
    const yestag = ["p","br","span","img","b","em","strong","a","blockquote","h2","h3","h4"];//允许的html标签

    function compressText(text) {
        if (!text || typeof text !== 'string') {
            return '';
        }
        const withoutLineBreaks = text.replace(/[\r\n]+/g, ' ');
        const withoutExtraSpaces = withoutLineBreaks.replace(/\s+/g, ' ');
        return withoutExtraSpaces.trim();
    }
    function checkAllowedHtmlTags(htmlText) {
        if (!htmlText || typeof htmlText !== 'string') {
            return true;
        }
        const tagRegex = /<\/?([a-zA-Z0-9]+)[^>]*>/g;
        let match;
        const foundTags = new Set();
        while ((match = tagRegex.exec(htmlText)) !== null) {
            const tagName = match[1].toLowerCase(); 
            foundTags.add(tagName);
        }
        for (const tag of foundTags) {
            if (!yestag.includes(tag)) {
                return false;
            }
        }
         return true;
    }

    function extractPlainTextFromHtml(htmlText) {
    if (!htmlText || typeof htmlText !== 'string') {
        return '';
    }

    const parser = new DOMParser();
    const doc = parser.parseFromString(htmlText, 'text/html');

    // 递归遍历节点并保留 img 标签
    function processNode(node) {
        let result = '';

        // 如果是文本节点，直接返回文本内容
        if (node.nodeType === Node.TEXT_NODE) {
            return node.textContent;
        }

        // 如果是元素节点
        if (node.nodeType === Node.ELEMENT_NODE) {
            // 保留 img 标签
            if (node.tagName.toLowerCase() === 'img') {
                const src = node.getAttribute('src') || '';
                const alt = node.getAttribute('alt') || '';
                return `<img src="${src}" alt="${alt}">`;
            }

            // 递归处理子节点
            for (let child of node.childNodes) {
                result += processNode(child);
            }
        }

        return result;
    }

    let plainText = processNode(doc.body);

    // 清理多余的空白字符
    plainText = plainText
        .replace(/[\r\n]+/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();

    return plainText;
}

    function isEmptyValue(value) {
        if (value === null || value === undefined) {
            return true;
        }
        if (typeof value !== 'string') {
            return false;
        }
        const whitespaceRegex = /^\s*$/;
        return whitespaceRegex.test(value);
    }

/* 表单提交 */
    
    const subpost = document.getElementById('subpost');//form表单
    const subbtn = document.getElementById('subbtn');//提交按钮
    const submsg = document.getElementById('submsg');//错误提示
        
    if(subpost&&subbtn&&submsg){
            //原始内容
            const sub_titlex = subpost.elements.sub_title.value.trim();
            const sub_tagx = subpost.elements.sub_tag.value.trim();
            const sub_contentx = subpost.elements.sub_textarea.value.trim();
            const sub_quote = subpost.elements.quote_ids.value.trim();
            const combinedStrx = `${sub_titlex}|${sub_tagx}|${sub_contentx}|${sub_quote}`;
            const md5Resultx = CryptoJS.MD5(combinedStrx).toString();

        subbtn.onclick=function (event){
            event.preventDefault();
            const sub_title = subpost.elements.sub_title.value.trim();//标题
            const sub_tag = subpost.elements.sub_tag.value.trim();//标签(下拉框)
            const sub_id = subpost.elements.sub_id.value.trim();//修改ID
            const sub_content = compressText(getContent().trim());//内容
            const quote_ids = subpost.elements.quote_ids.value.trim();//引用标签

            const combinedStr = `${sub_title}|${sub_tag}|${sub_content}|${quote_ids}`;
            const md5Result = CryptoJS.MD5(combinedStr).toString();

            if(md5Result==md5Resultx){
                msg('错误：请先修改信息！',submsg,500);
                return false;
            }

            if (sub_title == "") {
                msg('错误：标题不能为空！',submsg,500);
                return false;
            }

            if (sub_title.length > 180) {
                msg('错误：标题不能超过180个字符！',submsg,500);
                return false;
            }

            if (sub_tag == "" || sub_tag < 1 || !isPositiveInteger(sub_tag)||sub_id==""||sub_id < 1 || !isPositiveInteger(sub_id)) {
                msg('错误：参数不正确！',submsg,500);
                return false;
            }

            if (sub_content == "" || isEmptyValue(extractPlainTextFromHtml(sub_content))) {
                msg('错误：内容不能为空！',submsg,500);
                return false;
            }

            if (!checkAllowedHtmlTags(sub_content)){
                msg('错误：非法操作！',submsg,500);
                return false;
            }

            let quote_idsx = "";
            if(quote_ids){
                quote_idsx = quote_ids.replace(/，/g, ',');
                const quote_ids_arr = quote_idsx.split(',');
                quote_ids_arr.forEach((item, index) => {
                    if (item.trim() === "") {
                        quote_ids_arr.splice(index, 1);
                    }
                    for (let i = index + 1; i < quote_ids_arr.length; i++) {
                        if (item.trim() === quote_ids_arr[i].trim()) {
                            quote_ids_arr.splice(i, 1);
                            i--;
                        }
                    }
                });
                quote_idsx = quote_ids_arr.join(',');
                let quote_idsx_rep = quote_idsx.replace(/ /g, '');
                quote_idsx_rep  = quote_idsx_rep.replace(/{/g, '');
                quote_idsx_rep = quote_idsx_rep.replace(/}/g, '');

                if (!isAllPositiveIntegers(quote_idsx_rep)) {
                    msg('错误：引用的ID号只能是正整数！',submsg,500);
                    return false;
                }

                if (!isValidFormat(quote_idsx)) {
                    msg('错误：引用格式不正确！',submsg,500);
                    return false;
                }

                if (quote_idsx.split(',').length > 10) {
                    msg('错误：引用的ID不能超过10个！',submsg,500);
                    return false;
                }
            }
             subbtn.innerHTML = "提交中...";
             subbtn.style.opacity = "0.5";
             subbtn.disabled = true;

            $.ajax({
                url: 'newedit.php',
                type: 'POST',
                data: {
                    title:sub_title,
                    tag:sub_tag,
                    content:sub_content,
                    id:sub_id,
                    quote:quote_idsx,
                },
                            success: function(response) { // 成功回调函数
                                if(response == 500){
                                    msg('错误：非法操作！',submsg,500);
                                    subbtn.innerHTML = "提交";
                                    subbtn.disabled = false;
                                    subbtn.style.opacity = "1";
                                }else if(response == 200){
                                    msg('修改成功，正在跳转页面……',submsg,200);
                                }else if(response == 600){
                                    msg('错误：修改失败！',submsg,500);
                                    subbtn.innerHTML = "提交";
                                    subbtn.disabled = false;
                                    subbtn.style.opacity = "1";
                                }else if(response == 510){
                                    msg('错误：请先修改信息！',submsg,500);
                                    subbtn.innerHTML = "提交";
                                    subbtn.disabled = false;
                                    subbtn.style.opacity = "1";
                                }else if(response == 650){
                                    msg('错误：引用存在未知ID',submsg,500);
                                    subbtn.innerHTML = "提交";
                                    subbtn.disabled = false;
                                    subbtn.style.opacity = "1";
                                }else{
                                    msg('错误：服务器出错！',submsg,500);
                                    subbtn.innerHTML = "提交";
                                    subbtn.disabled = false;
                                    subbtn.style.opacity = "1";
                                }
                            }
          
              });

        }

    }
})