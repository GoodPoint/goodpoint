<?php

class TwilioMsg {

	//full messages
	public static function welcomeMessage() { return "Welcome to WIN+WIN card exchange! You must enter the code on the bottom of your newly obtained card to register it as yours!"; }
	public static function genericDatabaseError($num){ return "Database error ".$num.", contact WIN+WIN admin"; }
	public static function transactionSuccessful($barcode_id, $link, $phone){ 
		//return "You have registered this card successfully! Please go to ".$link." and add media to this transaction! You can also text us some media (pic/video) to show the good youve been a part of!"; 
		return "Thank you! You and the giver both just received +1 Good Point. Please go to ".$link." or text media to share your story. Keep up the Good Work!";
	}
	public static function transactionSuccessfulReceiver($barcode_id, $link, $phone){ 
		//return "You have registered this card successfully! Please go to ".$link." and add media to this transaction! You can also text us some media (pic/video) to show the good youve been a part of!"; 
		return "Thank you! You and the giver both just received +1 Good Point. Please go to ".$link." or text media to share your story. Keep up the Good Work!";
	}
	
	public static function transactionSuccessfulGiver($barcode_id, $link, $phone){ 
		//return "You have registered this card successfully! Please go to ".$link." and add media to this transaction! You can also text us some media (pic/video) to show the good youve been a part of!"; 
		return "Thank you! You and the receiver both just received +1 Good Point. Please go to ".$link." or text media to share your story. Keep up the Good Work!";
	}
	public static function promptForGiver($oldOwner){ return "Was this card given to you by ".$oldOwner." ?"; }
	public static function promptForRealGiver(){ return "Please enter the phone number of who gave you the card."; }
	public static function yesOrNo(){ return "yes or no?"; }
	public static function wrongPhoneFormat(){ return "You need to enter a valid phone number as xxxxxxxxxx with area code! Please try again. Please enter the phone number of who gave you the card. Thanks!"; }
	public static function transactionNewPrevOwnerSuccess($barcode_id, $link, $phone){ 
		//return "You have registered this card successfully! Please go to ".$link." and add media to this transaction! You can also text us some media (pic/video) to show the good youve been a part of! Thank you especially for giving credit where credit was due!";
		return "Thank you! You and the giver both just received +1 Good Point. Please go to ".$link." or text media to share your story. Keep up the Good Work! Thank you especially for giving credit where credit was due :)";
	}
	public static function didntSendMedia($t_id){ return "You didnt send us any media! :( If you want to send media for this transaction at another time, text your transaction ID: ".$t_id." with your image, audio, or video. Thank you!"; }
	public static function mediaFailed($t_id){ return "Error uploading one or more of the pictures/audio/video you sent :( If you want to add media to this transaction later, text transaction ID: ".$t_id." and your pictures/audio/video. Thanks!"; }
	public static function mediaSucceeded($t_id){ return "Thank you for adding media to share your goodness with the world! If you want to add more media for this transaction in the future, go to the site in the link above. Thanks!"; }
	public static function invalidCardId(){ return "Welcome to WIN+WIN card exchange! You entered an incorrect card ID. It is a 5 or 9-digit ID on the bottom of your card."; }
	public static function ownerToOwnerError(){ return "You are already the owner of this card, or have been the owner of this card in the last transaction. This means you cannot receive this card at this time. Spread the goodness and encourage sharing this card with someone else! Goodness cannot be hoarded!"; }//change to 3 after alpha

	//transaction
	public static function transactionWelcome($location, $gp, $location_items){
		$toReturn = "Welcome to ".$location."! Please select and text the item you would like to redeem with your Good Points. You have ".$gp." Good Points available to spend.";
		$counter = 1;
		foreach($location_items as $item){
			/*1. 1 slice of cheese or pepperoni pizza - 5GP
			2. Small Salad - 5 GP
			3. Premium slice - 10 GP";*/
			$toReturn .= "\n".$counter . ". ".$item["name"]." - ".$item["cost"]." GP";
			$counter++;
		}
		return $toReturn;
	}
}

?>
