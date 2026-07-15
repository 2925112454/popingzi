document.addEventListener('DOMContentLoaded', function() {
    const leftadsfloat = document.getElementById('leftads'); //左侧悬浮广告div
    const leftadsclose = document.getElementById('leftadsclose');//左侧悬浮广告关闭按钮

    const rightadsfloat = document.getElementById('rightads');//右侧悬浮广告div    
    const rightadsclose = document.getElementById('rightadsclose');//右侧悬浮广告关闭按钮

        // 退出动画（移出）
        function animeout(dom, leftorright) {
            if (leftorright === 0) {
                dom.classList.remove('slide-in-left');
                dom.classList.add('slide-out-left');
                setTimeout(() => {
                    dom.style.display = 'none';
                }, 600); // 等待动画结束再隐藏元素
            } else if (leftorright === 1) {
                dom.classList.remove('slide-in-right');
                dom.classList.add('slide-out-right');
                setTimeout(() => {
                    dom.style.display = 'none';
                }, 600);
            }
        }

        // 显示动画（移入）
        function animeromv(domx, leftorrightx) {
            domx.style.display = 'block';
            if (leftorrightx === 0) {
                domx.classList.remove('slide-out-left');
                domx.classList.add('slide-in-left');
            } else if (leftorrightx === 1) {
                domx.classList.remove('slide-out-right');
                domx.classList.add('slide-in-right');
            }
        }

        // 鼠标悬停时触发抖动动画
        function startShakeAnimation(elementw) {
            // 保存当前是否具有 slide-in 类
            const wasSlideInLeft = elementw.classList.contains('slide-in-left');
            const wasSlideInRight = elementw.classList.contains('slide-in-right');

            // 先清除所有动画类，防止冲突
            elementw.classList.remove('shake-animationx', 'slide-in-left', 'slide-in-right');

            // 强制重排以重新触发动画
            void elementw.offsetWidth;

            // 重新添加 shake 类
            elementw.classList.add('shake-animationx');

            setTimeout(() => {
                elementw.classList.remove('shake-animationx');
            }, 400); // 与动画持续时间一致
        }

    if (leftadsfloat &&  leftadsclose) {
        if (getCookie('leftadsfloat') === 'hidden') {
            leftadsfloat.style.display = 'none';
        }else{
            animeromv(leftadsfloat,0);
        }
        leftadsclose.onclick = function () { // 关闭广告
            animeout(leftadsfloat,0);
            setCookie('leftadsfloat', 'hidden', 1);
        }
        leftadsfloat.addEventListener('mouseenter', function () {
            if (!this.classList.contains('shake-animation')) {
                startShakeAnimation(this);
            }
        });
    }

    if (rightadsfloat &&  rightadsclose) {
        if (getCookie('rightadsfloat') === 'hidden') {
            rightadsfloat.style.display = 'none';
        }else{
            animeromv(rightadsfloat,1);
        }
        rightadsclose.onclick = function () { // 关闭广告
            animeout(rightadsfloat,1);
            setCookie('rightadsfloat', 'hidden', 1);
        }
        rightadsfloat.addEventListener('mouseenter', function () {
            if (!this.classList.contains('shake-animationx')) {
                startShakeAnimation(this);
            }
        });
    }

});