<?php

include_once("includes.php");

class HomeController extends BaseController {

	public function hello(){
		return Strings::hello();
	}

	public function twilio_response(){
		//initialize XML document properly
		header(Strings::xml_content_type());
		echo Strings::xml_header();
		//grab body of message (what user texted to our Twilio #). doesn't include MMS stuff (thats grabbed later).
		$body = $_REQUEST['Body'];
		//initialize helper variables for handling certain steps
		$step = -1; $barcode_id = -1; $old_owner = -1; $the_sid = -1;
		//TODO Line 14:need to validate body as phone # in more strict way/better if condition, find regex
		if(intval($body) == 0 || strlen($body) != 9){ 
			//we are here if they didnt enter a 9-digit number or if what they entered wasn't a number
			//this case handles every response that isn't a cardID (phone submission of giver + text/MMS submissions
			
			//get latest message to set up context in which responder should talk back to user
			//grabs all messages in case we need others, but uses latest one only at the moment
			$select = DB::select(Queries::getMessagesForUser($_REQUEST['From']));
			if(count($select) > 0){
				//we are here if they have a message history with our Twilio #
				$step = $select[0]->step;
				$barcode_id = $select[0]->cardid;
				$the_sid = $select[0]->sid;
				//if we are on step where they were just asked about the previous owner of an entered cardid, 
				//parse the previous owner out of the last message we sent them (which obviously included it)				
				if($step == 0){
					$old_owner_arr = explode(" ",$select[0]->msg);
					//weird but this returns 1 word before end, not 2 as you might expect
					$index = count($old_owner_arr) - 1;
					$old_owner = $old_owner_arr[$index];
				}
			} else {
				//TODO: log no message history, this is new user...
			}
			switch($step){
				case -1: 
					//their last message resulted in an error that requires restarting the transaction process,
					//or they were otherwise done with the workflow. so workflow is reset, and it welcomes them 
					$message = TwilioMsg::welcomeMessage();	
					$step = -1; break;
				case 0:
				if(strtolower($_REQUEST['Body']) == strtolower("Yes")){
					//we are here when user confirms that the last known owner was, in fact, 
					//who gave them the card ID'd by the # they previously entered with a "Yes"
					
					//we now update owner to confirmed new owner, record oldowner->new owner transaction
					//if there is error in any query it all resets, else new owner and transaction are recorded
					//and user is prompted to submit media. step is set to 2 (so we can receive MMS properly)
					$update = DB::update(Queries::updateOwner($_REQUEST['From'], $barcode_id));
					if(!$update){
						$message = TwilioMsg::genericDatabaseError();
						$step = -1;
					} else {
						$insert = DB::insert(Queries::insertTransaction($barcode_id, $_REQUEST['From'], $old_owner));
						if(!$insert){
							$message = TwilioMsg::welcomeMessage();
							$step = -1;
						} else {
							$message = TwilioMsg::transactionSuccessful($barcode_id);
							$step = 2;
						} 
					}
				} 
				if($_REQUEST['Body'] == "No"){
					$message = TwilioMsg::promptForRealGiver();
					$step = 1;
				}
				if($_REQUEST['Body'] != "Yes" && $_REQUEST['Body'] != "No"){
					$message = TwilioMsg::yesOrNo(); 
					$step = 0;
				}
				break;
				case 1:
				//check phone number submission in response to who giver really was
				//this happens after they say "no" to "is <last known giver> the giver?" msg
				if(intval($body) == 0 || strlen($body)!= 10){ 
					//need to validate phone number in more strict way, find regex
					//but basically we will always be here if they entered phone # wrong
					$message = TwilioMsg::wrongPhoneFormat();
					$step = 1;
				} else {
					//we can do same workflow as inserting transaction when they say yes,
					//except with the new, 'real' giver they just supplied.
					//try update owner of card, then process transaction. if either dont work
					//reset workflow to prevent 'pseudo'-transactions and provide error.
					$update = DB::update(Queries::updateOwner($_REQUEST['From'], $barcode_id));
					if(!$result){
						$message = TwilioMsg::genericDatabaseError();			
						$step = -1;
					} else {
						$query = DB::insert(Queries::insertTransaction($barcode_id, $_REQUEST['From'], $body));
						if(!$result){
							$message = TwilioMsg::genericDatabaseError();
							$step = -1;
						} else {
							$message = TwilioMsg::transactionNewPrevOwnerSuccess($barcode_id);
							$step = 2;
						}
					}
				}
				break;
				case 2:
				//grab newest transaction id for $barcode_id to link media to, like with messages 
				//we grab all transactions + info in case we need, but use latest id only
				$query = DB::select(Queries::getTransactionsForBarcode($barcode_id));
				$t_id = (count($query) > 0)? $query[0]->id : -1;
				//fetch media and store in db
				if($_REQUEST['NumMedia'] == 0){
					//they were prompted to send media but didn't send media
					$message = TwilioMsg::didntSendMedia($t_id);
				}
				else {
					//they did send media
					//flag used to determine outcome of trying to save media
					$failed = false;
					//sid | trans_id | url - schema of media table
					//single or multi insert is apparently the same despite Twilio docs -_- Twilio doc FAIL.
					for($i=0;$i<$_REQUEST['NumMedia'];$i++){
						$query = DB::insert(Queries::insertMedia($the_sid, $t_id, $_REQUEST['MediaUrl'.$i]));
						if(!$query){
							$failed = true;
							$message = TwilioMsg::mediaFailed($t_id);
						}
					}
					if(!$failed){
						$message = TwilioMsg::mediaSucceeded($t_id);
					}
				}
				$step = -1;//reset workflow for future transactions @ this no
				break;
			}
			$query2 = DB::insert(Queries::recordMsg($_REQUEST['From'], $message, $step, $barcode_id, $_REQUEST['MessageSid']));	
		} else {
			//get card by id, get all card info but use only owner
			$query = DB::select(Queries::getCardById($body));
			if(count($query) == 0){ //invalid card ID/barcode entered
				$message = TwilioMsg::invalidCardId();
				$step = -1;
			} else {
				$oldOwner = $query[0]->owner;
				$message = TwilioMsg::promptForGiver($oldOwner);
				$step = 0;
				$barcode_id = $body;
			}
			$query2 = DB::insert(Queries::recordMsg($_REQUEST['From'], $message, $step, $barcode_id, $_REQUEST['MessageSid']));
		}
		//echo var_dump($result);
		//TODO: debug mode/flag/param that outputs and logs results of queries+logic, including successes and failures
		//TODO: overall 'success' or 'failure' boolean that is based on context and result of logic, in any cases
		//not handled by error messages (there may be none, we need to test extensively though)
		echo "<Response>";
			echo "<Message>";
			echo $message;
			echo "</Message>";
		echo "</Response>";
	}

}
?>