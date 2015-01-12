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
	
}

?>