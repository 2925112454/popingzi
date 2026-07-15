const folus=document.querySelectorAll('.folusbut');//获取关注按钮
const folusx=document.getElementById("folus");//唯一关注按钮，用于修改按钮内容
const letter=document.getElementById("letter");//获取私信按钮
const letterx=document.getElementById("letterx");//获取私信关闭按钮
const lettertext=document.getElementById("lettertext");//获取私信输入框
const lettertextform= document.getElementById("lettertextform");//获取私信表单
const lettertextx= document.getElementById("lettertextx");//获取私信错误警告框
const bodyfs= document.body;//获取body标签
$(document).ready(function(){ 
if(folus && letter && lettertext && letterx && lettertextform && lettertextx && folusx){

        //点击私信按钮
        letter.onclick=function (){
        lettertext.showModal();
        lettertext.classList.add('loganime'); 
        bodyfs.style.cssText="overflow:hidden;";
        }

           //点击关闭按钮
           letterx.onclick=function (){
            lettertext.classList.remove('loganime');
            lettertext.classList.add('loganimeout'); 
            setTimeout(function() {  
                lettertext.classList.remove('loganimeout');
                lettertext.classList.remove('loganime');
            }, 500);
            bodyfs.style.cssText="overflow:;";
            lettertext.close();
            };

    //点击关注按钮
    folus.forEach(button => {
        button.addEventListener('click', function(event) {
            const folusid = button.getAttribute('data-fid'); 
            const folusidmun = Number(folusid);//转换为数字
            if (folusidmun > 0 && Number.isInteger(folusidmun)) {
              
                $.ajax({
                    url: '/inc/folus.php', // ajax请求
                    type: 'POST',   // 请求类型
                    data: {
                    fsid: folusidmun,
                    },
                    success: function(fsnew) {
                        if (fsnew == 500){
                            alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                        }else if(fsnew == 404){
                            alert("<font>(｡ŏ_ŏ)</font> 关注对象不存在！");
                        }else if(fsnew == 400){
                            alert("<font>(｡ŏ_ŏ)</font> 别对自己乱来哦！");
                        }else if(fsnew == 200){
                            folusx.innerHTML = "<i class='fa fa-plus'></i>已关注";
                        }else if(fsnew == 202){
                            folusx.innerHTML = "<i class='fa fa-plus'></i>关注";
                        }else if(fsnew == 9527){
                            alert("<font>(｡ŏ_ŏ)</font> 对方账号被封禁啦！");
                        }else{
                            alert("<font>(｡ŏ_ŏ)</font> 程序错误！");
                        }

                    }         
                })

            }else{
                console.log('兄弟，别乱搞！');
            }
                       
        })

    });

    //点击私信发送按钮(私信表单提交)
    lettertextform.addEventListener('submit', function(event) {  
        event.preventDefault();  // 阻止表单默认提交行为
        const lettext = lettertextform.querySelector("#lettertextinput").value;//获取私信内容
        const fsinput = document.getElementById("fsipbut");//获取发送按钮
        const terid = letter.getAttribute('data-id'); //获取收件人id
        if (fsinput){
            fsinput.value = "发送中";
        }

        if (lettext==""&&lettext==null){
            lettertextx.innerHTML="私信内容不能为空";
            lettertextx.style.display = 'block';
            fsinput.value = "发送";
        }else{
            if (lettext.length > 80){
                lettertextx.innerHTML="私信内容不能超过80字";
                lettertextx.style.display = 'block';
                fsinput.value = "发送";
            }else{

                $.ajax({
                    url: '/inc/letter.php', // ajax请求
                    type: 'POST',   // 请求类型
                    data: {
                     text: lettext,
                     terid:terid,//收件人
                    },
                    success: function(fsnew) {
                        if (fsnew == 500){
                            lettertextx.innerHTML="错误操作";
                            lettertextx.style.display = 'block';
                            fsinput.value = "发送";
                        }else if(fsnew == 404){
                            lettertextx.innerHTML="收件人不存在";
                            lettertextx.style.display = 'block';
                            fsinput.value = "发送";
                        }else if(fsnew == 80){
                            lettertextx.innerHTML="私信内容不能超过80字";
                            lettertextx.style.display = 'block';
                            fsinput.value = "发送";
                        }else if(fsnew == 100){
                            lettertextx.innerHTML="私信内容不能为空";
                            lettertextx.style.display = 'block';
                            fsinput.value = "发送";
                        }else if(fsnew == 400){
                            lettertextx.innerHTML="别对自己乱来哦";
                            lettertextx.style.display = 'block';
                            fsinput.value = "发送";
                        }else if(fsnew == 200){
                            lettertextx.innerHTML="发送成功";
                            lettertextx.style.display = 'block';
                            lettertextx.style.color = '#fff';
                            lettertextx.style.background = '#65dd8a';
                            fsinput.value = "已发送";
                            setTimeout(function() {  
                                location.reload(true);//刷新当前页面
                              }, 1000);
                        }else if(fsnew == 505){
                            lettertextx.innerHTML="请勿频繁发送私信（每次需间隔3分钟）";
                            lettertextx.style.display = 'block';
                            fsinput.value = "发送";
                        }else if(fsnew == 305){
                            lettertextx.innerHTML="文明你我他，造福千万家！";
                            lettertextx.style.display = 'block';
                            fsinput.value = "发送";
                        }else if(fsnew == 9527){
                            lettertextx.innerHTML="对方账号已被管理员封禁";
                            lettertextx.style.display = 'block';
                            fsinput.value = "发送";
                        }else{
                            lettertextx.innerHTML="程序错误";
                            lettertextx.style.display = 'block';
                            fsinput.value = "请联系管理员";
                            console.log(fsnew);
                        }

                    }         
                })

            }
            
        }
    });

  // 监听输入框的change事件  
  lettertextform.addEventListener("change", function() {  
    // 遍历所有的输入框元素  
    var inputss = lettertextform.getElementsByTagName("input");  
    for (var i = 0; i < inputss.length; i++) {  
      // 如果输入框被选中，则执行相应的操作  
      if (inputss[i].value !== "") {  
        lettertextx.innerHTML = '';
        lettertextx.style.display = 'none';   
      }  
    }  
  });
  const lettertextxx=document.getElementById("lettertextinput");//获取私信输入框
      //当鼠标聚焦私信输入框时
      if  (lettertextxx){ 
        lettertextxx.onfocus=function (){
            lettertextx.innerHTML = '';
            lettertextx.style.display = 'none';
        };
      }


}
});