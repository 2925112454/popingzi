document.addEventListener('DOMContentLoaded', function() {
    const allcheckbox = document.getElementById('allcheckbox'); // 全选/取消全选按钮
    const allcheckboxdel = document.getElementById('allcheckboxdel'); // 删除选中项按钮

    if (allcheckbox) {
        allcheckbox.addEventListener('click', function(e) {
            e.preventDefault();
            let allChecked = true;
            const checkboxes = document.querySelectorAll('input[type="checkbox"][name="allid"]');
            
            // 检查是否有未选中的复选框
            for (const checkbox of checkboxes) {
                if (!checkbox.checked) {
                    allChecked = false;
                    break;
                }
            }

            // 如果所有复选框都被选中，则取消全选
            if (allChecked) {
                for (const checkbox of checkboxes) {
                    checkbox.checked = false;
                }
            } else {
                // 否则全选所有复选框
                for (const checkbox of checkboxes) {
                    checkbox.checked = true;
                }
            }
        });
    }

    function isPositiveInteger(num) {
        return Number.isInteger(num) && Math.sign(num) === 1;
    }

    if (allcheckboxdel){
           allcheckboxdel.addEventListener('click', function(e) {
            e.preventDefault();
            // 获取所有选中的复选框的值
            const checkedIds = [];
            const checkboxes = document.querySelectorAll('input[type="checkbox"][name="allid"]:checked');
            for (const checkbox of checkboxes) {
                checkedIds.push(Number(checkbox.value)); // 使用Number函数转换为数字
            }
            if (checkedIds.length > 0) {
                //判断checkedIds里面是否都是正整数，不是则返回错误
                for (let i = 0; i < checkedIds.length; i++) {
                    if (isNaN(checkedIds[i])||checkedIds[i]<=0||checkedIds[i]==''||checkedIds[i]==' '||checkedIds[i]==null||checkedIds[i]==undefined||!isPositiveInteger(checkedIds[i])||checkedIds[i]==NaN) {
                        alert('<font>(｡ŏ_ŏ)</font> 错误操作！');
                        return;
                    }
                }
                // 确认删除
                if (prompt('请输入“确定删除”，以此来确认您确实需要这么做！')=='确定删除') {
                    const checkedIdsStr = checkedIds.join(',');
                        $.ajax({
                            url: '/inc/commentalldel.php', // 请求地址
                            type: 'POST',   // 请求类型
                            data: {
                                id:checkedIdsStr,//id
                                type:2,
                            },
                                    success: function(allcommentdel) { // 成功回调函数
                                        if(allcommentdel == 500){
                                            alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                                    
                                        }else if(allcommentdel == 200){
                                            alert("<font>(◕ܫ◕)</font> 删除成功！");
                                            setTimeout(function() {
                                                location.reload();
                                            }, 1000);
                                        }else if(allcommentdel == 404){
                                            alert("<font>(｡ŏ_ŏ)</font> 个别评论不存在！");
                                        }else if(allcommentdel == 600){
                                            alert("<font>(｡ŏ_ŏ)</font> 删除失败！");
                                        }else{
                                            alert("<font>(｡ŏ_ŏ)</font> 服务器出现错误！");
                                            console.log(allcommentdel);
                                        }
                                    }
                    
                        });

                }
            }else{
                alert('<font>(｡ŏ_ŏ)</font> 没有选择项！');
            }
        })
    }


    
});