<?php

/**
 * Logger.class.php
 * Description:
 *
 */

class Logger
{
	const DEBUG = 0x01;
	const FATAL = 0x02;
	const ERROR = 0x04;
	const WARN = 0x08;
	const INFO = 0x10;

	public static $levels = array(1 => "DEBUG", 2 => "FATAL", 4 => "ERROR", 8 => "WARN", 16 => "INFO");
	private static $instance;

	private $logPath = "";
	private $logFile = "Framework.log";
	private $logLevel = self::DEBUG;

	//@todo For some reason the log method is being called without a file and we are getting a permissions error saying the directory cannot be opened for writing. We need to check and make sure a logFile is selected
	public function __construct($level = null, $file = null)
	{
		$properties = new FrameworkProperties();
		$this->logFile = $properties->getLogFile();
		$this->logLevel = $properties->getLogLevel();
		$this->logPath = dirname(__FILE__) . "/../logs";
	}

/*	public static function &getInstance() {
		if (!isset(self::$instance)) {
			$className = __CLASS__;
			self::$instance  = new $className;
		}
		return self::$instance;
	}*/

	public static function getLevelString($int)
	{
		return self::$levels[$int];
	}

	public static function getLevelInt($string)
	{
		foreach (self::$levels as $key => $val)
		{
			if ($val == $string) {
				return $key;
			}
		}

		return NULL;
	}

	public function getLogLevelString()
	{
		return self::$levels[$this->logLevel];
	}

	public function getLogLevel()
	{
		return $this->logLevel;
	}

	private function log($level, $message)
	{
		if ($level >= $this->logLevel) {
			$log = $this->logPath . DIRECTORY_SEPARATOR . $this->logFile;
			$trace = debug_backtrace();
			$file = explode('/', $trace[1]['file']);
			$file = array_pop($file);
			$now = date("m/d/Y H:i:s", time());
			$msg = sprintf("%-14s | %-5s - %s", $now, self::$levels[$level], session_id() . " - (" . $file . ") - " . $message);
//			echo $log . '<br />';
			file_put_contents($log, "$msg\n", FILE_APPEND);
		}
	}

	public function debug($message)
	{
		$this->log(self::DEBUG, $message);
	}

	public function info($message)
	{
		$this->log(self::INFO, $message);
	}

	public function warn($message)
	{
		$this->log(self::WARN, $message);
	}

	public function error($message)
	{
		$this->log(self::ERROR, $message);
	}

	public function fatal($message)
	{
		$this->log(self::FATAL, $message);
	}
}
