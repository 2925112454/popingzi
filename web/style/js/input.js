//导航栏搜索框验证
function checkLen2(obj){
    let maxChars = 20;//最多字符数
    if (obj.value.length > maxChars){
        alert("<font>(ಠ .̫.̫ ಠ)</font>  字数最多只能20字哦~");
        obj.value = obj.value.substring(0,maxChars);
    }
};

function checkformss() {
	let soso = document.search.s.value;
        soso = soso.trim();
	let patrn = /[`~!=|@#$%^&.*()_+<>?:"{},\/;'[\]\\]/im;
    
	if (soso == null || soso === "" || soso === "--"){
                alert("<font>(ô‿ô)</font> 您要找什么呢~");
                return false;
    }else if(patrn.test(soso)){
		alert("<font>(ಠ .̫.̫ ಠ)</font> 不能含有特殊字符~");
		return false;	
	};
	
};

function checkformpage() {
	let pageinput = document.page.p.value;
	let patrnx=/[`~!=|@#$%^&.*()_+<>?:"{},\/;'[\]\\]/im;
    function isNumber(n) {  
        return !isNaN(parseFloat(n)) && isFinite(n) && Number.isInteger(Number(n));  
    }

	if (pageinput === "" ){
                alert("<font>(ô‿ô)</font> 您要到哪去？");
                return false;
    }else if(patrnx.test(pageinput)){
		alert("<font>(ಠ .̫.̫ ಠ)</font> 请正确输入页码~");
		return false;	
	}else if(!isNumber(pageinput)){
        alert("<font>(ಠ .̫.̫ ಠ)</font> 请正确输入页码~");
		return false;
    };
	
};
document.addEventListener('DOMContentLoaded', function() {
    // 获取所有 input type="number"
    const inputnumber = document.querySelectorAll('input[type="number"]');
    let activeInput = null; // 用于存储当前选中的 input 元素

    if (inputnumber) {
        inputnumber.forEach(input => {
            // 隐藏默认的上下箭头
            input.style.MozAppearance = 'textfield'; // Firefox
            input.style.WebkitAppearance = 'none'; // Chrome, Safari, Edge
            input.style.appearance = 'none';

            // 监听 focus 事件，设置当前选中的 input 元素
            input.addEventListener('focus', function() {
                activeInput = input;
            });

            // 监听 blur 事件，清除当前选中的 input 元素
            input.addEventListener('blur', function() {
                activeInput = null;
            });

            // 监听 input 事件，验证和调整输入值
            input.addEventListener('input', function() {
                const minValue = parseFloat(input.min) || 0; // 获取最小值，默认为 -Infinity
                const maxValue = parseFloat(input.max) || Infinity; // 获取最大值，默认为 Infinity
                let value = parseFloat(input.value) || 0; // 获取当前值，默认为0

                // 确保 value 在 min 和 max 范围内
                if (value < minValue) {
                    value = minValue;
                } else if (value > maxValue) {
                    value = maxValue;
                }

                input.value = value;
            });
        });

        // 监听全局 wheel 事件，并指定 { passive: false }
        document.addEventListener('wheel', function(event) {
            if (activeInput) {
                event.preventDefault(); // 阻止默认的滚动行为

                const step = parseFloat(activeInput.step) || 1; // 获取步长，默认为1
                const minValue = parseFloat(activeInput.min) || 0; // 获取最小值，默认为 -Infinity
                const maxValue = parseFloat(activeInput.max) || Infinity; // 获取最大值，默认为 Infinity
                const value = parseFloat(activeInput.value) || 0; // 获取当前值，默认为0

                let newValue;
                if (event.deltaY < 0) {
                    // 向上滚动，增加数值
                    newValue = value + step;
                } else {
                    // 向下滚动，减少数值
                    newValue = value - step;
                }

                // 确保 newValue 在 min 和 max 范围内
                if (newValue < minValue) {
                    newValue = minValue;
                } else if (newValue > maxValue) {
                    newValue = maxValue;
                }

                activeInput.value = newValue;
            }
        }, { passive: false }); // 指定 { passive: false }
    }
});

/* 为dialog添加拖动功能 */
document.addEventListener('DOMContentLoaded', function() {
    const dragdialog = document.querySelectorAll('.drag');
    if (dragdialog && dragdialog.length > 0) {
        dragdialog.forEach(dialog => {
            if (dialog.tagName === 'DIALOG') {
                // 创建一个元素作为拖动手柄
                const dragHandle = document.createElement('div');
                dragHandle.className = 'drag-handle';
                dragHandle.innerHTML = '<i class="fa fa-arrows-alt" aria-hidden="true"></i>';
                dialog.appendChild(dragHandle);
               
                // 添加拖动事件
                let isDragging = false;
                let initialX, initialY, initialLeft, initialTop;

                dragHandle.addEventListener('mousedown', (e) => {
                    e.preventDefault();
                    isDragging = true;
                    // 记录初始位置
                    initialX = e.clientX;// 鼠标点击时的X坐标
                    initialY = e.clientY;// 鼠标点击时的Y坐标
                    initialLeft = parseFloat(dialog.style.left) || 0;// dialog的left值
                    initialTop = parseFloat(dialog.style.top) || 0;// dialog的top值
                    dragHandle.style.cursor = 'grabbing'; 
                   
                });

                document.addEventListener('mouseup', () => {
                    if (isDragging) {
                        isDragging = false;
                        dragHandle.style.cursor = 'grab';
                    }
                });

                document.addEventListener('mousemove', (e) => {
                    if (isDragging) {
                        // 计算 dialog 的新位置
                        const deltaX = e.clientX - initialX;
                        const deltaY = e.clientY - initialY;
                        dialog.style.left = (initialLeft + deltaX) + 'px';
                        dialog.style.top = (initialTop + deltaY) + 'px';
                    }
                });

                // 双击 dialog 或 dragHandle 恢复默认位置
                dragHandle.addEventListener('dblclick', () => {
                    dialog.style.left = '';
                    dialog.style.top = '';
                    dialog.style.transform = '';
                    dragHandle.style.cursor = 'grab';
                });

                dialog.addEventListener('dblclick', () => {
                    // 判断是否是文本框聚焦
                    if (document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA' || document.activeElement.tagName === 'SELECT' || document.activeElement.tagName === 'LABEL' || document.activeElement.tagName === 'BUTTON') {
                        return;
                    }
                    dialog.style.left = '';
                    dialog.style.top = '';
                    dialog.style.transform = '';
                    dragHandle.style.cursor = '';
                });

                const closeButton = dialog.querySelector('i[class="fa fa-times"]'); // 关闭按钮
                if (closeButton) {
                    closeButton.addEventListener('click', () => {
                        dialog.style.left = '';
                        dialog.style.top = '';
                        dialog.style.transform = '';
                        dragHandle.style.cursor = '';
                        dialog.style.transition = '';
                    });
                }
            }
        });
    }
});

/* 鼠标拖动框选复选框（支持拖动时滚动页面，修复滚动后多选失效问题） */
class CheckboxDragSelect {
    constructor() {
        // 核心状态
        this.mouseDown = false;       // 左键是否按下
        this.isDragging = false;      // 是否正在拖动
        this.startX = 0;              // 拖动起始X（文档坐标）
        this.startY = 0;              // 拖动起始Y（文档坐标）
        this.dragThreshold = 8;       // 拖动阈值
        this.lastMoveTime = 0;        // 最后一次鼠标移动时间（防抖）
        this.autoScrollTimer = null;  // 自动滚动定时器
        this.scrollOffsetX = 0;       // 滚动偏移X（补偿用）
        this.scrollOffsetY = 0;       // 滚动偏移Y（补偿用）
        
        // 状态跟踪
        this.originStates = new Map(); // 存储复选框初始状态
        this.selectedBoxes = new Set(); // 记录当前被框选的复选框
        this.allTargetCheckboxes = new Set(); // 记录所有被框选过的复选框（滚动时保留）

        this.init();
    }

    // 初始化
    init() {
        this.bindEvents();
    }

    // 绑定事件
    bindEvents() {
        const that = this;

        // ========== 1. 左键按下：初始化状态 ==========
        document.addEventListener('mousedown', (e) => {
            if (e.button !== 0) return;
            
            // 重置所有状态
            that.mouseDown = true;
            that.isDragging = false;
            // 记录文档坐标（而非视口坐标），避免滚动影响
            that.startX = e.clientX + window.scrollX;
            that.startY = e.clientY + window.scrollY;
            that.scrollOffsetX = window.scrollX;
            that.scrollOffsetY = window.scrollY;
            that.originStates.clear();
            that.selectedBoxes.clear();
            that.allTargetCheckboxes.clear();

            // 记录所有【非禁用】复选框的初始状态
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(cb => {
                if (!cb.disabled) {
                    that.originStates.set(cb, cb.checked);
                }
            });
        });

        // ========== 2. 鼠标移动：防抖+精准判定+自动滚动 ==========
        document.addEventListener('mousemove', (e) => {
            // 防抖：10ms内只执行一次，避免高频触发
            const now = Date.now();
            if (now - that.lastMoveTime < 10) return;
            that.lastMoveTime = now;

            // 核心拦截：未按左键 → 直接返回
            if (!that.mouseDown) {
                // 清除自动滚动定时器
                if (that.autoScrollTimer) {
                    clearInterval(that.autoScrollTimer);
                    that.autoScrollTimer = null;
                }
                return;
            }

            // 计算偏移（带防抖，减少微小移动误判）
            const currentDocX = e.clientX + window.scrollX;
            const currentDocY = e.clientY + window.scrollY;
            const dx = currentDocX - that.startX;
            const dy = currentDocY - that.startY;
            const moveDistance = Math.sqrt(dx * dx + dy * dy);

            // 阈值判断：未达到拖动阈值则不处理
            if (moveDistance < that.dragThreshold) return;

            // 标记为拖动
            that.isDragging = true;

            // 启动自动滚动（当鼠标靠近视口边缘时）
            that.handleAutoScroll(e);

            // 计算框选范围（使用文档坐标，不受滚动影响）
            const selectRect = {
                left: Math.min(that.startX, currentDocX),
                top: Math.min(that.startY, currentDocY),
                right: Math.max(that.startX, currentDocX),
                bottom: Math.max(that.startY, currentDocY)
            };

            // 判定操作类型
            const isVertical = Math.abs(dy) > Math.abs(dx);
            const isPositive = isVertical ? (dy > 0) : (dx > 0);
            const targetChecked = isPositive;

            // 实时同步状态（跳过禁用复选框）
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            const currentInSelect = new Set();

            checkboxes.forEach(cb => {
                if (cb.disabled) return; // 跳过禁用

                // 获取复选框的文档坐标（视口坐标 + 滚动偏移）
                const cbRect = cb.getBoundingClientRect();
                const cbDocRect = {
                    left: cbRect.left + window.scrollX,
                    top: cbRect.top + window.scrollY,
                    right: cbRect.right + window.scrollX,
                    bottom: cbRect.bottom + window.scrollY
                };

                // 判断是否在框选范围内（文档坐标对比）
                const isIn = !(
                    cbDocRect.right < selectRect.left ||
                    cbDocRect.left > selectRect.right ||
                    cbDocRect.bottom < selectRect.top ||
                    cbDocRect.top > selectRect.bottom
                );

                if (isIn) {
                    currentInSelect.add(cb);
                    that.allTargetCheckboxes.add(cb); // 记录所有被框选过的复选框
                    // 仅当状态变化时才更新，避免重复触发change事件
                    if (cb.checked !== targetChecked) {
                        cb.checked = targetChecked;
                        cb.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }
            });

            // 优化：仅重置不在当前范围且从未被框选过的复选框
            that.selectedBoxes.forEach(cb => {
                if (cb.disabled) return;
                // 只有既不在当前范围，也不在历史框选记录中，才恢复初始状态
                if (!currentInSelect.has(cb) && !that.allTargetCheckboxes.has(cb)) {
                    const originalState = that.originStates.get(cb);
                    if (cb.checked !== originalState) {
                        cb.checked = originalState;
                        cb.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }
            });

            that.selectedBoxes = currentInSelect;
        });

        // ========== 3. 左键松开：重置所有状态 ==========
        document.addEventListener('mouseup', (e) => {
            if (e.button !== 0) return;
            that.mouseDown = false;
            that.isDragging = false;
            that.originStates.clear();
            that.selectedBoxes.clear();
            that.allTargetCheckboxes.clear();
            
            // 清除自动滚动定时器
            if (that.autoScrollTimer) {
                clearInterval(that.autoScrollTimer);
                that.autoScrollTimer = null;
            }
        });

        // ========== 4. 鼠标离开窗口：强制重置 ==========
        document.addEventListener('mouseleave', () => {
            that.mouseDown = false;
            that.isDragging = false;
            that.originStates.clear();
            that.selectedBoxes.clear();
            that.allTargetCheckboxes.clear();
            
            // 清除自动滚动定时器
            if (that.autoScrollTimer) {
                clearInterval(that.autoScrollTimer);
                that.autoScrollTimer = null;
            }
        });

        // ========== 5. 监听滚动事件：补偿坐标偏移 ==========
        document.addEventListener('scroll', () => {
            if (that.isDragging) {
                // 滚动时更新起始坐标，保持框选范围相对文档不变
                const scrollDeltaX = window.scrollX - that.scrollOffsetX;
                const scrollDeltaY = window.scrollY - that.scrollOffsetY;
                if (scrollDeltaX !== 0 || scrollDeltaY !== 0) {
                    that.startX += scrollDeltaX;
                    that.startY += scrollDeltaY;
                    that.scrollOffsetX = window.scrollX;
                    that.scrollOffsetY = window.scrollY;
                }
            }
        });
    }

    // 处理拖动时的自动滚动
    handleAutoScroll(e) {
        const that = this;
        const viewportHeight = window.innerHeight;
        const viewportWidth = window.innerWidth;
        const edgeThreshold = 50; // 边缘触发距离
        const scrollSpeed = 8;    // 滚动速度

        // 清除之前的自动滚动定时器
        if (that.autoScrollTimer) {
            clearInterval(that.autoScrollTimer);
            that.autoScrollTimer = null;
        }

        // 计算滚动方向和速度
        let scrollX = 0;
        let scrollY = 0;

        // 水平滚动判断（左右边缘）
        if (e.clientX < edgeThreshold) {
            scrollX = -scrollSpeed; // 向左滚动
        } else if (e.clientX > viewportWidth - edgeThreshold) {
            scrollX = scrollSpeed;  // 向上滚动
        }

        // 垂直滚动判断（上下边缘）
        if (e.clientY < edgeThreshold) {
            scrollY = -scrollSpeed; // 向上滚动
        } else if (e.clientY > viewportHeight - edgeThreshold) {
            scrollY = scrollSpeed;  // 向下滚动
        }

        // 需要滚动时启动定时器
        if (scrollX !== 0 || scrollY !== 0) {
            that.autoScrollTimer = setInterval(() => {
                window.scrollBy(scrollX, scrollY);
            }, 16); // 约60fps
        }
    }
}

// 页面加载完成后初始化
document.addEventListener('DOMContentLoaded', () => {
    new CheckboxDragSelect();
});