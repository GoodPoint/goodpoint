<?php

include_once("../includes.php");

class ProfileModel /*extends BaseController */{

	public function profile($arrValues){
		$sid = $arrValues['sid'];
		$arrResult = array();
		$userID = ($sid != "")? DB::select("SELECT `To` FROM `messages` WHERE sid='".$sid."'")[0]->To : "";
		if($userID == ""){
			$arrResult['error'] = "error. no user specified";
			return $arrResult;
		}
		$profile_json = DB::select("SELECT profile_json FROM `user` WHERE id='".$userID."'")[0]->profile_json;
		$profile_arr = json_decode($profile_json, true);
		$returnArr = array("userID"=>$userID, "profile"=>$profile_arr, "sid"=>$sid);
		$arrResult['userID'] = $userID;
		$arrResult['profile'] = $profile_arr;
		$arrResult['sid'] = $sid;
		return $arrResult;
	}
	
	public function addProfile($arrValues){
		$arrResult = array();
		$sid = $arrValues['sid'];
		//profile info
		$name = $arrValues['name'];
		$age = $arrValues['age'];
		$gender = $arrValues['gender'];
		$userID = ($sid != "")? DB::select("SELECT `To` FROM `messages` WHERE sid='".$sid."'")[0]->To : "";
		if($userID == ""){
		//	return "{\"result\":\"error. no user specified\"}";
			$arrResult['error'] = "error. no user specified";
			return $arrResult;
		}
		$profile_json = DB::select("SELECT profile_json FROM `user` WHERE id='".$userID."'")[0]->profile_json;
		//convert to assoc array to add or update associative name/age/gender values
		$profile_arr = json_decode($profile_json, true);
		$profile_arr["name"] = $name;
		$profile_arr["age"] = $age;
		$profile_arr["gender"] = $gender;
		//re-encode with updated value and insert into db
		$profile_json = json_encode($profile_arr);
		$profile_update = DB::update("UPDATE `user` SET profile_json='".$profile_json."' WHERE id='".$userID."'");
		$arrResult['success'] = "success";
		return $arrResult;
//		return "{\"result\":\"success\"}";
	}
	
	public function uploadProfilePic($arrValues){
		$arrResult = array();
		$sid = $arrValues['sid'];
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
			
			$userID = ($sid != "")? DB::select("SELECT `To` FROM `messages` WHERE sid='".$sid."'")[0]->To : "";
			if($userID == ""){
				return "{\"result\":\"error. no user specified\"}";
			}
			$filename = $new_name . "." . $ext;		
			$profile_json = DB::select("SELECT profile_json FROM `user` WHERE id='".$userID."'")[0]->profile_json;
			//convert to assoc array to add or update associative pic value
			$profile_arr = json_decode($profile_json, true);
			$profile_arr["pic"] = $filename;
			//re-encode with updated value and insert into db
			$profile_json = json_encode($profile_arr);
			$profile_update = DB::update("UPDATE `user` SET profile_json='".$profile_json."' WHERE id='".$userID."'");
			$arrResult['success'] = "success";
			//echo 'File is uploaded successfully.';
		} catch (RuntimeException $e) {
		//	return "{\"result\":\"".$e->getMessage()."\"}";
			$arrResult['error'] = $e->getMessage();
		}
		return $arrResult;
	}
}

?>
