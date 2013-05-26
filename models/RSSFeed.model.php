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
	private $_category;
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
			WHERE
				active = 1
		";

		$result = mysql_query($sql);

		while ($row = mysql_fetch_assoc($result)) {
			$uuid = $row['uuid'];
			$feedUrl = $row['feedUrl'];
			$feedArray[$uuid] = $feedUrl;
		}

		return $feedArray;
	}

	public static function GetFeedsForAPI()
	{
		$feedArray = array();

		$sql = "
			SELECT
				*
			FROM
				rssFeeds
		";

		$result = mysql_query($sql);

		while ($row = mysql_fetch_assoc($result)) {
			$feedArray[] = $row;
		}

		return $feedArray;
	}

	public static function GetFeedCategoriesForAPI()
	{
		$categoryArray = array();

		$sql = "
			SELECT
				DISTINCT category
			FROM
				rssFeeds
			ORDER BY category
		";

		$result = mysql_query($sql);

		while ($row = mysql_fetch_assoc($result)) {
			$categoryArray[] = array('name' => $row['category']);
		}

		return $categoryArray;
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
				category = '$this->_category',
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
		$this->_uuid = mysql_real_escape_string($uuid);
	}

	public function getUuid()
	{
		return $this->_uuid;
	}

	public function setFeedUrl($feedUrl)
	{
		$this->_feedUrl = mysql_real_escape_string($feedUrl);
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

	public function setCategory($category)
	{
		$this->_category = mysql_real_escape_string($category);
	}

	public function getCategory()
	{
		return $this->_category;
	}

	public function setPermalink($permaLink)
	{
		$this->_permalink = mysql_real_escape_string($permaLink);
	}

	public function getPermalink()
	{
		return $this->_permalink;
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
}
