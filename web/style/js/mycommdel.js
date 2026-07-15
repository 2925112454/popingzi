document.addEventListener('DOMContentLoaded', function() {
    const allcommdel = document.querySelectorAll('.commdel');//所有删除按钮
    if(allcommdel&&allcommdel.length>0){
        allcommdel.forEach(function(button) {
            button.addEventListener('click', function(eventx) {
                const commid = eventx.currentTarget.getAttribute('data-cid');//获取id
                if(commid&&commid>0){//判断行是否存在
                    if(confirm('确定要删除本条评论吗？')){
                        $.ajax({
                            url: 'edit/delmycomm.php',
                            type: 'POST',
                            dataType: 'json',
                            data: {cid: commid},
                            success: function(delcomm) {
                                if(delcomm.code==200){
                                    const mycommidx  = document.getElementById(`mycomm${commid}`);
                                    if(mycommidx){
                                      mycommidx.remove();
                                      const allcommdelx = document.querySelectorAll('.commdel');
                                      if((allcommdelx.length<=0||!allcommdelx)){
                                        window.location.href = '/user/user.php?type=4';
                                      }
                                    }
                                }else if(delcomm.code==500){
                                    alert('<font>(｡ŏ_ŏ)</font>'+delcomm.msg);
                                }else{
                                    alert('<font>(｡ŏ_ŏ)</font> 服务器错误！');
                                }
                           }
                        })
                    }
                }
            })
        })
    }
});