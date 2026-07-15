document.addEventListener('DOMContentLoaded', function() {
    const dellog=document.getElementById('dellog');//删除选择项
    const ifdellog=document.getElementById('ifdellog');//删除一年前记录
    const ifdellogmo=document.getElementById('ifdellogmo');//删除三月前记录
    const allcheckboxxxx=document.getElementById('allcheckbox');//全选或取消全选
    const allcheckBoxes = document.querySelectorAll('input[type="checkbox"][name="id"]');//所有复选框
    const notlogdel = document.querySelectorAll('.dellog'); // 获取所有删除按钮

        function isValidPositiveInteger(value) {
        // 先把值转换为字符串
        const str = String(value);
        // 使用正则表达式验证是否为有效的正整数格式
        const regex = /^[1-9]\d*$/;
        // 检查是否符合正则表达式，并且转换回数字后大于 0
        return regex.test(str) && Number(str) > 0;
      }

      function checkInputs(d,time) {
        $.ajax({
            url: '/inc/alldellog.php', // 请求地址
            type: 'POST',   // 请求类型
            data: {
                id:d,//id
                time:time//删除指定时间的记录，1为默认，2为一年前的所有记录，3为三月前的所有记录(相当于只保留一年或三月的记录)
            },
                    success: function(alldellog) { // 成功回调函数
                        if(alldellog == 500){
                            alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                                    
                        }else if(alldellog == 200){
                            alert("<font>(◕ܫ◕)</font> 删除成功！");
                            setTimeout(function() {
                                location.reload();//刷新页面
                            }, 1000);
                        }else if(alldellog == 404){
                            alert("<font>(｡ŏ_ŏ)</font> 个别记录不存在！");
                        }else if(alldellog == 600){
                            alert("<font>(｡ŏ_ŏ)</font> 个别记录删除失败！");
                        }else if(alldellog == 600){
                            alert("<font>(｡ŏ_ŏ)</font> 删除失败！");
                        }else{
                            alert("<font>(｡ŏ_ŏ)</font> 服务器出现错误！");
                        }
                    }
    
        });
    }

    if(dellog&&allcheckboxxxx&&allcheckBoxes){

        dellog.addEventListener('click', function(e) {
            e.preventDefault();
            //获取所有选中的复选框的值
            const checkedValues = [];
            for (const checkbox of allcheckBoxes) {
                if (checkbox.checked) {
                    checkedValues.push(checkbox.value);
                }
            }
            if(checkedValues.length>0){
                
                    //判断checkedIds里面是否都是正整数，不是则返回错误
                    for (let i = 0; i < checkedValues.length; i++) {
                        if (isNaN(checkedValues[i])||checkedValues[i]<1||checkedValues[i]==''||checkedValues[i]==' '||checkedValues[i]==null||checkedValues[i]==undefined||checkedValues[i]==NaN) {
                            alert('<font>(｡ŏ_ŏ)</font> 错误操作！');
                            return;
                        }
                        if (!isValidPositiveInteger(checkedValues[i].trim())) {
                            alert('<font>(｡ŏ_ŏ)</font> 错误操作！');
                            return;
                        }
                    }

                    if (prompt('请输入“确定删除”，以此来确认您确实需要这么做！')=='确定删除') {
                        const checkedIdsStr = checkedValues.join(',');
                        checkInputs(checkedIdsStr,1);
                    }

                    
            }else{
               alert('<font>(｡ŏ_ŏ)</font> 没有选择项！');
            }
        });

         // 全选/取消全选功能
        allcheckboxxxx.addEventListener('click', function(e) {
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
    if(notlogdel){
        notlogdel.forEach(button => { 
            button.addEventListener('click', function(event) {
                const target = event.target.closest('.dellog');
                const notid = target ? target.getAttribute('data-d') : null; // 获取 data-d 的值
                const num = parseInt(notid);
                if (notid && !isNaN(num) && num > 0 && Number.isInteger(num) && isValidPositiveInteger(notid)) {
                    if (prompt('请输入“确定删除”，以此来确认您确实需要这么做！')=='确定删除') {
                        checkInputs(notid,1);
                    }
                } else {
                    alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                }
            });
        });
    }
    if(ifdellog){
        ifdellog.addEventListener('click', function(e) {
            e.preventDefault();
            if (prompt('请输入“确定删除”，以此来确认您确实需要这么做！')=='确定删除') {
                checkInputs(0,2);
            }
        });
    }
    if(ifdellogmo){
        ifdellogmo.addEventListener('click', function(e) {
            e.preventDefault();
            if (prompt('请输入“确定删除”，以此来确认您确实需要这么做！')=='确定删除') {
                checkInputs(0,3);
            }
        });
    }
})