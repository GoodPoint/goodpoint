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
	public static function insertMedia($sid, $trans_id, $url){ 
		return "INSERT INTO media (sid, trans_id, url) VALUES ('".$sid."','".$trans_id."','".$url."')"; 
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
		$select = DB::select("SELECT * FROM `transaction` where cardid=".$barcode_id." order by timestamp desc limit 1");
		for($i=0; $i<count($select); $i++){
			if($select[$i]->giver == $potential_owner || $select[$i]->receiver == $potential_owner){
				return true;
			}
		}
		return false;
	}
	public static function getLeaderboard($sid){
		$userID = ($sid != "")? DB::select("SELECT `To` FROM `messages` WHERE sid='".$sid."'")[0]->To : "";
		$leaderboard = DB::select("SELECT user.id, (SELECT COUNT(`transaction`.id) FROM transaction WHERE transaction.giver = user.id OR transaction.receiver = user.id) as `GoodPoints` FROM user ORDER BY `GoodPoints` DESC");
		$returnArr = array("userID"=>$userID, "leaderboard"=>$leaderboard, "sid"=>$sid);
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
	public static function appInsertMedia($filename, $tid){
		$url = "http://54.149.200.91/winwin/laravel/public/web/uploads/"+$filename;
		$insert = DB::insert("INSERT INTO `media` (sid,trans_id,url) VALUES ('app_upload','".$tid."','".$url."')");
	}
	public static function getLatestTransactions(){
		return json_encode(DB::select("SELECT * FROM transaction ORDER BY timestamp DESC LIMIT 100"));
	}
	public static function getTransactionsById($id, $type){
		//$type == "Card" or "User"
		$return = ($type=="Card")? DB::select(self::getTransactionsForBarcode($id)):DB::select("SELECT * FROM transaction WHERE giver ='".$id."' OR receiver='".$id."' OR giver ='+".$id."' OR receiver='+".$id."' OR giver ='+1".$id."' OR receiver='+1".$id."' ORDER BY timestamp desc");
		return json_encode($return);
	}
	public static function getTransactionDetails($tid, $uid){
		$media = DB::select("SELECT url FROM media WHERE trans_id=".$tid); 
		$transaction = DB::select("SELECT * FROM transaction WHERE id=".$tid);
		if($uid == ""){
			$can_edit = false;
		} else {
			$can_edit = ($transaction->giver == $uid || $transaction->receiver == $uid);
		}
		$returnArr = array("media"=>$media, "transaction"=>$transaction, "can_edit"=>$can_edit);
		return json_encode($returnArr);
	}
}

?>