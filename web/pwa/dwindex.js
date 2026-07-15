// 注册Service Worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js', { scope: '/' })
            .then((registration) => {
                console.log('ServiceWorker 注册成功: ', registration.scope);
                registration.update();
            })
            .catch((error) => {
                console.log('ServiceWorker 注册失败: ', error);
            });
    });
}