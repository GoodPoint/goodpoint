//functions
function populateLeaderboard(){
	var data = new Object();
	data.url = "/web/leaderboard";
	data.sid = getParameterByName("sid") || "";
	ajax("GET",data, populateLeaderboardS, generalF);
}
//success callbacks
var populateLeaderboardS = function(data){
	var UL_HTML = "";
	for(var i=0; i<data.leaderboard.length; i++){
		var da_span = (data.leaderboard[i].id == data.userID)? "<span class='profile_link' style='float:right;'>Edit Profile</span>" : "";
		UL_HTML += "<li>";
		UL_HTML += data.leaderboard[i].id+" -- "+data.leaderboard[i].GoodPoints+"GP";
		UL_HTML += da_span+"</li>";
	}
	$("ul#the_leaderboard").html(UL_HTML);
};
//failure callbacks
var generalF = function(data,err){alert("error in web application"+data+err);};
//binds
$("span.profile_link").click(function(){
	window.location.href = "player_profile.php?sid="+getParameterByName("sid");
});