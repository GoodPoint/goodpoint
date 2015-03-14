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
					populateLeaderboard();
					var link_sid = (getParameterByName("sid") == "null" || getParameterByName("sid") == undefined)? "" : getParameterByName("sid");
					$(".transaction_list_link").attr("href","transaction_list.php?sid="+link_sid);
					$("div#logo").click(function(){
						window.location.href = "leaderboard_page.php?sid="+link_sid;
					});
					if(getParameterByName("first") == "true"){
						var myInput = document.getElementById('profile_pic');
						myInput.addEventListener('change', sendPic, false);
						$("input[name='sid']").val(getParameterByName("sid"));
						$('progress').hide();
						// open the popup
						//$("#Edit").popup();
						//$("#Edit").popup("open");
						setTimeout(function(){$("#Edit").popup();$("#Edit").popup("open");},3000);
					}
				});
			});
		</script>
		<script src="jqMobile/jquery.mobile-1.4.5.min.js"></script>
		<script>
			function sendPic() {
				$('progress').show();
				//var file = myInput.files[0];
				// Send file here either by adding it to a `FormData` object 
				// and sending that via XHR, or by simply passing the file into 
				// the `send` method of an XHR instance.
				var formData = new FormData($("#picForm")[0]);
				$.ajax({
					url: 'http://54.149.200.91/winwin/laravel/public/index.php/web/uploadFirstMedia',  //Server script to process data
					type: 'POST',
					xhr: function() {  // Custom XMLHttpRequest
						var myXhr = $.ajaxSettings.xhr();
						if(myXhr.upload){ // Check if upload property exists
							myXhr.upload.addEventListener('progress',progressHandlingFunction, false); // For handling the progress of the upload
						}
						return myXhr;
					},
					//Ajax events
					//beforeSend: beforeSendHandler,
					success: sendPicS3,
					error: generalF,
					// Form data
					data: formData,
					//Options to tell jQuery not to process data or worry about content-type.
					cache: false,
					contentType: false,
					processData: false
				});
			}
			function progressHandlingFunction(e){
				if(e.lengthComputable){
					$('progress').attr({value:e.loaded,max:e.total});
				}
			}
		</script>
		<!---->
		<title>WIN+WIN</title>
	</head>
	<body><!-- onload="populateLeaderboard()"-->
		<div id="leaderboard_page" data-role="page" data-theme="b">
			<div data-role="header">
				<center><span><div id="logo"></div></span></center> 
				<a id="back" style="float:right;display:none;" data-rel="back">Back</a>
			</div>
			<div data-role="navbar">
				<ul>
					<li><a href="#" class="ui-btn-active" style="padding-top:13px;padding-bottom:14px;">Leaderboard</a></li>
					<li><a class="transaction_list_link"><!--Transactions--><span class="plusone"></span>/<span class="plusone"></span>'s</a></li>
				</ul>
			</div>
			<div class="ui-content">
				<!--leaderboard-->
				<ul id="the_leaderboard" data-role="listview">
					
				</ul>
			</div>
			<div data-role="footer"><span style="font-size:10px;">&copy; 2014 GoodPointGame</span></div>
			<!--popup-->
			<a href="#Edit" id="editLink" data-rel="popup" data-position-to="window" style="display:none;">Edit</a>
			<div data-role="popup" id="Edit">
				<a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" id="da_close" class="ui-btn-right">Close</a>
				<center>
					<h2>Upload Media to share the Good Story!</h2>
					<form id="picForm" enctype="multipart/form-data">
						Caption: <input type="text" name="caption" placeholder="caption" /><br/>
						<input id="profile_pic" name="profile_pic" type="file" accept="image/*;capture=camera" />
						<p>(Pictures only through this form. Currently you can add video and audio only through texting -- after a successful transaction, just text us your memories!)</p>
						<input name="sid" type="hidden" value="" />
					</form>
					<progress></progress>
				</center>
			</div>
		</div>
	</body>
</html>
