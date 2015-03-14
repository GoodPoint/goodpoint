<?php

include_once("includes.php");

class WebController extends BaseController {

	public function hello(){
		return Strings::hello();
	}
	
	public function leaderboard(){
		$sid = $_REQUEST['sid'];
		$event = $_REQUEST['event'];
		if($event == ""){
			return Queries::getLeaderboard($sid);
		}
		switch($event){
			case "letskeepbuilding": return Queries:getLeaderboardByDate("2015-03-14", "2015-03-16");
			default: return "{\"result\":\"invalid event\"}";
		}
	}
	
	public function GPinfo(){
		$phone = $_REQUEST['phone'];
		return Queries::getGPinfo($phone);
	}
	
}

?>