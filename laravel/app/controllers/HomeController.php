<?php

include_once("includes.php");

class HomeController extends BaseController {

	public function hello(){
		return Strings::hello();
	}
	
	public function batchAdd(){
		for($i=111111111;$i<111111611;$i++){
			DB::insert("INSERT INTO cards (barcode_id,card_type,card_value,owner) VALUES(".$i.", 1, 10, 'GoodPoint Launch')");
		}
	}
	
	public function qrowner(){
		$cid = $_REQUEST['cid'];
		$owner = DB::select("SELECT owner FROM cards WHERE barcode_id = ".$cid)[0]->owner;
		return json_encode(array("owner"=>$owner));
	}
	
	public function qrscan(){
		try{
			$phone = $_REQUEST['phone'];
			$cardno = $_REQUEST['cardno'];
			$old_owner = $_REQUEST['oldOwner'];
			
			$custom_sid = "qrscan_".$phone;
			$_link = "";
			switch($_REQUEST['aorb']){
				case "A": 
					if($_REQUEST['yesorno'] == "yes"){
						//award point, A->B
						$owner_to_owner = Queries::checkOwner($phone, $cardno);
						if($owner_to_owner){
							$message = TwilioMsg::ownerToOwnerError();
							$step = -1;
							$ab = "a_selfToSelfAttempt";
						} else {
							$update = DB::update(Queries::updateOwner($phone, $cardno));
							if(!$update){
								$message = TwilioMsg::genericDatabaseError(1).$phone. $cardno;
								$step = -1;
								$ab = "a_err";
							} else {
								$insert = DB::insert(Queries::insertTransaction($cardno, $phone, $old_owner));
								if(!$insert){
									$message = TwilioMsg::welcomeMessage();
									$step = -1;
									$ab = "a_err";
								} else {
									//add incomplete user records for giver and receiver
									$insert = Queries::initUser($phone);
									$insert = Queries::initUser($old_owner);
									$custom_sid = "qrscan_".$phone;
									//get link to use in message
									//$_link = GAPI::urlShorten("http://54.149.200.91/winwin/laravel/public/web/leaderboard_page.php?sid=".$_REQUEST['MessageSid']);
									$_link = "http://54.149.200.91/winwin/laravel/public/web/leaderboard_page.php?sid=".$custom_sid;
									$message = TwilioMsg::transactionSuccessful($cardno, $_link, $phone);
									$step = 2;
									$ab = "a_success";
								} 
							}
						}
					} else {
						//award real owner->old owner, and then A->B
						$owner_to_owner = Queries::checkOwner($phone, $cardno);
						if($owner_to_owner){
							$message = TwilioMsg::ownerToOwnerError();
							$step = -1;
							$ab = "a_selfToSelfAttempt";
						} else {
							$update = DB::update(Queries::updateOwner($phone, $cardno));
							if(!$update){
								$message = TwilioMsg::genericDatabaseError(2);			
								$step = -1;
								$ab = "a_err";
							} else {
								$real_giver = $_REQUEST['realgiver'];
								$query = DB::insert(Queries::insertTransaction($cardno, $real_giver, $old_owner));
								if(!$query){
									$message = TwilioMsg::genericDatabaseError(3);
									$step = -1;
									$ab = "a_err";
								} else {
									$query = DB::insert(Queries::insertTransaction($cardno, $phone, $real_giver));
									if(!$query){
										$message = TwilioMsg::genericDatabaseError(3);
										$step = -1;
										$ab = "a_err";
									} else {
										//add incomplete user records for giver and receiver
										$insert = Queries::initUser($phone);
										$insert = Queries::initUser($real_giver);
										$custom_sid = "qrscan_".$phone;
										//get link to use in message
										//$_link = GAPI::urlShorten("http://54.149.200.91/winwin/laravel/public/web/leaderboard_page.php?sid=".$_REQUEST['MessageSid']);
										$_link = "http://54.149.200.91/winwin/laravel/public/web/leaderboard_page.php?sid=".$custom_sid;
										$message = TwilioMsg::transactionNewPrevOwnerSuccess($cardno, $_link, $phone);
										$step = 2;
										$ab = "a_success2";
									}
								}
							}
						}
					}
					break;
				case "B": 
					//award point, A->B
					//could be that owner is already $_REQUEST['From'] or within last 3 owners/receivers
					$owner_to_owner = Queries::checkOwner($phone, $cardno);
					if($owner_to_owner){
						$message = TwilioMsg::ownerToOwnerError();
						$step = -1;
						$ab = "b_selfToSelfAttempt";
					} else {
						$update = DB::update(Queries::updateOwner($phone, $cardno));
						if(!$update){
							$message = TwilioMsg::genericDatabaseError(1).$phone.$cardno;
							$step = -1;
							$ab = "b_err_updateOwner";
						} else {
							$insert = DB::insert(Queries::insertTransaction($cardno, $phone, $old_owner));
							if(!$insert){
								$message = TwilioMsg::welcomeMessage();
								$step = -1;
								$ab = "b_err_transaction";
							} else {
								//add incomplete user records for giver and receiver
								$insert = Queries::initUser($phone);
								$insert = Queries::initUser($old_owner);
								$custom_sid = "qrscan_".$phone;
								//get link to use in message
								//$_link = GAPI::urlShorten("http://54.149.200.91/winwin/laravel/public/web/leaderboard_page.php?sid=".$_REQUEST['MessageSid']);
								$_link = "http://54.149.200.91/winwin/laravel/public/web/leaderboard_page.php?sid=".$custom_sid;
								$message = TwilioMsg::transactionSuccessful($cardno, $_link, $phone);
								$step = 2;
								$ab = "b_success";
							} 
						}
					}
					break;
				default: break;
			}
			$query2 = DB::insert(Queries::recordMsg($phone, $message, $step, $cardno, $custom_sid, $ab));
			return json_encode(array("link"=>$_link,"result"=>$message));
		} catch(Exception $e) {
			return "{\"result\":\"".$e->getMessage()."\"}";
		}
	}

