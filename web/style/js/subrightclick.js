$(function() {
    function msgalertxx(text,code){
        if(code == 200){
            alert('<font>(ô‿ô)</font> '+text);
        }else{
            alert('<font>(｡ŏ_ŏ)</font> '+text);
        }
    };
    
    const customMenu = $('#customMenu');
    let currentCheckbox = null;

    // 给所有复选框绑定右键
    $(document).on('contextmenu', 'input[type="checkbox"][name="id"]', function(e) {
        e.preventDefault();
        currentCheckbox = $(this);
        customMenu.css({
            left: e.pageX + 10 + 'px',
            top: e.pageY + 'px',
            display: 'block'
        });
    });

    // 点击空白处隐藏右键菜单
    $(document).click(function() {
        customMenu.hide();
    });
    // 右键非目标复选框时隐藏菜单
    $(document).contextmenu(function(e) {
        if (!$(e.target).is('input[type="checkbox"][name="id"]')) {
            customMenu.hide();
        }
    });

    // 通过审核（ifmun=3）
    $('#menuCheck').click(function() {
        if (currentCheckbox) {
            const val = currentCheckbox.val();
            sendAuditAjax(3, val, ''); // 通过审核无需理由
        }
    });

    // 等待审核（ifmun=1）
    $('#menuCheckx').click(function() {
        if (currentCheckbox) {
            const val = currentCheckbox.val();
            sendAuditAjax(1, val, ''); // 等待审核无需理由
        }
    });

    // 驳回审核（ifmun=2）- 需要输入驳回理由
    $('#menuCheckxx').click(function() {
        if (currentCheckbox) {
            const val = currentCheckbox.val();
            // 弹出输入框，允许为空，取消则不执行操作
            const rejectReason = prompt('请输入驳回理由：') || '';
            if (rejectReason !== null) {
                sendAuditAjax(2, val, rejectReason);
            }
        }
    });

    // 置顶话题（ifmunx=3）
    $('#menuCheckxxx').click(function() {
        if (currentCheckbox) {
            const val = currentCheckbox.val();
            sendTopicTypeAjax(3, val);
        }
    });

    // 加精话题（ifmunx=2）
    $('#menuCheckxxxx').click(function() {
        if (currentCheckbox) {
            const val = currentCheckbox.val();
            sendTopicTypeAjax(2, val);
        }
    });

    // 普通话题（ifmunx=1）
    $('#menuCheckxxxxx').click(function() {
        if (currentCheckbox) {
            const val = currentCheckbox.val();
            sendTopicTypeAjax(1, val);
        }
    });

    /**
     * 审核状态
     * @param {Number} ifmun 审核状态 1-等待审核 2-驳回审核 3-通过审核
     * @param {String} id 话题ID
     * @param {String} reason 驳回理由（仅驳回时有效）
     */
    function sendAuditAjax(ifmun, id, reason) {
        // 验证参数
        if (![1, 2, 3].includes(ifmun)) {
            alert("<font>(｡ŏ_ŏ)</font> 参数错误！");
            return;
        }
        const idStr = String(id).trim();
        const intReg = /^[1-9]\d*$/;
        if (!intReg.test(idStr)) {
            alert("<font>(｡ŏ_ŏ)</font> 参数错误！");
            return;
        }

        $.ajax({
            url: '/subject/admin/newyes.php',
            type: 'POST',
            data: {
                if: ifmun,
                ids: idStr,
                reason: reason
            },
            success: function(newyes) {
                if (newyes == 200) {
                    window.location.reload();
                } else if (newyes == 500) {
                    msgalertxx('操作错误！', 500);
                } else {
                    msgalertxx('修改失败！', 500);
                }
            },
            error: function() {
                msgalertxx('网络错误，请重试！', 500);
            }
        });
    }

    /**
     * 话题类型
     * @param {Number} ifmunx 话题类型 1-普通 2-j精选 3-置顶
     * @param {String} id 话题ID
     */
    function sendTopicTypeAjax(ifmunx, id) {
        // 验证参数
        if (![1, 2, 3].includes(ifmunx)) {
            alert("<font>(｡ŏ_ŏ)</font> 参数错误！");
            return;
        }
        const idStr = String(id).trim();
        const intReg = /^[1-9]\d*$/;
        if (!intReg.test(idStr)) {
            alert("<font>(｡ŏ_ŏ)</font> 参数错误！");
            return;
        }

        $.ajax({
            url: '/subject/admin/newtop.php',
            type: 'POST',
            data: {
                if: ifmunx,
                ids: idStr
            },
            success: function(newtop) {
                if (newtop == 200) {
                    window.location.reload();
                } else if (newtop == 500) {
                    msgalertxx('操作错误！', 500);
                } else {
                    msgalertxx('修改失败！', 500);
                }
            },
            error: function() {
                msgalertxx('网络错误，请重试！', 500);
            }
        });
    }
});