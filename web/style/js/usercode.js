document.addEventListener('DOMContentLoaded', function() {
/* 自定义特殊字符 */
let customSpecialCharacters = ['@', '%', '&', '*', '#', '$', '~', '^', '+', '/', '-', '|', '?','=','{','}','[',']'];//注意：别添加","、"<"、">"作为特殊符号，后续","会用于分隔数组的符号,而"<"、">"作为HTML标签符号，会导致前端显示出错。
const newcardbut = document.getElementById('newcode'); // 按钮
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
    if (suijicheckbox && qianzhui && weishu && tiaoshu && navfldialogbutnew && navfldialogbutnewtext && navfldialogbutnewsubmit && navfldialogbutnewerror && copycardbut && cardfilebut && nullcard) {
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

        navfldialogbutnew.onclick = function () {
            const suijicheckbox_value = Array.from(suijicheckbox).filter(checkbox => checkbox.checked).map(checkbox => checkbox.value); // 获取选中的复选框的值（生成的充值卡所包含的内容）
            const qianzhui_value = qianzhui.value; // 前缀
            const weishu_value = parseInt(weishu.value, 10); // 位数
            const tiaoshu_value = parseInt(tiaoshu.value, 10); // 条数
            
            // 验证位数和条数是否在允许范围内
            if (weishu_value <= 0 || weishu_value > 64) {
                cdrderr("位数必须在1到64之间！")
                return;
            }

            if (tiaoshu_value <= 0 || tiaoshu_value > 1000) {
                cdrderr("条数必须在1到1000之间！")
                return;
            }

            if (suijicheckbox_value.length > 0) {
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
        };

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
                    link.download = 'generated_code.txt';
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
        if (cardbutnavfldialogbut) {
            cardbutnavfldialogbut.onclick = function () {
                const cardnumbersx = navfldialogbutnewtext.value.split('\n');
                if (cardnumbersx.length > 0 && cardnumbersx[0] !== ''){
                    //把数组转换为字符串
                    const cardtext= cardnumbersx.join(',');
                    //弹窗确认
                    if (confirm('确定要添加 ' + cardnumbersx.length + ' 张邀请码吗？')) {

                        $.ajax({
                            url: '/inc/newcode.php', // 请求地址
                            type: 'POST',   // 请求类型
                            dataType: 'json',//数据类型
                            data: {
                            code:cardtext,//卡号
                            },
                                success: function(newcodex) { // 成功回调函数

                                        if(newcodex.err == 500){
                                            cdrderr("错误操作！")
                                        }else if(newcodex.err == 200){
                                            cardthuer("添加成功！")
                                            navfldialogbutnewtext.value = '';//清空充值卡
                                        }else if(newcodex.err == 600){
                                            cdrderr("添加失败！")
                                        }else if(newcodex.err == 800){
                                            cdrderr("有"+newcodex.repeatnum+"条邀请码已存在；请重新生成。")
                                            console.log("重复邀请码："+newcodex.repeat);
                                        }else{
                                        cdrderr("程序错误！")
                                        console.log(newcodex);
                                        }                      
                                  
                                },

                          });   

                        
                    }

                }else{
                    cdrderr("没有邀请码可添加至数据库！")
                }
            };
        }


    }
}


/*导出邀请码*/
const dccardtext= document.getElementById('codetext');//导出按钮
const navfldialogx= document.getElementById('navfldialogx');//导出弹窗
const navfldialogclosex = document.getElementById('navfldialogclosex');//关闭弹窗

const huoqucard= document.getElementById('huoqucard');//获取邀请码按钮
const copyallcard = document.getElementById('copycardx');//复制按钮
const cardfiledcx = document.getElementById('cardfilex');//导出文件按钮
const navfldialogtextareardcx= document.getElementById('navfldialogtextarearx');//预览文本框
const navfldialogerrdcx= document.getElementById('navfldialogerrx');//错误提示
if (dccardtext&& navfldialogx&&navfldialogclosex&&huoqucard&&copyallcard&&cardfiledcx&&navfldialogtextareardcx&&navfldialogerrdcx) {
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
        daochuerr("请先获取邀请码！");
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
            newlink.download = 'code.txt';
            newlink.click();
            URL.revokeObjectURL(newurl);
        }else{
            daochuerr("请先获取邀请码！");
        }
    
    }

    // 获取邀请码按钮
    huoqucard.onclick = function () {
        //向数据库获取数据ajax
        $.ajax({
            url: '/api/code.php', // 请求地址
            type: 'POST',   // 请求类型
            dataType: 'json',//数据类型
                success: function(code) { // 成功回调函数
                        if(code.err == 500){
                            daochuerr("错误操作！");
                        }else if(code.err == 200){
                            //按英文逗号分隔并换行
                            const cardarr = code.codet.join('\n');
                            //获取条数
                            const cardarrnum = code.codet.length;
                            //将数据写入textarea
                            navfldialogtextareardcx.value = cardarr;
                            daochuthuer("获取到"+cardarrnum+"个邀请码！");
                        }else if(code.err == 600){
                            daochuerr("获取失败！");
                        }else{
                            daochuerr("程序错误！");
                            console.log(code);
                        }
                }
          }); 
    }
    

}

