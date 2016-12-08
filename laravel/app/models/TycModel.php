<?php

class TycModel {
	
	public function login(){
		$error = false; $emsg = "";
		$user = DB::select("SELECT * FROM tyc_user WHERE user_id='".$_POST['user_id']."' AND org='".$_POST['org']."'");
		if(count($user) < 1){
			$error = true; $emsg .= " invalid username";
		}
		if(Hash::check($_POST['pw'],$user[0]->pw)){
			return array("blharagle"=>$_POST['user_id']);
		} else {
			$error = true; $emsg .= " invalid password";
		}
	}
	
	public function signup(){
		$org = $_POST['org'];
		$user_id = $_POST['user_id'];
		$pw = $_POST['pw'];
		$hashedPW = Hash::make($pw);
		
		$signup = DB::insert("INSERT INTO `user` VALUES('".$user_id."','{}',CURRENT_TIMESTAMP,1)");
		$signup_tyc = DB::insert("INSERT INTO tyc_user VALUES('".$user_id."','".$hashedPW."','".$org."','".$pw."')");
		
		return array("blharagle"=>$user_id);
	}
	
	public function forgot(){
		$email = $_POST['email'];
		$user = DB::select("SELECT forgot_pw,org FROM tyc_user WHERE user_id='".$email."'");
		if(count($user)<1){
			return array("message"=>"Email not found in our records. Make sure you enter the email you registered with.");
		} else {
			$forgot_pw = $user[0]->forgot_pw;
			$subject = "Your Thank You Card Campaign Password";
			$message = "Your password is: ".$forgot_pw."\n"."Login here:"."http://www.goodpointgame.com/winwin/laravel/public/web/thankyoucard.php?org=".$user[0]->org;
			$headers = 'From: webmaster@goodpointgame.com' . "\r\n" .
			'Reply-To: webmaster@goodpointgame.com' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
			mail($email, $subject, $message, $headers);
			return array("message"=>"Password sent to your email address!");
		}
	}
	
	public function submitCardID(){
		$uid = $_POST['uid'];
		$cardno = $_POST['cardno'];
		$alert = ""; $message = ""; $cssClass = "";
		
		//1. Check for valid card
		$validCard = DB::select("SELECT * FROM cards WHERE barcode_id='".$cardno."'");
		if(count($validCard) < 1){
			$alert = "Invalid Card ID. Make sure its correct and try again";
			$message = "Error. Please Try Again";
			return array("alert"=>$alert,"message"=>$message,"cssClass"=>"error");
		}
		
		//2. Check for last 3 transactions
		$lastThree = DB::select("SELECT receiver,giver FROM `transaction` WHERE cardid='".$cardno."' ORDER BY timestamp DESC LIMIT 3");
		foreach($lastThree as $eachOne){
			if($eachOne->giver == $uid || $eachOne->receiver == $uid){
				$alert = "You need to let this card go around more before entering it again!";
				$message = "Error. Please Try Later";
				return array("alert"=>$alert,"message"=>$message,"cssClass"=>"error");
			}
		}
		
		//3. Get last receiver (current giver)
		$lastOne = DB::select("SELECT receiver FROM `transaction` WHERE cardid='".$cardno."' ORDER BY timestamp DESC LIMIT 1");
		if(count($lastOne)<1){
			$giver = "Blackhawk Middle School";
		} else {
			$giver = $lastOne[0]->receiver;
		}
		
		//4. Record transaction!
		$new_transaction = DB::insert("INSERT INTO `transaction` VALUES(NULL,'".$cardno."','".$uid."','".$giver."',CURRENT_TIMESTAMP)");
		$alert = "Thank you for recording!";
		$message = "Thank You!";
		$cssClass = "success";
		return array("alert"=>$alert,"message"=>$message,"cssClass"=>$cssClass);
	}
	
}

?>
