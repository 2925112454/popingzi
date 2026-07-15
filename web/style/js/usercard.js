document.addEventListener('DOMContentLoaded', function() {

    /* 自定义特殊字符 */
    let customSpecialCharacters = ['@', '%', '&', '*', '#', '$', '~', '^', '+', '/', '-', '|', '?','=','{','}','[',']'];//注意：别添加","、"<"、">"作为特殊符号，后续","会用于分隔数组的符号,而"<"、">"作为HTML标签符号，会导致前端显示出错。

    const newcardbut = document.getElementById('newcard'); // 按钮
    const navfldialogt = document.getElementById('navfldialog'); // 添加充值卡dialog弹窗
    const navfldialogcloset = document.getElementById('navfldialogclose'); // 关闭弹窗

    if (newcardbut && navfldialogt && navfldialogcloset) {
        newcardbut.onclick = function () {
            navfldialogt.showModal();
            navfldialogt.style.display = "flex";
        }
        navfldialogcloset.onclick = function () {
            navfldialogt.close();
            navfldialogt.style.display = "none";
        }

        /* 获取name="notifup"的checkbox多选框 */
        const suijicheckbox = document.querySelectorAll('input[name="notifup"]'); // 充值卡包含的内容：1数字，2大写字母，3小写字母，4特殊字符
        /* 获取前缀文本框 */
        const qianzhui = document.getElementById('qianzhui'); // 充值卡前缀内容
        /* 获取位数文本框 */
        const weishu = document.getElementById('weishu'); // 生成的充值卡位数
        /* 获取条数文本框 */
        const tiaoshu = document.getElementById('tiaoshu'); // 生成的充值卡条数
        /* 获取name="notifupx"的radio单选框 */
        const fenleiradio = document.querySelectorAll('input[name="notifupx"]'); // 充值卡类型：1月度会员，2季度会员，3年度会员，4百年会员，5积分充值
        /* 获取积分div（默认隐藏） */
        const jifennot = document.getElementById('jifennot'); // 充值卡类型为5时显示
        /* 获取name="notifups"的radio单选框 */
        const jifenradio = document.querySelectorAll('input[name="notifups"]'); // 充值积分金额，充值卡类型为5时显示，1为10,2为20,3为30,4为40,5为50，6为100,7为1000
        /* 生成按钮 */
        const navfldialogbutnew = document.getElementById('navfldialogbutnew');
        /* 展示用的多行文本框 */
        const navfldialogbutnewtext = document.getElementById('navfldialogtextarear');
        /* 提交按钮 */
        const navfldialogbutnewsubmit = document.getElementById('navfldialogbut');
        /* 错误提示框 */
        const navfldialogbutnewerror = document.getElementById('navfldialogerr');
        /* 复制按钮 */
        const copycardbut = document.getElementById('copycard');
        /* 导出按钮 */
        const cardfilebut = document.getElementById('cardfile');
        /* 清空按钮 */
        const nullcard= document.getElementById('nullcard');
       

        if (nullcard&&navfldialogbutnewerror && suijicheckbox && qianzhui && weishu && tiaoshu && fenleiradio && jifennot && navfldialogbutnew && navfldialogbutnewtext && navfldialogbutnewsubmit && jifenradio) {

            //cdrderr函数
            let errorTimeoutId = null;
            function cdrderr(text) {
                // 清除之前的计时器
                if (errorTimeoutId) {
                    clearTimeout(errorTimeoutId);
                }
                navfldialogbutnewerror.style.display = 'block';
                navfldialogbutnewerror.innerHTML = '<i class="fa fa-times" aria-hidden="true"></i>' + text;

                // 设置新的计时器
                errorTimeoutId = setTimeout(function () {
                    navfldialogbutnewerror.style.display = 'none';
                    navfldialogbutnewerror.innerHTML = '';
                    errorTimeoutId = null; // 重置计时器ID
                }, 2500);
            }

            //cardthuer函数
            function cardthuer(text) {
                                // 清除之前的计时器
                                if (errorTimeoutId) {
                                    clearTimeout(errorTimeoutId);
                                }
                                navfldialogbutnewerror.style.display = 'block';
                                navfldialogbutnewerror.style.color = '#8bc34a';
                                navfldialogbutnewerror.innerHTML = '<i class="fa fa-check"></i>' + text;
                
                                // 设置新的计时器
                                errorTimeoutId = setTimeout(function () {
                                    navfldialogbutnewerror.style.display = 'none';
                                    navfldialogbutnewerror.style.color = '';
                                    navfldialogbutnewerror.innerHTML = '';
                                    errorTimeoutId = null; // 重置计时器ID
                                }, 2500);
            }

            // 实时监控fenleiradio选择状态
            fenleiradio.forEach(function (radio) {
                radio.addEventListener('change', function () {
                    if (radio.value === '5') {
                        jifennot.style.display = 'flex';
                    } else {
                        jifennot.style.display = 'none';
                    }
                });
            });

            navfldialogbutnew.onclick = function () {
                const suijicheckbox_value = Array.from(suijicheckbox).filter(checkbox => checkbox.checked).map(checkbox => checkbox.value); // 获取选中的复选框的值（生成的充值卡所包含的内容）
                const qianzhui_value = qianzhui.value; // 前缀
                const weishu_value = parseInt(weishu.value, 10); // 位数
                const tiaoshu_value = parseInt(tiaoshu.value, 10); // 条数
                const fenleiradio_value = document.querySelector('input[name="notifupx"]:checked').value; // 充值卡类型
                const jifenradio_value = document.querySelector('input[name="notifups"]:checked') ? document.querySelector('input[name="notifups"]:checked').value : null; // 充值积分金额

                // 验证位数和条数是否在允许范围内
                if (weishu_value <= 0 || weishu_value > 64) {
                    cdrderr("位数必须在1到64之间！")
                    return;
                }

                if (tiaoshu_value <= 0 || tiaoshu_value > 1000) {
                    cdrderr("条数必须在1到1000之间！")
                    return;
                }

                if (['1', '2', '3', '4', '5'].includes(fenleiradio_value) && suijicheckbox_value.length > 0 && (fenleiradio_value !== '5' || ['1', '2', '3', '4', '5', '6','7'].includes(jifenradio_value))) {
                    let cardnumbers = new Set(); // 存储生成的充值卡，使用Set自动去重

                    while (cardnumbers.size < tiaoshu_value) {
                        let cardnumber = ''; // 生成的充值卡
                        for (let j = 0; j < weishu_value; j++) {
                            const randomType = suijicheckbox_value[Math.floor(Math.random() * suijicheckbox_value.length)];
                            switch (randomType) {
                                case '1': // 数字
                                    cardnumber += String.fromCharCode(Math.floor(Math.random() * 10) + 48);
                                    break;
                                case '2': // 大写字母
                                    cardnumber += String.fromCharCode(Math.floor(Math.random() * 26) + 65);
                                    break;
                                case '3': // 小写字母
                                    cardnumber += String.fromCharCode(Math.floor(Math.random() * 26) + 97);
                                    break;
                                case '4': // 自定义特殊字符
                                    cardnumber += customSpecialCharacters[Math.floor(Math.random() * customSpecialCharacters.length)];
                                    break;
                            }
                        }
                        cardnumbers.add(qianzhui_value + cardnumber);
                    }

                    navfldialogbutnewtext.value = Array.from(cardnumbers).join('\n');
                } else {
                    cdrderr("生成配置不正确！")
                }
            }

            //复制
            if(copycardbut){
                copycardbut.onclick = function () {
                    const text = navfldialogbutnewtext.value;
                    if (text) {
                        navigator.clipboard.writeText(text).then(() => {
                            cardthuer("复制成功！")
                        }).catch((error) => {
                            cdrderr("充值卡复制失败！")
                            console.error('复制失败：', error);
                        });
                    } else {
                        cdrderr("没有可复制充值卡！")
                    }
                }
            }

            // 导出
            if(cardfilebut){
                cardfilebut.onclick = function () {
                    const textfield = navfldialogbutnewtext.value;
                    if (textfield) {
                        const blob = new Blob([textfield], { type: 'text/plain' });
                        const url = URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        link.href = url;
                        link.download = 'generated_cards.txt';
                        link.click();                        
                    }else{
                        cdrderr("没有可导出充值卡！")
                    }
                }
            }
            //清空
            if(nullcard){
                nullcard.onclick = function () {
                    navfldialogbutnewtext.value = '';
                }
            }
            //获取添加按钮
            const cardbutnavfldialogbut = document.getElementById('navfldialogbut');
        //添加
        if (cardbutnavfldialogbut) {
            cardbutnavfldialogbut.onclick = function () {
                const cardnumbersx = navfldialogbutnewtext.value.split('\n');
                if (cardnumbersx.length > 0 && cardnumbersx[0] !== ''){
                    //获取多选按钮的值
                    const cardtype = document.querySelector('input[name="notifupx"]:checked').value;
                    if (cardtype === '1'|| cardtype === '2'|| cardtype === '3'|| cardtype === '4'|| cardtype === '5') {
                        let cardtypexvalue = 0;
                        if (cardtype === '5') {
                            //获取单选框的值
                            const cardtypex = document.querySelector('input[name="notifups"][type="radio"]:checked').value;
                            if (cardtypex === '1'|| cardtypex === '2'|| cardtypex === '3'|| cardtypex === '4'|| cardtypex === '5'|| cardtypex === '6' || cardtypex === '7') {
                                //转换为数值
                                cardtypexvalue = parseInt(cardtypex);
                            }else{
                                cardtypexvalue = 0;
                            }
                        }else{
                            cardtypexvalue = 1;
                        }

                        if (cardtypexvalue === 0) {
                            cdrderr("错误参数！")
                        }else{
                            //把数组转换为字符串
                            const cardtext= cardnumbersx.join(',');
                            let cardif= '';
                            if(cardtype === '1'){
                                cardif= '月度会员';
                            }else if(cardtype === '2'){
                                cardif= '季度会员';
                            }else if(cardtype === '3'){
                                cardif= '年度会员';
                            }else if(cardtype === '4'){
                                cardif= '百年会员';
                            }else if(cardtype === '5'){
                                cardif= '积分';
                            }else{
                                cardif= '未知';
                            }
                            //弹窗确认
                            if (confirm('确定要添加 ' + cardnumbersx.length + ' 张 ' +cardif+ ' 充值卡吗？')) {

                                $.ajax({
                                    url: '/inc/newcard.php', // 请求地址
                                    type: 'POST',   // 请求类型
                                    dataType: 'json',//数据类型
                                    data: {
                                    type: cardtype,//类型
                                    card:cardtext,//卡号
                                    gold:cardtypexvalue,//积分
                                    },
                                        success: function(newpassx) { // 成功回调函数
                                                if(newpassx.err == 500){
                                                    cdrderr("错误操作！")
                                                }else if(newpassx.err == 200){
                                                    cardthuer("添加成功！")
                                                    navfldialogbutnewtext.value = '';//清空充值卡
                                                }else if(newpassx.err == 600){
                                                    cdrderr("添加失败！")
                                                }else if(newpassx.err == 800){
                                                    cdrderr("有"+newpassx.repeatnum+"条充值卡已存在；请重新生成。")
                                                    console.log("重复充值卡："+newpassx.repeat);
                                                }else{
                                                cdrderr("程序错误！")
                                                console.log(newpassx);
                                            }                      
                                          
                                        }
                                  });   

                                
                            }
                        }
                        
                    }else{
                        cdrderr("错误参数！")
                    }
                    
                }else{
                    cdrderr("没有充值卡可添加至数据库！")
                }
            };
        }

        }
    }



/*导出充值卡*/
const dccardtext= document.getElementById('cardtext');//导出按钮
const navfldialogx= document.getElementById('navfldialogx');//导出弹窗
const navfldialogclosex = document.getElementById('navfldialogclosex');//关闭弹窗

const huoqucard= document.getElementById('huoqucard');//获取充值卡按钮
const cardall = document.getElementById('cardall');//全选/全不选
const copyallcard = document.getElementById('copycardx');//复制按钮
const cardfiledcx = document.getElementById('cardfilex');//导出文件按钮
const navfldialogtextareardcx= document.getElementById('navfldialogtextarearx');//预览文本框
const navfldialogerrdcx= document.getElementById('navfldialogerrx');//错误提示
const jifennotx = document.getElementById('jifennotx');//积分复选框DIV，默认隐藏

       // 获取所有分类复选框
       const allcheckboxes = document.querySelectorAll('input[type="checkbox"][name="notifupdc"]');
       // 获取所有积分复选框
       const allcheckboxesx = document.querySelectorAll('input[type="checkbox"][name="notifupsd"]');

if (dccardtext&&navfldialogx&&navfldialogclosex&&huoqucard&&cardall&&copyallcard&&cardfiledcx&&navfldialogtextareardcx&&navfldialogerrdcx&&jifennotx) {

    let errorTimeoutIdnew = null;
    //daochuerr函数
    function daochuerr(text) {
        // 清除之前的计时器
        if (errorTimeoutIdnew) {
            clearTimeout(errorTimeoutIdnew);
        }
        navfldialogerrdcx.innerHTML ='<i class="fa fa-times" aria-hidden="true"></i>'+text;
        navfldialogerrdcx.style.display = "block";
        errorTimeoutIdnew = setTimeout(function () {
            navfldialogerrdcx.style.display = "none";
            navfldialogerrdcx.innerHTML = '';
            errorTimeoutIdnew = null;
        }, 2500);
    }
    //daochuthuer函数
    function daochuthuer(text) {
        // 清除之前的计时器
        if (errorTimeoutIdnew) {
            clearTimeout(errorTimeoutIdnew);
        }
        navfldialogerrdcx.innerHTML ='<i class="fa fa-check"></i>'+text;
        navfldialogerrdcx.style.color = '#8bc34a';
        navfldialogerrdcx.style.display = "block";

        errorTimeoutIdnew = setTimeout(function () {
            navfldialogerrdcx.style.display = "none";
            navfldialogerrdcx.style.color="";
            navfldialogerrdcx.innerHTML = '';
            errorTimeoutIdnew = null;
            
        }, 2500);
    }
    //打开弹窗
    dccardtext.onclick = function () {
        navfldialogx.showModal();
        navfldialogx.style.display = "flex";
    };
    //关闭弹窗
    navfldialogclosex.onclick = function () {
        navfldialogx.close();
        navfldialogx.style.display = "none";
    };

// 监听所有分类复选框，当选中项包含5时，显示积分复选框，否则隐藏积分复选框
allcheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', function () {
        if (checkbox.checked) {
            if (checkbox.value === '5') {
                jifennotx.style.display = "flex";
            }
        } else {
            if (checkbox.value === '5') {
                //清空积分复选框(除了第一个)
                for (let i = 1; i < allcheckboxesx.length; i++) {
                    allcheckboxesx[i].checked = false;
                }
                jifennotx.style.display = "none";
            }
        }

        // 更新 cardall 复选框的状态
        const allChecked = Array.from(allcheckboxes).every(cb => cb.checked);
        cardall.checked = allChecked;
    });
});

