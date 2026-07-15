  const example_image_upload_handler = (blobInfo, progress) => new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();
    xhr.withCredentials = false;
    xhr.open('POST', '/api/upimg.php');
  
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
    formData.append('file', blobInfo.blob(), blobInfo.filename());
    xhr.send(formData);
  });


  tinymce.init({
    selector: "#rowtext",
    external_plugins: {
      'batchUploadImage': `/style/tinymce/plugins/batchUploadImage/plugin.min.js`,
      },
      plugins: "accordion advlist anchor autolink autosave batchUploadImage charmap code codesample directionality emoticons fullscreen help image importcss insertdatetime link lists media nonbreaking pagebreak preview quickbars save searchreplace table visualblocks visualchars wordcount linkcopy",
      toolbar: "styleselect |fontsize forecolor backcolor bold italic underline numlist bullist align link linkcopy anchor emoticons|image batchUploadImage media|fullscreen",
      language: "zh_CN",
      language_url: "/style/tinymce/langs/zh_CN.js",
      height: 500,//高度
      menubar: true,//菜单栏
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
      quickbars_selection_toolbar: 'bold italic underline link h2 h3 h4 blockquote',//内容快速操作配置，加粗、斜体、下划线、h1、h2、h3、引用
      placeholder: "请在这里输入内容",//占位符
      images_file_types: 'jpg,webp,gif,png,jpeg,avif',//图片上传类型
      file_picker_types: 'image',//上传类型
      images_upload_url: '/api/upimg.php',//上传地址
      image_caption: false,//图片标题
      images_upload_handler:example_image_upload_handler,//上传处理
      content_css:'/style/css/tinymce.css',
      license_key: 'gpl',
  });
