document.addEventListener('DOMContentLoaded', function() {
    let vscookieValue = getCookie('viewswitching');
    const viewswitchingbut = document.getElementById('viewswitching');//视图切换按钮
    const viewswitchingimgxc = document.getElementById('imgxc');
    if (viewswitchingbut&&viewswitchingimgxc) {
        viewswitchingbut.addEventListener('click', function() {
            if (vscookieValue == 2) {
                vscookieValue=1;
                setCookie('viewswitching',1, 365);
                viewswitchingbut.innerHTML = '<i class="fa fa-align-justify" aria-hidden="true"></i>大图模式';
                viewswitchingimgxc.classList.remove('vsclass');
            } else {
                vscookieValue=2;
                setCookie('viewswitching',2, 365);
                viewswitchingbut.innerHTML = '<i class="fa fa-th" aria-hidden="true"></i>小图模式';
                viewswitchingimgxc.classList.add('vsclass');
           }
        });
    }

})