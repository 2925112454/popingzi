function goBack() {
    window.history.back();//返回上一页
}
function goHOME() {
    window.location.href="/";
}

// 添加随机漂浮的星星效果
const starsContainer = document.querySelector('.stars');
const shootingStarsContainer = document.querySelector('.shooting-stars');

function createStar() {
    const star = document.createElement('div');
    const size = Math.random() * 3 + 1;
    star.style.width = `${size}px`;
    star.style.height = `${size}px`;
    star.style.background = 'white';
    star.style.borderRadius = '50%';
    star.style.position = 'absolute';
    star.style.top = `${Math.random() * 100}%`;
    star.style.left = `${Math.random() * 100}%`;
    star.style.boxShadow = `0 0 ${Math.random() * 5 + 5}px white`;
    star.style.opacity = Math.random() * 0.8 + 0.2;
    
    // 随机闪烁效果
    const blinkDuration = Math.random() * 3 + 2;
    star.style.animation = `blink ${blinkDuration}s ease-in-out infinite ${Math.random() * 2}s`;
    
    starsContainer.appendChild(star);
    
    // 移除星星
    setTimeout(() => {
        star.remove();
    }, 15000);
}

// 创建流星
function createShootingStar() {
    const shootingStar = document.createElement('div');
    const startX = Math.random() * 100;
    const startY = Math.random() * 30;
    
    shootingStar.style.position = 'absolute';
    shootingStar.style.top = `${startY}%`;
    shootingStar.style.left = `${startX}%`;
    shootingStar.style.width = '60px';
    shootingStar.style.height = '2px';
    shootingStar.style.background = 'linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 100%)';
    shootingStar.style.transformOrigin = '0 50%';
    shootingStar.style.transform = 'rotate(30deg)';
    shootingStar.style.opacity = 0;
    
    shootingStarsContainer.appendChild(shootingStar);
    
    // 流星动画
    setTimeout(() => {
        shootingStar.style.transition = 'all 1.5s linear';
        shootingStar.style.opacity = 1;
        shootingStar.style.left = `${startX + 30}%`;
        shootingStar.style.top = `${startY + 15}%`;
        shootingStar.style.opacity = 0;
    }, 100);
    
    // 移除流星
    setTimeout(() => {
        shootingStar.remove();
    }, 2000);
}

// 每隔一段时间创建星星和流星
setInterval(createStar, 150);
setInterval(createShootingStar, 5000);

// 页面加载完成后添加漂浮动画
window.addEventListener('load', () => {
    const astronaut = document.querySelector('.astronaut');
    const planet = document.querySelector('.planet');
    const title = document.querySelector('h1');
    
    // 宇航员漂浮动画
    setTimeout(() => {
        astronaut.style.animation = 'float 6s ease-in-out infinite';
    }, 100);
    
    // 星球旋转动画
    setTimeout(() => {
        planet.style.animation = 'rotate 20s linear infinite';
    }, 500);
    
    // 标题文字动画
    setTimeout(() => {
        title.style.animation = 'pulse 2s ease-in-out infinite';
    }, 800);
});

// 宇航员点击交互
document.querySelector('.astronaut').addEventListener('click', () => {
    const astronaut = document.querySelector('.astronaut');
    astronaut.style.transform = 'rotate(360deg) scale(1.1)';
    astronaut.style.transition = 'all 0.5s ease';
    
    setTimeout(() => {
        astronaut.style.transform = '';
        astronaut.style.transition = '';
    }, 1000);
});

// 按钮悬停效果
document.querySelector('button').addEventListener('mouseenter', function(e) {
    this.style.transform = 'scale(1.1)';
    this.style.boxShadow = '0 0 20px rgba(64, 43, 255, 0.5)';
});

document.querySelector('button').addEventListener('mouseleave', function(e) {
    this.style.transform = '';
    this.style.boxShadow = '';
});
    