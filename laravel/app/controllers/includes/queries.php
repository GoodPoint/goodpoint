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
		$select = DB::select("SELECT count(*) as count FROM cards WHERE owner='".$potential_owner."' AND barcode_id = ".$barcode_id);
		if($select[0]->count > 0){ return true; }
		return false;
	}
	public static function getLeaderboard($sid){
		$userID = ($sid != "")? DB::select("SELECT `To` FROM `messages` WHERE sid='".$sid."'")[0]->To : "";
		$leaderboard = DB::select("SELECT user.id, (SELECT COUNT(`transaction`.id) FROM transaction WHERE transaction.giver = user.id OR transaction.receiver = user.id) as `GoodPoints` FROM user");
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
	public static function getTransactionsById($id, $type){
		//$type == "Card" or "User"
		$return = ($type=="Card")? DB::select(self::getTransactionsForBarcode($id)):DB::select("SELECT * FROM transaction WHERE giver ='".$id."' OR receiver='".$id."' ORDER BY timestamp desc");
		return json_encode($return);
	}
	public static function getTransactionDetails($tid){
		$media = DB::select("SELECT url FROM media WHERE trans_id=".$tid); 
		$transaction = DB::select("SELECT * FROM transaction WHERE id=".$tid);
		$returnArr = array("media"=>$media, "transaction"=>$transaction);
		return json_encode($returnArr);
	}
}

?>