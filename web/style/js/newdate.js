const datebut = document.getElementById("newdate");
var newdataerrdiv = document.getElementById("newdataerr");//签到提示元素
let newtimeoutId = null; // 用于存储计时器ID

if (datebut && newdataerrdiv) {
    datebut.onclick = function () {
        const newugoldtext = document.getElementById("newugoldtwo");
        const newugoldtwoone = document.getElementById("newugoldone");

        if (datebut.innerHTML === "签到" && (newugoldtext || newugoldtwoone)) {
            $.ajax({
                url: '/inc/date.php',
                type: 'POST',
                dataType: 'json',
                success: function (datavipf) {
                    if (datavipf.err === 400) {
                        const newugoldtextsize = Number(newugoldtwoone.innerHTML);
                        if (newugoldtext) newugoldtext.innerHTML = newugoldtextsize + Number(datavipf.txt);
                        if (newugoldtwoone) newugoldtwoone.innerHTML = newugoldtextsize + Number(datavipf.txt);
                        if(datavipf.txt>0){
                            showMessage(newdataerrdiv, "(◕ܫ◕) 签到成功,今日获得 " + datavipf.txt + " 积分！", "#8BC34A");
                        }else{
                            showMessage(newdataerrdiv, "(◕ܫ◕) 签到成功！", "#8BC34A");
                        }
                        datebut.innerHTML = "已签到";
                        datebut.setAttribute("id", "newdatenull");
                        datebut.setAttribute("disabled", "disabled");
                    } else {
                        let message = "(｡ŏ_ŏ) 签到失败！";
                        if (datavipf.err === 404) message = "(｡ŏ_ŏ) 今天已经签过到了！";
                        showMessage(newdataerrdiv, message);

                    }
                }
            });
        } else {
            showMessage(newdataerrdiv, "(｡ŏ_ŏ) 错误操作！");
        }
    };
} else {
    const datebutnull = document.getElementById("newdatenull");
    if (datebutnull) {
        datebutnull.onclick = function () {
            showMessage(newdataerrdiv, "(｡ŏ_ŏ) 今天已经签过到了！");
        };
    }
}

function showMessage(elementdiv, text, backgroundColor = "") {
   // 清除之前的计时器
    if (newtimeoutId) {
    clearTimeout(newtimeoutId);
    }
    elementdiv.classList.add('show');
    elementdiv.classList.remove('hide');
    elementdiv.style.display = "block";
    elementdiv.style.backgroundColor = backgroundColor;
    elementdiv.innerHTML = text;
    newtimeoutId = setTimeout(() => {
      elementdiv.classList.remove('show');
      elementdiv.classList.add('hide');
      elementdiv.addEventListener('animationend', () => {
        elementdiv.style.display = "none";
        elementdiv.innerHTML = "";
        elementdiv.style.backgroundColor = "";
        elementdiv.classList.remove('hide');
      }, { once: true });
    }, 2000);
}