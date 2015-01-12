<?php
	//determine A or B in ab testing
	$a_or_b = mt_rand(0,1);
	//0 = A, 1 = B
	$display = ($a_or_b == 0)? "A": "B";
?>
<!DOCTYPE html>
<html>
	<head>
		<!---->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
		<META HTTP-EQUIV="Expires" CONTENT="-1">
		<!---->
		<link href="../web/css/styles.css" type="text/css" />
		<link rel="stylesheet" href="../web/jqMobile/jquery.mobile-1.4.5.min.css" type="text/css" />
		<!---->
		<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
		<script src="../web/js/util.js"></script>
		<script src="../web/js/main.js"></script>
		<script>
			$(document).bind("mobileinit", function () {
				//alert("mobileinit");
				$.mobile.ajaxEnabled = false;
				$(document).ready(function(){
					//desired ajax onload functions here
					getOldOwner(<?php echo $_REQUEST['cid']; ?>);
				});
			});
		</script>
		<script src="../web/jqMobile/jquery.mobile-1.4.5.min.js"></script>
		<!---->
		<title>WIN+WIN</title>
	</head>
	<body><!-- onload="populateLeaderboard()"-->
		<div id="qrscan_page" data-role="page" data-theme="b">
			<div data-role="header">
				<center><span style="font-size:20px;font-weight:bold;">GoodPoint Registration</span></center> 
			</div>
			<div class="ui-content">
				<?php if($display == "A") { ?>
				<span class="a">
					<h2>1. Did <span id="oldOwner"></span> give you this card?</h2>
					<input type="hidden" name="oldOwner" value="" />
					<select name="yesorno">
						<option value="yes">yes</option>
						<option value="no">no</option>
					</select>
					<p>1b. If not, enter the phone number of who gave you this card! (If you don't know, ask! or, worst case, enter some other identifying info)</p>
					<input type="text" name="realgiver" style="width:80%;" /><br/>
				</span><!--A: a visible, B: a hidden-->
				<?php } ?>
				<h2>2.
				<?php if($display == "A") { ?><span class="a">Now, </span><?php } ?>
				Please Enter your Phone Number:</h2>
				<input type="text" name="phone" style="width:80%;" /><br/>
				<input type="hidden" name="aorb" value="<?php echo $display; ?>" />
				<input type="hidden" name="cardno" value="<?php echo $_REQUEST['cid']; ?>" />
				<input type="button" value="submit" onclick="submitQRscan()" />
				<p>(Note: This phone number will be used to start your GoodPoint account. Make sure you supply your real, correct phone number, because you may not be able to use your GoodPoints otherwise!)</p>
			</div>
			<div data-role="footer"><span style="font-size:10px;">&copy; 2014 GoodPointGame</span></div>
		</div>
	</body>
</html>