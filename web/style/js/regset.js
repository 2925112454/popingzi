document.addEventListener('DOMContentLoaded', function() {//监听DOM加载完成
    const registerifbutton = document.getElementById('registerifbutton');//获取确认按钮
    function regifValue() {  
        const regifup= document.querySelectorAll('.upfilelabel input[type="radio"][name="registerif"]');
        for (var i = 0; i < regifup.length; i++) {  
          if (regifup[i].checked) {  
            return regifup[i].value;  
          }  
        }  
       return null;
      }
      function regoffValue() {  
        const regoffup= document.querySelectorAll('.upfilelabel input[type="radio"][name="registeroff"]');
        for (var i = 0; i < regoffup.length; i++) {  
          if (regoffup[i].checked) {  
            return regoffup[i].value;  
          }  
        }  
       return null;
      }
      function regisBetweenOneAndThreeupsetbutton(num) {
        const validNumbers = [1, 2];  
        return validNumbers.includes(num);  
    }
    registerifbutton.addEventListener('click', function() {
        const regifupValuex = Number(regifValue());
        const regoffupValuey = Number(regoffValue());
        const registertextarea = document.getElementById('registertextarea').value;
        if(regisBetweenOneAndThreeupsetbutton(regifupValuex)&&regisBetweenOneAndThreeupsetbutton(regoffupValuey)){

           //Ajax提交表单
           $.ajax({
            url: '/inc/regset.php', // 请求地址
            type: 'POST',   // 请求类型
            data: {
                text:registertextarea,
                if:regifupValuex,
                off:regoffupValuey,
            },
                        success: function(regx) { // 成功回调函数
                            if(regx == 500){
                                alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                                    
                            }else if(regx == 200){
                                alert("<font>(◕ܫ◕)</font> 修改成功！");
                            }else if(regx == 404){
                            alert("<font>(｡ŏ_ŏ)</font> 修改失败！");
                            }else{
                                alert("<font>(｡ŏ_ŏ)</font> 服务器出现错误！");
                            }
                        }
      
          });
            
        }else{
            alert('<font>(｡ŏ_ŏ)</font> 错误操作！');
        }


    });
});