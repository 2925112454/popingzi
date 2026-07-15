document.addEventListener('DOMContentLoaded', function () {
    /*
    *
    *回复详情查看↓↓↓↓↓↓↓↓↓↓↓↓↓↓
    * 
    */
    function isIPv6(str) {
        // 正则表达式用于匹配标准和压缩形式的IPv6地址
        const ipv6Pattern = /^(?:[A-F0-9]{1,4}:){7}[A-F0-9]{1,4}$|^(?:[A-F0-9]{1,4}:){1,7}:$|^(?:[A-F0-9]{1,4}:){1,6}:[A-F0-9]{1,4}$|^(?:[A-F0-9]{1,4}:){1,5}(?::[A-F0-9]{1,4}){1,2}$|^(?:[A-F0-9]{1,4}:){1,4}(?::[A-F0-9]{1,4}){1,3}$|^(?:[A-F0-9]{1,4}:){1,3}(?::[A-F0-9]{1,4}){1,4}$|^(?:[A-F0-9]{1,4}:){1,2}(?::[A-F0-9]{1,4}){1,5}$|^[A-F0-9]{1,4}:(?:(?::[A-F0-9]{1,4}){1,6})$|^:(?:(?::[A-F0-9]{1,4}){1,7}|:)$|^(?:[A-F0-9]{1,4}:){1,7}[A-F0-9]{1,4}$/i;
        return ipv6Pattern.test(str);
    }

    const eyeplal=document.querySelectorAll('.flexspace-between-text');//获取所有eyeplal元素
    const eyescomment=document.getElementById('eyescon');//获取dialog
    const eyescommentclose=document.getElementById('eyesconclose');//获取关闭按钮
    const eyescommenttext=document.getElementById('eyescontext');//获取回复显示区域
    const datehftime=document.getElementById('datehftime');//回复时间显示区域
    const datehfip=document.getElementById('datehfip');//回复ip显示区域
    if (eyeplal.length>0){
        //点击事件
        eyeplal.forEach(function(eyeplal){
            eyeplal.addEventListener('click',function(){
                const datatxt=eyeplal.getAttribute('data-con');//回复内容
                const dataconname=eyeplal.getAttribute('data-name');//回复者昵称
                const dataconid=eyeplal.getAttribute('data-aid');//回复者id
                const datahftime=eyeplal.getAttribute('data-time');//回复时间
                const datahfip=eyeplal.getAttribute('data-ip');//回复ip
                const conadmintext=document.getElementById('conadmintext');//回复者及链接显示区域
                if (eyescomment&&eyescommentclose&&eyescommenttext&&conadmintext&&datehfip&&datehftime){
                    //显示dialog
                    eyescomment.showModal();
                    eyescomment.style.display='flex';
                    eyescommenttext.innerHTML=datatxt;
                    if(dataconid>0){
                        conadmintext.innerHTML='回复由 > '+dataconname+' < 发布';
                        conadmintext.href='/user.php?id='+dataconid;
                    }else{
                        conadmintext.innerHTML='回复者账号异常';
                        conadmintext.removeAttribute('href');
                    }
                    //判断ip
                    if(datahfip=='127.0.0.1'||datahfip=='::1'){
                        datehfip.innerHTML='IP：本机';
                    }else{
                        if (datahfip==null||datahfip==''||datahfip==undefined){
                            datehfip.innerHTML='IP：未知';
                        }else{
                            if (isIPv6(datahfip)){//判断是否为ipv6
                                //添加a标签至显示区域
                                datehfip.href='https://www.ipshudi.com/'+datahfip;
                                //只显示前16位
                                const newdatahfip= datahfip.substring(0,10);
                                datehfip.innerHTML='IP：'+newdatahfip+'……';
                            }else{
                                datehfip.href='https://www.ipshudi.com/'+datahfip;
                                datehfip.innerHTML='IP：'+datahfip;
                            }
                            
                        }
                        
                    }
                    datehftime.innerHTML='时间：'+datahftime;
                }

            })
        })
    }
        //关闭事件
        eyescommentclose.addEventListener('click',function(){
            eyescomment.close();
            eyescomment.style.display='none';
    
        });
        //点击背景也能关闭
        eyescomment.addEventListener('click',function(event){
            if (event.target===eyescomment){
                eyescomment.close();
                eyescomment.style.display='none';
            }
        })

    /*
    *
    *评论字数统计↓↓↓↓↓↓↓↓↓↓↓↓↓↓
    * 
    */
   const plv=document.getElementById('plv');//获取评论输入框
   const pvsize=document.getElementById('pvsize');//显示评论字数的区域
   const maxsize=240;//最大字数
   //实时监听plv输入框的值
   if (plv&&pvsize){
    plv.addEventListener('input',function(){
        const plvtxt=plv.value;//获取评论输入框的值
        const plvtxtlen=plvtxt.length;//获取评论输入框的值的长度
        pvsize.innerHTML=plvtxtlen;
        if (plvtxtlen>maxsize){
            //改变颜色
            pvsize.style.color='red';
        }else{
            //去除颜色
            pvsize.style.color='';
        }
    })
   }

    /*
    *
    *回复删除↓↓↓↓↓↓↓↓↓↓↓↓↓↓
    * 
    */
   let delconbut=[];//存储所有删除按钮
   let delif=0;//判断是公告评论还是文章评论,1为公告评论，2为文章评论
   const delcon=document.querySelectorAll('.ggcondel');//获取所有公告评论的回复删除按钮
   if (delcon.length>0){
        delconbut = document.querySelectorAll('.ggcondel');
        delif = 1;
   }else{
        delconbut = document.querySelectorAll('.plcondel');//获取所有文章评论的回复删除按钮
        delif = 2;
   }
   if (delconbut.length>0 && delif>0){
        //点击事件
        delconbut.forEach(function(delconbut){
            delconbut.addEventListener('click',function(){
                const datadelcid=delconbut.getAttribute('data-hfdid');//回复id
                if (datadelcid > 0 && datadelcid !== '' && datadelcid !== null && datadelcid !== undefined && datadelcid.length !== 0 && /^[1-9]\d*$/.test(datadelcid)){
                        
                    if (confirm("确认要删除这条回复吗？")){
                        $.ajax({
                            url: '/inc/replydel.php', // 请求地址
                            type: 'POST',   // 请求类型
                            data: {
                                rid: datadelcid,//回复id
                                dif:delif,//判断是公告评论还是文章评论,1为公告评论，2为文章评论
                            },
                                        success: function(rdel) { // 成功回调函数
                                        if(rdel == 200){
                                            alert("<font>(◕ܫ◕)</font> 删除成功！");
                                            setTimeout(function() {  
                                            location.reload(true);//刷新当前页面
                                            }, 1000);
                                        }else if(rdel == 404){
                                            alert("<font>(｡ŏ_ŏ)</font> 回复不存在！");
                                        }else if(rdel == 500){
                                            alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                                        }else if(rdel == 600){
                                            alert("<font>(｡ŏ_ŏ)</font> 删除失败！");
                                        }else{
                                            alert("<font>(｡ŏ_ŏ)</font> 删除异常！");
                                            console.log(rdel); 
                                        }
                                
                                
                                        }
                    
                        })
                    }
                }
            })
        })

    }
    /*
    *
    *清空全部回复↓↓↓↓↓↓↓↓↓↓↓↓↓↓
    * 
    */
    const allrep=document.getElementById('delallrep');//获取清空全部回复按钮
    if (allrep){
        allrep.addEventListener('click',function(){//点击事件
            if (confirm("操作不可逆，确定要继续嘛？")){
                const allrepid=allrep.getAttribute('data-all');//获取评论ID
                const allrepif=allrep.getAttribute('data-if');//判断是公告评论还是文章评论,1为公告评论，2为文章评论
                if (allrepid > 0 && allrepid !== '' && allrepid !== null && allrepid !== undefined && allrepid.length !== 0 && /^[1-9]\d*$/.test(allrepid)){//判断是否为数字
                    if (allrepif==1||allrepif==2){
                        //弹出输入框
                        const delallrepyes=prompt("请输入“确定清空”，以此来确认您确实需要这么做！");
                        if (delallrepyes=="确定清空"){
                            $.ajax({
                                url: '/inc/replydel.php', // 请求地址
                                type: 'POST',   // 请求类型
                                data: {
                                    allrepid: allrepid,//评论id
                                    allrepif:allrepif,//判断是公告评论还是文章评论,2为公告评论，1为文章评论
                                    all:1
                                },
                                            success: function(allrdel) { // 成功回调函数
                                            if(allrdel == 200){
                                                alert("<font>(◕ܫ◕)</font> 清空成功！");
                                                setTimeout(function() {  
                                                location.reload(true);//刷新当前页面
                                                }, 1000);
                                            }else if(allrdel == 404){
                                                alert("<font>(｡ŏ_ŏ)</font> 评论不存在！");
                                            }else if(allrdel == 405){
                                                alert("<font>(｡ŏ_ŏ)</font> 没有可清空回复！");
                                            }else if(allrdel == 500){
                                                alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                                            }else if(allrdel == 600){
                                                alert("<font>(｡ŏ_ŏ)</font> 清空失败！");
                                            }else{
                                                alert("<font>(｡ŏ_ŏ)</font> 清空异常！");
                                                console.log(allrdel); 
                                            }
                                    
                                    
                                            }
                        
                            })
                        }
                    }else{
                        alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                    }
                }else{
                    alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                }
            }
        })
    }

    /*
    *
    *修改评论↓↓↓↓↓↓↓↓↓↓↓↓↓↓
    * 
    */
   const editcon=document.getElementById('conpost');//获取修改评论按钮
   if (editcon){
    editcon.addEventListener('click',function(){
        const editconid=editcon.getAttribute('data-pid');//获取评论ID
        const editconif=editcon.getAttribute('data-pif');//判断是公告评论还是文章评论,2为公告评论，1为文章评论
        if (editconid > 0 && editconid !== '' && editconid !== null && editconid !== undefined && editconid.length !== 0 && /^[1-9]\d*$/.test(editconid)){//判断是否为数字
            if (editconif==1||editconif==2){
                const newedit=document.getElementById('plv');//获取评论文本框
                if (newedit){
                    const neweditvl=newedit.value;//获取评论内容
                    if (neweditvl==""||neweditvl==null||neweditvl==undefined||neweditvl.length<1){
                        alert("<font>(｡ŏ_ŏ)</font> 评论不能为空！");
                    }else{
                        if (neweditvl.length>240){
                                alert("<font>(｡ŏ_ŏ)</font> 评论字数不能超过240字！");//判断文本内容是否超过240个字
                        }else{

                            let text = 0;
                            if (editconif==1){
                                text=1;//文章评论
                            }else if (editconif==2){
                                text=2;//公告评论
                            }else{
                                text=0;
                            }

                            $.ajax({
                            url: '/inc/newcomment.php', // 请求地址
                            type: 'POST',   // 请求类型
                            data: {
                                text:neweditvl,//文本内容
                                id:editconid,//评论id
                                if:text,//1为文章评论，2为公告评论
                            },
                                    success: function(newcomm) { // 成功回调函数
                                        if(newcomm == 500){
                                            alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                                    
                                        }else if(newcomm == 200){
                                            alert("<font>(◕ܫ◕)</font> 修改成功！");
                                            setTimeout(function() {
                                                location.reload();
                                            }, 1000);//刷新当前页面
                                        }else if(newcomm == 400){
                                            alert("<font>(｡ŏ_ŏ)</font> 评论不能为空！");
                                        }else if(newcomm == 401){
                                            alert("<font>(｡ŏ_ŏ)</font> 评论字数不能超过240字！");
                                        }else if(newcomm == 402){
                                            alert("<font>(｡ŏ_ŏ)</font> 评论没有发生变化！");
                                        }else if(newcomm == 404){
                                            alert("<font>(｡ŏ_ŏ)</font> 评论不存在！");
                                        }else if(newcomm == 600){
                                            alert("<font>(｡ŏ_ŏ)</font> 修改失败！");
                                        }else{
                                            alert("<font>(｡ŏ_ŏ)</font> 服务器出现错误！");
                                            console.log(newcomm);
                                        }
                                    }

                    
                        });

                        }
                    }
                }
            }else{
                alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
            }
        }else{
            alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
        }

    })
   }
/*快速回复*/
                    function arr(n){
                        quicklytextnull.innerHTML='<i class="fa fa-times" aria-hidden="true"></i>'+n;
                        quicklytextnull.style.display='block';
                        setTimeout(function() {
                            quicklytextnull.style.display='none';
                            quicklytextnull.innerHTML='';
                        }, 2000);
                    }

                    function yes(y){
                        quicklytextnull.innerHTML='<i class="fa fa-check" aria-hidden="true"></i>'+y;
                        quicklytextnull.style.display='block';
                        quicklytextnull.style.color="#8bc34a";
                        setTimeout(function() {
                                location.reload();//刷新当前页面
                        }, 2000);
                    }

const quickly=document.getElementById('quickly');//获取快速回复按钮
const eyequickly=document.getElementById('eyequickly');//获取快速回复弹出框(dialog)
const eyequicklyclose=document.getElementById('eyequicklyclose');//获取快速回复弹出框关闭按钮
const quicklytext=document.getElementById('quicklytext');//获取快速回复弹出框文本框
const quicklybtn=document.getElementById('quicklybtn');//获取快速回复弹出框提交按钮
const quicklytextnull=document.getElementById('quicklytextnull');//警示框
    if (quickly&&eyequickly&&eyequicklyclose&&quicklytext&&quicklybtn&&quicklytextnull){
        //快速回复按钮
        quickly.addEventListener('click',function(){
            eyequickly.style.display="flex";
            eyequickly.showModal();
        })
            //快速回复弹出框关闭按钮
            eyequicklyclose.addEventListener('click',function(){
                eyequickly.close();
                eyequickly.style.display="none";
            })
            //快速回复弹出框提交按钮
            quicklybtn.addEventListener('click',function(){
                //获取data-tif；1为文章评论，2为公告评论
                const quicklytif=quicklybtn.getAttribute('data-tif');
                //获取data-repid；这是评论id
                const quicklyrepid=quicklybtn.getAttribute('data-repid');
                if (quicklytif==1||quicklytif==2){
                    //判断id是否是正整数
                    if (quicklyrepid>0){
                        
                        const neweditvl=quicklytext.value;//获取文本内容
                        if (neweditvl==""||neweditvl==null||neweditvl==undefined||neweditvl.length<1){
                            arr('回复不能为空！');
                        }else{
                            if (neweditvl.length>90){
                                arr('回复不能超过90个字！');
                            }else{
                                $.ajax({
                                    url: '/inc/quickly.php', // ajax请求
                                    type: 'POST',   // 请求类型
                                    data: {
                                    value: neweditvl,//回复
                                    repid: quicklyrepid,//评论id
                                    tif: quicklytif,//1为文章评论，2为公告评论
                                    },
                                    success: function(quicklyyes) {
                                        if (quicklyyes == 500){
                                            arr('错误操作！');
                                        }else if(quicklyyes == 600){
                                            arr('回复失败！');
                                        }else if(quicklyyes == 404){
                                            arr('评论ID不存在！');
                                        }else if(quicklyyes == 200){
                                            yes('回复成功，正在刷新页面！');
                                        }else{
                                            arr('程序错误！');
                                            console.log(quicklyyes);
                                        }
                
                                    }         
                                })
                            }
                        }

                    }else{
                        arr('错误操作！');
                    }

                }else{
                    arr('错误操作！');                    
                }

            })

    }
})