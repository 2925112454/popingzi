document.addEventListener('DOMContentLoaded', function() {
    /*批量删除*/
    const allcheckbox = document.getElementById('allcheckbox'); // 全选/取消全选按钮
    const allcheckboxdel = document.getElementById('delallmess'); // 删除选中项按钮
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
                        url: '/inc/alldelmess.php', // ajax请求
                        type: 'POST',   // 请求类型
                        data: {
                        allid: checkedIdsStr,
                        },
                        success: function(alldel) {
                            if (alldel == 500){
                                alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                            }else if(alldel == 600){
                                alert("<font>(｡ŏ_ŏ)</font> 部分删除失败！");
                                setTimeout(function() {  
                                    location.reload(true);//刷新当前页面
                                }, 1000);
                            }else if(alldel == 200){
                                alert("<font>(◕ܫ◕)</font> 删除成功！");
                                setTimeout(function() {  
                                    location.reload(true);//刷新当前页面
                                }, 1000);
                            }
                            else{
                                alert("<font>(｡ŏ_ŏ)</font> 程序错误！");
                            }
    
                        }         
                    })
                }
            }else{
                alert('<font>(｡ŏ_ŏ)</font> 没有选择项！');
            }
        })
    }
    /*预览*/
    const eyemess = document.querySelectorAll('.eyemess');//获取所有预览按钮
    const eyescontwo = document.getElementById('eyescon');//获取dialog标签
    const eyescontexttwo = document.getElementById('eyescontext');//获取内容展示div
    const eyesclosetwo = document.getElementById('eyesconclose');//获取dialog关闭按钮
    if(eyemess&&eyescontwo&&eyescontexttwo&&eyesclosetwo){
        eyemess.forEach(function(eyemess) {
            eyemess.addEventListener('click', function(e) {
                e.preventDefault();
                const messtext = eyemess.getAttribute('data-eye');//获取内容
                // 判断内容是否为空
                if (messtext) {
                    eyescontexttwo.innerHTML = messtext;
                    eyescontwo.showModal();
                    eyescontwo.style.display="flex";
                }
            })
        })
        // 关闭dialog
        eyesclosetwo.addEventListener('click', function(e) {
            e.preventDefault();
            eyescontwo.close();
            eyescontwo.style.display="none";
            eyescontexttwo.innerHTML = '';
        })
            //点击背景也能关闭
            eyescontwo.addEventListener('click', function(e) {
                e.preventDefault();
                if (e.target === eyescontwo) {
                    eyescontwo.close();
                    eyescontwo.style.display="none";
                    eyescontexttwo.innerHTML = '';
                }
            });
    }


