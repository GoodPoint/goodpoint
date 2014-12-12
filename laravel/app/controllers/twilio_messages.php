<?php

class TwilioMsg {

	//full messages
	public static function welcomeMessage() { return "Welcome to WIN+WIN card exchange! You must enter in the 9-digit ID on your newly obtained card to register it as yours!"; }
	public static function genericDatabaseError(){ return "Database error, contact WIN+WIN admin"; }
	public static function transactionSuccessful(){ return "Awesome! You are now the owner of card ".$barcode_id.". Please submit some media to show the good youve been a part of! ".TwilioMsg::transactionAddendum(); }
	public static function promptForGiver(){ return "Was this card given to you by ".$oldOwner." ?"; }
	public static function promptForRealGiver(){ return "Please enter the phone number of who gave you the card."; }
	public static function yesOrNo(){ return "yes or no?"; }
	public static function wrongPhoneFormat(){ return "You need to enter a valid phone number as xxxxxxxxxx with area code! Please try again. Please enter the phone number of who gave you the card. Thanks!"; }
	public static function transactionNewPrevOwnerSuccess(){ return "Thank you for giving credit where credit is due. You are now the owner of card ".$barcode_id.". Please submit some media to show the good youve been a part of! ".TwilioMsg::transactionAddendum(); }
	public static function didntSendMedia(){ return "You didnt send us any media! :( If you want to send media for this transaction at another time, text your transaction ID: ".$t_id." with your image, audio, or video. Thank you!"; }
	public static function mediaFailed(){ return "Error uploading one or more of the pictures/audio/video you sent :( If you want to add media to this transaction later, text transaction ID: ".$t_id." and your pictures/audio/video. Thanks!"; }
	public static function mediaSucceeded(){ return "Thank you for adding media to share your goodness with the world! If you want to add more media for this transaction in the future, text transaction ID: ".$t_id." and your pictures/audio/video. Thanks!"; }
	public static function invalidCardId(){ return "Welcome to WIN+WIN card exchange! You entered an incorrect card ID. It is a 9-digit ID on the card."; }
	
	//helpers - i.e. pieces of messages that are part of more than one message
	public static function transactionAddendum() { return "You can also go to ".$link." and claim ".$_REQUEST['From']."s points, and add media to this transaction!"; }
}

?>