// 全选/全不选
cardall.onclick = function () {
    // 若是全选状态则取消全选，并隐藏积分复选框，若非全选，则全选并显示积分复选框
    if (cardall.checked) {
        for (let i = 0; i < allcheckboxes.length; i++) {
            if (i === 0) {
                allcheckboxes[i].checked = true; // 保留第一个分类复选框的选中状态
            } else {
                allcheckboxes[i].checked = false;
            }
        }
        for (let i = 0; i < allcheckboxesx.length; i++) {
            if (i === 0) {
                allcheckboxesx[i].checked = true; // 保留第一个积分复选框的选中状态
            } else {
                allcheckboxesx[i].checked = false;
            }
        }
        jifennotx.style.display = "none";
        cardall.checked = false; // 确保 cardall 复选框状态正确
    } else {
        for (const checkbox of allcheckboxes) {
            checkbox.checked = true;
        }
        for (const checkbox of allcheckboxesx) {
            checkbox.checked = true;
        }
        jifennotx.style.display = "flex";
        cardall.checked = true; // 确保 cardall 复选框状态正确
    }
};

// 复制按钮
copyallcard.onclick = function () {
    const textnew = navfldialogtextareardcx.value;//获取文本框内容
    if (textnew) {
        navigator.clipboard.writeText(textnew)
        .then(() => {
            daochuthuer("复制成功！");
        })
        .catch(err => {
            daochuerr("复制失败！");
            console.error('复制失败: ', err);
        });
        
    }else{
        daochuerr("请先获取充值卡！");
    }
}

