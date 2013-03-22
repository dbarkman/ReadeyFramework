<?php

session_start();

require_once dirname(__FILE__) . '/includes/includes.php';
require_once dirname(__FILE__) . '/SimplePie/autoloader.php';

if (!isset($argv[1])) {
	echo 'please include a feed' . PHP_EOL;
	exit();
}

$feedUrl = $argv[1];

$addRSS = new addRSSFeed();
$addRSS->addRss($feedUrl);

class addRSSFeed
{
	private $_logger;
	private $_mySqlConnect;

	public function __construct()
	{
		$container = new Container();

		$this->_logger = $container->getLogger();

		$this->_mySqlConnect = $container->getMySqlConnect();
	}

	public function addRss($feedUrl)
	{
		$feed = new SimplePie();
		$feed->set_feed_url($feedUrl);
		$feed->init();
		$feed->handle_content_type();

		$return = 'fail';
		if (!$feed->error()) {
			$date = time();
			$rssFeedObject = new RSSFeed($this->_logger, $this->_mySqlConnect->db);
			$rssFeedObject->setUuid(UUID::getUUID());
			$rssFeedObject->setFeedUrl($feedUrl);
			$rssFeedObject->setTitle($feed->get_title());
			$rssFeedObject->setPermalink($feed->get_permalink());
			$rssFeedObject->setCreated($date);
			$rssFeedObject->setModified($date);
			if ($rssFeedObject->createFeed() === TRUE) {
				$return = 'success' . PHP_EOL;
				$return .= 'Title: ' . $feed->get_title() . PHP_EOL;
			}
		}
		echo $return . PHP_EOL;
	}
}
