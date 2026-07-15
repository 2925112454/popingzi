// DOM元素
const likeContainer = document.getElementById('like-container');

// 状态变量
let currentIndex = 0;
const maxVisibleLikes = 4;
const displayDuration = 3000; // 每个点赞显示的时间（毫秒）
const animationDuration = 500; // 动画持续时间（毫秒）

let cycleTimer = null;         // 多条循环 intervalID
let singleTimer = null;        // 单条循环 timeoutID
let isPaused = false;          // 是否暂停
let hoverTimer = null;         // 悬浮延时恢复定时器
let modeSingle = false;        // true=只有1条数据模式

// ========== 创建操作菜单 ==========
const actionMenu = document.createElement('div');
actionMenu.id = 'like-action-menu';
actionMenu.style.cssText = `
    position: fixed;
    background: var(--heder-color);
    color: var(--font-color);
    border-radius: 12px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.18);
    z-index: 9999;
    padding: 8px 0;
    display: none;
`;
actionMenu.innerHTML = `
    <div class="menu-item" data-action="pause">暂停动画</div>
    <div class="menu-item" data-action="resume">恢复动画</div>
    <div class="menu-item" data-action="clear">清空动画</div>
`;
// 菜单样式
const style = document.createElement('style');
style.textContent = `
    #like-action-menu .menu-item{
        padding:12px 18px;
        font-size:12px;
        cursor:pointer;
    }
    #like-action-menu .menu-item:active{
        background:var(--button-color);
    }
`;
document.head.appendChild(style);
document.body.appendChild(actionMenu);

// 菜单点击事件
actionMenu.addEventListener('click', (e)=>{
    const action = e.target.dataset.action;
    switch(action){
        case 'pause':
            pauseLikeAnim();
            break;
        case 'resume':
            resumeLikeAnim();
            break;
        case 'clear':
            clearAllLikes();
            break;
    }
    hideMenu();
});

// 显示菜单
function showMenu(targetItem){
    const rect = targetItem.getBoundingClientRect();
    actionMenu.style.left = `${rect.left}px`;
    actionMenu.style.top = `${rect.bottom + 8}px`;
    actionMenu.style.display = 'block';

    // 边界检测，防止菜单超出屏幕
    setTimeout(()=>{
        const menuRect = actionMenu.getBoundingClientRect();
        if(menuRect.right > window.innerWidth){
            actionMenu.style.left = `${window.innerWidth - menuRect.width - 10}px`;
        }
        if(menuRect.bottom > window.innerHeight){
            actionMenu.style.top = `${rect.top - menuRect.height - 8}px`;
        }
    },0);
}
// 隐藏菜单
function hideMenu(){
    actionMenu.style.display = 'none';
}

// 全局点击空白关闭菜单
document.addEventListener('click', (e)=>{
    // 如果点击区域不是菜单，也不是like-item，则关闭
    if(!actionMenu.contains(e.target) && !e.target.closest('.like-item')){
        hideMenu();
    }
});


document.addEventListener('DOMContentLoaded', () => {
    if (!likeContainer) {
        return;
    }
    if (!likeData || likeData.length < 1) {
        return;
    }

    modeSingle = likeData.length === 1;

    // PC端悬浮暂停
    bindHoverEvent();

    // 【核心】事件委托：点击任意like-item弹出菜单（支持移动端+PC）
    likeContainer.addEventListener('click', (e) => {
        const targetItem = e.target.closest('.like-item');
        if(targetItem){
            e.preventDefault();
            showMenu(targetItem);
        }
    });

    // 初始加载点赞条目
    const initialDisplayCount = Math.min(maxVisibleLikes, likeData.length);
    
    for (let i = 0; i < initialDisplayCount; i++) {
        setTimeout(() => {
            addLike(likeData[i]);
            currentIndex++;
            
            if (i === initialDisplayCount - 1) {
                if (modeSingle) {
                    singleTimer = setTimeout(cycleSingleLike, displayDuration);
                } else {
                    startCyclingDisplay();
                }
            }
        }, i * (displayDuration / 2));
    }
});

/**
 * PC悬浮暂停
 */
