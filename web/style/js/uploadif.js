document.addEventListener('DOMContentLoaded', function() {
    function notifupValue() {  
        const notifup= document.querySelectorAll('.upfileradio input[type="radio"][name="notifup"]');
        // 遍历这些radio元素，找到被选中的那个  
        for (var i = 0; i < notifup.length; i++) {  
          if (notifup[i].checked) {  
            return notifup[i].value;  
          }  
        }  
       return null;
    }
    function notifupValueimg() {  
      const notifup_img= document.querySelectorAll('.upfileradio input[type="radio"][name="notifup_img"]');
      // 遍历这些radio元素，找到被选中的那个  
      for (var i = 0; i < notifup_img.length; i++) {  
        if (notifup_img[i].checked) {  
          return notifup_img[i].value;  
        }  
      }  
     return null;
  }
      function isBetweenOneAndThreeupsetbutton(num) {  
        // 创建一个包含1、0的数组  
        const validNumbers = [1, 0];  
        // 使用includes方法检查num是否在数组中  
        return validNumbers.includes(num);  
    }

      const upsetbutton = document.getElementById('upsetbutton');//确认按钮
      const upyesfiletype = document.getElementById('upyesfiletype');//textarea多行文本框
      const upyesfilesize = document.getElementById('upyesfilesize');//input输入框
      const upyesfcsize = document.getElementById('upyesfcsize');
      const upyesvipsize = document.getElementById('upyesvipsize');

      upsetbutton.addEventListener('click', function() {
        const notifupValuex = Number(notifupValue());
        const notifupValuex_img = Number(notifupValueimg());
        if(isBetweenOneAndThreeupsetbutton(notifupValuex)&&isBetweenOneAndThreeupsetbutton(notifupValuex_img)){
           const upyesfiletypevalue = upyesfiletype.value;//获取textarea的值
           const upyesfilesizevalue = upyesfilesize.value;//获取input的值
           const upyesfcsizenum=upyesfcsize.value;
           const upyesvipsizevalue=upyesvipsize.value;
           const upsizenumberbint=Number(upyesfilesizevalue);

           if (upyesfilesizevalue){
             const upsizenumber=Number(upyesfilesizevalue);
             //判断是不是数字
             if(isNaN(upsizenumber)){
                alert('<font>(｡ŏ_ŏ)</font> 附件大小必须为数字！');
                return;
             }
             if(upyesfilesizevalue!==0 && upyesfilesizevalue!=="0"){
               if(upyesfilesizevalue.toString().startsWith('0')){
                alert('<font>(｡ŏ_ŏ)</font> 附件大小格式不正确！');
                return;
               }
             }
             
           }

           if(upyesfcsizenum){
            const upyesfcsizenumnumber=Number(upyesfcsizenum);
              if(isNaN(upyesfcsizenumnumber)){
                    alert('<font>(｡ŏ_ŏ)</font> 分成必须是数字！');
                    return;
              }

              if(upyesfcsizenumnumber!==0 && upyesfcsizenumnumber!=="0"){
               if(upyesfcsizenumnumber.toString().startsWith('0')){
                alert('<font>(｡ŏ_ŏ)</font> 分成格式不正确！');
                return;
               }
             }

             if(upyesfcsizenumnumber>100||upyesfcsizenumnumber<0){
                alert('<font>(｡ŏ_ŏ)</font> 分成必须是0-100之间！');
                return;
             }

           }

           if(upyesvipsizevalue){
            const upyesvipsizevalueber=Number(upyesvipsizevalue);
              if(isNaN(upyesvipsizevalueber)){
                    alert('<font>(｡ŏ_ŏ)</font> 折扣必须是数字！');
                    return;
              }

              if(upyesvipsizevalueber!==0 && upyesvipsizevalueber!=="0"){
               if(upyesvipsizevalueber.toString().startsWith('0')){
                alert('<font>(｡ŏ_ŏ)</font> 折扣格式不正确！');
                return;
               }
             }

             if(upyesvipsizevalueber>100||upyesvipsizevalueber<0){
                alert('<font>(｡ŏ_ŏ)</font> 折扣必须是0-100之间！');
                return;
             }

           }

           //Ajax提交表单
           $.ajax({
            url: '/inc/upfileset.php', // 请求地址
            type: 'POST',   // 请求类型
            data: {
                size:upsizenumberbint,//大小
                fcsize:upyesfcsizenum,//分成
                vipsize:upyesvipsizevalue,//折扣
                mime:upyesfiletypevalue,//MIME类型
                type:notifupValuex,
                imgtype:notifupValuex_img,
            },
                        success: function(upf) { // 成功回调函数
                        if(upf == 500){
                            alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                                    
                         }else if(upf == 200){
                            alert("<font>(◕ܫ◕)</font> 修改成功！");
                         }else if(upf == 400){
                            alert("<font>(｡ŏ_ŏ)</font> 附件大小必须为数字！");
                         }else if(upf == 300){
                            alert("<font>(｡ŏ_ŏ)</font> 附件大小格式不正确！");
                         }else if(upf == 600){
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