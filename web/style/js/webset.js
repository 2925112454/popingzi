document.addEventListener('DOMContentLoaded', function() {//监听DOM加载完成

    const webform=document.getElementById('webform');//获取FORM表单
    const webbtn=document.getElementById('webbtn');//获取提交按钮
    webbtn.addEventListener('click',function(e){
        e.preventDefault();//阻止默认事件
        const toplogo=webform.elements.toplogo.value;//导航栏LOGO
        const toplogourl=webform.elements.toplogourl.value;//导航栏LOGO
        const butlogo=webform.elements.butlogo.value;//底部LOGO
        const newintel=webform.elements.newintel.value;//新媒体账号
        const webtext=webform.elements.webtext.value;//网站名称
        const webtxt=webform.elements.webtxt.value;//网站副标题
        const webpas=webform.elements.webpas.value;//网站关键词
        const webvar=webform.elements.webvar.value;//网站描述
        const webbut=webform.elements.webbut.value;//网站版权信息
        const qq=webform.elements.qq.value;//QQ
        const zq=webform.elements.zq.value;//QQ群
        const wb=webform.elements.wb.value;//微博
        const email=webform.elements.email.value;//邮箱
        const webjifen=webform.elements.jifen.value;//签到奖励积分

    if (webtext==""||webvar==""||webpas==""||webbut==""||webtxt==""){
        alert("<font>(｡ŏ_ŏ)</font> 必填项不能为空！");
    }else{
        if (toplogo==""||butlogo==""){
            alert("<font>(｡ŏ_ŏ)</font> 请上传LOGO！");
        }else{
           //若QQ、QQ群、微博、邮箱为空，则不显示，不为空则判断是否是url地址
           if (qq==""&&zq==""&&wb==""&&email==""){}else{
                if(qq!=""){
                    if(qq.indexOf("http://")==-1&&qq.indexOf("https://")==-1){
                        alert("<font>(｡ŏ_ŏ)</font> 请输入正确的QQ地址！");
                        return false;
                    }
                };

                if(zq!=""){
                    if(zq.indexOf("http://")==-1&&zq.indexOf("https://")==-1){
                        alert("<font>(｡ŏ_ŏ)</font> 请输入正确的QQ群地址！");
                        return false;
                    }
                };

                if(wb!=""){
                    if(wb.indexOf("http://")==-1&&wb.indexOf("https://")==-1){
                        alert("<font>(｡ŏ_ŏ)</font> 请输入正确的微博地址！");
                        return false;
                    }
                };

                if(email!=""){
                 //邮箱验证
                 var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
                 if(!reg.test(email)){
                    alert("<font>(｡ŏ_ŏ)</font> 请输入正确的邮箱地址！");
                    return false;
                 }
                };
           };
           if(webjifen){
                const isValid = /^\d+(-\d+)?$/.test(webjifen);
                if (!isValid) {
                    alert("<font>(｡ŏ_ŏ)</font> 签到奖励格式不正确！");
                    return false;
                }
           }
           


           //Ajax提交表单
           $.ajax({
            url: '/inc/webset.php', // 请求地址
            type: 'POST',   // 请求类型
            data: {
                toplogo:toplogo,
                toplogourl:toplogourl,
                butlogo:butlogo,
                newintel:newintel,
                webtext:webtext,
                webtxt:webtxt,
                webpas:webpas,
                webvar:webvar,
                webbut:webbut,
                qq:qq,
                zq:zq,
                wb:wb,
                email:email,
                jifen:webjifen,
            },
                        success: function(set) { // 成功回调函数
                        if(set == 500){
                            alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                                    
                         }else if(set == 200){
                            alert("<font>(◕ܫ◕)</font> 修改成功！");
                         }else if(set == 404){
                            alert("<font>(｡ŏ_ŏ)</font> 必填项不能为空！");
                         }else if(set == 403){
                            alert("<font>(｡ŏ_ŏ)</font> 请上传LOGO！");
                         }else if(set == 402){
                            alert("<font>(｡ŏ_ŏ)</font> QQ或QQ群或微博地址错误！");
                         }else if(set == 405){
                            alert("<font>(｡ŏ_ŏ)</font> 签到奖励格式不正确！");
                         }else if(set == 401){
                            alert("<font>(｡ŏ_ŏ)</font> 请输入正确的邮箱地址！");
                         }else{
                            alert("<font>(｡ŏ_ŏ)</font> 服务器出现错误！");
                         }
                        }
      
          })


        }
    }

    });
});