function bindHoverEvent() {
    likeContainer.addEventListener('mouseenter', () => {
        pauseLikeAnim();
        clearTimeout(hoverTimer);
    });
    likeContainer.addEventListener('mouseleave', () => {
        clearTimeout(hoverTimer);
        hoverTimer = setTimeout(() => {
            resumeLikeAnim();
        }, 800);
    });
}

// 添加点赞DOM
function addLike(like) {
    const likeEl = document.createElement('div');
    likeEl.className = 'like-item';
    likeEl.innerHTML = `
        <img src="${like.avatar}" alt="${like.name}的头像" class="avatar">
        <div class="user-info">
            <div class="user-name-x">${like.name}</div>
            <div class="like-text">
                <i class="fa fa-thumbs-up mr-1"></i>
                <span>点赞了该评论</span>
            </div>
        </div>
        <i class="fa fa-heart heart-icon"></i>
    `;
    
    likeContainer.appendChild(likeEl);
    
    setTimeout(() => {
        likeEl.classList.add('visible');
    }, 10);
}

// 单条数据循环
function cycleSingleLike() {
    // 如果暂停，不继续生成下一轮定时器，直接退出
    if (isPaused) {
        singleTimer = null;
        return;
    }

    const like = likeData[0];
    const likeEl = likeContainer.querySelector('.like-item');
    
    if (likeEl) {
        likeEl.classList.remove('visible');
        likeEl.classList.add('fade-out');
        
        setTimeout(() => {
            if (likeContainer.contains(likeEl)) {
                likeContainer.removeChild(likeEl);
            }
            // 移除完成后再新增
            addLike(like);
            // 创建下一轮延时
            singleTimer = setTimeout(cycleSingleLike, displayDuration);
        }, animationDuration + 500);
    }
}

// 多条数据循环
function startCyclingDisplay() {
    if (cycleTimer) return;

    cycleTimer = setInterval(() => {
        if (isPaused) return;

        currentIndex = currentIndex % likeData.length;
        
        const like = likeData[currentIndex];
        addLike(like);
        
        currentIndex++;
        
        if (likeContainer.children.length > maxVisibleLikes) {
            const oldestLike = likeContainer.firstChild;
            
            oldestLike.classList.remove('visible');
            oldestLike.classList.add('fade-out');
            
            setTimeout(() => {
                if (likeContainer.contains(oldestLike)) {
                    likeContainer.removeChild(oldestLike);
                }
            }, animationDuration);
        }
    }, displayDuration);
}

/**
 * 暂停动画
 */
function pauseLikeAnim() {
    isPaused = true;
    clearTimeout(hoverTimer);

    if (cycleTimer) {
        clearInterval(cycleTimer);
        cycleTimer = null;
    }
    if (singleTimer) {
        clearTimeout(singleTimer);
        singleTimer = null;
    }
}

/**
 * 恢复动画
 */
function resumeLikeAnim() {
    if(!isPaused) return;
    isPaused = false;

    if(modeSingle){
        if(!singleTimer){
            singleTimer = setTimeout(cycleSingleLike, displayDuration);
        }
    }else{
        if(!cycleTimer){
            startCyclingDisplay();
        }
    }
}

/**
 * 【清屏】
 */
function clearAllLikes() {
    pauseLikeAnim();

    const items = likeContainer.querySelectorAll('.like-item');
    items.forEach(item => {
        item.classList.remove('visible');
        item.classList.add('fade-out');
    });

    setTimeout(() => {
        likeContainer.innerHTML = '';
        currentIndex = 0;
    }, animationDuration);
}


function restartLikeAnim() {
    clearAllLikes();
    setTimeout(() => {
        isPaused = false;
        const initialDisplayCount = Math.min(maxVisibleLikes, likeData.length);
        for (let i = 0; i < initialDisplayCount; i++) {
            setTimeout(() => {
                addLike(likeData[i]);
                currentIndex++;
                if (i === initialDisplayCount - 1) {
                    if (modeSingle) {
                        singleTimer = setTimeout(cycleSingleLike, displayDuration);
                    } else {
                        startCyclingDisplay();
                    }
                }
            }, i * (displayDuration / 2));
        }
    }, animationDuration + 100);
}