/* 邀请码设置 */
const cardset = document.getElementById("codeedit");//设置按钮
const cardsetdialog = document.getElementById("cardset");
const cardsetclose= document.getElementById("cardsetclose");
const cardsetbtn = document.getElementById("cardsetbut");//确认按钮
const cardseterr = document.getElementById("cardseterr");//错误提示显示框
const codetext = document.getElementById("codetextval");//说明
const codermb = document.getElementById("codermb");//价格
const codeurl = document.getElementById("codeurl");//获取地址

const allipturl = document.querySelectorAll(".ipturl");//获取所有类名为ipturl的input元素

if (allipturl&&allipturl.length > 0) {
    allipturl.forEach(ipturl => {
        ipturl.addEventListener("focus", function () {
            //移除is-invalid
            ipturl.classList.remove("is-invalid");
        });
    });
}

if (cardset&&cardsetdialog&&cardsetclose&&cardsetbtn&&cardseterr) {
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

function hasHtmlTags(str) {
    const htmlTagRegex = /<[a-z][\s\S]*>/i;
    return htmlTagRegex.test(str);
}

cardset.onclick = function () {
    cardsetdialog.showModal();
    cardsetdialog.style.display = "flex";
}
cardsetclose.onclick = function () {
    cardsetdialog.close();
    cardsetdialog.style.display = "none";
}

cardsetbtn.onclick = function (event) {
    event.preventDefault(); 
    if (codetext&&codermb&&codeurl){
        const codetextval = codetext.value;//获取说明，可为空
        const codermbval = codermb.value;//获取价格，不能为空，可为0或0以上的正整数
        const codeurlval = codeurl.value;//获取地址,可为空
    
        if (codermbval == "" || codermbval < 0 || isNaN(codermbval)) {
            cardsetmeg("请填写正确的价格！");
            return;
        }
    
        if (codeurlval != "" && !cadrcheckUrl(codeurlval)) {
            cardsetmeg("请正确填写获取地址！");
            codeurl.classList.add("is-invalid");
            return;
        }
    
        if(codetextval != "" && hasHtmlTags(codetextval)){
            cardsetmeg("说明不能包含HTML标签！");
            return;
        }

            $.ajax({
                url: '/api/codeset.php', // 请求地址
                type: 'POST',   // 请求类型
                data: {
                    url: codeurlval,//获取地址
                    price: codermbval,//价格
                    text: codetextval,//说明
                },
                            success: function(codeset) { // 成功回调函数
                                if(codeset == 500){
                                    cardsetmeg("错误操作！");                                                                
                                }else if(codeset == 200){
                                    cardsetmegs("修改成功！");
                                }else if(codeset == 404){
                                    cardsetmeg("数据表不存在！");  
                                }else if(codeset == 600){
                                    cardsetmeg("修改失败！");  
                                }else{
                                    cardsetmeg("程序错误！");
                                    console.log(codeset);
                                }
                            }
        
            });

    }
}
}

