<?php

class TransactionModel /*extends BaseController */ {
	
	public function getMyTransactions($arrValues){
		$sid = $arrValues['sid'];
		$arrResult = array();
		$userID = ($sid != "")? DB::select("SELECT `To` FROM `messages` WHERE sid='".$sid."'")[0]->To : "";
		if($userID == ""){
			$arrResult['error'] = "error. no user specified";
			return $arrResult;
		}
		//$type == "Card" or "User"
		$transactions = ($type=="Card")? DB::select(self::getTransactionsForBarcode($id)):DB::select("SELECT * FROM transaction WHERE giver ='".$id."' OR receiver='".$id."' OR giver ='+".$id."' OR receiver='+".$id."' OR giver ='+1".$id."' OR receiver='+1".$id."' ORDER BY timestamp desc");
		$givers = array(); 
		$receivers = array();
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
		$arrResult['transactions'] = $transactions;
		$arrResult['givers'] = $givers;
		$arrResult['receivers'] = $receivers;
		return $arrResult;
	}
	
	public function getLatestTransactions(){
		$transactions = DB::select("SELECT * FROM transaction ORDER BY timestamp DESC LIMIT 100");
		$givers = array(); 
		$receivers = array();
		$arrResult = array();
		foreach($transactions as $transaction){
			//get u1 name
			if(!is_numeric($transaction->giver)){
				 $name1 = $transaction->giver; 
			} 
			else {
				$user1 = json_decode(DB::select("SELECT profile_json FROM user WHERE id=".$transaction->giver)[0]->profile_json,true);
				if(isset($user1["name"])){ 
					$name1 = $user1["name"]; 
				}
				 else { 
					$name1 = "Anonymous"; 
				}
			}
			//get u2 name
			if(!is_numeric($transaction->receiver)){ 
				$name1 = $transaction->receiver; 
			} 
			else {
				$user2 = json_decode(DB::select("SELECT profile_json FROM user WHERE id=".$transaction->receiver)[0]->profile_json,true);
				if(isset($user2["name"])){ 
					$name2 = $user2["name"]; 
				} 
				else { 
					$name2 = "Anonymous"; 
				}
			}
			$givers[] = $name1;
			$receivers[] = $name2;
		}
		$arrResult['transactions'] = $transactions;
		$arrResult['givers'] = $givers;
		$arrResult['receivers'] = $receivers;
		return $arrResult;
	}
	
