document.addEventListener('DOMContentLoaded', function() {
    const delcollect = document.querySelectorAll('.delcollect');//所有删除按钮
    if(delcollect&&delcollect.length>0){
        delcollect.forEach(function(button) {
            button.addEventListener('click', function(event) {
                const collectid = event.currentTarget.getAttribute('data-coll');//获取id
                if(collectid&&collectid>0){//判断行是否存在
                    if(confirm('确定要取消收藏吗？')){
                        $.ajax({
                            url: 'edit/delmycoll.php',
                            type: 'POST',
                            dataType: 'json',
                            data: {id: collectid},
                            success: function(delcoll) {
                                if(delcoll.code==200){
                                    const collbox = document.getElementById(`delcollect${collectid}`);
                                    if(collbox){
                                    collbox.remove();
                                    const allrow_box = document.querySelectorAll('.row_box');
                                        if((allrow_box.length<=0||!allrow_box)){
                                            window.location.href = '/user/user.php?type=5';
                                        }
                                    }
                                }else if(delcoll.code==500){
                                    alert('<font>(｡ŏ_ŏ)</font>'+delcoll.msg);
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
})