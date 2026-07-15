// 粘性导航栏(顶部)
function debounce(func, wait) {
  let timeout;
  return function() {
      const context = this;
      const args = arguments;
      clearTimeout(timeout);
      timeout = setTimeout(() => func.apply(context, args), wait);
  };
}
$(document).ready(function() {
  var navbar = $('#navbar');  // 获取导航栏
  var navbarHeight = navbar.outerHeight(); // 获取导航栏高度
  var sticky = navbar.offset().top;  // 获取导航栏距离顶部的距离
  // 创建占位元素
  var navbarPlaceholder = $('<div>').attr('id', 'navbar-placeholder').css({
    height: navbarHeight + 'px',
    display: 'none'
  });
  navbar.after(navbarPlaceholder); // 将占位元素插入到导航栏后面

  $(window).scroll(debounce(function() {
      stickyNavbar();
  }, 10));  // 滚动事件（增加防抖延迟时间）

  function stickyNavbar() {
      var scroll = $(window).scrollTop();  // 获取滚动条高度
      if (scroll >= sticky) {
          navbar.addClass('sticky');
          navbarPlaceholder.show(); // 显示占位元素，防止页面跳动
      } else {
          navbar.removeClass('sticky');
          navbarPlaceholder.hide(); // 隐藏占位元素
      }
  }
  // 初始化时立即执行一次
  stickyNavbar();
  // 窗口大小改变时更新导航栏高度和占位元素高度
  $(window).resize(function() {
    navbarHeight = navbar.outerHeight();
    navbarPlaceholder.css('height', navbarHeight + 'px');
    sticky = navbar.offset().top;
    stickyNavbar();
  });
});

//粘性右侧导航栏
$(document).ready(function(){  
    var navrightx = document.getElementById('navright');
      if(navrightx){
        var navright = $('#navright');
      var stickyrc = navright.offset().top;
      var stickyr = stickyrc - 120;  
      var navright_height = navright.outerHeight(true);   
      $(window).scroll(function() {stickyrnavright()}); 
       function stickyrnavright() {   
          var scrollr = $(window).scrollTop();   
          if (scrollr >= stickyr) {   
              navright.addClass('stickyr')   
          } else {   
              navright.removeClass('stickyr');   
          }   
      }   
    }
});

//返回顶部
$(document).ready(function(){  
    $(window).scroll(function () {
      if ($(this).scrollTop() > 500) {
        $("#backToTop").css("display", "block");
      }else {
        $("#backToTop").css("display", "none");
        $("#backToTop").css("top", "");
      }
    })
    $("#backToTop").click(function () {
      $("body,html").animate({
        scrollTop: 0
      }, 500);
    })
  });
  
  //设置翻页按钮禁止点击状态
var pagenobutton=document.querySelectorAll('.page-no-button');
if(pagenobutton){
  for(var i=0;i<pagenobutton.length;i++){
    pagenobutton[i].onclick=function(){
      return false;
    }
  }
}

/*美化底部栏定位*/
window.onload = function () {
  //获取body高度
  const bodyhight = document.body.clientHeight;
  //获取视口高度
  const windowhight = window.innerHeight;
  //判断body是否小于视口高度
  if (bodyhight < windowhight) {
      //获取footer标签(无id)
      const footerpo = document.querySelector('footer');
      if(footerpo){
        //为其添加一个class
        footerpo.classList.add('footer-absolute');
      }
  } 
};

//查找所有noscript标签
const noscripts = document.querySelectorAll('noscript');
if (noscripts&&noscripts.length>0){
  //将所有的noscript标签display设置为none
  for (let i = 0; i < noscripts.length; i++) {
      noscripts[i].style.display = 'none';
  }
}