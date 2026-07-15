document.addEventListener('DOMContentLoaded', function() {
    function isEmptyValue(value) {
        if (value === null || value === undefined) {
            return true;
        }
        if (typeof value !== 'string') {
            return false;
        }
        const trimmedValue = value.replace(/\s+/g, '');
        return trimmedValue === '';
    }
    function msgalert(text,code){
        if(code == 200){
            alert('<font>(ô‿ô)</font> '+text);
        }else{
            alert('<font>(｡ŏ_ŏ)</font> '+text);
        }
    }
    function isNonNegativeInteger(value) {
        if (value === null || value === undefined || typeof value === 'boolean' || typeof value === 'object') {
            return false;
        }
        if(value>999999999){
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
        return Number.isInteger(numValue) && numValue >= 0;
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
    const subset_yes = document.getElementById('subset_yes');//开关（单选）
    const subtitle = document.getElementById('subtitle');//标题
    const subname = document.getElementById('subname');//话题名称
    const submun = document.getElementById('submun');//限制发表篇数
    const repmun = document.getElementById('repmun');//限制回复层级
    const up_sub_set_button = document.getElementById('up_sub_set_button');//提交按钮
    if(subset_yes&&subtitle&&subname&&submun&&repmun&&up_sub_set_button){
        up_sub_set_button.onclick=function (){
           //获取值
           let subset_yes_valuex = 0;
           const subset_yes_value = subset_yes.checked;
           const subtitle_value = subtitle.value;
           const subname_value = subname.value;
           const submun_value = submun.value;
           const repmun_value = repmun.value;
           if(subset_yes_value){
             subset_yes_valuex = 1;
           }
           if(!isNonNegativeInteger(submun_value)||!isNonNegativeInteger(repmun_value)||!isNonNegativeInteger(subset_yes_valuex)||isEmptyValue(submun_value)||isEmptyValue(repmun_value)){
               msgalert('参数不正确！',500)
               return;
           }
           if(hasHtmlTag(subtitle_value)||hasHtmlTag(subname_value)){
               msgalert('不能包含HTML标签！',500)
               return;
           }
           $.ajax({
                url: '/subject/admin/set.php',
                type: 'POST',
                data: {
                    off:subset_yes_valuex,
                    title:subtitle_value,
                    name:subname_value,
                    mun:submun_value,
                    repmun:repmun_value
                },
                success: function(set) {
                    if(set == 200){
                        msgalert('设置成功！',200)
                    }else if(set == 500){
                        msgalert('操作错误！',500)
                    }else{
                        msgalert('设置失败！',500)
                    }
                }
            });

        }
    }
})