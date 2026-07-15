var down = document.getElementById("gdown");//获取签到按钮
if (down) {
down.onclick=function (){

    if ((typeof gold === "number" && !isNaN(gold)) && (typeof dwid === "number" && !isNaN(dwid)) ) {  

    var userResponse = confirm("是否花费"+gold+"积分购买该文件？");
    if (userResponse) {
                          if(down.innerHTML=="立即购买"){
                                                    $.ajax({
                                                        url: '/inc/down.php', // 请求地址
                                                        type: 'POST',   // 请求类型
                                                        data: {
                                                            dwid: dwid,
                                                          },
                                                                    success: function(downif) { // 成功回调函数
                                                                      if(downif == 400){
                                                                        alert("<font>(◕ܫ◕)</font> 购买成功！");
                                                                        setTimeout(function() {  
                                                                          location.reload(true);//刷新当前页面
                                                                        }, 1000);
                                                                      }else if(downif == 404){
                                                                        alert("<font>(◕ܫ◕)</font> 免费文件无需购买！");
                                                                      }else if(downif == 300){
                                                                        alert("<font>(◕ܫ◕)</font> 积分不足！");
                                                                      }else if(downif == 500){
                                                                        alert("<font>(｡ŏ_ŏ)</font> 购买失败！");
                                                                      }else if(downif == 200){
                                                                        alert("<font>(｡ŏ_ŏ)</font> 已经购买过了！");
                                                                      }else if(downif == 600){
                                                                        alert("<font>(｡ŏ_ŏ)</font> 交易记录错误！");
                                                                        console.log(downif);
                                                                      }else{
                                                                        alert("<font>(｡ŏ_ŏ)</font> 购买失败！");
                                                                        console.log(downif);
                                                                    }
                                                              
                                                              
                                                                    }
                                                  
                                                      })
                          }else{
                              alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                          };
                        }

                    }else{alert("<font>(｡ŏ_ŏ)</font> 错误操作！");}

    };
}