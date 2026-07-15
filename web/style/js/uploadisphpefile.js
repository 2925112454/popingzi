document.addEventListener('DOMContentLoaded', function() {
  
    const uploadisphpefile = document.getElementById('uploadisphpefile');//查询按钮
    const uploadisphpetextfile=  document.getElementById('uploadisphpetextfile');//查询结果展示区域
    
    if(uploadisphpefile&&uploadisphpetextfile){
        uploadisphpefile.style.display="revert";
            //点击按钮
            uploadisphpefile.addEventListener('click',function(e){
                e.preventDefault();
                uploadisphpetextfile.innerHTML = '正在扫描网站目录，此过程比较耗时，请耐心等待...';
                $.ajax({
                    url: '/api/uploadisphpefile.php', // 请求地址
                    dataType:'json',//json
                    
                               success: function(phpiss) { // 成功回调函数
                                    if(phpiss.code == 2){
                                        uploadisphpetextfile.innerHTML = '<span class="text-red"><i class="fa fa-times" aria-hidden="true"></i>'+phpiss.message+'</span>';
                                    }else if(phpiss.code == 3){
                                        const messarr = Object.values(phpiss.data.sample_files);
                                        // 处理中文编码（decodeURIComponent）
                                        const decodedFiles = messarr.map(file => decodeURIComponent(file.replace(/\\/g, '/')));
                                        // 转为<li>标签
                                         const liTags = decodedFiles.map(file => '<li class="errli">'+file+'</li>').join('');
                                        uploadisphpetextfile.innerHTML = '<span class="text-red"><i class="fa fa-times" aria-hidden="true"></i>'+phpiss.message+'：<br/><ol>'+liTags+'</ol></span>';
                                    }else if(phpiss.code == 1){
                                        uploadisphpetextfile.innerHTML = '<span class="text-green"><i class="fa fa-check" aria-hidden="true"></i>'+phpiss.message+'</span>';
                                    }else{
                                        uploadisphpetextfile.innerHTML = '查询失败！服务器返回错误状态。';
                                    }
                                }
                })
            });
    }
});