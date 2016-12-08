//ajax
//var baseUrl = "http://54.149.200.91/winwin/laravel/public/index.php";
var baseUrl = "http://www.goodpointgame.com/winwin/laravel/public/index.php";
function ajax(type, dataObj, successCallback, errorCallback)
{
	$.ajax({
		type: type,
		url: baseUrl+dataObj.url,
		data: $.param(dataObj),
		async: true,
		dataType: "json",
		success: successCallback,
		error: errorCallback
	});
}
//querystring parsing
function getParameterByName(name) {
    var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
    return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
}

function createCookie(c_name,value,exdays)
{
	var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	document.cookie=c_name + "=" + c_value;
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function eraseCookie(name)
{
    document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}
