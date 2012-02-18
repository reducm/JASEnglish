<?php
	//users:id, uid, name, province, city, domain, gender, followers_count, friends_count, statuses_count, created_at, verified,  last_login
	//followers: whos(uid),ids;ids:111112312|12312323|123123123|12312323|12321323
	//friends:whos(uid),ids;
	//guests:id,uid,name,profile_img,province,city,gender,followers_count,friends_count.statuses_count,created_at,verified
	include_once 'db.class.php';
	include_once '../tool/date.php';
	include_once '../tool/Tool.php';
	class BaoDB {
		static function setUser(array $user) {
			$mysqli = DB::getConn();
			$sql = "insert into users(uid,name,profile_img,province,city,domain,gender,followers_count,friends_count,statuses_count,created_at,verified,last_login) values(?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$stmt = $mysqli ->prepare($sql);
			$stmt ->bind_param("sssiissiiiisi", $uid, $name, $profile_img,$province, $city, $domain, $gender, $followers_count, $friends_count, $statuses_count, $created_at, $verified,  $last_login);
			$uid = $user['id'];
			$name = $user['name'];
			$profile_img = $user['profile_image_url'];
			$province = $user['province'];
			$city = $user['city'];
			if($user['domain']!=''){
				$domain = $user['domain'];
			}else {
				$domain = $user['id'];
			}
			$gender = $user['gender'];
			$followers_count = $user['followers_count'];
			$friends_count = $user['friends_count'];
			$statuses_count =$user['statuses_count'];
			$created_at = JDate::weiboDateToTimeStamp($user['created_at']);
			$verified = $user['verified'];
			$last_login =time();
			$stmt -> execute();	
			DB::close($mysqli,$stmt);
		}
		
	static function updateUser(array $user) {
			$mysqli = DB::getConn();
			$sql = "update users set profile_img=?, followers_count=?,friends_count=?,statuses_count=?,last_login=? where uid='{$user['id']}'";
			$stmt = $mysqli ->prepare($sql);
			$stmt ->bind_param("siiii", $profile_img,$followers_count, $friends_count, $statuses_count, $last_login);
			$profile_img = $user['profile_image_url'];
			$followers_count = $user['followers_count'];
			$friends_count = $user['friends_count'];
			$statuses_count =$user['statuses_count'];
			$last_login =time();
			$stmt -> execute();	
			DB::close($mysqli,$stmt);
		}
		/**
		 * 判断数据库之前有没有这个USER,没有的话就是新USER
		 * @param unknown_type $name
		 */
		static function hasUser($name){
			$mysqli = DB::getConn();
			$sql = "select id from users where name ='{$name}'";
			$result = DB::getResult($sql,$mysqli);
			$array = $result-> fetch_assoc();
			DB::close($mysqli,null,$result);
			if(isset($array['id'])) {
				return false;
			}else{
				return true;
			}
		}
		
		/**
		 * 拿上次登录时间的timestamp
		 * @param 传入$me[name];
		 */
		static function getLastLogin($name){
			//
			$mysqli = DB::getConn();
			$sql = "select last_login from users where name ='{$name}'";
			$result = DB::getResult($sql,$mysqli);
			$array = $result-> fetch_assoc();
			DB::close($mysqli,null,$result);	
			return  $array['last_login']; 			
		}
		static function setFollowersAndFriends($uid,array $followers,array $friends){
			self::setFollowers($uid,$followers);
			self::setFriends($uid,$friends);
		}
		static function setFollowers($uid, array $followers){			
			$str = self::arrayToString($followers);
			$mysqli = DB::getConn();			
			$sql = "insert into followers(whos,ids,created_at) values(?,?,?)";
			$stmt = $mysqli ->prepare($sql);
			if($mysqli->errno){
				echo $mysqli->error;
			}
			$stmt ->bind_param("ssi",$whos,$ids,$create_at);		
			$whos = $uid;
			$ids = $str;
			$create_at = time();
			$stmt -> execute();				
			DB::close($mysqli,$stmt);
		}
		
		static function setFriends($uid,array $friends){
			$str = self::arrayToString($friends);			
			$mysqli = DB::getConn();
			$sql = "insert into friends(whos,ids,created_at) values(?,?,?)";
			$stmt = $mysqli ->prepare($sql);
			$stmt ->bind_param("ssi",$whos,$ids,$create_at);
			$whos = $uid;
			$ids = $str;
			$create_at = time();
			$stmt -> execute();	
			DB::close($mysqli,$stmt);
		}
		
		static function updateFollowersAndFriends($uid,array $followers,array $friends){
			self::updateFollowers($uid,$followers);
			self::updateFriends($uid,$friends);
		}
		
		static function updateFollowers($uid,array $followers){
			$str = self::arrayToString($followers);			
			$mysqli = DB::getConn();
			$now = time();
			$sql = "update followers set  ids='{$str}', created_at='{$now}' where whos ='{$uid}'";			
			DB::close($mysqli);
		}
		
		static function updateFriends($uid,array $friends) {
			$str = self::arrayToString($friends);			
			$mysqli = DB::getConn();
			$now = time();
			$sql = "update friends set  ids='{$str}', created_at='{$now}' where whos ='{$uid}'";			
			DB::close($mysqli);
		}
		
		/**
		 * 拿出传入$uid用户的粉丝ids数组
		 * @param unknown_type $uid
		 */
		static function getFollowers($uid){
			$sql = "select ids from followers where whos ='{$uid}'";
			$mysqli = DB::getConn();			
			$result = $mysqli->query($sql);
			$str = $result-> fetch_assoc();
			$array = explode("|", $str['ids']);
			DB::close($mysqli,null,$result);
			return $array;
		}
		/**
		 * 拿出传入$uid用户的关注ids数组
		 * @param unknown_type $uid
		 */
		static function getFriends($uid){			
			$sql = "select ids from friends where whos ='{$uid}'";
			$mysqli = DB::getConn();
			$result = $mysqli->query($sql);
			$str = $result-> fetch_assoc();
			$array = explode("|", $str['ids']);
			DB::close($mysqli,null,$result);	
			return $array;
		}
		/**
		 * 更新followers表
		 * @param $uid  属于谁的朋友
		 * @param $friends ids数组
		 */
		/*static function updateFollowers($uid,array $followers){
			$str = self::arrayToString($followers);
			$mysqli = DB::getConn();		
			$sql = "update followers set ids =?,created_at=? where whos='{$uid}'";
			$stmt = $mysqli ->prepare($sql);
			if($mysqli->errno){
				echo $mysqli->error;
			}
			$stmt ->bind_param("si",$ids,$create_at);
			$ids = $str;
			$create_at = time();
			$stmt -> execute();				
			DB::close($mysqli,$stmt);
		}*/
		/**
		 * 更新friends表
		 * @param $uid  属于谁的朋友
		 * @param $friends ids数组
		 */
/*		static function updateFriends($uid, array $friends){
			$str = self::arrayToString($friends);
			$mysqli = DB::getConn();		
			$sql = "update friends set ids =?,created_at=? where whos='{$uid}'";
			$stmt = $mysqli ->prepare($sql);
			if($mysqli->errno){
				echo $mysqli->error;
			}
			$stmt ->bind_param("si",$ids,$create_at);
			$ids = $str;
			$create_at = time();
			$stmt -> execute();				
			DB::close($mysqli,$stmt);
		}*/
		/**
		 * 传入ids数组时,把数组转成字符串,形式123123123|12312312312|12312312| 
		 * @param array $arr
		 */
		static function arrayToString(array $arr){
			$str="";
			$count = count($arr);
			for($i=0;$i<$count;$i++){
				if($i != $count-1){
					$str.=$arr[$i]."|";
				}else{
					$str.=$arr[$i];
				}
			}
			return $str;
		}
		/**
		 * 处理详细用户信息的函数,会写入或更新数据库
		 * @param $guests 传入索引型数组,每个下标对应某一个用户的详细信息 
		 */
		static function dealGuests($guests){
			//JTool::printArray($guests);
			$mysqli = DB::getConn();
			$mysqli->autocommit(0);
			foreach($guests as $user){
				$uid = $user['id'];
				$sql = "select id from guests where uid ='{$uid}'";
				$result = DB::getResult($sql,$mysqli);
				$id = $result-> fetch_assoc();
				if(isset($id['id'])) {
					//echo "更新用户".BR;
					self::updateGuest($user,$mysqli);
				}else{
					//echo "添加用户".BR;
					self::setGuest($user,$mysqli);
				}
				$result->free();
			}
			$mysqli->commit();
			$mysqli->autocommit(1);
			DB::close($mysqli);
		}
		
		static function setGuest($user,mysqli $mysqli){			
			$sql = "insert into guests(uid,name,profile_img,province,city,gender,followers_count,friends_count,statuses_count,created_at,verified) values(?,?,?,?,?,?,?,?,?,?,?)";
			$stmt = $mysqli->prepare($sql);
			if($mysqli->errno){
				echo $mysqli->error;
			}
			$stmt->bind_param("sssiisiiiis",$uid,$name,$profile_img,$province,$city,$gender,$followers_count,$friends_count,$statuses_count,$created_at,$verified);	
			$uid = $user['id'];
			$name = $user['name'];
			$profile_img=$user['profile_image_url'];
			$province = $user['province'];
			$city = $user['city'];
			$gender = $user['gender'];
			$followers_count = $user['followers_count'];
			$friends_count = $user['friends_count'];
			$statuses_count =$user['statuses_count'];
			$created_at = JDate::weiboDateToTimeStamp($user['created_at']);
			$verified = $user['verified'];		
			$stmt->execute();
			if($stmt->errno){
				echo "stmterro:".$stmt->error;
			}
			$stmt->close();
		}		
		
		static function updateGuest($user,mysqli $mysqli) {			
			$sql = "update guests set name=?,profile_img=?,province=?,city=?,gender=?,followers_count=?,friends_count=?,statuses_count=?,verified=? where uid = '{$user['id']}'";
			$stmt = $mysqli->prepare($sql);
			if($mysqli->errno){
				echo $mysqli->error;
			}
			$stmt->bind_param("ssiisiiis",$name,$profile_img,$province,$city,$gender,$followers_count,$friends_count,$statuses_count,$verified);		
			$name = $user['name'];
			$profile_img=$user['profile_image_url'];
			$province = $user['province'];
			$city = $user['city'];
			$gender = $user['gender'];
			$followers_count = $user['followers_count'];
			$friends_count = $user['friends_count'];
			$statuses_count =$user['statuses_count'];
			$verified = $user['verified'];	
			$stmt->execute();
			if($stmt->errno){
				echo "stmterro:".$stmt->error;
			}
			$stmt->close();
		}
		
		static function getUsersDetail(array $uids){
			$str = '';
			$count = count($uids);
			for($i=0;$i<$count;$i++){
				if($i != $count-1) {
					$str.=$uids[$i].",";
				}else{
					$str.=$uids[$i];
				}
			}
			$sql = "select uid,name,profile_img,gender,friends_count,followers_count,statuses_count from guests where uid in({$str})";
			$mysqli = DB::getConn();
			$result = $mysqli->query($sql);
			if($mysqli ->errno){
				echo $mysqli->error;
			}
			$usersdetail = array();
			while($row = $result->fetch_assoc()){
				$usersdetail[] = array(
				"id"=>$row['uid'],
				"name"=>$row['name'],
				"profile_image_url"=>$row['profile_img'],
				"gender"=>$row['gender'],
				"friends_count"=>$row['friends_count'],
				"followers_count"=>$row['followers_count'],
				"statuses_count"=>$row['statuses_count']
				);
			}
			if($mysqli ->errno){
				echo $mysqli->error;
			}
			DB::close($mysqli,null,$result);
			return $usersdetail;
		}		
		
/*--表1:vote(投票表):id 题目(tittle) 发起人(uid)  返回微博地址(mid) 是否多选,默认单选是1,最大5(vote_type) 使用哪个投票图片模板,默认是0(template)日期(created_at) isssi
--表2:be_voted(涉及人表):id 投票id(vid) 涉及人(bevoted_uid) 描述(describe)  票数(score) iissi
--表3:voter(投票人表):id 投票id(vid) 投票人(uid) 投给谁(bevoted_uid) 投票原因(reason) 返回微博地址(mid) 日期(created_at) isssssi*/
		static function buildVote($uid, $tittle, $vote_type, array $options,$template) {
			$mysqli = DB::getConn();
			$mysqli -> autocommit(0);
			$sql = "insert into vote(tittle,uid,vote_type,created_at,template) values(?,?,?,?,?)";
			$stmt =$mysqli ->prepare($sql);
			DB::printError($mysqli);
			$stmt->bind_param("ssiii", $tittle,$uid,$vote_type,$created_at,$template);
			$created_at = time();
			$stmt->execute();
			$mysqli->commit();
			
			$sql = "select max(id) from vote where uid = '{$uid}'"; //获取上面设入的vid
			$result = $mysqli ->query($sql);
			DB::printError($mysqli);
			$temp = $result->fetch_array();
			$vid = $temp[0];
						
			$sql = "insert into be_voted values(null,{$vid},?,?,'0')";
			$stmt = $mysqli->prepare($sql);			
			DB::printError($mysqli);
			DB::printError($stmt);
			$stmt->bind_param("ss",$bevoted_uid,$describe);
			
			foreach($options as $key=>$value) {
				$bevoted_uid = $key;
				$describe = $value;
				$stmt->execute();
				DB::printError($stmt);
			}			
			$mysqli->commit();				
			$mysqli -> autocommit(1);
			DB::close($mysqli,$stmt,null);
			return $vid;
		}
		
		static function deleteVote($vid){			
			$sql = "delete from vote where id = {$vid}";
			$mysqli = DB::getConn();
			$mysqli->autocommit(0);
			$mysqli->query($sql);			
			$sql = "delete from be_voted where vid ={$vid}";
			$mysqli->query($sql);
			$sql = "delete from voter where vid ={$vid}";
			$mysqli->query($sql);
			$sql = "delete from voter_bids where vid ={$vid}";
			$mysqli->query($sql);
			$mysqli->commit();
			$mysqli->autocommit(1);
			DB::close($mysqli,null,null);	
			return true;
		}
		/**
		 * 更新发起投票的mid
		 */
		static function updateVoteMid($vid,$mid){
			$sql = "update vote set mid={$mid} where id={$vid}";
			echo $sql.BR;
			$mysqli = DB::getConn();
			$mysqli->query($sql);
			if($mysqli->errno){
				echo $mysqli->error;
				return false;
			}else{
				return true;
			}						
		}
		/**
		 * 更新参与投票的mid
		 */
		static function updateVoterMid($voterid,$mid) {
			
		}
		//--表3:voter(投票人表):id 投票id(vid) 投票人(uid) 投给谁(bevoted_uid) 投票原因(reason) 返回微博地址(mid) 日期(created_at) isssssi
		static function voteToSomebody($vid,$uid,$reason,array $voteoptions){
			$now = time();
			$sql = "insert into voter(vid,uid,reason,created_at) values('{$vid}','{$uid}','{$reason}','{$now}')";
			$mysqli = DB::getConn();
			$mysqli->autocommit(0);
			$stmt = $mysqli->prepare($sql);
			DB::printError($mysqli);DB::printError($stmt);
			$stmt -> execute();
			$mysqli -> commit();
			
			$sql = "select max(id) from voter where uid ='{$uid}'";
			$result = $mysqli -> query($sql);
			$row = $result ->fetch_array();
			$voterid = $row[0];
			
			$sql = "insert into voter_bids(vid,voterid,bevoted_uid) values('{$vid}','{$voterid}',?)";
			$sql2 = "update be_voted set score=score+1 where vid='{$vid}' and bevoted_uid =?";
			$stmt = $mysqli->prepare($sql);
			$stmt2 = $mysqli->prepare($sql2);
			$stmt->bind_param("s", $bid);
			$stmt2->bind_param("s", $bid2);
			foreach($voteoptions as $value) {
				$bid = $value;
				$bid2 = $value;				
				$stmt->execute();
				$stmt2->execute();
			}					
			$mysqli->commit();
			$ar = $mysqli->affected_rows;
			if($ar <0){
				$mysqli->autocommit(1);
				DB::close($mysqli,$stmt,$result);			
				return false;
			}				
			DB::printError($mysqli);DB::printError($stmt);
			$mysqli->autocommit(1);
			DB::close($mysqli,$stmt,null);		
			return true;	
		}
		
		//select v.id,v.tittle,v.uid,v.mid,v.created_at,b.bevoted_uid,b.describe,b.score from vote v,be_voted b where v.id = b.vid and v.uid=2255700674;
		 //select v.id,v.tittle,v.uid,v.mid,v.created_at,b.bevoted_uid,g.name,b.describe,b.score from vote v,be_voted b,guests g where v.id = b.vid and g.uid=b.bevoted_uid order by created_at desc;
		static function getUserCreateVotes($uid,$name,$profile_img) {
			$votes = array();
			$sql = "select v.id,v.tittle,v.mid,v.vote_type,v.template,v.created_at,b.bevoted_uid,g.name,g.profile_img,b.describe,b.score from vote v,be_voted b,guests g where v.id = b.vid and v.uid={$uid} and g.uid=b.bevoted_uid order by created_at desc";
			$mysqli = DB::getConn();
			$result = $mysqli->query($sql);			
			while($row = $result->fetch_assoc()){				
				if(isset($votes[$row['id']])){
					$votes[$row['id']]['options'][]=array("bid"=>$row['bevoted_uid'], "name"=>$row['name'],"profile_image_url"=>$row['profile_img'],"describe"=>$row['describe'], "score"=>$row['score']);
					$votes[$row['id']]['totalscore']+=$row['score'];			
				}else {
					$votes[$row['id']]=array("tittle"=>$row['tittle'],"mid"=>$row['mid'],"vote_type"=>$row['vote_type'],"template"=>$row['template'],"created_at"=>$row['created_at'],"options"=>array());
					$votes[$row['id']]['options'][]=array("bid"=>$row['bevoted_uid'],"name"=>$row['name'], "profile_image_url"=>$row['profile_img'],"describe"=>$row['describe'], "score"=>$row['score']);
					$votes[$row['id']]['totalscore']+=$row['score'];
				}
			}
			DB::close($mysqli,null,$result);			
			$fixvotes = array();
			$aleady = self::getAlreadyVotes($uid);
			foreach($votes as $key=>$value) {
				if(in_array($key, $aleady)) {
					$fixvotes[]=array("vid"=>$key,"tittle"=>$value['tittle'],"uid"=>$uid,"name"=>$name,"profile_image_url"=>$profile_img,"vote_type"=>$value['vote_type'],"options"=>$value['options'],"mid"=>$value['mid'],"template"=>$value['template'],"created_at"=>$value['created_at'],"already_vote"=>1,"totalscore"=>$value['totalscore']);
				}else{
					$fixvotes[]=array("vid"=>$key,"tittle"=>$value['tittle'],"uid"=>$uid,"name"=>$name,"profile_image_url"=>$profile_img,"vote_type"=>$value['vote_type'],"options"=>$value['options'],"mid"=>$value['mid'],"template"=>$value['template'],"created_at"=>$value['created_at'],"alreadt_vote"=>0,"totalscore"=>$value['totalscore']);
				}							
			}
			return $fixvotes;
		}

		static function getVoteFromVid($vid,$uid=null) {
			$votes = array();
			$sql = " select v.id,v.tittle,v.uid,u.name cname,u.profile_img cprofile_img,v.mid,v.vote_type,v.template,v.created_at,b.bevoted_uid,g.name,g.profile_img,b.describe,b.score,t.totalscore totalscore 
						from (select sum(score) totalscore from be_voted b2 where b2.vid={$vid}) t,vote v,be_voted b,guests g,users u
						where v.id={$vid} and v.id = b.vid and g.uid=b.bevoted_uid and v.uid=u.uid
						order by created_at desc";
			$mysqli = DB::getConn();
			$result = $mysqli->query($sql);
			while($row = $result->fetch_assoc()){
				if(isset($votes['vid'])){
					$votes['options'][]=array("bid"=>$row['bevoted_uid'], "name"=>$row['name'],"profile_image_url"=>$row['profile_img'],"describe"=>$row['describe'], "score"=>$row['score']);		
				}else {
					$votes=array("vid"=>$row['id'],"tittle"=>$row['tittle'],"uid"=>$row['uid'],"name"=>$row['cname'],"profile_image_url"=>$row['cprofile_img'],"mid"=>$row['mid'],"vote_type"=>$row['vote_type'],"template"=>$row['template'],"created_at"=>$row['created_at'],"options"=>array(),"totalscore"=>$row['totalscore']);
					$votes['options'][]=array("bid"=>$row['bevoted_uid'],"name"=>$row['name'], "profile_image_url"=>$row['profile_img'],"describe"=>$row['describe'], "score"=>$row['score']);					
				}
			}
			
			if($uid == null) { //判断是否已投票
				$votes['already_vote'] = 2;//未登陆情况
			}else {
				$alvarr = self::getAlreadyVotes($uid);
				if(in_array($uid, $alvarr)) {
					$votes['already_vote'] = 1;//已情况
				}else {
					$votes['already_vote'] = 0;//未登陆情况
				}
			}
			DB::close($mysqli,null,$result);
			return $votes;
			//select v.id,v.tittle,v.mid,v.vote_type,v.template,v.created_at,b.bevoted_uid,g.name,g.profile_img,b.describe,b.score,t.totalscore 
			//from (select sum(score) totalscore from be_voted b2 where b2.vid=1) t,vote v,be_voted b,guests g
			//where v.id=1 and v.id = b.vid and g.uid=b.bevoted_uid order by created_at desc;
		}
		
		static function getVoteCount($uid) {
			$vc = array();
			$sql = "select count(uid) from vote where uid = '{$uid}' ";
			$mysqli = DB::getConn();
			$result = $mysqli -> query($sql);
			$temp = $result->fetch_array();
			$vc['cvote_count']=$temp[0];
			
			$sql = "select count(uid) from voter where uid = '{$uid}' ";			
			$result = $mysqli -> query($sql);
			$temp = $result->fetch_array();
			$vc['vote_count']=$temp[0];
			DB::close($mysqli,null,$result);
			return $vc;
		}
				
		static function getTopTenCreate($uid=null){
			$top = array();
			$sql = " select u.uid,count(v.id) count,u.name from vote v,users u where u.uid=v.uid group by uid order by count desc limit 0,8";
			$mysqli = DB::getConn();
			$result = $mysqli->query($sql);
			while($row = $result->fetch_assoc()){
				$top[] = array("uid"=>$row['uid'], "name"=>$row['name'],"count"=>$row['count']);				
			}			
			DB::close($mysqli,null,$result);
			//echo "从数据库创建了topten";
			return $top;
		}
		
		static function getTopTenVote(){
			$top = array();
			$sql = "select v.uid, count(v.vid) count,u.name from voter v,users u where u.uid=v.uid group by uid order by count desc limit 0,10";
			$mysqli = DB::getConn();
			$result = $mysqli->query($sql);
			while($row = $result->fetch_assoc()){
				$top[] = array("uid"=>$row['uid'], "name"=>$row['name'],"count"=>$row['count']);				
			}
			DB::close($mysqli,null,$result);
			//echo "从数据库创建了topten";
			return $top;
		}
		
		private static function getAlreadyVotes($uid){
			$votes = array();
			$sql = "select vid from voter where uid ='{$uid}'";
			$mysqli = DB::getConn();
			$result = $mysqli->query($sql);
			while($row = $result->fetch_assoc()){
				$votes[] = $row['vid'];
			}
			DB::close($mysqli,null,$result);			
			return $votes;
		}
		
		static function getNewTenVote() {//拿出首页滚动最新十条投票
			$votes = array();
			$sql = "select v.id,v.tittle,v.uid,u.name cname, u.profile_img cprofile_img,v.mid,v.vote_type,v.template,v.created_at,b.bevoted_uid,g.name,g.profile_img,b.describe,b.score,t.totalscore totalscore 
						from 
						(select vid,sum(score) totalscore from be_voted b2 group by b2.vid) t,
						(select id,tittle,uid,mid,vote_type,template,created_at from vote order by created_at desc limit 0,10) v,
						be_voted b,guests g,users u
						where v.id = b.vid and v.id = t.vid and g.uid=b.bevoted_uid and u.uid =v.uid order by created_at desc";
			$mysqli = DB::getConn();
			$result = $mysqli->query($sql);
			while($row = $result->fetch_assoc()){
				if(isset($votes[$row['id']])){
					$votes[$row['id']]['options'][]=array("bid"=>$row['bevoted_uid'],"name"=>$row['name'],"profile_image_url"=>$row['profile_img'],"describe"=>$row['describe'], "score"=>$row['score']);		
				}else {
					$votes[$row['id']]=array("vid"=>$row['id'],"tittle"=>$row['tittle'],"uid"=>$row['uid'],"name"=>$row['cname'],"profile_image_url"=>$row['cprofile_img'],"mid"=>$row['mid'],"vote_type"=>$row['vote_type'],"template"=>$row['template'],"created_at"=>$row['created_at'],"options"=>array(),"totalscore"=>$row['totalscore']);
					$votes[$row['id']]['options'][]=array("bid"=>$row['bevoted_uid'],"name"=>$row['name'], "profile_image_url"=>$row['profile_img'],"describe"=>$row['describe'], "score"=>$row['score']);					
				}
			}
			$fixvotes = array();
			foreach($votes as $each) {
				$fixvotes[] = $each;
			}
			DB::close($mysqli,null,$result);
			return $fixvotes;
		}
		
		static function getPaticpateUIDS(array $friends){			
			$fstr = "";
			$fc = count($friends);
			for($i=0;$i<$fc;$i++) {
				if($i !=$fc-1) {
					$fstr.=$friends[$i].",";
				}else {
					$fstr.=$friends[$i];
				}
			}
			$votes = array();
			$sql = "select uid from vote where uid in({$fstr})";
			$mysqli = DB::getConn();
			$result = $mysqli->query($sql);
			DB::printError($mysqli);
			while($row = $result->fetch_assoc()){
				if(! in_array($row['uid'], $votes)){
					$votes[] = $row['uid'];
				}
			}
			DB::close($mysqli,null,$result);			
			return $votes;
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	