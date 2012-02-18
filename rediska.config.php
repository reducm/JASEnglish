<?php
include 'Rediska.php';
include 'Rediska/Key.php';
include 'config.php'
//include_once 'Tool.php';
$options = array( 'servers'=>array(
  array('host'=> Redis_HOST, 'port'=>Redis_PORT, 'password'=>Redis_PASSWORD)
), 
'namespace' => 'English_'	);	
$rediska = new Rediska($options);

//$key = new Rediska_Key("hi");
//$test = array("a"=>"aaa","b"=>"bbbbbbb","c"=>"cccccccc");
//$key->setValue($test);
/* 	$list = new Rediska_Key_List("testlist");
	$list[] = "fuck1";
	$list[] = "fuck2";
$list[2] = "fuck3"; */
//JTool::printArray($key->getValue());
//JTool::printArray($list);
//$list->delete();
//var_dump($list);


