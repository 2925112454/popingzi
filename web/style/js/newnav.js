document.addEventListener('DOMContentLoaded', function() {
    const navname = document.getElementById('navname');//获取导航栏名字
    const iconname = document.getElementById('iconname');//获取图标名字
    const navimg=document.getElementById('navimg');//文章封面的类别，1为竖屏，2为横屏，3为资讯类(即一排左侧单图或无图模式)
    const navint=document.getElementById('navint');//文章封面的单行数量，1为默认4张，2为3张（注明：对于类别为‘资讯类’的，此参数无效）
    const addnav=document.getElementById('addnav');//添加按钮
    addnav.addEventListener('click',function(){//添加按钮点击事件
        if(navname.value!=''){
            const navimgvalue=navimg.value;
            const navintvalue=navint.value;
            const navnamevalue=navname.value;
            const iconnamevalue=iconname.value;
            if((navimgvalue==1||navimgvalue==2||navimgvalue==3)&&(navintvalue==1||navintvalue==2)){
                    //Ajax提交表单
                $.ajax({
                    url: '/inc/newnav.php', // 请求地址
                    type: 'POST',   // 请求类型
                    data: {
                        name:navnamevalue,//名称
                        ico:iconnamevalue,//图标
                        nav:navimgvalue,//类别
                        int:navintvalue,//数量
                    },
                                success: function(nav) { // 成功回调函数
                                    if(nav == 500){
                                        alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                                    
                                    }else if(nav == 200){
                                        alert("<font>(◕ܫ◕)</font> 新增成功！");
                                        setTimeout(function(){
                                            window.location.reload();//刷新页面
                                          },2000);
                                    }else if(nav == 404){
                                        alert("<font>(｡ŏ_ŏ)</font> 列表名称不能为空！");
                                    }else if(nav == 600){
                                        alert("<font>(｡ŏ_ŏ)</font> 新增失败！");
                                    }else{
                                        alert("<font>(｡ŏ_ŏ)</font> 服务器出现错误！");
                                    }
                                }
            
                });
            }else{
                alert('<font>(｡ŏ_ŏ)</font> 错误操作！');
            }
        }else{
            alert('<font>(｡ŏ_ŏ)</font> 列表名称不能为空！');
        }
    });
});