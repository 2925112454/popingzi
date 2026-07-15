const delx=document.getElementById('del');
if (delx){
        delx.addEventListener('click', function(event) {  
if (prompt('请输入“确定删除”，以此来确认您确实需要这么做！')=='确定删除'){
            const delid=event.target.getAttribute('data-del');  
            $.ajax({
                url: '/inc/delete.php', // 请求地址
                type: 'POST',   // 请求类型
                data: {
                did: delid,
                },
                            success: function(del) { // 成功回调函数
                            if(del == 500){
                              alert("<font>(｡ŏ_ŏ)</font> 操作错误！");                                    
                             }else if(del == 404){
                              alert("<font>(｡ŏ_ŏ)</font> 删除文章不存在！");                                                                
                           }else if(del == 505){
                               alert("<font>(｡ŏ_ŏ)</font> 您无权删除该文章！");                                                                
                           }else if(del == 200){
                            alert("<font>(◕ܫ◕)</font> 删除成功！");
                            setTimeout(function() {  
                                location.href="/index.php"; //返回首页 
                              }, 1000);
                        }else{
                            alert("<font>(｡ŏ_ŏ)</font> 程序错误！"); 
                        }                      
                      
                            }
          
              })

            };

        })

}