//functions
function populateLeaderboard(){
	var data = new Object();
	data.url = "/web/leaderboard";
	data.sid = getParameterByName("sid") || "";
	ajax("GET",data, populateLeaderboardS, generalF);
}
function populateProfile(){
	var data = new Object();
	data.url = "/web/profile";
	data.sid = getParameterByName("sid") || "";
	ajax("GET",data, populateProfileS, generalF);
}
function addProfile(){
	var data = new Object();
	data.url = "/web/profile/add";
	data.sid = getParameterByName("sid") || "";
	data.name = $("input[name='name']").val() || "n/a";
	data.age = $("input[name='age']").val() || "n/a";
	data.gender = $("select[name='gender']").val() || "n/a";
	ajax("POST",data,addProfileS,generalF);
}
function getMyTransactions(){
	var data = new Object();
	data.url = "/web/transactions/mine";
	data.sid = getParameterByName("sid");
	ajax("GET",data,getTransS,generalF);
}
function getLatestTransactions(){
	var data = new Object();
	data.url = "/web/transactions/latest";
	ajax("GET",data,getTransS,generalF);
}
function getTransactionsByCard(){
	var data = new Object();
	data.url = "/web/transactions/card";
	data.id = $("input[name='number']").val();
	data.type = "Card";
	ajax("GET",data,getTransS,generalF);
}
function getTransactionsByPhone(){
	var data = new Object();
	data.url = "/web/transactions/phone";
	data.id = $("input[name='number']").val();
	data.type = "Phone";
	ajax("GET",data,getTransS,generalF);
}
//success callbacks
var populateProfileS = function(data){
	var profile = data.profile;
	$("span.name").html(profile.name || "not entered");
	$("span.age").html(profile.age || "not entered");
	$("span.gender").html(profile.gender || "not entered");
	$("span.phone").html(data.userID || "not entered");
	$("#profilePic").attr("src","http://54.149.200.91/winwin/laravel/public/web/uploads/"+profile.pic || "http://54.149.200.91/winwin/laravel/public/web/uploads/nophoto.png");
	if(profile.name == undefined && profile.age == undefined && profile.gender == undefined){
		$("span.only_if_new").show();
	} else {
		$("span.only_if_new").hide();
	}
	if(profile.name == undefined){$("#nameWrap").hide();}
	if(profile.age == undefined){$("#ageWrap").hide();}
	if(profile.gender == undefined){$("#genderWrap").hide();}
};
var getTransS = function(data){
	var UL_HTML = "";
	for(var i=0; i<data.length; i++){
		var transaction = data[i];
		UL_HTML += "<li><a href='transaction.php?id="+transaction.id+"'";
		UL_HTML += "Card "+transaction.cardid+" From "+transaction.giver+" To "+transaction.receiver;
		UL_HTML += "</a></li>";
	}
	//populate UL with concatenated string
	$("ul#transaction_list").html(UL_HTML);
	$("ul#transaction_list").listview("refresh");
};
var addProfileS = function(data){
	alert("successfully added profile info!");
	populateProfile();
}
var sendPicS = function(data){
	alert("successfully updated profile picture!");
	$('progress').hide();
	populateProfile();
}
var populateLeaderboardS = function(data){
	var UL_HTML = "";
	for(var i=0; i<data.leaderboard.length; i++){
		var da_span = (data.leaderboard[i].id == data.userID)? "<span class='profile_link' style='float:right;cursor:pointer;'>Edit Profile</span>" : "";
		var da_href = (data.leaderboard[i].id == data.userID)? "player_profile.php?sid="+getParameterByName("sid") : "#";
		UL_HTML += "<li><a href='"+da_href+"'>";
		UL_HTML += data.leaderboard[i].id+" -- "+data.leaderboard[i].GoodPoints+"GP";
		UL_HTML += da_span+"</a></li>";
	}
	//populate UL with concatenated string
	$("ul#the_leaderboard").html(UL_HTML);
	$("ul#the_leaderboard").listview("refresh");
	//bind for profile link not needed since added logic to manipulate actual anchor tag
	/*$("span.profile_link").click(function(){
		window.location.href = "player_profile.php?sid="+getParameterByName("sid");
	});*/
};
//failure callbacks
var generalF = function(data,err){alert("error in web application"+data+err);};
//static variables
var genderArr = ['Female','Male','Trans Female','Trans Male','Other'];