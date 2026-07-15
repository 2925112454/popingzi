const plbut=document.getElementById('plbut');//获取提交按钮
const pltext=document.getElementById('pltext');//获取文本框

if (plbut && pltext){
  plbut.onclick=function (){
      const textid= plbut.getAttribute('data-txt'); //获取文章id
      const pltextvl=pltext.value;
      if (pltextvl==""||pltextvl==null||pltextvl==undefined||pltextvl.length<1){
alert("<font>(｡ŏ_ŏ)</font> 评论不能为空！");
      }else{

        if (textid<0||textid==null||textid==undefined||textid==""||textid==0){
            alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
        }else{
            
            if (pltextvl.length>240){
                alert("<font>(｡ŏ_ŏ)</font> 评论字数超过限制！");//判断文本内容是否超过240个字
            }else{

                $.ajax({
                    url: '/inc/comments.php', // 请求地址
                    type: 'POST',   // 请求类型
                    data: {
                    coid: textid,
                    cotext: pltextvl,
                    },
                    success: function(comm) { // 成功回调函数
                      if(top == 500){
                        alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                      }else if (comm == 200){
                        location.reload();  
                        location.hash = '#plall';                        
                      }else if (comm == 400){
                        alert("<font>(｡ŏ_ŏ)</font> 该文章最多可评论3条！");
                      }else if (comm == 404){
                        alert("<font>(｡ŏ_ŏ)</font> 评论不能为空！");
                      }else if (comm == 300){
                        alert("<font>(｡ŏ_ŏ)</font> 字数超过240字！");
                      }else if (comm == 305){
                        alert("<font>(｡ŏ_ŏ)</font> 含有违禁词，请文明用语！");
                      }else{
                        alert("<font>(｡ŏ_ŏ)</font> 程序错误！");
                        console.log(comm);
                      }
                
                    }
                  })


            }

        }

      }

    }
}