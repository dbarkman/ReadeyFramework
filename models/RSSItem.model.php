<?php

/**
 * RSSITEM.model.php
 * Description:
 *
 */

class RSSItem
{
	private $_logger;
	private $_db;

	private $_uuid;
	private $_feed;
	private $_md5String;
	private $_title;
	private $_date;
	private $_permalink;
	private $_description;
	private $_content;
	private $_created;
	private $_modified;

	public function __construct($logger, $db)
	{
		$this->_logger = $logger;

		$this->_db = $db;
	}

	public static function GetItemsForFeedForAPI($feed)
	{
		$itemArray = array();

		$sql = "
			SELECT
				*
			FROM
				rssItems
			WHERE
				feed = '$feed'
			LIMIT 10
		";

		$result = mysql_query($sql);

		while ($row = mysql_fetch_assoc($result)) {
			$itemArray[] = $row;
		}

		return $itemArray;
	}

	public static function GetItemsForCategoryForAPI($category, $page, $itemsPerPage, $user)
	{
//		$properties = new ReadeyProperties();
//		$logFile = $properties->getLogFile();
//		$logLevel = $properties->getLogLevel();
//		$logger = new Logger($logLevel, $logFile);

		$limit = intval($itemsPerPage);
		$index = ($page > 0) ? (($page - 1) * $itemsPerPage) : 0;

		$itemArray = array();

		$sql = "
			SELECT
				DISTINCT
				SQL_CALC_FOUND_ROWS
				ri.uuid,
				rf.title AS feedTitle,
				ri.title,
				ri.date,
				rl.rssItemUuid AS alreadyRead,
				ri.permalink,
				ri.content
			FROM
				rssItems ri
				JOIN rssFeeds rf ON rf.uuid = ri.feed
				JOIN rssCategories rc ON rc.uuid = rf.category
				LEFT JOIN readLogs rl ON rl.rssItemUuid = ri.uuid AND rl.user = '$user'
			WHERE
				rc.uuid = '$category'
			ORDER BY
				date DESC
			LIMIT " . $index . "," . $limit . "
		";

//		$logger->debug($sql);

		$result = mysql_query($sql);
		$resultCount = mysql_query("SELECT FOUND_ROWS()");
		$resultCountRows = mysql_fetch_array($resultCount);
		$itemArray[] = $resultCountRows[0];

		while ($row = mysql_fetch_assoc($result)) {
			$row['date'] = date('D, n/j, g:i a', $row['date']);
			$row['wordCount'] = str_word_count($row['content']);
			$row['alreadyRead'] = ($row['alreadyRead'] === NULL) ? false : true;
			$itemArray[] = $row;
		}

		return $itemArray;
	}

	public static function getNewestItemDate($feed)
	{
		$sql = "
			SELECT
				date
			FROM
				rssItems
			WHERE
				feed = '$feed'
			ORDER BY
				date DESC
			LIMIT 1
		";

		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);

		return $row['date'];
//		$this->_uuid = $row['uuid'];
//		$this->_feed = $row['feed'];
//		$this->_md5String = $row['md5String'];
//		$this->_title = $row['title'];
//		$this->_date = $row['date'];
//		$this->_permalink = $row['permalink'];
//		$this->_description = $row['description'];
//		$this->_content = $row['content'];
//		$this->_created = $row['created'];
//		$this->_modified = $row['modified'];
	}

	public function createItem()
	{
		$sql = "
			INSERT INTO
				rssItems
			SET
				uuid = '$this->_uuid',
				feed = '$this->_feed',
				md5String = '$this->_md5String',
				title = '$this->_title',
				date = '$this->_date',
				permalink = '$this->_permalink',
				description = '$this->_description',
				content = '$this->_content',
				created = '$this->_created',
				modified = '$this->_modified'
		";

		mysql_query($sql);// or die ('Cannot insert item into the database because: ' . mysql_error());
		$rowsAffected = mysql_affected_rows();

		if ($rowsAffected === 1) {
			return TRUE;
		} else {
			$this->_logger->error('Cannot insert item into the database because: ' . mysql_error());
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

	public function setFeed($feed)
	{
		$this->_feed = mysql_real_escape_string($feed);
	}

	public function getFeed()
	{
		return $this->_feed;
	}

	public function setMd5String($md5String)
	{
		$this->_md5String = mysql_real_escape_string($md5String);
	}

	public function getMd5String()
	{
		return $this->_md5String;
	}

	public function setTitle($title)
	{
		$this->_title = mysql_real_escape_string($title);
	}

	public function getTitle()
	{
		return $this->_title;
	}

	public function setDate($date)
	{
		$this->_date = mysql_real_escape_string($date);
	}

	public function getDate()
	{
		return $this->_date;
	}

	public function setPermalink($permaLink)
	{
		$this->_permalink = mysql_real_escape_string($permaLink);
	}

	public function getPermalink()
	{
		return $this->_permalink;
	}

	public function setDescription($description)
	{
		$this->_description = mysql_real_escape_string($description);
	}

	public function getDescription()
	{
		return $this->_description;
	}

	public function setContent($content)
	{
		$this->_content = mysql_real_escape_string($content);
	}

	public function getContent()
	{
		return $this->_content;
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

	private function fixMessyItems()
	{
		$errorArray = array();

		$sql = "
			SELECT
				uuid,
				title,
				description,
				content
			FROM
				rssItems
			ORDER BY
				date DESC
		";

		$result = mysql_query($sql);

		while ($row = mysql_fetch_assoc($result)) {
			$title = mysql_real_escape_string($this->contentCleanup($row['title']));
			$description = mysql_real_escape_string($this->contentCleanup($row['description']));
			$content = mysql_real_escape_string($this->contentCleanup($row['content']));
			$uuid = $row['uuid'];

			$updateSQL = "
				UPDATE
					rssItems
				SET
					title = '$title',
					description = '$description',
					content = '$content'
				WHERE
					uuid = '$uuid'
			";

			mysql_query($updateSQL);// or die ('Cannot insert item into the database because: ' . mysql_error());
			$updateRowsAffected = mysql_affected_rows();

			if ($updateRowsAffected == 1) {
				$this->_logger->info('Item Update Succeeded');
			} else {
				$this->_logger->error('Cannot update item in the database because: ' . mysql_error() . ' - UUID: ' . $uuid);
				$errorArray[] = $row;
			}
		}

		return $errorArray;
	}

	private function contentCleanup($content)
	{
		$content = strip_tags($content);
		$search = array(
			"&mdash;",
			"&ndash;",
			"&ldquo;",
			"&rdquo;",
			"&lsquo;",
			"&rsquo;",
			"&hellip;",
			"&amp;",
			"&bull;",
			"&Prime;",
			"&prime;",
			"&Eacute;",
			"&eacute;",
			"&Iacute;",
			"&iacute;",
			"&Ouml;",
			"&ouml;",
			"&Uuml;",
			"&uuml;",
			"&nbsp;",
			"\n"
		);
		$replace = array(
			'-',
			'-',
			'“',
			'”',
			"‘",
			"’",
			'...',
			'&',
			'•',
			'″',
			"′",
			'É',
			'é',
			'Í',
			'í',
			'Ö',
			'ö',
			'Ü',
			'ü',
			' ',
			' '
		);
		$content = str_replace($search, $replace, $content);

		$garbageSearch = array(
			"/li&gt;"
		);
		$garbageReplace = array(
			' '
		);
		$content = str_replace($garbageSearch, $garbageReplace, $content);

		return $content;
	}
}