document.addEventListener('DOMContentLoaded', function() {
const passxformtel= document.getElementById("passxformtel");//获取表单
const nextbut=document.getElementById('nextbut');//获取下一步按钮
passxformtel.addEventListener('submit', function(event) { 
    event.preventDefault();  // 阻止表单默认提交行为
    nextbut.innerHTML="处理中";
    nextbut.setAttribute("disabled", "disabled");
    nextbut.style.opacity = "0.5";
    if (djstime<=0){
        window.location.reload();
    }else{

  // 获取单选项组    
  var radios = passxformtel.elements["passtel"]; 
  if (radios.length==undefined){
    selectedValue = radios.value;//单选项组只有一个选项时
  }else{
    var selectedValue = null;    
    // 遍历单选项组，找到选中的单选项    
    for (var i = 0; i < radios.length; i++) {    
        if (radios[i].checked) {    
            selectedValue = radios[i].value; // 选中的值
            break;    
        }    
    }
}
  
if (selectedValue == "email" || selectedValue == "tel"){
        
    $.ajax({
        url: '/inc/emailpost.php', // 请求地址
        type: 'POST',   // 请求类型
        data: {
        typepost: selectedValue,
        },
                    success: function(emalix) { // 成功回调函数
                    if(emalix == 500){
                      alert("<font>(｡ŏ_ŏ)</font> 不支持发送邮件！"); 
                      nextbut.removeAttribute("disabled");
                      nextbut.innerHTML="下一步";
                      nextbut.style.opacity = "1"; 
                    }else if(emalix == 600){
                        alert("<font>(｡ŏ_ŏ)</font> 验证码发送失败！"); 
                        nextbut.removeAttribute("disabled");
                        nextbut.innerHTML="下一步";
                        nextbut.style.opacity = "1"; 
                      }else if(emalix == 200){
                        window.location.reload();//刷新页面
                    }else if(emalix == 505){
                        window.location.reload();
                    }else{
                    alert("<font>(｡ŏ_ŏ)</font> 程序错误！"); 
                    console.log(emalix);
                }                      
              
                    }
      })
    
}else{
    alert("<font>(｡ŏ_ŏ)</font> 操作错误！"); 
    nextbut.innerHTML="禁止乱搞";
    nextbut.setAttribute("disabled", "disabled");
    nextbut.style.opacity = "0.5";
}
    
    }
 
})

});