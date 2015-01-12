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
		<script>
			function sendPic() {
				$('progress').show();
				//var file = myInput.files[0];
				// Send file here either by adding it to a `FormData` object 
				// and sending that via XHR, or by simply passing the file into 
				// the `send` method of an XHR instance.
				var formData = new FormData($("#picForm")[0]);
				$.ajax({
					url: 'http://54.149.200.91/winwin/laravel/public/index.php/web/uploadMedia',  //Server script to process data
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
					success: sendPicS2,
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
				<div id="if_user" class="content-container" style="display:none;">
					<form id="picForm" enctype="multipart/form-data">
						Add Media and Memories to this WIN+WIN!: <input id="media_pic" name="media_pic" type="file" accept="image/*;capture=camera" />
						<input name="tid" type="hidden" value="" />
						<p>(Pictures only. Currently you can add video and audio only through texting -- after a successful transaction, just text us your memories!)</p>
					</form>
					<progress></progress>
				</div>
			</div>
			<div data-role="footer"><span style="font-size:10px;">&copy; 2014 GoodPointGame</span></div>
		</div>
	</body>
</html>
