<?php

/**
 * ReadLog.model.php
 * Description:
 *
 */

class ReadLog
{
	private $_logger;
	private $_db;

	private $_uuid;
	private $_created;
	private $_modified;
	private $_user;
	private $_words;
	private $_speed;
	private $_rssItemUuid;
	private $_rssCategoryUuid;

	public function __construct($logger, $db)
	{
		$this->_logger = $logger;

		$this->_db = $db;
	}

	public static function GetFirstReadAlreadyReadStatus($user)
	{
		//don't change the ri.uuid below, it should be hardcoded for now - 642
		$sql = "
			SELECT
				DISTINCT
				rl.rssItemUuid AS alreadyRead
			FROM
				rssItems ri
				LEFT JOIN readLogs rl ON rl.rssItemUuid = ri.uuid AND rl.user = '$user'
			WHERE
				ri.uuid = 'FDAE3FCD-7794-436C-ACFC-BA71317B6F9E'
		";

		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		return ($row['alreadyRead'] === NULL) ? false : true;
	}

	public static function GetLatestReadeyServiceUpdateAlreadyReadStatus($user)
	{
		//don't change the ri.uuid below, it should be hardcoded for now - 642
		$sql = "
			SELECT
				DISTINCT
				rl.rssItemUuid AS alreadyRead
			FROM
				rssItems ri
				LEFT JOIN readLogs rl ON rl.rssItemUuid = ri.uuid AND rl.user = '$user'
			WHERE
				ri.uuid = '3BBF63FA-3F2E-4E9A-9037-5A98C83551E6'
		";

		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		return ($row['alreadyRead'] === NULL) ? false : true;
	}

	public function createReadLog()
	{
		$sql = "
			INSERT INTO
				readLogs
			SET
				uuid = '$this->_uuid',
				created = '$this->_created',
				modified = '$this->_modified',
				user = '$this->_user',
				words = '$this->_words',
				speed = '$this->_speed',
				rssItemUuid = '$this->_rssItemUuid',
				rssCategoryUuid = '$this->_rssCategoryUuid'
		";

		mysql_query($sql);
		$rowsAffected = mysql_affected_rows();

		if ($rowsAffected === 1) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function getTotalWordsRead()
	{
		$sql = "
			SELECT
				SUM(words)
			FROM
				readLogs
		";

		$result = mysql_query($sql);

		if (mysql_num_rows($result) > 0) {
			$row = mysql_fetch_array($result);
			return $row[0];
		} else {
			return 0;
		}
	}

	public function getNewestReadLog()
	{
		$sql = "
			SELECT
				*
			FROM
				readLogs
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
		$this->_words = $row['words'];
		$this->_speed = $row['speed'];
		$this->_rssItemUuid = $row['rssItemUuid'];
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

	public function setUser($user)
	{
		$this->_user = mysql_real_escape_string($user);
	}

	public function getUser()
	{
		return $this->_user;
	}

	public function setWords($words)
	{
		$this->_words = mysql_real_escape_string($words);
	}

	public function getWords()
	{
		return $this->_words;
	}

	public function setSpeed($speed)
	{
		$this->_speed = mysql_real_escape_string($speed);
	}

	public function getSpeed()
	{
		return $this->_speed;
	}

	public function setRssItemUuid($rssItemUuid)
	{
		$this->_rssItemUuid = mysql_real_escape_string($rssItemUuid);
	}

	public function getRssItemUuid()
	{
		return $this->_rssItemUuid;
	}

	public function setRssCategoryUuid($rssCategoryUuid)
	{
		$this->_rssCategoryUuid = mysql_real_escape_string($rssCategoryUuid);
	}

	public function getRssCategoryUuid()
	{
		return $this->_rssCategoryUuid;
	}
}
