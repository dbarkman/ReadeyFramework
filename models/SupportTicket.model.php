<?php

/**
 * SupportTicket.model.php
 * Description:
 *
 */

class SupportTicket {

	private $_logger;
	private $_db;

	private $_uuid;
	private $_created;
	private $_modified;
	private $_name;
	private $_email;
	private $_usersName;
	private $_comment;

	public function __construct($logger, $db)
	{
		$this->_logger = $logger;

		$this->_db = $db;
	}

	public function createSupportTicket()
	{
		$sql = "
			INSERT INTO
				supportTickets
			SET
				uuid = '$this->_uuid',
				created = '$this->_created',
				modified = '$this->_modified',
				name = '$this->_name',
				email = '$this->_email',
				usersName = '$this->_usersName',
				comment = '$this->_comment'
		";

		mysql_query($sql);
		$rowsAffected = mysql_affected_rows();

		if ($rowsAffected === 1) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function getNewestSupportTicket()
	{
		$sql = "
			SELECT
				*
			FROM
				supportTickets
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
		$this->_usersName = $row['usersName'];
		$this->_comment = $row['comment'];
	}

	public function setUuid($uuid)
	{
		$this->_uuid = mysql_real_escape_string($uuid);
	}

	public function getUuid()
	{
		return $this->_uuid;
	}

	public function setCreated($created)
	{
		$this->_created = mysql_real_escape_string($created);
	}

	public function getCreated()
	{
		return $this->_created;
	}

	public function setModified($modified)
	{
		$this->_modified = mysql_real_escape_string($modified);
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
		$this->_email = mysql_real_escape_string($email);
	}

	public function getEmail()
	{
		return $this->_email;
	}

	public function setUsersName($usersName)
	{
		$this->_usersName = mysql_real_escape_string($usersName);
	}

	public function getUsersName()
	{
		return $this->_usersName;
	}

	public function setComment($comment)
	{
		$this->_comment = mysql_real_escape_string($comment);
	}

	public function getComment()
	{
		return $this->_comment;
	}
}
