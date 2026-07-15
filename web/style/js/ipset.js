document.addEventListener('DOMContentLoaded', function() {//监听DOM加载完成
    const safetyform=document.getElementById('safetyform');//获取FORM表单
    const safetybtn=document.getElementById('safetybtn');//获取提交按钮
    safetybtn.addEventListener('click',function(e){
        e.preventDefault();//阻止默认行为
        const safetyip=safetyform.elements.safetyip.value;//获取输入框的值
            //去除输入框中的空格和回车
           const newsafetyip = safetyip.replace(/\s+/g,'');
           if(newsafetyip==''){}else{
                    //将newsafetyip按|分割为数组
                    const iparr=newsafetyip.split('|');
                    //判断数组里面的ip地址是否合法
                    for(let i=0;i<iparr.length;i++){
                    if(!/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(iparr[i])){
                        //判断是否是ipv6
                        if(!/^([0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}$|^([0-9a-fA-F]{1,4}:){1,7}:$|^::([0-9a-fA-F]{1,4}:){0,6}[0-9a-fA-F]{1,4}$|^([0-9a-fA-F]{1,4}:){1,6}::[0-9a-fA-F]{1,4}$|^([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}$|^([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}$|^([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}$|^([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}$|^[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})$|^:(:[0-9a-fA-F]{1,4}){1,7}$/.test(iparr[i])){
                            if(!/^((?:[A-Fa-f0-9]{1,4}(?::[A-Fa-f0-9]{1,4})*)?)::((?:[A-Fa-f0-9]{1,4}(?::[A-Fa-f0-9]{1,4})*)?)$/.test(iparr[i])){
                                alert("<font>(｡ŏ_ŏ)</font> 个别IP地址不合法！");
                                return;
                            }
                        }
                    }
                    //判断是否有重复的ip
                    for(let j=0;j<iparr.length;j++){
                        if(i!=j){
                            if(iparr[i]==iparr[j]){//判断是否重复
                                alert("<font>(｡ŏ_ŏ)</font> 有重复的IP地址,已为你去除！");
                                const newsafetyip2=newsafetyip.replace(iparr[j]+'|','');//去除重复的ip
                                safetyform.elements.safetyip.value=newsafetyip2;
                                return;
                            }
                        }
                    }
                        }
           }
           
               //Ajax提交表单
               $.ajax({
                url: '/inc/ipset.php', // 请求地址
                type: 'POST',   // 请求类型
                data: {
                    newip:newsafetyip,
                },
                            success: function(nip) { // 成功回调函数
                            if(nip == 500){
                                alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                                    
                             }else if(nip == 200){
                                alert("<font>(◕ܫ◕)</font> 修改成功！");
                             }else if(nip == 404){
                                alert("<font>(｡ŏ_ŏ)</font> 个别IP重复，请先清除重复IP！");
                             }else if(nip == 403){
                                alert("<font>(｡ŏ_ŏ)</font> 个别IP地址不合法！");
                             }else{
                                alert("<font>(｡ŏ_ŏ)</font> 服务器出现错误！");
                                console.log(nip);
                             }
                            }
          
              })


    });
});