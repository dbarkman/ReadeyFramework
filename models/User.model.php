<?php

/**
 * User.model.php
 * Description:
 *
 */

class User {

	private $_logger;
	private $_db;

	private $_uuid;
	private $_created;
	private $_modified;
	private $_name;
	private $_email;
	private $_username;

	public function __construct($logger, $db)
	{
		$this->_logger = $logger;

		$this->_db = $db;
	}

	public function createUser()
	{
		$sql = "
			INSERT INTO
				users
			SET
				uuid = '$this->_uuid',
				created = '$this->_created',
				modified = '$this->_modified',
				name = '$this->_name',
				email = '$this->_email',
				username = '$this->_username'
		";

		mysql_query($sql);
		$rowsAffected = mysql_affected_rows();

		if ($rowsAffected === 1) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function getTotalUsers()
	{
		$sql = "
			SELECT
				COUNT(uuid)
			FROM
				users
		";

		$result = mysql_query($sql);

		if (mysql_num_rows($result) > 0) {
			$row = mysql_fetch_array($result);
			return $row[0];
		} else {
			return 0;
		}
	}

	public function getNewestUser()
	{
		$sql = "
			SELECT
				*
			FROM
				users
			ORDER BY
				created DESC
			LIMIT 1
		";

		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);

		$this->_uuid = $row['uuid'];
		$this->_created = $row['created'];
		$this->_modified = $row['modified'];
		$this->_name = $row['name'];
		$this->_email = $row['email'];
		$this->_username = $row['username'];
	}

	public function setUuid($uuid)
	{
		$this->_uuid = $uuid;
	}

	public function getUuid()
	{
		return $this->_uuid;
	}

	public function setCreated($created)
	{
		$this->_created = $created;
	}

	public function getCreated()
	{
		return $this->_created;
	}

	public function setModified($modified)
	{
		$this->_modified = $modified;
	}

	public function getModified()
	{
		return $this->_modified;
	}

	public function setName($name)
	{
		$this->_name = mysql_real_escape_string($name);
	}

	public function getName()
	{
		return $this->_name;
	}

	public function setEmail($email)
	{
		$this->_email = $email;
	}

	public function getEmail()
	{
		return $this->_email;
	}

	public function setUsername($username)
	{
		$this->_username = $username;
	}

	public function getUsername()
	{
		return $this->_username;
	}
}
