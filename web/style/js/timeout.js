var divxsd = document.getElementById('djsdiv');  
if(divxsd){
    var countdownInterval;  
    // 显示倒计时的函数  
    function showCountdown() {  
        // 如果时间已经到0，则停止倒计时  
        if (djstime <= 0) {  
            clearInterval(countdownInterval);  
            divxsd.innerHTML = "操作剩余时间：00:00";  
            return;  
        }  
          
        // 计算分钟和秒  
        var minutes = Math.floor(djstime / 60);  
        var seconds = djstime % 60;  
          
        // 格式化分钟和秒为两位数  
        minutes = minutes < 10 ? '0' + minutes : minutes;  
        seconds = seconds < 10 ? '0' + seconds : seconds;  
          
        // 更新显示的倒计时  
        divxsd.innerHTML = "操作剩余时间：" + minutes + ":" + seconds;  
          
        // 减少倒计时时间  
        djstime--;  
    }  
      
    // 开始倒计时  
    countdownInterval = setInterval(showCountdown, 1000); // 每隔1000毫秒（1秒）调用一次showCountdown函数  
}
