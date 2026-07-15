const eyemesdivshow=document.getElementById("eyemesdivshow");//获取内容展示框
const eyemesa=document.querySelectorAll('.eyemes')//获取所有查看按钮
var bodyxx = document.body;
eyemesa.forEach(button => { //为按钮添加点击事件
    button.addEventListener('click', function(event) {
        const mestext = event.currentTarget.getAttribute('data-mestext'); //获取所点击按钮的内容
        const mesidr = event.currentTarget.getAttribute('data-mesidr'); //获取所点击按钮的内容
        //判断内容是否为空
        if (mestext && mesidr) {
            eyemesdivshow.innerHTML = mestext;
            const eyemesdiv = document.getElementById("eyemesdiv");//获取对话框
                 eyemesdiv.classList.add('loganime'); 
                eyemesdiv.showModal();
                bodyxx.style.cssText="overflow:hidden;";
               
                $.ajax({
                    url: '/inc/mesyestow.php', // ajax请求
                    type: 'POST',   // 请求类型
                    data: {
                        mesidr: mesidr,
                    },
                    success: function(mesy) {
                        if (mesy == 500){
                            console.log("私信操作错误！");
                        }else if (mesy == 200){

                       const redmes=document.getElementById(`redmes${mesidr}`);//获取未读框
                       if(redmes){ redmes.style.display="none"; }

                        }else if (mesy == 202){
                            
                        }else{
                            console.log("私信操作错误！");
                        }
    
                    }         
                });
                
        }
    })
})

const eyemesdivx=document.getElementById("eyemesdivx");//获取关闭按钮
if (eyemesdivx){

    eyemesdivx.onclick=function (){ //关闭对话框
        const eyemesdivxx = document.getElementById("eyemesdiv");//获取对话框
        eyemesdivxx.close();
        eyemesdivxx.classList.remove('loganime');
        eyemesdivshow.innerHTML = "";
        bodyxx.style.cssText="overflow:auto;";
    }


}