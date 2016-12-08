<?php

class TycController extends BaseController {
	
	private $tycModel;
	
	public function __construct() {
		$this->tycModel = new TycModel();
		
	}
	
	public function login(){
		return json_encode($this->tycModel->login());
	}
	
	public function signup(){
		return json_encode($this->tycModel->signup());
	}
	
	public function forgot(){
		return json_encode($this->tycModel->forgot());
	}
	
	public function submitCardID(){
		return json_encode($this->tycModel->submitCardID());
	}

}

?>