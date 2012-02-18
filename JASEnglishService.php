<?php
	include_once 'Tool.php';
	include_once 'JASEnglishDAO.class.php';
	session_start();		
	if($_POST['insert']) {
		$chinese = $_POST['chinese'];
		$english = $_POST['english'];
		$example = $_POST['example'];
		$flag = JASEnglish::insertNewWord($english, $chinese, $example);
		if($flag) {
			$_SESSION['insert'] = true;
			JTool::printArray($_SESSION);
			header("location:index.php");
		}else {
			$_SESSION['insert'] = false;
			header("location:index.php");
		}
	}
	
	if($_POST['findDynamic']==true) {
		$arr = JASEnglish::getAll();
		$newarr = array();
		foreach($arr as $each) {
			$i = strpos(strtolower($each['english']),strtolower($_POST['str']));
			if($i!==false) {
				$newarr[]=$each;
			}
		}
		echo json_encode($newarr);		
	}