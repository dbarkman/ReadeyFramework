<?php

/**
 * MySQLConnect.php
 * Description:
 *
 */

class MySQLConnect
{
	private $_logger;
	private $_host;
	private $_user;
	private $_pass;
	private $_database;

	protected $result;

	public $db;

	public function __construct($host = null, $user = null, $pass = null, $database = null)
	{
		global $readeyDB;

		$this->_logger = Singleton::getInstance('Logger');
		$this->_logger->debug('');

		$this->_host = $readeyDB['sqlHost'];
		$this->_user = $readeyDB['sqlUser'];
		$this->_pass = $readeyDB['sqlPassword'];
		$this->_database = $readeyDB['sqlDatabase'];

		if ($host !== null) $this->_host = $host;
		if ($user !== null) $this->_user = $user;
		if ($pass !== null) $this->_pass = $pass;
		if ($database !== null) $this->_database = $database;

		$this->db = mysql_connect($this->_host, $this->_user, $this->_pass) or die ('Cannot connect to the database because:: ' . mysql_error());
		self::changeDatabase($this->_database);
	}

	public function changeConnection($host, $user, $pass, $database)
	{
		self::close();

		$this->_host = $host;
		$this->_user = $user;
		$this->_pass = $pass;
		$this->_database = $database;

		$this->db = mysql_connect($this->_host, $this->_user, $this->_pass) or die ('Cannot connect to the database because:: ' . mysql_error());
		self::changeDatabase($this->_database);
	}

	public function changeDatabase($database)
	{
		mysql_select_db($database);
	}

	public function close()
	{
		mysql_close($this->db);
	}
}
