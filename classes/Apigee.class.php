<?php

/**
 * Apigee.class.php
 * Description:
 *
 */

class Apigee {

	private $_logger;

	public function __construct() {
		$this->_logger = Singleton::getInstance('Logger');
		$this->_logger->debug('');
	}

	public function getReadLogs($when = null, $created = null)
	{
		$baseUrl = 'https://api.usergrid.com/reallysimpleapps/readey/readlogs';
		$query = '';
		if (isset($when) && isset($created)) {
			if ($when === 'newer') {
				$query .= 'ql=select%20*%20where%20created>' . $created;
			} else if ($when === 'older') {
				$query .= 'ql=select%20*%20where%20created<' . $created;
			} else if ($when === 'equal') {
				$query .= 'ql=select%20*%20where%20created=' . $created;
			}
		}
		$url = $baseUrl . '?' . $query;

		$readLogs = json_decode(self::runCurl('GET', $url));
		return $readLogs;
	}

	private function runCurl($requestMethod, $url)
	{
		$logger = Singleton::getInstance('Logger');
		// is cURL installed yet?
		if (!function_exists('curl_init')){
			$logger->debug('Sorry cURL is not installed!');
			die('Sorry cURL is not installed!');
		}

		$ch = curl_init();
		if ($requestMethod === 'GET') curl_setopt($ch, CURLOPT_HTTPGET, 1);
		if ($requestMethod === 'POST') curl_setopt($ch, CURLOPT_POST, 1);
		if ($requestMethod === 'PUT') curl_setopt($ch, CURLOPT_PUT, 1);
		if ($requestMethod === 'DELETE') curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}
}