//ajax
var baseUrl = "http://54.149.200.91/winwin/laravel/public/index.php";
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