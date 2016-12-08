<?php

class ProfileController extends BaseController {

	private $profileModel;
	
	public function __construct() {
		$this->profileModel = new ProfileModel();
	}


	public function profile(){
		$arrValues = array();
		$arrValues['sid'] = $_REQUEST['sid'];
		return json_encode($this->profileModel->profile($arrValues));
	}
	
	public function addProfile(){
		$arrValues = array();
		$arrValues['sid'] = $_REQUEST['sid'];
		$arrValues['name'] = $_REQUEST['name'];
		$arrValues['age'] = $_REQUEST['age'];
		$arrValues['gender'] = $_REQUEST['gender'];
		return json_encode($this->profileModel->addProfile($arrValues));
	}
	
	public function uploadProfilePic(){
		$arrValues = array();
		$arrValues['sid'] = $_REQUEST['sid'];
		return json_encode($this->profileModel->uploadProfilePic($arrValues));
	}
	
	public function getLevel(){
		$userid = $_REQUEST['userid'];
		return json_encode($this->profileModel->getGPLevel($userid));
	}
	
	public function getRedeemable(){
		$userid = $_REQUEST['userid'];
		return json_encode($this->profileModel->getRedeemableGP($userid));
	}
}

?>
