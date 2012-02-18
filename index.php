<?php
	include_once 'Tool.php';
	//JTool::printArray($_GET);	
	session_start();
	//$insert = $_SESSION['insert'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=${encoding}">
<title>JASEnglish</title>
<link rel="stylesheet" type="text/css" href="css/jas.css " />
<link rel="shortcut icon" href="css/ghost.ico" type="image/x-icon" />
<script src="js/jquery.js" type="text/javascript" charset="utf-8"></script>
<script src="js/jas.js<?php echo "?".rand(1, 20)?>" type="text/javascript" charset="utf-8"></script>
<script src="js/english.js<?php echo "?".rand(1, 20)?>" type="text/javascript" charset="utf-8"></script>
</head>

<body>
	<div class="orangediv" id="search">
		<input type="text" id="find" />
		<input type="button" value="查  找" onclick="findStatic()">
	</div>
	
	<div class="orangediv" id="add">
		<form method="post" action="JASEnglishService.php">
			新单词：</br>
			英语：<input type="text" name="english" autocomplete="off" /></br>
			中文：<input type="text" name="chinese" autocomplete="off" /></br>
			英文例句：<input type="text" name="example" autocomplete="off" />
			<input type="hidden" name="insert" value="insert" />	
			<input type="submit" value="提  交" />
		</form>
		</div>
	
	<div id="result">
		<?php 
			if($_SESSION['insert']===true){
				echo "单词插入成功！";
				unset($_SESSION['insert']);				
			}else {}
		?>
	</div>	
	
	<script type="text/javascript">
	</script>
</body>

</html>