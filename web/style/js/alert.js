//更改默认弹窗样式
window.alert=function (msg){
	//创建弹出窗口（用DIV），窗口上的内容包括一个显示文本字符串的容器和一个按钮
	var dd=document.createElement('div');
	document.body.appendChild(dd);
    var box=document.createElement('div');
	var msgbox=document.createElement('div');
	dd.appendChild(box);
	box.appendChild(msgbox);
	var btn=document.createElement('button');
	box.appendChild(btn);
	dd.id='a2';
	box.id='a1';
	dd.style.cssText="width:100%;height:100%;position: fixed;background-color:rgb(0 0 0 / 15%);top:0;left:0;z-index:9999999;";
	

    //设置弹出窗口的显示样式
	box.style.cssText="padding: 10px;background:var(--alert-bg-color);border-radius: 5px;color:var(--alert-font-color);text-align: center;border: 1px solid var(--lvse-color);box-shadow: 6px 8px 2px 0px var(--lvsetm-color);";
	
    //设置弹出窗口的位置
	box.style.position="fixed";
	box.style.top='25%';
	box.style.left='50%';	
    
    //设置弹出窗口上的文本内容及其样式
	msgbox.id="t1";
	msgbox.style.cssText="padding:10px;";
	msgbox.innerHTML=msg;
	
	//获取当前盒子宽高
	var a1 = msgbox;
    var width= a1.clientWidth||a1.offsetWidth;
	var height= a1.clientHeight||a1.offsetHeight;
	var w=(width+20)/2;
	console.log(w);
    box.style.margin='0px 0px 0px ' +"-"+ w + 'px';
 
    //设置按钮
	btn.innerHTML="确定";
	btn.style.cssText="width: 100%;margin-top: 5px;padding: 5px 15px;background-color:var(--alert-button-color);border: 0;color:var(--alert-button-font-color);";
 
	 //给键盘按动事件，用于删除多余弹窗，避免按键回车造成多个弹窗叠加
  	document.addEventListener('keydown', function() {  
			if (document.body.contains(dd)) {  
			document.body.removeChild(dd);  
			console.log("先关闭弹窗再按键盘！"); 
		};
	}); 
	
    //给单击事件
	dd.onclick=function (){
	document.body.removeChild(dd);//点击背景后让alert窗口消失
	return;	
};
};