// 导出文件按钮
cardfiledcx.onclick = function () {
    const textnewxx = navfldialogtextareardcx.value;//获取文本框内容
    if (textnewxx) {
        const newblob = new Blob([textnewxx], { type: 'text/plain;charset=utf-8' });
        const newurl = URL.createObjectURL(newblob);
        const newlink = document.createElement('a');
        newlink.href = newurl;
        newlink.download = 'card.txt';
        newlink.click();
        URL.revokeObjectURL(newurl);
    }else{
        daochuerr("请先获取充值卡！");
    }
   
}

// 获取充值卡按钮
huoqucard.onclick = function () {
    //获取所有复选框的值
    const checkedValues = [];
    for (const checkbox of allcheckboxes) {
        if (checkbox.checked) {
            checkedValues.push(checkbox.value);
        }
    }
    if (checkedValues.length === 0) {
        daochuerr("请先选择导出项！");
    }else{
        //判断复选框的值是否是是1、2、3、4、5
        if (checkedValues.every(value => ['1', '2', '3', '4', '5'].includes(value))) {
            const checkedValuesx = [];
                //若包含5，则获取积分复选框的值
                if (checkedValues.includes('5')) {
                    for (const checkbox of allcheckboxesx) {
                        if (checkbox.checked) {
                            checkedValuesx.push(checkbox.value);
                        }
                    }
                }else{
                    checkedValuesx.push('1');//默认选中1
                }

                if (checkedValuesx.length === 0 && checkedValues.includes('5') && !checkedValuesx.every(value => ['1', '2', '3', '4', '5', '6','7'].includes(value))) {
                    daochuerr("错误参数！");
                    return;
                }

                // 转换为字符串
                const checkedValuesString = checkedValues.join(',');//分类
                const checkedValuesStringx = checkedValuesx.join(',');//积分

                //向数据库获取数据ajax
                $.ajax({
                    url: '/api/card.php', // 请求地址
                    type: 'POST',   // 请求类型
                    dataType: 'json',//数据类型
                    data: {
                    type: checkedValuesString,//类型
                    gold:checkedValuesStringx,//积分
                    },
                        success: function(card) { // 成功回调函数
                                if(card.err == 500){
                                    daochuerr("错误操作！");
                                }else if(card.err == 200){
                                    //按英文逗号分隔并换行
                                    const cardarr = card.card.join('\n');
                                    //获取条数
                                    const cardarrnum = card.card.length;
                                    //将数据写入textarea
                                    navfldialogtextareardcx.value = cardarr;
                                    daochuthuer("获取到"+cardarrnum+"个充值卡！");
                                }else if(card.err == 600){
                                    daochuerr("获取失败！");
                                }else{
                                    daochuerr("程序错误！");
                                    console.log(card);
                                }
                        }
                  });  




        }else{
            daochuerr("错误参数！");
        }

    }
}


}

