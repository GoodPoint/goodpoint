<?php

class TransactionController extends BaseController {
	
	private $transactionModel;
	
	public function __construct() {
		$this->transactionModel = new TransactionModel();
		
	}
	
	public function getMyTransactions(){
		$arrValues = array();
		$arrValues['sid'] = $_REQUEST['sid'];
		return json_encode($this->transactionModel->getMyTransactions($arrValues));
	}
	
	public function getLatestTransactions(){
		return json_encode($this->transactionModel->getLatestTransactions());
	}
	
	public function getTransactionsByCardId(){
		$arrValues = array();
		$arrValues['id'] = $_REQUEST['id'];
		$arrValues['type'] = $_REQUEST['type'];
		return json_encode($this->transactionModel->getTransactionsById($arrValues));
	}
	
	public function getTransactionsByPhoneId(){
		$phone = $_REQUEST['id']; $pattern = '/[^\d]/'; $replacement = '';
		$phone = preg_replace($pattern, $replacement, $phone);
		$arrValues = array();
		$arrValues['id'] = $phone;
		$arrValues['type'] = $_REQUEST['type'];
		return json_encode($this->transactionModel->getTransactionsById($arrValues));
	}
	
	public function getTransactionInfo(){
		$arrValues = array();
		$arrValues['id'] = $_REQUEST['id'];
		$arrValues['sid'] = $_REQUEST['sid'];
		return json_encode($this->transactionModel->getTransactionInfo($arrValues));
	}
	
	public function uploadMedia(){
	$arrValues = array();
	$arrValues['tid'] = $_REQUEST['tid'];
	$arrValues['caption'] = $_REQUEST['caption'];
	return json_encode($this->transactionModel->uploadMedia($arrValues));	
	}
	
	public function uploadFirstMedia(){
		$arrValues = array();
		$arrValues['sid'] = $_REQUEST['sid'];
		$arrValues['caption'] = $_REQUEST['caption'];
		return json_encode($this->transactionModel->uploadFirstMedia($arrValues));
	}
}

?>
