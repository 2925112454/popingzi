const CACHE_NAME = 'vcode-v1';// 缓存名称
const ASSETS_TO_CACHE = [
    '/style/css/style.css',
    '/style/css/font-awesome-4.7.0/css/font-awesome.min.css',
    '/style/js/jquery-3.5.1.min.js',
    '/style/js/input.js',
    '/style/js/alert.js',
    '/style/js/login.js',
    '/pwa/offline.html'
];

const CUSTOM_PWA_TITLE = ''; 


function replaceTitleInHtml(response, isOffline = false) {
    if (!response) {
        return new Response('<h1>网络连接失败</h1>', {
            headers: { 'Content-Type': 'text/html' },
            status: 503
        });
    }

    const contentType = response.headers.get('content-type');
    if (!contentType || !contentType.includes('text/html')) {
        return response;
    }

    return response.text().then(html => {
         let newTitle;
        if (CUSTOM_PWA_TITLE) {
            newTitle = CUSTOM_PWA_TITLE;
        } else if (isOffline) {
            newTitle = '破瓶子网站';
        } else {
            const titleMatch = html.match(/<title>(.*?)<\/title>/i);
            newTitle = titleMatch ? titleMatch[1] : '破瓶子网站';
        }
        const newHtml = html.replace(
            /<title>(.*?)<\/title>/i, 
            `<title>${newTitle}</title>`
        );
        
        return new Response(newHtml, {
            headers: response.headers,
            status: response.status,
            statusText: response.statusText
        });
    });
}

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('开始缓存核心文件');
                return Promise.all(
                    ASSETS_TO_CACHE.map(url => {
                        return cache.add(url).catch(err => {
                            console.warn(`缓存文件失败: ${url}`, err);
                            return null;
                        });
                    })
                );
            })
            .then(() => {
                return self.skipWaiting();
            })
            .catch(err => {
                console.error('安装阶段失败', err);
            })
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('删除旧缓存:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => {
            return self.clients.claim();
        })
        .catch(err => {
            console.error('激活阶段失败', err);
        })
    );
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;
    const skipUrls = ['/', '/index.php', '/api/','/inc/']; 
    const requestUrl = new URL(event.request.url);
    const pathname = requestUrl.pathname;
    if (skipUrls.some(url => pathname.startsWith(url))) {
        event.respondWith(fetch(event.request).catch(() => {
            return caches.match('/offline.html').then(offlineResponse => {
                return replaceTitleInHtml(offlineResponse, true);
            });
        }));
        return;
    }
    if (event.request.url.includes('sw.js')) return;
    event.respondWith(
        caches.match(event.request)
            .then((cachedResponse) => {
                if (cachedResponse) {
                    return replaceTitleInHtml(cachedResponse);
                }

                return fetch(event.request)
                    .then((networkResponse) => {
                        if (networkResponse && networkResponse.ok) {
                            const responseClone = networkResponse.clone();
                            caches.open(CACHE_NAME).then((cache) => {
                                cache.put(event.request, responseClone);
                            });
                        }
                        return replaceTitleInHtml(networkResponse);
                    })
                    .catch(() => {
                        console.warn('网络请求失败，尝试返回离线页面');
                        return caches.match('/offline.html')
                            .then(offlineResponse => {
                                return replaceTitleInHtml(offlineResponse, true);
                            });
                    });
            })
    );
});