/* 充值卡设置 */
const cardset = document.getElementById("cardedit");//设置按钮
const cardsetdialog = document.getElementById("cardset");
const cardsetclose= document.getElementById("cardsetclose");

const yueurlipt = document.getElementById("yueurlipt");//月度充值卡购买地址
const yuermbipt = document.getElementById("yuermbipt");//月度充值卡价格
const jiurlipt = document.getElementById("jiurlipt");//季度充值卡购买地址
const jirmbipt = document.getElementById("jirmbipt");//季度充值卡价格
const nianurlipt = document.getElementById("nianurlipt");//年度充值卡购买地址
const nianrmbipt = document.getElementById("nianrmbipt");//年度充值卡价格
const baiurlipt = document.getElementById("baiurlipt");//百年度充值卡购买地址
const bairmbipt = document.getElementById("bairmbipt");//百年度充值卡价格
const shiurlipt = document.getElementById("shiurlipt");//10积分充值卡购买地址
const shirmbipt = document.getElementById("shirmbipt");//10积分充值卡价格
const erurlipt = document.getElementById("erurlipt");//20积分充值卡购买地址
const errmbipt = document.getElementById("errmbipt");//20积分充值卡价格
const sanurlipt = document.getElementById("sanurlipt");//30积分充值卡购买地址
const sanrmbipt = document.getElementById("sanrmbipt");//30积分充值卡价格
const siurlipt = document.getElementById("siurlipt");//40积分充值卡购买地址
const sirmbipt = document.getElementById("sirmbipt");//40积分充值卡价格
const wuurlipt = document.getElementById("wuurlipt");//50积分充值卡购买地址
const wurmbipt = document.getElementById("wurmbipt");//50积分充值卡价格
const yiurlipt = document.getElementById("yiurlipt");//100积分充值卡购买地址
const yirmbipt = document.getElementById("yirmbipt");//100积分充值卡价格
const qianurlipt = document.getElementById("qianurlipt");//1000积分充值卡购买地址
const qianrmbipt = document.getElementById("qianrmbipt");//1000积分充值卡价格

