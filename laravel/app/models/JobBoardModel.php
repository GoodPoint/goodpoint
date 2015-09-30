<?php

/*
 * Job board database structure:
 * 
 * id | description | posterId | zipcode | numGoodPoints | offerOrRequest | jobCompleted
 * 
 * 
 * offerOrRequest = 0 => the post is an OFFER
 * offerOrRequest = 1 => the post is a REQUEST
 * 
 * jobCompleted => 0 is not complete, 1 is complete
 * if GoodCause is posting on JobBoard, then posterId could point to the GoodCause admin
 * */
class JobBoardModel{

	public function __construct() {
	}

	public function addJob($arrValues) {
		 $arrResult = array();
		 $title = $arrValues['title'];
		 $descr = $arrValues['description'];
		 $posterId = $arrValues['posterId'];
		 $zipcode = $arrValues['zipcode'];
		 $goodPoints = $arrValues['numGoodPoints'];
		 $offerOrRequest = $arrValues['offerOrRequest'];
		 $jobCompleted = 0; // all jobs start out not completed 
		 $sql = "INSERT INTO JobBoard VALUES ";
		 $sql .= "(NULL,'" . $title . "','" . $descr . "','" . $posterId . "','" . $zipcode . "','" . $goodPoints . "','" . $offerOrRequest . "'," . $jobCompleted . "')";
		 try {
			$arrResult['query_result'] = DB::insert($sql);
		} catch(Exception $e) {
			$arrResult['error'] = $e->getMessage();
		}
		return $arrResult;
	}
	
	public function getJobs($arrValues) {
		$arrResult = array();
		$whereClause = $arrValues['whereClause'];
		$sql = "SELECT * FROM JobBoard WHERE " . $whereClause;
		$fetch = null;
		try {
			$fetch = DB::select($sql); // NOTE this is returning the rows as objects
		} catch(Exception $e) {
			$arrResult['error'] = $e->getMessage();
		}
		$arrResult['data'] = $fetch;
		return $arrResult;
	}
	
	public function markJobCompleted($arrValues) {
		$id = $arrValues['id'];
		$sql = "UPDATE `JobBoard` SET jobCompleted=1 WHERE id='".$id."'";
		$arrResult = array();
		try {
			$arrResult['query_result'] = DB::update($sql);
		} catch(Exception $e) {
			$arrResult['error'] = $e->getMessage();
		}
		return $arrResult;
	}
	
	public function markJobNotCompleted($arrValues) {
		$id = $arrValues['id'];
		$sql = "UPDATE `JobBoard` SET jobCompleted=0 WHERE id='".$id."'";
		$arrResult = array();
		try {
			$arrResult['query_result'] = DB::update($sql);
		} catch(Exception $e) {
			$arrResult['error'] = $e->getMessage();
		}
		return $arrResult;
	}
}
?>
