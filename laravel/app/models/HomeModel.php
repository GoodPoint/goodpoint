<?php

class HomeModel /*extends BaseController */{

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
	
	public function qrowner($cid){
		$arrResult = array();
		try {
			$owner = DB::select("SELECT owner FROM cards WHERE barcode_id = ".$cid)[0]->owner;
		} catch(Exception $e) {
			$arrResult['error'] = $e->getMessage();
		}
		$arrResult['owner'] = $owner;
		return $arrResult;
	}
	
	public function qrscan($arrValues){
		$arrResult = array();
		$from = $arrValues['from'];
		$phone = $arrValues['phone'];
		$cardno = $arrValues['cardno'];
		$old_owner = $arrValues['oldOwner'];
		$custom_sid = "qrscan_" . $phone;
		$_link = "";
		$aorb = $arrValues['aorb'];
		$yesOrNo = $arrValues['yesorno'];
		try{
			switch($aorb){
				case "A": 
					if($yesOrNo == "yes"){
						//award point, A->B
						$owner_to_owner = $this->checkOwner($phone, $cardno);
						if($owner_to_owner){
							$message = TwilioMsg::ownerToOwnerError();
							$step = -1;
							$ab = "a_selfToSelfAttempt";
						} else {
							$sql = "UPDATE cards SET owner = '".$phone."' WHERE barcode_id = ".$cardno;
							$update = DB::update($sql);
							if(!$update){
								$message = TwilioMsg::genericDatabaseError(1).$phone. $cardno;
								$step = -1;
								$ab = "a_err";
							} 
							else {
								$sql = "INSERT INTO transaction (cardid,receiver,giver) VALUES ('".$cardno."','".$phone."','".$old_owner."')";
								$insert = DB::insert($sql);
								if(!$insert){
									$message = TwilioMsg::welcomeMessage();
									$step = -1;
									$ab = "a_err";
								} 
								else {
									//add incomplete user records for giver and receiver
									$insert = $this->initUser($phone);
									$insert = $this->initUser($old_owner);														
									$custom_sid = "qrscan_".$phone;
									//get link to use in message
									$_link = GAPI::urlShorten($arrValues['MessageSid']);
									//$_link = "http://54.149.200.91/winwin/laravel/public/web/leaderboard_page.php?sid=".$custom_sid;
									$message = TwilioMsg::transactionSuccessful($cardno, $_link, $phone);
									$step = 2;
									$ab = "a_success";
								} 
							}
						}
					} 
					else {
						//award real owner->old owner, and then A->B
						$owner_to_owner = $this->checkOwner($phone, $cardno);
						if($owner_to_owner){
							$message = TwilioMsg::ownerToOwnerError();
							$step = -1;
							$ab = "a_selfToSelfAttempt";
						} 
						else {
							$sql = "UPDATE cards SET owner = '".$phone."' WHERE barcode_id = ".$cardno; 
							$update = DB::update($sql);
							if(!$update){
								$message = TwilioMsg::genericDatabaseError(2);			
								$step = -1;
								$ab = "a_err";
							} 
							else {
								$real_giver = $arrValues['realgiver'];
								$sql = "INSERT INTO transaction (cardid,receiver,giver) VALUES ('".$cardno."','".$real_giver."','".$old_owner."')";
								$query = DB::insert($sql);
								if(!$query){
									$message = TwilioMsg::genericDatabaseError(3);
									$step = -1;
									$ab = "a_err";
								} 
								else {
									$sql = "INSERT INTO transaction (cardid,receiver,giver) VALUES ('".$cardno."','".$phone."','".$old_owner."')";
									$query = DB::insert($sql);
									if(!$query){
										$message = TwilioMsg::genericDatabaseError(3);
										$step = -1;
										$ab = "a_err";
									} 
									else {
										//add incomplete user records for giver and receiver
										$insert = $this->initUser($phone);
										$insert = $this->initUser($real_giver);
										$custom_sid = "qrscan_".$phone;
										//get link to use in message
										$_link = GAPI::urlShorten($arrValues['MessageSid']);
										//$_link = "http://54.149.200.91/winwin/laravel/public/web/leaderboard_page.php?sid=".$custom_sid;
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
					$owner_to_owner = $this->checkOwner($phone, $cardno);
					if($owner_to_owner){
						$message = TwilioMsg::ownerToOwnerError();
						$step = -1;
						$ab = "b_selfToSelfAttempt";
					}
					 else {
						$sql = "UPDATE cards SET owner = '".$phone."' WHERE barcode_id = ".$cardno; 
						$update = DB::update($sql);
						if(!$update){
							$message = TwilioMsg::genericDatabaseError(1).$phone.$cardno;
							$step = -1;
							$ab = "b_err_updateOwner";
						} 
						else {
							$sql = "INSERT INTO transaction (cardid,receiver,giver) VALUES ('".$cardno."','".$phone."','".$old_owner."')"; 
							$insert = DB::insert($sql);
							if(!$insert){
								$message = TwilioMsg::welcomeMessage();
								$step = -1;
								$ab = "b_err_transaction";
							} 
							else {
								//add incomplete user records for giver and receiver
								$insert = $this->initUser($phone);
								$insert = $this->initUser($old_owner);
								$custom_sid = "qrscan_".$phone;
								//get link to use in message
								$_link = GAPI::urlShorten($arrValues['MessageSid']);
								//$_link = "http://54.149.200.91/winwin/laravel/public/web/leaderboard_page.php?sid=".$custom_sid;
								$message = TwilioMsg::transactionSuccessful($cardno, $_link, $phone);
								$step = 2;
								$ab = "b_success";
							} 
						}
					}
					break;
				default: break;
			}
			$sql = "INSERT INTO `messages` (`To`,`msg`,`step`,`cardid`,`sid`,`ab`) VALUES ('".$phone."','".$message."','".$step."','".$cardno."','".$custom_sid."','".$ab."')"; 
			$query2 = DB::insert($sql);
			$arrResult['link'] = $_link;
			$arrResult['result'] = $message;
		} catch(Exception $e) {
			$arrResult['error'] = $e->getMessage();
		}
		return $arrResult;
	}
	
		//TODO: use json string to/from DB for $ab instead of | as separator for 'extra' value.
	//      it will be structured as such: {"ab":ab_string_value, "extra":extra_value}
	// going to leave this out of the model for right now
	public function twilio_response($arrValues){
		//initialize XML document properly
		header(Strings::xml_content_type());
		echo Strings::xml_header();
		//grab body of message (what user texted to our Twilio #). doesn't include MMS stuff (thats grabbed later).
		$body = $arrValues['Body'];
		$uncleaned_from = $arrValues['From'];
		$arrValues['From'] = str_replace("+1","",$arrValues['From']);
		//initialize helper variables for handling certain steps
		$step = -1; $barcode_id = -1; $old_owner = -1; $the_sid = -1; $message = "no msg"; $ab = "n/a"; $old_ab = ""; $firstT_step = false;
		//TODO Line 14:need to validate body as phone # in more strict way/better if condition, find regex
		if(intval($body) == 0 || (strlen($body) != 9 && strlen($body)!=5)){ 
			//we are here if they didnt enter a 5-digit number, a 9-digit number, or if what they entered wasn't a number
			//this case handles every response that isn't a cardID (phone submission of giver + text/MMS submissions
			/********IF text matches a supported location, step=10 and set location to $ab********/
			if(array_search(strtoupper(trim($body)), Arrays::vendorArr()) !== false){
				//only if user has GP and message history, continue
				$sql = "SELECT * FROM `messages` WHERE `To`='" . $arrValues['From'] . "' ORDER BY time desc"; 
				$select = DB::select($sql);
				
				if(count($select) > 0){
					$sql = "SELECT (SELECT COUNT(`transaction`.id) FROM transaction WHERE transaction.giver = user.id OR transaction.receiver = user.id) ";
					$sql .= "as `GoodPoints` FROM user where id='" . $arrValues['From'] . "'";
					$res = DB::select($sql);
					if(count($res) == 0) {
						$gp = 0;
					}
					else {
						$gp = $res[0]->GoodPoints;
					}
				//	$gp = Queries::getGPForUser($_REQUEST['From']);
					if(intval($gp) >= Arrays::minimumPurchaseArr()[strtoupper(trim($body))]){
						$location = strtoupper(trim($body));
						$ab = $location;
						$step = 10;
						$message = TwilioMsg::transactionWelcome($location, $gp, Arrays::vendorItemsArr()[$location]);
						$barcode_id = 9;//key for transaction workflow
						$firstT_step = true;
						$sql = "INSERT INTO `messages` (`To`,`msg`,`step`,`cardid`,`sid`,`ab`) VALUES ";
						$sql .= "('".$arrValues['From']."','".$message."','".$step."','".$barcode_id."','".$arrValues['MessageSid']."','".$ab."')"; 
					//	$query2 = DB::insert(Queries::recordMsg($_REQUEST['From'], $message, $step, $barcode_id, $_REQUEST['MessageSid'], $ab));
						$query2 = DB::insert($sql);
						echo "<Response>";
						echo "<Message>";
						//echo $message."hi".$step.$location.$message.$barcode_id.$gp;
						echo $message;
						echo "</Message>";
						echo "</Response>";return;
					} else {
						$message = "Sorry, you do not have enough good points to redeem for goodies at ".strtoupper(trim($body))."...yet! Spread the good and check back in later! (".Arrays::minimumPurchaseArr()[strtoupper(trim($body))]."GP minimum)";
					}
				} else {
					//new user, cannot purchase at Ians...move along
				}
			}
			//get latest message to set up context in which responder should talk back to user
			//grabs all messages in case we need others, but uses latest one only at the moment
			$sql = "SELECT * FROM `messages` WHERE `To`='".$arrValues['From']."' ORDER BY time desc";
			$select = DB::select($sql);
			//$select = DB::select(Queries::getMessagesForUser($_REQUEST['From']));
			//echo var_dump($select);
			if(count($select) > 0 && !$firstT_step){
				//we are here if they have a message history with our Twilio #
				$step = $select[0]->step;
				$barcode_id = $select[0]->cardid;
				$the_sid = $select[0]->sid;
				$old_msg = $select[0]->msg;
				//if we are on step where they were just asked about the previous owner of an entered cardid, 
				//parse the previous owner out of the last message we sent them (which obviously included it)	
				//TODO: now that we have more than one step needing conditional vars set; use switch instead of if's
				if($step > 9 && $step < 16){
					//transaction workflow
					//handleTransaction workflow after location was entered
					switch($step){
						case 10:
							if(!$firstT_step){
								$ab = $select[0]->ab;
								if(isset(Arrays::vendorItemsArr()[$ab][intval(trim($body))-1])){
									$step = 11;
									//to confirm your purchase of <insert menu item here>, text 1. To change the item, text 2. To cancel, text 3. 
									$message = "To confirm your purchase of ".Arrays::vendorItemsArr()[$ab][intval(trim($body))-1]["name"].", text 1. To change the item, text 2. To cancel, text 3.";
									$ab .= "_".(intval(trim($body))-1);
								} else {
									$step = 10;
									$message = "Invalid Entry. Select one of the following choices at ".$ab.":";
									for($i=0;$i<count(Arrays::vendorItemsArr()[$ab]);$i++){
										$item = Arrays::vendorItemsArr()[$ab][$i];
										$message .= "\n".($i+1). ". ".$item["name"]." - ".$item["cost"]." GP";
									}
									break;
								}
							}
							break;
						case 11:
							$ab = $select[0]->ab;
							if(intval(trim($body))!=0){
								switch(intval(trim($body))){
									case 1:
										$ab .= "_confirm";
										//$ab = IANS_2_confirm
										$location = explode("_",$ab)[0];
										$item = explode("_",$ab)[1];
										$message = "To complete your transaction, please present this code to the cashier.".Arrays::vendorItemsArr()[$location][$item]["code"].mt_rand(10000,99999);
										$step = 15;
										break;
									case 2:
										$ab = explode("_",$ab)[0];
										$step = 10;
										$message = "Select one of the following choices at ".$ab.":";
										for($i=0;$i<count(Arrays::vendorItemsArr()[$ab]);$i++){
											$item = Arrays::vendorItemsArr()[$ab][$i];
											$message .= "\n".($i+1). ". ".$item["name"]." - ".$item["cost"]." GP";
										}
										break;
									case 3:
										$ab .= "_cancelled";
										$step = 15;
										$message = "You have cancelled the transaction.";
										break;
									default:
										$step = 11;
										$message = "Invalid Entry. To confirm your purchase, text 1. To change the item, text 2. To cancel, text 3.";
										break;
								}
							}else{
								
							}
						case 12:
						case 13:
						//case 15:
						default: break;
					}
				}
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
				if(strtolower($arrValues['Body']) == "yes"){
					//we are here when user confirms that the last known owner was, in fact, 
					//who gave them the card ID'd by the # they previously entered with a "Yes"
					
					//we now update owner to confirmed new owner, record oldowner->new owner transaction
					//if there is error in any query it all resets, else new owner and transaction are recorded
					//and user is prompted to submit media. step is set to 2 (so we can receive MMS properly)
					/*could be that owner is already $_REQUEST['From'], in that case throw different error message*/
					//$owner_to_owner = Queries::checkOwner($_REQUEST['From'], $barcode_id);
					$owner_to_owner = $this->checkOwner($arrValues['From'], $barcode_id);
					if($owner_to_owner){
						$message = TwilioMsg::ownerToOwnerError();
						$step = -1;
						$ab = "a_selfToSelfAttempt";
					} else {
						$sql = "UPDATE cards SET owner = '" . $arrValues['From'] . "' WHERE barcode_id = ".$barcode_id; 
						//$update = DB::update(Queries::updateOwner($_REQUEST['From'], $barcode_id));
						$update = DB::update($sql);
						if(!$update){
							$message = TwilioMsg::genericDatabaseError(1).$arrValues['From'].$barcode_id;
							$step = -1;
							$ab = "a_err";
						} else {
							$sql = "INSERT INTO transaction (cardid,receiver,giver) VALUES ('".$barcode_id."','".$arrValues['From']."','".$old_owner."')"; 
							//$insert = DB::insert(Queries::insertTransaction($barcode_id, $_REQUEST['From'], $old_owner));
							$insert = DB::insert($sql);
							if(!$insert){
								$message = TwilioMsg::welcomeMessage();
								$step = -1;
								$ab = "a_err";
							} 
							else {
								//add incomplete user records for giver and receiver
							//	$insert = Queries::initUser($_REQUEST['From']);
							//	$insert = Queries::initUser($old_owner);
								$insert = $this->initUser($arrValues['From']);
								$insert = $this->initUser($old_owner);
								//get link to use in message
								$_link = GAPI::urlShorten($arrValues['MessageSid']);
								//$_link = GAPI::urlShorten("http://54.149.200.91/winwin/laravel/public/web/leaderboard_page.php?sid=".$_REQUEST['MessageSid']);
								//$_link = "http://54.149.200.91/winwin/laravel/public/web/leaderboard_page.php?sid=".$_REQUEST['MessageSid'];
								$message = TwilioMsg::transactionSuccessfulReceiver($barcode_id, $_link, $arrValues['From']);
								$message2 = TwilioMsg::transactionSuccessfulGiver($cardno, $_link, $old_owner); // send media message request to the giver
								$step = 2;
								$ab = "a_success";
							} 
						}
					}
				} 
				if(strtolower($arrValues['Body']) == "no"){
					$message = TwilioMsg::promptForRealGiver();
					$step = 1;
					$ab = "a_prompt2"."|".$old_owner;
				}
				if(strtolower($arrValues['Body']) != "yes" && strtolower($arrValues['Body']) != "no"){
					$message = TwilioMsg::yesOrNo(); 
					$step = 0;
					$ab = "a_stall";
				}
				break;
				case 1:
				//check phone number submission in response to who giver really was
				//this happens after they say "no" to "is <last known giver> the giver?" msg
				$phone = $body; $pattern = '/[^\d]/'; $replacement = '';
				$phone = preg_replace($pattern, $replacement, $phone);
				if(strlen($phone)!= 10 && !(strlen($phone)==11 && substr($phone, 0, 1)=='1')){ 
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
				//	$owner_to_owner = Queries::checkOwner($_REQUEST['From'], $barcode_id);
					$owner_to_owner = $this->checkOwner($arrValues['From'], $barcode_id);
					if($owner_to_owner){
						$message = TwilioMsg::ownerToOwnerError();
						$step = -1;
						$ab = "a_selfToSelfAttempt";
					} else {
						$sql = "UPDATE cards SET owner = '".$arrValues['From']."' WHERE barcode_id = ".$barcode_id; 
						//$update = DB::update(Queries::updateOwner($_REQUEST['From'], $barcode_id));
						$update = DB::update($sql);
						if(!$update){
							$message = TwilioMsg::genericDatabaseError(2);			
							$step = -1;
							$ab = "a_err";
						} else {
							$sql = "INSERT INTO transaction (cardid,receiver,giver) VALUES ('".$barcode_id."','".$phone."','".$old_ab."')"; 
							//$query = DB::insert(Queries::insertTransaction($barcode_id, $phone, $old_ab));
							$query = DB::insert($sql);
							if(!$query){
								$message = TwilioMsg::genericDatabaseError(3);
								$step = -1;
								$ab = "a_err";
							} else {
								$sql = "INSERT INTO transaction (cardid,receiver,giver) VALUES ('".$barcode_id."','".$arrValues['From']."','".$phone."')"; 
							//	$query = DB::insert(Queries::insertTransaction($barcode_id, $_REQUEST['From'], $phone));
								$query = DB::insert($sql);
								if(!$query){
									$message = TwilioMsg::genericDatabaseError(3);
									$step = -1;
									$ab = "a_err";
								} else {
									//add incomplete user records for giver and receiver
								//	$insert = Queries::initUser($_REQUEST['From']);
								//	$insert = Queries::initUser($phone);
									$insert = $this->initUser($arrValues['From']);
									$insert = $this->initUser($phone);
									//get link to use in message
									$_link = GAPI::urlShorten($arrValues['MessageSid']);
									//$_link = GAPI::urlShorten("http://54.149.200.91/winwin/laravel/public/web/leaderboard_page.php?sid=".$_REQUEST['MessageSid']);
									//$_link = "http://54.149.200.91/winwin/laravel/public/web/leaderboard_page.php?sid=".$_REQUEST['MessageSid'];
									$message = TwilioMsg::transactionNewPrevOwnerSuccess($barcode_id, $_link, $arrValues['From']);
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
				$sql = "SELECT * FROM transaction WHERE cardid=".$barcode_id." ORDER BY timestamp desc"; 
				//$query = DB::select(Queries::getTransactionsForBarcode($barcode_id));
				$query = DB::select($sql);
				$t_id = (count($query) > 0)? $query[0]->id : -1;
				//fetch media and store in db
				if($arrValues['NumMedia'] == 0){
					//they were prompted to send media but didn't send media
					$message = TwilioMsg::didntSendMedia($t_id);
				}
				else {
					//they did send media
					//flag used to determine outcome of trying to save media
					$failed = false;
					//sid | trans_id | url - schema of media table
					//single or multi insert is apparently the same despite Twilio docs -_- Twilio doc FAIL.
					for($i=0;$i<$arrValues['NumMedia'];$i++){
						$caption = "";
						$sql = "INSERT INTO media (sid, trans_id, url, caption) VALUES ('".$the_sid."','".$t_id."','".$arrValues['MediaUrl'.$i]."','".$caption."')"; 
					//	$query = DB::insert(Queries::insertMedia($the_sid, $t_id, $_REQUEST['MediaUrl'.$i], ""));
						$query = DB::insert($sql);
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
				case 15:
					$step = -1;
					break;
			}
			$sql = "INSERT INTO `messages` (`To`,`msg`,`step`,`cardid`,`sid`,`ab`) VALUES ('".$arrValues['From']."','".$message."','".$step."','".$barcode_id."','".$arrValues['MessageSid']."','".$ab."')"; 
			//$query2 = DB::insert(Queries::recordMsg($_REQUEST['From'], $message, $step, $barcode_id, $_REQUEST['MessageSid'], $ab));	
			$query2 = DB::insert($sql);	
		} else {
			//get card by id, get all card info but use only owner
			$sql = "SELECT * FROM cards WHERE barcode_id = ".$body; 
			//$query = DB::select(Queries::getCardById($body));
			$query = DB::select($sql);
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
				//SKP 3/28: remove a/b test, go with B: get the points
				$a_or_b = 1;//mt_rand(0,1);
				if($a_or_b == 0){
					//A: ask for old owner
					//could be that owner is already $_REQUEST['From'] or within last 3 owners/receivers
					$owner_to_owner = $this->checkOwner($arrValues['From'], $barcode_id);
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
					$owner_to_owner = $this->checkOwner($arrValues['From'], $barcode_id);
					if($owner_to_owner){
						$message = TwilioMsg::ownerToOwnerError();
						$step = -1;
						$ab = "b_selfToSelfAttempt";
					} else {
						$sql = "UPDATE cards SET owner = '".$arrValues['From']."' WHERE barcode_id = ".$barcode_id; 
						//$update = DB::update(Queries::updateOwner($_REQUEST['From'], $barcode_id));
						$update = DB::update($sql);
						if(!$update){
							$message = TwilioMsg::genericDatabaseError(1).$arrValues['From'].$barcode_id;
							$step = -1;
							$ab = "b_err_updateOwner";
						} else {
							$sql = "INSERT INTO transaction (cardid,receiver,giver) VALUES ('".$barcode_id."','". $arrValues['From']."','".$oldOwner."')"; 
						//	$insert = DB::insert(Queries::insertTransaction($barcode_id, $_REQUEST['From'], $oldOwner));
							$insert = DB::insert($sql);
							if(!$insert){
								$message = TwilioMsg::welcomeMessage();
								$step = -1;
								$ab = "b_err_transaction";
							} else {
								//add incomplete user records for giver and receiver
								$insert = $this->initUser($arrValues['From']);
								$insert = $this->initUser($oldOwner);
								//get link to use in message
								die();
								$_link = GAPI::urlShorten($arrValues['MessageSid']);
								//$_link = GAPI::urlShorten("http://54.149.200.91/winwin/laravel/public/web/leaderboard_page.php?sid=".$_REQUEST['MessageSid']);
								//$_link = "http://54.149.200.91/winwin/laravel/public/web/leaderboard_page.php?sid=".$_REQUEST['MessageSid'];
								$message = "Thank you! You and the giver both just received +1 Good Point. Please go to ".$_link." or text media to share your story. Keep up the Good Work!";
								die(var_dump($_link));
								$cardno = $barcode_id;
								$message2 = "Thank you for sharing good! ".$arrValues['From']." just recorded the card you gave them!"; // send media message request to the giver
								$step = 2;
								$ab = "b_success";
							} 
						}
					}
				}
				//END A/B TEST.
			}
			
			$sql = "INSERT INTO `messages` (`To`,`msg`,`step`,`cardid`,`sid`,`ab`) VALUES ";
			$sql .= "('".$arrValues['From']."','".$message."','".$step."','".$barcode_id."','".$arrValues['MessageSid']."','".$ab."')"; 
			//$query2 = DB::insert(Queries::recordMsg($_REQUEST['From'], $message, $step, $barcode_id, $_REQUEST['MessageSid'], $ab));
			$query2 = DB::insert($sql);
			if(isset($message2) && intval($oldOwner) != 0){
				$sql = "INSERT INTO `messages` (`To`,`msg`,`step`,`cardid`,`sid`,`ab`) VALUES ";
				$sql .= "('".$oldOwner."','".$message2."','".$step."','".$barcode_id."','".$arrValues['MessageSid']."','".$ab."')"; 
				//$query3 = DB::insert(Queries::recordMsg($old_owner, $message2, $step, $barcode_id, $_REQUEST['MessageSid'], $ab));
				$query3 = DB::insert($sql);
				//$this->sendText($oldOwner, "+16082607105", $message2);
			}
			
		}
		//echo var_dump($result);
		//TODO: debug mode/flag/param that outputs and logs results of queries+logic, including successes and failures
		//TODO: overall 'success' or 'failure' boolean that is based on context and result of logic, in any cases
		//not handled by error messages (there may be none, we need to test extensively though)
		echo "<Response>";
			echo "<Message><Body>";
			echo $message;
			echo "</Body></Message>";
		echo "</Response>";
/*		
		echo "<Response>";
			echo "<Message><Body>";
			echo $message2;
			echo "</Body></Message>";
		echo "</Response>";
*/
	
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

	private static function checkOwner($potential_owner, $barcode_id){
		/*$select = DB::select("SELECT * FROM `transaction` where cardid=".$barcode_id." order by timestamp desc limit 2");
		for($i=0; $i<count($select); $i++){
			if($select[$i]->giver == $potential_owner || $select[$i]->receiver == $potential_owner){
				return true;
			}
		}*/
		//alpha
		$select = DB::select("SELECT * FROM `transaction` where cardid=".$barcode_id." order by timestamp desc");
		for($i=0; $i<count($select); $i++){
			if(($select[$i]->giver == $potential_owner || $select[$i]->receiver == $potential_owner) && $i < 2){
				//if there is a match with giver or receiver in the last two transactions ($i=0 and $i=1) then not allowed
				return true;
			}
			$date = date('M j Y g:i A', strtotime($select[$i]->timestamp));
			if(($select[$i]->giver == $potential_owner || $select[$i]->receiver == $potential_owner) && ($date > (time() - 86400))){
				//if match with giver or receiver in the past 24hr, no matter what, then not allowed
				return true;
			}
		}
		return false;
	}
	
	private function initUser($number) {
		$check = DB::select("SELECT count(*) as count FROM `user` WHERE `id`='".$number."'");
		if($check[0]->count == 0){
			return DB::insert("INSERT INTO `user` (`id`, `profile_json`, `last_updated`) VALUES ('".$number."', '{}', CURRENT_TIMESTAMP)");
		}
	}

}
?>
