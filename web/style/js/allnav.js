document.addEventListener('DOMContentLoaded', function() {
    function setTimeoutinnerHTML(event) {//延迟清除
        setTimeout(function(){
            event.innerHTML = '';
            event.style.color = '';
            event.style.display = 'none';
        },1500);
    }
    //获取所有一级菜单
    var allnavdiv = document.querySelectorAll('.allnavdiv');
    if(allnavdiv){
        allnavdiv.forEach(button => {
            button.addEventListener('click', function(event) {
                const datalinkid = event.target.getAttribute('data-linkid');//获取data-linkid属性的值
                var divboxid = document.getElementById(`navtwoid${datalinkid}`);//获取对应ID的二级菜单div
                if(divboxid){//判断二级菜单是否存在
                    // 显示被点击一级菜单对应的二级菜单
                    if(divboxid.style.display == 'block'){
                        divboxid.style.display = 'none';
                    }else{
                        
                        var allSecondaryMenus = document.querySelectorAll('.navtwo-item'); //所有二级菜单class
                        allSecondaryMenus.forEach(menu => {  
                            menu.style.display = 'none'; // 隐藏所有二级菜单  
                        });  
                        divboxid.style.display = 'block';
                    }
                }
              
            });
            
        });
    }
        //获取所有一级菜单的添加按钮
        var addnavnewtwo = document.querySelectorAll('.navnewtwo');
        if(addnavnewtwo){
            addnavnewtwo.forEach(buttonx => {
                buttonx.addEventListener('click', function(eventx) {
                    const datanavid = eventx.currentTarget.getAttribute('data-navid');
                    var divboxidx = document.getElementById(`navtwoid${datanavid}`);
                    if(divboxidx){
                        if(divboxidx.style.display == 'block'){
                            divboxidx.style.display = 'none';
                        }else{
                            var allSecondaryMenusx = document.querySelectorAll('.navtwo-item');
                            var allnavinput = document.getElementById(`navtwoinput${datanavid}`);
                            allSecondaryMenusx.forEach(menu => {  
                                menu.style.display = 'none';
                            });  
                            divboxidx.style.display = 'block';
                            if (allnavinput) {  
                                allnavinput.focus();  
                            } 
                        }
                    }
                  
                });
                
            });
        }
    //获取所有一级菜单的删除按钮
    var allnavdel = document.querySelectorAll('.navdel');
    if(allnavdel){
        allnavdel.forEach(buttond => {
            buttond.addEventListener('click', function(eventd) {
                const navdelid = eventd.currentTarget.getAttribute('data-navdid');
                if(confirm('确定要删除该列表及分类吗？')){
                    if(confirm('操作不可逆，请确保分类下已无文章！')){
                        if(prompt("请输入‘确认删除’来确定您要这么做！", "")==="确认删除"){
                            if(navdelid && !isNaN(navdelid)){
                                $.ajax({
                                    url: '/inc/navdel.php', // 请求地址
                                    type: 'POST',   // 请求类型
                                    data: {
                                        id:navdelid,//id
                                    },
                                                success: function(navdel) { // 成功回调函数
                                                    if(navdel == 500){
                                                        alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                                    
                                                    }else if(navdel == 200){
                                                        alert("<font>(◕ܫ◕)</font> 删除成功！");
                                                        setTimeout(function(){
                                                           location.reload();//延迟1.5秒刷新
                                                          },1500);
                                                    }else if(navdel == 600){
                                                        alert("<font>(｡ŏ_ŏ)</font> 删除失败！");
                                                    }else if(navdel == 601){
                                                        alert("<font>(｡ŏ_ŏ)</font> 删除分类成功，但列表删除失败！");
                                                    }else if(navdel == 501){
                                                        alert("<font>(｡ŏ_ŏ)</font> 该列表下存在文章，不能删除！");
                                                    }else{
                                                        alert("<font>(｡ŏ_ŏ)</font> 服务器出现错误！");
                                                    }
                                                }
                            
                                });
                            }else{
                                alert('<font>(｡ŏ_ŏ)</font> 错误操作！');
                            }
                        }else{
                            alert('<font>(｡ŏ_ŏ)</font> 输入错误或已取消操作！');
                        }
                     
                    }
                }
            })
        })

    }
    //获取所以二级菜单删除按钮
    var allflnav = document.querySelectorAll('.fldel');
    if(allflnav){
        allflnav.forEach(button => {
            button.addEventListener('click', function(event) {
                const fldelid = event.currentTarget.getAttribute('data-fid');
                const fldelname = document.getElementById(`navflname${fldelid}`);
                if(fldelname.innerHTML == '' || fldelname.innerHTML == null || fldelname.innerHTML == undefined){
                    var fldelnametxt = "该分类";
                }else{
                   var fldelnametxt = ' ['+fldelname.innerHTML+'] ';
                }
                if(confirm('确定要删除'+fldelnametxt+'吗？')){
                    if(confirm('操作不可逆，请确保'+fldelnametxt+'下已无文章！')){
                        if(prompt("请输入‘确认删除’来确定您要这么做！", "")==="确认删除"){
                            if(fldelid && !isNaN(fldelid)){
                                $.ajax({
                                    url: '/inc/fldel.php', // 请求地址
                                    type: 'POST',   // 请求类型
                                    data: {
                                        fid:fldelid,//id
                                    },
                                                success: function(fldel) { // 成功回调函数
                                                    if(fldel == 500){
                                                        alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                                    
                                                    }else if(fldel == 200){
                                                        alert("<font>(◕ܫ◕)</font> 删除成功！");
                                                        setTimeout(function(){
                                                           location.reload();//延迟1.5秒刷新
                                                          },1500);
                                                    }else if(fldel == 600){
                                                        alert("<font>(｡ŏ_ŏ)</font> 删除失败！");
                                                    }else if(fldel == 501){
                                                        alert("<font>(｡ŏ_ŏ)</font> 该分类下存在文章，不能删除！");
                                                    }else{
                                                        alert("<font>(｡ŏ_ŏ)</font> 服务器出现错误！");
                                                        console.log(fldel);
                                                    }
                                                }
                            
                                });
                            }else{
                                alert('<font>(｡ŏ_ŏ)</font> 错误操作！');
                            }
                        }else{
                            alert('<font>(｡ŏ_ŏ)</font> 输入错误或已取消操作！');
                        }
                     
                    }
                }
            })
        })

    }
    //获取所有二级菜单编辑按钮
    var allfledit = document.querySelectorAll('.fledit');
    var navfldialog = document.getElementById('navfldialog');//获取二级菜单编辑弹窗
    if(allfledit&&navfldialog){
        allfledit.forEach(button => {
            button.addEventListener('click', function(event) {
                const fleditid = event.currentTarget.getAttribute('data-fid');
                const fleditnamex = document.getElementById(`navflname${fleditid}`);//获取二级菜单名称
                const navfldialogerr= document.getElementById('navfldialogerr');//获取二级菜单编辑弹窗错误提示
                const navfldialoginput = document.getElementById('navfldialoginput');//获取二级菜单编辑弹窗输入框
                const navfldialogsubmit = document.getElementById('navfldialogbut');//获取二级菜单编辑弹窗提交按钮
                const navfldialogclose = document.getElementById('navfldialogclose');//获取二级菜单编辑弹窗关闭按钮
                const bodynav = document.querySelector('body');//获取body
                if(fleditid && !isNaN(fleditid) && fleditnamex && navfldialogerr && navfldialoginput && navfldialogsubmit && navfldialogclose && navfldialog.close){//显示弹窗
                    navfldialog.showModal();
                    navfldialog.style.display = 'flex';
                    navfldialoginput.value = fleditnamex.innerHTML;
                    navfldialogsubmit.setAttribute('data-yid',fleditid);
                    bodynav.style.overflow = 'hidden';
                }
                if(navfldialog.style.display!=='none' && navfldialogerr && navfldialog.open && navfldialoginput && navfldialogsubmit && navfldialogclose){//若弹窗是打开状态
                    navfldialogclose.addEventListener('click', function(event) {//点击关闭弹窗
                        navfldialogerr.innerHTML = '';
                        navfldialoginput.value = '';
                        navfldialogsubmit.setAttribute('data-yid','');
                        navfldialog.close();
                        navfldialog.style.display = 'none';
                        bodynav.style.overflow = 'auto';
                    });
                    navfldialogsubmit.addEventListener('click', function(event) {//点击提交
                        const navfldialoginputtxt = navfldialoginput.value;//获取输入框内容
                        const navfldialogsubmitid = event.currentTarget.getAttribute('data-yid');//获取提交按钮id

                        if(navfldialoginputtxt && navfldialoginputtxt==fleditnamex.innerHTML){
                            navfldialogerr.innerHTML = '<i class="fa fa-exclamation-circle"></i> 你还没有修改分类名称！';
                            navfldialogerr.style.display = 'block'; 
                            setTimeoutinnerHTML(navfldialogerr);
                            return;
                        }

                        if(navfldialoginputtxt){
                            if(navfldialogsubmitid && !isNaN(navfldialogsubmitid)){

                                $.ajax({
                                    url: '/inc/fledit.php', // 请求地址
                                    type: 'POST',   // 请求类型
                                    data: {
                                        name:navfldialoginputtxt,//名称
                                        id:navfldialogsubmitid,//id
                                    },
                                                success: function(edit) { // 成功回调函数
                                                    if(edit == 500){
                                                        navfldialogerr.innerHTML = '<i class="fa fa-exclamation-circle"></i> 错误操作！';
                                                        navfldialogerr.style.display = 'block'; 
                                                        setTimeoutinnerHTML(navfldialogerr);     
                                                    }else if(edit == 200){
                                                        navfldialogerr.innerHTML = '<i class="fa fa-check-square-o"></i> 修改成功！';
                                                        navfldialogerr.style.display = 'block'; 
                                                        navfldialogerr.style.color = '#4caf50';
                                                        setTimeout(function(){
                                                            location.reload();
                                                          },1500);
                                                    }else if(edit == 404){
                                                        navfldialogerr.innerHTML = '<i class="fa fa-exclamation-circle"></i> 分类名称不能为空！';
                                                        navfldialogerr.style.display = 'block'; 
                                                        setTimeoutinnerHTML(navfldialogerr);   
                                                    }else if(edit == 400){
                                                        navfldialogerr.innerHTML = '<i class="fa fa-exclamation-circle"></i> 列表下已有该分类！';
                                                        navfldialogerr.style.display = 'block'; 
                                                        setTimeoutinnerHTML(navfldialogerr);   
                                                    }else if(edit == 401){
                                                        navfldialogerr.innerHTML = '<i class="fa fa-exclamation-circle"></i> 你还没有修改分类名称！';
                                                        navfldialogerr.style.display = 'block'; 
                                                        setTimeoutinnerHTML(navfldialogerr);   
                                                    }else if(edit == 600){
                                                        navfldialogerr.innerHTML = '<i class="fa fa-exclamation-circle"></i> 修改失败！';
                                                        navfldialogerr.style.display = 'block'; 
                                                        setTimeoutinnerHTML(navfldialogerr);   
                                                    }else{
                                                        navfldialogerr.innerHTML = '<i class="fa fa-exclamation-circle"></i> 服务器错误！';
                                                        navfldialogerr.style.display = 'block'; 
                                                        setTimeoutinnerHTML(navfldialogerr);  
                                                    }
                                                }
                            
                                });

                            }else{
                                navfldialogerr.innerHTML = '<i class="fa fa-exclamation-circle"></i> 错误操作！';
                                navfldialogerr.style.display = 'block';
                                setTimeoutinnerHTML(navfldialogerr);
                            }
                        }else{
                            navfldialogerr.innerHTML = '<i class="fa fa-exclamation-circle"></i> 分类名称不能为空！';
                            navfldialogerr.style.display = 'block';
                            setTimeoutinnerHTML(navfldialogerr);
                        }
                    });
                }
            })
        })
    }
//获取所有二级菜单的新增按钮
var navtwoaddbut = document.querySelectorAll('.navtwoaddbut');
if(navtwoaddbut){
    navtwoaddbut.forEach(function(button){
        button.addEventListener('click', function(event) {
            const navtwoaddbutid = event.currentTarget.getAttribute('data-inputid');
            if(navtwoaddbutid && !isNaN(navtwoaddbutid)){
                const navtwoinput = document.getElementById(`navtwoinput${navtwoaddbutid}`)//获取对应输入框
                if(navtwoinput.value){
                    const navtwoinputvalue = navtwoinput.value;
                    $.ajax({
                        url: '/inc/flnew.php', // 请求地址
                        type: 'POST',   // 请求类型
                        data: {
                            name:navtwoinputvalue,//名称
                            id:navtwoaddbutid,//id
                        },
                                    success: function(newa) { // 成功回调函数
                                        if(newa == 500){
                                            alert('<font>(｡ŏ_ŏ)</font> 错误操作！');
                                        }else if(newa == 200){
                                            alert('<font>(｡ŏ_ŏ)</font> 新增成功！');
                                            setTimeout(function(){
                                                location.reload();
                                              },1500);
                                        }else if(newa == 404){
                                            alert('<font>(｡ŏ_ŏ)</font> 分类名称不能为空！');
                                        }else if(newa == 400){
                                            alert('<font>(｡ŏ_ŏ)</font> 列表下已有相同分类！');
                                        }else if(newa == 402){
                                            alert('<font>(｡ŏ_ŏ)</font> 包含重复的分类项！');
                                        }else if(newa == 600){
                                            alert('<font>(｡ŏ_ŏ)</font> 新增失败！');
                                        }else{
                                            alert('<font>(｡ŏ_ŏ)</font> 服务器错误！');
                                        }
                                    }
                
                    });
                }else{
                    alert('<font>(｡ŏ_ŏ)</font> 分类名称不能为空！');
                }
            }
        })
    })
}
})