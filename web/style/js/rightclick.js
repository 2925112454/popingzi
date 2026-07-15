$(function() {
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

    $(document).click(function() {
        customMenu.hide();
    });
    $(document).contextmenu(function(e) {
        if (!$(e.target).is('input[type="checkbox"][name="id"]')) {
            customMenu.hide();
        }
    });

    // 1. 通过审核 = 4
    $('#menuCheck').click(function() {
        if (currentCheckbox) {
            let val = currentCheckbox.val();
            sendAjax(4, val);
        }
    });

    // 2. 撤销审核 = 3
    $('#menuCheckx').click(function() {
        if (currentCheckbox) {
            let val = currentCheckbox.val();
            sendAjax(3, val);
        }
    });

    // 3. 等待审核 = 1
    $('#menuCheckxx').click(function() {
        if (currentCheckbox) {
            let val = currentCheckbox.val();
            sendAjax(1, val);
        }
    });

    // 4. 驳回审核 = 2
    $('#menuCheckxxx').click(function() {
        if (currentCheckbox) {
            let val = currentCheckbox.val();
            sendAjax(2, val);
        }
    });

    function sendAjax(inputConfirmationmun, checkedIdsxString) {
        // 参数合法性判断
        if (![1, 2, 3, 4].includes(inputConfirmationmun)) {
            alert("<font>(｡ŏ_ŏ)</font> 参数错误！");
            return;
        }

        const idStr = String(checkedIdsxString).trim();
        const intReg = /^[1-9]\d*$/;
        if (!intReg.test(idStr)) {
            alert("<font>(｡ŏ_ŏ)</font> 参数错误！");
            return;
        }

        $.ajax({
            url: '/inc/allexirow.php',
            type: 'POST',
            data: {
                if: inputConfirmationmun,
                idsx: checkedIdsxString,
            },
            success: function(allexirow) {
                if (allexirow == 500) {
                    alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                } else if (allexirow == 200) {
                    alert("<font>(◕ܫ◕)</font> 修改状态成功！");
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else if (allexirow == 600) {
                    alert("<font>(｡ŏ_ŏ)</font> 修改状态失败！");
                } else if (allexirow == 404) {
                    alert("<font>(｡ŏ_ŏ)</font> 个别文章不存在！");
                } else {
                    alert("<font>(｡ŏ_ŏ)</font> 服务器出现错误！");
                    console.log(allexirow);
                }
            },
            error: function() {
                alert("<font>(｡ŏ_ŏ)</font> 服务器请求失败！");
            }
        });
    }
});