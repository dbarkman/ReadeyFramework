<?php

session_start();

require_once dirname(__FILE__) . '/includes/includes.php';

if (!isset($argv[1])) {
	echo 'please include a category' . PHP_EOL;
	exit();
}

$category = $argv[1];

$addCategory = new addCategory();
$addCategory->addCategory($category);

class addCategory
{
	private $_logger;
	private $_mySqlConnect;

	public function __construct()
	{
		$container = new Container();

		$this->_logger = $container->getLogger();

		$this->_mySqlConnect = $container->getMySqlConnect();
	}

	public function addCategory($category)
	{
		$date = time();
		$categoryObject = new RSSCategory($this->_logger, $this->_mySqlConnect->db);
		$categoryObject->setUuid(UUID::getUUID());
		$categoryObject->setName($category);
		$categoryObject->setCreated($date);
		$categoryObject->setModified($date);
		$categoryObject->setRank(9999);
		if ($categoryObject->createCategory() === TRUE) {
			$return = 'Successfully added: ' . $category . PHP_EOL;
		} else {
			$return = 'Failed to add: ' . $category . PHP_EOL;
		}
		echo $return . PHP_EOL;
	}
}
