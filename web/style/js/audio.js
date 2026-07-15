document.addEventListener('DOMContentLoaded', function() {
    const allAudio = document.querySelectorAll('audio');
    let currentAudio = null;
    
    // 为每个音频元素创建自定义控制界面
    allAudio.forEach(audio => {
        // 隐藏原生音频控件
        audio.controls = false;
        
        // 创建自定义控制容器
        const container = document.createElement('div');
        container.className = 'audio-container';
        
        // 创建播放/暂停按钮
        const playButton = document.createElement('button');
        playButton.className = 'audio-button play-pause';
        playButton.innerHTML = '<i class="fa fa-play"></i>';
        
        // 创建进度条容器
        const progressContainer = document.createElement('div');
        progressContainer.className = 'progress-container';
        
        // 创建进度条
        const progressBar = document.createElement('div');
        progressBar.className = 'progress-bar';
        
        // 创建已播放进度
        const progressFilled = document.createElement('div');
        progressFilled.className = 'progress-filled';
        
        // 创建缓冲进度
        const progressBuffer = document.createElement('div');
        progressBuffer.className = 'progress-buffer';
        
        // 创建时间显示
        const timeDisplay = document.createElement('div');
        timeDisplay.className = 'time-display';
        timeDisplay.textContent = '00:00 / 00:00';
        
        // 创建音量控制容器
        const volumeContainer = document.createElement('div');
        volumeContainer.className = 'volume-container';
        
        // 创建音量图标
        const volumeIcon = document.createElement('i');
        volumeIcon.className = 'fa fa-volume-up';
        
        // 创建音量滑块
        const volumeSlider = document.createElement('input');
        volumeSlider.type = 'range';
        volumeSlider.min = '0';
        volumeSlider.max = '1';
        volumeSlider.step = '0.05';
        volumeSlider.value = '1';
        volumeSlider.className = 'volume-slider';
        
        // 组装界面元素
        progressBar.appendChild(progressBuffer);
        progressBar.appendChild(progressFilled);
        progressContainer.appendChild(progressBar);
        volumeContainer.appendChild(volumeIcon);
        volumeContainer.appendChild(volumeSlider);
        
        container.appendChild(playButton);
        container.appendChild(progressContainer);
        container.appendChild(timeDisplay);
        container.appendChild(volumeContainer);
        
        // 将自定义控件插入到音频元素后面
        audio.parentNode.insertBefore(container, audio.nextSibling);
        
        // 更新播放/暂停按钮状态
        function updatePlayButton() {
            if (audio.paused) {
                playButton.innerHTML = '<i class="fa fa-play"></i>';
                playButton.classList.remove('playing');
            } else {
                playButton.innerHTML = '<i class="fa fa-pause"></i>';
                playButton.classList.add('playing');
            }
        }
        
        // 更新进度条
        function updateProgress() {
            // 更新播放进度
            const percent = (audio.currentTime / audio.duration) * 100;
            progressFilled.style.width = `${percent}%`;
            
            // 更新缓冲进度
            if (audio.buffered.length > 0) {
                const bufferedEnd = audio.buffered.end(audio.buffered.length - 1);
                const duration = audio.duration;
                const loadPercent = (bufferedEnd / duration) * 100;
                progressBuffer.style.width = `${loadPercent}%`;
            }
            
            // 更新时间显示
            const currentMinutes = Math.floor(audio.currentTime / 60);
            const currentSeconds = Math.floor(audio.currentTime % 60);
            const durationMinutes = Math.floor(audio.duration / 60);
            const durationSeconds = Math.floor(audio.duration % 60);
            
            timeDisplay.textContent = `${currentMinutes.toString().padStart(2, '0')}:${currentSeconds.toString().padStart(2, '0')} / ${durationMinutes.toString().padStart(2, '0')}:${durationSeconds.toString().padStart(2, '0')}`;
        }
        
        // 跳转到进度条点击位置
        function scrub(e) {
            // 使用保存的进度条边界信息
            const rect = progressBar.getBoundingClientRect();
            // 限制x坐标在进度条范围内
            const x = Math.max(0, Math.min(e.clientX - rect.left, rect.width));
            const scrubTime = (x / rect.width) * audio.duration;
            
            // 更新音频时间
            audio.currentTime = scrubTime;
        }
        
        // 更新音量图标
        function updateVolumeIcon() {
            if (audio.volume === 0 || audio.muted) {
                volumeIcon.className = 'fa fa-volume-off';
            } else if (audio.volume < 0.5) {
                volumeIcon.className = 'fa fa-volume-down';
            } else {
                volumeIcon.className = 'fa fa-volume-up';
            }
        }
        
        // 事件监听
        playButton.addEventListener('click', () => {
            if (audio.paused) {
                audio.play();
            } else {
                audio.pause();
            }
        });
        
        audio.addEventListener('play', () => {
            // 如果有正在播放的音频，暂停它
            if (currentAudio && currentAudio !== audio) {
                currentAudio.pause();
            }
            // 更新当前播放的音频
            currentAudio = audio;
            updatePlayButton();
        });
        
        audio.addEventListener('pause', updatePlayButton);
        audio.addEventListener('timeupdate', updateProgress);
        audio.addEventListener('loadedmetadata', updateProgress);
        audio.addEventListener('progress', updateProgress);
        
        // 进度条事件监听
        let isDragging = false;
        let progressBarRect = null;
        
        // 改进的进度条拖拽处理
        progressBar.addEventListener('mousedown', (e) => {
            isDragging = true;
            progressBarRect = progressBar.getBoundingClientRect();
            scrub(e);
            progressBar.classList.add('scrubbing');
            // 阻止默认行为和事件冒泡，防止拖拽时选中文本
            e.preventDefault();
            e.stopPropagation();
        });
        
        // 使用document监听mousemove和mouseup，确保可以捕获鼠标在任何位置的释放
        document.addEventListener('mousemove', (e) => {
            if (isDragging) {
                scrub(e);
            }
        });
        
        document.addEventListener('mouseup', () => {
            if (isDragging) {
                isDragging = false;
                progressBar.classList.remove('scrubbing');
                progressBarRect = null;
            }
        });
        
        // 阻止拖拽时可能出现的页面滚动
        document.addEventListener('dragover', (e) => {
            if (isDragging) {
                e.preventDefault();
            }
        });
        
        // 阻止拖拽时的默认行为
        progressBar.addEventListener('dragstart', (e) => {
            e.preventDefault();
        });
        
        // 音量控制事件
        volumeSlider.addEventListener('input', () => {
            audio.volume = volumeSlider.value;
            audio.muted = false;
            updateVolumeIcon();
        });
        
        volumeIcon.addEventListener('click', () => {
            audio.muted = !audio.muted;
            if (audio.muted) {
                volumeSlider.dataset.volume = volumeSlider.value;
                volumeSlider.value = 0;
            } else {
                volumeSlider.value = volumeSlider.dataset.volume || 1;
            }
            updateVolumeIcon();
        });        
    });
});