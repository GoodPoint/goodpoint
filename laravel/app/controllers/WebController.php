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
	
	public function GPinfo(){
		$phone = $_REQUEST['phone'];
		return Queries::getGPinfo($phone);
	}
	
}

?>