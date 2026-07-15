document.addEventListener('DOMContentLoaded', function() {
    const navname = document.getElementById('navname');//获取导航栏名字
    const iconname = document.getElementById('iconname');//获取图标名字
    const navimg=document.getElementById('navimg');//文章封面的类别，1为竖屏，2为横屏，3为资讯类(即一排左侧单图或无图模式)
    const navint=document.getElementById('navint');//文章封面的单行数量，1为默认4张，2为3张（注明：对于类别为‘资讯类’的，此参数无效）
    const addnav=document.getElementById('addnav');//添加按钮
    function isIntegerString(n) {  
        const pattern = /^\d+$/;  
        return pattern.test(n);  
    }
    addnav.addEventListener('click',function(){//添加按钮点击事件
        if(navname.value!=''){
            const navimgvalue=navimg.value;
            const navintvalue=navint.value;
            const navnamevalue=navname.value;
            const iconnamevalue=iconname.value;
            const addnavid=addnav.getAttribute('data-id');
            if((navimgvalue==1||navimgvalue==2||navimgvalue==3)&&(navintvalue==1||navintvalue==2)&&addnavid!=''&&addnavid!=null&&isIntegerString(addnavid)){
                    //Ajax提交表单
                $.ajax({
                    url: '/inc/newnavtwo.php', // 请求地址
                    type: 'POST',   // 请求类型
                    data: {
                        name:navnamevalue,//名称
                        ico:iconnamevalue,//图标
                        nav:navimgvalue,//类别
                        int:navintvalue,//数量
                        id:addnavid,//列表id
                    },
                                success: function(newnav) { // 成功回调函数
                                    if(newnav == 500){
                                        alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                                    
                                    }else if(newnav == 200){
                                        alert("<font>(◕ܫ◕)</font> 修改成功！");
                                        setTimeout(function(){
                                            window.location.href = "popingzi.php?type=2";//跳转
                                          },2000);
                                    }else if(newnav == 404){
                                        alert("<font>(｡ŏ_ŏ)</font> 列表名称不能为空！");
                                    }else if(newnav == 600){
                                        alert("<font>(｡ŏ_ŏ)</font> 修改失败！");
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