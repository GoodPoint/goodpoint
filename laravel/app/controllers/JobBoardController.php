<?php

include_once("../includes.php");

/*
 * Job board database structure:
 * 
 * id | title | description | posterId | zipcode | numGoodPoints | offerOrRequest | jobCompleted
 * 
 * 
 * offerOrRequest = 0 => the post is an OFFER
 * offerOrRequest = 1 => the post is a REQUEST
 * 
 * jobCompleted => 0 is not complete, 1 is complete
 * */


class JobBoardController extends BaseController {


	private $jobBoardModel;
	
	public function __construct() {
		$this->jobBoardModel = new JobBoardModel();
	}

	public function hello(){
		return Strings::hello();
	}

	public function addJob() {
		$arrValues = array();
		$arrValues['title'] = $_REQUEST['title'];
		$arrValues['description'] = $_REQUEST['description'];
		$arrValues['posterId'] = $_REQUEST['posterId'];
		$arrValues['zipcode'] = $_REQUEST['zipcode'];
		$arrValues['numGoodPoints'] = $_REQUEST['numGoodPoints'];
		$arrValues['offerOrRequest'] = $_REQUEST['offerOrRequest'];
		return json_encode($this->jobBoardModel->addJob($arrValues));
	}
	
	public function getJobById() {
		$id = $_REQUEST['id'];
		$whereClause = "id='" . $id . "'";
		$arrValues = array();
		$arrValues['whereClause'] = $whereClause;
		return json_encode($this->jobBoardModel->getJobs($arrValues))
	}
	
	public function getJobsByZipcode() {
		$zipcode = $_REQUEST['zipcode'];
		$whereClause = "zipcode='" . $zipcode . "' AND jobCompleted=0";
		$arrValues = array();
		$arrValues['whereClause'] = $whereClause;
		return json_encode($this->jobBoardModel->getJobs($arrValues));
	}
	
	public function getJobsByPosterId() {
		$posterId = $_REQUEST['posterId'];
		$whereClause = "posterId='" . $posterId . "' AND jobCompleted=0";
		$arrValues = array("whereClause" => $whereClause);
		return json_encode($this->jobBoardModel->getJobs($arrValues));
	}
	
	public function getAllJobOffers() {
		$whereClause = "offerOrRequest=0 AND jobCompleted=0";
		$arrValues = array("whereClause" => $whereClause);
		return json_encode($this->jobBoardModel->getJobs($arrValues));
	}
	
	public function getAllJobOffersInZipcode() {
		$zipCode = $_REQUEST['zipcode'];
		$whereClause = "offerOrRequest=0 AND zipcode='" . $zipcode . "' AND jobCompleted=0";
		$arrValues = array("whereClause" => $whereClause);
		return json_encode($this->jobBoardModel->getJobs($arrValues));
	}
	
	public function getAllJobRequests() {
		$whereClause = "offerOrRequest=1 AND jobCompleted=0";
		$arrValues = array("whereClause" => $whereClause);
		return json_encode($this->jobBoardModel->getJobs($arrValues));
	}

	
	public function markJobCompleted() {
		$arrValues = array("id" => $_REQUEST['id']);
		return json_encode($this->jobBoardModel->markJobCompleted($arrValues));
	}
	
	public function markJobNotCompleted() {
		$arrValues = array("id" => $_REQUEST['id']);
		return json_encode($this->jobBoardModel->markJobNotCompleted($arrValues));
	}
	
}

?>
