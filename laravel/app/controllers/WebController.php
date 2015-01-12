<?php

include_once("includes.php");

class WebController extends BaseController {

	public function hello(){
		return Strings::hello();
	}
	
	public function leaderboard(){
		$sid = $_REQUEST['sid'];
		return Queries::getLeaderboard($sid);
	}
	
	public function profile(){
		$sid = $_REQUEST['sid'];
		return Queries::getProfile($sid);
	}
	
	public function uploadProfilePic(){
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
			if (!move_uploaded_file(
				$_FILES['profile_pic']['tmp_name'],
				sprintf('../../web/uploads/%s.%s',
					sha1_file($_FILES['profile_pic']['tmp_name']),
					$ext
				)
			)) {
				throw new RuntimeException('Failed to move uploaded file.');
			}
			
			return "{\"result\":\"success\"}";
			//echo 'File is uploaded successfully.';

		} catch (RuntimeException $e) {

			return "{\"result\":\"".$e->getMessage()."\"}";

		}

	}
}

?>