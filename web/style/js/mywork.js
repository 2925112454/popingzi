document.addEventListener('DOMContentLoaded', function() {
    const allworkdel = document.querySelectorAll('.workdel');//获取所有删除按钮
    const allworkeye = document.querySelectorAll('.workeye');//所有查看详情按钮
    const allworkeyehf = document.querySelectorAll('.workeyehf');//所有查看回复按钮
    const work_dialog = document.getElementById('work_dialog');//详情弹出层
    const work_dialog_close = document.getElementById('work_dialog_close');//关闭详情弹出层
    const work_dialog_content = document.getElementById('work_dialog_content');//详情展示框
    const dialog_title=document.getElementById('dialog_title');//标题
    /* 详情弹出层 */
    if (allworkeye&&work_dialog&&work_dialog_close&&work_dialog_content&&dialog_title&&allworkeyehf) {

        allworkeye.forEach(function(workeye) {
            workeye.addEventListener('click', function(event) {
                const workcon = event.currentTarget.getAttribute('data-t');
                if(workcon){
                    dialog_title.innerHTML = "工单详情";
                    work_dialog.style.display = 'block';
                    work_dialog_content.innerHTML = workcon;
                    work_dialog.showModal();

                }
            })

        })

        work_dialog_close.onclick = function() {
            work_dialog.style.display = "";
            dialog_title.innerHTML = "";
            work_dialog.close();
        }

        work_dialog.onclick = function() {
            work_dialog.style.display = "";
            dialog_title.innerHTML = "";
            work_dialog.close();
        }

        allworkeyehf.forEach(function(item) {
            item.onclick = function() {
               //获取点击元素的innerHTML
               const workhfcon = item.innerHTML;
               if(workhfcon){
                work_dialog.showModal();
                dialog_title.innerHTML = "回复详情";
                work_dialog.style.display = "block";
                work_dialog_content.innerHTML = workhfcon;
               }
            }
        })

    }

    if(allworkdel){
        allworkdel.forEach(function(d) {
            d.onclick = function(){
                const workid = d.getAttribute('data-kid');
                if(workid&&workid>0){
                    if(confirm('确定要删除工单吗？')){
                        console.log(workid);
                        $.ajax({
                            url: 'edit/delwork.php',
                            type: 'POST',
                            dataType: 'json',
                            data: {id: workid},
                            success: function(delwork) {
                                if(delwork.code==200){
                                    const workidx  = document.getElementById(`mywork${workid}`);
                                    if(workidx){
                                      workidx.remove();
                                      const allworkdelx = document.querySelectorAll('.workdel');
                                      if((allworkdelx.length<=0||!allworkdelx)){
                                        window.location.href = '/user/user.php?type=8';
                                      }
                                    }
                                }else if(delwork.code==500){
                                    alert('<font>(｡ŏ_ŏ)</font>'+delwork.msg);
                                }else{
                                    alert('<font>(｡ŏ_ŏ)</font> 服务器错误！');
                                }
                            }
                        })
                    }
                }
            }
        })
    }

})