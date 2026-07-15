document.addEventListener('DOMContentLoaded', function() {
    /**
     * 查看驳回原因
     */
    const notos = document.querySelectorAll('.noto');
    const subdialog = document.getElementById('subdialog');
    const sub_no_mesg = document.getElementById('sub_no_mesg');
    let sub_mesg = '很遗憾，你的话题没有通过审核，具体原因请联系管理员进行了解。';
    if(notos&&notos.length>0){
        notos.forEach(function(noto) {
            noto.addEventListener('click', function() {
                const nototext = noto.getAttribute('data-text');
                if(nototext){
                    sub_mesg = nototext;
                }
                if(subdialog){
                    subdialog.showModal();
                    subdialog.style.display = 'block';
                }
                if(sub_no_mesg){
                    sub_no_mesg.innerHTML = '<div class="span">理由：</div>'+sub_mesg;
                }
            });
        });
    }
    if(subdialog){
        subdialog.addEventListener('click', function(e) {
            subdialog.close();
            subdialog.style.display = 'none';
            if(sub_no_mesg){
                    sub_no_mesg.innerHTML = "";
            }
        });
    }
    /**
     * 全选/全不选
     */
    const checkBoxes = document.querySelectorAll('input[type="checkbox"][name="id"]');
    const allcheckbox = document.getElementById('allcheckbox');
    if(allcheckbox&&checkBoxes.length>0){
        allcheckbox.addEventListener('click', function() {
            const checkedCount = Array.from(checkBoxes).filter(checkbox => checkbox.checked).length;
            const isAllChecked = checkedCount === checkBoxes.length;
            const targetChecked = !isAllChecked;         
            checkBoxes.forEach(checkbox => {
                checkbox.checked = targetChecked;
            });
            this.checked = targetChecked;
        });
        checkBoxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checkedCount = Array.from(checkBoxes).filter(cb => cb.checked).length;
                allcheckbox.checked = checkedCount === checkBoxes.length;
            });
        });
    }
    /**
     * 批量删除
     */
    function isNonNegativeInteger(value) {
        if (value === null || value === undefined || typeof value === 'boolean' || typeof value === 'object') {
            return false;
        }
        const strValue = String(value).trim();
        if (strValue === '') {
            return false;
        }
        const integerReg = /^[0-9]+$/;
        if (!integerReg.test(strValue)) {
            return false;
        }
        const numValue = Number(strValue);
        return Number.isInteger(numValue) && numValue > 0;
    }
    function msgalert(text,code){
        if(code == 200){
            alert('<font>(ô‿ô)</font> '+text);
        }else{
            alert('<font>(｡ŏ_ŏ)</font> '+text);
        }
    }
    const del_all = document.getElementById('allcheckboxdel');
    if(del_all){
        del_all.addEventListener('click', function() {
            const checkedIds = Array.from(checkBoxes).filter(checkbox => checkbox.checked).map(checkbox => checkbox.value);
            if(checkedIds.length>0){
                const confirmDelete = prompt('危险操作，请输入“确定删除”进行确认？');
                if (confirmDelete=="确定删除") {
                    //判断数组的每一个值是否都是正整数
                    for (let i = 0; i < checkedIds.length; i++) {
                        if (!isNonNegativeInteger(checkedIds[i])) {
                            msgalert('参数错误！',500)
                            return;
                        }
                    }
                   const checkedIdsString = checkedIds.join(',');//将数组转换为字符串
                   $.ajax({
                        url: '/subject/admin/alldel_sub.php',
                        type: 'POST',
                        data: {
                            ids:checkedIdsString
                        },
                        success: function(alldel) {
                            if(alldel == 200){
                               window.location.reload();
                            }else if(alldel == 500){
                                msgalert('操作错误！',500)
                            }else{
                                msgalert('删除失败！',500)
                            }
                        }
                    });

                }
            }else{
               msgalert('请选择要删除的项！',500)
            }
        })
    }
    /**
     * 批量审核
     */
    const allcheckboxexe = document.getElementById('allcheckboxexe');
    if(allcheckboxexe){
        allcheckboxexe.addEventListener('click', function() {
            const checkedIdsx = Array.from(checkBoxes).filter(checkbox => checkbox.checked).map(checkbox => checkbox.value);
            if(checkedIdsx.length>0){
                for (let i = 0; i < checkedIdsx.length; i++) {
                    if (!isNonNegativeInteger(checkedIdsx[i])) {
                        msgalert('参数错误！',500)
                        return;
                    }
                }
                const checkedIdsStringx = checkedIdsx.join(',');//将数组转换为字符串
                const confirmDeletex = prompt('输入状态(三选一)：通过、驳回、待审核');
                let confirmDeletexnew='';//驳回理由
                let ifmun=0;
                if(confirmDeletex=="驳回"){
                    confirmDeletexnew = prompt('请输入驳回理由：');
                    ifmun = 2;
                }else if(confirmDeletex=="待审核"){
                    ifmun = 1;
                }else if(confirmDeletex=="通过"){
                    ifmun = 3;
                }else{}
                if(confirmDeletex=="驳回"||confirmDeletex=="通过"||confirmDeletex=="待审核"){
                    $.ajax({
                        url: '/subject/admin/newyes.php',
                        type: 'POST',
                        data: {
                            if:ifmun,//状态
                            ids:checkedIdsStringx,//ID
                            reason:confirmDeletexnew
                        },
                        success: function(newyes) {
                            if(newyes == 200){
                                window.location.reload();
                            }else if(newyes == 500){
                                msgalert('操作错误！',500)
                            }else{
                                msgalert('修改失败！',500)
                            }
                        }
                    });
                }              

            }else{
                msgalert('请选择要审核的项！',500)
            }
        })
    }
    /**
     * 批量置顶/加精
     */
    const alltop = document.getElementById('alltop');
    if(alltop){
        alltop.addEventListener('click', function() {
            const checkedIdsxx = Array.from(checkBoxes).filter(checkbox => checkbox.checked).map(checkbox => checkbox.value);
            if(checkedIdsxx.length>0){
                for (let i = 0; i < checkedIdsxx.length; i++) {
                    if (!isNonNegativeInteger(checkedIdsxx[i])) {
                        msgalert('参数错误！',500)
                        return;
                    }
                }
                const checkedIdsStringxx = checkedIdsxx.join(',');//将数组转换为字符串
                const confirmDeletexx = prompt('输入状态(三选一)：普通、精选、置顶');
      
                let ifmunx=1;
                if(confirmDeletexx=="精选"){
                    ifmunx = 2;
                }else if(confirmDeletexx=="置顶"){
                    ifmunx = 3;
                }else if(confirmDeletexx=="普通"){
                    ifmunx = 1;
                }else{
                    ifmunx = 1;
                }
                if(confirmDeletexx=="普通"||confirmDeletexx=="精选"||confirmDeletexx=="置顶"){
                    $.ajax({
                        url: '/subject/admin/newtop.php',
                        type: 'POST',
                        data: {
                            if:ifmunx,//状态
                            ids:checkedIdsStringxx,//ID
                        },
                        success: function(newtop) {
                            if(newtop == 200){
                                window.location.reload();
                            }else if(newtop == 500){
                                msgalert('操作错误！',500)
                            }else{
                                msgalert('修改失败！',500)
                            }
                        }
                    });
                }              

            }else{
                msgalert('请选择要操作的项！',500)
            }
        })
    }
    /**
     * 单删除
     */
    const deleteButtons = document.querySelectorAll('.udel');
    if (deleteButtons&&deleteButtons.length>0) {
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const sub_commentId = button.getAttribute('data-d');
                if(isNonNegativeInteger(sub_commentId)&&sub_commentId){
                    $.ajax({
                        url: '/subject/admin/del.php',
                        type: 'POST',
                        data: {
                            id:sub_commentId,
                        },
                        success: function(del) {
                            if(del == 200){
                                window.location.reload();
                            }else if(del == 500){
                                msgalert('操作错误！',500)
                            }else{
                                msgalert('删除失败！',500)
                            }
                        }
                    });
                }else{
                    msgalert('参数错误！',500)
                }
            })
        })
    }
})