const cardsetbtn = document.getElementById("cardsetbut");//确认按钮
const cardseterr = document.getElementById("cardseterr");//错误提示显示框

const allipturl = document.querySelectorAll(".ipturl");//获取所有类名为ipturl的input元素

if (allipturl&&allipturl.length > 0) {
    allipturl.forEach(ipturl => {
        ipturl.addEventListener("focus", function () {
            //移除is-invalid
            ipturl.classList.remove("is-invalid");
        });
    });
}

//错误提示函数
let cardsetmegtime = null;
function cardsetmeg(err) {
    if (cardsetmegtime) {
        clearTimeout(cardsetmegtime);//清除定时器
    }
    cardseterr.innerHTML = '<i class="fa fa-times" aria-hidden="true"></i>' + err;
    cardseterr.style.display = "block";
    cardseterr.style.color = "";
    cardsetmegtime = setTimeout(function () {
        cardseterr.style.display = "none";
        cardseterr.innerHTML = "";
        cardsetmegtime = null;
    }, 2500);
}

//成功提示函数
function cardsetmegs(err) {
    if (cardsetmegtime) {
        clearTimeout(cardsetmegtime);//清除定时器
    }
    cardseterr.innerHTML = '<i class="fa fa-check" aria-hidden="true"></i>' + err;
    cardseterr.style.display = "block";
    cardseterr.style.color = "#8bc34a";
    cardsetmegtime = setTimeout(function () {
        cardseterr.style.display = "none";
        cardseterr.innerHTML = "";
        cardseterr.style.color = "";
        cardsetmegtime = null;
    }, 2500)
}



function cadrcheckUrl(url) {
    // 定义正则表达式，匹配 http://、https://、ftp:// 开头，或者以 /、.html、.php 结尾的 URL
    const urlPattern = /^(http:\/\/|https:\/\/|ftp:\/\/|\/|\S+\.(htm|php))\S*$/i;
    // 检查输入是否为字符串
    if (typeof url !== 'string') {
        return false;
    }
    try {
        // 使用正则表达式测试 URL
        return urlPattern.test(url);
    } catch (error) {
        return false;
    }
}

