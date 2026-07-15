document.addEventListener('DOMContentLoaded', function() {
    function isPositiveInteger(num) {
        return Number.isInteger(num) && Math.sign(num) === 1;
    }
        /*批量删除*/
        const allcheckbox = document.getElementById('allcheckbox'); // 全选/取消全选按钮
        const allcheckboxdel = document.getElementById('delallmess'); // 删除选中项按钮
        const allservice = document.getElementById('allservice'); //修改选中项状态按钮
        if (allcheckbox) {
            allcheckbox.addEventListener('click', function(e) {
                e.preventDefault();
                let allChecked = true;
                const checkboxes = document.querySelectorAll('input[type="checkbox"][name="seid"]');
                
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
        if (allcheckboxdel){
            allcheckboxdel.addEventListener('click', function(e) {
             e.preventDefault();
             // 获取所有选中的复选框的值
             const checkedIds = [];
             const checkboxes = document.querySelectorAll('input[type="checkbox"][name="seid"]:checked');
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
                     console.log(checkedIdsStr);
                     $.ajax({
                         url: '/inc/alldelservice.php', // ajax请求
                         type: 'POST',   // 请求类型
                         data: {
                         allid: checkedIdsStr,
                         },
                         success: function(alldels) {
                             if (alldels == 500){
                                 alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                             }else if(alldels == 600){
                                 alert("<font>(｡ŏ_ŏ)</font> 部分删除失败！");
                                 setTimeout(function() {  
                                     location.reload(true);//刷新当前页面
                                 }, 1000);
                             }else if(alldels == 200){
                                 alert("<font>(◕ܫ◕)</font> 删除成功！");
                                 setTimeout(function() {  
                                     location.reload(true);//刷新当前页面
                                 }, 1000);
                             }else{
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

     if (allservice){
        allservice.addEventListener('click', function(e) {
         e.preventDefault();
         // 获取所有选中的复选框的值
         const checkedIdsx = [];
         const checkboxesx = document.querySelectorAll('input[type="checkbox"][name="seid"]:checked');
         for (const checkboxx of checkboxesx) {
             checkedIdsx.push(Number(checkboxx.value)); // 使用Number函数转换为数字
         }
         if (checkedIdsx.length > 0) {
             //判断checkedIds里面是否都是正整数，不是则返回错误
             for (let i = 0; i < checkedIdsx.length; i++) {
                 if (isNaN(checkedIdsx[i])||checkedIdsx[i]<=0||checkedIdsx[i]==''||checkedIdsx[i]==' '||checkedIdsx[i]==null||checkedIdsx[i]==undefined||!isPositiveInteger(checkedIdsx[i])||checkedIdsx[i]==NaN) {
                     alert('<font>(｡ŏ_ŏ)</font> 错误操作！');
                     return;
                 }
             }
             const allservicexprompt = prompt('请输入您要修改的状态：待处理 或 已处理');
             let muns=null;
             if (allservicexprompt=='待处理') {
                muns=1;
             }else{
                if (allservicexprompt=='已处理') {
                    muns=2;
                }else{
                    muns=0;
                }
             }
             if (muns==1||muns==2) {
                    const checkedIdsStrx = checkedIdsx.join(',');
                    $.ajax({
                        url: '/inc/alleditservice.php', // ajax请求
                        type: 'POST',   // 请求类型
                        data: {
                        allidx: checkedIdsStrx,
                        allmuns: muns,
                        },
                        success: function(alldelsx) {
                            if (alldelsx == 500){
                                alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                            }else if(alldelsx == 600){
                                alert("<font>(｡ŏ_ŏ)</font> 部分修改失败！");
                                setTimeout(function() {  
                                    location.reload(true);//刷新当前页面
                                }, 1000);
                            }else if(alldelsx == 200){
                                alert("<font>(◕ܫ◕)</font> 修改成功！");
                                setTimeout(function() {
                                    location.reload(true);//刷新当前页面
                                }, 1000);
                            }else{
                                alert("<font>(｡ŏ_ŏ)</font> 程序错误！");
                            }
    
                        }         
                    })
             }
         }else{
             alert('<font>(｡ŏ_ŏ)</font> 没有选择项！');
         }
     })
 };
 /*预览*/
 const eyemess = document.querySelectorAll('.eyemess');//获取所有预览按钮
 const eyescontwo = document.getElementById('eyescon');//获取dialog标签
 const eyescontexttwo = document.getElementById('eyescontext');//获取内容展示div
 const eyesclosetwo = document.getElementById('eyesconclose');//获取dialog关闭按钮
 const eyelink= document.getElementById('eyelink');//获取附件链接展示区域
 if(eyemess&&eyescontwo&&eyescontexttwo&&eyesclosetwo&&eyelink){
     eyemess.forEach(function(eyemess) {
         eyemess.addEventListener('click', function(e) {
             e.preventDefault();
             const messtext = eyemess.getAttribute('data-eye');//获取内容
             const messlink = eyemess.getAttribute('data-link');//获取附件地址
             // 判断内容是否为空
             if (messtext) {
                 eyescontexttwo.innerHTML = messtext;
                 eyescontwo.showModal();
                 eyescontwo.style.display="flex";
             }
             if (messlink) {
                 eyelink.href = messlink;
                 eyelink.style.display="flex";
                 eyelink.addEventListener('click', function(e) {
                    e.stopPropagation(); // 阻止事件冒泡
                });
             }else{
                 eyelink.style.display="none";
                 eyelink.href = '';
             }
         })
     })
     // 关闭dialog
     eyesclosetwo.addEventListener('click', function(e) {
         e.preventDefault();
         eyescontwo.close();
         eyescontwo.style.display="none";
         eyescontexttwo.innerHTML = '';
         eyelink.href = '';
         eyelink.style.display="none";
     })
         //点击背景也能关闭
         eyescontwo.addEventListener('click', function(e) {
             e.preventDefault();
             if (e.target === eyescontwo) {
                 eyescontwo.close();
                 eyescontwo.style.display="none";
                 eyescontexttwo.innerHTML = '';
                 eyelink.href = '';
                 eyelink.style.display="none";
             }
         });
 }
/*删除*/
const ddelmess = document.querySelectorAll('.delmess');//获取所有删除按钮
if(ddelmess){
    ddelmess.forEach(function(ddelmess) {
        ddelmess.addEventListener('click', function(e) {
            e.preventDefault();
            const messdelid = ddelmess.getAttribute('data-d');//获取私信id
            if (messdelid&&messdelid>0){
                if (prompt('请输入“确定删除”，以此来确认您确实需要这么做！')=='确定删除') {
                    $.ajax({
                        url: '/inc/alldelservice.php', // ajax请求
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
/*编辑*/
const editmessbutton = document.querySelectorAll('.editmess');//获取所有编辑按钮
const navfldialogtwo = document.getElementById('navfldialog');//获取dialog标签
const navfldialoginputtwo= document.getElementById('navfldialogtextarea');//获取dialog输入框
const navfldialogbuttwo = document.getElementById('navfldialogbut');//获取修改确认按钮
const navfldialogclosetwot = document.getElementById('navfldialogclose');//获取dialog关闭按钮
const navfldialogerrtwo = document.getElementById('navfldialogerr');//获取dialog错误提示
const notifupno = document.getElementById('notifup-allow');//待处理单选框
const notifupyes = document.getElementById('notifup-deny');//已处理单选框
if(editmessbutton&&navfldialogtwo&&navfldialoginputtwo&&navfldialogbuttwo&&navfldialogclosetwot&&navfldialogerrtwo&&notifupno&&notifupyes){
    editmessbutton.forEach(function(editmessbutton) {
        editmessbutton.addEventListener('click', function(e) {
            e.preventDefault();
            const hfval = editmessbutton.getAttribute('data-hft');//回复内容
            const sersid = editmessbutton.getAttribute('data-seid');//工单id
            const sersif = editmessbutton.getAttribute('data-zt');//状态，1待处理，2已处理
            
            if(sersif&&(sersif==1||sersif==2)&&sersid&&sersid>0){
                navfldialogtwo.showModal();
                navfldialogtwo.style.display="flex";
                if(hfval){
                    navfldialoginputtwo.value = hfval;
                }
                if(sersif==1){
                    notifupno.checked = true;
                    notifupyes.checked = false;
                }else{
                    notifupno.checked = false;
                    notifupyes.checked = true;
                }
                //对修改确认按钮的data-yid属性赋值
                navfldialogbuttwo.setAttribute('data-yid',sersid);
            }
        })
    })
       // 关闭dialog
       navfldialogclosetwot.addEventListener('click', function(e) {
        e.preventDefault();
        navfldialogtwo.close();
        navfldialogtwo.style.display="none";
        navfldialoginputtwo.value = '';
    })
    //错误信息处理函数
    function sererr(text){
        navfldialogerrtwo.innerHTML = '<i class="fa fa-times" aria-hidden="true"></i>'+text;
        navfldialogerrtwo.style.display="block";
            setTimeout(function() {  
                navfldialogerrtwo.innerHTML = '';
                navfldialogerrtwo.style.display="none";
        }, 2000);
    }
    //修改提交
    navfldialogbuttwo.addEventListener('click', function(e) {
        e.preventDefault();
        //获取dialog输入框的值
        const hfvalx = navfldialoginputtwo.value;
            //获取单选框的值
            const sersifxv = document.querySelector('input[name="notifup"]:checked').value;
            if (sersifxv==1||sersifxv==2) {
                //获取修改确认按钮的data-yid属性值
                const seryid = navfldialogbuttwo.getAttribute('data-yid');
                if(seryid&&seryid>0){
                    $.ajax({
                        url: '/inc/editservice.php', // ajax请求
                        type: 'POST',   // 请求类型
                        data: {
                        text: hfvalx,//回复内容
                        if:sersifxv,//状态，1待处理，2已处理
                        id:seryid,//工单id
                        },
                        success: function(editser) {
                            if (editser == 500){
                                sererr("错误操作！");
                            }else if(editser == 600){
                                sererr("修改失败！");
                            }else if(editser == 404){
                                sererr("工单不存在！");
                            }else if(editser == 200){
                                navfldialogerrtwo.innerHTML = '<i class="fa fa-check" aria-hidden="true"></i>修改成功,正在刷新页面!';
                                navfldialogerrtwo.style.display="block";
                                navfldialogerrtwo.style.color="rgb(139, 195, 74)";
                                // $('.editmess[data-seid="'+seryid+'"]').attr('data-hft',hfvalx);
                                // $('.editmess[data-seid="'+seryid+'"]').attr('data-zt',sersifxv);
                                setTimeout(function() {  
                                    location.reload(true);//刷新当前页面
                                }, 1500);
                            }
                            else{
                                sererr("程序错误！");
                            }

                        }         
                    })
                }else{
                    sererr("错误参数！");
                }

                
            }else{
                sererr('状态参数不正确！');
            }
        
    })
}
});