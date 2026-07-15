document.addEventListener('DOMContentLoaded', function () {
    const eyeplal=document.querySelectorAll('.eyeplal');//获取所有eyeplal元素
    const eyescomment=document.getElementById('eyescomment');//获取dialog
    const eyescommentclose=document.getElementById('eyescommentclose');//获取关闭按钮
    const eyescommenttext=document.getElementById('eyescommenttext');//获取评论显示区域
    const commadmin=document.getElementById('commadmin');//发布者显示区域
    const commhf=document.getElementById('commhf');//回复数显示区域
    const commtop=document.getElementById('commtop');//点赞数显示区域
    const conlink=document.getElementById('commlink');//来源链接
    if (eyeplal.length>0){
        //点击事件
        eyeplal.forEach(function(eyeplal){
            eyeplal.addEventListener('click',function(){
                const datatxt=eyeplal.getAttribute('data-txt');//评论内容
                const datatop=eyeplal.getAttribute('data-top');//点赞数
                const datahf=eyeplal.getAttribute('data-hf');//回复数
                const dataadmin=eyeplal.getAttribute('data-admin');//发布者昵称
                const dataconif=eyeplal.getAttribute('data-plif');//评论类别,2为公告评论，1为文章评论
                const dataconplid=eyeplal.getAttribute('data-rowid');//文章id
                if (dataconplid>0&&conlink&&eyescomment&&eyescommentclose&&eyescommenttext&&commadmin&&commhf&&commtop){
                    //显示dialog
                    eyescomment.showModal();
                    eyescomment.style.display='flex';
                    eyescommenttext.innerHTML=datatxt;
                    commadmin.innerHTML=dataadmin;
                    commhf.innerHTML=datahf;
                    commtop.innerHTML=datatop;
                    if (dataconif==2){
                        conlink.href='/anctshow.php?id='+dataconplid;
                    }else{
                        conlink.href='/show.php?id='+dataconplid;
                    }
                }

            })
        })
    }

    //关闭事件
    eyescommentclose.addEventListener('click',function(){
        eyescomment.close();
        eyescomment.style.display='none';
    });
    //点击背景也能关闭
    eyescomment.addEventListener('click',function(event){
        if (event.target===eyescomment){
            eyescomment.close();
            eyescomment.style.display='none';
        }
    })

})

/*
*
*
********↓↓↓删除评论↓↓↓********
*
*
*/
function removeComment(dplid,dtype){
    if (dplid>0 && dplid!=='' && dplid!==null && dplid!==undefined && dplid.length!==0 && /^[1-9]\d*$/.test(dplid)){
        if (dtype==1||dtype==2){
            if (prompt('请输入“确定删除”，以此来确认您确实需要这么做！')=='确定删除'){
                $.ajax({
                    url: '/inc/commentdel.php', // 请求地址
                    type: 'POST',   // 请求类型
                    data: {
                        id:dplid,//评论id
                        type:dtype,//评论类型：1为公告评论，2为文章评论
                    },
                            success: function(delcomm) { // 成功回调函数
                                if(delcomm == 500){
                                    alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                                    
                                }else if(delcomm == 200){
                                    alert("<font>(◕ܫ◕)</font> 删除成功！");
                                    setTimeout(function() {
                                        location.reload();
                                    }, 1000);//刷新当前页面
                                }else if(delcomm == 404){
                                    alert("<font>(｡ŏ_ŏ)</font> 评论不存在！");
                                }else if(delcomm == 600){
                                    alert("<font>(｡ŏ_ŏ)</font> 删除失败！");
                                }else if(delcomm == 601){
                                    alert("<font>(｡ŏ_ŏ)</font> 评论下存在无法删除的回复！");
                                }else{
                                    alert("<font>(｡ŏ_ŏ)</font> 服务器出现错误！");
                                    console.log(delcomm);
                                }
                            }
                });
            }
        }else{
            alert("<font>(｡ŏ_ŏ)</font> 删除失败！");
        }
    }
}

const dgg=document.querySelectorAll('.dgg');//所有删除公告评论按钮
const dpl=document.querySelectorAll('.dpl');//所有删除文章评论按钮

if (dgg.length>0){
    //删除公告评论
    dgg.forEach(function(dgg){
        dgg.addEventListener('click',function(){
            const dggid=dgg.getAttribute('data-d');//评论id
            if (dggid>0 && dggid!=='' && dggid!==null && dggid!==undefined && dggid.length!==0 && /^[1-9]\d*$/.test(dggid)){
                removeComment(dggid,1);
            }
        })
    })
}

if (dpl.length>0){
    //删除文章评论
    dpl.forEach(function(dpl){
        dpl.addEventListener('click',function(){
            const dplid=dpl.getAttribute('data-d');//评论id
            if (dplid>0 && dplid!=='' && dplid!==null && dplid!==undefined && dplid.length!==0 && /^[1-9]\d*$/.test(dplid)){
                removeComment(dplid,2);
            }
        })
    })
}