	public static function getTransactionsById($arrValues){
		//$type == "Card" or "User"
		$id = $arrValues['id'];
		$type = $arrValues['type'];
		$arrResult = array();
		$transactions = ($type=="Card")? DB::select(self::getTransactionsForBarcode($id)):DB::select("SELECT * FROM transaction WHERE giver ='".$id."' OR receiver='".$id."' OR giver ='+".$id."' OR receiver='+".$id."' OR giver ='+1".$id."' OR receiver='+1".$id."' ORDER BY timestamp desc");
		$givers = array();
		$receivers = array();
		foreach($transactions as $transaction){
			//get u1 name
			if(!is_numeric($transaction->giver)){
				$name1 = $transaction->giver; 
			} 
			else {
				$user1 = json_decode(DB::select("SELECT profile_json FROM user WHERE id=".$transaction->giver)[0]->profile_json,true);
				if(isset($user1["name"])){ 
					$name1 = $user1["name"]; 
				}
				 else { 
					$name1 = "Anonymous"; 
				}
			}
			//get u2 name
			if(!is_numeric($transaction->receiver)){ 
				$name1 = $transaction->receiver; 
			} 
			else {
				$user2 = json_decode(DB::select("SELECT profile_json FROM user WHERE id=".$transaction->receiver)[0]->profile_json,true);
				if(isset($user2["name"])){ 
					$name2 = $user2["name"]; 
				}
				else { 
					$name2 = "Anonymous"; 
				}
			}
			$givers[] = $name1;
			$receivers[] = $name2;
		}
		$arrResult['transactions'] = $transactions;
		$arrResult['givers'] = $givers;
		$arrResult['receivers'] = $receivers;
		return $arrResult;
	}
/*	
	public function getTransactionsByCardId(){
		$arrResult = array();
		return Queries::getTransactionsById($_REQUEST['id'],$_REQUEST['type']);
	}
	
	public function getTransactionsByPhoneId(){
		$phone = $_REQUEST['id']; $pattern = '/[^\d]/'; $replacement = '';
		$phone = preg_replace($pattern, $replacement, $phone);
		return Queries::getTransactionsById($phone,$_REQUEST['type']);
	}
*/	
	public function getTransactionInfo($arrValues){
		$tid = $arrValues['id'];
		$sid = $arrValues['sid'];
		$userID = ($sid != "" && $sid != "null")? DB::select("SELECT `To` FROM `messages` WHERE sid='". $sid ."'")[0]->To : "";
		$arrResult = array();
		//get media + transaction 
		$media = DB::select("SELECT url,caption FROM media WHERE trans_id=".$tid); 
		$transaction = DB::select("SELECT * FROM transaction WHERE id=".$tid);
		//get u1 name
		if(!is_numeric($transaction[0]->giver)){ 
			$name1 = $transaction[0]->giver; 
		} 
		else {
			$user1 = json_decode(DB::select("SELECT profile_json FROM user WHERE id=".$transaction[0]->giver)[0]->profile_json,true);
			if(isset($user1["name"])){ 
				$name1 = $user1["name"]; 
			} 
			else { 
				$name1 = "Anonymous"; 
			}
		}
		//get u2 name
		if(!is_numeric($transaction[0]->receiver)){
			 $name1 = $transaction[0]->receiver; 
		} 
		else {
			$user2 = json_decode(DB::select("SELECT profile_json FROM user WHERE id=".$transaction[0]->receiver)[0]->profile_json,true);
			if(isset($user2["name"])){ 
				$name2 = $user2["name"]; 
			} 
			else { 
				$name2 = "Anonymous"; 
			}
		}
		//get edit status
		if($uid == ""){
			$can_edit = false;
		}
		else {
			$can_edit = ($transaction[0]->giver == $uid || $transaction[0]->receiver == $uid);
		}
		//compile + return
		$returnArr = array("media"=>$media, "transaction"=>$transaction, "can_edit"=>$can_edit, "n1"=>$name1, "n2"=>$name2);
		$arrResult['media'] = $media;
		$arrResult['transaction'] = $transaction;
		$arrResult['can_edit'] = $can_edit;
		$arrResult['n1'] = $name1;
		$arrResult['n2'] = $name2;
		return $arrResult;	
//		return Queries::getTransactionDetails($_REQUEST['id'],$userID);
	}
	
	public function uploadMedia($arrValues){
		$arrResult = array();
		$tid = $arrValuues['tid'];
		$caption = $arrValues['caption'];
		try {
			$tid = $_REQUEST['tid'];
			$caption = $_REQUEST['caption'];
			// Undefined | Multiple Files | $_FILES Corruption Attack
			// If this request falls under any of them, treat it invalid.
			if (
				!isset($_FILES['media_pic']['error']) ||
				is_array($_FILES['media_pic']['error'])
			) {
				throw new RuntimeException('Invalid parameters.');
			}
			// Check $_FILES['media_pic']['error'] value.
			switch ($_FILES['media_pic']['error']) {
				case UPLOAD_ERR_OK:
					break;
				case UPLOAD_ERR_NO_FILE:
					throw new RuntimeException('No file sent.');
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					throw new RuntimeException('Exceeded filesize limit.');
				default:
					throw new RuntimeException('Unknown errors.');
			}
			// You should also check filesize here. 
			if ($_FILES['media_pic']['size'] > 1000000) {
				throw new RuntimeException('Exceeded filesize limit.');
			}
			// DO NOT TRUST $_FILES['media_pic']['mime'] VALUE !!
			// Check MIME Type by yourself.
			$finfo = new finfo(FILEINFO_MIME_TYPE);
			if (false === $ext = array_search(
				$finfo->file($_FILES['media_pic']['tmp_name']),
				array(
					'jpg' => 'image/jpeg',
					'png' => 'image/png',
					'gif' => 'image/gif',
				),
				true
			)) {
				throw new RuntimeException('Invalid file format.');
			}
			// You should name it uniquely.
			// DO NOT USE $_FILES['media_pic']['name'] WITHOUT ANY VALIDATION !!
			// On this example, obtain safe unique name from its binary data.
			$new_name = sha1_file($_FILES['media_pic']['tmp_name']);
			if (!move_uploaded_file(
				$_FILES['media_pic']['tmp_name'],
				sprintf('%s/winwin/laravel/public/web/uploads/%s.%s',
					$_SERVER['DOCUMENT_ROOT'],
					$new_name,
					$ext
				)
			)) {
				throw new RuntimeException('Failed to move uploaded file.');
			}
			$filename = $new_name . "." . $ext;		
			$url = "http://54.149.200.91/winwin/laravel/public/web/uploads/".$filename;
			$arrResult['result'] = DB::insert("INSERT INTO `media` (sid,trans_id,url,caption) VALUES ('app_upload','".$tid."','".$url."','".$caption."')");
			//echo 'File is uploaded successfully.';
		} catch (RuntimeException $e) {
			$arrResult['error'] = $e->getMessage();
		//	return "{\"result\":\"".$e->getMessage()."\"}";
		}
		return $arrResult;
	}
	
