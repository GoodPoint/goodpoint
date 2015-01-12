<!DOCTYPE html>
<html>
	<head>
		<!---->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
		<META HTTP-EQUIV="Expires" CONTENT="-1">
		<!---->
		<link rel="stylesheet" href="css/styles.css" type="text/css" />
		<link rel="stylesheet" href="jqMobile/jquery.mobile-1.4.5.min.css" type="text/css" />
		<!---->
		<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
		<script src="js/util.js"></script>
		<script src="js/main.js"></script>
		<script>
			$(document).bind("mobileinit", function () {
				//alert("mobileinit");
				$.mobile.ajaxEnabled = false;
				$(document).ready(function(){
					populateTransactionInfo(getParameterByName("id"));
					$("#backURL").attr("href","transaction_list.php?sid="+getParameterByName("sid"));
				});
			});
		</script>
		<script src="jqMobile/jquery.mobile-1.4.5.min.js"></script>
		<!---->
		<title>WIN+WIN</title>
	</head>
	<body><!-- onload="populateLeaderboard()"-->
		<div id="t_page" data-role="page" data-theme="b">
			<div data-role="header">
				<div id="back-btn-wrap" style="position:relative;top:10px;">
					<a id="backURL" href="leaderboard_page.php"><input style="display:inline" type="button" value="Back"></a>
				</div>
				<center><span style="position:relative;bottom:25px;"><div id="logo"></div></span></center> 
				<a id="back" style="float:right;display:none;" data-rel="back">Back</a>
			</div>
			<div class="ui-content">
				<!--transaction-->
				<div id="transaction_details" class="content-container">
				
				</div>
			</div>
			<div data-role="footer"><span style="font-size:10px;">&copy; 2014 GoodPointGame</span></div>
		</div>
	</body>
</html>
