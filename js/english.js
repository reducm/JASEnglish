var timeoutid;
$(document).ready(function() {
	find = $("#find");	
/*	find.bind('keyup',function(){
		var str = find.val();
		
		if(str.length>0) {
			$.post("JASEnglishService.php",{findDynamic:true,str:str},callback);
		}
		
		function callback (data){
			showWords(eval(data));
		}
	});*/	
	find.bind("keyup",function(){
		clearTimeout(timeoutid);
		timeoutid = setTimeout(function(){
			flagFDY = true;
			var str = find.val();		
			if(str.length>0) {
				$.post("JASEnglishService.php",{findDynamic:true,str:str},callback);
			}		
			function callback (data){
				showWords(eval(data));
				flagFDY = false;
			}
		}, 300);		
	});
});


function findStatic() {
	var findobj = $("#find");
	alert(findobj.val());
}

function showWords(data) {
	var str = "<table style=\"border:1px solid orange;\" >";
	str += "<tr><td>English</td><td>Chinese</td><td>Example</td><td>最后修改时间</td></tr>";
	for(var i in data) {
		var word = data[i];
		str+="<tr wid=\""+word.id+"\"   >";
		str+="<td>"+word.english+"</td>";
		str+="<td>"+word.chinese+"</td>";
		str+="<td>"+word.example+"</td>";
		str+="<td>"+word.created_at+"</td>";
		str+="</tr>";
	}
	str += "</table>";
	$("#result").html(str);
}