<?php
	use GuzzleHttp\Client;
	class GAPI {	
		public static function urlShorten($url){
			/*API KEY: AIzaSyCAopWJPUluIim8KiwYnAsxeueQctV_Odk
			**REQUEST INFO: 
			**POST https://www.googleapis.com/urlshortener/v1/url?key=API_KEY
			**Content-Type: application/json
			**{"longUrl": "http://www.google.com/"}*/
			//DEBUG URL: $url = "http://flow-enterprises.com";
			/*GOOGLE DOESNT WORK:
			$url_googleUrlShortener = "https://www.googleapis.com/urlshortener/v1/url?key=AIzaSyCAopWJPUluIim8KiwYnAsxeueQctV_Odk";
			$client = new Client();
			$r = $client->post($url_googleUrlShortener, 
								[
								'json' => ['longUrl' => $url],
								'headers' => ['Content-Type' => 'application/json']
								]
							  );
			return $r->json()["id"];
			*/
			$uri = "http://po.st/api/shorten";
			$querystring = "?longURL=http%3A%2F%2F54.149.200.91%2Fwinwin%2Flaravel%2Fpublic%2Fweb%2Fleaderboard_page.php%3Fsid%3DSMccc7f717c1873b1edbb0490ff3f4a35f&apiKey=D638C739-28D4-48B5-9A1F-ECE285DB5B88";
			$client = new Client();
			$r = $client->get($uri.$querystring);
			return $r->json()["short_url"];
		}
	}
?>