if (cardset&&cardsetdialog&&cardsetclose&&cardsetbtn&&cardseterr) {
    cardset.onclick = function () {
            cardsetdialog.showModal();
            cardsetdialog.style.display = "flex";
    }
    cardsetclose.onclick = function () {
            cardsetdialog.close();
            cardsetdialog.style.display = "none";
    }
    cardsetbtn.onclick = function () {

        if(yueurlipt&&yuermbipt&&jiurlipt&&jirmbipt&&nianurlipt&&nianrmbipt&&baiurlipt&&bairmbipt&&shiurlipt&&shirmbipt&&erurlipt&&errmbipt&&sanurlipt&&sanrmbipt&&siurlipt&&sirmbipt&&wuurlipt&&wurmbipt&&yiurlipt&&yirmbipt&&qianrmbipt&&qianurlipt){
            //判断价格是否为空
            if(yuermbipt.value&&jirmbipt.value&&nianrmbipt.value&&bairmbipt.value&&shirmbipt.value&&errmbipt.value&&sanrmbipt.value&&sirmbipt.value&&wurmbipt.value&&yirmbipt.value&&yirmbipt.value){
                //判断价格是否是有效的数字(包括0)
                if(!isNaN(yuermbipt.value)&&!isNaN(jirmbipt.value)&&!isNaN(nianrmbipt.value)&&!isNaN(bairmbipt.value)&&!isNaN(shirmbipt.value)&&!isNaN(errmbipt.value)&&!isNaN(sanrmbipt.value)&&!isNaN(sirmbipt.value)&&!isNaN(wurmbipt.value)&&!isNaN(yirmbipt.value)&&!isNaN(qianrmbipt.value)&&(qianrmbipt.value>=0&&yirmbipt.value>=0&&wurmbipt.value>=0&&sirmbipt.value>=0&&sanrmbipt.value>=0&&errmbipt.value>=0&&shirmbipt.value>=0&&bairmbipt.value>=0&&nianrmbipt.value>=0&&jirmbipt.value>=0&&yuermbipt.value>=0)){
                    
                    if(yueurlipt.value.trim()){
                        if(!cadrcheckUrl(yueurlipt.value.trim())){
                            cardsetmeg("请正确填写购买地址！");
                            yueurlipt.classList.add("is-invalid");
                            return;
                        }                      
                    }

                    if(jiurlipt.value.trim()){
                        if(!cadrcheckUrl(jiurlipt.value.trim())){
                            cardsetmeg("请正确填写购买地址！");
                            jiurlipt.classList.add("is-invalid");
                            return;
                        }                      
                    }

                    if(nianurlipt.value.trim()){
                        if(!cadrcheckUrl(nianurlipt.value.trim())){
                            cardsetmeg("请正确填写购买地址！");
                            nianurlipt.classList.add("is-invalid");
                            return;
                        }                      
                    }

                    if(baiurlipt.value.trim()){
                        if(!cadrcheckUrl(baiurlipt.value.trim())){
                            cardsetmeg("请正确填写购买地址！");
                            baiurlipt.classList.add("is-invalid");
                            return;
                        }                      
                    }

                    if(shiurlipt.value.trim()){
                        if(!cadrcheckUrl(shiurlipt.value.trim())){
                            cardsetmeg("请正确填写购买地址！");
                            shiurlipt.classList.add("is-invalid");
                            return;
                        }                      
                    }

                    if(erurlipt.value.trim()){
                        if(!cadrcheckUrl(erurlipt.value.trim())){
                            cardsetmeg("请正确填写购买地址！");
                            erurlipt.classList.add("is-invalid");
                            return;
                        }                      
                    }

                    if(sanurlipt.value.trim()){
                        if(!cadrcheckUrl(sanurlipt.value.trim())){
                            cardsetmeg("请正确填写购买地址！");
                            sanurlipt.classList.add("is-invalid");
                            return;
                        }                      
                    }

                    if(siurlipt.value.trim()){
                        if(!cadrcheckUrl(siurlipt.value.trim())){
                            cardsetmeg("请正确填写购买地址！");
                            siurlipt.classList.add("is-invalid");
                            return;
                        }                      
                    }

                    if(wuurlipt.value.trim()){
                        if(!cadrcheckUrl(wuurlipt.value.trim())){
                            cardsetmeg("请正确填写购买地址！");
                            wuurlipt.classList.add("is-invalid");
                            return;
                        }                      
                    }

                    if(yiurlipt.value.trim()){
                        if(!cadrcheckUrl(yiurlipt.value.trim())){
                            cardsetmeg("请正确填写购买地址！");
                            yiurlipt.classList.add("is-invalid");
                            return;
                        }                      
                    }

                    if(qianurlipt.value.trim()){
                        if(!cadrcheckUrl(qianurlipt.value.trim())){
                            cardsetmeg("请正确填写购买地址！");
                            qianurlipt.classList.add("is-invalid");
                            return;
                        }                      
                    }
                    

                    $.ajax({
                        url: '/api/cardset.php', // 请求地址
                        type: 'POST',   // 请求类型
                        data: {
                            yuermb:yuermbipt.value,//月度价格
                            yueurl:yueurlipt.value,//月度购买地址
                            jirmb:jirmbipt.value,//季度价格
                            jiurl:jiurlipt.value,//季度购买地址
                            nianrmb:nianrmbipt.value,//年度价格
                            nianurl:nianurlipt.value,//年度购买地址
                            bairmb:bairmbipt.value,//百年价格
                            baiurl:baiurlipt.value,//百年购买地址
                            shirmb:shirmbipt.value,//10积分价格
                            shiurl:shiurlipt.value,//10积分购买地址
                            errmb:errmbipt.value,//20积分价格
                            erurl:erurlipt.value,//20积分购买地址
                            sanrmb:sanrmbipt.value,//30积分价格
                            sanurl:sanurlipt.value,//30积分购买地址
                            sirmb:sirmbipt.value,//40积分价格
                            siurl:siurlipt.value,//40积分购买地址
                            wurmb:wurmbipt.value,//50积分价格
                            wuurl:wuurlipt.value,//50积分购买地址
                            yirmb:yirmbipt.value,//100积分价格
                            yiurl:yiurlipt.value,//100积分购买地址
                            qianrmb:qianrmbipt.value,//1000积分价格
                            qianurl:qianurlipt.value,//1000积分购买地址
                        },
                                    success: function(cardset) { // 成功回调函数
                                        if(cardset == 500){
                                            cardsetmeg("错误操作！");                                                                
                                        }else if(cardset == 200){
                                            cardsetmegs("修改成功！");
                                        }else if(cardset == 404){
                                            cardsetmeg("数据表不存在！");  
                                        }else if(cardset == 600){
                                            cardsetmeg("修改失败！");  
                                        }else{
                                            cardsetmeg("程序错误！");
                                            console.log(cardset);
                                        }
                                    }
                
                    });

                    
                    
                   
                }else{
                    cardsetmeg("请填写正确的价格！");
                }
                
            }else{
                cardsetmeg("请填写正确的价格！");
            }
        }else{
            cardsetmeg("错误参数！");
        }
    }
}

