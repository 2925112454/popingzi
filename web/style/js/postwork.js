document.addEventListener('DOMContentLoaded', function() {
    const work_select = document.getElementById('select');//选择框
    const work_title = document.getElementById('title');//标题框
    const work_images = document.getElementById('images');//附件框
    const work_content = document.getElementById('text');//内容框
    const work_submit = document.getElementById('post');//提交按钮
    const work_mun = document.getElementById('mun');//内容数量框（显示剩余字数）
    if (work_select&&work_title&&work_images&&work_content&&work_submit&&work_mun) {
        const maxLength = 500;//内容最大字数
        const maxLength_title = 60;//标题最大字数
        work_mun.textContent = maxLength;
        work_content.maxLength=maxLength;
        work_title.maxLength=maxLength_title;
        work_content.addEventListener('input', function() {
            const currentLength = work_content.value.length;
            const remainingLength = maxLength - currentLength;
            work_mun.textContent = remainingLength;
        });
        function isPositiveIntegerUniversal(value) {
            const num = Number(value);
            return (
                !isNaN(num) &&
                Number.isInteger(num) &&
                num >= 0
            );
        }
        work_submit.addEventListener('click', function() {
            const title_v = work_title.value;
            const images_v = work_images.value;
            const content_v = work_content.value;
            const select_v = work_select.value;
            if(!content_v||!title_v||content_v.length<1||title_v.length<1){
                alert("<font>(｡ŏ_ŏ)</font> 内容或标题不能为空！");
                return;
            }
            if(!select_v || select_v<0 ||!isPositiveIntegerUniversal(select_v)){
                alert("<font>(｡ŏ_ŏ)</font> 错误参数！");
                return;
            }
            if(content_v.length > maxLength){
                alert("<font>(｡ŏ_ŏ)</font> 内容不能超过"+maxLength+"个字符！");
                return;
            }
            if(title_v.length > maxLength_title){
                alert("<font>(｡ŏ_ŏ)</font> 标题不能超过"+maxLength_title+"个字符！");
                return;
            }
            if(images_v){
                if (!images_v.startsWith("http://") && !images_v.startsWith("https://") && !images_v.startsWith("//")){
                    alert("<font>(｡ŏ_ŏ)</font> 附件地址格式错误！");
                    return;
                }
            }

            $.ajax({
                type: "POST",
                url: "edit/postwork.php",
                data: {
                    "title": title_v,
                    "content": content_v,
                    "images": images_v,
                    "select": select_v,
                },
                dataType: "json",
                success: function(post) {
                    if (post.code == 200) {
                        window.location.href = "user.php?type=8";
                    } else if (post.code==500) {
                        alert("<font>(｡ŏ_ŏ)</font> "+post.msg);
                    }else{
                        alert("<font>(｡ŏ_ŏ)</font> 服务器错误！");
                    }
                }
                
            })




            

        })
    }
})