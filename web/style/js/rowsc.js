var rowscbut = document.getElementById("rowsc");//获取收藏按钮
if(rowscbut){

    rowscbut.onclick = function(){

        $.ajax({
            url: '/inc/rowsc.php', // 请求地址
            type: 'POST',   // 请求类型
            data: {
            rid: rid,
            },


                        success: function(sc) { // 成功回调函数
                        if(sc == 500){
                            alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                                    
                         }else if(sc == 200){ 
                            rowscbut.innerHTML ="<i class='fa fa-star'></i>已收藏";
                            rowscbut.className = "rowsc nocopy yes";
                        }else if(sc == 203){
                            rowscbut.innerHTML ="<i class='fa fa-star'></i>收藏";
                            rowscbut.className = "rowsc nocopy";
                        }else if(sc == 404){
                            alert("<font>(｡ŏ_ŏ)</font> 收藏对象不存在！"); 
                        }else{
                            alert("<font>(｡ŏ_ŏ)</font> 错误操作！");  
                            console.log(sc);
                        }
                  
                  
                        }
      
          })

    }

}