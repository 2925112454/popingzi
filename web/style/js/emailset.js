document.addEventListener('DOMContentLoaded', function() {
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function isValidHtml(htmlString) {
    const allowedTags = ['a', 'p', 'b', 'img', 'br', 'span', 'h1', 'h2', 'h3', 'h4', 'h5'];
    const regex = /<([a-z]+)(?:\s[^>]*)?>/gi;
    let match;
    let isValid = true;
    while ((match = regex.exec(htmlString)) !== null) {
        const tagName = match[1].toLowerCase();
        if (!allowedTags.includes(tagName)) {
        isValid = false;
        break;
        }
    }
    return isValid;
    }

function isValidCustomFormat(str) {
  if (str.length < 3) return false;
  if (str[0] !== '【' || str[str.length - 1] !== '】') return false;
  if (str.indexOf('【', 1) !== -1 || str.lastIndexOf('】', str.length - 2) !== -1) {
    return false;
  }
  const content = str.substring(1, str.length - 1);
  return /^[a-zA-Z0-9\u4e00-\u9fa5]+$/.test(content);
}

function isValidPhone(phone) {
    const regex = /^1[3-9]\d{9}$/;
    return regex.test(phone);
}

  function containsHtmlTags(str) {
    const htmlTagRegex = /<(?!\s*(?:area|base|br|col|embed|hr|img|input|keygen|link|meta|param|source|track|wbr)[^>]*\/?\s*>)[^>]+>/i;
    return htmlTagRegex.test(str);
  }
    const emailform = document.getElementById('emailform');//邮箱配置表单
    const emailsubmit = document.getElementById('smtpbtn');//提交按钮
    const smtpces = document.getElementById('smtpces');//测试按钮

    const telform = document.getElementById('telform');//短信配置表单
    const telsubmit = document.getElementById('telbtn');//提交按钮
    const telces = document.getElementById('telces');//测试按钮
    const telyue = document.getElementById('telyue');//短信宝余额查询

    if (emailform&&emailsubmit&&smtpces&&telform&&telsubmit&&telces&&telyue) {
        emailsubmit.addEventListener('click', function(e) {
            e.preventDefault();
            const emailsmtp = emailform.smtp.value;//smtp服务器地址
            const emailport = emailform.smtpport.value;//端口
            const emailuser = emailform.smtpemailname.value;//用户名
            const emailpass = emailform.smtpemailpass.value;//密码
            const smtpemail = emailform.smtpemail.value;//邮箱
            const smtpname = emailform.smtpname.value;//发件人名称
            const smtpdiyhed = emailform.smtpdiyhed.value;//前缀
            const smtpdiy = emailform.smtpdiy.value;//后缀
            // 判断是否所有配置项都为空
            const allEmpty = !emailsmtp && !emailport && !emailuser && !emailpass && !smtpemail && !smtpname && !smtpdiyhed && !smtpdiy;

            if(allEmpty || (emailsmtp && emailport && emailuser && emailpass && smtpemail)){
                //判断邮箱地址
                if(isValidEmail(smtpemail)||allEmpty){
                    if(isValidHtml(smtpdiy)&&isValidHtml(smtpdiyhed)){
                                            $.ajax({
                                                        url: '/api/smtp.php', // 请求地址
                                                        type: 'POST',   // 请求类型
                                                        data: {
                                                            smtp: emailsmtp,//smtp
                                                            port: emailport,//端口
                                                            user: emailuser,//用户名
                                                            pass: emailpass,//密码
                                                            email: smtpemail,//邮箱
                                                            name: smtpname,//发件人名称
                                                            diyhed: smtpdiyhed,//前缀
                                                            diytail: smtpdiy,//后缀
                                                          },
                                                                    success: function(stmp) { // 成功回调函数
                                                                      if(stmp == 500){
                                                                        alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                                    
                                                                     }else if(stmp == 200){
                                                                        alert("<font>(◕ܫ◕)</font> 保存成功！");
                                                                      }else if(stmp == 400){
                                                                        alert("<font>(｡ŏ_ŏ)</font> 发件人邮箱不正确！");
                                                                      }else if (stmp == 404){
                                                                        alert("<font>(｡ŏ_ŏ)</font> 必填项不能为空！");
                                                                      }else if (stmp == 600){
                                                                        alert("<font>(｡ŏ_ŏ)</font> 保存失败！");
                                                                      }else{
                                                                        alert("<font>(｡ŏ_ŏ)</font> 服务器错误！");   
                                                                        console.log(stmp);
                                                                    }
                                                              
                                                              
                                                                    }
                                                  
                                                      })
                    }else{
                        alert("<font>(｡ŏ_ŏ)</font> 仅支持a、p、b、img、br、span、h1、h2、h3、h4、h5标签！");
                    }
                }else{
                    alert("<font>(｡ŏ_ŏ)</font> 发件人邮箱不正确！");
                }

            }else{
                alert("<font>(｡ŏ_ŏ)</font> 必填项不能为空！");
            }

        });
        smtpces.addEventListener('click', function(e) {
            e.preventDefault();
            const useremail = prompt("请输入接收邮箱：");
            if (useremail) {
            const emailsmtp = emailform.smtp.value;//smtp服务器地址
            const emailport = emailform.smtpport.value;//端口
            const emailuser = emailform.smtpemailname.value;//用户名
            const emailpass = emailform.smtpemailpass.value;//密码
            const smtpemail = emailform.smtpemail.value;//邮箱
            const smtpname = emailform.smtpname.value;//发件人名称
            const smtpdiyhed = emailform.smtpdiyhed.value;//前缀
            const smtpdiy = emailform.smtpdiy.value;//后缀
            if(emailsmtp && emailport && emailuser && emailpass && smtpemail){
                //判断邮箱地址
                if(isValidEmail(smtpemail)&&isValidEmail(useremail)){
                    if(isValidHtml(smtpdiy)&&isValidHtml(smtpdiyhed)){
                                            $.ajax({
                                                        url: '/api/smtpcs.php', // 请求地址
                                                        type: 'POST',   // 请求类型
                                                        data: {
                                                            smtp: emailsmtp,//smtp
                                                            port: emailport,//端口
                                                            user: emailuser,//用户名
                                                            pass: emailpass,//密码
                                                            email: smtpemail,//邮箱
                                                            name: smtpname,//发件人名称
                                                            diyhed: smtpdiyhed,//前缀
                                                            diytail: smtpdiy,//后缀
                                                            useremail:useremail,//收件人邮箱
                                                          },
                                                                    success: function(stmpcs) { // 成功回调函数
                                                                      if(stmpcs == 500){
                                                                        alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                                    
                                                                     }else if(stmpcs == 200){
                                                                        alert("<font>(◕ܫ◕)</font> 发送成功！");
                                                                      }else if(stmpcs == 400){
                                                                        alert("<font>(｡ŏ_ŏ)</font> 邮箱格式不正确！");
                                                                      }else if (stmpcs == 404){
                                                                        alert("<font>(｡ŏ_ŏ)</font> 必填项不能为空！");
                                                                      }else if (stmpcs == 600){
                                                                        alert("<font>(｡ŏ_ŏ)</font> 发送失败！");
                                                                      }else{
                                                                        alert("<font>(｡ŏ_ŏ)</font> 服务器错误！");   
                                                                        console.log(stmpcs);
                                                                    }
                                                              
                                                              
                                                                    }
                                                  
                                                      })
                    }else{
                        alert("<font>(｡ŏ_ŏ)</font> 仅支持a、p、b、img、br、span、h1、h2、h3、h4、h5标签！");
                    }
                }else{
                    alert("<font>(｡ŏ_ŏ)</font> 邮箱格式不正确！");
                }

            }else{
                alert("<font>(｡ŏ_ŏ)</font> 必填项不能为空！");
            }


            }

        });
        telsubmit.addEventListener('click', function(e) {
            e.preventDefault();
            const teluser = telform.teluser.value;//用户名
            const telkey = telform.telkey.value;//密钥
            const teldiy =  telform.teldiy.value;//短信签名
            const telbody = telform.telbody.value;//自定义后缀
            const allnull = !teluser && !telkey && !teldiy && !telbody;
            if(allnull||(teluser&&telkey&&teldiy)){

              if(allnull||isValidCustomFormat(teldiy)){

                if(!containsHtmlTags(teluser)&&!containsHtmlTags(teldiy)&&!containsHtmlTags(telbody)&&!containsHtmlTags(telkey)){
                  $.ajax({
                    url: '/api/telset.php', // 请求地址
                    type: 'POST',   // 请求类型
                    dataType: 'json',
                    data: {
                        tuser: teluser,//账号
                        key: telkey,//秘钥
                        diy: teldiy,//签名
                        body: telbody,//后缀
                      },
                                success: function(tel) { // 成功回调函数
                                  if(tel.err == 500){
                                    alert("<font>(｡ŏ_ŏ)</font> "+tel.msg+"");                                                                    
                                 }else if(tel.err == 200){
                                    alert("<font>(◕ܫ◕)</font> "+tel.msg+"");
                                  }else{
                                    alert("<font>(｡ŏ_ŏ)</font> 服务器错误！");   
                                    console.log(tel);
                                  }
                          
                          
                                }
              
                  })
                }else{
                  alert("<font>(｡ŏ_ŏ)</font> 短信不允许含有html标签！");
                }

              }else{
                alert("<font>(｡ŏ_ŏ)</font> 短信签名格式错误！");
              }

            }else{
                alert("<font>(｡ŏ_ŏ)</font> 必填项不能为空！");
            }

        });
        telces.addEventListener('click', function(e) {
            e.preventDefault();
            const teluser = telform.teluser.value;//用户名
            const telkey = telform.telkey.value;//密钥
            const teldiy =  telform.teldiy.value;//短信签名
            const telbody = telform.telbody.value;//自定义后缀
            const allnull = !teluser && !telkey && !teldiy && !telbody;
            const useretel = prompt("请输入接收手机号，您会收到一个验证码为666888的短信：");
            if(allnull||(teluser&&telkey&&teldiy)){

              if(isValidCustomFormat(teldiy)){

                if(!containsHtmlTags(teluser)&&!containsHtmlTags(teldiy)&&!containsHtmlTags(telbody)&&!containsHtmlTags(telkey)){
                  if(useretel){
                    //判断手机号是否正确
                      if(isValidPhone(useretel)){
                                                        $.ajax({
                                                        url: '/api/telcsx.php', // 请求地址
                                                        type: 'POST',   // 请求类型
                                                        dataType: 'json',
                                                        data: {
                                                            tuser: teluser,//账号
                                                            key: telkey,//秘钥
                                                            tel: useretel,//手机号
                                                            diy: teldiy,//签名
                                                            body: telbody,//后缀
                                                          },
                                                                    success: function(telcsx) { // 成功回调函数
                                                                      if(telcsx.err == 500){
                                                                        alert("<font>(｡ŏ_ŏ)</font> "+telcsx.msg+"");                                                                    
                                                                     }else if(telcsx.err == 200){
                                                                        alert("<font>(◕ܫ◕)</font> "+telcsx.msg+"");
                                                                      }else{
                                                                        alert("<font>(｡ŏ_ŏ)</font> 服务器错误！");   
                                                                        console.log(telcsx);
                                                                      }
                                                              
                                                              
                                                                    }
                                                  
                                                      })
                      }else{
                        alert("<font>(｡ŏ_ŏ)</font> 手机号格式错误！");
                      }
                  }
                }else{
                  alert("<font>(｡ŏ_ŏ)</font> 短信不允许含有html标签！");
                }

              }else{
                alert("<font>(｡ŏ_ŏ)</font> 短信签名格式错误！");
              }

            }else{
                alert("<font>(｡ŏ_ŏ)</font> 必填项不能为空！");
            }
        });
        telyue.addEventListener('click', async function(e) {
    e.preventDefault();
    const teluser = telform.teluser.value; // 用户名
    const telkey = telform.telkey.value; // 密钥
    
    if(teluser && telkey) {
        if(!containsHtmlTags(teluser) && !containsHtmlTags(telkey)) {
                                                        $.ajax({
                                                        url: '/api/telcs.php', // 请求地址
                                                        type: 'POST',   // 请求类型
                                                        dataType: 'json',
                                                        data: {
                                                            tuser: teluser,//账号
                                                            key: telkey,//秘钥
                                                          },
                                                                    success: function(telcs) { // 成功回调函数
                                                                      if(telcs.err == 500){
                                                                        alert("<font>(｡ŏ_ŏ)</font> "+telcs.msg+"");                                                                    
                                                                     }else if(telcs.err == 200){
                                                                        alert("<font>(◕ܫ◕)</font> "+telcs.msg+"");
                                                                      }else{
                                                                        alert("<font>(｡ŏ_ŏ)</font> 服务器错误！");   
                                                                        console.log(telcs);
                                                                      }
                                                              
                                                              
                                                                    }
                                                  
                                                      })
        } else {
            alert('(｡ŏ_ŏ) 账号或密码不允许含有HTML标签！');
        }
    } else {
        alert('(｡ŏ_ŏ) 账号和密码不能为空！');
    }
  });
    }



    /* 实时更新输入框内容 */
    const telsetdiy= document.getElementById('telsetdiy');//更新签名位置
    const telsetbody= document.getElementById('telsetbody');//更新后缀位置
    const teldiyinput = document.querySelector('input[name="teldiy"]');
    const telbodyinput = document.querySelector('input[name="telbody"]');
    if(teldiyinput && telbodyinput && telsetdiy && telsetbody) {
      teldiyinput.addEventListener('input', function() {
        telsetdiy.textContent = teldiyinput.value;
      });
      telbodyinput.addEventListener('input', function() {
        telsetbody.textContent = telbodyinput.value;
      });
    }


});