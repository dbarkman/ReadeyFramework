<?php

/**
 * Feedback.model.php
 * Description:
 *
 */

class Feedback {

	private $_logger;
	private $_db;

	private $_uuid;
	private $_created;
	private $_modified;
	private $_user;
	private $_email;
	private $_feedbackType;
	private $_description;
	private $_status;

	public function __construct($logger, $db)
	{
		$this->_logger = $logger;

		$this->_db = $db;
	}

	public function createFeedback()
	{
		$sql = "
			INSERT INTO
				feedbacks
			SET
				uuid = '$this->_uuid',
				created = '$this->_created',
				modified = '$this->_modified',
				user = '$this->_user',
				email = '$this->_email',
				feedbackType = '$this->_feedbackType',
				description = '$this->_description',
				status = '$this->_status'
		";

		mysql_query($sql);
		$rowsAffected = mysql_affected_rows();

		if ($rowsAffected === 1) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function getNewestFeedback()
	{
		$sql = "
			SELECT
				*
			FROM
				feedbacks
			ORDER BY
				created DESC
			LIMIT 1
		";

		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);

		$this->_uuid = $row['uuid'];
		$this->_created = $row['created'];
		$this->_modified = $row['modified'];
		$this->_user = $row['user'];
		$this->_email = $row['email'];
		$this->_feedbackType = $row['feedbackType'];
		$this->_description = $row['description'];
		$this->_status = $row['status'];
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

	public function setUser($user)
	{
		$this->_user = $user;
	}

	public function getUser()
	{
		return $this->_user;
	}

	public function setEmail($email)
	{
		$this->_email = $email;
	}

	public function getEmail()
	{
		return $this->_email;
	}

	public function setDescription($description)
	{
		$this->_description = mysql_real_escape_string($description);
	}

	public function getDescription()
	{
		return $this->_description;
	}

	public function setFeedbackType($feedbackType)
	{
		$this->_feedbackType = $feedbackType;
	}

	public function getFeedbackType()
	{
		return $this->_feedbackType;
	}

	public function setStatus($status)
	{
		$this->_status = $status;
	}

	public function getStatus()
	{
		return $this->_status;
	}
}
