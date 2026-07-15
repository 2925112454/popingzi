document.addEventListener('DOMContentLoaded', () => {

    let carouselData = [];//幻灯片数据
    let slides = [];//幻灯片元素
    let indicatorsElements = [];//指示器元素
    let currentSlide = 0;//当前幻灯片索引
    let autoplayInterval;//定时器
    let isPlaying = true;//默认开启
    const autoplaySpeed = 5000;//5秒
    function setWithExpiry(key, value, ttl = 6 * 60 * 60 * 1000) { // 默认时间为6小时
            const now = new Date();
            const item = {
                value: value,
                expiry: now.getTime() + ttl
            };
            localStorage.setItem(key, JSON.stringify(item));
        }

        function getWithExpiry(key) {
            const itemStr = localStorage.getItem(key);
            if (!itemStr) return null;

            const item = JSON.parse(itemStr);
            const now = new Date();

            if (now.getTime() > item.expiry) {
                localStorage.removeItem(key); // 过期删除
                return null;
            }

            return item.value;
        }
        

        const localCarouselData = getWithExpiry('carouselData');
        if (localCarouselData && Array.isArray(localCarouselData)) {
                carouselData = localCarouselData;
                initCarousel(); // 数据就绪后再初始化
        }else{
        fetch('/api/get_carousel_data.php')
            .then(response => response.json())
            .then(data => {
                carouselData = data;
                setWithExpiry('carouselData', data, 24 * 60 * 60 * 1000); //  缓存数据，有效期24小时
                initCarousel(); // 初始化轮播
            })
            .catch(error => console.error('获取数据失败:', error));
        }
        


        // 初始化轮播
        function initCarousel() {

            // DOM元素
            const carousel = document.getElementById('carouselxx');
            const indicators = document.getElementById('carousel-indicators');
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            const mainElement  = document.getElementById('mainElement');

            if (!carousel || !indicators || !prevBtn || !nextBtn) {
                console.error("轮播所需 DOM 元素未找到");
                return;
            }

             let validSlideCount = 0; // 用于统计有效幻灯片数量

            // 创建轮播项
            carouselData.forEach((item, index) => {
                // 判断 img 是否为空
                if (!item.img) return; // 跳过 img 为空的项
                validSlideCount++;
                
                const slide = document.createElement('div');
                slide.className = 'carousel-item absolute inset-0';
                if (index === 0) slide.classList.add('active');

                slide.innerHTML = `
                    <a href="${item.url}" class="block h-full" target="_blank">
                        <img src="${item.img}" alt="${item.title}" class="w-full h-full object-cover">
                        <div class="carousel-gradient"></div>
                        <div class="carousel-content">
                            <h3 class="carousel-title">${item.title}</h3>
                            <p class="carousel-desc">${item.desc}</p>
                        </div>
                    </a>
                `;

                carousel.appendChild(slide);
                slides.push(slide);

                // 创建指示器
                const indicator = document.createElement('button');
                indicator.className = `carousel-indicator focus:outline-none ${index === 0 ? 'active' : ''}`;
                indicator.setAttribute('aria-label', `切换到第${index + 1}张图片`);
                indicator.addEventListener('click', () => goToSlide(index));
                indicators.appendChild(indicator);
                indicatorsElements.push(indicator);
            });

            // 如果没有有效轮播图
            if (validSlideCount === 0) {
                if (mainElement && mainElement.parentNode) {
                    mainElement.parentNode.removeChild(mainElement); // 移除 main 标签
                }
                return; // 不再执行后续轮播初始化
            }
            
            // 启动自动播放
            startAutoplay();
            handleSingleSlide(prevBtn, nextBtn, indicators);

            // 事件监听器
            prevBtn.addEventListener('click', prevSlide);
            nextBtn.addEventListener('click', nextSlide);
        
            // 鼠标悬停暂停自动播放
            carousel.addEventListener('mouseenter', stopAutoplay);
            carousel.addEventListener('mouseleave', () => {
                if (isPlaying) startAutoplay();
            });
        }

        function handleSingleSlide(prevBtn, nextBtn, indicators) {
            if (slides.length <= 1) {
                if (prevBtn) prevBtn.style.display = 'none';
                if (nextBtn) nextBtn.style.display = 'none';
                if (indicators) indicators.style.display = 'none';
                stopAutoplay();
            }
        }

        function goToSlide(index) {
            // 隐藏当前幻灯片
            slides[currentSlide].classList.remove('active');
            indicatorsElements[currentSlide].classList.remove('active');

            // 更新当前索引
            currentSlide = index;

            // 显示新幻灯片
            slides[currentSlide].classList.add('active');
            indicatorsElements[currentSlide].classList.add('active');

            //更新当前幻灯片的链接
            const currentLink = slides[currentSlide].querySelector('a');
            if (currentLink && carouselData[currentSlide]) {
                currentLink.setAttribute('href', carouselData[currentSlide].url);
            }
            // 重置自动播放
            resetAutoplay();
        }

        // 下一张幻灯片
        function nextSlide() {
            const nextIndex = (currentSlide + 1) % slides.length;
            goToSlide(nextIndex);
        }

        // 上一张幻灯片
        function prevSlide() {
            const prevIndex = (currentSlide - 1 + slides.length) % slides.length;
            goToSlide(prevIndex);
        }

        // 开始自动播放
        function startAutoplay() {
            autoplayInterval = setInterval(nextSlide, autoplaySpeed);
        }

        // 停止自动播放
        function stopAutoplay() {
            clearInterval(autoplayInterval);
        }

        // 重置自动播放
        function resetAutoplay() {
            stopAutoplay();
            if (isPlaying) startAutoplay();
        }
    });