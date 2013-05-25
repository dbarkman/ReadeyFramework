<?php

/**
 * RSSCategory.model.php
 * Description:
 *
 */

class RSSCategory
{
	private $_logger;
	private $_db;

	private $_uuid;
	private $_name;
	private $_created;
	private $_modified;
	private $_rank;

	public function __construct($logger, $db)
	{
		$this->_logger = $logger;

		$this->_db = $db;
	}

	public static function GetCategoriesDetailsForAPI()
	{
		$categoryArray = array();

		$sql = "
			SELECT
				*
			FROM
				rssCategories
		";

		$result = mysql_query($sql);

		while ($row = mysql_fetch_assoc($result)) {
			$categoryArray[] = $row;
		}

		return $categoryArray;
	}

	public static function GetCategoriesForAPI($user)
	{
		$showFirstReadCategorySql = '';
		$firstReadAlreadyRead = ReadLog::GetFirstReadAlreadyReadStatus($user);
		if ($firstReadAlreadyRead === true) {
			$showFirstReadCategorySql = "AND rank != '0'";
		}

		$categoryArray = array();

		$sql = "
			SELECT
				uuid,
				name
			FROM
				rssCategories
			WHERE
				active = 1
		";
		$sql .= $showFirstReadCategorySql;
		$sql .= "
			ORDER BY rank ASC, name
		";

		$result = mysql_query($sql);

		while ($row = mysql_fetch_assoc($result)) {
			$categoryArray[] = array('name' => $row['name'], 'uuid' => $row['uuid']);
		}

		return $categoryArray;
	}

	public static function GetCategoryUUIDForName($name)
	{
		$sql = "
			SELECT
				uuid
			FROM
				rssCategories
			WHERE
				name = '$name'
		";

		$result = mysql_query($sql);
		if (mysql_num_rows($result) > 0) {
			$row = mysql_fetch_array($result);
			return $row['0'];
		}
		return 'tbd';
	}

	public function createCategory()
	{
		$sql = "
			INSERT INTO
				rssCategories
			SET
				uuid = '$this->_uuid',
				name = '$this->_name',
				created = '$this->_created',
				modified = '$this->_modified',
				rank = '$this->_rank'
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

	public function setName($category)
	{
		$this->_name = $category;
	}

	public function getName()
	{
		return $this->_name;
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

	public function setRank($rank)
	{
		$this->_rank = $rank;
	}

	public function getRank()
	{
		return $this->_rank;
	}
}
