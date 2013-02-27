<?php

/**
 * Apigee.class.php
 * Description:
 *
 */

class Apigee {

	private $_logger;
	private $_accessToken;

	public function __construct() {
		$this->_logger = Singleton::getInstance('Logger');
		$this->_logger->debug('');
	}

	public function getAuthToken()
	{
		$baseUrl = 'https://api.usergrid.com/reallysimpleapps/readey/token';
		$query = '?grant_type=password&username=readeyAPI&password=VWt8D24Kr3R47Hgsp96ZUcEz6xamY8Ne';
		$url = $baseUrl . $query;

		$token = json_decode(self::runCurl('GET', $url));
		if (isset($token->access_token)) {
			$this->_accessToken = $token->access_token;
			return TRUE;
		} else {
			return FALSE;
		}
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
		$this->_logger->debug('ReadLog URL: ' . $url);

		$readLogs = json_decode(self::runCurl('GET', $url, FALSE));
		return $readLogs;
	}

	public function getUsers($when = null, $created = null)
	{
		$baseUrl = 'https://api.usergrid.com/reallysimpleapps/readey/users';
		$query = '';
		if (isset($when) && isset($created)) {
			if ($when === 'newer') {
				$query .= '?ql=select%20*%20where%20created>' . $created;
			} else if ($when === 'older') {
				$query .= '?ql=select%20*%20where%20created<' . $created;
			} else if ($when === 'equal') {
				$query .= '?ql=select%20*%20where%20created=' . $created;
			}
		}
		$url = $baseUrl . $query;

		$users = json_decode(self::runCurl('GET', $url, TRUE));
		return $users;
	}

	private function runCurl($requestMethod, $url, $auth = FALSE)
	{
		if ($auth === TRUE) {
			$url .= (strstr($url, '?') !== FALSE) ? '&' : '?';
			$url .= 'access_token=' . $this->_accessToken;
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