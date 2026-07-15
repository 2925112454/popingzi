document.addEventListener('DOMContentLoaded', function() {
    // 功能检测：判断是否需要实现自定义手势
    function shouldImplementCustomGestures() {
        // 检查是否为触摸设备
        const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        // 检查浏览器是否支持overscroll-behavior (用于下拉刷新)
        const supportsOverscrollBehavior = 'overscrollBehavior' in document.documentElement.style;
        // 检查是否在iOS Safari上 (滑动返回在iOS Safari上无法禁用)
        const isIOSSafari = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        // 仅在触摸设备且不支持overscroll-behavior或在iOS Safari上实现自定义手势
        return isTouchDevice && (!supportsOverscrollBehavior || isIOSSafari);
    }

    if (!shouldImplementCustomGestures()) {
        return;//浏览器支持原生下拉刷新/滑动返回，禁用自定义手势代码
    }

    function isMobileBrowser() {
        const userAgent = navigator.userAgent || navigator.vendor || window.opera;
        return /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od|ad)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(userAgent) ||
                (typeof window.orientation !== 'undefined') ||
                (navigator.maxTouchPoints > 0) ||
                (navigator.msMaxTouchPoints > 0);
    }

    const wapfooter = document.getElementById('wapfooter');
    const mobileBody = document.body;

    let touchStartX = 0;
    let touchStartY = 0;
    let isScrolling = false;
    let isRefreshing = false;
    let startScrollTop = 0;
    let isHandlingScroll = false;
    let isSwipingBack = false;
    let swipeStartX = 0;

    // 下拉刷新相关元素
    const refreshIndicator = document.createElement('div');
    refreshIndicator.className = 'refresh-indicator';
    refreshIndicator.innerHTML = '';
    refreshIndicator.style.cssText = `
        position: fixed;
        top: -40px;
        left: 0;
        width: 100%;
        height: 40px;
        background-color: var(--home-hover-color);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: top 0.3s ease;
        z-index: 10000;
    `;

    // 滑动返回指示器
    const swipeBackIndicator = document.createElement('div');
    swipeBackIndicator.style.cssText = `
        position: fixed;
        top: 0;
        left: -70px;
        width:60px;
        height: 100%;
        background: var(--home-hover-color);
        opacity:0;
        pointer-events: none;
        z-index: 1;
        transition: left 0.1s ease;
        box-shadow: 0 0 15px var(--home-hover-color);
    `;

    // 添加刷新指示器和滑动返回指示器到页面
    mobileBody.insertBefore(refreshIndicator, mobileBody.firstChild);
    mobileBody.appendChild(swipeBackIndicator);

    function refreshPage() {
        refreshIndicator.innerHTML = '页面刷新中...';
        setTimeout(() => {
            isRefreshing = false;
            refreshIndicator.innerHTML = '';
            refreshIndicator.style.top = '-40px';
            location.reload();
        }, 1500);
    }

    if (wapfooter && mobileBody && isMobileBrowser()) {
        mobileBody.addEventListener('touchstart', function(e) {
            touchStartX = e.touches[0].pageX;
            touchStartY = e.touches[0].pageY;
            isScrolling = false;
            isHandlingScroll = false;
            startScrollTop = window.scrollY;
            swipeStartX = touchStartX;
            isSwipingBack = false;
            if (startScrollTop <= 0) {
                touchStartY = e.touches[0].pageY;
            }
        }, { passive: false });

        mobileBody.addEventListener('touchmove', function(e) {
            if (isScrolling || isHandlingScroll) return;
            const moveX = e.touches[0].pageX;
            const moveY = e.touches[0].pageY;
            const distanceX = moveX - touchStartX;
            const distanceY = moveY - touchStartY;

            if (Math.abs(distanceY) > Math.abs(distanceX)) {
                isScrolling = true;
                isHandlingScroll = startScrollTop <= 0 && distanceY > 0;

                if (isHandlingScroll) {
                    e.preventDefault();
                    const pullDownDistance = Math.min(distanceY, 80);
                    refreshIndicator.style.top = `${-40 + pullDownDistance/2}px`;

                    if (pullDownDistance > 60) {
                        refreshIndicator.innerHTML = '释放刷新...';
                    } else {
                        refreshIndicator.innerHTML = '';
                    }
                }
            } else if (Math.abs(distanceX) > 10) {
                // 右滑返回
                isScrolling = true;
                isSwipingBack = distanceX > 0;

                if (isSwipingBack) {
                    e.preventDefault();
                    const swipeDistance = moveX - swipeStartX;
                    const progress = 1;

                    // 更新滑动返回指示器
                    swipeBackIndicator.style.opacity = progress;
                    swipeBackIndicator.style.left = `${-80 + swipeDistance}px`;
                }
            }
        }, { passive: false });

        mobileBody.addEventListener('touchend', function(e) {
            // 处理下拉刷新释放逻辑
            if (startScrollTop <= 0 && isHandlingScroll) {
                const moveY = e.changedTouches[0].pageY;
                const distanceY = moveY - touchStartY;

                if (distanceY > 60) {
                    isRefreshing = true;
                    refreshIndicator.style.top = '0px';
                    refreshPage();
                } else {
                    refreshIndicator.style.top = '-40px';
                }

                isHandlingScroll = false;
            }

            // 处理滑动返回释放逻辑
            if (isSwipingBack) {
                const moveX = e.changedTouches[0].pageX;
                const distanceX = moveX - swipeStartX;
                const maxSwipeDistance = window.innerWidth * 0.8;

                // 重置指示器
                swipeBackIndicator.style.opacity = 0;
                swipeBackIndicator.style.left = '-70px';

                if (distanceX > maxSwipeDistance * 0.5) {
                    // 执行返回操作
                    window.history.back();
                }
            }
        });
    }
    const areachartbut=document.getElementById('areachart');
    const areachartbox=document.getElementById('areachartbox');
    if(areachartbut && areachartbox){
        areachartbut.addEventListener('click', function(event) {
            if(areachartbox.style.display == 'block'){
                areachartbox.style.display = 'none';
            }else{
                areachartbox.style.display = 'block';
            }
            setTimeout(function() {
                areachartbox.style.display = 'none';
            }, 2000);
        });
    }
});