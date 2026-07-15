const mesdel=document.querySelectorAll('.mesdel'); //所有删除按钮
const mesyes=document.querySelectorAll('.mesyes'); //所有已读按钮

mesdel.forEach(button => { //为删除按钮添加点击事件
    button.addEventListener('click', function(event) {
        const mesdelid = event.target.getAttribute('data-mesid'); //获取点击按钮的私信id
        if (mesdelid<1 || isNaN(mesdelid) ||  mesdelid=="" || mesdelid==null){
            alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
        }else{
            if (confirm("删除不可逆，确定要删除嘛？")){
            $.ajax({
                url: '/inc/mesdel.php', // ajax请求
                type: 'POST',   // 请求类型
                data: {
                mesid: mesdelid,
                },
                success: function(mesd) {
                    if (mesd == 500){
                        alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                    }else if(mesd == 404){
                        alert("<font>(｡ŏ_ŏ)</font> 私信不存在！");
                    }else if(mesd == 200){
                        alert("<font>(◕ܫ◕)</font> 删除成功！");
                        setTimeout(function() {  
                            location.reload(true);//刷新当前页面
                          }, 1000);
                    }else{
                        alert("<font>(｡ŏ_ŏ)</font> 程序错误！");
                    }

                }         
            });
        }
        }
    })

});

mesyes.forEach(button => { //为已读按钮添加点击事件
    button.addEventListener('click', function(event) {
        const mesyesid = event.target.getAttribute('data-yesid'); //获取点击按钮的私信id
        if (mesyesid<1 || isNaN(mesyesid) ||  mesyesid=="" || mesyesid==null){
            alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
        }else{
            $.ajax({
                url: '/inc/mesyes.php', // ajax请求
                type: 'POST',   // 请求类型
                data: {
                mesyid: mesyesid,
                },
                success: function(mesy) {
                    if (mesy == 500){
                        alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                    }else if(mesy == 404){
                        alert("<font>(｡ŏ_ŏ)</font> 私信不存在！");
                    }else if(mesy == 200){
                        alert("<font>(◕ܫ◕)</font> 标记成功！");
                        setTimeout(function() {  
                            location.reload(true);
                          }, 1000);
                    }else if(mesy == 202){
                        alert("<font>(◕ܫ◕)</font> 取消成功！");
                        setTimeout(function() {  
                            location.reload(true);
                          }, 1000);
                    }else{
                        alert("<font>(｡ŏ_ŏ)</font> 程序错误！");
                    }

                }         
            });
        }
    })

});

const mesallyes=document.getElementById('mesallyes');//全部已读按钮
const mesalldel=document.getElementById('mesalldel');//全部删除按钮
if (mesallyes && mesalldel){

    mesalldel.onclick=function (){//全部删除点击事件
        if (confirm("删除不可逆，确定要删除嘛？")){
            $.ajax({
                url: '/inc/allmesdel.php', // ajax请求
                type: 'POST',   // 请求类型
                success: function(mesalld) {
                    if (mesalld == 500){
                        alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                    }else if(mesalld == 200){
                        alert("<font>(◕ܫ◕)</font> 全部删除成功！");
                        setTimeout(function() {  
                            location.reload(true);
                          }, 1000);
                    }else{
                        alert("<font>(｡ŏ_ŏ)</font> 程序错误！");
                        console.log(mesalld);
                    }

                }         
            });
        }
    }

    mesallyes.onclick=function (){//全部已读点击事件
        $.ajax({
            url: '/inc/allmesyes.php', // ajax请求
            type: 'POST',   // 请求类型
            success: function(mesally) {
                if (mesally == 500){
                    alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                }else if(mesally == 404){
                    alert("<font>(｡ŏ_ŏ)</font> 没有未读私信！");
                }else if(mesally == 200){
                    alert("<font>(◕ܫ◕)</font> 全部标记成功！");
                    setTimeout(function() {  
                        location.reload(true);
                      }, 1000);
                }else{
                    alert("<font>(｡ŏ_ŏ)</font> 程序错误！");
                }

            }         
        });
    }

}