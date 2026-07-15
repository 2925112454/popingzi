document.addEventListener('DOMContentLoaded', function() {
    const delmyrow = document.querySelectorAll('.delmyrow');//所有撤销按钮

    if(delmyrow&&delmyrow.length>0){
        delmyrow.forEach(function(button) {
            button.addEventListener('click', function(event) {
                const myrowid = event.target.getAttribute('data-id');//获取id
                if(myrowid&&myrowid>0){//判断行是否存在
                    if(confirm('确定要撤销投稿吗？')){
                        
                        //发送ajax请求
                        $.ajax({
                            url: 'edit/delmyrow.php',
                            type: 'POST',
                            data: {myrowid: myrowid},
                            success: function(data) {
                                if(data.code==200){
                                    const myrowidx  = document.getElementById(`myrow${myrowid}`);
                                    if(myrowidx){
                                        myrowidx.innerHTML='<span class="no">已撤销</span>';
                                    }
                                  const buttons = document.querySelectorAll(`.delmyrow[data-id='${myrowid}']`);
                                    buttons.forEach(function(btn) {
                                        const newButton = document.createElement('i');
                                        newButton.className = 'fa fa-ban';
                                        newButton.innerHTML = '';
                                        newButton.setAttribute('aria-hidden', 'true');
                                        btn.parentNode.replaceChild(newButton, btn);
                                    });
                                }else if(data.code==500){
                                    alert('<font>(｡ŏ_ŏ)</font>'+data.msg);
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