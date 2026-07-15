  
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
if(upimgoff=="on"){
  tinymce.init({
    selector: "#sub_textarea",
      plugins: "autolink link emoticons quickbars image fullscreen",
      toolbar: "styleselect |fontsize forecolor backcolor italic underline numlist bullist align bold link emoticons removeformat image|fullscreen",
      language: "zh_CN",
      language_url: "/style/tinymce/langs/zh_CN.js",
      height: 300,//高度
      menubar: false,//菜单栏
      branding: false,//底部版权
      statusbar: false,//状态栏
      toolbar_mode: "floating",//工具栏
      toolbar_sticky: false,//粘性工具栏
      preview_styles: false,//预览样式
      skin: "oxide",//主题
      elementpath: false,//路径栏
      promotion: false,//广告
      quickbars_insert_toolbar:false,//内容快速插入配置
      link_default_target: "_blank",
      quickbars_selection_toolbar: 'bold italic underline link h2 h3 h4 blockquote',
      placeholder: "请输入内容……",//占位符
      images_file_types: 'jpg,webp,gif,png,jpeg,avif',
      file_picker_types: 'image',
      images_upload_url: '/api/tougaoimg.php',
      image_caption: false,
      images_upload_handler: example_image_upload_handler,
      content_css:'/style/css/tinymce.css',
      license_key: 'gpl',
  });
}else{
  tinymce.init({
    selector: "#sub_textarea",
      plugins: "autolink link emoticons quickbars fullscreen image",
      toolbar: "styleselect |fontsize forecolor backcolor italic underline numlist bullist align bold link emoticons removeformat image|fullscreen",
      language: "zh_CN",
      language_url: "/style/tinymce/langs/zh_CN.js",
      height: 300,//高度
      menubar: false,//菜单栏
      branding: false,//底部版权
      statusbar: false,//状态栏
      toolbar_mode: "floating",//工具栏
      toolbar_sticky: false,//粘性工具栏
      preview_styles: false,//预览样式
      skin: "oxide",//主题
      elementpath: false,//路径栏
      promotion: false,//广告
      quickbars_insert_toolbar:false,//内容快速插入配置
      link_default_target: "_blank",
      quickbars_selection_toolbar: 'bold italic underline link h2 h3 h4 blockquote',
      placeholder: "请输入内容……",//占位符
      content_css:'/style/css/tinymce.css',
      license_key: 'gpl',
  });
}