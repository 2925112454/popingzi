document.addEventListener('DOMContentLoaded', function() {
  const submit = document.getElementById("post_btn");//提交按钮
  const dwbtn = document.getElementById("dwbtn");//下载信息配置弹窗按钮
  const dwform = document.getElementById("rowdowdialog");//下载信息配置dialog
  const dwcancel = document.getElementById("rowdowclose");//关闭dialog按钮
  const tougaotips = document.getElementById('tougaotips');//提示框
  const tipsbtn=document.getElementById('tipsbtn');//提示框按钮
  const tipsclose = document.getElementById('tipsclose');
  const body = document.body;
  
  if(post_max_size){
    post_max_size = parseInt(post_max_size) * 1024;//KB转转换为 Bytes
  }else{
    post_max_size=0;
  }
  if(upimgoff){
    upimgoff=upimgoff;
  }else{
    upimgoff="off";
  }

  if(tipsbtn&&tougaotips){
    tipsbtn.addEventListener("click", function() {
      tougaotips.showModal();
      tougaotips.style.display = "flex";
      if(body){
        body.style.overflow = "hidden";
      }
    });
    tipsclose.addEventListener("click", function() {
      tougaotips.close();
      tougaotips.style.display = "";
      if(body){
        body.style.overflow = "";
      }
    });
  }

  let errorTimeout = null;

  // 图片上传处理器 - 用于TinyMCE
  const example_image_upload_handler = (blobInfo, progress) => new Promise((resolve, reject) => {
    if(upimgoff=="on"){
        const fileSize = blobInfo.blob().size;
        if((fileSize > post_max_size)&&post_max_size > 0){
          let new_num_kb= post_max_size / 1024 / 1024;
          let mub_kb = 'MB';
          if(new_num_kb<1){
            new_num_kb = post_max_size / 1024;
            mub_kb = 'KB';
          }
          reject("图片不能超过"+parseFloat(new_num_kb.toFixed(2))+mub_kb);
          return;
        }
        if(fileSize < 37){
          reject("图片类型不正确！");
          return;
        }
        const file = blobInfo.blob();
        const fileName = blobInfo.filename();
        const fileType = file.type;
        
        // 对于不需要转换的格式，直接上传
        if (fileType === 'image/webp' || fileType === 'image/avif' || fileType === 'image/gif') {
            uploadFile(file, fileName, progress, resolve, reject);
            return;
        }
        
        // 对于需要转换的格式，先转换为WebP
        convertToWebP(file).then(webpFile => {
            uploadFile(webpFile, changeExtensionToWebP(fileName), progress, resolve, reject);
        }).catch(error => {
            reject(`图片转换失败: ${error.message}`);
        });
    }else{
      reject("上传功能未开启！");
      return;
    }
  });

  // 转换图片为WebP格式
  function convertToWebP(file) {
      return new Promise((resolve, reject) => {
          const reader = new FileReader();
          
          reader.onload = function(event) {
              const img = new Image();
              
              img.onload = function() {
                  const canvas = document.createElement('canvas');
                  const ctx = canvas.getContext('2d');
                  
                  canvas.width = img.width;
                  canvas.height = img.height;
                  
                  // 绘制图片到Canvas
                  ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                  
                  // 检查是否为PNG并且包含透明像素
                  const isPNG = file.type === 'image/png';
                  const hasTransparency = isPNG ? checkTransparency(ctx, canvas.width, canvas.height) : false;
                  
                  // 根据是否有透明通道选择质量
                  const quality = hasTransparency ? 0.9 : 0.8;
                  
                  // 将Canvas内容转换为WebP格式
                  canvas.toBlob(
                      function(blob) {
                          if (!blob) {
                              reject(new Error('无法创建WebP格式的图片'));
                              return;
                          }
                          
                          const webpFile = new File([blob], 'converted.webp', { type: 'image/webp' });
                          resolve(webpFile);
                      },
                      'image/webp',
                      quality
                  );
              };
              
              img.onerror = function() {
                  reject(new Error('图片加载失败'));
              };
              
              img.src = event.target.result;
          };
          
          reader.onerror = function() {
              reject(new Error('读取文件失败'));
          };
          
          reader.readAsDataURL(file);
      });
  }
  
  // 检查图片是否包含透明像素
  function checkTransparency(ctx, width, height) {
      const imageData = ctx.getImageData(0, 0, width, height);
      const data = imageData.data;
      
      // 只检查前1000个像素，提高性能
      const pixelsToCheck = Math.min(data.length / 4, 1000);
      
      for (let i = 0; i < pixelsToCheck; i++) {
          // 每个像素由RGBA四个值表示，A是透明度(0-255)
          if (data[i * 4 + 3] < 255) {
              return true; // 发现透明像素
          }
      }
      
      return false; // 未发现透明像素
  }
  
  // 上传文件 - 用于TinyMCE

    function uploadFile(file, fileName, progress, resolve, reject) {
        const xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open('POST', '/api/tougaoimg.php');
        
        xhr.upload.onprogress = (e) => {
            progress(e.loaded / e.total * 100);
        };
        
        xhr.onload = () => {
            if (xhr.status === 403) {
                reject({ message: 'HTTP Error: ' + xhr.status, remove: true });//错误
                return;
            }
            
            if (xhr.status < 200 || xhr.status >= 300) {
                reject('HTTP Error: ' + xhr.status);//错误
                return;
            }
            
            const json = JSON.parse(xhr.responseText);
            if (!json || typeof json.location != 'string') {
                reject(json.error);
                return;
            }
            resolve(json.location);
        };
        
        xhr.onerror = () => {
            reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
        };
        
        const formData = new FormData();
        formData.append('image', file, fileName);
        xhr.send(formData);
    }


  
  // 更改文件扩展名为.webp
  function changeExtensionToWebP(fileName) {
      const lastDotIndex = fileName.lastIndexOf('.');
      return lastDotIndex === -1 ? fileName + '.webp' : fileName.substring(0, lastDotIndex) + '.webp';
  }

tinymce.init({
  selector: "#post_text",
  plugins: "autolink link emoticons image fullscreen quickbars",
  toolbar: "styleselect |fontsize forecolor backcolor italic underline numlist bullist align bold link emoticons|image custommedia|removeformat fullscreen",
  language: "zh_CN",
  language_url: "/style/tinymce/langs/zh_CN.js",
  height: 400,
  menubar: false,
  branding: false,
  statusbar: false,
  toolbar_mode: "floating",
  toolbar_sticky: false,
  preview_styles: false,
  skin: "oxide",
  elementpath: false,
  promotion: false,
  quickbars_insert_toolbar: false,
  link_default_target: "_blank",
  quickbars_selection_toolbar: 'bold italic underline link h2 h3 h4 blockquote',
  placeholder: "请输入正文内容……",
  images_file_types: 'jpg,webp,gif,png,jpeg,avif',
  file_picker_types: 'image',
  images_upload_url: '/api/tougaoimg.php',
  image_caption: false,
  images_upload_handler: example_image_upload_handler,
  content_css: '/style/css/tinymce.css',
  license_key: 'gpl',
  media_alt_source: false, 
  media_poster: false,
  // 加载HLS.js库
  setup: function(editor) {
      // 定义允许的标签列表
      const allowedTags = ['p', 'br', 'strong', 'em', 'u', 's', 'ol', 'ul', 'li', 'a', 'img', 'h2', 'h3', 'h4', 'blockquote', 'span', 'video', 'source'];
      // 注册自定义SVG图标
      editor.ui.registry.addIcon('custom-video', '<svg width="24" height="24" focusable="false"><path d="M4 3h16c.6 0 1 .4 1 1v16c0 .6-.4 1-1 1H4a1 1 0 0 1-1-1V4c0-.6.4-1 1-1Zm1 2v14h14V5H5Zm4.8 2.6 5.6 4a.5.5 0 0 1 0 .8l-5.6 4A.5.5 0 0 1 9 16V8a.5.5 0 0 1 .8-.4Z" fill-rule="nonzero"></path></svg>');
      // 加载HLS.js库
      const script = document.createElement('script');
      script.src = '/style/videojs/hls.js';
      script.onerror = function() {
          console.error('HLS.js 加载失败！');
      };
      document.head.appendChild(script);
      
      // 创建自定义媒体按钮
      editor.ui.registry.addButton('custommedia', {
          icon: 'custom-video',
          tooltip: '插入媒体',
          onAction: function() {
              editor.windowManager.open({
                  title: '插入媒体',
                  body: {
                      type: 'panel',
                      items: [
                          {
                              type: 'input',
                              name: 'videoUrl',
                              label: '媒体URL (支持m3u8/mp4/webm/ogg/mp3/aac/m4a格式)'
                          }
                      ]
                  },
                  buttons: [
                      {
                          text: '取消',
                          type: 'cancel'
                      },
                      {
                          text: '插入',
                          type: 'submit',
                          primary: true
                      }
                  ],
                  onSubmit: function(api) {
                      const data = api.getData();
                      const videoUrl = data.videoUrl.trim();
                      
                      if (videoUrl) {
                          let embedHtml = '';
                          if (videoUrl.toLowerCase().endsWith('.m3u8')) {
                              // 创建HLS视频播放器
                              embedHtml = `
                                      <video class="tinymce-hls-video" width="100%" height="auto" controls>
                                          <source src="${videoUrl}" type="application/x-mpegURL">
                                          您的浏览器不支持HLS视频播放
                                      </video>
                              `;
                          } else {
                            function getFileExtension(url) {
                                try {
                                    const parsedUrl = new URL(url);
                                    const pathname = parsedUrl.pathname;
                                    const lastSegment = pathname.split('/').pop();
                                    const lastDotIndex = lastSegment.lastIndexOf('.');
                                    
                                    if (lastDotIndex === -1) {
                                        return '';
                                    }
                                    
                                    return lastSegment.slice(lastDotIndex + 1);
                                } catch (error) {
                                    // 处理无效 URL 的情况
                                    const lastSlashIndex = url.lastIndexOf('/');
                                    const filename = lastSlashIndex !== -1 ? url.slice(lastSlashIndex + 1) : url;
                                    const lastDotIndex = filename.lastIndexOf('.');
                                    
                                    return lastDotIndex === -1 ? '' : filename.slice(lastDotIndex + 1);
                                }
                            }
                            const getMimeType = (ext) => {
                                const mimeTypeMap = {
                                  'mp4': 'video/mp4',
                                  'webm': 'video/webm',
                                  'ogg': 'video/ogg',
                                  'm4a': 'audio/mp4', 
                                  'aac': 'audio/aac', 
                                  'mp3': 'audio/mpeg' 
                                };
                                return mimeTypeMap[ext] || '';
                              };
                            const mimeType = getMimeType(getFileExtension(videoUrl));
                              //mp4或者mp3使用video
                              if (videoUrl.endsWith('.mp4') || videoUrl.endsWith('.webm')||videoUrl.endsWith('.ogg')) {
                                  embedHtml = `
                                  <video width="100%" height="auto" controls>
                                      <source src="${videoUrl}" type="${mimeType}">
                                  </video>
                              `;
                              }else if(videoUrl.endsWith('.mp3')||videoUrl.endsWith('.m4a')||videoUrl.endsWith('.aac')){
                                  embedHtml = `
                                    <audio src="${videoUrl}" controls="controls"></audio>
                                  `;
                              } else {
                                embedHtml = `
                                  <video width="100%" height="auto" controls>
                                      <source src="${videoUrl}" type="video/mp4">
                                      <source src="${videoUrl}" type="application/x-mpegURL">
                                      不支持此类型的视频播放
                                  </video>
                              `;
                              }
                          }
                          
                          // 插入内容到编辑器
                          editor.insertContent(embedHtml);
                          
                          // 初始化HLS播放器
                          setTimeout(function() {
                              initializeHlsPlayers();
                          }, 100);
                          
                          api.close();
                      }
                  }
              });
          }
      });
      
      // 初始化HLS播放器
      function initializeHlsPlayers() {
          if (typeof Hls !== 'undefined') {
              const hlsVideos = editor.getDoc().querySelectorAll('.tinymce-hls-video');
              hlsVideos.forEach(function(video) {
                  if (!video.hlsInitialized) {
                      video.hlsInitialized = true;
                      const hls = new Hls();
                      hls.loadSource(video.querySelector('source').src);
                      hls.attachMedia(video);
                  }
              });
          } else {
              console.warn('HLS.js not loaded yet');
          }
      }
      
      // 监听内容变化事件
      editor.on('change', function() {
          checkForbiddenTags(editor);
      });
      
      // 监听表单提交事件
      editor.on('submit', function() {
          if (!checkForbiddenTags(editor, true)) {
              return false;
          }
      });
      
      // 获取提交按钮并绑定事件
      const submitButton = document.querySelector('#submit');
      if (submitButton) {
          submitButton.addEventListener("click", function() {
              if (!checkForbiddenTags(editor, true)) {
                  alert('<font>(｡ŏ_ŏ)</font> 兄弟，你越界了！');
                  return false;
              }
          });
      }
      
      // 检查是否存在禁止的标签
      function checkForbiddenTags(editor, showAlert = false) {
          const content = editor.getContent();
          const doc = new DOMParser().parseFromString(content, 'text/html');
          const allElements = doc.body.querySelectorAll('*');
          
          let forbiddenTags = [];
          let hasForbiddenTags = false;
          
          // 遍历所有元素，检查是否存在不在允许列表中的标签
          allElements.forEach(element => {
              const tagName = element.tagName.toLowerCase();
              if (!allowedTags.includes(tagName)) {
                  if (!forbiddenTags.includes(tagName)) {
                      forbiddenTags.push(tagName);
                  }
                  hasForbiddenTags = true;
              }
          });
          
          // 如果有禁止的标签
          if (hasForbiddenTags) {
              if (showAlert) {
                  alert('<font>(｡ŏ_ŏ)</font> 兄弟，你越界了！');
                  return false;
              }
          }
          
          return !hasForbiddenTags;
      }
  }
});

  if(dwbtn&&dwform&&dwcancel){
    dwbtn.addEventListener("click", function() {
      dwform.showModal();
      dwform.style.display = "flex";
      if(body){
        body.style.overflow = "hidden";
      }
    });
    dwcancel.addEventListener("click", function() {
      rowdowclose();
    });
  }
  
  function getContent() {
      return tinymce.get('post_text').getContent();
  }
  
  function isValidNonNegativeInteger(value) {
      if (value === '' || value === null || value === undefined) {
          return false;
      }
      const num = Number(value);
      return !isNaN(num) && 
             isFinite(num) && 
             Number.isInteger(num) && 
             num >= 0;
  }
  
  const post_title=document.getElementById("post_title");//标题框
  const cat=document.getElementById("cat");//一级列表
  const catfl=document.getElementById("catfl");//二级分类
  const post_img = document.getElementById("post_img");//封面框
  const imgbtn = document.getElementById("imgbtn");//封面上传按钮
  const post_dw_jf = document.getElementById("post_dw_jf");//下载积分
  const rowdowname = document.getElementById("rowdowname");//网盘名称
  const rowdowpx = document.getElementById("rowdowpx");//分辨率
  const rowdowurl = document.getElementById("rowdowurl");//下载地址
  const rowdowpas = document.getElementById("rowdowpas");//提取码
  const rowdowmun = document.getElementById("rowdowmun");//文件数量
  const rowdowsize = document.getElementById("rowdowsize");//文件大小
  const rowdowzip = document.getElementById("rowdowzip");//解压密码
  const rowdowcloseyes = document.getElementById("rowdowcloseyes");//确定按钮
  const allnull = document.getElementById("allnull");//一键清空
  function compressText(text) {
    if (!text || typeof text !== 'string') {
        return '';
    }
    const withoutLineBreaks = text.replace(/[\r\n]+/g, ' ');
    const withoutExtraSpaces = withoutLineBreaks.replace(/\s+/g, ' ');
    return withoutExtraSpaces.trim();
}

  if(submit&&post_title&&cat&&catfl&&post_img&&post_dw_jf&&rowdowname&&rowdowpx&&rowdowurl&&rowdowpas&&rowdowmun&&rowdowsize&&rowdowzip&&rowdowcloseyes&&allnull){
      /* 提交 */
      submit.addEventListener("click", function() {
        const new_Content = compressText(getContent().trim());//内容
        const new_Title = post_title.value.trim();//标题
        const new_Cat = cat.value.trim();//列表id
        const new_Catfl = catfl.value.trim();//分类id
        const new_post_img = post_img.value.trim();
        const new_post_dw_jf = post_dw_jf.value.trim();
        const new_rowdowname = rowdowname.value.trim();
        const new_rowdowpx = rowdowpx.value.trim();
        const new_rowdowurl = rowdowurl.value.trim();
        const new_rowdowpas = rowdowpas.value.trim();
        const new_rowdowmun = rowdowmun.value.trim();
        const new_rowdowsize = rowdowsize.value.trim();
        const new_rowdowzip = rowdowzip.value.trim();

        if(!new_Title){
          alert("<font>(｡ŏ_ŏ)</font> 标题不能为空！");
          return;
        }
        if(!new_Content){
          alert("<font>(｡ŏ_ŏ)</font> 正文不能为空！");
          return;
        }
        if(!new_Cat||!new_Catfl||new_Catfl<0||new_Cat<0||!isValidNonNegativeInteger(new_Cat)||!isValidNonNegativeInteger(new_Catfl)){
          alert("<font>(｡ŏ_ŏ)</font> 分类参数不正确！");
          return;
        }
        if(new_post_img){
          //判断图片地址是否是http或者https或者//或者/开头,不是则不通过
          if (!new_post_img.startsWith('http://') && !new_post_img.startsWith('https://') && !new_post_img.startsWith('//') && !new_post_img.startsWith('/')) {
            alert("<font>(｡ŏ_ŏ)</font> 封面地址不正确！");
            return;
          }
          if(new_post_img.startsWith('/')){
            if (!new_post_img.startsWith('/upload/')) {
              alert("<font>(｡ŏ_ŏ)</font> 封面地址不正确！");
              return;
            }
            if (!new_post_img.endsWith('.jpg') && !new_post_img.endsWith('.jpeg') && !new_post_img.endsWith('.png') && !new_post_img.endsWith('.gif') && !new_post_img.endsWith('.webp') && !new_post_img.endsWith('.avif')) {
              alert("<font>(｡ŏ_ŏ)</font> 封面格式不正确！");
              return;
            }
          }
        }
        if (!new_post_dw_jf||new_post_dw_jf<0||new_post_dw_jf>999999999||!isValidNonNegativeInteger(new_post_dw_jf)) {
          alert("<font>(｡ŏ_ŏ)</font> 积分参数错误！");
          return;
        }
        if(new_rowdowname||new_rowdowpx||new_rowdowurl||new_rowdowpas||new_rowdowmun||new_rowdowsize||new_rowdowzip){
          if(!new_rowdowname||!new_rowdowurl||!new_rowdowmun||!new_rowdowsize){
            alert("<font>(｡ŏ_ŏ)</font> 下载信息不完整！");
            return;
          }
          //判断地址
          if (!new_rowdowurl.startsWith("https://") && 
                !new_rowdowurl.startsWith("http://") && 
                !new_rowdowurl.startsWith("//")) {
              alert("<font>(｡ŏ_ŏ)</font> 下载信息地址错误！");
              return;
          }
        }
        function resetSubmitBtn() {
            submit.style.pointerEvents = "auto";
            submit.style.opacity = 1;
            submit.textContent = "发布";
        }
        submit.style.pointerEvents="none";
        submit.style.opacity=0.5;
        submit.textContent="正在提交...";
        $.ajax({
                      url: '/api/post.php', // 请求地址
                      type: 'POST',   // 请求类型
                      dataType: 'json',
                      data: {
                        text: new_Content,//内容
                        title: new_Title,//标题
                        link:new_Cat,//列表
                        fl:new_Catfl,//分类
                        img:new_post_img,//封面
                        jf:new_post_dw_jf,//积分
                        name:new_rowdowname,//网盘名称
                        px:new_rowdowpx,//分辨率
                        url:new_rowdowurl,//网盘地址
                        pass:new_rowdowpas,//提取码
                        number:new_rowdowmun,//数量
                        size:new_rowdowsize,//体积大小
                        zip:new_rowdowzip,//解压密码
                      },
                        success: function(post) { // 成功回调函数
                          if(post.code == 200){
                              window.location.href = "user.php?type=3";
                          }else if(post.code == 500){
                              alert("<font>(｡ŏ_ŏ)</font> "+post.msg);
                              resetSubmitBtn();
                          }else{
                              alert("<font>(｡ŏ_ŏ)</font> 服务器错误！"); 
                              resetSubmitBtn();
                          }
                        }
                
                    })
      });
      
      /*清空下载信息*/
      allnull.addEventListener("click", function() {
        rowdowname.value = "";
        rowdowpx.value = "";
        rowdowurl.value = "";
        rowdowpas.value = "";
        rowdowmun.value = "";
        rowdowsize.value = "";
        rowdowzip.value = "";
      });

      rowdowcloseyes.addEventListener("click", function() {
            rowdowclose();
      });
      
      function rowdowclose() {
        const formDataxx = {
            name: rowdowname.value.trim(),
            url: rowdowurl.value.trim(),
            mun: rowdowmun.value.trim(),
            zip: rowdowzip.value.trim(),
            px: rowdowpx.value.trim(),
            pas: rowdowpas.value.trim(),
            size: rowdowsize.value.trim()
          };
          
          const rowdowcloseerr = document.getElementById('rowdowcloseerr');
           // 检查是否全为空
          const isAllEmpty = Object.values(formDataxx).every(value => !value);
          
          // 检查必填字段
          if (!isAllEmpty) {
            const requiredFields = ['name', 'url', 'mun', 'size'];
            if (requiredFields.some(field => !formDataxx[field])) {
              showError("必填项不能为空");
              return;
            }
            // 验证URL格式
            if (!formDataxx.url.startsWith("https://") && 
                !formDataxx.url.startsWith("http://") && 
                !formDataxx.url.startsWith("//")) {
              showError("下载地址不正确");
              return;
            }
          }
          // 关闭表单
          dwform.close();
          dwform.style.display = "";
          if(body) body.style.overflow = "";
          // 错误提示函数
           function showError(message) {
            // 清除上一次的定时器
            if (errorTimeout) {
              clearTimeout(errorTimeout);
            }
            rowdowcloseerr.innerHTML = message;
            rowdowcloseerr.style.display = "block";
            // 设置新的定时器
            errorTimeout = setTimeout(() => {
              rowdowcloseerr.innerHTML = "";
              rowdowcloseerr.style.display = "none";
            }, 2500);
          }
      }
      
      /*上传封面*/
      const imgfile = document.getElementById("imgfile");
      if (imgfile) {
        imgbtn.addEventListener("click", function () {
          imgfile.click(); // 触发文件选择
        });
        
        imgfile.addEventListener("change", async function () {
          if (imgfile.files.length > 0) {
            //判断文件大小是否超过限制
            let new_num_mb= post_max_size / 1024 / 1024;
            let mub_mb = 'MB';
            if(new_num_mb<1){
              new_num_mb = post_max_size / 1024;
              mub_mb = 'KB';
            }
            if ((imgfile.files[0].size > post_max_size)&&post_max_size!=0) {
              alert("<font>(｡ŏ_ŏ)</font> 图片不能超过" + parseFloat(new_num_mb.toFixed(2)) +mub_mb);
              return;
            }
            if (imgfile.files[0].size < 37) {
              alert("<font>(｡ŏ_ŏ)</font> 图片类型不正确！");
              return;
            }
            if (imgfile.files[0].type.indexOf("image") == -1) {
              alert("<font>(｡ŏ_ŏ)</font> 图片类型不正确！");
              return;
            }
            if (
              imgfile.files[0].type.indexOf("image/jpeg") == -1 &&
              imgfile.files[0].type.indexOf("image/png") == -1 &&
              imgfile.files[0].type.indexOf("image/gif") == -1 &&
              imgfile.files[0].type.indexOf("image/webp") == -1 &&
              imgfile.files[0].type.indexOf("image/avif") == -1
            ) {
              alert("<font>(｡ŏ_ŏ)</font> 图片类型不正确！");
              return;
            }
            
            // 检查是否需要转换为WebP
            const file = imgfile.files[0];
            const fileType = file.type;
            
            // 对于不需要转换的格式，直接上传
            if (fileType === 'image/webp' || fileType === 'image/avif' || fileType === 'image/gif') {
              uploadImage(file);
              return;
            }
            
            // 对于需要转换的格式，先转换为WebP
            try {
              const webpFile = await convertToWebP(file);
              uploadImage(webpFile);
            } catch (error) {
              alert(`图片转换失败: ${error.message}`);
            }
          }
        });
      }
  }

  // 统一的上传函数 - 用于封面上传
  async function uploadImage(file) {
    let formData = new FormData();
    let fileToUpload;

    // 判断是Base64还是File对象
    if (typeof file === 'string' && file.startsWith('data:')) {
      // 处理Base64编码的图片
      const response = await fetch(file);
      const blob = await response.blob();
      fileToUpload = new File([blob], 'image.webp', { type: blob.type });
    } else if (file instanceof File) {
      // 处理File对象
      fileToUpload = file;
    } else {
      throw new Error('不支持的文件类型');
    }

    // 添加文件到FormData
    formData.append('image', fileToUpload);
    
    try {
      const response = await fetch('/api/tougaoimg.php', {
        method: 'POST',
        body: formData,
      });
      
      if (response.ok) {
        const responseData = await response.json();
        if (responseData.code == 200) {
            post_img.value = responseData.location;
        }else if(responseData.code == 500){
          alert('<font>(｡ŏ_ŏ)</font> ' + responseData.error.message);
        } else {
          return null;
        }
      }    
    } catch (error) {
      console.error('上传过程中出错:', error);
      throw error;
    }
  }
});