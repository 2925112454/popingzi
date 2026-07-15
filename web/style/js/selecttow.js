document.addEventListener('DOMContentLoaded', function() {
  //点击上传按钮
var newworduploadimg = document.getElementById('newworduploadimg');//上传图片按钮
if(newworduploadimg){

    document.querySelector('.custom-file-upload button').addEventListener('click', function() {
        document.querySelector('#fileUpload').click();
      });
    
      const fileInput = document.getElementById('fileUpload');
      const fileInfo = document.getElementById('fileInfo');
      const fileTypeimg = document.getElementById('fileimgbox');
      const uploadOverlay = document.getElementById('uploadOverlay');
      const closeUploadOverlay = document.getElementById('closeUploadOverlay');
      const openUploadOverlay = document.getElementById('openUploadOverlay');
      const dragArea = document.getElementById('dragArea');
      const fileerr = document.getElementById('fileerr');
      const body = document.querySelector('body');
    
      const filesizekb = 1024; // 限制文件上传大小，单位kb
      const allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4','image/svg+xml','application/x-mpegURL','image/x-icon','video/mpeg']; // 允许上传的文件类型
      const filesizemb = (filesizekb / 1024); // 转换限制文件大小单位为MB
    let Typeup = 0;
      const topshowOverlay = () => {
        uploadOverlay.style.display = 'flex';
        fileInput.value = '';
        fileInfo.innerHTML = '请先点击上方选择要上传的文件';
        fileInfo.style.display = 'flex';
        fileerr.style.display = 'none';
        fileTypeimg.innerHTML = '<i class="fa fa-plus"></i>';
        body.style.overflow = 'hidden';
        Typeup = 1;
      };
      const hideOverlay = () => {
        uploadOverlay.style.display = 'none';
        fileInput.value = '';
        fileInfo.innerHTML = '请先点击上方选择要上传的文件';
        fileInfo.style.display = 'flex';
        fileerr.style.display = 'none';
        fileTypeimg.innerHTML = '<i class="fa fa-plus"></i>';
        body.style.overflow = 'auto';
      };
    
      const handleFilePreview = (latestFile) => {
        fileTypeimg.innerHTML = '';
        fileInfo.innerHTML = '';
         if (!latestFile) {
          fileInfo.innerHTML = '请先点击上方选择要上传的文件';
          fileInfo.style.display = 'flex';
          fileTypeimg.innerHTML = '<i class="fa fa-plus"></i>';
          body.style.overflow = 'hidden';
          fileerr.style.display = 'none';
          return;
        }
     
        const fileSize = latestFile.size;
        const fileType = latestFile.type;
        const filehz = latestFile.name.split('.').pop();
        const sizeInMB = (fileSize / (1024 * 1024)).toFixed(2);
        const mimeTypeMap = {
          // MIME类型到友好描述的映射
          'image/png': 'PNG图片',
          'image/jpeg': 'JPG图片',
          'image/gif': 'GIF图片',
          'image/bmp': 'BMP图片',
          'image/tiff': 'TIF图片',
          'image/x-icon': 'ICO图标',
          'image/svg+xml': 'SVG文件',
          'video/mp4': 'MP4视频',
          'video/webm': 'WebM视频',
          'video/ogg': 'Ogg视频',
          'video/quicktime': 'QuickTime视频',
          'video/3gpp': '3GPP视频',
          'video/3gp2': '3GP2视频',
          'video/avi': 'AVI视频',
          'video/flv': 'FLV视频',
          'video/mkv': 'MKV视频',
          'video/mpeg': 'MPEG视频',
          'video/x-matroska': 'Matroska视频',
          'video/x-ms-asf': 'ASF视频',
          'video/x-ms-wmv': 'WMV视频',
          'video/x-flv': 'FLV视频',
          'audio/mpeg': 'MP3音频',
          'audio/ogg': 'Ogg音频',
          'audio/wav': 'WAV音频',
          'audio/wave': 'WAVE音频',
          'audio/webm': 'WebM音频',
          'audio/x-ms-wma': 'WMA音频',
          'audio/aac': 'AAC音频',
          'audio/flac': 'FLAC音频',
          'audio/midi': 'MIDI音频',
          'audio/x-m4a': 'M4A音频',
          'application/x-rar-compressed': 'RAR压缩包',
          'application/x-zip-compressed': 'ZIP压缩包',
          'application/rar': 'RAR压缩包',
          'application/x-rar': 'RAR压缩包',
          'application/x-zip': 'ZIP压缩包',
          'application/zip': 'ZIP压缩包',
          'application/x-7z-compressed': '7Z压缩包',
          'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'XLSX表格',
          'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'DOCX文档',
          'application/vnd.openxmlformats-officedocument.presentationml.presentation': 'PPTX演示文稿',
          'application/pdf': 'PDF文档',
          'text/plain': '文本文件',
          'application/msword': 'DOC文档',
          'default':  filehz.toUpperCase() + '文件'
        };
     
    const friendlyFileType = mimeTypeMap[fileType] || mimeTypeMap['default'] || (filehz + wenjian);
    
        let filePreview;
        if (fileType.startsWith('image/') && !fileType.startsWith('image/tiff')) {
          filePreview = `<img src="${URL.createObjectURL(latestFile)}" alt="${latestFile.name}" />`;
          fileInfo.innerHTML = '<b>文件大小: </b>' + sizeInMB + ' MB <b>文件类型: </b>' + friendlyFileType + '';
        } else if (fileType.startsWith('video/')) {
          filePreview = `<video src="${URL.createObjectURL(latestFile)}" controls style="max-width: 100%; max-height: 100%;"></video>`;
          fileInfo.innerHTML = '<b>文件大小: </b>' + sizeInMB + ' MB <b>文件类型: </b>' + friendlyFileType + '';
        }else if (fileType.startsWith('audio/')) {
          filePreview = `<audio src="${URL.createObjectURL(latestFile)}" controls style="max-width: 100%; max-height: 100%;"></audio>`;
          fileInfo.innerHTML = '<b>文件大小: </b>' + sizeInMB + ' MB <b>文件类型: </b>' + friendlyFileType + '';
        }  else {
          const fileIcon = getFileIcon(fileType);
          filePreview = fileIcon ? `<div class="file-icon">${fileIcon}</div>` : '<div class="file-icon"><i class="fa fa-file"></i></div>';
          fileInfo.innerHTML = '<b>文件大小: </b>' + sizeInMB + ' MB <b>文件类型: </b>' + friendlyFileType + '';
        }
    
        fileTypeimg.innerHTML = filePreview;
        fileInfo.style.display = 'grid';
      };
    
      const getFileIcon = (fileType) => {
        switch (true) {
          case fileType === 'application/pdf':
            return '<i class="fa fa-file-pdf-o"></i>';
          case ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/msword'].includes(fileType):
            return '<i class="fa fa-file-word-o"></i>';
          case fileType === 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
            return '<i class="fa fa-file-powerpoint-o"></i>';
          case fileType === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
            return '<i class="fa fa-file-excel-o"></i>';
          case fileType === 'text/plain':
            return '<i class="fa fa-file-text-o"></i>';
          case ['application/x-sh', 'application/x-perl', 'application/x-python', 'application/x-ruby', 'application/x-shellscript', 'application/x-tcl', 'application/x-tex', 'application/x-texinfo', 'application/x-vhdl', 'application/x-verilog'].includes(fileType):
            return '<i class="fa fa-file-code-o"></i>';
          case ['application/x-php', 'text/javascript', 'application/x-asp', 'application/x-msdownload', 'application/msaccess', 'application/javascript', 'text/css', 'text/html', 'application/xml', 'application/xhtml+xml'].includes(fileType):
            return '<i class="fa fa-file-code-o"></i>';
          case ['application/zip', 'application/rar', 'application/7z', 'application/x-rar-compressed', 'application/x-zip-compressed', 'application/x-7z-compressed'].includes(fileType):
            return '<i class="fa fa-file-zip-o"></i>';
          case ['application/font-woff', 'application/font-woff2', 'application/font-sfnt', 'application/font-ttf', 'application/font-otf'].includes(fileType):
            return '<i class="fa fa-file-font-o"></i>';
          case ['audio/mpeg', 'audio/mp4', 'audio/ogg', 'audio/wav', 'audio/webm', 'audio/x-ms-wma', 'audio/x-ms-wax', 'audio/x-wav', 'audio/x-pn-wav'].includes(fileType):
            return null; // 音频文件直接显示播放器
          default:
            return null;
        }
      };
    
      let lastDroppedFile = null; // 用于保存最后拖拽的文件
    
      const handleDragOver = (e) => {
        e.preventDefault();
        e.stopPropagation();
        dragArea.classList.add('fileactive');
      };
    
      const handleDragLeave = (e) => {
        e.preventDefault();
        dragArea.classList.remove('fileactive');
      };
    
      const handleDrop = (e) => {// 拖拽文件
        e.preventDefault();
        e.stopPropagation();
        dragArea.classList.remove('fileactive');
        const files = e.dataTransfer.files;
        if (files && files.length > 0) {
          fileerr.style.display = 'none';
          lastDroppedFile = files[files.length - 1]; // 取最后一个文件作为最新的文件
          updateFileNameDisplay(); // 更新文件名显示
          handleFilePreview(lastDroppedFile); // 显示最新文件的预览信息
        }
      };
    
      const handleFileChange = () => {// 文件选择器选择文件时触发
        const files = fileInput.files;
        if (files && files.length > 0) {
          fileerr.style.display = 'none';
          lastDroppedFile = files[files.length - 1]; // 取最后一个文件作为最新的文件
          updateFileNameDisplay(); // 更新文件名显示
          handleFilePreview(lastDroppedFile); // 显示最新文件的预览信息
        }else{
          fileerr.style.display = 'none';
        }
      };
      const uploadButton = document.getElementById('newworduploadimg');
      const updateFileNameDisplay = () => {
          openUploadOverlay.removeEventListener('click', updateUploadOverlay); // 移除旧的监听器   
          if (lastDroppedFile) {  
              openUploadOverlay.addEventListener('click', updateUploadOverlay); // 添加新的监听器 
          } 
      };
      const updateUploadOverlay = function() {
      if(lastDroppedFile){
        if (Typeup === 1 || Typeup === 2 || Typeup === 3) {  
            if ((lastDroppedFile.size / (1024 * 1024)) > filesizemb) {  
                fileerr.innerHTML = "请上传小于" + filesizemb + "MB的文件";  
                fileerr.style.display = 'block'; 
            } else if (!allowedFileTypes.includes(lastDroppedFile.type)) {  
                fileerr.innerHTML = "该文件格式不允许上传";  
                fileerr.style.display = 'block'; 
            } else {  
            // 使用jQuery的$.ajax进行文件上传  
            var formData = new FormData();  
            formData.append('file', lastDroppedFile); // 添加文件数据
            formData.append('typeup', Typeup); // 添加typeup数据
    
            $.ajax({    
              url: '/inc/uprowimg.php',    
              type: 'POST',    
              data: formData, // 直接传递formData对象  
              contentType: false, // 不设置内容类型  
              processData: false, // 不处理发送的数据  
              dataType: 'json', // 指定返回的数据类型为JSON  
              success: function(data) { // data已经是解析后的对象  
                  try {    
                      if (data.code === 200) {
                      $('#rowimg').val(data.url);
                      fileInput.value = '';
                      lastDroppedFile = null;
                      updateFileNameDisplay();  
                      handleFilePreview(null);
                      hideOverlay();
                      } else if (data.code === 500) {    
                          showError('文件上传失败');
                      } else if (data.code === 0) {    
                          showError('该文件格式不允许上传');    
                      } else if (data.code === 1) {    
                          showError('请上传小于' + data.size + 'MB的文件');    
                      }    
                  } catch (e) {    
                      // 这里通常不会捕获到异常，因为jQuery已经处理了JSON解析  
                      console.error('处理服务器响应时发生错误:', e);  
                      showError('服务器响应错误');   
                      console.log(response);
                  }    
              },    
              error: function(jqXHR, textStatus, errorThrown) {    
                  // 请求失败时调用  
                  console.error('AJAX请求失败:', textStatus, errorThrown);  
                  showError('上传文件时发生错误');   
                  console.log(jqXHR.responseText); // 这里输出服务器返回的原始响应文本  
              }    
          });
    
            // 辅助函数，用于显示错误信息  
            function showError(message) {  
              fileerr.innerHTML = message;  
              fileerr.style.display = 'block';  
            }
            }  
        }
      }
    };
    
        uploadButton.addEventListener('click', topshowOverlay);
        closeUploadOverlay.addEventListener('click', hideOverlay);
        dragArea.addEventListener('dragover', handleDragOver);
        dragArea.addEventListener('dragleave', handleDragLeave);
        dragArea.addEventListener('drop', handleDrop);
        fileInput.addEventListener('change', handleFileChange);

}
})