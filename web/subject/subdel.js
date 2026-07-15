document.addEventListener('DOMContentLoaded', function() {
    let newtimeoutId = null;
    function isPositiveIntegerx(value) {
        if (value === null || value === undefined || isNaN(value) || !isFinite(value)) {
            return false;
        }
        const num = Number(value);
        return Number.isInteger(num) && num > 0;
    }
    function mesgdel(code,text,but){
        const newdataerrdiv = document.getElementById("newdataerr");//提示元素
        if(code==200){
                var backgroundColorx = "#8BC34A";
                setTimeout(() => {
                    location.reload();
                }, 2000);
        }else{
                var backgroundColorx = "";
                but.innerHTML = '删除';
                but.style.opacity = 1;
                but.style.pointerEvents = '';
                but.style.cursor = '';
        }
                if (newtimeoutId) {
                    clearTimeout(newtimeoutId);
                }
                newdataerrdiv.classList.add('show');
                newdataerrdiv.classList.remove('hide');
                newdataerrdiv.style.display = "block";
                newdataerrdiv.style.backgroundColor = backgroundColorx;
                newdataerrdiv.innerHTML = text;
                newtimeoutId = setTimeout(() => {
                newdataerrdiv.classList.remove('show');
                newdataerrdiv.classList.add('hide');
                newdataerrdiv.addEventListener('animationend', () => {
                    newdataerrdiv.style.display = "none";
                    newdataerrdiv.innerHTML = "";
                    newdataerrdiv.style.backgroundColor = "";
                    newdataerrdiv.classList.remove('hide');
                }, { once: true });
                }, 3000);
    }

    const subdel = document.querySelectorAll('.sub-del');
    if(subdel.length>0){
        subdel.forEach(function(V){
            V.addEventListener('click', function() {
                const subid = V.getAttribute('sub-id');
                if(isPositiveIntegerx(subid)){
                    if (confirm('删除不可撤回，您确定要继续吗？')) {
                        //改变按钮状态
                        V.innerHTML = '<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>';
                        V.style.opacity = 0.8;
                        V.style.pointerEvents = 'none';
                        V.style.cursor = 'not-allowed';
                        $.ajax({
                            url: 'subdel.php',
                            type: 'POST',
                            data: {
                                id:subid,
                            },
                                        success: function(del) { // 成功回调函数
                                            if(del == 200){
                                                mesgdel(200,'(◕ܫ◕) 删除成功，正在刷新页面！');                                   
                                            }else{
                                                mesgdel(500,'(｡ŏ_ŏ) 删除失败！',V);
                                            }
                                        }
                    
                        });
                        
                    }
                }else{
                    mesgdel(500,'(｡ŏ_ŏ) 非法操作！');
                }
            })
        })
    }
})