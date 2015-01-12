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
					getLatestTransactions();
					$(".leaderboard_page_link").attr("href","leaderboard_page.php?sid="+getParameterByName("sid"));
				});
			});
		</script>
		<script src="jqMobile/jquery.mobile-1.4.5.min.js"></script>
		<!---->
		<title>WIN+WIN</title>
	</head>
	<body><!-- onload="populateLeaderboard()"-->
		<div id="tlist_page" data-role="page" data-theme="b">
			<div data-role="header">
				<center><span><div id="logo"></div></span></center> 
				<a id="back" style="float:right;display:none;" data-rel="back">Back</a>
			</div>
			<div data-role="navbar">
				<ul>
					<li><a class="leaderboard_page_link">Leaderboard</a></li>
					<li><a href="#" class="ui-btn-active"><!--Transactions--><span class="plusone"/>/<span class="plusone"/>'s</a></li>
				</ul>
			</div>
			<br/>
			<div data-role="navbar">
				<center><h2>View GoodPoint Transactions!</h2></center>
				<ul>
					<li id="my_transactions"><a href="#" id="mine_link" onclick="getMyTransactions()">Mine</a></li>
					<li><a href="#" id="hundo_link" onclick="getLatestTransactions()" class="ui-btn-active">Latest 100</a></li>
					<!--li><a href="#" onclick="getTransactionsByCard()">By Card# </a></li>
					<li><a href="#" onclick="getTransactionsByPhone()">By Phone# </a></li-->
				</ul><p>OR search by</p>
				<input type="radio" name="cardOrPhone" value="Card" style="display:inline;" />
				<span style="position:relative;left: 35px;bottom: 5px;">Card ID</span>
				&nbsp;&nbsp;<input type="radio" name="cardOrPhone" value="Phone" style="display:inline;" />
				<span style="position:relative;left: 35px;bottom: 5px;">Phone #</span><br/>
				<input type="text" placeholder="card# / phone#" name="number" style="float:left;width:80%;" />
				<input type="button" value="Go" style="float:left;" onclick="getTransactionsByNum()" />
			</div>
			<div class="ui-content">
				<!--leaderboard-->
				<ul id="transaction_list" data-role="listview">
					
				</ul>
			</div>
			<div data-role="footer"><span style="font-size:10px;">&copy; 2014 GoodPointGame</span></div>
		</div>
	</body>
</html>