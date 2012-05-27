<?php
/*
 * This class only contains a little functionality. But it will help to learn how to use Google api for server side scripting
 * Bascially used for wordpress plugin
 * */
 
class GCalendar{
	
	/*
	 * constants are defined to use clender API Version-3
	 * */
	const AUTHSUB_REQUEST_URI = 'https://accounts.google.com/o/oauth2/auth';
	const AUTHSUB_TOKEN_URI = 'https://accounts.google.com/o/oauth2/token';
	const CALENDER_SCOPE = 'https://www.googleapis.com/auth/calendar';
	const CALENDERS_LIST = 'https://www.googleapis.com/calendar/v3/users/me/calendarList';
	const CALENDER_EVENT = 'https://www.googleapis.com/calendar/v3/calendars';
	
	
	private $client_id;
	private $client_secret;
	private $redirect_uri;
	private $authenticated = false;
	
	
   /**
   * Class constructor to create an instance, takes client id and client secret
   * @param string $client_id  Your Google application access Id
   * @param string $client_secret  Your Google application secret Id
   */
	function __construct($client_id, $client_secret, $redirect_uri){
		$this->client_id = $client_id;
		$this->client_secret = $client_secret;
		$this->redirect_uri = $redirect_uri;
	}
	
	/**
	 * Returns the AuthSub URL which the user must visit to authenticate requests
	 * from this application.
	 *
	 * Uses getCurrentUrl() to get the next URL which the user will be redirected
	 * to after successfully authenticating with the Google service.
	 *
	 * @return string AuthSub URL
	 */
	public function getAuthSubUrl(){			  
		  $next = $this->redirect_uri;
		  $request_uri = self::AUTHSUB_REQUEST_URI;	 
		  $scope = self::CALENDER_SCOPE;
		  return $this->getAuthSubTokenUri($next, $scope, $request_uri);
	}
	
	
	/*
	 * private method to return the authentication uri to validate the users
	 * */
	private function getAuthSubTokenUri($next, $scope, $request_uri){
		$querystring = '?next=' . urlencode($next)
		 . '&scope=' . urldecode($scope)
		 . '&client_id=' . urlencode($this->client_id)
		 . '&redirect_uri=' . urlencode($this->redirect_uri)
		 . '&response_type=' . 'code';
		return $request_uri . $querystring;
	}
	
	 /**
	   * Private helper function to get a cURL handle with the correct options, authentication included. The user has to be successfully authenticated with authenticate() first
	   * @param string $url           The URL where the http request should go
	   * 
	   */
	 private function curlGetHandle($url) {					
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);			
		return $ch;    
	 }
	  
	  /**
	   * Private helper function to get a cURL handle for POST actions with the correct options. The user has to be successfully authenticated with authenticate() first. 
	   * @param string $url           The URL where the http request should go	   * 
	   */
	  private function curlPostHandle($url) {
			
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		
		return $ch;
	 }
	 
	  /**
	   * Private helper function to get a cURL handle for POST actions with the correct options. The user has to be successfully authenticated with authenticate() first. 
	   * @param string $url           The URL where the http request should go	   * 
	   */
	  private function curlPutHandle($url) {
			
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		
		return $ch;
	 }
	 
	 
	/*
	 * public functions to return the calender lists
	 * */
	public function getAllCalendars($token = ''){
		if(empty($token)) return false;
		
		$url = self::CALENDERS_LIST . '?access_token=' . $token;
		$ch = $this->curlGetHandle($url);
		$response = curl_exec($ch);		
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if($http_code == 200){
			return json_decode($response);
		}
		else{
			return false;
		}
	}
	
	
	/*
	 * returns an event
	 * @param $calender_id
	 * @event id
	 * */
	public function getEvent($calendar_id, $event_id){
		$url = self::CALENDER_EVENT . '/' . urlencode($calendar_id) . '/events/' . urlencode($event_id);
		$ch = $this->curlGetHandle($url);
		$response = curl_exec($ch);		
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if($http_code == 200){
			return json_decode($response);
		}
		else{
			return false;
		}
		
	}
	
	
	/*
	 * creates an event and return the created events
	 * */
	public function createEvent($calender_id, $summary, $description='', $event_start=null, $event_end, $time_zone){
		$url = self::CALENDER_EVENT . '/' . $calender_id . '/events';
		$data = sprintf('{
			"summary": %s,
			"description": %s,
			"start": {
				"dateTime": %s,
				"timeZone": %s
			  },
		  "end": {
			"dateTime": %s,
			"timeZone": %s
		  },
		}', $summary, $description, $event_start, $time_zone, $event_end, $time_zone);
		
		$ch = $this->curlPostHandle($url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if($http_code == 200){
			return json_decode($response);
		}
		else{
			return false;
		}
		
	}
	
	
	/*
	 * Public function to update an existing event or patch the event
	 * return the updated events
	 * */
	public function updateEvent($calendar_id, $event_id, $summar, $description, $event_start, $event_end, $time_zone){
		$url = self::CALENDER_EVENT . '/' . urlencode($calendar_id) . '/events/' . urlencode($event_id);
		
		$event = $this->getEvent($calendar_id, $event_id);
		if($event){
			$event->summary = $summar;
			$event->description = $description;
			$event->start->dateTime = $event_start;	
			$event->start->timeZone = $time_zone;	
			$event->end->dateTime = $event_end;
			$event->end->timeZone = $time_zone;	
		}
		else{
			return false;
		}
		
		$ch = $this->curlPutHandle($url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($event));
		$response = curl_exec($ch);
		
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if($http_code == 200){
			return json_decode($response);
		}
		else{
			return false;
		}
				
	}
	
	/*
	 * private functions to authentication
	 */
	private function authenticate($code){
		$url = self::AUTHSUB_TOKEN_URI;
		$ch = $this->curlPostHandle($url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, sprintf("client_id=%s&redirect_uri=%s&client_secret=%s&grant_type=%s&code=%s", urlencode($this->client_id), urlencode($this->redirect_uri), urlencode($this->client_secret), 'authorization_code', urlencode($code)));
		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if($http_code == 200){
			$response_d = json_decode($response);
			return $response_d->access_token;
		}
		else{
			return false;
		}
	}
	
	/*
	 * a an access token after the webserver verification
	 */
	public function get_authenticated_token($code){
		return (empty($code)) ? false : $this->authenticate($code);
	}
	
	
}


?>
