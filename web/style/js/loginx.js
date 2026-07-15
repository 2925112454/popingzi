const loginxbut=document.getElementById('logxbut');//获取提交按钮
const loginxform= document.getElementById("passxform");//获取表单
const loginxinp = document.getElementById("loginxinp");//获取输入框
loginxform.addEventListener('submit', function(event) { 
    event.preventDefault();  // 阻止表单默认提交行为
    const loginxtext = loginxform.querySelector("#loginxinp").value;//获取账号
    loginxinp.setAttribute("disabled", "disabled");//改变输入框的输入状态为禁止输入
    loginxbut.innerHTML = "处理中";//改变提交按钮内容
    loginxbut.setAttribute("disabled", "disabled");//禁止提交按钮button被点击
    loginxinp.style.opacity = "0.5"; //改变输入框透明度
    loginxbut.style.opacity = "0.5";//改变提交按钮透明度
    if (loginxtext) {
        //判断账号是否是数字且不小于等于0
        if (loginxtext>0 && loginxtext.length >= 6 && loginxtext.length <= 11 && isNumber(loginxtext)){
            $.ajax({
                url: '/inc/logxpost.php', // 请求地址
                type: 'POST',   // 请求类型
                data: {
                ue: loginxtext,
                },
                            success: function(loginx) { // 成功回调函数
                            if(loginx == 500){
                              alert("<font>(｡ŏ_ŏ)</font> 操作错误！"); 
                              loginxinp.removeAttribute("disabled");  
                              loginxinp.value="";
                              loginxinp.style.opacity = "1";  
                              loginxbut.removeAttribute("disabled");
                              loginxbut.innerHTML="下一步";
                              loginxbut.style.opacity = "1";  
                            }else if(loginx == 404){
                              alert("<font>(｡ŏ_ŏ)</font> 账号不存在！");  
                              loginxinp.removeAttribute("disabled");  
                              loginxinp.style.opacity = "1";  
                              loginxbut.removeAttribute("disabled");
                              loginxbut.innerHTML="下一步";
                              loginxbut.style.opacity = "1";                                                             
                           }else if(loginx == 401){
                            alert("<font>(｡ŏ_ŏ)</font> 该账号已被封禁！"); 
                            loginxinp.removeAttribute("disabled");  
                            loginxinp.style.opacity = "1";  
                            loginxbut.removeAttribute("disabled");
                            loginxbut.innerHTML="下一步";
                            loginxbut.style.opacity = "1";                                                              
                         }else if(loginx == 403){
                            alert("<font>(｡ŏ_ŏ)</font> 账号格式不正确！");  
                            loginxinp.removeAttribute("disabled");  
                            loginxinp.style.opacity = "1";  
                            loginxbut.removeAttribute("disabled");
                            loginxbut.innerHTML="下一步";
                            loginxbut.style.opacity = "1";                                                             
                         }else if(loginx == 402){
                            alert("<font>(｡ŏ_ŏ)</font> 该账号不允许线上找回密码！");  
                              loginxinp.removeAttribute("disabled");  
                              loginxinp.style.opacity = "1";  
                              loginxbut.removeAttribute("disabled");
                              loginxbut.innerHTML="下一步";
                              loginxbut.style.opacity = "1";                                                           
                         }else if(loginx == 505){
                               alert("<font>(｡ŏ_ŏ)</font> 该账号邮箱和手机均没验证！"); 
                               loginxinp.removeAttribute("disabled");  
                               loginxinp.style.opacity = "1";  
                               loginxbut.removeAttribute("disabled");
                               loginxbut.innerHTML="下一步";
                               loginxbut.style.opacity = "1";                                                                 
                           }else if(loginx == 400){
                            alert("<font>(｡ŏ_ŏ)</font> 账号不能为空！");
                            loginxinp.removeAttribute("disabled");  
                            loginxinp.value="";
                            loginxinp.style.opacity = "1";  
                            loginxbut.removeAttribute("disabled");
                            loginxbut.innerHTML="下一步";
                            loginxbut.style.opacity = "1";                                                                  
                        }else if(loginx == 200){
                            window.location.reload();//刷新页面
                        }else{
                            alert("<font>(｡ŏ_ŏ)</font> 程序错误！"); 
                            console.log(loginx);
                        }                      
                      
                            }
              })
        }else{
            alert("<font>(｡ŏ_ŏ)</font> 账号格式不正确！");
                            loginxinp.removeAttribute("disabled");  
                            loginxinp.style.opacity = "1";  
                            loginxbut.removeAttribute("disabled");
                            loginxbut.innerHTML="下一步";
                            loginxbut.style.opacity = "1"; 
        }
        
    }else{
        alert("<font>(｡ŏ_ŏ)</font> 账号不能为空！");
                            loginxinp.removeAttribute("disabled");  
                            loginxinp.value="";
                            loginxinp.style.opacity = "1";  
                            loginxbut.removeAttribute("disabled");
                            loginxbut.innerHTML="下一步";
                            loginxbut.style.opacity = "1"; 
    }

});