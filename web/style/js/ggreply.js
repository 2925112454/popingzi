// 获取所有的回复按钮和回复表单  
const replyBtns = document.querySelectorAll('.huifu');  
const replyForms = document.querySelectorAll('.reply-form');
let currentDate = new Date();  
let year = currentDate.getFullYear();  
let month = currentDate.getMonth() + 1; // 注意，月份是从0开始的，所以需要+1  
let day = currentDate.getDate();  
let month2 = month < 10 ? "0" + month : month; // 如果月份小于10，前面加0  
let day2 = day < 10 ? "0" + day : day; // 如果日期小于10，前面加0  
const timess=year + "-" + month2 + "-" + day2;//获取当前时间
// 初始化回复表单的显示状态  
replyForms.forEach(form => {  
  form.style.display = 'none';  
});  
  
// 为每个回复按钮添加点击事件监听器  
replyBtns.forEach(button => {  

  button.addEventListener('click', function(event) {  
    // 获取当前点击的评论ID  
    const currentCommentId = event.target.getAttribute('data-id');  
    // 隐藏所有回复表单，除了当前点击的评论的回复表单  
    replyForms.forEach(form => {  
      if (form.id.startsWith(`reply-form${currentCommentId}`)) {  
   
        if (form.style.display === 'block') {  
            form.style.display = 'none'; 
          } else {  
            form.style.display = 'block';  
  
            const texteaaa = document.getElementById(`reply-text${currentCommentId}`);
            const spanmun= document.getElementById(`spanmun${currentCommentId}`);
            if (spanmun){
                texteaaa.addEventListener('input', function(e) {  
                    //动态计算用户输入了多少字
                    if (e.target.value.length > 0) {
                        //当用户输入超过90字时，禁止用户继续输入
                        if (e.target.value.length > 90) {
                            e.target.value = e.target.value.substring(0, 90);
                        }else{
                            spanmun.innerText = 90 - e.target.value.length;
                        }

                    } else {
                        spanmun.innerText = "90";
                    }

                });  
            }
            const buttont= document.getElementById(`reply-submit${currentCommentId}`);
            if(buttont){
 
                buttont.onclick=function (){
                  const text=document.getElementById(`reply-text${currentCommentId}`).value;
                    if (text==""||text==null||text==undefined||text.length==0){
                        alert("<font>(｡ŏ_ŏ)</font> 回复不能为空！");
                    }else{
                        //判断text字符是否超过90字
                        if (text.length > 90){
                            alert("<font>(｡ŏ_ŏ)</font> 回复字数不能超过90字！");
                        }else{

                          
        $.ajax({
          url: '/inc/ggreply.php', // 请求地址
          type: 'POST',   // 请求类型
          data: {
          rplid: currentCommentId,
          rtext: text,
          },


                      success: function(rep) { // 成功回调函数
                      if(rep == 500){
                          alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                          document.getElementById(`reply-text${currentCommentId}`).value="";                                                                    
                       }else if(rep == 303){
                        alert("<font>(｡ŏ_ŏ)</font> 最多可回复3次！");                                                                
                     }else if(rep == 305){
                      alert("<font>(｡ŏ_ŏ)</font> 含有违禁词，请文明用语！");                                                                
                   }else if(rep == 404){
                          alert("<font>(｡ŏ_ŏ)</font> 评论或回复不存在！"); 
                          document.getElementById(`reply-text${currentCommentId}`).value="";
                      }else if(rep == 300){
                        alert("<font>(｡ŏ_ŏ)</font> 请3分钟后再试！"); 
                        document.getElementById(`reply-submit${currentCommentId}`).onclick=function (){
                          alert("<font>(｡ŏ_ŏ)</font> 操作太频繁啦！");
                          return false;
                        }
                    }else if(rep == 400){
                        alert("<font>(｡ŏ_ŏ)</font> 回复字数超过90字"); 
                      }else if(rep == 200){
                       document.getElementById(`reply-text${currentCommentId}`).value="";
                       var newrepdiv=document.getElementById(`plreply${currentCommentId}`);
                       var newrep="<p><span class='detspan'><a href='user.php?id="+unameid+"' target='_blank'><i style='background:url("+unameimg+");background-size: 100%;    background-repeat: no-repeat;'></i>"+uname+"：</a><span class='timess'>"+timess+"</span></span><span>"+text+"</span></p>";
                      if (newrepdiv){
                       newrepdiv.innerHTML = newrep + newrepdiv.innerHTML;
                       document.getElementById(`reply-submit${currentCommentId}`).onclick=function (){
                        document.getElementById(`reply-text${currentCommentId}`).value="";
                        alert("<font>(｡ŏ_ŏ)</font> 操作太频繁啦！");
                        return false;
                      }
                      }else{
                        document.getElementById(`reply-text${currentCommentId}`).value="";
                        var newrepdivx=document.getElementById(`reply-form${currentCommentId}`);
                        var newrepx="<div id='plreply"+currentCommentId+"' class='plreply' ><p><span class='detspan'><a href='user.php?id="+unameid+"' target='_blank'><i style='background:url("+unameimg+");background-size: 100%;    background-repeat: no-repeat;'></i>"+uname+"：</a><span class='timess'>"+timess+"</span></span><span>"+text+"</span></p></div>";
                        newrepdivx.innerHTML += newrepx;
                        document.getElementById(`reply-submit${currentCommentId}`).onclick=function (){
                          document.getElementById(`reply-text${currentCommentId}`).value="";
                          alert("<font>(｡ŏ_ŏ)</font> 操作太频繁啦！");
                          return false;
                        }
           
                      }
                       }else{
                          alert("<font>(｡ŏ_ŏ)</font> 错误操作！");  
                          console.log(rep);
                      }
                
                
                      }
    
        })






                        }

                    }

                }


            }

          }  

      } else {  
        form.style.display = 'none';  
      }  
    });  
  });  
});

// 获取所有的点赞按钮  
const reptop = document.querySelectorAll('.reptop');  
if (reptop) {  

  reptop.forEach(buttonx => {// 遍历所有点赞按钮 

    buttonx.addEventListener('click', function(eventx) {//监控点赞按钮点击事件
      const repId = buttonx.getAttribute('data-rid'); //获取回复id
      const repm = document.getElementById(`one${repId}`);
      const repmun = repm.innerHTML;

      if (repmun==""||repmun==null||repmun==undefined||repmun.length==0){
        repmunx=0;
      }else{
        repmunx=Number(repmun);
      }

      if (repId!==""&&repId!==null&&repId!==undefined&&repId.length!==0){

       const topa=document.getElementById(`topa${repId}`);
if (topa){



  $.ajax({
    url: '/inc/ggreptop.php', // 请求地址
    type: 'POST',   // 请求类型
    data: {
    topid: repId,
    },
    success: function(top) { // 成功回调函数
      if(top == 500){
        alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
      }else if (top == 200){
        repm.innerHTML = repmunx + 1;
      }else if (top == 300){
        repm.innerHTML = repmunx - 1;
      }else{
        alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
        console.log(top);
      }

    }
  })

}
      }else{
        alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
      }



      
    })
    

  });
}