/*全选 or 取消全选*/
const cardcheckAll = document.getElementById("allcode");//全选、全不选按钮
if(cardcheckAll){
    cardcheckAll.addEventListener('click', function(e) {
        const cardcheckBoxes = document.querySelectorAll('input[type="checkbox"][name="codeid"]');//获取所有多选框
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
const delallcard = document.getElementById("delallcode");
if(delallcard){
    delallcard.addEventListener('click', function(e) {
        const cardcheckBoxesxx = document.querySelectorAll('input[type="checkbox"][name="codeid"]');
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
                            url: '/api/codedel.php', // 请求地址
                            type: 'POST',   // 请求类型
                            data: {
                                id:cardcheckBoxesxx_value_str,//id
                            },
                                        success: function(codedel) { // 成功回调函数
                                            if(codedel == 500){
                                                alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                               
                                            }else if(codedel == 200){
                                                alert("<font>(◕ܫ◕)</font> 删除成功！");
                                                setTimeout(function(){
                                                    window.location.reload();//刷新页面
                                                },2000);
                                            }else if(codedel == 600){
                                                alert("<font>(｡ŏ_ŏ)</font> 删除错误！");  
                                            }else{
                                                alert("<font>(｡ŏ_ŏ)</font> 程序错误！");
                                                console.log(codedel);
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
const delcard = document.querySelectorAll('.delcode');//所有删除按钮
if(delcard&&delcard.length>0){
    delcard.forEach(function(delcard){
        delcard.addEventListener('click', function(e) {
            const dcardid = this.getAttribute('data-d');//获取data-d的值
            if(dcardid&&dcardid>0&&dcardid.match(/^[1-9]\d*$/)){
                    if(prompt("请输入“确定删除”，以此来确认您确实需要这么做！")==='确定删除'){
                        $.ajax({
                            url: '/api/codedel.php', // 请求地址
                            type: 'POST',   // 请求类型
                            data: {
                                id:dcardid,//id
                            },
                                        success: function(codedelx) { // 成功回调函数
                                            if(codedelx == 500){
                                                alert("<font>(｡ŏ_ŏ)</font> 错误操作！");                                                               
                                            }else if(codedelx == 200){
                                                alert("<font>(◕ܫ◕)</font> 删除成功！");
                                                setTimeout(function(){
                                                    window.location.reload();//刷新页面
                                                },2000);
                                            }else if(codedelx == 600){
                                                alert("<font>(｡ŏ_ŏ)</font> 删除错误！");  
                                            }else{
                                                alert("<font>(｡ŏ_ŏ)</font> 程序错误！");
                                                console.log(codedelx);
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
const alleditcard = document.querySelectorAll('.editcode');//所有编辑按钮
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
    const cardyesid = document.getElementById('codeid'+yid);
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
                ncarddialogerr('邀请码没有改动！');
            }else{
                    if (neweditcardid&&neweditcardid>0&&neweditcardid.match(/^[1-9]\d*$/)){
                         
                        $.ajax({
                            url: '/api/codeedit.php', // 请求地址
                            type: 'POST',   // 请求类型
                            data: {
                                id:neweditcardid,//id
                                text:neweditcardtext,//值
                            },
                                        success: function(codeeditxx) { // 成功回调函数
                                            if(codeeditxx == 500){
                                                ncarddialogerr('错误操作！');                                                              
                                            }else if(codeeditxx == 200){
                                                ycarddialogerr('修改成功！',neweditcardid,neweditcardtext); 
                                                nowinputvalue=neweditcardtext;
                                            }else if(codeeditxx == 600){
                                                ncarddialogerr('修改失败！');
                                            }else if(codeeditxx == 800){
                                                ncarddialogerr('邀请码已存在！');
                                            }else{
                                                ncarddialogerr('程序错误！');
                                                console.log(codeeditxx);
                                            }
                                        }
                    
                        });
                    }else{
                        ncarddialogerr('参数错误！');
                    }

            }

        }else{
            ncarddialogerr('邀请码不能为空！');
        }
        
    })
}

})