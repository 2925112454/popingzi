document.addEventListener('DOMContentLoaded', function() {
    function getUrlParam(paramName, url) {
        const currentUrl = url || window.location.href;
        const urlObj = new URL(currentUrl);
        const paramValue = urlObj.searchParams.get(paramName);
        return paramValue;
    }
       function isPositiveIntegerx(value) {
        if (value === null || value === undefined || isNaN(value) || !isFinite(value)) {
            return false;
        }
        const num = Number(value);
        return Number.isInteger(num) && num > 0;
    }

    function postsubmsg(uidx,code){
        const subuser_post_uuid = document.querySelectorAll(`.subuser_post[data-uuid="${uidx}"]`);
        if(code==200){
                if(subuser_post_uuid.length>0){
                    //处理所有
                    subuser_post_uuid.forEach(function(itemx) {
                        //判断subuser_post_uuid是否含有subfollowed类名
                        if(itemx.classList.contains('subfollowed')){
                            itemx.classList.remove('subfollowed');
                            itemx.innerHTML = '<i class="fa fa-plus" aria-hidden="true"></i>关注';
                        }else{
                            itemx.classList.add('subfollowed');
                            itemx.innerHTML = '<i class="fa fa-check" aria-hidden="true"></i>已关注';
                        }

                    })
            }
            const getfollow = getUrlParam('follow');
            if (getfollow == 1) {
                location.reload();
            }
        }else{
            if(subuser_post_uuid.length>0){
                subuser_post_uuid.forEach(function(itemx) {
                        if(itemx.classList.contains('subfollowed')){
                            itemx.innerHTML = '<i class="fa fa-check" aria-hidden="true"></i>已关注';
                        }else{
                            itemx.innerHTML = '<i class="fa fa-plus" aria-hidden="true"></i>关注';
                        }
               })
            }
            alert('<font>(ô‿ô)</font> 操作失败，请稍后再试！');
        }

    }
    
    const subuser_post = document.querySelectorAll('.subuser_post');//获取所有关注按钮
    if(subuser_post.length>0){
        subuser_post.forEach(function(item) {
            item.addEventListener('click', function() {
                const uuid = item.getAttribute('data-uuid');
                if(isPositiveIntegerx(uuid)){
                    item.innerHTML = '<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>处理中...';
                    $.ajax({
                        url: '/inc/folus.php',
                        type: 'POST',
                        data: {
                            fsid:uuid,
                        },
                                    success: function(response) { // 成功回调函数
                                        if(response == 200 || response == 202){
                                            postsubmsg(uuid,200);                                                   
                                        }else{
                                            postsubmsg(uuid,500);
                                        }
                                    }
                
                    });                    
                    
                }
            })
        })
    }

    const violate = document.querySelectorAll('.violate');
    const subdialog = document.getElementById('subdialog');
    if(violate.length > 0 && subdialog){
        violate.forEach(function(item) {
            item.addEventListener('click', function() {
                const mesg = item.getAttribute('data-text');
                if(mesg){
                    subdialog.showModal();
                    subdialog.innerHTML = '<h1><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>审核未通过</h1><p><b class="span">理由：</b>'+mesg+'</p><span>*请按要求修改后再提交审核！</span>';
                    subdialog.style.display = 'block';
                }
            })
        })
        subdialog.addEventListener('click', function() {
            subdialog.close();
            subdialog.style.display = 'none';
            subdialog.innerHTML = '';
        })
    }
})