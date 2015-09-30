<?php

include_once("../includes.php");

class WebModel /*extends BaseController*/ {

	public function hello(){
		return Strings::hello();
	}
	
	public function leaderboard($arrValues){
		$sid = $arrValues['sid'];
		$event = $arrValues['event'];
		$arrResult = array();
		if($event == ""){
			return ($this->getLeaderboard($sid));
		}
		switch($event){
			case "letskeepbuilding": 
				$startDate = "2015-03-14";
				$endDate = "2015-03-16";
				return ($this->getLeaderboardByDate($startDate, $endDate));
				break;
			default: 
			$arrResult['error'] = "invalid event";
			break;
		}
		return $arrResult;
	}
	
	public function GPinfo($phone){
		$result = DB::select("SELECT (SELECT COUNT(`transaction`.id) FROM transaction WHERE transaction.giver = user.id OR transaction.receiver = user.id) as `GoodPoints`, profile_json FROM user where id='".$phone."'");
		return $result;
	}
	
	private function getLeaderboard($sid) {
		$arrResult = array();
		$userID = ($sid != "")? DB::select("SELECT `To` FROM `messages` WHERE sid='".$sid."'")[0]->To : "";
		$leaderboard = DB::select("SELECT user.id, (SELECT COUNT(`transaction`.id) FROM transaction WHERE transaction.giver = user.id OR transaction.receiver = user.id) as `GoodPoints`, user.profile_json FROM user ORDER BY `GoodPoints` DESC");
		$arrResult['userID'] = $userID;
		$arrResult['leaderboard'] = $leaderboard;
		$arrResult['sid'] = $sid;
		return $arrResult;
	}
	
	private function getLeaderboardByDate($startDate, $endDate) {
		$arrResult = array();
		//$dateClause = "(WHERE transaction.timestamp > '".$date."' AND transaction.timestamp < '".$endDate."'";
		$leaderboard = DB::select("SELECT user.id, (SELECT COUNT(`transaction`.id) FROM transaction WHERE (transaction.giver = user.id OR transaction.receiver = user.id) AND transaction.timestamp > '".$startDate."' AND transaction.timestamp < '".$endDate."') as `GoodPoints`, user.profile_json FROM user ORDER BY `GoodPoints` DESC");
		$returnArr = array("userID"=>0, "leaderboard"=>$leaderboard, "sid"=>0);
		$arrResult['userID'] = 0;
		$arrResult['leaderboad'] = $leaderboard;
		$arrResult['sid'] = 0;
		return $arrResult;
	}
}

?>
