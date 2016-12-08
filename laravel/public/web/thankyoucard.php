<?php
	function dieNoOrg(){die("no organization selected!");}
	
	if(!isset($_GET['org'])){dieNoOrg();}
	switch($_GET['org']){
		case "8e0ba":
			$org_name = "Blackhawk Middle School";
			break;
		default: 
			dieNoOrg();
			break;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?=$org_name?> Thank You Card Program</title>
		<script src="https://code.jquery.com/jquery-1.12.3.min.js"></script>
		<script src="js/util.js"></script>
		<script src="js/tyc_callbacks.js"></script>
		<link rel="stylesheet" href="tyc_styles.css" />
	</head>
	<body>
		<div class="big_page">
			<div id="header" class="page_header">
			<center>
				<h3><?=$org_name?> Thank You Card Campaign</h3>
				<p>a WIN+WIN Game</p>
			</center>
			</div>
			<!--LOGIN-->
			<div id="login" class='page_content page'>
				<h3>Login</h3>
				<input type='text' name='login_email' placeholder='Your Email' /><br/>
				<input type='password' name='login_pw' placeholder='Your Password' /><br/>
				<input type='button' name='login' value='Login!' />
				<p>
					<span class="regLink half">Register</span>
					<span class="forgotLink half">Forgot Password</span>
				</p>
			</div>
			<!--SIGNUP-->
			<div id="signup" class='page_content page'>
				<h3>Registration Instructions</h3>
				<p>(Use school email and your normal school password)</p>
				<input type='text' name='signup_email' placeholder='Your Email' /><br/>
				<input type='password' name='signup_pw' placeholder='Your Password' /><br/>
				<input type='password' name='signup_pw2' placeholder='Repeat Password' /><br/>
				<input type='button' name='signup' value='Register!' />
				<p>
					<span class="loginLink half">Login</span>
					<span class="forgotLink half">Forgot Password</span>
				</p>
			</div>
			<!--FORGOT-->
			<div id="forgot" class='page_content page'>
				<h3>I forgot my Password!</h3>
				<p>Enter your email address and we'll email you your password.</p>
				<input type='text' name='forgot_email' placeholder='Your Email' /><br/>
				<input type='button' name='forgot' value='Submit' />
				<p>
					<span class="loginLink half">Login</span>
					<span class="regLink half">Register</span>
				</p>
			</div>
		</div>
		<div class="big_page">
			<!-- POST-LOGIN 1: Enter points -->
			<div id="enter_points" class="page_content pl_page">
				<!--svg with input element and submit btn, and area for response text-->
				<img src="card.svg" id="card_svg" alt="Thank You Card" />
				<h3 class="mobile_only">Enter Thank You Card ID:</h3>
				<img src="card.png" id="card_png" alt="Thank You Card" />
				<input type='text' name='card_enter' placeholder='ENTER HERE' /><br/>
				<input type='button' name='card_submit' value='Submit' />
				<p id="card_submit_response"></p>
				<!--logout function clears cookie, takes to login, clears client-side userids-->
				<h4><a href="#" onclick="logout()">Logout when you are done.</a></h4>
			</div>
		</div>
		<input type='hidden' name='loggedin_user_id' value='' />
		<script>
			//PAGES: 0=login, 1=signup, 2=forgot, 3=enter_points
			var pages = ['login','signup','forgot','enter_points'];
			$(document).ready(function(){
				//link bindings
				$(".loginLink").click(function(){loadPage(0);});
				$(".regLink").click(function(){loadPage(1);});
				$(".forgotLink").click(function(){loadPage(2);});
				//button click bindings
				$("input[name='login']").click(function(){
					var data = new Object();
					data.url = "/tyc/login";
					data.org = getParameterByName("org");
					data.user_id = $("input[name='login_email']").val();
					data.pw = $("input[name='login_pw']").val();
					ajax("POST", data, loginSuccess, loginFailure);
				});
				$("input[name='signup']").click(function(){
					var data = new Object();
					data.url = "/tyc/signup";
					data.org = getParameterByName("org");
					data.user_id = $("input[name='signup_email']").val();
					data.pw = $("input[name='signup_pw']").val();
					if($("input[name='signup_pw']").val() != $("input[name='signup_pw2']").val()){
						alert("passwords must match!"); return;
					}
					ajax("POST", data, regSuccess, regFailure);
				});
				$("input[name='forgot']").click(function(){
					var data = new Object();
					data.url = "/tyc/forgot";
					data.email = $("input[name='forgot_email']").val();
					ajax("POST", data, forgotSuccess, forgotFailure);
				});
				$("input[name='card_submit']").click(function(){
					var data = new Object();
					data.url = "/tyc/submitCardID";
					data.uid = readCookie("blharagle");
					data.cardno = $("input[name='card_enter']").val();
					ajax("POST", data, csSuccess, csFailure);
				});
				//actions to take right away
				loadPage(0);
			});
			function loadPage(x){
				$(".page").hide(); $(".pl_page").hide();
				if($("#"+pages[x]).length > 0){ //show specified page
					if(x>=3 && readCookie("blharagle") == null){
						alert("not authorized to view this page");
						$("#"+pages[0]).show(); return;
					}
					$("#"+pages[x]).show(); return;
				} else { //show login page if invalid page called.
					$("#"+pages[0]).show(); return;
				}
			}
			function logout(){
				eraseCookie("blharagle");
				loadPage(0);
			}
		</script>
	</body>
</html>
