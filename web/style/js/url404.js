const urlss = document.getElementById("urlss");//获取状态按钮
const url200=document.getElementById("url200");

function errinnerheml(text,color=""){
  if(urlss&&url200&&text){
    urlss.innerHTML="";
    url200.innerHTML =text;
    url200.style.color=color;
    urlss.style.cssText="display:none;";
  }
}

if (urlss&&url200) {
    urlss.onclick=function (){
        url200.innerHTML="";
                          if(urlss.innerHTML=="点击获取状态"){
                            urlss.innerHTML="正在获取，请稍后...";
                                                    $.ajax({
                                                        url: '/inc/url404.php', // 请求地址
                                                        type: 'POST',   // 请求类型
                                                        data: {
                                                            uuid: id,
                                                          },
                                   
                                                                    success: function(ss) { // 成功回调函数
                                                                      if(ss == 500){
                                                                        alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                                    
                                                                     }else if(ss == 404){
                                                                       errinnerheml("页面不存在或无法访问(404)！","red");
                                                                      }else if(ss == 401){
                                                                        errinnerheml("链接页面的服务器存在错误(500)！","red");
                                                                      }else if (ss == 403){
                                                                        errinnerheml("该文件下载链接无法访问或访问超时！","red");
                                                                      }else if (ss == 429){
                                                                        errinnerheml("操作过于频繁，请三十分钟后再试！","red");
                                                                      }else{
                                                                        if(ss=="百度网盘 请输入提取码"){
                                                                          errinnerheml("资源链接正常，可放心购买！","");
                                                                        }else if(ss=="页面不存在"){
                                                                          errinnerheml("页面不存在或无法访问(404)！","red");
                                                                        }else if(ss=="百度网盘-链接不存在"){
                                                                          errinnerheml("此链接内容可能因为涉及侵权、色情、反动、低俗等信息，无法访问！","red");
                                                                        }else if(ss=="未找到标题"){
                                                                          errinnerheml("没有找到页面标题，链接可能失效！","red");
                                                                        }else if(ss=="百度网盘-免费云盘丨文件共享软件丨超大容量丨存储安全"){
                                                                          errinnerheml("百度网盘首页标题，不是有效网盘链接。","red");
                                                                        }else{
                                                                          errinnerheml(ss,"");
                                                                        }
                                                                    }
                                                              
                                                              
                                                                    }
                                                  
                                                      })
                          }else{
                              alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                          };
                        



    };
}