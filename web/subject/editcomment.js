document.addEventListener('DOMContentLoaded', function() {
    function isPositiveIntegerByConvert(str) {
        const num = Number(str);
        return !isNaN(num) && Number.isInteger(num) && num > 0 && num.toString() === str;
    }

    /* 标记已读状态 */
    const comm_edit = document.querySelectorAll('.comm-edit');// 获取所有标记已读按钮
    if(comm_edit&&comm_edit.length>0){
        comm_edit.forEach(function(button) {
            button.addEventListener('click', function() {
                const commentId = button.getAttribute('data-comm-id');
                if (isPositiveIntegerByConvert(commentId)&&commentId) {
                    $.ajax({
                        url: 'yessub.php',
                        type: 'POST',
                        data: {
                            id:commentId,
                        },
                        success: function(comm) {
                            if(comm == 200){
                                button.remove();
                                const yes_comm = document.querySelector(`[date-yes-id="${commentId}"]`);
                                if(yes_comm){
                                    yes_comm.remove();
                                }
                            }else{
                                alert('<font>(ಠ.̫.̫ ಠ)</font> 错误操作！');
                            }
                        }
                    });
                }else{
                    alert('<font>(ಠ.̫.̫ ಠ)</font> 错误操作！');
                }
            });
        });
    }

    /* 清除所有未读 */
    const clear_all = document.querySelector('#allsubcomm');
    if(clear_all){
        clear_all.addEventListener('click', function() {
           $.ajax({
                url: 'allyessub.php',
                type: 'POST',
                data: {
                    id:200,
                },
                success: function(allcomm) {
                    if(allcomm == 200){
                        window.location.reload();// 刷新页面
                    }else{
                        alert('<font>(ಠ.̫.̫ ಠ)</font> 错误操作！');
                    }
                }
            });
        })
    }

    /* 删除评论 */
    const del_comm = document.querySelectorAll('.comm-delete');
    if(del_comm&&del_comm.length>0){
        del_comm.forEach(function(button) {
            button.addEventListener('click', function() {
                if (confirm('删除不可撤回，您确定要继续吗？')) {              
                    const d_commentId = button.getAttribute('data-comm-id');
                    if(isPositiveIntegerByConvert(d_commentId)&&d_commentId){
                        $.ajax({
                            url: 'delsub.php',
                            type: 'POST',
                            data: {
                                id:d_commentId,
                            },
                            success: function(delcomm) {
                                if(delcomm == 200){
                                    const all_sub_comm_list = document.querySelectorAll('.sub-comm-list');
                                    if(all_sub_comm_list&&all_sub_comm_list.length<2){
                                        window.location.reload();
                                    }else{
                                            const del_comm = document.querySelector(`[date-del-id="${d_commentId}"]`);
                                            if(del_comm){
                                                del_comm.remove();
                                            }
                                    }
                                }else{
                                    window.location.reload();
                                }
                            }
                        });
                    }else{
                        alert('<font>(ಠ.̫.̫ ಠ)</font> 错误操作！');
                    }
                }
            })
        })
    }
});