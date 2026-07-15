  tinymce.init({
    selector: "#rowtext",
      plugins: "autolink link emoticons quickbars",
      toolbar: "styleselect |bold link emoticons removeformat",
      language: "zh_CN",
      language_url: "/style/tinymce/langs/zh_CN.js",
      height: 300,//高度
      menubar: false,//菜单栏
      branding: false,//底部版权
      statusbar: false,//状态栏
      toolbar_mode: "floating",//工具栏
      toolbar_sticky: false,//粘性工具栏
      preview_styles: false,//预览样式
      skin: "oxide",//主题
      elementpath: false,//路径栏
      promotion: false,//广告
      quickbars_insert_toolbar:false,//内容快速插入配置
      link_default_target: "_blank",
      quickbars_selection_toolbar: 'bold link removeformat',//内容快速操作配置
      placeholder: "摘要(简介)……",//占位符
      content_css:'/style/css/tinymce.css',
      license_key: 'gpl',
  });