<?php
    header("content-type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $body = $_REQUEST['Body'];
    $mysqli = new mysqli('localhost','root','r00t','winwin');
    $step = -1;$barcode_id = -1;$old_owner = -1;$the_sid = -1;
    if(intval($body) == 0 || strlen($body) != 9){ //need to validate body as phone # in more strict way, find regex
	$select = "SELECT * FROM `messages` WHERE `To`='".$_REQUEST['From']."' ORDER BY time desc";
	$selResult = $mysqli->query($select);
	while($row = $selResult->fetch_assoc()){
		if($step == -1){$step = $row['step'];}
		if($barcode_id == -1){$barcode_id = $row['cardid'];}
		if($the_sid == -1){$the_sid = $row['sid'];}
		if($step != -1){
			$old_owner_arr = explode(" ",$row['msg']);
			$index = count($old_owner_arr) - 1;//1 before end
			$old_owner = $old_owner_arr[$index];
		}
	}
	switch($step){
	    case -1: 
		$message = "Welcome to WIN+WIN card exchange! You must enter in the 9-digit ID on your newly obtained card to register it as yours!";	
    		$step = -1; break;
	    case 0:
		if($_REQUEST['Body'] == "Yes"){
		    $query = "UPDATE cards SET owner = '".$_REQUEST['From']."' WHERE barcode_id = ".$barcode_id;
		    $result = $mysqli->query($query);
                    if(!$result){
			$message = "Database error, contact WIN+WIN admin";
			$step = -1;
		    } else {
			$query = "INSERT INTO transaction (cardid,receiver,giver) VALUES ('".$barcode_id."','".$_REQUEST['From']."','".$old_owner."')";
			$result = $mysqli->query($query);
			if(!$result){
				$message = "Database error, contact WIN+WIN admin";
				$step = -1;
			} else {
		    	    $message = "Awesome! You are now the owner of card ".$barcode_id.". Please submit some media to show the good you\'ve been a part of!";
		            $step = 2;
			} 
		    }
		} 
		if($_REQUEST['Body'] == "No"){
			$message = "Please enter the phone number of who gave you the card.";
			$step = 1;
		}
		if($_REQUEST['Body'] != "Yes" && $_REQUEST['Body'] != "No"){
			$message = "yes or no?"; $step = 0;
		}
		break;
	    case 1:
		//check phone number
		if(intval($body) == 0 || strlen($body)!= 10){ //need to validate phone number in more strict way, find regex
		    $message = "You need to enter a valid phone number as xxxxxxxxxx with area code! Please try again. Please enter the phone number of who gave you the card. Thanks!";
		    $step = 1;
		} else {
		    $query = "UPDATE cards SET owner = ".$_REQUEST['From']." WHERE barcode_id=".$barcode_id;
   		    $result = $mysqli->query($query);
		    if(!$result){
			$message = "Database error, please contact WIN+WIN admin";			
			$step = -1;
		    } else {
			$query = "INSERT INTO transaction (cardid,receiver,giver) VALUES ('".$barcode_id."','".$_REQUEST['From']."','".$body."')";
		        $result = $mysqli->query($query);
			if(!$result){
			    $message = "Database error, please contact WIN+WIN admin";
			    $step = -1;
			} else {
			    $message = "Thank you for giving credit where credit is due. You are now the owner of card ".$barcode_id.". Please submit some media to show the good you\'ve been a part of!";
			    $step = 2;
			}
		    }
		}
		break;
	    case 2:
		//grab newest transaction id for $barcode_id to link media to
		$query = "SELECT * FROM transaction WHERE cardid=".$barcode_id." ORDER BY timestamp desc";
		$result = $mysqli->query($query);$t_id=-1;
		while($row = $result->fetch_assoc()){
			if($t_id==-1){$t_id = $row['id'];}
		}
		//fetch media and store in db
		if($_REQUEST['NumMedia'] == 0){
		    $message = "You didnt send us any media! :( If you want to send media for this transaction at another time, text your transaction ID: ".$t_id." with your image, audio, or video. Thank you!";
		}
		else {
		    $failed = false;
		    //sid | trans_id | url
		    //single or multi insert is apparently the same despite Twilio docs -_-
  		    for($i=0;$i<$_REQUEST['NumMedia'];$i++){
   		        $query = "INSERT INTO media (sid, trans_id, url) VALUES ('".$the_sid."','".$t_id."','".$_REQUEST['MediaUrl'.$i]."')";
		        $result = $mysqli->query($query);
			if(!$result){
			    $failed = true;
			    $message = "Error uploading one or more of the pictures/audio/video you sent :( If you want to add media to this transaction later, text transaction ID: ".$t_id." and your pictures/audio/video. Thanks!";
			}
		    }
		    if(!$failed){
			$message = "Thank you for adding media to share your goodness with the world! If you want to add more media for this transaction in the future, text transaction ID: ".$t_id." and your pictures/audio/video. Thanks!";
		    }
		}
		$step = -1;//reset workflow for future transactions @ this no
		break;
	}
        $query2 = "INSERT INTO `messages` (`To`,`msg`,`step`,`cardid`,`sid`) VALUES ('".$_REQUEST['From']."','".$message."','".$step."','".$barcode_id."','".$_REQUEST['MessageSid']."')";	
	$result = $mysqli->query($query2);
    } else {
	//get card id
	$query = "SELECT * FROM cards WHERE barcode_id = ".$body;
	$result = $mysqli->query($query);
	if(count($result->fetch_assoc()) == 0){
	//if(!$result){
	    $message = "Welcome to WIN+WIN card exchange! You entered an incorrect card ID. It is a x-digit ID on the card.";
	    $step = -1;
	} else {
	    $result->data_seek(0);
	    while($row = $result->fetch_assoc()){
		$oldOwner = $row['owner'];
	    }
	    $message = "Was this card given to you by ".$oldOwner." ?";
	    $step = 0;
	    $barcode_id = $body;
	}
        $query2 = "INSERT INTO `messages` (`To`,`msg`,`step`,`cardid`,`sid`) VALUES ('".$_REQUEST['From']."','".$message."','".$step."','".$barcode_id."','".$_REQUEST['MessageSid']."')";	
        $result = $mysqli->query($query2);
    }
    //echo var_dump($result);
?>
<Response>
    <Message>
	<?php echo $message;/*.$query2.var_dump($result);*/ ?>
    </Message>
</Response>
