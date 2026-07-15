var bodyx = document.body;
var vipa = document.getElementById("vipa");//获取导航栏vip充值按钮
var viplog = document.getElementById("viplog");//获取vip充值框
var vipx= document.getElementById("vipx");//获取关闭按钮
var viplogform = document.getElementById("viplogform");//获取充值表单
var vipbut = document.getElementById("vipbut");//获取充值按钮
var vipxx= document.getElementById("vipxx");//获取vip充值警告框

                 //给vip充值按钮单击事件
                 vipa.onclick=function (){
                    viplog.classList.add('loganime'); 
                    viplog.showModal();
                    bodyx.style.cssText="overflow:hidden;";
                    };

                      
         //VIP充值框关闭按钮
            vipx.onclick=function (){
                viplog.classList.remove('loganime');
                viplog.classList.add('loganimeout');
                setTimeout(function() {  
                  viplog.classList.remove('loganimeout');
                  viplog.classList.remove('loganime');
                 }, 500);
                 bodyx.style.cssText="overflow:;";
                 viplog.close();
                };


                viplogform.addEventListener("change", function() {  
                  var inputsss = viplogform.getElementsByTagName("input");  
                  for (var i = 0; i < inputsss.length; i++) {  
                    if (inputsss[i].value !== "") {  
                      vipxx.innerHTML = '';
                      vipxx.style.display = 'none';   
                    }  
                  }  
                });


//充值表单判断拦截
viplogform.addEventListener('submit', function(event) {  
   event.preventDefault();  //阻止表单默认提交行为

   var usernamevip = viplogform.querySelector("#usernamevip").value;//获取充值卡号内容

            if ( usernamevip === "" ){
               vipxx.style.display="block";
               vipxx.innerHTML='<i class="fa fa-warning"></i>请输入充值卡号！';
               return false;	
            }else{

               vipbut.value="正在充值";
               vipbut.style="pointer-events: none;opacity:0.5;";

               setTimeout(function() {  
   
                  //充值Ajax响应  
                  $.ajax({
                    url: '/inc/viptime.php', // 请求地址
                    type: 'POST',   // 请求类型
                    data: {
                      usernamevip: usernamevip,
                    }, // 发送到服务器的数据
                    success: function(datavip) { // 成功回调函数
                      
                      if(datavip == 1){
                        vipxx.innerHTML = '<i class="fa fa-warning"></i>请输入充值卡号！';
                        vipxx.style.display = 'block';
                        vipbut.value="确定";
                        vipbut.style="pointer-events: auto;opacity:1;";
                       }else if(datavip == 2){
                        vipxx.innerHTML = '<i class="fa fa-warning"></i>充值卡号错误！';
                        vipxx.style.display = 'block';
                        vipbut.value="确定";
                        vipbut.style="pointer-events: auto;opacity:1;";
                       }else if(datavip == 500){
                        vipxx.innerHTML = '<i class="fa fa-warning"></i>错误操作！';
                        vipxx.style.display = 'block';
                        vipbut.value="确定";
                        vipbut.style="pointer-events: auto;opacity:1;";
                       }else if(datavip == 700){
                        vipxx.innerHTML = '<i class="fa fa-warning"></i>大佬，你无需充值！';
                        vipxx.style.display = 'block';
                        vipbut.value="确定";
                        vipbut.style="pointer-events: auto;opacity:1;";
                       }else if(datavip == 404){
                        vipxx.innerHTML = '<i class="fa fa-warning"></i>积分充值失败！';
                        vipxx.style.display = 'block';
                        vipbut.value="确定";
                        vipbut.style="pointer-events: auto;opacity:1;";
                       }else if(datavip == 200){
                        viplog.classList.remove('loganimeout');
                        viplog.classList.remove('loganime');
                        vipxx.innerHTML = '<i class="fa fa-check"></i>成功充值会员一个月！';
                        vipxx.style.display = 'block';
                        vipxx.style.color = '#fff';
                        vipxx.style.background = '#65dd8a';
                     setTimeout(function() {  
                        location.reload(true);//刷新当前页面
                      }, 1000);
                       }else if(datavip == 300){
                        viplog.classList.remove('loganimeout');
                        viplog.classList.remove('loganime');
                        vipxx.innerHTML = '<i class="fa fa-check"></i>成功充值会员三个月！';
                        vipxx.style.display = 'block';
                        vipxx.style.color = '#fff';
                        vipxx.style.background = '#65dd8a';
                     setTimeout(function() {  
                        location.reload(true);//刷新当前页面
                      }, 1000);
                       }else if(datavip == 400){
                        viplog.classList.remove('loganimeout');
                        viplog.classList.remove('loganime');
                        vipxx.innerHTML = '<i class="fa fa-check"></i>成功充值会员十二个月！';
                        vipxx.style.display = 'block';
                        vipxx.style.color = '#fff';
                        vipxx.style.background = '#65dd8a';
                     setTimeout(function() {  
                        location.reload(true);//刷新当前页面
                      }, 1000);
                       }else if(datavip == 600){
                        viplog.classList.remove('loganimeout');
                        viplog.classList.remove('loganime');
                        vipxx.innerHTML = '<i class="fa fa-check"></i>恭喜你成为百年会员！';
                        vipxx.style.display = 'block';
                        vipxx.style.color = '#fff';
                        vipxx.style.background = '#65dd8a';
                     setTimeout(function() {  
                        location.reload(true);//刷新当前页面
                      }, 1000);
                       }else if(datavip == 800){
                        viplog.classList.remove('loganimeout');
                        viplog.classList.remove('loganime');
                        vipxx.innerHTML = '<i class="fa fa-check"></i>积分充值成功！';
                        vipxx.style.display = 'block';
                        vipxx.style.color = '#fff';
                        vipxx.style.background = '#65dd8a';
                     setTimeout(function() {  
                        location.reload(true);//刷新当前页面
                      }, 1000);
                       }else{
                        vipxx.innerHTML = '<i class="fa fa-warning""></i>充值发生未知错误';
                          vipxx.style.display = 'block';
                            vipbut.value="程序发生错误";
                            vipbut.style="pointer-events:none;opacity:0.5;";
                            console.log(datavip); 
                       }
              
              
                    }
              
                  })
              
                }, 1000);




            }




});