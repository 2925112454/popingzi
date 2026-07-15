//更改默认提示框样式
function detaTitle(){
    var deta_title = document.querySelectorAll("[deta-title]");
    for (var i = 0; i < deta_title.length; i++){
        deta_title[i].setAttribute("deta-title",deta_title[i].getAttribute("title"));
        deta_title[i].removeAttribute("title");
    }
}
detaTitle();

function  outlogin(){
        window.location.href="/inc/loginout.php"; //退出登录
};


// 获取所有的img标签  
var images = document.querySelectorAll('#postimgnull');  
// 遍历每个图片  
images.forEach(function(image) {  
    // 监听图片的加载事件  
    image.addEventListener('load', function() {  
        // 图片加载完成，显示图片  
        this.style.display = 'block'; 
    });  
    // 监听图片的error事件  
    image.addEventListener('error', function() {  
        // 图片加载失败，显示“图片不存在”  
        this.style.display = 'none';  
        this.src = '/images/web/null.jpg';
    });  
});

// 登录提示
function loginFunction(){
    alert("<font>(,,•́ . •̀,,)</font> 请先登录！");
};
function iandiFunction(){
    alert("<font>(,,•́ . •̀,,)</font> 别对自己乱来哦！");
};
    var adminlink = document.getElementById('adminLink');  
    // 为链接元素添加点击事件监听器
    if (adminlink){
        adminlink.addEventListener('click', function(event) {  
            // 阻止链接的默认行为（例如跳转）  
            event.preventDefault();  
            // 弹出警告框  
            alert('<font>(,,•́ . •̀,,)</font> 该内容无法直接查看！');  
        }); 
    }  
    
    var errlink = document.getElementById('errlink');  
    // 为链接元素添加点击事件监听器  
    if (errlink){
        errlink.addEventListener('click', function(event) {  
            // 阻止链接的默认行为（例如跳转）  
            event.preventDefault();  
            // 弹出警告框  
            alert('<font>(,,•́ . •̀,,)</font> 错误操作！');  
        }); 
    }
 