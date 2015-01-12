<?php

include_once("includes.php");

class TransactionController extends BaseController {
	public function getMyTransactions(){
		$sid = $_REQUEST['sid'];
		$userID = ($sid != "")? DB::select("SELECT `To` FROM `messages` WHERE sid='".$sid."'")[0]->To : "";
		if($userID == ""){return "{\"result\":\"error. no user specified\"}";}
		return Queries::getTransactionsById($userID,"Phone");
	}
	
	public function getLatestTransactinos(){
		return Queries::getLatestTransactions();
	}
	
	public function getTransactionsByCardId(){
		return Queries::getTransactionsById($_REQUEST['id'],$_REQUEST['type']);
	}
	
	public function getTransactionsByPhoneId(){
		return Queries::getTransactionsById($_REQUEST['id'],$_REQUEST['type']);
	}
	
	public function getTransactionInfo(){
		return Queries::getTransactionDetails($_REQUEST['id']);
	}
}

?>