	//TODO: use json string to/from DB for $ab instead of | as separator for 'extra' value.
	//      it will be structured as such: {"ab":ab_string_value, "extra":extra_value}
	public function twilio_response(){
		//initialize XML document properly
		header(Strings::xml_content_type());
		echo Strings::xml_header();
		//grab body of message (what user texted to our Twilio #). doesn't include MMS stuff (thats grabbed later).
		$body = $_REQUEST['Body'];
		//initialize helper variables for handling certain steps
		$step = -1; $barcode_id = -1; $old_owner = -1; $the_sid = -1; $message = "no msg"; $ab = "n/a"; $old_ab = "";
		//TODO Line 14:need to validate body as phone # in more strict way/better if condition, find regex
		if(intval($body) == 0 || strlen($body) != 9){ 
			//we are here if they didnt enter a 9-digit number or if what they entered wasn't a number
			//this case handles every response that isn't a cardID (phone submission of giver + text/MMS submissions
			
			//get latest message to set up context in which responder should talk back to user
			//grabs all messages in case we need others, but uses latest one only at the moment
			$select = DB::select(Queries::getMessagesForUser($_REQUEST['From']));
			//echo var_dump($select);
			if(count($select) > 0){
				//we are here if they have a message history with our Twilio #
				$step = $select[0]->step;
				$barcode_id = $select[0]->cardid;
				$the_sid = $select[0]->sid;
				//if we are on step where they were just asked about the previous owner of an entered cardid, 
				//parse the previous owner out of the last message we sent them (which obviously included it)	
				//TODO: now that we have more than one step needing conditional vars set; use switch instead of if's
				if($step == 0){
					$old_owner_arr = explode(" ",$select[0]->msg);
					//get word before ?, i.e. the previous recorded owner we asked if they got it from
					$index = count($old_owner_arr) - 2;
					$old_owner = $old_owner_arr[$index];
				}
				if($step == 1){	
					//TODO:when updating ab field in DB and $ab to use json, parse it out with json_decode
					//     instead of this array parsing...
					$old_ab_arr = explode("|",$select[0]->ab);
					$old_ab = $old_ab_arr[1];
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
				//TODO: $y_or_n = strtolower($_REQUEST['Body']).replace(" ","") or trim whitespace on both sides
				//then use $y_or_n instead of repeating $_REQUEST['Body']
				if(strtolower($_REQUEST['Body']) == "yes"){
					//we are here when user confirms that the last known owner was, in fact, 
					//who gave them the card ID'd by the # they previously entered with a "Yes"
					
					//we now update owner to confirmed new owner, record oldowner->new owner transaction
					//if there is error in any query it all resets, else new owner and transaction are recorded
					//and user is prompted to submit media. step is set to 2 (so we can receive MMS properly)
					/*could be that owner is already $_REQUEST['From'], in that case throw different error message*/
					$owner_to_owner = Queries::checkOwner($_REQUEST['From'], $barcode_id);
					if($owner_to_owner){
						$message = TwilioMsg::ownerToOwnerError();
						$step = -1;
						$ab = "a_selfToSelfAttempt";
					} else {
						$update = DB::update(Queries::updateOwner($_REQUEST['From'], $barcode_id));
						if(!$update){
							$message = TwilioMsg::genericDatabaseError(1).$_REQUEST['From'].$barcode_id;
							$step = -1;
							$ab = "a_err";
						} else {
							$insert = DB::insert(Queries::insertTransaction($barcode_id, $_REQUEST['From'], $old_owner));
							if(!$insert){
								$message = TwilioMsg::welcomeMessage();
								$step = -1;
								$ab = "a_err";
							} else {
								//add incomplete user records for giver and receiver
								$insert = Queries::initUser($_REQUEST['From']);
								$insert = Queries::initUser($old_owner);
								//get link to use in message
								//$_link = GAPI::urlShorten("http://54.149.200.91/winwin/laravel/public/web/leaderboard_page.php?sid=".$_REQUEST['MessageSid']);
								$_link = "http://54.149.200.91/winwin/laravel/public/web/leaderboard_page.php?sid=".$_REQUEST['MessageSid'];
								$message = TwilioMsg::transactionSuccessful($barcode_id, $_link, $_REQUEST['From']);
								$step = 2;
								$ab = "a_success";
							} 
						}
					}
				} 
				if(strtolower($_REQUEST['Body']) == "no"){
					$message = TwilioMsg::promptForRealGiver();
					$step = 1;
					$ab = "a_prompt2"."|".$old_owner;
				}
				if(strtolower($_REQUEST['Body']) != "yes" && strtolower($_REQUEST['Body']) != "no"){
					$message = TwilioMsg::yesOrNo(); 
					$step = 0;
					$ab = "a_stall";
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
					$ab = "a_err";
				} else {
					//we can do same workflow as inserting transaction when they say yes,
					//except with the new, 'real' giver they just supplied.
					//try update owner of card, then process 2 transactions. if either of 3 steps 
					//dont work, reset workflow to prevent 'pseudo'-transactions and provide error.
					//TODO:find better way to catch errors along the way, group error handling
					//     into helper fxn since its basically the same or similar for all cases...
					//     probably with log+return statement right at error, so dont have to mess with conditionals
					/*could be that owner is already $_REQUEST['From'], in that case throw different error message*/
					$owner_to_owner = Queries::checkOwner($_REQUEST['From'], $barcode_id);
					if($owner_to_owner){
						$message = TwilioMsg::ownerToOwnerError();
						$step = -1;
						$ab = "a_selfToSelfAttempt";
					} else {
						$update = DB::update(Queries::updateOwner($_REQUEST['From'], $barcode_id));
						if(!$update){
							$message = TwilioMsg::genericDatabaseError(2);			
							$step = -1;
							$ab = "a_err";
						} else {
							$query = DB::insert(Queries::insertTransaction($barcode_id, $body, $old_ab));
							if(!$query){
								$message = TwilioMsg::genericDatabaseError(3);
								$step = -1;
								$ab = "a_err";
							} else {
								$query = DB::insert(Queries::insertTransaction($barcode_id, $_REQUEST['From'], $body));
								if(!$query){
									$message = TwilioMsg::genericDatabaseError(3);
									$step = -1;
									$ab = "a_err";
								} else {
									//add incomplete user records for giver and receiver
									$insert = Queries::initUser($_REQUEST['From']);
									$insert = Queries::initUser($body);
									//get link to use in message
									//$_link = GAPI::urlShorten("http://54.149.200.91/winwin/laravel/public/web/leaderboard_page.php?sid=".$_REQUEST['MessageSid']);
									$_link = "http://54.149.200.91/winwin/laravel/public/web/leaderboard_page.php?sid=".$_REQUEST['MessageSid'];
									$message = TwilioMsg::transactionNewPrevOwnerSuccess($barcode_id, $_link, $_REQUEST['From']);
									$step = 2;
									$ab = "a_success2";
								}
							}
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
			$query2 = DB::insert(Queries::recordMsg($_REQUEST['From'], $message, $step, $barcode_id, $_REQUEST['MessageSid'], $ab));	
		} else {
			//get card by id, get all card info but use only owner
			$query = DB::select(Queries::getCardById($body));
			if(count($query) == 0){ //invalid card ID/barcode entered
				$message = TwilioMsg::invalidCardId();
				$step = -1;
			} else {
				//grab old owner and cardID for this and potentially next step, used for both A/B cases in this step
				$oldOwner = $query[0]->owner;
				$barcode_id = $body;
				//BEGIN A/B TEST :) - roughly half the time we ask for old owner, half the time they just get the points.
				/*latest benchmark of mt_rand() function with 10000 calls: 5031[1] => 4969[0]...pretty good :)
				**with 100 calls, never beyond 40/60, rarely beyond 55/45...
				**you can benchmark rand and mt_rand in app/tests/random_test.php*/
				$a_or_b = mt_rand(0,1);
				if($a_or_b == 0){
					//A: ask for old owner
					//could be that owner is already $_REQUEST['From'] or within last 3 owners/receivers
					$owner_to_owner = Queries::checkOwner($_REQUEST['From'], $barcode_id);
					if($owner_to_owner){
						$message = TwilioMsg::ownerToOwnerError();
						$step = -1;
						$ab = "a_selfToSelfAttempt";
					} else {
						$message = TwilioMsg::promptForGiver($oldOwner);
						$step = 0;
						$ab = "a_prompt";
					}
				} else { //$a_or_b == 1
					//B: get the points
					//could be that owner is already $_REQUEST['From'] or within last 3 owners/receivers
					$owner_to_owner = Queries::checkOwner($_REQUEST['From'], $barcode_id);
					if($owner_to_owner){
						$message = TwilioMsg::ownerToOwnerError();
						$step = -1;
						$ab = "b_selfToSelfAttempt";
					} else {
						$update = DB::update(Queries::updateOwner($_REQUEST['From'], $barcode_id));
						if(!$update){
							$message = TwilioMsg::genericDatabaseError(1).$_REQUEST['From'].$barcode_id;
							$step = -1;
							$ab = "b_err_updateOwner";
						} else {
							$insert = DB::insert(Queries::insertTransaction($barcode_id, $_REQUEST['From'], $oldOwner));
							if(!$insert){
								$message = TwilioMsg::welcomeMessage();
								$step = -1;
								$ab = "b_err_transaction";
							} else {
								//add incomplete user records for giver and receiver
								$insert = Queries::initUser($_REQUEST['From']);
								$insert = Queries::initUser($oldOwner);
								//get link to use in message
								//$_link = GAPI::urlShorten("http://54.149.200.91/winwin/laravel/public/web/leaderboard_page.php?sid=".$_REQUEST['MessageSid']);
								$_link = "http://54.149.200.91/winwin/laravel/public/web/leaderboard_page.php?sid=".$_REQUEST['MessageSid'];
								$message = TwilioMsg::transactionSuccessful($barcode_id, $_link, $_REQUEST['From']);
								$step = 2;
								$ab = "b_success";
							} 
						}
					}
				}
				//END A/B TEST.
			}
			$query2 = DB::insert(Queries::recordMsg($_REQUEST['From'], $message, $step, $barcode_id, $_REQUEST['MessageSid'], $ab));
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