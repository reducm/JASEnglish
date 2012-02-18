<?php
	include_once 'db.class.php';
	include_once 'Tool.php';
	include_once 'rediska.config.php';
	
	class JASEnglish {
		static function insertNewWord ($english,$chinese,$example){
			$mysqli = DB::getConn();
			$sql = "insert into english(english,chinese,example,created_at) values('{$english}','{$chinese}','{$example}',now())";
			JTool::println($sql);
			$mysqli->query($sql);
			$flag = DB::printError($mysqli);
			$sql = "select * from english where id = (select max(id) from english);";
			$result = $mysqli -> query($sql);
			$arr = $result->fetch_assoc();
			$key = new Rediska_Key("allEnglish");
			if($allenglish = $key->getValue()) {
				$allenglish[]=$arr;
				$key->setValue($allenglish);
			}
			DB::close($mysqli,null,$result);
			return $flag;
		}
		
		static function getAll (){
			$key = new Rediska_Key("allEnglish");
			//var_dump($key->getValue());
			if($allenglish = $key->getValue()) {
				//echo "from redis";
				return $allenglish;
			}else{
				$mysqli = DB::getConn();
				$sql = "select * from english";
				$result = DB::getResult($sql, $mysqli);
				$arr = array();
				while($array = $result -> fetch_assoc()){
					$arr[] = $array;
				}
				$key->setValue($arr);
				$key->expire(60*60);
				DB::close($mysqli,null,$result);
				//echo "from db";
				return $arr;
			}
		}
				
		static function getChinese($english) {
			$mysqli = DB::getConn();
			$sql = "select * from english where english = '{$english}'";
			$result = DB::getResult($sql, $mysqli);
			$arr = array();
			while($array = $result -> fetch_assoc()){
				$arr[] = $array;
			}
			DB::close($mysqli,null,$result);
			return $arr;
		}
		
		static function update($id,$english,$chinese,$example){
			$mysqli = DB::getConn();
			$sql = "update from english set english={$english} chinese={$chinese} example={$example} created_at=now() where id={$id}";
			$mysqli->query($sql);			
		}
		
		static function getFromEnglish($english) {
			$mysqli = DB::getConn();
			$sql = "select * from english where english='{$english}'";
			$result = DB::getResult($sql, $mysqli);
			$arr = array();
			while($array = $result -> fetch_assoc()){
				$arr[] = $array;
			}
			DB::close($mysqli,null,$result);
			return $arr;
		}
	}
	