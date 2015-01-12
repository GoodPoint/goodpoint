<!DOCTYPE html>
<html>
	<head>
		<!---->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
		<META HTTP-EQUIV="Expires" CONTENT="-1">
		<!---->
		<link href="css/styles.css" type="text/css" />
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
					populateLeaderboard();
					$(".transaction_list_link").attr("href","transaction_list.php?sid="+getParameterByName("sid"));
				});
			});
		</script>
		<script src="jqMobile/jquery.mobile-1.4.5.min.js"></script>
		<!---->
		<title>WIN+WIN</title>
	</head>
	<body><!-- onload="populateLeaderboard()"-->
		<div id="leaderboard_page" data-role="page" data-theme="b">
			<div data-role="header">
				<center><span><div id="logo" /></span></center> 
				<a id="back" style="float:right;display:none;" data-rel="back">Back</a>
			</div>
			<div data-role="navbar">
				<ul>
					<li><a href="#" class="ui-btn-active">Leaderboard</a></li>
					<li><a class="transaction_list_link"><!--Transactions-->+1/+1's</a></li>
				</ul>
			</div>
			<div class="ui-content">
				<!--leaderboard-->
				<ul id="the_leaderboard" data-role="listview">
					
				</ul>
			</div>
			<div data-role="footer"><span style="font-size:10px;">&copy; 2014 GoodPointGame</span></div>
		</div>
	</body>
</html>
