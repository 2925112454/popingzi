document.addEventListener('DOMContentLoaded', function() {
    function validateJavaScriptCode(code) {
        try {
          new Function('"use strict";' + code);
          return true;
        } catch (e) {
            console.log('你的自定义JS代码报错如下：'+e);
          return false;
        }
      }
    const adsimgdelbut=document.getElementById('adsimgdel');//清除冗余图片按钮
    if (adsimgdelbut){
        adsimgdelbut.onclick=function (e){
            e.preventDefault();//阻止默认行为
            if (confirm("该操作会删除没有被直接引用且在广告图片目录下储存的所有图片，是否继续？")) {
                $.ajax({
                    url: '/api/adsimgdel.php', // 请求地址
                    type: 'POST',   // 请求类型
                    dataType: 'json',//数据类型
                    success: function(adsdel) { // 成功回调函数
                        if(adsdel.code == 200){
                            alert("<font>(◕ܫ◕)</font> 没有冗余图片！");
                        }else if(adsdel.code == 500){
                            alert("<font>(｡ŏ_ŏ)</font> "+adsdel.msg+"");
                        }else if(adsdel.code == 300){
                            alert("<font>(◕ܫ◕)</font> "+adsdel.msg+"");
                        }else{
                            alert("<font>(｡ŏ_ŏ)</font> 程序错误！");
                            console.log(adsdel); 
                        }                           
                    }
            
                }) 
            }
        }
    }
    /* 顶部横幅 广告input参数 */
   const adsoff_hf=document.getElementById('adsoff-hf');//广告开关checkbox，未选中为0，选中为1
   const adsvip_hf=document.getElementById('adsvip-hf');//VIP免广告开关checkbox，未选中为0，选中为1
   const adsip_sy_hf=document.getElementById('adsip-sy-hf');//广告展现位置(首页)，未选中为空，选中为1
   const adsip_lb_hf=document.getElementById('adsip-lb-hf');//广告展现位置(列表页)，未选中为空，选中为2
   const adsip_row_hf=document.getElementById('adsip-row-hf');//广告展现位置(内容页)，未选中为空，选中为3
   const adsoip_so_hf=document.getElementById('adsoip-so-hf');//广告展现位置(搜索页)，未选中为空，选中为4
   const asdtime_hf=document.getElementById('asdtime-hf');// 广告展现时间
   const adsimg_hf= document.getElementById('adsimg-hf');//广告图片地址
   const adsurl_hf= document.getElementById('adsurl-hf');// 广告链接地址
   /* 内容页横幅 广告input参数 */
   const adsoff_rowhf=document.getElementById('adsoff-rowhf');
   const adsvip_rowhf=document.getElementById('adsvip-rowhf');
   const adsip_row_rowhf=document.getElementById('adsip-row-rowhf');// 广告展现位置(内容页)，未选中为空，选中为3(由于本身就是内容页特有的广告，所以展现位置只有1个)
   const asdtime_rowhf=document.getElementById('asdtime-rowhf');
   const adsimg_rowhf= document.getElementById('adsimg-rowhf');
   const adsurl_rowhf= document.getElementById('adsurl-rowhf');
   /* 右下角弹窗  广告input参数 */
   const adsoff_yxj=document.getElementById('adsoff-yxj');
   const adsvip_yxj=document.getElementById('adsvip-yxj');
   const adsip_sy_yxj=document.getElementById('adsip-sy-yxj');
   const adsip_lb_yxj=document.getElementById('adsip-lb-yxj');
   const adsip_row_yxj=document.getElementById('adsip-row-yxj');
   const adsoip_so_yxj=document.getElementById('adsoip-so-yxj');
   const asdtime_yxj=document.getElementById('asdtime-yxj');
   const adsimg_yxj= document.getElementById('adsimg-yxj');
   const adsurl_yxj= document.getElementById('adsurl-yxj');
   /* 右侧边栏  广告input参数 */
   const adsoff_ybl=document.getElementById('adsoff-ybl');
   const adsvip_ybl=document.getElementById('adsvip-ybl');
   const adsip_sy_ybl=document.getElementById('adsip-sy-ybl');
   const adsip_lb_ybl=document.getElementById('adsip-lb-ybl');
   const adsip_row_ybl=document.getElementById('adsip-row-ybl');
   const adsoip_so_ybl=document.getElementById('adsoip-so-ybl');
   const asdtime_ybl=document.getElementById('asdtime-ybl');
   const adsimg_ybl= document.getElementById('adsimg-ybl');
   const adsurl_ybl= document.getElementById('adsurl-ybl');
   /* 左侧悬浮  广告input参数 */
   const adsoff_left=document.getElementById('adsoff-left');
   const adsvip_left=document.getElementById('adsvip-left');
   const adsip_sy_left=document.getElementById('adsip-sy-left');
   const adsip_lb_left=document.getElementById('adsip-lb-left');
   const adsip_row_left=document.getElementById('adsip-row-left');
   const adsoip_so_left=document.getElementById('adsoip-so-left');
   const asdtime_left=document.getElementById('asdtime-left');
   const adsimg_left= document.getElementById('adsimg-left');
   const adsurl_left= document.getElementById('adsurl-left');
    /* 右侧悬浮  广告input参数 */
    const adsoff_right=document.getElementById('adsoff-right');
    const adsvip_right=document.getElementById('adsvip-right');
    const adsip_sy_right=document.getElementById('adsip-sy-right');
    const adsip_lb_right=document.getElementById('adsip-lb-right');
    const adsip_row_right=document.getElementById('adsip-row-right');
    const adsoip_so_right=document.getElementById('adsoip-so-right');
    const asdtime_right=document.getElementById('asdtime-right');
    const adsimg_right= document.getElementById('adsimg-right');
    const adsurl_right= document.getElementById('adsurl-right');
    /* 自定义JS*/
    const adstext_js= document.getElementById('adstext-js');// 自定义JS textarea
    const adsoff_js=document.getElementById('adsoff-js');
    const adsvip_js=document.getElementById('adsvip-js');
    const adsip_sy_js=document.getElementById('adsip-sy-js');
    const adsip_lb_js=document.getElementById('adsip-lb-js');
    const adsip_row_js=document.getElementById('adsip-row-js');
    const adsoip_so_js=document.getElementById('adsoip-so-js');
    const asdtime_js=document.getElementById('asdtime-js');

    // 将 textarea 转换为 CodeMirror 编辑器
    const editorjs = CodeMirror.fromTextArea(adstext_js, {
        mode: "javascript",    // 设置语言模式
        theme: "ayu-dark",      // 设置主题
        lineNumbers: true,     // 显示行号
        indentUnit: 4,         // 缩进单位为 4 个空格
        smartIndent: true,     // 智能缩进
        matchBrackets: true,    // 匹配括号
        styleActiveLine: true,// 高亮当前行
        lint: true,
        gutters: ["CodeMirror-lint-markers"],
      });

    /* 提交按钮*/
     const ads_button= document.getElementById('ads-button');
     
    const alldom = adsoff_hf&&adsvip_hf&&adsip_sy_hf&&adsip_lb_hf&&adsip_row_hf&&adsoip_so_hf&&asdtime_hf&&adsimg_hf&&adsurl_hf&&adsoff_rowhf&&adsvip_rowhf&&adsip_row_rowhf&&asdtime_rowhf&&adsimg_rowhf&&adsurl_rowhf&&adsoff_yxj&&adsvip_yxj&&adsip_sy_yxj&&adsip_lb_yxj&&adsip_row_yxj&&adsoip_so_yxj&&asdtime_yxj&&adsimg_yxj&&adsurl_yxj&&adsoff_ybl&&adsvip_ybl&&adsip_sy_ybl&&adsip_lb_ybl&&adsip_row_ybl&&adsoip_so_ybl&&asdtime_ybl&&adsimg_ybl&&adsurl_ybl&&adsoff_left&&adsvip_left&&adsip_sy_left&&adsip_lb_left&&adsip_row_left&&adsoip_so_left&&asdtime_left&&adsimg_left&&adsurl_left&&adsoff_right&&adsvip_right&&adsip_sy_right&&adsip_lb_right&&adsip_row_right&&adsoip_so_right&&asdtime_right&&adsimg_right&&adstext_js&&adsoff_js&&adsvip_js&&adsip_sy_js&&adsip_lb_js&&adsip_row_js&&adsoip_so_js&&asdtime_js;
     //判断所有DOM是否都存在
      if (alldom){
                   
            ads_button.addEventListener('click',function(e){
                e.preventDefault();
                /*  顶部横幅变量信息设置 */
                let adsoff_hf_new=0;// 广告开关，关闭为0，开启为1
                let adsvip_hf_new=0;// VIP免广告开关，关闭为0，开启为1
                let adsip_sy_hf_new="";// 广告展现位置(首页)，默认为空，选中为1
                let adsip_lb_hf_new="";// 广告展现位置(列表页)，默认为空，选中为2
                let adsip_row_hf_new="";// 广告展现位置(内容页)，默认为空，选中为3
                let adsoip_so_hf_new="";// 广告展现位置(搜索页)，默认为空，选中为4
                let adsarr_hf_new=[];// 广告展现位置数组
                if(adsoff_hf.checked==true){
                    adsoff_hf_new=1;
                }
                if(adsvip_hf.checked==true){
                    adsvip_hf_new=1;
                }
                if(adsip_sy_hf.checked==true){
                    adsip_sy_hf_new=1;
                }
                if(adsip_lb_hf.checked==true){
                    adsip_lb_hf_new=2;
                }
                if(adsip_row_hf.checked==true){
                    adsip_row_hf_new=3;
                }
                if(adsoip_so_hf.checked==true){
                    adsoip_so_hf_new=4;
                }
                adsarr_hf_new=[adsip_sy_hf_new,adsip_lb_hf_new,adsip_row_hf_new,adsoip_so_hf_new];
                adsarr_hf_new=adsarr_hf_new.filter(function(item){return item!=""});
                adsarr_hf_new=adsarr_hf_new.join(",");

                /* 内容页横幅变量信息设置*/
                let adsoff_rowhf_new=0;// 广告开关，关闭为0，开启为1
                let adsvip_rowhf_new=0;// VIP免广告开关，关闭为0，开启为1
                let adsip_row_rowhf_new="";// 广告展现位置(内容页)，默认为空，选中为3
                let adsarr_rowhf_new=[];// 广告展现位置数组
                if(adsoff_rowhf.checked==true){
                    adsoff_rowhf_new=1;
                }
                if(adsvip_rowhf.checked==true){
                    adsvip_rowhf_new=1;
                }
                if(adsip_row_rowhf.checked==true){
                    adsip_row_rowhf_new=3;
                }
                adsarr_rowhf_new=[adsip_row_rowhf_new];
                adsarr_rowhf_new=adsarr_rowhf_new.filter(function(item){return item!=""});
                adsarr_rowhf_new=adsarr_rowhf_new.join(",");

                /*  右下角弹窗变量信息设置 */
                let adsoff_yxj_new=0;// 广告开关，关闭为0，开启为1
                let adsvip_yxj_new=0;// VIP免广告开关，关闭为0，开启为1
                let adsip_sy_yxj_new="";// 广告展现位置(首页)，默认为空，选中为1
                let adsip_lb_yxj_new="";// 广告展现位置(列表页)，默认为空，选中为2
                let adsip_row_yxj_new="";// 广告展现位置(内容页)，默认为空，选中为3
                let adsoip_so_yxj_new="";// 广告展现位置(搜索页)，默认为空，选中为4
                let adsarr_yxj_new=[];// 广告展现位置数组
                if(adsoff_yxj.checked==true){
                    adsoff_yxj_new=1;
                }
                if(adsvip_yxj.checked==true){
                    adsvip_yxj_new=1;
                }
                if(adsip_sy_yxj.checked==true){
                    adsip_sy_yxj_new=1;
                }
                if(adsip_lb_yxj.checked==true){
                    adsip_lb_yxj_new=2;
                }
                if(adsip_row_yxj.checked==true){
                    adsip_row_yxj_new=3;
                }
                if(adsoip_so_yxj.checked==true){
                    adsoip_so_yxj_new=4;
                }
                adsarr_yxj_new=[adsip_sy_yxj_new,adsip_lb_yxj_new,adsip_row_yxj_new,adsoip_so_yxj_new];
                adsarr_yxj_new=adsarr_yxj_new.filter(function(item){return item!=""});
                adsarr_yxj_new=adsarr_yxj_new.join(",");

                /*  右边栏变量信息设置 */
                let adsoff_ybl_new=0;// 广告开关，关闭为0，开启为1
                let adsvip_ybl_new=0;// VIP免广告开关，关闭为0，开启为1
                let adsip_sy_ybl_new="";// 广告展现位置(首页)，默认为空，选中为1
                let adsip_lb_ybl_new="";// 广告展现位置(列表页)，默认为空，选中为2
                let adsip_row_ybl_new="";// 广告展现位置(内容页)，默认为空，选中为3
                let adsoip_so_ybl_new="";// 广告展现位置(搜索页)，默认为空，选中为4
                let adsarr_ybl_new=[];// 广告展现位置数组
                if(adsoff_ybl.checked==true){
                    adsoff_ybl_new=1;
                }
                if(adsvip_ybl.checked==true){
                    adsvip_ybl_new=1;
                }
                if(adsip_sy_ybl.checked==true){
                    adsip_sy_ybl_new=1;
                }
                if(adsip_lb_ybl.checked==true){
                    adsip_lb_ybl_new=2;
                }
                if(adsip_row_ybl.checked==true){
                    adsip_row_ybl_new=3;
                }
                if(adsoip_so_ybl.checked==true){
                    adsoip_so_ybl_new=4;
                }
                adsarr_ybl_new=[adsip_sy_ybl_new,adsip_lb_ybl_new,adsip_row_ybl_new,adsoip_so_ybl_new];
                adsarr_ybl_new=adsarr_ybl_new.filter(function(item){return item!=""});
                adsarr_ybl_new=adsarr_ybl_new.join(",");

                /*  左侧悬浮变量信息设置 */
                let adsoff_left_new=0;// 广告开关，关闭为0，开启为1
                let adsvip_left_new=0;// VIP免广告开关，关闭为0，开启为1
                let adsip_sy_left_new="";// 广告展现位置(首页)，默认为空，选中为1
                let adsip_lb_left_new="";// 广告展现位置(列表页)，默认为空，选中为2
                let adsip_row_left_new="";// 广告展现位置(内容页)，默认为空，选中为3
                let adsoip_so_left_new="";// 广告展现位置(搜索页)，默认为空，选中为4
                let adsarr_left_new=[];// 广告展现位置数组
                if(adsoff_left.checked==true){
                    adsoff_left_new=1;
                }
                if(adsvip_left.checked==true){
                    adsvip_left_new=1;
                }
                if(adsip_sy_left.checked==true){
                    adsip_sy_left_new=1;
                }
                if(adsip_lb_left.checked==true){
                    adsip_lb_left_new=2;
                }
                if(adsip_row_left.checked==true){
                    adsip_row_left_new=3;
                }
                if(adsoip_so_left.checked==true){
                    adsoip_so_left_new=4;
                }
                adsarr_left_new=[adsip_sy_left_new,adsip_lb_left_new,adsip_row_left_new,adsoip_so_left_new];
                adsarr_left_new=adsarr_left_new.filter(function(item){return item!=""});
                adsarr_left_new=adsarr_left_new.join(",");

                /*  右侧悬浮变量信息设置 */
                let adsoff_right_new=0;// 广告开关，关闭为0，开启为1
                let adsvip_right_new=0;// VIP免广告开关，关闭为0，开启为1
                let adsip_sy_right_new="";// 广告展现位置(首页)，默认为空，选中为1
                let adsip_lb_right_new="";// 广告展现位置(列表页)，默认为空，选中为2
                let adsip_row_right_new="";// 广告展现位置(内容页)，默认为空，选中为3
                let adsoip_so_right_new="";// 广告展现位置(搜索页)，默认为空，选中为4
                let adsarr_right_new=[];// 广告展现位置数组
                if(adsoff_right.checked==true){
                    adsoff_right_new=1;
                }
                if(adsvip_right.checked==true){
                    adsvip_right_new=1;
                }
                if(adsip_sy_right.checked==true){
                    adsip_sy_right_new=1;
                }
                if(adsip_lb_right.checked==true){
                    adsip_lb_right_new=2;
                }
                if(adsip_row_right.checked==true){
                    adsip_row_right_new=3;
                }
                if(adsoip_so_right.checked==true){
                    adsoip_so_right_new=4;
                }
                adsarr_right_new=[adsip_sy_right_new,adsip_lb_right_new,adsip_row_right_new,adsoip_so_right_new];
                adsarr_right_new=adsarr_right_new.filter(function(item){return item!=""});
                adsarr_right_new=adsarr_right_new.join(",");

                /*自定义JS 变量信息设置*/
                let adsoff_js_new=0;// 广告开关，关闭为0，开启为1
                let adsvip_js_new=0;// VIP免广告开关，关闭为0，开启为1
                let adsip_sy_js_new="";// 广告展现位置(首页)，默认为空，选中为1
                let adsip_lb_js_new="";// 广告展现位置(列表页)，默认为空，选中为2
                let adsip_row_js_new="";// 广告展现位置(内容页)，默认为空，选中为3
                let adsoip_so_js_new="";// 广告展现位置(搜索页)，默认为空，选中为4
                let adsarr_js_new=[];// 广告展现位置数组
                if(adsoff_js.checked==true){
                    adsoff_js_new=1;
                }
                if(adsvip_js.checked==true){
                    adsvip_js_new=1;
                }
                if(adsip_sy_js.checked==true){
                    adsip_sy_js_new=1;
                }
                if(adsip_lb_js.checked==true){
                    adsip_lb_js_new=2;
                }
                if(adsip_row_js.checked==true){
                    adsip_row_js_new=3;
                }
                if(adsoip_so_js.checked==true){
                    adsoip_so_js_new=4;
                }
                adsarr_js_new=[adsip_sy_js_new,adsip_lb_js_new,adsip_row_js_new,adsoip_so_js_new];
                adsarr_js_new=adsarr_js_new.filter(function(item){return item!=""});
                adsarr_js_new=adsarr_js_new.join(",");

                if(adsoff_hf_new===1){
                    if(!adsimg_hf.value.trim()){
                        alert('<font>(｡ŏ_ŏ)</font> 开启广告 图片不能为空！');
                        return false;
                    }
                }
                if(adsoff_rowhf_new===1){
                    if(!adsimg_rowhf.value.trim()){
                        alert('<font>(｡ŏ_ŏ)</font> 开启广告 图片不能为空！');
                        return false;
                    }
                }
                if(adsoff_yxj_new===1){
                    if(!adsimg_yxj.value.trim()){
                        alert('<font>(｡ŏ_ŏ)</font> 开启广告 图片不能为空！');
                        return false;
                    }
                }
                if(adsoff_ybl_new===1){
                    if(!adsimg_ybl.value.trim()){
                        alert('<font>(｡ŏ_ŏ)</font> 开启广告 图片不能为空！');
                        return false;
                    }
                }
                if(adsoff_left_new===1){
                    if(!adsimg_left.value.trim()){
                        alert('<font>(｡ŏ_ŏ)</font> 开启广告 图片不能为空！');
                        return false;
                    }
                }
                if(adsoff_right_new===1){
                    if(!adsimg_right.value.trim()){
                        alert('<font>(｡ŏ_ŏ)</font> 开启广告 图片不能为空！');
                        return false;
                    }
                }
                if(adsoff_js_new===1){
                    if(!editorjs.getValue().trim()){
                        alert('<font>(｡ŏ_ŏ)</font> 开启广告 JS内容不能为空！');
                        return false;
                    }
                }

                if(editorjs.getValue().trim()){
                    //判断是否包含<script>
                     if(editorjs.getValue().trim().indexOf("<script>")!=-1){
                        alert('<font>(｡ŏ_ŏ)</font> JS内容不能含有&lt;script&gt;标签！');
                        return false;
                    }
                    //判断是不是js代码
                     if(!validateJavaScriptCode(editorjs.getValue().trim())){
                        alert('<font>(｡ŏ_ŏ)</font> JS代码报错啦，看看控制台吧！');
                        return false;
                    }                     
                     
                }
                                                    $.ajax({
                                                        url: '/api/adsset.php', // 请求地址
                                                        type: 'POST',   // 请求类型
                                                        dataType: 'json',//数据类型
                                                        data: {
                                                            /* 横幅广告 */
                                                            off_hf: adsoff_hf_new,//开启、关闭广告，关闭为0，开启为1
                                                            vipoff_hf:adsvip_hf_new,//  VIP免广告开关，关闭为0，开启为1
                                                            ads_hf:adsarr_hf_new,//  广告展现位置
                                                            img_hf:adsimg_hf.value.trim(),// 广告图片
                                                            url_hf:adsurl_hf.value.trim(),// 广告链接
                                                            time_hf:asdtime_hf.value.trim(),// 广告时间
                                                            /* 内容页横幅广告 */
                                                            off_rowhf: adsoff_rowhf_new,//开启、关闭广告，关闭为0，开启为1
                                                            vipoff_rowhf:adsvip_rowhf_new,//  VIP免广告开关，关闭为0，开启为1
                                                            ads_rowhf:adsarr_rowhf_new,//  广告展现位置
                                                            img_rowhf:adsimg_rowhf.value.trim(),// 广告图片
                                                            url_rowhf:adsurl_rowhf.value.trim(),// 广告链接
                                                            time_rowhf:asdtime_rowhf.value.trim(),// 广告时间
                                                            /* 右下角弹窗广告 */
                                                            off_yxj: adsoff_yxj_new,//开启、关闭广告，关闭为0，开启为1
                                                            vipoff_yxj:adsvip_yxj_new,//  VIP免广告开关，关闭为0，开启为1
                                                            ads_yxj:adsarr_yxj_new,//  广告展现位置
                                                            img_yxj:adsimg_yxj.value.trim(),// 广告图片
                                                            url_yxj:adsurl_yxj.value.trim(),// 广告链接
                                                            time_yxj:asdtime_yxj.value.trim(),// 广告时间
                                                            /* 右边栏广告 */
                                                            off_ybl: adsoff_ybl_new,//开启、关闭广告，关闭为0，开启为1
                                                            vipoff_ybl:adsvip_ybl_new,//  VIP免广告开关，关闭为0，开启为1
                                                            ads_ybl:adsarr_ybl_new,//  广告展现位置
                                                            img_ybl:adsimg_ybl.value.trim(),// 广告图片
                                                            url_ybl:adsurl_ybl.value.trim(),// 广告链接
                                                            time_ybl:asdtime_ybl.value.trim(),// 广告时间
                                                            /* 左悬浮广告 */
                                                            off_left: adsoff_left_new,//开启、关闭广告，关闭为0，开启为1
                                                            vipoff_left:adsvip_left_new,//  VIP免广告开关，关闭为0，开启为1
                                                            ads_left:adsarr_left_new,//  广告展现位置
                                                            img_left:adsimg_left.value.trim(),// 广告图片
                                                            url_left:adsurl_left.value.trim(),// 广告链接
                                                            time_left:asdtime_left.value.trim(),// 广告时间
                                                            /* 右悬浮广告 */
                                                            off_right: adsoff_right_new,//开启、关闭广告，关闭为0，开启为1
                                                            vipoff_right:adsvip_right_new,//  VIP免广告开关，关闭为0，开启为1
                                                            ads_right:adsarr_right_new,//  广告展现位置
                                                            img_right:adsimg_right.value.trim(),// 广告图片
                                                            url_right:adsurl_right.value.trim(),// 广告链接
                                                            time_right:asdtime_right.value.trim(),// 广告时间
                                                            /* 自定义JS广告 */
                                                            off_js: adsoff_js_new,//开启、关闭广告，关闭为0，开启为1
                                                            vipoff_js:adsvip_js_new,//  VIP免广告开关，关闭为0，开启为1
                                                            ads_js:adsarr_js_new,//  广告展现位置
                                                            time_js:asdtime_js.value.trim(),// 广告时间
                                                            js:editorjs.getValue().trim(),//自定义JS广告
                                                          },
                                                                    success: function(adsset) { // 成功回调函数
                                                                      if(adsset.code == 200){
                                                                        alert("<font>(◕ܫ◕)</font> 保存成功！");
                                                                      }else if(adsset.code == 500){
                                                                        alert("<font>(｡ŏ_ŏ)</font> "+adsset.msg+"");
                                                                      }else{
                                                                        alert("<font>(｡ŏ_ŏ)</font> 程序错误！");
                                                                        console.log(adsset); 
                                                                    }
                                                              
                                                              
                                                                    }
                                                  
                                                    })
                
            });
      }
      
});