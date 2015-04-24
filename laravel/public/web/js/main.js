//functions
function populateLeaderboard(){
	var data = new Object();
	data.url = "/web/leaderboard";
	data.sid = getParameterByName("sid") || "";
	data.event = getParameterByName("event") || "";
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
function getTransactionsByNum(){
	if($("input[name='cardOrPhone']:checked").val() == "Card"){
		getTransactionsByCard(); $("#mine_link").removeClass("ui-btn-active"); $("#hundo_link").removeClass("ui-btn-active"); return;
	} else {
		if($("input[name='cardOrPhone']:checked").val() == "Phone"){
			getTransactionsByPhone(); $("#mine_link").removeClass("ui-btn-active"); $("#hundo_link").removeClass("ui-btn-active"); return;
		} else {
			alert("must specify what type of id to search by! card id is the text code on back of card that you want to see transactions for. phone# is the phone# of the person you want to see transactions for");
		}
	}
}
function getTransactionsByCard(){
	var data = new Object();
	data.url = "/web/transactions/card";
	data.id = $("input[name='number']").val();
	data.type = "Card";
	ajax("GET",data,getTransS,searchTransFail);
}
function getTransactionsByPhone(){
	var data = new Object();
	data.url = "/web/transactions/phone";
	data.id = $("input[name='number']").val();
	data.type = "Phone";
	ajax("GET",data,getTransS,searchTransFail);
}
function populateTransactionInfo(id){
	var data = new Object();
	data.url = "/web/transaction/details";
	data.id = id;
	data.sid = getParameterByName("sid") || "";
	ajax("GET",data,populateTransS,generalF);
}
function getOldOwner(cid){
	var data = new Object();
	data.url = "/qr/ownerForCard";
	data.cid = cid;
	ajax("GET",data,getOldOwnerS,generalF);
}
function submitQRscan(){
	if($("input[name='phone']").val() == "" || $("input[name='phone']").val().toString().length != 10){
		alert("must enter a 10-digit phone number in the format xxxxxxxxxx");return;
	}
	var data = new Object();
	data.url = "/qr/submit";
	data.oldOwner = $("input[name='oldOwner']").val();
	data.yesorno = $("select[name='yesorno']").val();
	data.realgiver = $("input[name='realgiver']").val();
	data.phone = $("input[name='phone']").val();
	data.aorb = $("input[name='aorb']").val();
	data.cardno = $("input[name='cardno']").val();
	ajax("GET",data,submitQRscanS,generalF);
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
var submitQRscanS = function(data){
	window.location.href = data.link;
};
var getOldOwnerS = function(data){
	$("#oldOwner").html(data.owner);
	$("input[name='oldOwner']").val(data.owner);
};
var populateTransS = function(data){
	var transaction = data.transaction[0];
	var mediaURL = data.media;
	
	var UL_HTML = "";
	UL_HTML += "<center><h2>";
	UL_HTML += "Card "+transaction.cardid+" Was Given ";
	UL_HTML += "</h2>";//From "+transaction.giver+" To "+transaction.receiver;
	UL_HTML += "<table style='width:100%;'><tr style='width:100%;'>";
	UL_HTML += "<td style='width:50%;'><center><h3>From<br/>"+data.n1+"</h3></center></td>";//transaction.giver
	UL_HTML += "<td style='width:50%;'><center><h3>To<br/>"+data.n2+"</h3></center></td>";//transaction.receiver
	UL_HTML += "</tr><tr style='width:100%;'>";
	UL_HTML += "<td style='width:50%;'><center><span class='bigplusone'/></center></td>";
	UL_HTML += "<td style='width:50%;'><center><span class='bigplusone'/></center></td>";
	UL_HTML += "</tr></table>";
	UL_HTML += "<h3>On "+transaction.timestamp+"</h3></center>";
	UL_HTML += "<hr><br/>";
	
	if(data.media.length>0){
		UL_HTML += "<h2>Memories for this WIN+WIN!</h2>";
		for(var i=0;i<data.media.length;i++){
			var url = data.media[i].url; 
			var caption = (data.media[i].caption == "")? "" : "<p>"+data.media[i].caption+"</p>";
			var img = "<img src='"+url+"' style='max-width:80%;height:auto;' />"+caption+"<br/><hr><br/>";
			UL_HTML += img;
		}
	} else {
		UL_HTML += "<h2>No Memories uploaded for this WIN+WIN! Convince the participants to share their good with the world!</h2>";
	}
	
	$("#transaction_details").html(UL_HTML);
	
	//show media upload if can edit
	if(data.can_edit){
		$("#if_user").show();
		$("progress").hide();
		$("input[name='tid']").val(transaction.id);
	}
	
};
var getTransS = function(data){
	var UL_HTML = "";
	for(var i=0; i<data.length; i++){
		var transaction = data[i];
		UL_HTML += "<li><a href='transaction.php?id="+transaction.id+"&sid="+getParameterByName("sid")+"'>";
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
var sendPicS2 = function(data){
	alert("Successfully added media to this transaction! Thank you for sharing the good story and the good memories! Spread the good!");
	$('progress').hide();
	populateTransactionInfo(getParameterByName("id"));
}
var sendPicS3 = function(data){
	alert("Successfully added media to this transaction! Thank you for sharing the good story and the good memories! Spread the good!");
	//alert(JSON.stringify(data));
	$('progress').hide();
	$('#da_close').click();
}
var populateLeaderboardS = function(data){
	var UL_HTML = "";
	for(var i=0; i<data.leaderboard.length; i++){
		if(data.leaderboard[i].id != "" && data.leaderboard[i].id != " " && data.leaderboard[i].id != "GoodPoint Launch" && data.leaderboard[i].id.toString().length == "10"){
			var da_span = (data.leaderboard[i].id == data.userID)? "<span class='profile_link' style='float:right;cursor:pointer;'>Edit Profile</span>" : "";
			var da_href = (data.leaderboard[i].id == data.userID)? "player_profile.php?sid="+getParameterByName("sid") : "#";
			UL_HTML += "<li><a href='"+da_href+"'>";
			//data.leaderboard[i].id
			var profile = JSON.parse(data.leaderboard[i].profile_json);
			var display_name = (profile.name == undefined)? "Anonymous" : profile.name;
			UL_HTML += display_name+" -- "+data.leaderboard[i].GoodPoints+"GP";
			UL_HTML += da_span+"</a></li>";
		}
	}
	//populate UL with concatenated string
	$("ul#the_leaderboard").html(UL_HTML);
	$("ul#the_leaderboard").listview("refresh");
	//bind close btn
	$('#da_close').click(function(){
		$('html, body').animate({
			scrollTop: $(".profile_link").offset().top - 20
		}, 2000);
	});
	//bind for profile link not needed since added logic to manipulate actual anchor tag
	/*$("span.profile_link").click(function(){
		window.location.href = "player_profile.php?sid="+getParameterByName("sid");
	});*/
};
//failure callbacks
var generalF = function(data,err){alert("error in web application"+data+err);};
var searchTransFail = function(data,err){
	alert("no results found -- you need to type in a number in the search field!");
}
//static variables
var genderArr = ['Female','Male','Other'];