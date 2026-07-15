document.addEventListener('DOMContentLoaded', function () {
    const share_img_arr = [1, 2, 3, 4, 5]; //文章封面编号
    // 默认海报数据
    const defaultShareData = {
        title: "未知标题",
        img: "/images/web/share1.jpg",
        name: "佚名",
        type: "未知分类",
        text: "该文章暂无文字摘要……",
        isvideo: false
    };

    // ========== 接口请求缓存 ==========
    const shareDataCache = new Map(); // key:文章id, value:接口返回完整数据
    let currentShareId = null; // 当前弹窗正在展示的文章ID
    let currentShareInfo = { ...defaultShareData }; // 当前生效分享数据（社交分享使用）

    // DOM元素缓存
    const elementsarr = {
        sharelinks: document.querySelectorAll('.sharelink'),
        pldivid: document.querySelector('.pldiv'),
        rowscbutt: document.getElementById('rowsc'),
        sharebox: document.querySelector('.share'),
        sharelogbox: document.querySelector('.sharelogbox'),
        nowcurrentUrl: window.location.href,
        sharebody: document.getElementById('sharebody'),
        sharelogimg: document.querySelector('.sharelogimg'),
        shareimgxtime: document.getElementById('shareimgxtime'),
        sharelogboxcircle: document.getElementById('sharelogboxcircle'),
        goweibo: document.getElementById('goweibo'),//分享到微博按钮
        douban: document.getElementById('douban'),//分享到豆瓣按钮
        goqq: document.getElementById('goqq'),//分享到QQ按钮
        goqzone: document.getElementById('goqzone'),//分享到QQ空间按钮
        gotwitter: document.getElementById('gotwitter'),//分享到推特按钮
        gofacebook: document.getElementById('gofacebook'),//分享到脸书按钮
        goinstagram: document.getElementById('goinstagram'),//分享到 Instagram 按钮
        sharelinkurl: document.getElementById('sharelinkurl'),
        sharelogdown: document.getElementById('sharelogdown'),
        shareimgx: document.querySelector('.shareimgx'),
        shareuser: document.getElementById('shareuser'),
        sharetypename: document.getElementById('sharetype'),
        sharetexe: document.getElementById('sharetexe'),
        shareimgxisvideo: document.getElementById('shareimgxisvideo')
    };

    /* 海报UI渲染函数 */
    function newsharebox(img, user, type, text, isvideo) {
        const el = elementsarr;
        if (el.shareimgxisvideo) {
            el.shareimgxisvideo.style.display = isvideo ? "flex" : "none";
        }
        if (el.shareimgx) {
            el.shareimgx.style.background = `url("${img}") center/cover no-repeat`;
        }
        if (el.shareuser) el.shareuser.textContent = user;
        if (el.sharetypename) el.sharetypename.textContent = type;
        if (el.sharetexe) el.sharetexe.textContent = text;
    }

    /*海报生成图片函数*/
    function createPoster() {
        const el = elementsarr;
        if (!el.sharelogimg || !el.sharelogdown) return;

        const originWidth = 380;
        html2canvas(el.sharelogimg, {
            scale: 3,
            useCORS: true,
            allowTaint: false,
            letterRendering: true,
            backgroundColor: '#ffffff',
            logging: false
        }).then(canvas => {
            const scaleRate = originWidth / canvas.width;
            const targetW = originWidth;
            const targetH = canvas.height * scaleRate;
            const outCanvas = document.createElement('canvas');
            const ctx = outCanvas.getContext('2d');
            outCanvas.width = targetW;
            outCanvas.height = targetH;
            ctx.drawImage(
                canvas,
                0, 0, canvas.width, canvas.height,
                0, 0, targetW, targetH
            );
            const newbase64 = outCanvas.toDataURL('image/jpeg', 1);

            function downloadHandler() {
                const a = document.createElement('a');
                a.href = newbase64;
                a.download = '分享海报.jpg';
                a.click();
            }
            if (el.sharelogdown) {
                el.sharelogdown.disabled = false;
                el.sharelogdown.innerHTML = '海报已就绪';
                el.sharelogdown.style.background = "";
            }
            el.sharelogdown.removeEventListener('click', el.sharelogdown._downloadFn);
            el.sharelogdown._downloadFn = downloadHandler;
            el.sharelogdown.addEventListener('click', el.sharelogdown._downloadFn);
        }).catch(err => {
            if (el.sharelogdown) {
                el.sharelogdown.innerHTML = '海报生成失败';
                el.sharelogdown.style.background = "#969696";
                el.sharelogdown.disabled = true;
                el.sharelogdown.onclick = null;
            }
        });
    }

    // 关闭分享弹窗统一处理函数
    function closeSharePopup() {
        const el = elementsarr;
        if (el.sharelogbox) el.sharelogbox.style.display = "none";
        if (el.sharelogdown) {
            el.sharelogdown.innerHTML = '海报生成中……';
            el.sharelogdown.style.background = "#969696";
            el.sharelogdown.disabled = true;
            el.sharelogdown.onclick = null;
        }
    }

    // 弹窗背景点击关闭
    if (elementsarr.sharelogbox) {
        elementsarr.sharelogbox.addEventListener('click', function (event) {
            if (event.target === elementsarr.sharelogbox) {
                closeSharePopup();
            }
        });
    }
    // 关闭按钮
    if (elementsarr.sharelogboxcircle) {
        elementsarr.sharelogboxcircle.addEventListener('click', closeSharePopup);
    }

    // 加载分享数据（优先读取缓存，无缓存再请求接口）
    function loadShareData(id) {
        const el = elementsarr;
        currentShareId = id;
        const randomNum = share_img_arr[Math.floor(Math.random() * share_img_arr.length)];

        // 如果缓存存在，直接使用缓存，不再请求接口
        if (shareDataCache.has(id)) {
            const resData = shareDataCache.get(id);
            renderShareUI(resData, randomNum);
            return;
        }

        // 没有缓存，发起AJAX请求
        $.ajax({
            url: '/api/share.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function (res) {
                shareDataCache.set(id, res);
                renderShareUI(res, randomNum);
            },
            error: function () {
                currentShareInfo = { ...defaultShareData };
                newsharebox(
                    `/images/web/share${randomNum}.jpg`,
                    defaultShareData.name,
                    defaultShareData.type,
                    defaultShareData.text,
                    defaultShareData.isvideo
                );
                setTimeout(() => createPoster(), 120);
            }
        });
    }

    // 根据接口数据渲染海报UI + 更新全局分享信息
    function renderShareUI(res, randomNum) {
        const el = elementsarr;
        // 判断接口正常返回
        if (res && res.code === 200) {
            currentShareInfo = {
                title: res.title || defaultShareData.title,
                img: res.img ? res.img : `/images/web/share${randomNum}.jpg`,
                name: res.name ? res.name : defaultShareData.name,
                type: res.type ? res.type : defaultShareData.type,
                text: res.text ? res.text : defaultShareData.text,
                isvideo: res.isvideo === 1
            };
        } else {
            currentShareInfo = {
                ...defaultShareData,
                img: `/images/web/share${randomNum}.jpg`
            };
        }

        newsharebox(
            currentShareInfo.img,
            currentShareInfo.name,
            currentShareInfo.type,
            currentShareInfo.text,
            currentShareInfo.isvideo
        );
        setTimeout(() => createPoster(), 120);
    }

    // 将相对路径解析为带域名的绝对URL（仅用于社交分享pic参数）
    function resolveAbsoluteUrl(relativePath) {
        if (!relativePath) return '';
        // 已经是完整http/https链接，直接返回
        if (/^https?:\/\//i.test(relativePath)) {
            return relativePath;
        }
        try {
            // 利用浏览器原生URL对象解析，自动处理 ../ ./ / 等各种相对路径
            return new URL(relativePath, window.location.href).href;
        } catch (e) {
            return relativePath;
        }
    }

    // ===================== 社交分享跳转绑定 =====================
    function bindSocialShare() {
        const el = elementsarr;
        // 微博分享
        if(el.goweibo){
            el.goweibo.onclick = function(){
                const url = encodeURIComponent(el.nowcurrentUrl);
                const title = encodeURIComponent(currentShareInfo.title);
                const desc = encodeURIComponent(currentShareInfo.text);
                const picAbsolute = resolveAbsoluteUrl(currentShareInfo.img);
                const pic = encodeURIComponent(picAbsolute);
                const shareUrl = `https://service.weibo.com/share/share.php?url=${url}&title=${title}&pic=${pic}`;
                window.open(shareUrl,'_blank');
            }
        }
        // QQ
        if(el.goqq){
            el.goqq.onclick = function(){
                const url = encodeURIComponent(el.nowcurrentUrl);
                const title = encodeURIComponent(currentShareInfo.title);
                const desc = encodeURIComponent(currentShareInfo.text);
                const picAbsolute = resolveAbsoluteUrl(currentShareInfo.img);
                const pic = encodeURIComponent(picAbsolute);
                const shareUrl = `https://connect.qq.com/widget/shareqq/index.html?url=${url}&title=${title}&desc=${desc}&pics=${pic}`;
                window.open(shareUrl,'_blank');
            }
        }
        // 豆瓣分享
        if(el.douban){
            el.douban.onclick = function(){
                const url = encodeURIComponent(el.nowcurrentUrl);
                const title = encodeURIComponent(currentShareInfo.title);
                const desc = encodeURIComponent(currentShareInfo.text);
                const picAbsolute = resolveAbsoluteUrl(currentShareInfo.img);
                const pic = encodeURIComponent(picAbsolute);
                const shareUrl = `https://www.douban.com/share/service?href=${url}&name=${title}&text=${desc}&image=${pic}`;
                window.open(shareUrl,'_blank');
            }
        }
        // QQ空间
        if(el.goqzone){
            el.goqzone.onclick = function(){
                const url = encodeURIComponent(el.nowcurrentUrl);
                const title = encodeURIComponent(currentShareInfo.title);
                const desc = encodeURIComponent(currentShareInfo.text);
                const picAbsolute = resolveAbsoluteUrl(currentShareInfo.img);
                const pic = encodeURIComponent(picAbsolute);
                const shareUrl = `https://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=${url}&title=${title}&summary=${desc}&pics=${pic}`;
                window.open(shareUrl,'_blank');
            }
        }
        // Twitter X
        if(el.gotwitter){
            el.gotwitter.onclick = function(){
                const url = encodeURIComponent(el.nowcurrentUrl);
                const title = encodeURIComponent(currentShareInfo.title);
                const shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
                window.open(shareUrl,'_blank');
            }
        }
        // Facebook
        if(el.gofacebook){
            el.gofacebook.onclick = function(){
                const url = encodeURIComponent(el.nowcurrentUrl);
                const shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
                window.open(shareUrl,'_blank');
            }
        }
        // Instagram
        if(el.goinstagram){
            el.goinstagram.onclick = function(){
                const url = encodeURIComponent(el.nowcurrentUrl);
                const title = encodeURIComponent(currentShareInfo.title);
                const shareUrl = `https://t.me/share/url?url=${url}&text=${title}`;
                window.open(shareUrl,'_blank');
            }
        }
    }

    // 绑定各个sharelink点击事件
    if (elementsarr.sharelinks && elementsarr.sharelinks.length > 0) {
        elementsarr.sharelinks.forEach(function (sharelink) {
            sharelink.addEventListener('click', function () {
                const sharetype = sharelink.getAttribute('data-type');
                const el = elementsarr;

                if (sharetype == "share") {
                    // 打开分享弹窗
                    const sharetypeid = sharelink.getAttribute('data-id');
                    if (el.sharelogbox) {
                        el.sharelogbox.style.display = "flex";
                    }
                    // 设置弹窗时间
                    if (el.shareimgxtime) {
                        const nowtime = new Date();
                        const weekArr = ["周日", "周一", "周二", "周三", "周四", "周五", "周六"];
                        const pad = n => n < 10 ? '0' + n : n;
                        const month = pad(nowtime.getMonth() + 1);
                        const date = pad(nowtime.getDate());
                        const nowtimetxt = `${month}/${date}`;
                        el.shareimgxtime.textContent = `${weekArr[nowtime.getDay()]} ${nowtimetxt}`;
                    }
                    // 加载数据（带缓存判断）
                    loadShareData(sharetypeid);
                    // 这里定时器可以保留，只是现在bindSocialShare仅绑定事件，不计算参数
                    setTimeout(bindSocialShare,150);
                } else if (sharetype == "comm") {
                    //评论滚动
                    if (el.pldivid) {
                        el.pldivid.scrollIntoView({ behavior: "smooth" });
                    }
                } else if (sharetype == "star") {
                    //收藏
                    if (el.rowscbutt) {
                        el.rowscbutt.click();
                        if (sharelink.classList.contains('scyes')) {
                            sharelink.classList.remove('scyes');
                            sharelink.querySelector('span').textContent = "收藏";
                        } else {
                            sharelink.classList.add('scyes');
                            sharelink.querySelector('span').textContent = "取消收藏";
                        }
                    } else {
                        loginFunction();
                    }
                } else if (sharetype == "refresh") {
                    window.location.reload();
                } else if (sharetype == "back") {
                    window.history.back();
                }
            });
        });
    }

    // 侧边分享栏滚动吸顶逻辑
    if (elementsarr.sharebox) {
        const stickyTop = 150;
        let originY = 0;
        let originLeft = 0;
        let isFixed = false;

        function getOriginPos() {
            elementsarr.sharebox.style.position = "";
            elementsarr.sharebox.style.top = "";
            elementsarr.sharebox.style.left = "";
            const rect = elementsarr.sharebox.getBoundingClientRect();
            originY = rect.top + window.scrollY - stickyTop;
            originLeft = rect.left;
        }

        function handleScroll() {
            const scrollY = window.scrollY;
            if (scrollY >= originY) {
                if (!isFixed) {
                    elementsarr.sharebox.style.position = "fixed";
                    elementsarr.sharebox.style.top = stickyTop + "px";
                    elementsarr.sharebox.style.left = originLeft + "px";
                    isFixed = true;
                }
            } else {
                if (isFixed) {
                    elementsarr.sharebox.style.position = "";
                    elementsarr.sharebox.style.top = "";
                    elementsarr.sharebox.style.left = "";
                    isFixed = false;
                }
            }
        }

        getOriginPos();
        window.addEventListener('scroll', handleScroll);
        window.addEventListener('resize', () => {
            getOriginPos();
            handleScroll();
        });
    }

    // 生成二维码
    if (elementsarr.sharebody) {
        const qrSize = 256;
        QRCode.toDataURL(elementsarr.nowcurrentUrl, {
            width: qrSize,
            height: qrSize,
            margin: 2,
            color: {
                dark: '#000000',
                light: '#ffffff'
            }
        }).then(function (base64Img) {
            elementsarr.sharebody.style.backgroundImage = 'url(' + base64Img + ')';
            elementsarr.sharebody.style.backgroundSize = 'contain';
            elementsarr.sharebody.style.backgroundRepeat = 'no-repeat';
            elementsarr.sharebody.style.backgroundPosition = 'center center';
        }).catch(function () {
            //console.error('二维码生成失败', err);
        });
    }

    // 复制链接
    if (elementsarr.sharelinkurl) {
        elementsarr.sharelinkurl.value = elementsarr.nowcurrentUrl;
        if (!elementsarr.sharelinkurl.dataset.bindCopy) {
            elementsarr.sharelinkurl.dataset.bindCopy = '1';
            elementsarr.sharelinkurl.addEventListener('click', async function () {
                const originText = elementsarr.nowcurrentUrl;
                try {
                    await navigator.clipboard.writeText(originText);
                    elementsarr.sharelinkurl.value = "复制成功！";
                } catch (err) {
                    elementsarr.sharelinkurl.select();
                    const success = document.execCommand('copy');
                    if (success) {
                        elementsarr.sharelinkurl.value = "复制成功！";
                    } else {
                        elementsarr.sharelinkurl.value = "复制失败，请手动复制";
                    }
                    elementsarr.sharelinkurl.blur();
                }
                setTimeout(() => {
                    elementsarr.sharelinkurl.value = originText;
                }, 1000);
            });
        }
    }
});