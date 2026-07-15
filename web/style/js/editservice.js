document.addEventListener('DOMContentLoaded', function() {
    /*添加工单分类*/
    const servicefl=document.getElementById('servicefl');//添加分类输入框
    const servicebtn=document.getElementById('servicebtn');//添加分类按钮
    if(servicefl&&servicebtn){
        servicebtn.addEventListener('click',function(){
            const serviceflvalue=servicefl.value;//获取输入框的值
            const serviceflvaluenul=serviceflvalue.trim();//去除空格
            if(serviceflvalue&&serviceflvalue!==' '&&serviceflvaluenul){
                if (serviceflvalue.length>10){
                    alert('<font>(｡ŏ_ŏ)</font> 分类名称不能超过10个字符！');
                }else{
                    $.ajax({
                        url: '/inc/newservice.php', // ajax请求
                        type: 'POST',   // 请求类型
                        dataType: 'json',// 返回的数据类型
                        data: {
                        ser: serviceflvalue,
                        },
                        success: function(newser) {
                            if (newser.err == 500){
                                alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                            }else if(newser.err == 600){
                                alert("<font>(｡ŏ_ŏ)</font> 添加失败！");
                            }else if(newser.err == 400){
                                alert("<font>(｡ŏ_ŏ)</font> 存在重复分类！");
                            }else if(newser.err == 200){
                                alert("<font>(◕ܫ◕)</font> 添加成功！");
                                servicefl.value='';
                                const newserviceul=document.getElementById('newserviceul');//获取ul
                                if(newserviceul){
                                    //添加html
                                    newserviceul.innerHTML+='<li id="serliid'+newser.id+'"><div class="serviceli"><span id="newsertxt'+newser.id+'">'+newser.name+'</span><div class="serfleditdiv"><a title="删除" class="serfldel" data-fid="'+newser.id+'"><i class="fa fa-trash-o" aria-hidden="true"></i></a><a title="编辑" class="serfledit" data-txt="'+newser.name+'" data-fid="'+newser.id+'"><i class="fa fa-edit" aria-hidden="true"></i></a></div></div></li>';
                                    bindDeleteEvents();
                                    bindeditEvents();
                                }
                            }else{
                                alert("<font>(｡ŏ_ŏ)</font> 程序错误！");
                            }
                        }         
                    })
                }
            }else{
                alert('<font>(｡ŏ_ŏ)</font> 分类名称不能为空！');
            }
        })
    }


    /*删除工单分类*/
    function bindDeleteEvents() {
        const serfldel = document.querySelectorAll('.serfldel');
        serfldel.forEach(function(serfldel) {
            serfldel.addEventListener('click', function() {
                const serfldelid = serfldel.getAttribute('data-fid');
                // 判断serfldelid是否为空且是否是正整数
                if (serfldelid && !isNaN(serfldelid) && parseInt(serfldelid) > 0) {
                    if (prompt('请输入“确定删除”，以此来确认您确实需要这么做！')=='确定删除') {
                        $.ajax({
                            url: '/inc/delservice.php', // ajax请求
                            type: 'POST',   // 请求类型
                            data: {
                            id: serfldelid,
                            },
                            success: function(delser) {
                                if (delser == 500){
                                    alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                                }else if(delser == 600){
                                    alert("<font>(｡ŏ_ŏ)</font> 删除失败！");
                                }else if(delser == 404){
                                    alert("<font>(｡ŏ_ŏ)</font> 分类不存在！");
                                }else if(delser == 200){
                                    const delserliid = document.getElementById('serliid' + serfldelid);
                                    if(delserliid){
                                        delserliid.remove();
                                    }else{
                                        alert("<font>(◕ܫ◕)</font> 删除成功，正在刷新页面！");
                                        setTimeout(function() {  
                                            location.reload(true);//刷新当前页面
                                        }, 2000);
                                    }
                                }else{
                                    alert("<font>(｡ŏ_ŏ)</font> 程序错误！");
                                }
        
                            }         
                        })
                    }

                }
            });
        });
    }
    bindDeleteEvents();    // 初始化绑定删除事件

/*编辑工单分类*/
const navfldialog=document.getElementById('navfldialog');//dialog弹出层
const navfldialogclose=document.getElementById('navfldialogclose');//关闭按钮
const navfldialoginput=document.getElementById('navfldialoginput');//输入框
const navfldialogbut=document.getElementById('navfldialogbut');//确认按钮
const navfldialogerr=document.getElementById('navfldialogerr');//错误提示
function bindeditEvents() {
        const serfledit = document.querySelectorAll('.serfledit');
        serfledit.forEach(function(serfledit) {
            serfledit.addEventListener('click', function() {
                const serfleditid = serfledit.getAttribute('data-fid');
                const serfledittxt = serfledit.getAttribute('data-txt');
                // 判断serfleditid是否为空且是否是正整数
                if (serfleditid && !isNaN(serfleditid) && parseInt(serfleditid) > 0) {
                    if (serfledittxt){
                        navfldialog.showModal();
                        navfldialoginput.value=serfledittxt;
                        navfldialogbut.setAttribute('data-yid', serfleditid);
                        navfldialog.style.display="flex";
                    }
                }
            });
        });
}
if (navfldialog && navfldialogclose && navfldialoginput && navfldialogbut && navfldialogerr){
    bindeditEvents();// 初始化绑定编辑事件
    //关闭弹窗
    navfldialogclose.addEventListener('click', function(event) {
        event.preventDefault();
        navfldialogerr.innerHTML = '';
        navfldialoginput.value = '';
        navfldialogbut.setAttribute('data-yid','');
        navfldialog.close();
        navfldialog.style.display = 'none';
        navfldialogerr.style.display = 'none';
        navfldialogbut.style.color = '';
    });
        //错误信息处理函数
        function sererr(text){
            navfldialogerr.innerHTML = '<i class="fa fa-times" aria-hidden="true"></i>'+text;
            navfldialogerr.style.display = 'block';
            setTimeout(function() {  
                navfldialogerr.innerHTML = '';
                navfldialogerr.style.display="none";
            }, 2000);
        }
    //确认修改
    navfldialogbut.addEventListener('click', function(event) {
        event.preventDefault();
        if (navfldialoginput.value.trim() !== '') {
            const editserflid = navfldialogbut.getAttribute('data-yid');//分类id
            if (editserflid && !isNaN(editserflid) && parseInt(editserflid) > 0) {
                const newserflname = navfldialoginput.value;//分类名称
                    if (newserflname&&newserflname.length > 0){
                        if (newserflname.length <= 10){
                            $.ajax({
                                url: '/inc/editservicefl.php', // ajax请求
                                type: 'POST',   // 请求类型
                                data: {
                                id: editserflid,
                                name:newserflname
                                },
                                success: function(newser) {
                                    if (newser == 500){
                                        sererr('错误操作！');
                                    }else if(newser == 600){
                                        sererr('修改失败！');
                                    }else if(newser == 400){
                                        sererr('重复 或 没有进行任何修改！');
                                    }else if(newser == 200){
                                        navfldialogerr.innerHTML = '<i class="fa fa-check" aria-hidden="true"></i>修改成功！';
                                        navfldialogerr.style.display = 'block';
                                        navfldialogerr.style.color="rgb(139, 195, 74)";
                                        setTimeout(function() {  
                                            navfldialogerr.innerHTML = '';
                                            navfldialogerr.style.display="none";
                                            navfldialogerr.style.color="";
                                        }, 2000);
                                        $('.serfledit[data-fid="'+editserflid+'"]').attr('data-txt',newserflname);//修改前端元素的分类名称
                                        const newsertxt=document.getElementById('newsertxt'+editserflid);
                                        if (newsertxt){
                                            newsertxt.innerHTML=newserflname;
                                        }
                                    }
                                    else{
                                        sererr('程序错误！');
                                    }
            
                                }         
                            })
                        }else{
                            sererr('分类名称不能超过10个字符！');
                        }
                    }else{
                        sererr('分类名称不能为空！');
                    }
            }else{
                sererr('错误操作！');
            }
                
        }else{
            sererr('分类名称不能为空！');
        }
    });
}
})