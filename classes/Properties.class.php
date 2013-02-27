<?php

/**
 * Properties.class.php
 * Description:
 *
 */

class Properties
{
	protected $data = array();
//	private $logger = NULL;

	public function __construct()
	{
//		$this->logger = new Logger(Logger::INFO, "Properties.log");
	}

	public function load($file)
	{
		if (file_exists($file)) {
			//$this->logger->info("Loading properties file $file.");

			$contents = file_get_contents($file);
			$lines = explode("\n", $contents);

			foreach ($lines as $line)
			{
				$keyvals = explode("=", $line);
				if (count($keyvals) == 2) {
					$this->data[$keyvals[0]] = rtrim($keyvals[1]);
				}
			}
		} else {
//			$this->logger->warn("Unable to find properties file $file.");
		}
	}

	public function save($file)
	{
		if (isset($file)) {
//			$this->logger->info("Writing Properties file: $file");
			$output = "";
			foreach ($this->data as $key => $val)
			{
				if ($key != NULL && $val != NULL) {
					$output = $output . "$key=$val\n";
				}
			}
			file_put_contents($file, $output);
		}
		else
		{
//			$this->logger->warn("Missing required file.");
		}
	}

	protected function getProperty($name)
	{
		if (isset($this->data[$name])) {
			return $this->data[$name];
		}
		return NULL;
	}

	protected function setProperty($name, $val)
	{
		$this->data[$name] = $val;
	}
}
