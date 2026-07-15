document.addEventListener('DOMContentLoaded', function() {
    const allcheckbox = document.getElementById('allcheckbox'); // 全选/取消全选按钮
    const allcheckboxdel = document.getElementById('allcheckboxdel'); // 删除选中项按钮
    const rowdel = document.querySelectorAll('.udel');  //获取所有类名为rowdel的删除按钮
    const allcheckboxexe=document.getElementById('allcheckboxexe'); // 通过选中项按钮

    if (allcheckbox) {
        allcheckbox.addEventListener('click', function(e) {
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
    }

    function isPositiveInteger(num) {
        return Number.isInteger(num) && Math.sign(num) === 1;
    }

    if (allcheckboxdel){
           allcheckboxdel.addEventListener('click', function(e) {
            e.preventDefault();
            // 获取所有选中的复选框的值
            const checkedIds = [];
            const checkboxes = document.querySelectorAll('input[type="checkbox"][name="id"]:checked');
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
                            url: '/inc/alldeluser.php', // 请求地址
                            type: 'POST',   // 请求类型
                            data: {
                                ids:checkedIdsStr,//id
                            },
                                    success: function(alldelrow) { // 成功回调函数
                                        if(alldelrow == 500){
                                            alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                                    
                                        }else if(alldelrow == 200){
                                            alert("<font>(◕ܫ◕)</font> 删除成功！");
                                            setTimeout(function() {
                                                location.reload();
                                            }, 1000);
                                        }else if(alldelrow == 202){
                                            alert("<font>(◕ܫ◕)</font> 除无权删除的用户均已删除！");
                                            setTimeout(function() {
                                                location.reload();
                                            }, 1000);
                                        }else if(alldelrow == 404){
                                            alert("<font>(｡ŏ_ŏ)</font> 个别会员不存在！");
                                        }else if(alldelrow == 600){
                                            alert("<font>(｡ŏ_ŏ)</font> 删除失败！");
                                        }else{
                                            alert("<font>(｡ŏ_ŏ)</font> 服务器出现错误！");
                                            console.log(alldelrow);
                                        }
                                    }
                    
                        });

                }
            }else{
                alert('<font>(｡ŏ_ŏ)</font> 没有选择项！');
            }
        })
    }

    if (allcheckboxexe){
        allcheckboxexe.addEventListener('click', function(e) {
         e.preventDefault();
         // 获取所有选中的复选框的值
         const checkedIdsx = [];
         const checkboxesx = document.querySelectorAll('input[type="checkbox"][name="id"]:checked');
         for (const checkbox of checkboxesx) {
             checkedIdsx.push(Number(checkbox.value)); // 使用Number函数转换为数字
         }
         if (checkedIdsx.length > 0) {
             //判断checkedIdsx里面是否都是正整数，不是则返回错误
             for (let i = 0; i < checkedIdsx.length; i++) {
                 if (isNaN(checkedIdsx[i])||checkedIdsx[i]<=0||checkedIdsx[i]==''||checkedIdsx[i]==' '||checkedIdsx[i]==null||checkedIdsx[i]==undefined||!isPositiveInteger(checkedIdsx[i])||checkedIdsx[i]==NaN) {
                     alert('<font>(｡ŏ_ŏ)</font> 错误操作！');
                     return;
                 }
             }
             // 弹出输入框确认
             const inputConfirmation = prompt('请输入状态(二选一)：封禁、解封');
             if (inputConfirmation !== null&&inputConfirmation!=''&&inputConfirmation!=' '&&inputConfirmation!==''&&inputConfirmation!==undefined&&!isPositiveInteger(inputConfirmation)) {

                if (inputConfirmation=='封禁'||inputConfirmation=='解封'){

                    let inputConfirmationmun=1;
                    if (inputConfirmation=='解封') {
                        inputConfirmationmun=1;
                    }else if(inputConfirmation=='封禁'){
                        inputConfirmationmun=2;
                    }else{
                        inputConfirmationmun=1;
                    }
                   const checkedIdsxString = checkedIdsx.join(',');//将数组转换为字符串
                    $.ajax({
                        url: '/inc/allexiuser.php', // 请求地址
                        type: 'POST',   // 请求类型
                        data: {
                            if:inputConfirmationmun,//状态
                            idsx:checkedIdsxString,//id
                        },
                                success: function(allexirow) { // 成功回调函数
                                    if(allexirow == 500){
                                        alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                                    
                                    }else if(allexirow == 200){
                                        alert("<font>(◕ܫ◕)</font> 修改状态成功！");
                                        setTimeout(function() {
                                            location.reload();
                                        }, 1000);
                                    }else if(allexirow == 600){
                                        alert("<font>(｡ŏ_ŏ)</font> 修改状态失败！");
                                    }else if(allexirow == 404){
                                        alert("<font>(｡ŏ_ŏ)</font> 个别会员不能被操作！");
                                    }else{
                                        alert("<font>(｡ŏ_ŏ)</font> 服务器出现错误！");
                                        console.log(allexirow);
                                    }
                                }
                
                    });

                }else{
                    alert('<font>(｡ŏ_ŏ)</font> 输入错误！');
                }
            }
         }else{
             alert('<font>(｡ŏ_ŏ)</font> 没有选择项！');
         }
     })
 }

    if (rowdel&&rowdel.length>0){
        rowdel.forEach(function(item) {//点击事件
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const rddid = Number(this.getAttribute('data-d'));
                if (rddid == ''||rddid==' '||rddid==null||rddid==undefined||isNaN(rddid)||rddid==NaN||rddid<=0||!isPositiveInteger(rddid)) {
                    alert('<font>(｡ŏ_ŏ)</font> 错误操作！');
                    return;
                }
                if (prompt('请输入“确定删除”，以此来确认您确实需要这么做！')=='确定删除') {
                    $.ajax({
                        url: '/inc/alldeluser.php', // 请求地址
                        type: 'POST',   // 请求类型
                        data: {
                        ids: rddid,
                        },
                                    success: function(del) { // 成功回调函数
        
                                        if(del == 500){
                                            alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                                    
                                        }else if(del == 200){
                                            alert("<font>(◕ܫ◕)</font> 删除成功！");
                                            setTimeout(function() {
                                                location.reload();
                                            }, 1000);
                                        }else if(del == 202){
                                            alert("<font>(◕ܫ◕)</font> 除无权删除的用户均已删除！");
                                            setTimeout(function() {
                                                location.reload();
                                            }, 1000);
                                        }else if(del == 404){
                                            alert("<font>(｡ŏ_ŏ)</font> 个别会员不存在！");
                                        }else if(del == 600){
                                            alert("<font>(｡ŏ_ŏ)</font> 删除失败！");
                                        }else{
                                            alert("<font>(｡ŏ_ŏ)</font> 服务器出现错误！");
                                            console.log(del);
                                        }                   
                              
                                    }
                  
                      })
                }
            });
        });

    }

    
});