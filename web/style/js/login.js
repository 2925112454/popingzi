//密码框查看按钮，开启
function passwordeye(){
    // 获取 a 和 input 元素  
  var diva = document.getElementById('login-eye-a');  
  var inputa = document.getElementById('password');  
    // 更改 input 元素的类型  
    inputa.type = 'text';  
     // 更改 i 标签的类  
    var iTag = document.querySelector('.fa.fa-eye-slash');  
    iTag.classList.remove('fa-eye-slash');  
    iTag.classList.add('fa-eye');  
    // 更改 a 的 onkeyup 属性  
    diva.setAttribute("onclick", "passwordeyex()");
    return;
  };
  //密码框查看按钮，关闭
  function passwordeyex(){
    // 获取 a 和 input 元素  
  var divx = document.getElementById('login-eye-a');  
  var inputx = document.getElementById('password');  
    // 更改 a 元素的类型  
    inputx.type = 'password';  
     // 更改 i 标签的类  
    var iTagx = document.querySelector('.fa.fa-eye');  
    iTagx.classList.remove('fa-eye');  
    iTagx.classList.add('fa-eye-slash');  
    // 更改 a 的 onkeyup 属性  
    divx.setAttribute("onclick", "passwordeye()");
    return;
  };
  
  //注册密码框查看按钮，开启
  function newpasswordeye(){
    // 获取 a 和 input 元素  
  var newdiva = document.getElementById('newlogin-eye-a');  
  var newinputa = document.getElementById('newpassword');  
    // 更改 input 元素的类型  
    newinputa.type = 'text';  
     // 更改 i 标签的类  
    var newiTag = document.getElementById('eyei');  
    newiTag.classList.remove('fa-eye-slash');  
    newiTag.classList.add('fa-eye');  
    // 更改 a 的 onkeyup 属性  
    newdiva.setAttribute("onclick", "newpasswordeyex()");
    return;
  };
  //注册密码框查看按钮，关闭
  function newpasswordeyex(){
    // 获取 a 和 input 元素  
  var newdivx = document.getElementById('newlogin-eye-a');  
  var newinputx = document.getElementById('newpassword');  
    // 更改 a 元素的类型  
    newinputx.type = 'password';  
     // 更改 i 标签的类  
    var newiTagx = document.getElementById('eyei');  
    newiTagx.classList.remove('fa-eye');  
    newiTagx.classList.add('fa-eye-slash');  
    // 更改 a 的 onkeyup 属性  
    newdivx.setAttribute("onclick", "newpasswordeye()");
    return;
  };
  
  /*
  注册表单验证↓
  */
  function isNumber(n) {  
    return !isNaN(parseFloat(n)) && isFinite(n);  //定义isnumber
  };
  
  var body= document.body;
  var dl = document.getElementById("Signinlog");//获取登录框
  var zc = document.getElementById("Signuplog");//获取注册框
  var newa = document.getElementById("newa");//获取注册下的登录按钮
  var loga = document.getElementById("loga");//获取登录下的注册按钮
  var showModaladl = document.getElementById("showModaladl");//获取导航栏登录按钮
  var showModalazc = document.getElementById("showModalazc");//获取导航栏注册按钮
  var showModaladl2 = document.getElementById("showModaladl2");//获取侧边栏登录按钮
  var showModalazc2 = document.getElementById("showModalazc2");//获取侧边栏注册按钮
  var showModaladl3 = document.getElementById("showModaladl3");//获取文件下载登录按钮
  var dlxx = document.getElementById("dlxx");//获取登录框关闭按钮
  var zcxx = document.getElementById("zcxx");//获取注册框关闭按钮
  
      //给登录按钮单击事件
      showModaladl.onclick=function (){
        dl.showModal();
    dl.classList.add('loganime'); 
    body.style.cssText="overflow:hidden;";
        };
    
            //给注册按钮单击事件
            showModalazc.onclick=function (){
              zc.classList.add('loganime'); 
              zc.showModal();
              body.style.cssText="overflow:hidden;";
              };
      

  
      //给注登录按钮单击事件2
      if(showModaladl2){
      showModaladl2.onclick=function (){
        dl.showModal();
    dl.classList.add('loganime'); 
    body.style.cssText="overflow:hidden;";
        };
      }
            //给注册按钮单击事件2
            if(showModalazc2){
            showModalazc2.onclick=function (){
              zc.classList.add('loganime'); 
              zc.showModal();
              body.style.cssText="overflow:hidden;";
              };
            }

     if (showModaladl3) {  
        //给注登录按钮单击事件3
      showModaladl3.onclick=function (){
        dl.showModal();
    dl.classList.add('loganime'); 
    body.style.cssText="overflow:hidden;";
        };
            }          


  
      //给注册下的登录按钮单击事件
      newa.onclick=function (){
      zc.classList.remove('loganime');
      zc.classList.add('loganimeout'); 
      zc.close();
      dl.classList.remove('loganimeout');
      dl.classList.add('loganime'); 
      dl.showModal();
      body.style.cssText="overflow:hidden;";
      };
  
          //给登录下的注册按钮单击事件
          loga.onclick=function (){
            dl.classList.remove('loganime');
            dl.classList.add('loganimeout'); 
            dl.close();
            zc.classList.remove('loganimeout');
            zc.classList.add('loganime'); 
            zc.showModal();
            body.style.cssText="overflow:hidden;";
            };
  
      //登录框关闭按钮
      dlxx.onclick=function (){
        dl.classList.remove('loganime');
        dl.classList.add('loganimeout'); 
        setTimeout(function() {  
          zc.classList.remove('loganimeout');
          zc.classList.remove('loganime');
          dl.classList.remove('loganimeout');
          dl.classList.remove('loganime');
        }, 500);
        body.style.cssText="overflow:;";
        dl.close();
        };
    
            //注册框关闭按钮
            zcxx.onclick=function (){
              zc.classList.remove('loganime');
              zc.classList.add('loganimeout');
              setTimeout(function() {  
                zc.classList.remove('loganimeout');
                zc.classList.remove('loganime');
                dl.classList.remove('loganimeout');
                dl.classList.remove('loganime');
              }, 500);
              body.style.cssText="overflow:;";
              zc.close();
              };

  
  var Signuplogform= document.getElementById("Signuplogform");//获取注册表单
  var Signinlogform= document.getElementById("Signinlogform");//获取登录表单
  var x= document.getElementById("x");//获取注册警告框
  var logx= document.getElementById("logx");//获取登录警告框
  
  // 监听输入框的change事件  
  Signuplogform.addEventListener("change", function() {  
    // 遍历所有的输入框元素  
    var inputs = Signuplogform.getElementsByTagName("input");  
    for (var i = 0; i < inputs.length; i++) {  
      // 如果输入框被选中，则执行相应的操作  
      if (inputs[i].value !== "") {  
        x.innerHTML = '';
        x.style.display = 'none';   
      }  
    }  
  });  
  
  Signinlogform.addEventListener("change", function() {  
    var inputss = Signinlogform.getElementsByTagName("input");  
    for (var i = 0; i < inputss.length; i++) {  
      if (inputss[i].value !== "") {  
        logx.innerHTML = '';
        logx.style.display = 'none';   
      }  
    }  
  });  
  
  
  //注册表单判断拦截
  Signuplogform.addEventListener('submit', function(event) {  
    // 阻止表单默认提交行为，以便我们可以手动处理  
    event.preventDefault();  
     //定义非法字符
  var illegalCharacters = /[`~!=|#$%^&*()_+<>?:"{},\/;'[\]]/im;
   //获取注册表单项
  var newname = Signuplogform.querySelector("#newname").value;//获取昵称
  var newusername = Signuplogform.querySelector("#newusername").value;//获取账号
  var newpassword = Signuplogform.querySelector("#newpassword").value;//获取密码
  var newyzm= Signuplogform.querySelector("#newyzm").value;//获取验证码
  var newemail=Signuplogform.querySelector("#newemail").value;//获取电子邮箱
  var newyqmx = document.getElementById("newyqm");
  if (newyqmx){
    var newyqm= newyqmx.value;
  }
  var newbut = document.getElementById("newbut");//获取注册按钮
  
  if(newname === "" || newusername === "" || newpassword === "" || newyzm === "" || newemail === ""){
    x.innerHTML = '<i class="fa fa-warning"></i>昵称、账号、密码、邮箱、验证码不能为空';
    x.style.display = 'block';   
    return false;	
  }else{
        
    if(illegalCharacters.test(newname)){
          x.innerHTML = '<i class="fa fa-warning"></i>昵称不能有特殊符号';
      x.style.display = 'block'; 
          return false;	
      };
       if(newusername.length < 6 || newusername.length > 11){
        x.innerHTML = '<i class="fa fa-warning"></i>账号必须是6-11位的数字';
        x.style.display = 'block'; 
        return false;	
       };
       if(!isNumber(newusername)){
        x.innerHTML = '<i class="fa fa-warning"></i>账号只能是纯数字';
        x.style.display = 'block'; 
        return false;	
       };
       if(newusername.charAt(0) === '0'){
        x.innerHTML = '<i class="fa fa-warning"></i>账号不能以0开头';
        x.style.display = 'block'; 
        return false;	
       };
       if(newusername === newname){
        x.innerHTML = '<i class="fa fa-warning"></i>昵称和账号不能一样';
        x.style.display = 'block'; 
        return false;	
       };
       if(isNumber(newpassword)){
        x.innerHTML = '<i class="fa fa-warning"></i>密码不能只有数字哦';
        x.style.display = 'block'; 
        return false;	
       };
       if(newpassword.length < 6){
        x.innerHTML = '<i class="fa fa-warning"></i>密码不能低于6位数';
        x.style.display = 'block';
        return false;	
       };
  
       function validateEmail(email) {  
        // 正则表达式匹配邮箱地址  
        var re = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;  
        // 判断是否匹配邮箱地址模式  
        if (re.test(email)) {  
          return true;  
        } else {  
          return false;  
        }  
      };
  
       if(validateEmail(newemail)){
       }else{
        x.innerHTML = '电子邮箱格式不正确';
        x.style.display = 'block';
        return false;	
       };
  
       if(newyzm.length < 4){
        x.innerHTML = '<i class="fa fa-warning"></i>请正确输入验证码';
        x.style.display = 'block';
        return false;	
       };
  
       if(newyzm.length > 6){
        x.innerHTML = '<i class="fa fa-warning"></i>验证码最多6位数';
        x.style.display = 'block';
        return false;	
       };
  };
  newbut.value="正在注册";
  newbut.style="pointer-events: none;opacity:0.5;";
  
  setTimeout(function() {  
 
  //注册Ajax响应  
    $.ajax({
      url: '/inc/register.php', // 请求地址
      type: 'POST',   // 请求类型
      data: {
        newname: newname,
        newusername: newusername,
        newpassword: newpassword,
        newyzm: newyzm,
        newyqm: newyqm,
        newemail: newemail,
      }, // 发送到服务器的数据
      success: function(data) { // 成功回调函数
             if(data == 4){
              x.innerHTML = '<i class="fa fa-warning"></i>所有项目都要填写哦';
              x.style.display = 'block';
              newbut.value="注册";
              newbut.style="pointer-events: auto;opacity:;";
             }else if(data == 1){
              x.innerHTML = '<i class="fa fa-warning""></i>请刷新验证码';
              x.style.display = 'block';
              newbut.value="注册";
              newbut.style="pointer-events: auto;opacity:;";
              captchaimg();//刷新验证码
             }else if(data == 2){
              x.innerHTML = '<i class="fa fa-warning""></i>验证码不能为空';
              x.style.display = 'block';
              newbut.value="注册";
              newbut.style="pointer-events: auto;opacity:;";
             }else if(data == 3){
              x.innerHTML = '<i class="fa fa-warning""></i>验证码不正确';
              x.style.display = 'block';
              newbut.value="注册";
              newbut.style="pointer-events: auto;opacity:;";
              captchaimg();//刷新验证码
             }else if(data == 5){
              x.innerHTML = '<i class="fa fa-warning""></i>昵称不能超过12字';
              x.style.display = 'block';
              newbut.value="注册";
              newbut.style="pointer-events: auto;opacity:;";
             }else if(data == 6){
              x.innerHTML = '<i class="fa fa-warning""></i>账号必须为6-11位数字';
              x.style.display = 'block';
              newbut.value="注册";
              newbut.style="pointer-events: auto;opacity:;";
             }else if(data == 505){
              x.innerHTML = '<i class="fa fa-warning""></i>邀请码不能为空！';
              x.style.display = 'block';
              newbut.value="注册";
              newbut.style="pointer-events: auto;opacity:;";
             }else if(data == 506){
              x.innerHTML = '<i class="fa fa-warning""></i>邀请码不正确！';
              x.style.display = 'block';
              newbut.value="注册";
              newbut.style="pointer-events: auto;opacity:;";
              captchaimg();//刷新验证码
             }else if(data == 7){
              x.innerHTML = '<i class="fa fa-warning""></i>账号只能是数字';
              x.style.display = 'block';
              newbut.value="注册";
              newbut.style="pointer-events: auto;opacity:;";
             }else if(data == 8){
              x.innerHTML = '<i class="fa fa-warning""></i>密码不能是纯数字';
              x.style.display = 'block';
              newbut.value="注册";
              newbut.style="pointer-events: auto;opacity:;";
             }else if(data == 9){
              x.innerHTML = '<i class="fa fa-warning""></i>密码不能低于6位数';
              x.style.display = 'block';
              newbut.value="注册";
              newbut.style="pointer-events: auto;opacity:;";
             }else if(data == 60){
              x.innerHTML = '<i class="fa fa-warning""></i>账号不能以0开头';
              x.style.display = 'block';
              newbut.value="注册";
              newbut.style="pointer-events: auto;opacity:;";
             }else if(data == 10){
              x.innerHTML = '<i class="fa fa-warning""></i>邮箱格式不正确';
              x.style.display = 'block';
              newbut.value="注册";
              newbut.style="pointer-events: auto;opacity:;";
             }else if(data == 11){
              x.innerHTML = '<i class="fa fa-warning""></i>昵称和账号不能一样';
              x.style.display = 'block';
              newbut.value="注册";
              newbut.style="pointer-events: auto;opacity:;";
             }else if(data == 12){
              x.innerHTML = '<i class="fa fa-warning""></i>你已经注册过了，请12小时后再试！';
              x.style.display = 'block';
              newbut.value="请登录";
              newbut.style="pointer-events:none;opacity:0.5;";
              captchaimg();//刷新验证码
             }else if(data == 13){
              x.innerHTML = '<i class="fa fa-warning""></i>该账号已被注册';
              x.style.display = 'block';
              newbut.value="注册";
              newbut.style="pointer-events: auto;opacity:;";
              captchaimg();//刷新验证码
             }else if(data == 14){
              x.innerHTML = '<i class="fa fa-warning""></i>注册失败，请重试！';
              x.style.display = 'block';
              newbut.value="注册";
              newbut.style="pointer-events: auto;opacity:;";
              captchaimg();//刷新验证码
             }else if(data == 16){
              x.innerHTML = '<i class="fa fa-warning""></i>邮箱已被注册！';
              x.style.display = 'block';
              newbut.value="注册";
              newbut.style="pointer-events: auto;opacity:;";
              captchaimg();//刷新验证码
             }else if(data == 17){
              x.innerHTML = '<i class="fa fa-warning""></i>该IP已注册过账号！';
              x.style.display = 'block';
              newbut.value="注册";
              newbut.style="pointer-events: auto;opacity:;";
              captchaimg();//刷新验证码
             }else if(data == 15){
              x.innerHTML = '<i class="fa fa-warning""></i>非法操作！';
              x.style.display = 'block';
              newbut.value="注册";
              newbut.style="pointer-events: auto;opacity:;";
              captchaimg();//刷新验证码
              setTimeout(function(){
                window.location.reload();//刷新页面
              },2000);
             }else if(data== 200 ) {

              zc.close();//关闭注册框
              dl.showModal();   //打开登录
              zc.classList.remove('loganimeout');
              zc.classList.remove('loganime');
              dl.classList.remove('loganimeout');
              dl.classList.remove('loganime');
              logx.innerHTML = '<i class="fa fa-check"></i>注册成功,马上登录吧！';
              logx.style.display = 'block';
              logx.style.color = '#fff';
              logx.style.background = '#65dd8a';
              Signinlogform.querySelector("#username").value=newusername;//将账号写入登录表单
              Signinlogform.querySelector("#password").value="";//重置登录密码框
              var inputElements = Signuplogform.elements;   
              for (var i = 0; i < inputElements.length; i++) {  
                if (inputElements[i].type === "text" || inputElements[i].type === "password" || inputElements[i].type === "email") {  
                  inputElements[i].value = "";  
                }  
              } ;//清空注册表单内容
                 newbut.value="注册成功，请登录！";
              captchaimg();//刷新验证码
              newpasswordeyex();//关闭注册密码查看状态
             
             }else{
            
              x.innerHTML = '<i class="fa fa-warning""></i>注册发生未知错误';
              x.style.display = 'block';
              newbut.value="程序发生错误";
              newbut.style="pointer-events:none;opacity:0.5;";
              console.log(data); 

             }
      },
      error: function() { // 失败回调函数
        x.innerHTML = '<i class="fa fa-warning"></i>注册请求失败';
        x.style.display = 'block';
        console.log(error);
      }
    });
  
  
  
  }, 1000);
  
  });  
  
  
  
  //登录表单判断拦截
  Signinlogform.addEventListener('submit', function(event) {  
    // 阻止表单默认提交行为，以便我们可以手动处理  
    event.preventDefault();  
  
   //获取登录表单项
  var username = Signinlogform.querySelector("#username").value;//获取账号
  var password = Signinlogform.querySelector("#password").value;//获取密码
  var logbut = document.getElementById("logbut");//获取登录按钮
  
  if(username === "" || password === ""){
    logx.innerHTML = '<i class="fa fa-warning"></i>账号和密码不能为空';
    logx.style.display = 'block';   
    return false;	
  }else{
   
      if(username.length > 11){
        logx.innerHTML = '<i class="fa fa-warning"></i>账号格式错误';
        logx.style.display = 'block'; 
        return false;	
       };
       if(username.length < 6){
        logx.innerHTML = '<i class="fa fa-warning"></i>账号格式错误';
        logx.style.display = 'block'; 
        return false;	
       };
       if(!isNumber(username)){
        logx.innerHTML = '<i class="fa fa-warning"></i>账号格式错误';
        logx.style.display = 'block'; 
        return false;	
       };
       if(isNumber(password)){
        logx.innerHTML = '<i class="fa fa-warning"></i>密码格式错误';
        logx.style.display = 'block'; 
        return false;	
       };
       if(password.length < 6){
        logx.innerHTML = '<i class="fa fa-warning"></i>密码格式错误';
        logx.style.display = 'block';
        return false;	
       };
       
  };
  
  logbut.value="正在登录";
  logbut.style="pointer-events: none;opacity:0.5;";
  setTimeout(function() {  
   
    //登录Ajax响应  
    $.ajax({
      url: '/inc/login.php', // 请求地址
      type: 'POST',   // 请求类型
      data: {
        username: username,
        password: password,
      }, // 发送到服务器的数据
      success: function(datax) { // 成功回调函数
        
        if(datax == 1){
          logx.innerHTML = '<i class="fa fa-warning"></i>错误操作！';
          logx.style.display = 'block';
          logbut.value="登录";
          logbut.style="pointer-events: auto;opacity:1;";
         }else if(datax == 2){
          logx.innerHTML = '<i class="fa fa-warning"></i>账号或密码格式不正确';
          logx.style.display = 'block';
          logbut.value="登录";
          logbut.style="pointer-events: auto;opacity:1;";
         }else if(datax == 3){
          logx.innerHTML = '<i class="fa fa-warning"></i>账号或密码错误';
          logx.style.display = 'block';
          logbut.value="登录";
          logbut.style="pointer-events: auto;opacity:1;";
         }else if(datax == 4){
          logx.innerHTML = '<i class="fa fa-warning"></i>该账号已被禁用';
          logx.style.display = 'block';
          logbut.value="登录";
          logbut.style="pointer-events: auto;opacity:1;";
         }else if(datax == 9){
          logx.innerHTML = '<i class="fa fa-warning"></i>您的IP地址已被封禁';
          logx.style.display = 'block';
          logbut.value="登录";
          logbut.style="pointer-events: auto;opacity:1;";
         }else if(datax == 200){
          zc.classList.remove('loganimeout');
          zc.classList.remove('loganime');
          dl.classList.remove('loganimeout');
          dl.classList.remove('loganime');
          logx.innerHTML = '<i class="fa fa-check"></i>登录成功！';
          logx.style.display = 'block';
          logx.style.color = '#fff';
          logx.style.background = '#65dd8a';
          setTimeout(function() {  
          location.reload(true);//刷新当前页面
        }, 1000);
         }else{
          logx.innerHTML = '<i class="fa fa-warning""></i>登录发生未知错误';
            logx.style.display = 'block';
              logbut.value="程序发生错误";
              logbut.style="pointer-events:none;opacity:0.5;";
              console.log(datax); 
         }


      }

    })

  }, 1000);
  
  }); 
