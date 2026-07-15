document.addEventListener('DOMContentLoaded', function() {
    function isValidImageUrl(url) {  
        // 允许的图片后缀名  
        const validExtensions = ['jpg', 'jpeg', 'gif', 'png', 'webp', 'svg','avif'];  
        // 提取最后一个`.`之后的部分作为扩展名  
        const extension = url.split('.').pop().toLowerCase();  
        // 如果扩展名在允许的列表中，则返回true  
        return validExtensions.includes(extension);  
    }

    const wordform=document.getElementById('wordform');//表单
    const newwordsubmit=document.getElementById('newwordsubmit');//提交按钮
    newwordsubmit.addEventListener('click',function(event){
        event.preventDefault();//阻止默认事件
        const rowhead=wordform.rowhead.value;//标题
        const rowimg=wordform.rowimg.value;//封面
        const rowcontent=tinymce.get('rowtext').getContent();//内容
        const rowtop=wordform.rowtop.value;
        if (rowhead==''||rowhead==null||rowhead==undefined){
            alert("<font>(｡ŏ_ŏ)</font> 标题不能为空！");
            return;
        }
        if (rowcontent==''||rowcontent==null||rowcontent==undefined){
            alert("<font>(｡ŏ_ŏ)</font> 内容不能为空！");
            return;
        }
        if (rowimg!=''&&rowimg!=null&&rowimg!=undefined){
            if (!isValidImageUrl(rowimg)) {
                alert("<font>(｡ŏ_ŏ)</font> 封面图片格式错误！");
                return;
            }
        }
        if (rowtop!=1&&rowtop!=2){
            alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
            return;
        }
        function resetSubmitBtn() {
            newwordsubmit.style.pointerEvents = "auto";
            newwordsubmit.style.opacity = 1;
            newwordsubmit.textContent = "提交";
        }
        newwordsubmit.style.pointerEvents="none";
        newwordsubmit.style.opacity=0.5;
        newwordsubmit.textContent="正在提交...";
                    //ajax提交表单
                    $.ajax({
                        url: '/inc/newnotice.php', // 请求地址
                        type: 'POST',   // 请求类型
                        data: {
                        title: rowhead,//标题
                        img:rowimg,//封面图片
                        content: rowcontent,//内容
                        top:rowtop,//置顶
                        },
                                    success: function(not) {
                                    if(not == 500){
                                        alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                                    }else if(not == 200){
                                        alert("<font>(｡ŏ_ŏ)</font> 发布成功，正在跳转！");
                                            setTimeout(function(){
                                                window.location.href = "popingzi.php?type=3";
                                            },2000);
                                    }else if(not == 404){
                                        alert("<font>(｡ŏ_ŏ)</font> 标题或内容不能为空！");
                                        resetSubmitBtn();
                                    }else if(not == 401){
                                        alert("<font>(｡ŏ_ŏ)</font> 封面图片格式不正确！");
                                        resetSubmitBtn();
                                    }else if(not == 600){
                                        alert("<font>(｡ŏ_ŏ)</font> 发布失败！");
                                        resetSubmitBtn();
                                    }else{
                                        alert("<font>(｡ŏ_ŏ)</font> 程序错误！"); 
                                        resetSubmitBtn();
                                    }
                                    }
                      });  
        
    })
});