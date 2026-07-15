document.addEventListener("DOMContentLoaded", function() {  
    var tabs = document.querySelectorAll(".tab");  
    var contents = document.querySelectorAll(".content");  
    tabs.forEach(function(tab, index) {  
      tab.addEventListener("click", function() {  
        // 移除所有选项卡的活跃状态  
        tabs.forEach(function(tab) {  
          tab.classList.remove("active");  
        });  
        // 隐藏所有内容  
        contents.forEach(function(content) {  
          content.classList.remove("active");  
        });  
        // 添加活跃状态到被点击的选项卡  
        tab.classList.add("active");  
        // 显示对应的内容  
        var contentId = "content" + (index + 1);  
        var content = document.getElementById(contentId);  
        content.classList.add("active");  
      });  
    });  
  });