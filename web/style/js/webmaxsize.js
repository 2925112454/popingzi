document.addEventListener('DOMContentLoaded', function() {
    var webmaxsizedialog=document.getElementById('webmaxsizedialog');//获取弹出层
    var webmaxsize=document.getElementById('webmaxsize');//获取点击按钮
    if(webmaxsizedialog&&webmaxsize){

        const bodyhidden=document.getElementsByTagName('body')[0];
        const webmaxsizeclose=document.getElementById('webmaxsizeclose');//获取关闭按钮
        const webmaxsizebut=document.getElementById('webmaxsizebut');//获取确定按钮
        const webmaxsizeinput=document.getElementById('webmaxsizeinput');//获取输入框
        const webmaxsizeerr=document.getElementById('webmaxsizeerr');//获取错误信息框

       //点击打开弹出层
       webmaxsize.onclick=function(){
        webmaxsizedialog.showModal();
        bodyhidden.style.cssText="overflow:hidden;";
        webmaxsizedialog.style.cssText="display:flex;";
        //获取点击按钮的data-max值
        const maxsizemun=this.getAttribute('data-max');
        if(maxsizemun==''||maxsizemun==null||maxsizemun==' '||maxsizemun==0||maxsizemun<0||!maxsizemun.match(/^[0-9]+$/)){
            webmaxsizeinput.value=0;
        }else{
            webmaxsizeinput.value=maxsizemun;
        }
       }
       //点击关闭弹出层
       webmaxsizeclose.onclick=function(){
        webmaxsizedialog.close();
        bodyhidden.style.cssText="overflow:auto;";
        webmaxsizedialog.style.cssText="display:none;";
        webmaxsizeinput.value='';
       }
       //点击确定按钮
       webmaxsizebut.onclick=function(){
        if(webmaxsizeinput.value==''||webmaxsizeinput.value==null||webmaxsizeinput.value==' '||webmaxsizeinput.value==0||webmaxsizeinput.value<0||!webmaxsizeinput.value.match(/^[0-9]+$/)){
            webmaxsizeerr.innerHTML="请输入有效的正整数";
            webmaxsizeerr.style.cssText="display:block;";
            webmaxsizeerr.classList.add('maxerr');
            setTimeout(function(){
                webmaxsizeerr.innerHTML="";
                webmaxsizeerr.style.cssText="display:none;";
                webmaxsizeerr.classList.remove('maxerr');
            },2000);
        }else{
            //去除00123这种开头的0
            const webmaxsizeinputv=webmaxsizeinput.value.replace(/^0+/,'');
            if(webmaxsizeinputv.length>9){
                webmaxsizeerr.innerHTML="你的储存空间大得惊人";
                webmaxsizeerr.style.cssText="display:block;";
                webmaxsizeerr.classList.add('maxerr');
                setTimeout(function(){
                    webmaxsizeerr.innerHTML="";
                    webmaxsizeerr.style.cssText="display:none;";
                    webmaxsizeerr.classList.remove('maxerr');
                },2000);
            }else{
                $.ajax({
                    url: '/inc/webmaxsize.php', // 请求地址
                    type: 'POST',   // 请求类型
                    data: {
                        size:webmaxsizeinputv,//数值
                    },
                                success: function(max) { // 成功回调函数
                                    if(max == 500){
                                        webmaxsizeerr.innerHTML="错误操作";
                                        webmaxsizeerr.style.cssText="display:block;";
                                        webmaxsizeerr.classList.add('maxerr');
                                        setTimeout(function(){
                                            webmaxsizeerr.innerHTML="";
                                            webmaxsizeerr.style.cssText="display:none;";
                                            webmaxsizeerr.classList.remove('maxerr');
                                        },2000);                                                                   
                                    }else if(max == 200){
                                        webmaxsizeerr.innerHTML="保存成功，正在刷新页面……";
                                        webmaxsizeerr.style.cssText="display:block;";
                                        webmaxsizeerr.classList.add('maxyes');
                                        setTimeout(function(){
                                            location.reload();
                                        },2000);
                                    }else if(max == 600){
                                        webmaxsizeerr.innerHTML="保存失败";
                                        webmaxsizeerr.style.cssText="display:block;";
                                        webmaxsizeerr.classList.add('maxerr');
                                        setTimeout(function(){
                                            webmaxsizeerr.innerHTML="";
                                            webmaxsizeerr.style.cssText="display:none;";
                                            webmaxsizeerr.classList.remove('maxerr');
                                        },2000);
                                    }else if(max == 700){
                                        webmaxsizeerr.innerHTML="您无权限修改该数据";
                                        webmaxsizeerr.style.cssText="display:block;";
                                        webmaxsizeerr.classList.add('maxerr');
                                        setTimeout(function(){
                                            webmaxsizeerr.innerHTML="";
                                            webmaxsizeerr.style.cssText="display:none;";
                                            webmaxsizeerr.classList.remove('maxerr');
                                        },2000);
                                    }else if(max == 404){
                                        webmaxsizeerr.innerHTML="请输入有效的正整数";
                                        webmaxsizeerr.style.cssText="display:block;";
                                        webmaxsizeerr.classList.add('maxerr');
                                        setTimeout(function(){
                                            webmaxsizeerr.innerHTML="";
                                            webmaxsizeerr.style.cssText="display:none;";
                                            webmaxsizeerr.classList.remove('maxerr');
                                        },2000);
                                    }else if(max == 400){
                                        webmaxsizeerr.innerHTML="你的储存空间大得惊人";
                                        webmaxsizeerr.style.cssText="display:block;";
                                        webmaxsizeerr.classList.add('maxerr');
                                        setTimeout(function(){
                                            webmaxsizeerr.innerHTML="";
                                            webmaxsizeerr.style.cssText="display:none;";
                                            webmaxsizeerr.classList.remove('maxerr');
                                        },2000);
                                    }else{
                                        webmaxsizeerr.innerHTML="服务器错误";
                                        webmaxsizeerr.style.cssText="display:block;";
                                        webmaxsizeerr.classList.add('maxerr');
                                        setTimeout(function(){
                                            webmaxsizeerr.innerHTML="";
                                            webmaxsizeerr.style.cssText="display:none;";
                                            webmaxsizeerr.classList.remove('maxerr');
                                        },2000);
                                        console.log(max);
                                    }
                                }
            
                });
            }
        }
       }


    }
})