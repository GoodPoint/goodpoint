<?php

include_once("includes.php");

class TransactionController extends BaseController {
	public function getMyTransactions(){
		$sid = $_REQUEST['sid'];
		$userID = ($sid != "")? DB::select("SELECT `To` FROM `messages` WHERE sid='".$sid."'")[0]->To : "";
		if($userID == ""){return "{\"result\":\"error. no user specified\"}";}
		return Queries::getTransactionsById($userID,"Phone");
	}
	
	public function getLatestTransactinos(){
		return Queries::getLatestTransactions();
	}
	
	public function getTransactionsByCardId(){
		return Queries::getTransactionsById($_REQUEST['id'],$_REQUEST['type']);
	}
	
	public function getTransactionsByPhoneId(){
		return Queries::getTransactionsById($_REQUEST['id'],$_REQUEST['type']);
	}
	
	public function getTransactionInfo(){
		$userID = ($_REQUEST['sid'] != "" && $_REQUEST['sid'] != "null")? DB::select("SELECT `To` FROM `messages` WHERE sid='".$_REQUEST['sid']."'")[0]->To : "";
		return Queries::getTransactionDetails($_REQUEST['id'],$userID);
	}
	
	public function uploadMedia(){
		try {
			$tid = $_REQUEST['tid'];
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
			
			$success = Queries::appInsertMedia($new_name.".".$ext, $tid);
			return "{\"result\":\"success\"}";
			//echo 'File is uploaded successfully.';

		} catch (RuntimeException $e) {

			return "{\"result\":\"".$e->getMessage()."\"}";

		}

	}
	
	public function uploadFirstMedia(){
		try {
			$sid = $_REQUEST['sid'];
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
			if ($_FILES['profile_pic']['size'] > 1000000) {
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
			
			$success = Queries::appInsertFirstMedia($new_name.".".$ext, $sid);
			DB::insert("insert into fuckedup(value) VALUES('".json_encode($success)."')");
			return "{\"result\":\"success\",\"queryResults\":".json_encode($success)."}";
			//echo 'File is uploaded successfully.';

		} catch (RuntimeException $e) {

			return "{\"result\":\"".$e->getMessage()."\"}";

		}

	}
}

?>