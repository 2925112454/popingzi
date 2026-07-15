document.addEventListener('DOMContentLoaded', function() {
    const uploadisphpe = document.getElementById('uploadisphpe');//查询按钮
    const uploadisphpetext=  document.getElementById('uploadisphpetext');//查询结果展示区域
    
    if(uploadisphpe&&uploadisphpetext){

        uploadisphpe.style.display="revert";
            //点击按钮
            uploadisphpe.addEventListener('click',function(e){
                e.preventDefault();
                uploadisphpetext.innerHTML = '正在查询...';
                $.ajax({
                    url: '/api/uploadisphpe.php', // 请求地址
                                success: function(phpis) { // 成功回调函数
                                if(phpis == 1){
                                    uploadisphpetext.innerHTML = '<span class="text-red"><i class="fa fa-times" aria-hidden="true"></i>请关闭目录执行权限</span>';
                                }else if(phpis == 2){
                                    uploadisphpetext.innerHTML = '<span class="text-green"><i class="fa fa-check" aria-hidden="true"></i>已关闭目录执行权限</span>';
                                }else if(phpis == 500){
                                    alert("<font>(◕ܫ◕)</font> 错误操作！");
                                }else if(phpis == 404){
                                    alert("<font>(◕ܫ◕)</font> 上传目录不存在！");
                                }else if(phpis == 300){
                                    alert("<font>(◕ܫ◕)</font> 创建测试文件失败！");
                                }else if(phpis == 400){
                                    alert("<font>(◕ܫ◕)</font> Curl扩展不可用！");
                                }else{
                                    uploadisphpetext.innerHTML = '查询失败！服务器返回错误状态。';
                                }
                                }
                })
            });

       
    }
});