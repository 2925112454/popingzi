$(function() {
    const customMenu = $('#customMenu');
    let currentCheckbox = null;

    // 给所有复选框绑定右键
    $(document).on('contextmenu', 'input[type="checkbox"][name="seid"]', function(e) {
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
        if (!$(e.target).is('input[type="checkbox"][name="seid"]')) {
            customMenu.hide();
        }
    });

    // 1. 已处理 = 2
    $('#menuCheck').click(function() {
        if (currentCheckbox) {
            let val = currentCheckbox.val();
            sendAjax(2, val);
        }
    });

    // 2. 待处理 = 1
    $('#menuCheckx').click(function() {
        if (currentCheckbox) {
            let val = currentCheckbox.val();
            sendAjax(1, val);
        }
    });

    function sendAjax(inputConfirmationmun, checkedIdsxString) {
        // 参数合法性判断
        if (![1, 2].includes(inputConfirmationmun)) {
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
            url: '/inc/alleditservice.php', // ajax请求
            type: 'POST',   // 请求类型
            data: {
            allidx: checkedIdsxString,
            allmuns: inputConfirmationmun,
            },
            success: function(alldelsx) {
                if (alldelsx == 500){
                    alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                }else if(alldelsx == 600){
                    alert("<font>(｡ŏ_ŏ)</font> 部分修改失败！");
                    setTimeout(function() {  
                        location.reload(true);
                    }, 1000);
                }else if(alldelsx == 200){
                    alert("<font>(◕ܫ◕)</font> 修改成功！");
                    setTimeout(function() {
                        location.reload(true);
                    }, 1000);
                }else{
                    alert("<font>(｡ŏ_ŏ)</font> 程序错误！");
                }

            }         
        });
    }
});