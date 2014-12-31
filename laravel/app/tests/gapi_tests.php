<?php
	require '../../vendor/autoload.php';
	class GAPI {	
		use GuzzleHttp\Client;
		public static function urlShorten($url){
			/*API KEY: AIzaSyCAopWJPUluIim8KiwYnAsxeueQctV_Odk
			**REQUEST INFO: 
			**POST https://www.googleapis.com/urlshortener/v1/url?key=API_KEY
			**Content-Type: application/json
			**{"longUrl": "http://www.google.com/"}*/
			//$url = "http://flow-enterprises.com";
			$url_googleUrlShortener = "https://www.googleapis.com/urlshortener/v1/url?key=AIzaSyCAopWJPUluIim8KiwYnAsxeueQctV_Odk";
			$client = new Client();
			$r = $client->post($url_googleUrlShortener, 
								[
								'json' => ['longUrl' => $url],
								'headers' => ['Content-Type' => 'application/json']
								//FOR WINDOWS: 'verify' => 'C:/wamp/crt/ca-bundle.crt'
								]
							  );
			//return $r->json();
			echo json_encode($r->json());
		}
	}
	GAPI::urlShorten("http://flow-enterprises.com/");
?>