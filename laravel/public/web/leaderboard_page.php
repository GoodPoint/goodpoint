<?php
	//get sid if exists in request, and then populate view or claim based on user profile status
	$sid = isset($_REQUEST['sid'])? $_REQUEST['sid'] : "";
	if($sid != ""){
		$user = DB::select("SELECT `user`.* FROM `user` INNER JOIN messages ON `user`.id=messages.To WHERE messages.sid='".$sid."'")[0];
		$link_text = ($user->profile_json == "{}")? "CLAIM!":"VIEW";
		$link_href = "player_profile.php?uid=".$user->id;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<!---->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!---->
		<link href="css/styles.css" type="text/css" />
		<link rel="stylesheet" href="jqMobile/jquery.mobile-1.4.5.min.css" type="text/css" />
		<!---->
		<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
		<script src="jqMobile/jquery.mobile-1.4.5.min.js"></script>
		<!--script src="js/main.js"></script-->
		<!---->
		<title>WIN+WIN</title>
	</head>
	<body>
		<div id="leaderboard_page" data-role="page">
			<div data-role="header">
				<center><span style="font-size:20px;font-weight:bold;">GoodPoint Leaderboard</span></center> 
				<a id="back" style="float:right;display:none;" data-rel="back">Back</a>
			</div>
			<div class="ui-content">
				<!--leaderboard-->
				<ul id="the_leaderboard" data-role="listview">
					<?php echo $link_text; echo $link_href; ?>
				</ul>
			</div>
			<div data-role="footer"><span style="font-size:10px;">&copy; 2014 GoodPointGame</span></div>
		</div>
	</body>
</html>
