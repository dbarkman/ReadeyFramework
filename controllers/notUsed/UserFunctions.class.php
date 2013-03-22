<?php

//imports
require_once dirname(__FILE__) . '/../../config/credentials.php';

class MySQLTools {

    //properties

    /**
     * @var type and description
     * @access public or private
     */
    private $_username;

    /**
     * @var type and description
     * @access public or private
     */
    private $_password;

    /**
     * @var type and description
     * @access public or private
     */
    private $_uniqrand;

    /**
     * @var bool Stores the setting for the users logged in status
     * 0 = failed log in attempt or not logged in
     * 1 = logged in
     * @access public
     */
    public $isLoggedIn = 0;

    /**
     * @var array Stores errors produced while retrieving config settings to be displayed to the end user
     * @access public
     */
    public $errorsDisp = array();

    //objects

	/**
	 * @var purpose
	 * @access private or public
	 */
	public $db;

    //constructor
    /**
     * @access public or private
     * description
     */
    public function __construct()
    {
		global $logRequest;
		$this->db = mysql_connect($logRequest['sqlHost'], $logRequest['sqlUser'], $logRequest['sqlPassword']) or die ('I cannot connect to the database because: ' . mysql_error());
		mysql_select_db ($logRequest['sqlDatabase']);
	}

    //methods

    /**
     * setter for the username property
     * @access public
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->_username = $username;
    }

    /**
     * setter for the password property
     * @access public
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->_password = $password;
    }

    /**
     * description
     * @access public or private
     * @param type and param name
     */
    private function getSalt($username)
    {
        $query = "SELECT salt FROM salt WHERE id = '$username'";
        $result = @mysql_query($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        return $row['salt'];
    }

    /**
     * description
     * @access public or private
     * @param type and param name
     */
    public function userAdd()
    {
        $salt = $this->createSalt();
		$query = "INSERT INTO salt (id, salt) VALUES ('$this->_username', '$salt')";
		$result = @mysql_query($query); // Run the query.
		unset($query, $result);

        $encrypted = strtoupper(hash('sha512', $salt.$this->_password));
        $query = "INSERT INTO users (username, password) VALUES ('$this->_username', '$encrypted')";
        $result = @mysql_query($query); // Run the query.
    }

	/**
	 * description
	 * @access public or private
	 * @param type and param name
	 */
	public function createSalt($chars = 64, $use_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789~!@#$%^&*')
	{
		$num_usable = strlen($use_chars) - 1;
		$token = '';

		for ($i = 0; $i < $chars; $i++) {
			$rand_char = rand(0, $num_usable);
			$token .= $use_chars{$rand_char};
		}
		return $token;
	}

    /**
     * description
     * @access public or private
     * @param type and param name
     */
    public function userAuth()
    {
        $salt = $this->getSalt($this->_username);
        $encrypted = strtoupper(hash('sha512', $salt.$this->_password));
        $query = "SELECT * FROM users WHERE username = '$this->_username' AND password = '$encrypted'";
        $result = @mysql_query($query); // Run the query.
        $row = mysql_fetch_array($result, MYSQL_ASSOC); // Return a record, if applicable.

        if ($row['username'] == $this->_username && $row['password'] == $encrypted) { // A record was pulled from the database.
            if ($this->lastLogin($this->_username) == null && $this->uniqueRand($this->_username) == null) {
                $this->isLoggedIn = 1;
            }
        }
    }

    /**
     * @access public
     * creates a session for holding data to transfer between pages
     * holds classification and logged in status for securing other pages
     */
    public function createSession()
    {
        //create a session cookie and load it with the username, classification and logged in status
		require_once 'database_session_fns.php';

        session_name('PHPSESSID');
        $_SESSION['created'] = time();
        $_SESSION['username'] = $this->_username;
        $_SESSION['uniqrand'] = $this->_uniqrand;
    }

    /**
     * description
     * @access public or private
     * @param type and param name
     */
    private function lastLogin($username)
    {
        $now = time();
        $query = "UPDATE users SET lastlogin = '$now' WHERE username = '$username'";
        $result = @mysql_query($query); // Run the query.

        if (mysql_affected_rows() != 1) { // A record was pulled from the database.
            return 'error';
        }
    }

    /**
     * description
     * @access public or private
     * @param type and param name
     */
    private function uniqueRand($username)
    {
        $uniqrand = md5(uniqid(mt_rand(),true));
        $query = "UPDATE users SET uniqrand = '$uniqrand' WHERE username = '$username'";
        $result = @mysql_query($query); // Run the query.

        if (mysql_affected_rows() != 1) { // A record was pulled from the database.
            return 'error';
        } else {
            $this->_uniqrand = $uniqrand;
        }
    }
    
    /**
     * description
     * @access public or private
     * @param type and param name
     */
    public function getUniqRand($username)
    {
        $query = "SELECT uniqrand FROM users WHERE username = '$username'";
        $result = @mysql_query($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        return $row['uniqrand'];
    }

}
