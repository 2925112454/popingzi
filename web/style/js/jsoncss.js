document.addEventListener('DOMContentLoaded', function() {//监听DOM加载完成


  function removeSpacesAndNewLines(kongge) {  
    return kongge.replace(/[\s\r\n]+/g, '');  
} 
    const jsonTextarea = document.getElementById('indexcarouseljson');
    const jsoncss=document.getElementById('jsoncss');//点击按钮

jsoncss.addEventListener('click', function() {
      // 初始的JSON字符串
      const initialJson = '[{"img": "图片地址1","url": "链接地址1","title": "标题1","desc": "描述1"},{"img": "图片地址2","url": "链接地址2","title": "标题2","desc": "描述2"},{"img": "图片地址3","url": "链接地址3","title": "标题3","desc": "描述3"},{"img": "图片地址4","url": "链接地址4","title": "标题4","desc": "描述4"},{"img": "图片地址5","url": "链接地址5","title": "标题5","desc": "描述5"}]';
      // 格式化JSON字符串的简单函数
  function formatJson(jsonString) {
    try {
      const obj = JSON.parse(jsonString);
      return JSON.stringify(obj, null, 2); // 使用空格进行缩进
    } catch (e) {
      console.error("Invalid JSON", e);
      return jsonString;
    }
  }
    // 设置格式化后的JSON到textarea
  jsonTextarea.value = formatJson(initialJson);
    });
      // 如果需要用户输入后自动格式化，可以监听textarea的input事件
  jsonTextarea.addEventListener('input', function() {
    try {
      const userInput = this.value;
      const formattedJson = formatJson(userInput);
      this.value = formattedJson;
    } catch (e) {
      // 处理格式化失败的情况
    }
  });
  const carousela=document.getElementById('carousela');//设置轮播按钮
  const bigcarousela=document.getElementById('bigcarousela');//设置轮播按钮2
  const indexcarousel=document.getElementById('indexcarousel');//设置轮播弹出层
  const closeindexcarousel=document.getElementById('closeindexcarousel');//关闭设置轮播弹出层按钮
  const body = document.querySelector('body');
  const upcarousel=document.getElementById('upcarousel');//确认提交按钮

  carousela.addEventListener('click', function() {
    indexcarousel.style.display = 'flex';
    indexcarousel.style.width = '100%';
    indexcarousel.style.height = '100%';
    body.style.overflow = 'hidden';
  });

  bigcarousela.addEventListener('click', function() {
    indexcarousel.style.display = 'flex';
    indexcarousel.style.width = '100%';
    indexcarousel.style.height = '100%';
    body.style.overflow = 'hidden';
  });

  closeindexcarousel.addEventListener('click', function() {
    indexcarousel.style.display = 'none';
    indexcarousel.style.width = '0';
    indexcarousel.style.height = '0';
    body.style.overflow = 'auto';
  });

  upcarousel.addEventListener('click', function() {
    const indexjsonTextarea = document.getElementById('indexcarouseljson');//Textarea输入框
    const selectindex=document.getElementById('indexcarouselmode');//select模式选择
    const upcarouseldiv=document.getElementById('upcarouseldiv');//提示框DIV
    const upcarouselspan=document.getElementById('upcarouselspan');//提示框span
    const indexjson = indexjsonTextarea.value;//获取输入框的值
    const selectindexvalue=selectindex.value; //获取select的值
    upcarouselspan.innerHTML='';
    upcarouseldiv.classList.remove('up');

    if (indexjson!=='' && indexjson!==null && indexjson!==undefined) {
            let resultkongge = removeSpacesAndNewLines(indexjson);//去除输入框的值的空格及回车
            //判断输入框的值是否是JSON
            function isValidJsonFormat(jsonString) {  
              try {  
                // 尝试解析JSON字符串  
                const jsonData = JSON.parse(jsonString);  
              
                // 检查jsonData是否是一个数组  
                if (!Array.isArray(jsonData)) {  
                  return false;  
                }  
              
                // 遍历数组中的每个元素  
                for (const item of jsonData) {  
                  // 检查每个元素是否是一个对象，并且具有img和url属性  
                  if (typeof item !== 'object' || item === null ||   
                      typeof item.img !== 'string' ||   
                      typeof item.url !== 'string') {  
                    return false;  
                  }  
                }  
              
                // 如果所有检查都通过，返回true  
                return true;  
              } catch (e) {  
                // 如果解析JSON时抛出异常，说明JSON格式不正确  
                return false;  
              }  
            }
          if (!isValidJsonFormat(resultkongge)) {
            upcarouselspan.innerHTML='<i class="fa fa-times"></i>输入的JSON格式不正确！';
            setTimeout(function() {
              upcarouselspan.innerHTML='';
              upcarouseldiv.classList.remove('up');
            }, 2000);
            return;
          }
    }

    if (selectindexvalue=='1' || selectindexvalue=='2' || selectindexvalue=='3' || selectindexvalue=='4' || selectindexvalue=='5' || selectindexvalue=='6'){
                    //Ajax提交表单
                    $.ajax({
                      url: '/inc/carouseljson.php', // 请求地址
                      type: 'POST',   // 请求类型
                      data: {
                          json:indexjson,
                          mode:selectindexvalue
                      },
                                  success: function(jon) { // 成功回调函数
                                  if(jon == 500){
                                    upcarouselspan.innerHTML='<i class="fa fa-times"></i>错误操作！';
                                    upcarouseldiv.classList.remove('up');
                                    setTimeout(function() {
                                      upcarouselspan.innerHTML='';
                                      upcarouseldiv.classList.remove('up');
                                    }, 2000);                                                                  
                                   }else if(jon == 200){
                                          upcarouselspan.innerHTML='<i class="fa fa-check"></i>设置已保存！';
                                          upcarouseldiv.classList.add('up');
                                          setTimeout(function() {
                                            upcarouselspan.innerHTML='';
                                            upcarouseldiv.classList.remove('up');
                                          }, 2000);
                                          localStorage.removeItem('carouselData');
                                   }else if(jon == 404){
                                    upcarouselspan.innerHTML='<i class="fa fa-times"></i>输入的JSON格式不正确！';
                                    upcarouseldiv.classList.remove('up');
                                    setTimeout(function() {
                                      upcarouselspan.innerHTML='';
                                      upcarouseldiv.classList.remove('up');
                                    }, 2000);
                                   }else{
                                    upcarouselspan.innerHTML='<i class="fa fa-times"></i>服务器出错！';
                                    upcarouseldiv.classList.remove('up');
                                    setTimeout(function() {
                                      upcarouselspan.innerHTML='';
                                      upcarouseldiv.classList.remove('up');
                                    }, 2000); 
                                    console.log(jon);
                                   }
                                  }
                
                    });
    }else{
      upcarouselspan.innerHTML='<i class="fa fa-times"></i>错误操作！';
      setTimeout(function() {
        upcarouselspan.innerHTML='';
        upcarouseldiv.classList.remove('up');
      }, 2000);
    }
  });

/*白天模式*/
  const daydiya=document.getElementById('daydiya');//白天模式的CSS编辑按钮
  const diydaycss=document.getElementById('diydaycss');//白天模式的弹出层
  daydiya.addEventListener('click',function(){
    diydaycss.style.display='flex';
    diydaycss.style.width='100%';
    diydaycss.style.height='100%';
    body.style.overflow = 'hidden';
    const daycss=document.getElementById('daycss');//白天模式的CSS示例按钮
    const diydaycsscss=document.getElementById('diydaycsscss');//白天模式的CSS输入框
    const closediydaycss=document.getElementById('closediydaycss');//关闭按钮
    const updiydaycss=document.getElementById('updiydaycss');//提交按钮
    const updiydaycssdiv=document.getElementById('updiydaycssdiv');//错误提示框DIV
    const updiydaycssspan=document.getElementById('updiydaycssspan');//错误提示框SPAN
    //点击示例按钮，将CSS示例代码写入输入框
    daycss.addEventListener('click',function(){
      diydaycsscss.value=
      '--bg-color: #f9f9f9;/* 背景颜色 */\n'+
      '--heder-color: #ffffff;/* 页眉主题颜色 */\n'+
      '--footer-bg-color: #232323;/* 页脚背景颜色 */\n'+
      '--footer-font-color: rgb(169 169 169);/* 页脚字体颜色 */\n'+
      '--footer-h-color: #FF9800;/* 页脚链接颜色 */\n'+
      '--link-a-color: #ffffff;/* 页脚链接颜色 */\n'+
      '--link-a-hover: #ffffff;/* 页脚链接鼠标移入颜色 */\n'+
      '--font-color: #333333;/* 字体颜色 */\n'+
      '--font-s-color: #666666;/* 字体辅助颜色 */\n'+
      '--hover-color: #FF9800;/* 鼠标移入颜色 */\n'+
      '--home-hover-color: #ff0000;/* 页眉首页按钮，鼠标移入时的下边框颜色 */\n'+
      '--border-color: #f1f1f1;/* 边框颜色 */\n'+
      '--input-color: #f9f9f9;/* 输入框颜色 */\n'+
      '--input-border-radius: 4px;/* 保持不变的输入框圆角 */\n'+
      '--alert-font-color: #ffffff;/* alert警告框字体颜色 */\n'+
      '--alert-bg-color: #2b2b2b;/* alert警告框背景颜色 */\n'+
      '--alert-button-color: #FF9800;/* alert警告框按钮颜色 */\n'+
      '--alert-button-font-color: #333333;/* alert警告框按钮字体颜色 */\n'+
      '--button-color: #efefef;/* 按钮颜色 */\n'+
      '--button-font-color: #8b8b8b;/* 按钮字体颜色 */\n'+
      '--menu-border-color: #ffffff;/* 页眉除首页按钮外的下边框颜色 */\n'+
      '--menu-color: rgba(0, 0, 0, 0.8);/* 页眉字体颜色 */\n'+
      '--box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.1);/* 盒子阴影 */\n'+
      '--rightlogin-bgcolor: linear-gradient(0deg, #ffffff, #f9f9f9, #ffd79b);/* 右侧登录背景颜色 */\n'+
      '--rightlogin-fontcolor: #ffffff;/* 右侧登录字体颜色 */\n'+
      '--dows-fontcolor: #333333;/* 封面下载标识字体颜色 */\n'+
      '--page-font-color: #ffffff;/* 翻页可点击状态的字体颜色 */\n'+
      '--login-bg-color: #ffffff;/* 登录背景颜色 */\n'+
      '--login-input-bg: #ffffff;/* 登录输入框背景颜色 */\n'+
      '--login-bg-img: #FF9800;/* 设置颜色时，登录背景不显示，仅限作用排行榜标题背景，设置图片则都作用*/\n'+
      '--login-bg-filter: none;/* 登录背景滤镜 */\n'+
      '--x-bg-color: #f9f9f9;/* 登录错误背景颜色 */\n'+
      '--x-font-color: #FF5722;/* 登录错误字体颜色 */\n'+
      '--backtotop-bg: #2f2f2f;/* 返回顶部按钮背景颜色 */\n'+
      '--deta-title-bg: #232323;/* 鼠标移入,弹出提示框背景颜色;即deta-title */\n'+
      '--info-bg-color: linear-gradient(343deg, #ffffff, #ffffff);/* 详情页背景颜色 */\n'+
      '--info-border-color: #e0e0e0;/* 详情页边框颜色 */\n'+
      '--xx-color: rgba(156, 156, 156, 0.8);/* 关闭按钮颜色(登录框右上角X) */\n'+
      '--vip-color: linear-gradient(343deg, #FF5722, #FF9800);/* VIP颜色 */\n'+
      '--text-shadow: none;/* 排行榜文字阴影 */\n'+
      '--all-center-color: #333333;/* 后台付费会员及空间容量百分比文字颜色 */\n'+
      '--emoji-image-url: url(/style/emoji/emoji.png);/* 评论框表情按钮图标,不影响美观,可不改! */\n'+
      '--placeholder-color: #cdcdcd;/*输入框默认提示文字颜色*/\n'+
      '--dropdown-bg-color:#fff;/*排序下拉菜单背景色*/\n'+
      '--h-font-color: #47474;/*h1-h6标题字体颜色*/\n'+
      '--h-bg-color: #f5f5f5;/*h1-h6标题背景颜色*/\n'+
      '--dow-bg-color: transparent;/*内容页下载框下的版权声明背景颜色*/\n'+
      '--dow-font-color: #666666;/*内容页下载框下的版权声明文字颜色*/\n\n'+
      '/*这个是 <亮黄色主题> 示例，请修改后提交；当然，不修改也可直接使用！*/';
;
    })

    closediydaycss.addEventListener('click',function(){
      diydaycss.style.display='none';
      diydaycss.style.width='0';
      diydaycss.style.height='0';
      body.style.overflow = 'auto';
    })

    updiydaycss.addEventListener('click',function(){
      const newdaycss=diydaycsscss.value;
      if (newdaycss!==''&&newdaycss!==null&&newdaycss!==undefined){
            function isPossibleCssVariableName(varcss) {  
              return varcss.startsWith('--');  
            }
            //去除空格及回车换行
            const xnewdaycss=newdaycss.replace(/\s/g,'').replace(/\n/g,'').replace(/\r/g,'');
            if (!isPossibleCssVariableName(xnewdaycss)){
              updiydaycssspan.innerHTML='<i class="fa fa-times"></i>CSS变量格式不正确！';
              updiydaycssdiv.classList.remove('up');
              setTimeout(function() {
                updiydaycssspan.innerHTML='';
                updiydaycssdiv.classList.remove('up');
              }, 2000); 
              return;   
            }

      };

                          //Ajax提交表单
                          $.ajax({
                            url: '/inc/daycss.php', // 请求地址
                            type: 'POST',   // 请求类型
                            data: {
                                daycss:newdaycss,
                            },
                                        success: function(dc) { // 成功回调函数
                                        if(dc == 500){
                                          updiydaycssspan.innerHTML='<i class="fa fa-times"></i>错误操作！';
                                          updiydaycssdiv.classList.remove('up');
                                          setTimeout(function() {
                                            updiydaycssspan.innerHTML='';
                                            updiydaycssdiv.classList.remove('up');
                                          }, 2000);                                                                  
                                         }else if(dc == 200){
                                          updiydaycssspan.innerHTML='<i class="fa fa-check"></i>设置已保存！';
                                          updiydaycssdiv.classList.add('up');
                                                setTimeout(function() {
                                                  updiydaycssspan.innerHTML='';
                                                  updiydaycssdiv.classList.remove('up');
                                                }, 2000);
                                         }else if(dc == 404){
                                          updiydaycssspan.innerHTML='<i class="fa fa-times"></i>CSS变量格式不正确！';
                                          updiydaycssdiv.classList.remove('up');
                                          setTimeout(function() {
                                            updiydaycssspan.innerHTML='';
                                            updiydaycssdiv.classList.remove('up');
                                          }, 2000);
                                         }else{
                                          updiydaycssspan.innerHTML='<i class="fa fa-times"></i>服务器出错！';
                                          updiydaycssdiv.classList.remove('up');
                                          setTimeout(function() {
                                            updiydaycssspan.innerHTML='';
                                            updiydaycssdiv.classList.remove('up');
                                          }, 2000); 
                                          console.log(dc);
                                         }
                                        }
                      
                          });

    })


    

  })
/*黑夜模式*/
const nightdiya=document.getElementById('nighta');//黑夜模式的CSS编辑按钮
  const diynightcss=document.getElementById('diynightcss');//黑夜模式的弹出层
  nightdiya.addEventListener('click',function(){
    diynightcss.style.display='flex';
    diynightcss.style.width='100%';
    diynightcss.style.height='100%';
    body.style.overflow = 'hidden';
    const nightcss=document.getElementById('nightcss');//黑夜模式的CSS示例按钮
    const diynightcsscss=document.getElementById('diynightcsscss');//黑夜模式的CSS输入框
    const closediynightcss=document.getElementById('closediynightcss');//关闭按钮
    const updiynightcss=document.getElementById('updiynightcss');//提交按钮
    const updiynightcssdiv=document.getElementById('updiynightcssdiv');//错误提示框DIV
    const updiynightcssspan=document.getElementById('updiynightcssspan');//错误提示框SPAN
    //点击示例按钮，将CSS示例代码写入输入框
    nightcss.addEventListener('click',function(){
      diynightcsscss.value=
'--bg-color: #2b3b50; /* 背景颜色 */\n'+
'--heder-color: #35455b; /* 页眉主题颜色 */\n'+
'--footer-bg-color: #35455b; /* 页脚背景颜色 */\n'+
'--footer-font-color: rgb(94 126 169); /* 页脚字体颜色 */\n'+
'--footer-h-color: #ff8a00; /* 页脚链接颜色 */\n'+
'--link-a-color: #ffffff; /* 页脚链接颜色 */\n'+
'--link-a-hover: #ffffff; /* 页脚链接鼠标移入颜色 */\n'+
'--font-color: #ffffff; /* 字体颜色 */\n'+
'--font-s-color: #b3b3b3; /* 字体辅助颜色 */\n'+
'--hover-color: #ff8a00; /* 鼠标移入颜色 */\n'+
'--home-hover-color: #f44336; /* 页眉首页按钮，鼠标移入时的下边框颜色 */\n'+
'--border-color: #3b4d66; /* 边框颜色 */\n'+
'--input-color: #2b3b50; /* 输入框颜色 */\n'+
'--input-border-radius: 4px; /* 保持不变的输入框圆角 */\n'+
'--alert-font-color: #ffffff; /* alert警告框字体颜色 */\n'+
'--alert-bg-color: #35455b; /* alert警告框背景颜色 */\n'+
'--alert-button-color: #ff8a00; /* alert警告框按钮颜色 */\n'+
'--alert-button-font-color: #ffffff; /* alert警告框按钮字体颜色 */\n'+
'--button-color: #4c5f79; /* 按钮颜色 */\n'+
'--button-font-color: #bac6d5; /* 按钮字体颜色 */\n'+
'--menu-border-color:#35455b; /* 页眉除首页按钮外的下边框颜色 */\n'+
'--menu-color: rgba(255, 255, 255, 0.8); /* 页眉字体颜色 */\n'+
'--box-shadow: 0px 0px 2px rgba(100, 112, 139, 0.1); /* 盒子阴影 */\n'+
'--rightlogin-bgcolor: linear-gradient(0deg, #35455b, #3b4d66, #465b7d); /* 右侧登录背景颜色 */\n'+
'--rightlogin-fontcolor: #ffffff; /* 右侧登录字体颜色 */\n'+
'--dows-fontcolor: #ffffff; /* 封面下载标识字体颜色 */\n'+
'--page-font-color: #ffffff; /* 翻页可点击状态的字体颜色 */\n'+
'--login-bg-color: #2b3b50; /* 登录背景颜色 */\n'+
'--login-input-bg: #2b3b50; /* 登录输入框背景颜色 */\n'+
'--login-bg-img: #3f5069; /* 设置颜色时，登录背景不显示，仅限作用排行榜标题背景，设置图片则都作用*/\n'+
'--login-bg-filter: none; /* 登录背景滤镜 */\n'+
'--x-bg-color: rgb(17 25 37); /* 登录错误背景颜色 */\n'+
'--x-font-color: #ff5722; /* 登录错误字体颜色 */\n'+
'--backtotop-bg: #17212f; /* 返回顶部按钮背景颜色 */\n'+
'--deta-title-bg: #1b293d; /* 鼠标移入,弹出提示框背景颜色;即deta-title */\n'+
'--info-bg-color: linear-gradient(343deg, #2b3b50, #111f32); /* 详情页背景颜色 */\n'+
'--info-border-color: #3b4d66; /* 详情页边框颜色 */\n'+
'--xx-color: rgba(255, 255, 255, 0.8); /* 关闭按钮颜色(登录框右上角X) */\n'+
'--vip-color: linear-gradient(343deg, #ff8a00, #ffad5c); /* VIP颜色 */\n'+
'--text-shadow:none; /* 排行榜文字阴影 */\n'+
'--all-center-color: #fff; /* 后台付费会员及空间容量百分比文字颜色 */\n'+
'--emoji-image-url: url(/style/emoji/emoji.png); /* 评论框表情按钮图标,不影响美观,可不改! */\n'+
'--placeholder-color: #999999; /* 输入框默认提示文字颜色 */\n'+
'--dropdown-bg-color:#2b3b50;/*排序下拉菜单背景色*/\n'+
'--h-font-color: #ffffff;/*h1-h6标题字体颜色*/\n'+
'--h-bg-color: #2f3d51;/*h1-h6标题背景颜色*/\n'+
'--dow-bg-color: transparent;/*内容页下载框下的版权声明背景颜色*/\n'+
'--dow-font-color: #8f9eb3;/*内容页下载框下的版权声明文字颜色*/\n\n'+
'/*这个是 <暗黄色主题> 示例，请修改后提交；当然，不修改也可直接使用！*/';
    })

    closediynightcss.addEventListener('click',function(){
      diynightcss.style.display='none';
      diynightcss.style.width='0';
      diynightcss.style.height='0';
      body.style.overflow = 'auto';
    })

    updiynightcss.addEventListener('click',function(){
      const newnightcss=diynightcsscss.value;
      if (newnightcss!==''&&newnightcss!==null&&newnightcss!==undefined){
            function isPossibleCssVariableName(varncss) {  
              return varncss.startsWith('--');  
            }
            //去除空格及回车换行
            const xnewnightcss=newnightcss.replace(/\s/g,'').replace(/\n/g,'').replace(/\r/g,'');
            if (!isPossibleCssVariableName(xnewnightcss)){
              updiynightcssspan.innerHTML='<i class="fa fa-times"></i>CSS变量格式不正确！';
              updiynightcssdiv.classList.remove('up');
              setTimeout(function() {
                updiynightcssspan.innerHTML='';
                updiynightcssdiv.classList.remove('up');
              }, 2000); 
              return;   
            }

      };

           //Ajax提交表单
           $.ajax({
            url: '/inc/nightcss.php', // 请求地址
            type: 'POST',   // 请求类型
            data: {
                nightcss:newnightcss,
            },
                        success: function(nc) { // 成功回调函数
                        if(nc == 500){
                          updiynightcssspan.innerHTML='<i class="fa fa-times"></i>错误操作！';
                          updiynightcssdiv.classList.remove('up');
                          setTimeout(function() {
                            updiynightcssspan.innerHTML='';
                            updiynightcssdiv.classList.remove('up');
                          }, 2000);                                                                  
                         }else if(nc == 200){
                          updiynightcssspan.innerHTML='<i class="fa fa-check"></i>设置已保存！';
                          updiynightcssdiv.classList.add('up');
                                setTimeout(function() {
                                  updiynightcssspan.innerHTML='';
                                  updiynightcssdiv.classList.remove('up');
                                }, 2000);
                         }else if(nc == 404){
                          updiynightcssspan.innerHTML='<i class="fa fa-times"></i>CSS变量格式不正确！';
                          updiynightcssdiv.classList.remove('up');
                          setTimeout(function() {
                            updiynightcssspan.innerHTML='';
                            updiynightcssdiv.classList.remove('up');
                          }, 2000);
                         }else{
                          updiynightcssspan.innerHTML='<i class="fa fa-times"></i>服务器出错！';
                          updiynightcssdiv.classList.remove('up');
                          setTimeout(function() {
                            updiynightcssspan.innerHTML='';
                            updiynightcssdiv.classList.remove('up');
                          }, 2000); 
                          console.log(nc);
                         }
                        }
      
          });

    })
  })
//模式选择
const diysafetybtn=document.getElementById('diysafetybtn');//确认按钮

diysafetybtn.addEventListener('click',function(e){
  e.preventDefault();
  function indexRadioValue() {  
    const indexdiy= document.querySelectorAll('.radio-indexdiy input[type="radio"][name="indexdiy"]');//获取首页版面下的所有单选项
    // 遍历这些radio元素，找到被选中的那个  
    for (var i = 0; i < indexdiy.length; i++) {  
      if (indexdiy[i].checked) {  
        return indexdiy[i].value;  
      }  
    }  
   return null;
  } 

  function dayRadioValue() {  
    const daydiy= document.querySelectorAll('.radio-indexdiy input[type="radio"][name="daydiy"]');//获取白天模式下的所有单选项
    // 遍历这些radio元素，找到被选中的那个  
    for (var i = 0; i < daydiy.length; i++) {  
      if (daydiy[i].checked) {  
        return daydiy[i].value;  
      }  
    }  
   return null;
  }

  function nightRadioValue() {  
    const nightdiy= document.querySelectorAll('.radio-indexdiy input[type="radio"][name="nightdiy"]');//获取夜间模式下的所有单选项
    // 遍历这些radio元素，找到被选中的那个  
    for (var i = 0; i < nightdiy.length; i++) {  
      if (nightdiy[i].checked) {  
        return nightdiy[i].value;  
      }  
    }  
   return null;
  }

  function isBetweenOneAndThree(num) {  
    // 创建一个包含1、2、3的数组  
    const validNumbers = [1, 2, 3];  
    // 使用includes方法检查num是否在数组中  
    return validNumbers.includes(num);  
} 

const indexselectedValue = Number(indexRadioValue());
const nightselectedValue = Number(nightRadioValue());
const dayselectedValue = Number(dayRadioValue());

if (isBetweenOneAndThree(indexselectedValue)&&isBetweenOneAndThree(dayselectedValue)&&isBetweenOneAndThree(nightselectedValue)){

             //Ajax提交表单
             $.ajax({
              url: '/inc/styleset.php', // 请求地址
              type: 'POST',   // 请求类型
              data: {
                  index:indexselectedValue,
                  day:dayselectedValue,
                  night:nightselectedValue,
              },
                          success: function(style) { // 成功回调函数
                          if(style == 500){
                            alert('<font>(｡ŏ_ŏ)</font> 错误操作！');                                                         
                           }else if(style == 200){
                            alert("<font>(◕ܫ◕)</font> 修改成功！");
                           }else{
                            alert('<font>(｡ŏ_ŏ)</font> 服务器错误！');
                            console.log(style);
                           }
                          }
        
            });


}else{
  alert('<font>(｡ŏ_ŏ)</font> 错误操作！');
}

});



  });