/*编辑*/
let nowtext='';
const editmess = document.querySelectorAll('.editmess');//获取所有编辑按钮
const navfldialogtwo = document.getElementById('navfldialog');//获取dialog标签
const navfldialoginputtwo= document.getElementById('navfldialogtextarea');//获取dialog输入框
const navfldialogbuttwo = document.getElementById('navfldialogbut');//获取修改确认按钮
const navfldialogclosetwot = document.getElementById('navfldialogclose');//获取dialog关闭按钮
const navfldialogerrtwo = document.getElementById('navfldialogerr');//获取dialog错误提示
if(editmess&&navfldialogtwo&&navfldialoginputtwo&&navfldialogbuttwo&&navfldialogclosetwot&&navfldialogerrtwo){
        editmess.forEach(function(editmess) {
        editmess.addEventListener('click', function(e) {
            e.preventDefault();
            const messedittxt = editmess.getAttribute('data-t');//获取内容
            const messeditid = editmess.getAttribute('data-i');//获取私信id
            nowtext=messedittxt;
            if (messeditid&&messeditid>0){
                navfldialoginputtwo.value = messedittxt;
                navfldialogbuttwo.setAttribute('data-yid',messeditid);
                navfldialogtwo.showModal();
                navfldialogtwo.style.display="flex";
            }
        })
    })
    // 关闭dialog
    navfldialogclosetwot.addEventListener('click', function(e) {
        e.preventDefault();
        navfldialogtwo.close();
        navfldialogtwo.style.display="none";
        navfldialoginputtwo.value = '';
        navfldialogbuttwo.setAttribute('data-yid','');
        nowtext='';
    })

//修改按钮事件
    navfldialogbuttwo.addEventListener('click', function(e) {
        e.preventDefault();
        const messeditidtwo = navfldialogbuttwo.getAttribute('data-yid');//获取私信id
        const messedittxttwo = navfldialoginputtwo.value;//获取内容
        if (messeditidtwo&&messeditidtwo>0){
            if(messedittxttwo){
if (messedittxttwo==nowtext){
    navfldialogerrtwo.innerHTML = '<i class="fa fa-times" aria-hidden="true"></i>内容没有改动！';
    navfldialogerrtwo.style.display="block";
    setTimeout(function() {
        navfldialogerrtwo.style.display="none";
    }, 2000);
}else{
    $.ajax({
        url: '/inc/editmess.php', // ajax请求
        type: 'POST',   // 请求类型
        data: {
        text: messedittxttwo,
        id: messeditidtwo,
        },
        success: function(editmess) {
            if (editmess == 500){
                navfldialogerrtwo.innerHTML = '<i class="fa fa-times" aria-hidden="true"></i>错误操作！';
                navfldialogerrtwo.style.display="block";
                    setTimeout(function() {
                        navfldialogerrtwo.style.display="none";
                    }, 2000);
            }else if(editmess == 600){
                navfldialogerrtwo.innerHTML = '<i class="fa fa-times" aria-hidden="true"></i>修改失败！！';
                navfldialogerrtwo.style.display="block";
                setTimeout(function() {
                    navfldialogerrtwo.style.display="none";
                }, 2000);
            }else if(editmess == 200){
                nowtext=messedittxttwo;
                const newtext= document.getElementById('editnewid'+messeditidtwo);
                if(newtext){
                    newtext.innerHTML = messedittxttwo;
                    newtext.setAttribute('data-eye',messedittxttwo);
                }
                //寻找data-i=messeditidtwo的元素
                const neweyemess= document.querySelectorAll('.editmess[data-i="'+messeditidtwo+'"]');
                if(neweyemess){
                    neweyemess.forEach(function(neweyemess) {
                        neweyemess.setAttribute('data-t',messedittxttwo);
                    })
                }

                navfldialogerrtwo.innerHTML = '<i class="fa fa-check" aria-hidden="true"></i>修改成功！';
                navfldialogerrtwo.style.color="#8bc34a";
                navfldialogerrtwo.style.display="block";
                setTimeout(function() {
                    navfldialogerrtwo.style.display="none";
                    navfldialogerrtwo.style.color="";
                }, 2000);
            }else{
                navfldialogerrtwo.innerHTML = '<i class="fa fa-times" aria-hidden="true"></i>程序错误！';
                navfldialogerrtwo.style.display="block";
                    setTimeout(function() {
                        navfldialogerrtwo.style.display="none";
                    }, 2000);
            }

        }         
    })
}
             
                
            }else{
                navfldialogerrtwo.innerHTML = '<i class="fa fa-times" aria-hidden="true"></i>私信不能为空！';
                navfldialogerrtwo.style.display="block";
                setTimeout(function() {
                    navfldialogerrtwo.style.display="none";
                }, 2000);
            }
        }else{
            navfldialogerrtwo.innerHTML = '<i class="fa fa-times" aria-hidden="true"></i>错误操作！';
            navfldialogerrtwo.style.display="block";
                setTimeout(function() {
                    navfldialogerrtwo.style.display="none";
                }, 2000);
        }
    })

    
}
/*删除*/
const delmess = document.querySelectorAll('.delmess');//获取所有删除按钮
if(delmess){
    delmess.forEach(function(delmess) {
        delmess.addEventListener('click', function(e) {
            e.preventDefault();
            const messdelid = delmess.getAttribute('data-d');//获取私信id
            if (messdelid&&messdelid>0){
                if (prompt('请输入“确定删除”，以此来确认您确实需要这么做！')=='确定删除') {
                    $.ajax({
                        url: '/inc/alldelmess.php', // ajax请求
                        type: 'POST',   // 请求类型
                        data: {
                        allid: messdelid,
                        },
                        success: function(alldeltwo) {
                            if (alldeltwo == 500){
                                alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                            }else if(alldeltwo == 600){
                                alert("<font>(｡ŏ_ŏ)</font> 删除失败！");
                            }else if(alldeltwo == 200){
                                alert("<font>(◕ܫ◕)</font> 删除成功！");
                                setTimeout(function() {  
                                    location.reload(true);//刷新当前页面
                                }, 1000);
                            }
                            else{
                                alert("<font>(｡ŏ_ŏ)</font> 程序错误！");
                            }
    
                        }         
                    })
                }
            }
        })
    })
}
});