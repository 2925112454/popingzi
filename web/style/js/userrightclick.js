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

    // 1. 封禁 = 2
    $('#menuCheck').click(function() {
        if (currentCheckbox) {
            let val = currentCheckbox.val();
            sendAjax(2, val);
        }
    });

    // 2. 解封 = 1
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
        url: '/inc/allexiuser.php', // 请求地址
        type: 'POST',   // 请求类型
        data: {
            if:inputConfirmationmun,//状态
            idsx:checkedIdsxString,//id
        },
                success: function(allexirow) { // 成功回调函数
                    if(allexirow == 500){
                        alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                                    
                    }else if(allexirow == 200){
                        alert("<font>(◕ܫ◕)</font> 修改状态成功！");
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    }else if(allexirow == 600){
                        alert("<font>(｡ŏ_ŏ)</font> 修改状态失败！");
                    }else if(allexirow == 404){
                        alert("<font>(｡ŏ_ŏ)</font> 个别会员不能被操作！");
                    }else{
                        alert("<font>(｡ŏ_ŏ)</font> 服务器出现错误！");
                    }
                }

    });
    }
});