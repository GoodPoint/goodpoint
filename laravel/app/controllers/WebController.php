<?php

class WebController extends BaseController {

	private $webModel;
	
	public function __construct() {
		$this->webModel = new WebModel();
	}

	public function hello(){
		return Strings::hello();
	}
	
	public function leaderboard(){
		$arrValues = array();
		$arrValues['sid'] = $_REQUEST['sid'];
		$arrValues['event'] = $_REQUEST['event'];
		return json_encode($this->webModel->leaderboard($arrValues));
	}
	
	public function GPinfo(){
		$phone = $_REQUEST['phone'];
		return json_encode($this->webModel->GPinfo($phone));
	}
	
}

?>
