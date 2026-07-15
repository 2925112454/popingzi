document.addEventListener('DOMContentLoaded', function() {
    function isValidPositiveInteger(value) {
        // 先把值转换为字符串
        const str = String(value);
        // 使用正则表达式验证是否为有效的正整数格式
        const regex = /^[1-9]\d*$/;
        // 检查是否符合正则表达式，并且转换回数字后大于 0
        return regex.test(str) && Number(str) > 0;
      }

    function checkInputs(did) {
        $.ajax({
            url: '/inc/alldelnot.php', // 请求地址
            type: 'POST',   // 请求类型
            data: {
                ids:did,//id
            },
                    success: function(alldelnot) { // 成功回调函数
                        if(alldelnot == 500){
                            alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                                    
                        }else if(alldelnot == 200){
                            alert("<font>(◕ܫ◕)</font> 删除成功！");
                            setTimeout(function() {
                                location.reload();//刷新页面
                            }, 1000);
                        }else if(alldelnot == 404){
                            alert("<font>(｡ŏ_ŏ)</font> 个别文章不存在！");
                        }else if(alldelnot == 600){
                            alert("<font>(｡ŏ_ŏ)</font> 删除失败！");
                        }else{
                            alert("<font>(｡ŏ_ŏ)</font> 服务器出现错误！");
                        }
                    }
    
        });
    }

    const notrowdel = document.querySelectorAll('.rowdel'); // 获取所有删除按钮
    if (notrowdel) {
        notrowdel.forEach(button => { 
            button.addEventListener('click', function(event) {
                const target = event.target.closest('.rowdel'); // 确保选中 .rowdel 元素
                const notid = target ? target.getAttribute('data-d') : null; // 获取 data-d 的值
                const num = parseInt(notid);
                if (notid && !isNaN(num) && num > 0 && Number.isInteger(num) && isValidPositiveInteger(notid)) {
                    if (prompt('请输入“确定删除”，以此来确认您确实需要这么做！')=='确定删除') {
                        checkInputs(notid);
                    }                    
                } else {
                    alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                }
            });
        });
    }


    /* 批量删除 */
    const alldelbut= document.getElementById('allcheckbox');//全选or取消全选按钮
    const allcheckboxdel = document.getElementById('allcheckboxdel');//批量删除按钮
    if (alldelbut&&allcheckboxdel) {
        alldelbut.addEventListener('click', function(e) {
            e.preventDefault();
            let allChecked = true;
            const checkboxes = document.querySelectorAll('input[type="checkbox"][name="id"]');
            
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

        allcheckboxdel.addEventListener('click', function(e) {
            e.preventDefault();
            const checkboxesx = document.querySelectorAll('input[type="checkbox"][name="id"]:checked');
            let checkedIds = [];
            if (checkboxesx.length > 0) {

                for (const checkbox of checkboxesx) {
                    checkedIds.push(checkbox.value);
                }

                if (checkedIds.length > 0) {
                    //判断checkedIds里面是否都是正整数，不是则返回错误
                    for (let i = 0; i < checkedIds.length; i++) {
                        if (isNaN(checkedIds[i])||checkedIds[i]<1||checkedIds[i]==''||checkedIds[i]==' '||checkedIds[i]==null||checkedIds[i]==undefined||checkedIds[i]==NaN) {
                            alert('<font>(｡ŏ_ŏ)</font> 错误操作！');
                            return;
                        }
                        if (!isValidPositiveInteger(checkedIds[i].trim())) {
                            alert('<font>(｡ŏ_ŏ)</font> 错误操作！');
                            return;
                        }
                    }

                    if (prompt('请输入“确定删除”，以此来确认您确实需要这么做！')=='确定删除') {
                        const checkedIdsStr = checkedIds.join(',');
                        checkInputs(checkedIdsStr);
                    }

                }

                

            } else {
                alert('<font>(｡ŏ_ŏ)</font> 没有选择项！');
            }
            
        });



    }else{
        alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
    }

});