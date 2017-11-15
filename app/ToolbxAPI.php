<?php

namespace App;

use \GuzzleHttp\Client;
use Session;

class ToolbxAPI
{

    private $client;
    
    private $strUserAgent;
    
	function __construct() {
		$this->client = new Client([
			'base_uri' => 'http://40.114.55.186/api/'
		]);
		$this->strUserAgent = 'Toolbx/2.0';
    }
    
	public function get($url, $token = NULL, $arguments = array(), $debug = false, $verify = false) {
		return $this->httpRequest('GET', $url, $token, $arguments, $debug, $verify);
    }
    
	public function post($url, $token = NULL, $arguments = array(), $debug = false, $verify = false) {
		return $this->httpRequest('POST', $url, $token, $arguments, $debug, $verify);
    }
    
	public function put($url, $token = NULL, $arguments = array(), $debug = false, $verify = false) {
		$arguments['_method'] = 'PUT';
		return $this->httpRequest('POST', $url, $token, $arguments, $debug, $verify);
    }
    
	public function delete($url, $token = NULL, $arguments = array(), $debug = false, $verify = false) {
		return $this->httpRequest('DELETE', $url, $token, $arguments, $debug, $verify);
    }
    
	private function httpRequest($type, $url, $token, $arguments, $debug, $verify) {
		//return $url;
		$verify = false;
		$options = [
			'allow_redirects' => true,
			'headers' => [
				'User-Agent' => $this->strUserAgent,
				'Accept' => 'application/json',
				'Authorization' => $token
			],
			'debug' => $debug,
			'verify' => $verify
		];
		//dd($options);
		if( $type == 'POST' ) {
			$options['form_params'] = $arguments;
		}
		try
		{
			$response = $this->client->request($type, $url, $options);
			//return $response;
		}
		catch (\Exception $e)
		{
			dd($e->getMessage());
			return json_decode($e->getResponse()->getBody()->getContents());
		}
		 // echo $response->getBody()->getContents();
		 // dd();
		if( $type == 'DELETE')
			return $response->getBody()->getContents();
		else
			return json_decode($response->getBody()->getContents());
	}

}
