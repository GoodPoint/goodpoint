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
		<script src="js/util.js"></script>
		<script src="js/main.js"></script>
		<script>
			//image stuff
			var myInput = document.getElementById('profile_pic');
			function sendPic() {
				var file = myInput.files[0];
				// Send file here either by adding it to a `FormData` object 
				// and sending that via XHR, or by simply passing the file into 
				// the `send` method of an XHR instance.
				var formData = new FormData($("#picForm")[0]);
				$.ajax({
					url: 'http://54.149.200.91/winwin/laravel/public/index.php/web/uploadProfilePic',  //Server script to process data
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
					success: sendPicS,
					error: generalF,
					// Form data
					data: formData,
					//Options to tell jQuery not to process data or worry about content-type.
					cache: false,
					contentType: false,
					processData: false
				});
			}
			myInput.addEventListener('change', sendPic, false);
			function progressHandlingFunction(e){
				if(e.lengthComputable){
					$('progress').attr({value:e.loaded,max:e.total});
				}
			}
			//gender dropdown
			for(var i=0; i<genderArr.length; i++){
				$("select[name='gender']").append("<option value='"+genderArr[i]+"'>"+genderArr[i]+"</option>);
			}
		</script>
		<!---->
		<title>WIN+WIN</title>
	</head>
	<body onload="populateProfile()">
		<div id="profile_page" data-role="page" data-theme="b">
			<div data-role="header">
				<center><span style="font-size:20px;font-weight:bold;">Your Profile Page</span></center> 
				<div id="back-btn-wrap">
					<a id="backURL" href="leaderboard_page.php"><input style="display:inline" type="button" value="Back"></a>
				</div>
			</div>
			<div class="ui-content">
				<div id="userProfile" class="content-container">
					Profile Picture: <img id="profilePic" style="width:100px;height:100px;" /><br/>
					Name: <span class="name"></span><br/>
					Age: <span class="age"></span><br/>
					Gender: <span class="gender"></span><br/>
					Phone: <span class="phone"></span><br/>
					<a href="#Edit" id="editLink" data-rel="popup" data-position-to="window">Edit</a>
				</div>
			</div>
			<div data-role="footer"><span style="font-size:10px;">&copy; 2014 GoodPointGame</span></div>
			<div data-role="popup" id="Edit">
				<center>
					<h2>Edit Profile:</h2>
					<form id="picForm" enctype="multipart/form-data">
						Upload New Profile Photo: <input id="profile_pic" name="profile_pic" type="file" accept="image/*;capture=camera" />
					</form>
					<progress></progress>
					<br/>
					<span class="only_if_new">
					<h2>Hey, first-timer! Initialize Your Profile (all fields optional):</h2>
						Name: <input type="text" name="name" />
						Age: <input type="number" name="age" min="18" max="120" />
						Gender: <select name="gender"></select>
						<input type="button" value="Add Profile" onclick="addProfile()" />
					</span>
					
				</center>
			</div>
		</div>
	</body>
</html>
