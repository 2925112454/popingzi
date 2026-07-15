document.addEventListener('DOMContentLoaded', function() {
    const yxjdasdiv = document.getElementById('yxjdasdiv'); // 右下角弹窗广告div
    const adsclose = document.getElementById('adsclose'); // 右下角弹窗广告关闭按钮

    if (yxjdasdiv && adsclose) {

        // 检查Cookie中是否有隐藏广告的标记
        if (getCookie('yxjdasdiv') === 'hidden') {
            yxjdasdiv.style.display = "none";
        }

        adsclose.onclick = function () { // 关闭广告
            yxjdasdiv.style.display = "none";
            // 设置Cookie，有效期1天
            setCookie('yxjdasdiv', 'hidden', 1);
        }

        // 可拖动右下角弹窗div
        const draggable = yxjdasdiv;
        let isDragging = false;
        let currentX;
        let currentY;
        let initialX;
        let initialY;
        // 鼠标按下时记录初始位置
        draggable.addEventListener('mousedown', dragStart);

        // 鼠标移动时执行拖动
        document.addEventListener('mousemove', drag);

        // 鼠标松开时结束拖动
        document.addEventListener('mouseup', dragEnd);
        document.addEventListener('mouseleave', dragEnd);

        function dragStart(e) {
            // 只有在点击把手时才允许拖动
            if (e.target.classList.contains('handle') || e.target === draggable) {
                e.preventDefault();

                // 获取当前元素的 left 和 top 值，如果为空则设为 0
                const style = window.getComputedStyle(draggable);
                const currentLeft = parseInt(style.left) || 0;
                const currentTop = parseInt(style.top) || 0;

                initialX = e.clientX - currentLeft;
                initialY = e.clientY - currentTop;

                isDragging = true;
            }
        }

        function drag(e) {
            if (isDragging) {
                e.preventDefault();

                // 获取视口尺寸和元素尺寸
                const viewportWidth = window.innerWidth;
                const viewportHeight = window.innerHeight;
                const elementWidth = draggable.offsetWidth;
                const elementHeight = draggable.offsetHeight;

                // 计算新位置
                currentX = e.clientX - initialX;
                currentY = e.clientY - initialY;

                // 限制元素不能拖出视口
                currentX = Math.max(0, Math.min(currentX, viewportWidth - elementWidth));
                currentY = Math.max(0, Math.min(currentY, viewportHeight - elementHeight));

                // 应用新位置
                draggable.style.left = `${currentX}px`;
                draggable.style.top = `${currentY}px`;
            }
        }

        function dragEnd() {
            isDragging = false;
        }

        // 页面加载后立即添加抖动动画
        yxjdasdiv.classList.add('shake-animation');

        // 当鼠标悬停在div上时移除抖动动画
        yxjdasdiv.addEventListener('mouseenter', function() {
            this.classList.remove('shake-animation');
        });

        // 当鼠标离开div时恢复抖动动画
        yxjdasdiv.addEventListener('mouseleave', function() {
            this.classList.add('shake-animation');
        });

        // 定义抖动动画的CSS样式
        const adsstyle = document.createElement('style');
        adsstyle.textContent = `
            @keyframes shake {
                0%, 100% { transform: translateX(0) rotate(0); }
                25% { transform: translateX(-2px) rotate(-0.5deg); }
                50% { transform: translateX(2px) rotate(0.5deg); }
                75% { transform: translateX(-1px) rotate(-0.3deg); }
            }
            .shake-animation {
                animation: shake 0.6s ease-in-out infinite;
            }
        `;
        document.head.appendChild(adsstyle);

        
    }
});