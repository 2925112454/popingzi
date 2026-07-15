document.addEventListener('DOMContentLoaded', function() {
    const yqmalertinfo=document.getElementById('yqmalertinfo');//按钮
    const yqmalert_dialog=document.getElementById('yqm_dialog');//dialog弹窗
    const yqmalert_close=document.getElementById('yqm_zcxx');//关闭按钮
    if  (yqmalertinfo&&yqmalert_dialog&&yqmalert_close) {
        
        yqmalertinfo.onclick=function (){ //打开弹窗
            yqmalert_dialog.style.display="flex";
            yqmalert_dialog.showModal();//显示弹窗
            yqmalert_dialog.style.opacity="1";
        }
        yqmalert_close.onclick=function (){ 
            yqmalert_dialog.style.opacity="";
            yqmalert_dialog.style.display="";
            yqmalert_dialog.close();
        }
        yqmalert_dialog.addEventListener('click', function (e) {
            if (e.target === yqmalert_dialog) {
                yqmalert_dialog.style.opacity = "";
                yqmalert_dialog.style.display = "";
                yqmalert_dialog.close();
            }
        });

        
    }
})