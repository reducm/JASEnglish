<?php
	include_once  'config.php';
	//include_once '../control/memmgr.class.php';
	
	class DB {
		/**
		 * 
		 * 获取mysqli链接
		 */
		static function getConn() {
			$mysqli = new mysqli(DB_HOST,DB_NAME,DB_PASSWORD,DB_DBNAME,DB_PORT);
			if(mysqli_connect_errno()) {
				echo "数据库链接失败".mysqli_connect_error();	
			}
			$mysqli -> query("set names utf8");
			return  $mysqli;		
		} 
		
		/**
		 * 设计连接池,mysqli数组 array("no"=>1,"mysqli"=>mysqli,"busy"=>false,"isNew"=>false)
		 */
	/*	static function getConnFromPool() {
			if($mysqliMemarray = MemMgr::GetMem("mysqli")){
				$mysqliarray = array();
				foreach($mysqliMemarray as $each) {
					if(!$each['busy']) {
						$each['busy'] = true;
						MemMgr::SetMem("mysqli", $mysqliMemarray, 0);
						$mysqliarray = $each;						
						break;
					}
				}
				if(isset($mysqliarray['mysqli'])) {
					echo "从Mem连接池拿到数据啦!";
					return $mysqliarray;
				}else {
					//所有线程都忙
					$mysqli = self::getConn();
					$mysqliarray['mysqli'] = $mysqli;
					$mysqliarray['busy'] =false;
					$mysqliarray['isNew'] = true;
					$mysqliarray['no'] = 100;
					echo "所有线程都忙";	
					return $mysqliarray;						
				}				
			}else{
				echo "走到那";
				$mysqliMemarray = array();
				$temp = array();
				for($i=0;$i<1;$i++){
					$mysqli = self::getConn();
					$mysqli -> query("set names utf8");
					$temp['mysqli'] = $mysqli;
					$temp['busy'] =false;
					$temp['isNew'] = false;
					$temp['no'] = $i;
					$mysqliMemarray[] =$temp;
				}
				MemMgr::SetMem("mysqli", $mysqliMemarray, 0);
				self::getConnFromPool();
			}
		}*/
		
		static function getResult($sql,mysqli $mysqli) {
			$result = $mysqli -> query($sql);  
			return $result;
		}

		
		/**
		 * 
		 * 关闭 stmt result mysqli的函数,传入这三个东西就行
		 * @param mysqli,stmt,result
		 */
		static function close(mysqli $mysqli=null,mysqli_stmt $stmt=null,mysqli_result $result=null){
			if($result !=null)
				$result -> free();
			if($stmt !=null)
				$stmt -> close();
			if($mysqli !=null)
				$mysqli ->close();
		}
		
		/*static function closeArray(array $mysqliarry=null,mysqli_stmt $stmt=null,mysqli_result $result=null){
			if($result !=null)
				$result -> free();
			if($stmt !=null)
				$stmt -> close();
			if($mysqli !=null){
				if($mysqliarry['isNew'] == true && $mysqli['no'] == 100) {
					unserialize($mysqliarray['mysqli'])->close();
				}else {
					$mysqliMemarray = MemMgr::GetMem("mysqli");
					$no = $mysqliarry['no'];
					$mysqliMemarray[$no]['busy']=false;
					MemMgr::SetMem("mysqli", $mysqliMemarray, 0);
					 echo "从Mem连接池释放线程成功啦!";
					unserialize($mysqliarry['mysqli'])->close();
				}
			}				
		}*/
		
		static function printError($e){
			if($e->errno) {
				echo $e->error;
				return false;
			}else {
				return true;
			}
		}
	}
