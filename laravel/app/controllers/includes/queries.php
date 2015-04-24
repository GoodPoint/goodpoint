<?php

class Queries {
	public static function getMessagesForUser($user){ 
		return "SELECT * FROM `messages` WHERE `To`='".$user."' ORDER BY time desc"; 
	}
	public static function updateOwner($user, $barcode_id){ 
		return "UPDATE cards SET owner = '".$user."' WHERE barcode_id = ".$barcode_id; 
	}
	public static function insertTransaction($cardid, $receiver, $giver){ 
		return "INSERT INTO transaction (cardid,receiver,giver) VALUES ('".$cardid."','".$receiver."','".$giver."')"; 
	}
	public static function getTransactionsForBarcode($barcode_id){ 
		return "SELECT * FROM transaction WHERE cardid=".$barcode_id." ORDER BY timestamp desc"; 
	}
	public static function insertMedia($sid, $trans_id, $url, $caption){ 
		return "INSERT INTO media (sid, trans_id, url, caption) VALUES ('".$sid."','".$trans_id."','".$url."','".$caption."')"; 
	}
	public static function recordMsg($user, $message, $step, $cardid, $sid, $ab){ 
		return "INSERT INTO `messages` (`To`,`msg`,`step`,`cardid`,`sid`,`ab`) VALUES ('".$user."','".$message."','".$step."','".$cardid."','".$sid."','".$ab."')"; 
	}//same vars for both
	public static function getCardById($cardid){ 
		return "SELECT * FROM cards WHERE barcode_id = ".$cardid; 
	}
	public static function initUser($number){
		$check = DB::select("SELECT count(*) as count FROM `user` WHERE `id`='".$number."'");
		if($check[0]->count == 0){
			return DB::insert("INSERT INTO `user` (`id`, `profile_json`, `last_updated`) VALUES ('".$number."', '{}', CURRENT_TIMESTAMP)");
		}
	}
	public static function checkOwner($potential_owner, $barcode_id){
		/*$select = DB::select("SELECT * FROM `transaction` where cardid=".$barcode_id." order by timestamp desc limit 2");
		for($i=0; $i<count($select); $i++){
			if($select[$i]->giver == $potential_owner || $select[$i]->receiver == $potential_owner){
				return true;
			}
		}*/
		//alpha
		$select = DB::select("SELECT * FROM `transaction` where cardid=".$barcode_id." order by timestamp desc limit 2");
		for($i=0; $i<count($select); $i++){
			if($select[$i]->giver == $potential_owner || $select[$i]->receiver == $potential_owner){
				return true;
			}
		}
		return false;
	}
	public static function getGPinfo($phone){
		$res = DB::select("SELECT (SELECT COUNT(`transaction`.id) FROM transaction WHERE transaction.giver = user.id OR transaction.receiver = user.id) as `GoodPoints`, profile_json FROM user where id='".$phone."'");
		return json_encode($res);
	}
	public static function getLeaderboard($sid){
		$userID = ($sid != "")? DB::select("SELECT `To` FROM `messages` WHERE sid='".$sid."'")[0]->To : "";
		$leaderboard = DB::select("SELECT user.id, (SELECT COUNT(`transaction`.id) FROM transaction WHERE transaction.giver = user.id OR transaction.receiver = user.id) as `GoodPoints`, user.profile_json FROM user ORDER BY `GoodPoints` DESC");
		$returnArr = array("userID"=>$userID, "leaderboard"=>$leaderboard, "sid"=>$sid);
		return json_encode($returnArr);
	}
	public static function getGPForUser($phone){
		$res = DB::select("SELECT (SELECT COUNT(`transaction`.id) FROM transaction WHERE transaction.giver = user.id OR transaction.receiver = user.id) as `GoodPoints` FROM user where id='".$phone."'");
		if(count($res) == 0){return 0;}
		return $res[0]->GoodPoints;
	}
	public static function getLeaderboardByDate($date, $endDate){
		//$dateClause = "(WHERE transaction.timestamp > '".$date."' AND transaction.timestamp < '".$endDate."'";
		$leaderboard = DB::select("SELECT user.id, (SELECT COUNT(`transaction`.id) FROM transaction WHERE (transaction.giver = user.id OR transaction.receiver = user.id) AND transaction.timestamp > '".$date."' AND transaction.timestamp < '".$endDate."') as `GoodPoints`, user.profile_json FROM user ORDER BY `GoodPoints` DESC");
		$returnArr = array("userID"=>0, "leaderboard"=>$leaderboard, "sid"=>0);
		return json_encode($returnArr);
	}
	public static function getProfile($sid){
		$userID = ($sid != "")? DB::select("SELECT `To` FROM `messages` WHERE sid='".$sid."'")[0]->To : "";
		if($userID == ""){return "{\"result\":\"error. no user specified\"}";}
		$profile_json = DB::select("SELECT profile_json FROM `user` WHERE id='".$userID."'")[0]->profile_json;
		$profile_arr = json_decode($profile_json, true);
		$returnArr = array("userID"=>$userID, "profile"=>$profile_arr, "sid"=>$sid);
		return json_encode($returnArr);
	}
	public static function addProfile($sid, $name, $age, $gender){
		$userID = ($sid != "")? DB::select("SELECT `To` FROM `messages` WHERE sid='".$sid."'")[0]->To : "";
		if($userID == ""){return "{\"result\":\"error. no user specified\"}";}
		$profile_json = DB::select("SELECT profile_json FROM `user` WHERE id='".$userID."'")[0]->profile_json;
		//convert to assoc array to add or update associative name/age/gender values
		$profile_arr = json_decode($profile_json, true);
		$profile_arr["name"] = $name;
		$profile_arr["age"] = $age;
		$profile_arr["gender"] = $gender;
		//re-encode with updated value and insert into db
		$profile_json = json_encode($profile_arr);
		$profile_update = DB::update("UPDATE `user` SET profile_json='".$profile_json."' WHERE id='".$userID."'");
		return true;
	}
	public static function profilePicJSON($filename,$sid){
		$userID = ($sid != "")? DB::select("SELECT `To` FROM `messages` WHERE sid='".$sid."'")[0]->To : "";
		if($userID == ""){return "{\"result\":\"error. no user specified\"}";}
		$profile_json = DB::select("SELECT profile_json FROM `user` WHERE id='".$userID."'")[0]->profile_json;
		//convert to assoc array to add or update associative pic value
		$profile_arr = json_decode($profile_json, true);
		$profile_arr["pic"] = $filename;
		//re-encode with updated value and insert into db
		$profile_json = json_encode($profile_arr);
		$profile_update = DB::update("UPDATE `user` SET profile_json='".$profile_json."' WHERE id='".$userID."'");
		return true;
	}
	public static function appInsertMedia($filename, $tid, $caption){
		$url = "http://54.149.200.91/winwin/laravel/public/web/uploads/".$filename;
		$insert = DB::insert("INSERT INTO `media` (sid,trans_id,url,caption) VALUES ('app_upload','".$tid."','".$url."','".$caption."')");
	}
	public static function appInsertFirstMedia($filename, $sid, $caption){
		$url = "http://54.149.200.91/winwin/laravel/public/web/uploads/".$filename;
		$tid = DB::select("SELECT t.id FROM `transaction` as t INNER JOIN messages as m ON m.cardid=t.cardid WHERE m.sid='".$sid."' and m.To = t.receiver")[0]->id;
		$insert = DB::insert("INSERT INTO `media` (sid,trans_id,url,caption) VALUES ('app_upload','".$tid."','".$url."','".$caption."')");
		return array("tid"=>$tid,"insert"=>$insert);
	}
	public static function getLatestTransactions(){
		$transactions = DB::select("SELECT * FROM transaction ORDER BY timestamp DESC LIMIT 100");
		$givers = array(); $receivers = array();
		foreach($transactions as $transaction){
			//get u1 name
			if(!is_numeric($transaction->giver)){ $name1 = $transaction->giver; } else {
				$user1 = json_decode(DB::select("SELECT profile_json FROM user WHERE id=".$transaction->giver)[0]->profile_json,true);
				if(isset($user1["name"])){ $name1 = $user1["name"]; } else { $name1 = "Anonymous"; }
			}
			//get u2 name
			if(!is_numeric($transaction->receiver)){ $name1 = $transaction->receiver; } else {
				$user2 = json_decode(DB::select("SELECT profile_json FROM user WHERE id=".$transaction->receiver)[0]->profile_json,true);
				if(isset($user2["name"])){ $name2 = $user2["name"]; } else { $name2 = "Anonymous"; }
			}
			$givers[] = $name1;
			$receivers[] = $name2;
		}
		return json_encode(array("transactions"=>$transactions,"givers"=>$givers,"receivers"=>$receivers));
	}
	public static function getTransactionsById($id, $type){
		//$type == "Card" or "User"
		$transactions = ($type=="Card")? DB::select(self::getTransactionsForBarcode($id)):DB::select("SELECT * FROM transaction WHERE giver ='".$id."' OR receiver='".$id."' OR giver ='+".$id."' OR receiver='+".$id."' OR giver ='+1".$id."' OR receiver='+1".$id."' ORDER BY timestamp desc");
		$givers = array(); $receivers = array();
		foreach($transactions as $transaction){
			//get u1 name
			if(!is_numeric($transaction->giver)){ $name1 = $transaction->giver; } else {
				$user1 = json_decode(DB::select("SELECT profile_json FROM user WHERE id=".$transaction->giver)[0]->profile_json,true);
				if(isset($user1["name"])){ $name1 = $user1["name"]; } else { $name1 = "Anonymous"; }
			}
			//get u2 name
			if(!is_numeric($transaction->receiver)){ $name1 = $transaction->receiver; } else {
				$user2 = json_decode(DB::select("SELECT profile_json FROM user WHERE id=".$transaction->receiver)[0]->profile_json,true);
				if(isset($user2["name"])){ $name2 = $user2["name"]; } else { $name2 = "Anonymous"; }
			}
			$givers[] = $name1;
			$receivers[] = $name2;
		}
		return json_encode(array("transactions"=>$transactions,"givers"=>$givers,"receivers"=>$receivers));
	}
	public static function getTransactionDetails($tid, $uid){
		//get media + transaction 
		$media = DB::select("SELECT url,caption FROM media WHERE trans_id=".$tid); 
		$transaction = DB::select("SELECT * FROM transaction WHERE id=".$tid);
		//get u1 name
		if(!is_numeric($transaction[0]->giver)){ $name1 = $transaction[0]->giver; } else {
			$user1 = json_decode(DB::select("SELECT profile_json FROM user WHERE id=".$transaction[0]->giver)[0]->profile_json,true);
			if(isset($user1["name"])){ $name1 = $user1["name"]; } else { $name1 = "Anonymous"; }
		}
		//get u2 name
		if(!is_numeric($transaction[0]->receiver)){ $name1 = $transaction[0]->receiver; } else {
			$user2 = json_decode(DB::select("SELECT profile_json FROM user WHERE id=".$transaction[0]->receiver)[0]->profile_json,true);
			if(isset($user2["name"])){ $name2 = $user2["name"]; } else { $name2 = "Anonymous"; }
		}
		//get edit status
		if($uid == ""){
			$can_edit = false;
		} else {
			$can_edit = ($transaction[0]->giver == $uid || $transaction[0]->receiver == $uid);
		}
		//compile + return
		$returnArr = array("media"=>$media, "transaction"=>$transaction, "can_edit"=>$can_edit, "n1"=>$name1, "n2"=>$name2);
		return json_encode($returnArr);
	}
}

?>