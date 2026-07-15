document.addEventListener('DOMContentLoaded', function() {
    let userup=[0];
    const userform=document.getElementById('userform');//表单
    const upuser=document.getElementById('newwordsubmit');//提交
    if(userform&&upuser){
        upuser.addEventListener('click', function(e) {
            e.preventDefault();
            const user = userform.user.value;//收件人
            const value= userform.rowtext.value;//内容
            if(value==''||value==' '||value==null||value==undefined){
                alert('<font>(｡ŏ_ŏ)</font> 消息内容不能为空！');
                return;                
            }
            if(user!=''&&user!=' '&&user!=null&&user!=undefined){
                //将收件人中文逗号转为英文逗号
                const usercn = user.replace(/，/g, ',').split(',');
                //判断数组的每个值是否都是阿拉伯数字
                for(let i=0;i<usercn.length;i++){
                    if(isNaN(usercn[i])){
                        alert('<font>(｡ŏ_ŏ)</font> '+usercn[i]+'格式不正确！');
                        console.log(usercn[i]);
                        return;
                    }
                }
                    //去除数组中的重复值和空值
                    userup=usercn.filter(function(item,index,array){
                        return array.indexOf(item) === index && item !== '';
                    });
            }else{
                userup=[0];
            }
            //将userup数组转换为字符串
            const userupfont=userup.join(',');

           //Ajax提交表单
           $.ajax({
            url: '/inc/upuser.php', // 请求地址
            type: 'POST',   // 请求类型
            data: {
                value:value,//内容
                user:userupfont,//收件人
            },
                    success: function(upuser) { // 成功回调函数
                        if(upuser == 500){
                            alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                                    
                         }else if(upuser == 200){
                            alert("<font>(◕ܫ◕)</font> 发送成功！");
                            userform.user.value='';
                            userform.rowtext.value='';
                         }else if(upuser == 404){
                            alert("<font>(｡ŏ_ŏ)</font> 个别会员账号不存在");
                         }else if(upuser == 300){
                            alert("<font>(｡ŏ_ŏ)</font> 个别会员账号格式不正确！");
                         }else if(upuser == 600){
                          alert("<font>(｡ŏ_ŏ)</font> 发送失败！");
                        }else if(upuser == 400){
                            alert("<font>(｡ŏ_ŏ)</font> 消息内容不能为空！");
                        }else{
                            alert("<font>(｡ŏ_ŏ)</font> 服务器出现错误！");
                        }
                    }
      
          });

        });
    }
})