/*全选 or 取消全选*/

const cardcheckAll = document.getElementById("allcardif");//全选、全不选按钮
if(cardcheckAll){
    cardcheckAll.addEventListener('click', function(e) {
        const cardcheckBoxes = document.querySelectorAll('input[type="checkbox"][name="cardid"]');//获取所有多选框
        if(cardcheckBoxes.length > 0){

            //若当前是全选，则取消全选，反之亦然(注意，不是反选)

            if(cardcheckAll.checked){
                cardcheckAll.checked = false;
                for (const cardcheckBox of cardcheckBoxes) {
                    cardcheckBox.checked = false;
                }
            }else{
                cardcheckAll.checked = true;
                for (const cardcheckBox of cardcheckBoxes) {
                    cardcheckBox.checked = true;
                }
            }
            
                  
        }
    });    
}

/* 批量删除 */
const delallcard = document.getElementById("delallcard");
if(delallcard){
    delallcard.addEventListener('click', function(e) {
        const cardcheckBoxesxx = document.querySelectorAll('input[type="checkbox"][name="cardid"]');
        if(cardcheckBoxesxx.length > 0){
            //判断是否有选中项
            let cardcheckBoxesxx_checked = false;
            for (const cardcheckBox of cardcheckBoxesxx) {
                if(cardcheckBox.checked){
                    cardcheckBoxesxx_checked = true;
                    break;
                }
            }
            if(cardcheckBoxesxx_checked){
                const cardcheckBoxesxx_value = [];//存储选中的项的值
                for (const cardcheckBox of cardcheckBoxesxx) {
                    if(cardcheckBox.checked){//判断是否选中
                        if(cardcheckBox.value.match(/^[1-9]\d*$/)){ //判断是否为正整数
                            cardcheckBoxesxx_value.push(cardcheckBox.value);
                        }else{
                            return; // 跳出循环
                        }
                    }
                }

                if(cardcheckBoxesxx_value&&cardcheckBoxesxx_value.length > 0){
                    //弹出输入框
                    if(prompt("请输入“确定删除”，以此来确认您确实需要这么做！")==='确定删除'){
                        //将数组转换为逗号分隔的字符串
                        const cardcheckBoxesxx_value_str = cardcheckBoxesxx_value.join(",");
                        $.ajax({
                            url: '/api/carddel.php', // 请求地址
                            type: 'POST',   // 请求类型
                            data: {
                                id:cardcheckBoxesxx_value_str,//id
                            },
                                        success: function(carddel) { // 成功回调函数
                                            if(carddel == 500){
                                                alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                               
                                            }else if(carddel == 200){
                                                alert("<font>(◕ܫ◕)</font> 删除成功！");
                                                setTimeout(function(){
                                                    window.location.reload();//刷新页面
                                                },2000);
                                            }else if(carddel == 600){
                                                alert("<font>(｡ŏ_ŏ)</font> 删除错误！");  
                                            }else{
                                                alert("<font>(｡ŏ_ŏ)</font> 程序错误！");
                                                console.log(carddel);
                                            }
                                        }
                    
                        });
                    }
                }else{
                    alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
                }
                


            }else{
                //弹窗
                alert("<font>(｡ŏ_ŏ)</font> 没有选择项！");
            }
            
        }        
    });
}

/* 删除 */
const delcard = document.querySelectorAll('.delcard');//所有删除按钮
if(delcard&&delcard.length>0){
    delcard.forEach(function(delcard){
        delcard.addEventListener('click', function(e) {
            const dcardid = this.getAttribute('data-d');//获取data-d的值
            if(dcardid&&dcardid>0&&dcardid.match(/^[1-9]\d*$/)){
                    if(prompt("请输入“确定删除”，以此来确认您确实需要这么做！")==='确定删除'){
                        $.ajax({
                            url: '/api/carddel.php', // 请求地址
                            type: 'POST',   // 请求类型
                            data: {
                                id:dcardid,//id
                            },
                                        success: function(carddelx) { // 成功回调函数
                                            if(carddelx == 500){
                                                alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                               
                                            }else if(carddelx == 200){
                                                alert("<font>(◕ܫ◕)</font> 删除成功！");
                                                setTimeout(function(){
                                                    window.location.reload();//刷新页面
                                                },2000);
                                            }else if(carddelx == 600){
                                                alert("<font>(｡ŏ_ŏ)</font> 删除错误！");  
                                            }else{
                                                alert("<font>(｡ŏ_ŏ)</font> 程序错误！");
                                                console.log(carddelx);
                                            }
                                        }
                    
                        });
                }
            }else{
                alert("<font>(｡ŏ_ŏ)</font> 错误操作！");
            }

        })
    })
}

