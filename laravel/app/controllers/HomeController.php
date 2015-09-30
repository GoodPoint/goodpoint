<?php

include_once("../includes.php");

class HomeController extends BaseController {


	private $homeModel;
	
	public function __construct() {
		$this->homeModel = new HomeModel();
	}


	public function hello(){
		return Strings::hello();
	}
	
	public function batchAdd(){
		for($i=111111111;$i<111111611;$i++){
			DB::insert("INSERT INTO cards (barcode_id,card_type,card_value,owner) VALUES(".$i.", 1, 10, 'GoodPoint Launch')");
		}
	}
	
	public function batchAdd2(){
		$row = 1;
		if (($handle = fopen("card_numbers.csv", "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$num = count($data);
				echo "<p> $num fields in line $row: <br /></p>\n";
				$row++;
				for ($c=0; $c < $num; $c++) {
					echo $data[$c] . "<br />\n";
				}
			}
			fclose($handle);
		}
	}

	public function qrowner(){
		$cid = $_REQUEST['cid'];
		return json_encode($this->homeModel->qrowner($cid));
	}
	
	public function qrscan(){
		$arrValues = array();
		$arrValues['phone'] = $_REQUEST['phone'];
		$arrValues['cardno'] = $_REQUEST['cardno'];
		$arrValues['oldOwner'] = $_REQUEST['oldOwner'];
		$arrValues['yesorno'] = $_REQUEST['yesorno'];
		$arrValues['MessageSid'] = $_REQUEST['MessageSid'];
		$arrValues['realgiver'] = $_REQUEST['realgiver'];
		$arrValues['aorb'] = $_REQUEST['aorb'];
		return json_encode($this->homeModel->qrscan($arrValues));
	}


	//TODO: use json string to/from DB for $ab instead of | as separator for 'extra' value.
	//      it will be structured as such: {"ab":ab_string_value, "extra":extra_value}
	// going to leave this out of the model for right now
	public function twilio_response(){
		$arrValues = array();
		$arrValues['body'] = $_REQUEST['body'];
		$arrValues['From'] = $_REQUEST['From'];
		$arrValues['MessageSid'] = $_REQUEST['MessageSid'];
		$arrValues['NumMedia'] = $_REQUEST['NumMedia'];
		$arrValues['MediaUrl'] = $_REQUEST['MediaUrl'];
		$this->homeModel->twilio_response($arrValues);	
	}
	
		private function sendText($to, $from, $body) {
		
		// set your AccountSid and AuthToken from www.twilio.com/user/account
		$AccountSid = "ACee1a2b72c78697cb71ffd9762bf5431a";
		$AuthToken = "eeeefef73f544fce3b7b7f3decfddf86";
		 
		$client = new Services_Twilio($AccountSid, $AuthToken);
		 
		$message = $client->account->messages->create(array(
			"From" => $from,
			"To" => $to,
			"Body" => $body,
		));
		 
		// Display a confirmation message on the screen
	//echo "Sent message {$message->sid}";
	}

}
?>
