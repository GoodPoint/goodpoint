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
	public static function recordMsg($user, $message, $step, $cardid, $sid){ 
		return "INSERT INTO `messages` (`To`,`msg`,`step`,`cardid`,`sid`) VALUES ('".$user."','".$message."','".$step."','".$cardid."','".$sid."')"; 
	}//same vars for both
	public static function getCardById($cardid){ 
		return "SELECT * FROM cards WHERE barcode_id = ".$cardid; 
	}
}

?>