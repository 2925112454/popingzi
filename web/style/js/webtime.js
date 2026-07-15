    if(nowtime){
        const  nowtimedom = document.getElementById('webtime');//显示区域
        if(nowtimedom){
            nowtimedom.innerHTML = nowtime;
            setInterval(function(){
                // 将字符串转换为Date对象
                const date = new Date(nowtime);
                // 增加1秒
                date.setSeconds(date.getSeconds() + 1);
                // 格式化回字符串
                nowtime = formatDateTime(date);
                // 更新显示
                nowtimedom.innerHTML = nowtime;
            },1000);

        }
    }
// 辅助函数：将Date对象格式化为"YYYY-MM-DD HH:MM:SS"
function formatDateTime(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const seconds = String(date.getSeconds()).padStart(2, '0');
    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}