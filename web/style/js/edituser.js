document.addEventListener('DOMContentLoaded', function() {

    function isPositiveIntegerLike(n) {  //自定义判断是否是正整数的函数
        var parsed = Number(n);  
        return !isNaN(parsed) && Number.isInteger(parsed) && parsed > 0;  
    }

    function isgold(n) {  //判断积分
        var parsed = Number(n);  
        return !isNaN(parsed) && Number.isInteger(parsed) && parsed >= 0;  
    }
    function isValidDateTime(dateTimeString) {  
        // 正则表达式1：匹配带时区信息的完整ISO 8601日期时间字符串（包括秒）  
        const iso8601WithTimezoneFull = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d+)?Z$/i;  
        // 正则表达式2：匹配带时区信息的ISO 8601日期时间字符串（不包括秒）  
        const iso8601WithTimezoneNoSeconds = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}(?:\.\d+)?Z$/i;  
        // 正则表达式3：匹配不带时区信息的完整ISO 8601日期时间字符串（包括秒）  
        const iso8601WithoutTimezoneFull = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d+)?$/i;  
        // 正则表达式4：匹配不带时区信息的ISO 8601日期时间字符串（不包括秒）  
        const iso8601WithoutTimezoneNoSeconds = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}(?:\.\d+)?$/i;  
        // 正则表达式5：匹配使用空格分隔的完整日期时间字符串（包括秒）  
        const spaceSeparatedFull = /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/;  
        // 正则表达式6：匹配使用空格分隔的日期时间字符串（不包括秒）  
        const spaceSeparatedNoSeconds = /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/;  
        // 尝试匹配所有可能的格式  
        if (iso8601WithTimezoneFull.test(dateTimeString) ||  
            iso8601WithTimezoneNoSeconds.test(dateTimeString) ||  
            iso8601WithoutTimezoneFull.test(dateTimeString) ||  
            iso8601WithoutTimezoneNoSeconds.test(dateTimeString) ||  
            spaceSeparatedFull.test(dateTimeString) ||  
            spaceSeparatedNoSeconds.test(dateTimeString)) {  
            return true;  
        }  
        // 如果都不匹配，则返回false  
        return false;  
    } 

    const deituserform=document.getElementById('userform');//表单
    const userButton = document.getElementById('newusersubmit');//获取提交按钮
    const sid= new URL(location.href).searchParams.get('sid');//获取sid
    if (deituserform&&userButton){
        userButton.addEventListener('click', function (e) {
            e.preventDefault();
            const haderimg = deituserform.userimg.value;//头像
            const newtime = deituserform.newtime.value;//注册时间
            const viptime = deituserform.viptime.value;//会员时间
            const name = deituserform.name.value;//昵称
            const user = deituserform.user.value;//账号
            const email = deituserform.email.value;//邮箱
            const tel = deituserform.tel.value;//手机
            const url = deituserform.url.value;//网址
            const gold = deituserform.gold.value;//积分
            const userif = deituserform.userif.value;//状态，1正常，2封禁
            const sexif = deituserform.sexif.value;//性别，1男，2女
            const vipif = deituserform.vipif.value;//身份，1普通，2管理员，3副站长
            const ip = deituserform.ip.value;//IP
            const ict = deituserform.ict.value;//简介
            const pcl = deituserform.pcl.value;//购买记录
            const cl = deituserform.cl.value;//收藏记录
            const telif = deituserform.telif.value;//手机验证状态
            const emilif = deituserform.emilif.value;//邮箱验证状态

            if (name&&user&&email){
                if(userif&&sexif&&vipif&&telif&&emilif&&gold&&newtime){

                    //判断头像格式
                    if (haderimg){
                        if (!haderimg.endsWith('.jpg') && !haderimg.endsWith('.jpeg') && !haderimg.endsWith('.png') && !haderimg.endsWith('.gif') && !haderimg.endsWith('.webp') && !haderimg.endsWith('.ico')&& !haderimg.endsWith('.svg')&& !haderimg.endsWith('.bmp')) {
                            alert("<font>(｡ŏ_ŏ)</font> 头像格式不正确！");
                            return;
                        }
                    }

                    //判断注册时间格式
                    if (newtime){
                           if (!isValidDateTime(newtime)) {
                            alert("<font>(｡ŏ_ŏ)</font> 注册时间格式不正确！");
                            return;
                        }
                    }

                    //判断会员时间格式
                    if (viptime){
                        if (!isValidDateTime(viptime)) {
                            alert("<font>(｡ŏ_ŏ)</font> 会员时间格式不正确！");
                            return;
                        }
                    }

                    //判断账号格式
                    if (user<=0 || !isPositiveIntegerLike(user)){
                        alert("<font>(｡ŏ_ŏ)</font> 账号必须是正整数！");
                        return;
                    }

                    if (user.length<6){
                        alert("<font>(｡ŏ_ŏ)</font> 账号不能小于6位！");
                        return;
                    }

                    if (user.length>11){
                        alert("<font>(｡ŏ_ŏ)</font> 账号不能大于11位！");
                        return;
                    }

                    if (user.startsWith('0')){
                        alert("<font>(｡ŏ_ŏ)</font> 账号请勿已0开头！");
                        return;
                    }

                    //判断邮箱格式
                    if (email){
                        var reg = /^([a-zA-Z0-9_\-.])+@([a-zA-Z0-9_\-])+(\.[a-zA-Z0-9_\-])+/;
                        if (!reg.test(email)) {
                            alert("<font>(｡ŏ_ŏ)</font> 邮箱格式不正确！");
                            return;
                        }
                    }

                    //判断手机格式
                    if (tel){
                        if (!isPositiveIntegerLike(tel) || tel.length > 11) {
                            alert("<font>(｡ŏ_ŏ)</font> 手机格式不正确！");
                            return;
                        }
                    }

                    //判断网址格式
                    if (url){
                        var regu = /^(https?|ftp|file):\/\/[-A-Za-z0-9+&@#\/%?=~_|!:,.;]+[-A-Za-z0-9+&@#\/%=~_|]/;
                        if (!regu.test(url)) {
                            alert("<font>(｡ŏ_ŏ)</font> 网址格式不正确！");
                            return;
                        }
                    }

                    //判断积分格式
                    if (gold){

                        if (!isgold(gold)) {
                            alert("<font>(｡ŏ_ŏ)</font> 积分格式不正确！");
                            return;
                        }

                        if (gold.length>9){
                            alert("<font>(｡ŏ_ŏ)</font> 积分过亿啦，不要太壕！");
                            return;
                        }

                        if (gold>0){
                            if (gold.startsWith('0')){
                                alert("<font>(｡ŏ_ŏ)</font> 数字前写再多0也没用！");
                                return;
                            }
                        }
                       
                    }
                    
                    //判断指定参数是否正确
                    if ((userif!=1 && userif!=2) || (sexif!=1 && sexif!=2) || (vipif!=1 && vipif!=2 && vipif!=3 && vipif!=4) || (telif!=1 && telif!=2) || (emilif!=1 && emilif!=2)){
                        alert("<font>(｡ŏ_ŏ)</font> 参数存在异常！");
                        return;
                    }

   
                    //判断简介格式
                    if (ict){
                        if (ict.length>240){
                            alert("<font>(｡ŏ_ŏ)</font> 简介最多240字,当前为"+ict.length+"字！");
                            return;
                        }
                    }

                    //判断购买记录格式
                    if (pcl){
                        //按|分割为数组
                        const pclarr = pcl.split('|');
                        //判断每个值都是都是在正整数
                        for (let i = 0; i < pclarr.length; i++) {
                            if (!isPositiveIntegerLike(pclarr[i])) {
                                alert("<font>(｡ŏ_ŏ)</font> 购买记录格式不正确！");
                                return;
                            }
                        }
                    }

                    //判断收藏记录格式
                    if (cl){
                        //按|分割为数组
                        const clarr = cl.split('|');
                        //判断每个值都是都是在正整数
                        for (let i = 0; i < clarr.length; i++) {
                            if (!isPositiveIntegerLike(clarr[i])) {
                                alert("<font>(｡ŏ_ŏ)</font> 收藏记录格式不正确！");
                               return;
                            }
                        }
                    }

                    //修改
                    $.ajax({
                        url: 'inc/edituser/', // 请求地址
                        type: 'POST',   // 请求类型
                        data: {
                            id:sid,// id
                            haderimg:haderimg,
                            newtime:newtime,
                            viptime:viptime,
                            name:name,
                            user:user,
                            email:email,
                            tel:tel,
                            url:url,
                            gold:gold,
                            userif:userif,
                            sexif:sexif,
                            vipif:vipif,
                            ip:ip,
                            ict:ict,
                            pcl:pcl,
                            cl:cl,
                            telif:telif,
                            emilif:emilif,
                        },
                                success: function(eidu) { // 成功回调函数
                                    if(eidu == 500){
                                        alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                             
                                    }else if(eidu == 200){
                                        alert("<font>(◕ܫ◕)</font> 修改成功，正在跳转！");
                                        setTimeout(function() {
                                           //跳转
                                           window.location.href = "popingzi.php?type=4";
                                        }, 1500);
                                    }else if(eidu == 600){
                                        alert("<font>(｡ŏ_ŏ)</font> 修改会员信息失败！");
                                    }else if(eidu == 404){
                                        alert("<font>(｡ŏ_ŏ)</font> 会员不存在！");
                                    }else if(eidu == 501){
                                        alert("<font>(｡ŏ_ŏ)</font> 账号重复！");
                                    }else if(eidu == 502){
                                        alert("<font>(｡ŏ_ŏ)</font> 邮箱重复！");
                                    }else if(eidu == 503){
                                        alert("<font>(｡ŏ_ŏ)</font> 手机重复！");
                                    }else{
                                        alert("<font>(｡ŏ_ŏ)</font> 服务器出现错误！");
                                        console.log(eidu);
                                    }
                                }
                
                    });
                    


                }else{
                    alert("<font>(｡ŏ_ŏ)</font> 参数存在异常！");
                }
            }else{
                alert("<font>(｡ŏ_ŏ)</font> 昵称、账号、邮箱不能为空！");
            }

        })
    }
/*预览头像*/
const jpgdialog=document.getElementById("xxxjpgdia");//获取弹窗
const jpgbtn=document.getElementById("xxxjpg");//获取按钮
if (jpgbtn&&jpgdialog) {
    jpgbtn.addEventListener('click', function (e) {
        e.preventDefault();
        const imgvue = document.getElementById("rowimg").value;//获取头像地址
        if (imgvue) {
            jpgdialog.style.backgroundImage = "url(" + imgvue + ")";
        } else {
            jpgdialog.style.backgroundImage = "url(/images/web/default.jpg)";
        }
        jpgdialog.showModal();
    })
    jpgdialog.addEventListener('click', function (e) {
        e.preventDefault();
        jpgdialog.close();
    })
}    
})