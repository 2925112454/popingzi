document.addEventListener('DOMContentLoaded', function() {
const fucksetbtn=document.getElementById('fucksetbtn');//确定按钮
fucksetbtn.addEventListener('click', function() {
const fucksettext=document.getElementById('fucksettext').value;//输入框

    if(fucksettext!=""&&fucksettext!=null&&fucksettext!=undefined){
        function isStringAnArrayLikeByPipeStrict(str) {  
            // 确保是字符串  
            if (typeof str !== 'string') return false;  
            // 如果字符串不包含'|'，则它必须是非空的单个元素  
            if (!str.includes('|')) return str.trim() !== '';  
            // 如果字符串包含'|'，则按'|'分割并检查每个部分是否非空  
            const parts = str.split('|');  
            return parts.every(part => part.trim() !== '');  
        }

        if(!isStringAnArrayLikeByPipeStrict(fucksettext)){
            alert("<font>(｡ŏ_ŏ)</font> 格式错误！")
            return;
        }

        const fucksettextarr = fucksettext.split('|');
        // 使用对象来跟踪元素出现的次数  
        const countMap = {};  
        for (var i = 0; i < fucksettextarr.length; i++) {  
            var item = fucksettextarr[i];  
            if (countMap[item]) {  
                countMap[item]++;  
            } else {  
                countMap[item] = 1;  
            }  
        }
        // 找出哪些元素是重复的  
        var duplicates = [];  
        for (var key in countMap) {  
            if (countMap[key] > 1) {  
                duplicates.push(key);  
            }  
        }

        if (duplicates.length > 0) {  
            alert("<font>(｡ŏ_ŏ)</font> 违禁词重复：" + duplicates.join(', '));
            return;
        }

    }

               //Ajax提交表单
               $.ajax({
                url: '/inc/fucket.php', // 请求地址
                type: 'POST',   // 请求类型
                data: {
                    fuck:fucksettext,
                },
                            success: function(fuck) { // 成功回调函数
                            if(fuck == 500){
                                alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                                    
                             }else if(fuck == 200){
                                alert("<font>(◕ܫ◕)</font> 修改成功！");
                             }else if(fuck == 400){
                                alert("<font>(｡ŏ_ŏ)</font> 格式错误或存在重复！");
                             }else if(fuck == 600){
                              alert("<font>(｡ŏ_ŏ)</font> 修改失败！");
                            }else{
                              alert("<font>(｡ŏ_ŏ)</font> 服务器出现错误！");
                             }
                            }
          
              });
});
});