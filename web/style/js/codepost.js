const pascode= document.getElementById("pascode");//获取表单
const codebut=document.getElementById('codebut');//获取确定按钮
const codeinput=document.getElementById("codeinput");//获取input
pascode.addEventListener('submit', function(event) { 
    event.preventDefault();  // 阻止表单默认提交行为
    codebut.innerHTML="验证中";
    codebut.setAttribute("disabled", "disabled");
    codebut.style.opacity = "0.5";
if (djstime<=0){//判断操作是否超时
       window.location.reload();
   }else{
    function isAlphanumeric(str) {  
        const regex = /^[a-zA-Z0-9]+$/;  //验证码只能是字母和数字的正则表达式
        return regex.test(str);  
    }
    var code=codeinput.value;//获取验证码
    if (code==""){//验证码是否为空
        alert("<font>(｡ŏ_ŏ)</font> 验证码不能为空！");
        codebut.removeAttribute("disabled");
        codebut.innerHTML="确定";
        codebut.style.opacity = "1"; 
    }else{
        if (code.length<6 || code.length>6 || !isAlphanumeric(code)){
        alert("<font>(｡ŏ_ŏ)</font> 验证码错误！");
        codebut.removeAttribute("disabled");
        codebut.innerHTML="确定";
        codebut.style.opacity = "1";
        }else{
            $.ajax({
                url: '/inc/codepost.php', // 请求地址
                type: 'POST',   // 请求类型
                data: {
                codetxt: code,
                },
                            success: function(codex) { // 成功回调函数
                            if(codex == 500){
                              alert("<font>(｡ŏ_ŏ)</font> 验证码错误！"); 
                              codebut.removeAttribute("disabled");
                              codebut.innerHTML="确定";
                              codebut.style.opacity = "1";  
                            }else if(codex == 200){
                                window.location.reload();//验证通过，刷新页面
                            }else if(codex == 505){
                                window.location.reload();
                            }else{
                            alert("<font>(｡ŏ_ŏ)</font> 程序错误！"); 
                            console.log(codex);
                        }                      
                      
                            }
              });       
        }
    }
}
})