/* 编辑 */
const alleditcard = document.querySelectorAll('.editcard');//所有编辑按钮
const carddialog= document.getElementById('carddialog');//弹窗
const carddialogclose= document.getElementById('carddialogclose');//关闭按钮
const carddialoginput= document.getElementById('carddialoginput');//输入框
const carddialogbut= document.getElementById('carddialogbut');//提交按钮
const carddialogerr= document.getElementById('carddialogerr');//错误提示

let carddialogerrx = null;
let nowinputvalue = '';
function ncarddialogerr(text) {
    if (carddialogerrx) {
        clearTimeout(carddialogerrx);
    }
    carddialogerr.innerHTML ='<i class="fa fa-times" aria-hidden="true"></i>'+text;
    carddialogerr.style.display = "block";
    carddialogerrx = setTimeout(function () {
        carddialogerr.style.display = "none";
        carddialogerr.innerHTML = '';
        carddialogerrx = null;
    }, 2000);
}

function ycarddialogerr(text,yid,value) {
    if (carddialogerrx) {
        clearTimeout(carddialogerrx);
    }
    carddialogerr.innerHTML ='<i class="fa fa-check" aria-hidden="true"></i>'+text;
    carddialogerr.style.display = "block";
    carddialogerr.style.color = "#8bc34a";
    carddialogerrx = setTimeout(function () {
        carddialogerr.style.display = "none";
        carddialogerr.innerHTML = '';
        carddialogerr.style.color = "";
        carddialogerrx = null;
    }, 2000);
    const cardyesid = document.getElementById('cardyesid'+yid);
    if(cardyesid){
        cardyesid.innerHTML = value;
    }
    if(alleditcard){
        alleditcard.forEach(function(alleditcard){
            const editcarddatai = alleditcard.getAttribute('data-i');//获取data-i的值
            if(editcarddatai==yid){
                alleditcard.setAttribute('data-t',value);
            }
        })
    }
}

if(alleditcard&&alleditcard.length>0&&carddialog&&carddialogclose&&carddialoginput&&carddialogbut&&carddialogerr){

    alleditcard.forEach(function(alleditcard){
        alleditcard.addEventListener('click', function(e) {
            const editcardtext = this.getAttribute('data-t');//获取data-t的值
            const editcardid = this.getAttribute('data-i');//获取data-i的值
            if(editcardid&&editcardid>0&&editcardid.match(/^[1-9]\d*$/)){
                carddialog.showModal();
                carddialoginput.value=editcardtext;
                carddialog.style.cssText="display:flex;";
                carddialogbut.setAttribute('data-cid',editcardid);
                nowinputvalue = editcardtext;
            }
        })
    })

    carddialogclose.addEventListener('click', function(e) {
        carddialog.close();
        carddialog.style.cssText="";
        carddialoginput.value='';
        carddialogbut.setAttribute('data-cid','');
        nowinputvalue='';
    })

    carddialogbut.addEventListener('click', function(e) {
        const neweditcardid = this.getAttribute('data-cid');//id
        const neweditcardtext = carddialoginput.value;//值
        if(neweditcardtext){

            if(neweditcardtext==nowinputvalue){
                ncarddialogerr('充值卡号没有改动！');
            }else{
                    if (neweditcardid&&neweditcardid>0&&neweditcardid.match(/^[1-9]\d*$/)){
                         
                        $.ajax({
                            url: '/api/cardedit.php', // 请求地址
                            type: 'POST',   // 请求类型
                            data: {
                                id:neweditcardid,//id
                                text:neweditcardtext,//值
                            },
                                        success: function(carde) { // 成功回调函数
                                            if(carde == 500){
                                                ncarddialogerr('错误操作！');                                                              
                                            }else if(carde == 200){
                                                ycarddialogerr('修改成功！',neweditcardid,neweditcardtext); 
                                                nowinputvalue=neweditcardtext;
                                            }else if(carde == 600){
                                                ncarddialogerr('修改失败！');
                                            }else if(carde == 800){
                                                ncarddialogerr('充值卡号已存在！');
                                            }else{
                                                ncarddialogerr('程序错误！');
                                                console.log(carde);
                                            }
                                        }
                    
                        });
                    }else{
                        ncarddialogerr('参数错误！');
                    }

            }

        }else{
            ncarddialogerr('充值卡不能为空！');
        }
        
    })

}



});