<?php

/**
 * RSSFeed.model.php
 * Description:
 *
 */

class RSSFeed
{
	private $_logger;
	private $_db;

	private $_uuid;
	private $_feedUrl;
	private $_title;
	private $_permalink;
	private $_created;
	private $_modified;

	public function __construct($logger, $db)
	{
		$this->_logger = $logger;

		$this->_db = $db;
	}

	public static function getFeedArray()
	{
		$feedArray = array();

		$sql = "
			SELECT
				uuid,
				feedUrl
			FROM
				rssFeeds
		";

		$result = mysql_query($sql);

		while ($row = mysql_fetch_assoc($result)) {
			$uuid = $row['uuid'];
			$feedUrl = $row['feedUrl'];
			$feedArray[$uuid] = $feedUrl;
		}

		return $feedArray;
	}

	public function createFeed()
	{
		$sql = "
			INSERT INTO
				rssFeeds
			SET
				uuid = '$this->_uuid',
				feedUrl = '$this->_feedUrl',
				title = '$this->_title',
				permalink = '$this->_permalink',
				created = '$this->_created',
				modified = '$this->_modified'
		";

		mysql_query($sql);
		$rowsAffected = mysql_affected_rows();

		if ($rowsAffected === 1) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function setUuid($uuid)
	{
		$this->_uuid = $uuid;
	}

	public function getUuid()
	{
		return $this->_uuid;
	}

	public function setFeedUrl($feedUrl)
	{
		$this->_feedUrl = $feedUrl;
	}

	public function getFeedUrl()
	{
		return $this->_feedUrl;
	}

	public function setTitle($title)
	{
		$this->_title = mysql_real_escape_string($title);
	}

	public function getTitle()
	{
		return $this->_title;
	}

	public function setPermalink($permaLink)
	{
		$this->_permalink = $permaLink;
	}

	public function getPermalink()
	{
		return $this->_permalink;
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
}