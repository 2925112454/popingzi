function getCookie(name) {
    name = name.toLowerCase();
    const decodedCookies = decodeURIComponent(document.cookie);
    const cookies = decodedCookies.split('; ');
    for (let i = 0; i < cookies.length; i++) {
        const cookie = cookies[i];
        const eqPos = cookie.indexOf('=');
        const namePos = cookie.substring(0, eqPos).toLowerCase();
        if (namePos === name) {
            return cookie.substring(eqPos + 1);
        }
    }
    return null;
}
function setCookie(name, value, days) {
    let expires = "";
    if (days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = `${name}=${value}${expires}; path=/`;
}

    // DOM操作缓存
    const daynightIconContainer = document.createElement('div');
    daynightIconContainer.id = "dayornight";
    document.addEventListener('DOMContentLoaded', function() {
    document.body.appendChild(daynightIconContainer);
    
    const currentStyle = getCookie("style") || "0";
    const linkElement = document.getElementById('diystylecss');

    if (linkElement) {
        updateStylesAndIcon(currentStyle); // 根据cookie设置样式和图标
        // 添加点击事件监听器
        daynightIconContainer.addEventListener('click', function() {
            const newStyle = getCookie("style") === "1" ? "0" : "1";
            setCookie("style", newStyle, 360);
            updateStylesAndIcon(newStyle);
        });
    } else {
        console.error('linkElement not found');
    }

    function updateDayNightIcon(iconType) {
        const daynightIcon = document.getElementById("dayornight");
        if (daynightIcon) {
            daynightIcon.innerHTML = `<i class="fa fa-${iconType}-o" aria-hidden="true"></i>`;
            if (iconType === "sun") {
                daynightIcon.innerHTML += `<div class="daynighttext">切换亮色</div>`;
            } else {
                daynightIcon.innerHTML += `<div class="daynighttext">切换暗色</div>`;
            }

        }
    }

    function updateStylesAndIcon(style) {
        if (linkElement) {
            linkElement.href = style === "1" ? nightstyle : daystyle;
            updateDayNightIcon(style === "1" ? "sun" : "moon");
        }
    }

    daynightIconContainer.classList.add('hidden'); // 添加隐藏类

    // 添加鼠标移入/移出事件监听器
   daynightIconContainer.addEventListener('mouseenter', function() {
       daynightIconContainer.classList.add('show');// 显示图标
   });
   daynightIconContainer.addEventListener('mouseleave', function() {
       daynightIconContainer.classList.remove('show');// 隐藏图标
   });
});