window.onload = function() {
	// alert("读完");
};
function setInput(event) {
	var me = event || window.event;
	if (me.keyCode == 13) {
		send();
	}
}
// 手写ajax真系好7辛苦
function send() {
	var text = document.getElementById("text").value;
	var img = document.getElementById("img").value;
	if (text.length == 0 || text == "") {
		alert("输入D野好播");
		document.getElementById("text").focus();
		return false;
	}
	var json = {
		fa : "fa",
		text : text,
		img : img
	};	
	jasPostAjax("POST", "ajax.php", json, callback); // 调用 自己写果个类似jquery$.post个方法
	function callback(data) {
		alert(data);
		var result = document.getElementById("result");
		result.innerHTML = data;
	}
}

/**
 * 
 * @param type String "post"
 * @param url string "url"
 * @param JSONdata json {key:value}
 * @param method string "method name"
 * @returns {Boolean}
 */
function jasPostAjax(type, url, JSONdata, method) {
	var req;
	if (typeof type != "string" && type != "POST") { // 一堆检查 参数是否符合要求
		alert("使用jasajax参数的type出错");
		return false;
	} else if (typeof url != "string") {
		alert("使用jasajax参数的url出错");
		return false;
	} else if (typeof JSONdata != "object") {
		alert("使用jasajax参数的JSONdata出错");
		return false;
	}

	if (window.XMLHttpRequest) { // 创建XMLHttpRequest对象,姐系ajax对象,固定写法,兼容ie同其他浏览器,记住
		req = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		req = new ActiveXObject("Microsoft.XMLHTTP");
	}

	var senddata = json2str(JSONdata); // 用左下面的方法,将json对象转成 属性=值&属性=值果种方式

	req.open(type, url, true); // ajax对象 使用,
								// 第一个参数系post或者get,url就系链接后台servlet的url,true就系代表异步传输,姐系更新数据不刷新页面
	req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // 关键,E行唔填的话,后台接收唔到参数的,固定写法记住就得
	req.send(senddata); // post方法就要写E行,里面就系 属性=值&属性=值&属性=值&属性=值 果种方式传去后台

	req.onreadystatechange = function() {
		if (req.readyState == 4) { // 0系open失败,1系后台loding,2系loaded,4就系成功
			if (req.status == 200) {// 200就系页面数据成功,都系固定写法,记住
				alert(req.responseText);
				method(req.responseText); // 调用个回调函数,传个后台返回来的数据返去
			}
		}
	};
}
/**
 * 将简单的{a:b,c:d}格式转成 a=b&c=d url格式
 * @param json
 * @returns
 */
function json2str(json) {
	var str = "";
	var jlength = 0;
	if (typeof json == "object") {
		for ( var m in json) {
			jlength++;
		}
		var count = 0;
		for ( var n in json) {
			if (count == jlength - 1) {
				str += n + "=" + json[n];
			} else {
				str += n + "=" + json[n] + "&";
			}
			count++;
		}
	} else {
		alert("传入的参数不是json!");
		return false;
	}
	// alert("jason to str后的str"+str);
	return str;
}

function createDarkBg() {
	if(document.getElementById("darkbg")){
		return false;
	}
	var darkbg = document.createElement("div");
	darkbg.id = "darkbg";
	var heightwhencreate = document.documentElement.scrollHeight;
	darkbg.setAttribute("style",
								"position:absolute;" +
								"width:100%;"/*+document.documentElement.clientWidth+"px;"*/+
								"height:"+heightwhencreate+"px;"+
								"background:#222;" +
								"top:0px;" +
								"left:0px;" +
								"z-index:10;" +
								"opacity:0.7;" +
								"filter:alpha(opacity=70);");
	document.body.appendChild(darkbg);
}

function closeDarkBg() {
	if(document.getElementById("darkbg")){
		var darkbg = document.getElementById("darkbg");
		darkbg.parentNode.removeChild(darkbg);
	}else {
		return;
	}
}

function attachEvent(obj,evt,fn){
	 if(obj.addEventListener){
	  obj.addEventListener(evt, fn, false);
	 }else if(obj.attachEvent){
	  obj.attachEvent('on'+evt, fn);
	 }
}

/**
 * 获取光标在textarea 或者 text里面的位置
 * @param ctrl 传入textarea或者text的节点
 * @returns {Number}
 */
function getCursortPosition (ctrl) {
	var CaretPos = 0;	// IE Support
	if (document.selection) {
	ctrl.focus ();
		var Sel = document.selection.createRange ();
		Sel.moveStart ('character', -ctrl.value.length);
		CaretPos = Sel.text.length;
	}else if (ctrl.selectionStart || ctrl.selectionStart == '0') // Firefox support
		CaretPos = ctrl.selectionStart;
	return (CaretPos);
}


function setCaretPosition(ctrl, pos){//设置光标位置函数
	if(ctrl.setSelectionRange)
	{
		ctrl.focus();
		ctrl.setSelectionRange(pos,pos);
	}
	else if (ctrl.createTextRange) {
		var range = ctrl.createTextRange();
		range.collapse(true);
		range.moveEnd('character', pos);
		range.moveStart('character', pos);
		range.select();
	}
}

/**
 * 遍历json对象，输出类似{a:'xxx',b:'fuck',c:{m:'爱你',n:'叼你'}}的字符串
 */
function jsonIterator(json) {
	var str = "{";
	var count = 0;	
	for(var i in json) {
		count ++;
		str+= i+":";
		str += isJson(json[i]) ? jsonIterator(json[i]) : json[i]; //判断是否json,是的话递归，不是的话就直接等于值
		str += ",";
	}
	str = str.substring(0, str.length-1);
	str += "}";
	return str;
}

/**
 * 判断传入对象是否json
 */
function isJson(obj) {
	var isjson = typeof(obj) == "object" && Object.prototype.toString.call(obj).toLowerCase() == "[object object]" && !obj.length;    
	return isjson;
}