	public function uploadFirstMedia($arrValues){
		$sid = $arrValues['sid'];
		$caption = $arrValues['caption'];
		$arrResult = array();
		try {
			// Undefined | Multiple Files | $_FILES Corruption Attack
			// If this request falls under any of them, treat it invalid.
			if (
				!isset($_FILES['profile_pic']['error']) ||
				is_array($_FILES['profile_pic']['error'])
			) {
				throw new RuntimeException('Invalid parameters.');
			}

			// Check $_FILES['profile_pic']['error'] value.
			switch ($_FILES['profile_pic']['error']) {
				case UPLOAD_ERR_OK:
					break;
				case UPLOAD_ERR_NO_FILE:
					throw new RuntimeException('No file sent.');
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					throw new RuntimeException('Exceeded filesize limit.');
				default:
					throw new RuntimeException('Unknown errors.');
			}

			// You should also check filesize here. 
			if ($_FILES['profile_pic']['size'] > 10000000) {
				throw new RuntimeException('Exceeded filesize limit.');
			}

			// DO NOT TRUST $_FILES['profile_pic']['mime'] VALUE !!
			// Check MIME Type by yourself.
			$finfo = new finfo(FILEINFO_MIME_TYPE);
			if (false === $ext = array_search(
				$finfo->file($_FILES['profile_pic']['tmp_name']),
				array(
					'jpg' => 'image/jpeg',
					'png' => 'image/png',
					'gif' => 'image/gif',
				),
				true
			)) {
				throw new RuntimeException('Invalid file format.');
			}

			// You should name it uniquely.
			// DO NOT USE $_FILES['profile_pic']['name'] WITHOUT ANY VALIDATION !!
			// On this example, obtain safe unique name from its binary data.
			$new_name = sha1_file($_FILES['profile_pic']['tmp_name']);
			if (!move_uploaded_file(
				$_FILES['profile_pic']['tmp_name'],
				sprintf('%s/winwin/laravel/public/web/uploads/%s.%s',
					$_SERVER['DOCUMENT_ROOT'],
					$new_name,
					$ext
				)
			)) {
				throw new RuntimeException('Failed to move uploaded file.');
			}
			$filename = $new_name . "." . $ext;		
	//		$success = Queries::appInsertFirstMedia($new_name.".".$ext, $sid, $caption);
			$url = "http://54.149.200.91/winwin/laravel/public/web/uploads/".$filename;
			$tid = DB::select("SELECT t.id FROM `transaction` as t INNER JOIN messages as m ON m.cardid=t.cardid WHERE m.sid='".$sid."' and m.To = t.receiver")[0]->id;
			$insert = DB::insert("INSERT INTO `media` (sid,trans_id,url,caption) VALUES ('app_upload','".$tid."','".$url."','".$caption."')");
//      	return array("tid"=>$tid,"insert"=>$insert);
			DB::insert("insert into fuckedup(value) VALUES('".json_encode($success)."')");
//			return "{\"result\":\"success\",\"queryResults\":".json_encode($success)."}";
			//echo 'File is uploaded successfully.';
			$arrResult['tid'] = $tid;
			$arrResult['insert'] = $insert;
		} catch (RuntimeException $e) {
	//		return "{\"result\":\"".$e->getMessage()."\"}";
			$arrResult['error'] = $e->getMessage();
		}
		return $arrResult;
	}
}

?>
