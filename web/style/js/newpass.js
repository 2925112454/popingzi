const newpass=document.getElementById("newpass");//获取表单
const newpassinput=document.getElementById("newpassinput");//获取密码1输入框
const newpassinput2=document.getElementById("newpassinput2");//获取确认密码输入框
const newpassbut=document.getElementById('newpassbut');//获取确定按钮
newpass.addEventListener('submit', function(event) { 
    event.preventDefault();  // 阻止表单默认提交行为
    newpassbut.innerHTML="正在提交新密码";
    newpassbut.setAttribute("disabled", "disabled");
    newpassbut.style.opacity = "0.5";
    const newpassinputvalue=document.getElementById('newpassinput').value;
    const newpassinput2value=document.getElementById('newpassinput2').value;
    if (djstime<=0){//判断时间是否过期
        window.location.reload();
    }else{
    
    //判断两个密码是否相等
    if(newpassinputvalue!=newpassinput2value){
        alert("<font>(｡ŏ_ŏ)</font> 两次密码不一致！");
        newpassbut.innerHTML="确定";
        newpassbut.removeAttribute("disabled");
        newpassbut.style.opacity = "1";
    }else{
        //判断密码是否小于6位
        if(newpassinputvalue.length<6 || newpassinput2value.length<6 || newpassinputvalue==null || newpassinput2value==null || newpassinputvalue=="" || newpassinput2value==""){
            alert("<font>(｡ŏ_ŏ)</font> 密码不能小于6位！");
            newpassbut.innerHTML="确定";
            newpassbut.removeAttribute("disabled");
            newpassbut.style.opacity = "1";
        }else{
            //判断密码是纯数字
            if(newpassinputvalue.match(/^\d+$/) || newpassinput2value.match(/^\d+$/)){
                alert("<font>(｡ŏ_ŏ)</font> 密码不能纯数字！");
                newpassbut.innerHTML="确定";
                newpassbut.removeAttribute("disabled");
                newpassbut.style.opacity = "1";
            }else{
                $.ajax({
                    url: '/inc/newpass.php', // 请求地址
                    type: 'POST',   // 请求类型
                    data: {
                    pass: newpassinputvalue,
                    pass2:newpassinput2value,
                    },
                                success: function(newpassx) { // 成功回调函数
                                if(newpassx == 500){
                                  alert("<font>(｡ŏ_ŏ)</font> 密码修改失败！"); 
                                  newpassbut.removeAttribute("disabled");
                                  newpassbut.innerHTML="确定";
                                  newpassbut.style.opacity = "1";  
                                }else if(newpassx == 200){
                                    alert("<font>(◕ܫ◕)</font> 密码修改成功！"); 
                                    setTimeout(function() {  
                                        //跳转至首页
                                        window.location.href = "/";
                                    }, 1000);
                                }else if(newpassx == 300){
                                    alert("<font>(｡ŏ_ŏ)</font> 两次密码不一致！"); 
                                    newpassbut.removeAttribute("disabled");
                                    newpassbut.innerHTML="确定";
                                    newpassbut.style.opacity = "1"; 
                                }else if(newpassx == 301){
                                    alert("<font>(｡ŏ_ŏ)</font> 密码不能小于6位！"); 
                                    newpassbut.removeAttribute("disabled");
                                    newpassbut.innerHTML="确定";
                                    newpassbut.style.opacity = "1"; 
                                }else if(newpassx == 302){
                                    alert("<font>(｡ŏ_ŏ)</font> 密码不能纯数字！"); 
                                    newpassbut.removeAttribute("disabled");
                                    newpassbut.innerHTML="确定";
                                    newpassbut.style.opacity = "1"; 
                                }else if(newpassx == 505){
                                    window.location.reload();
                                }else{
                                alert("<font>(｡ŏ_ŏ)</font> 程序错误！"); 
                                console.log(newpassx);
                            }                      
                          
                                }
                  });   
            }
        }
    }
}

});