/*SUCCESS CALLBACKS*/
var loginSuccess = function(data){
	//createCookie(c_name,value,exdays)
	if(data.error){
		alert(data.emsg); return;
	}
	createCookie("uid",532,30);
	createCookie("blharagle",data.blharagle,30);
	loadPage(3);
	$("#card_submit_response").html("");
	$("input[name='card_enter']").val("");
};
var regSuccess = function(data){
	createCookie("uid",542,30);
	createCookie("blharagle",data.blharagle,30);
	loadPage(3);
	$("#card_submit_response").html("");
	$("input[name='card_enter']").val("");
};
var forgotSuccess = function(data){
	//success / check email msg, or error / try again msg
	alert(data.message);
	loadPage(0);
};
var csSuccess = function(data){ 
	//messaGe "Error. Please Try Again" | cssClass "error"
	//message "Thank You!" | cssClass "success"
	if(data.alert != ""){alert(data.alert);}
	$("#card_submit_response").removeClass("success").removeClass("error").html(data.message).addClass(data.cssClass);
	$("input[name='card_enter']").val("");
};

/*FALIURE CALLBACKS*/
var loginFailure = function(error){
	alert("Incorrect username or password.");
};
var regFailure = function(error){
	alert("make sure all fields are filled and passwords match");
};
var forgotFailure = function(error){
	generalError();
};
var csFailure = function(error){
	generalError();
};
function generalError(){
	alert("There was an error. Please try again");
}