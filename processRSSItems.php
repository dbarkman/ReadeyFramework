<?php

//go to the database and get an array of feeds
//iterate over feed array and get items for each feed
//iterate over items for items with a date newer than the newest date, put them in the database

session_start();

require_once dirname(__FILE__) . '/includes/includes.php';
require_once dirname(__FILE__) . '/SimplePie/autoloader.php';

$pri = new proccessRSSItems();
$pri->processFeeds();

class proccessRSSItems
{
	private $_logger;
	private $_mySqlConnect;

	private $_currentFeed;
	private $_createdCount;

	public function __construct()
	{
		$container = new Container();

		$this->_logger = $container->getLogger();

		$this->_mySqlConnect = $container->getMySqlConnect();
	}

	public function processFeeds()
	{
		$feedArray = RSSFeed::getFeedArray();

		foreach ($feedArray as $uuid => $feedUrl) {
			$this->_currentFeed = $uuid;
			$newestDate = RSSItem::getNewestItemDate($uuid);
			if (empty($newestDate)) $newestDate = 0;

			$feed = new SimplePie();
			$feed->set_feed_url($feedUrl);
			$feed->set_cache_location('/srv/http/ReadeyFramework/cache');
			$feed->set_cache_duration(0);
			$feed->init();
			$feed->handle_content_type();

			$this->_createdCount = 0;
			$itemsArray = $feed->get_items();
			$itemCount = count($itemsArray);
			for ($i = 0; $i < $itemCount; $i++) {
				if ($itemsArray[$i]->get_date('U') > $newestDate) {
					$this->addItem($itemsArray[$i]);
				}
			}
			$this->_logger->debug('Items Created for ' . $feed->get_title() . ': ' . $this->_createdCount);
		}
	}

	private function addItem($item)
	{
		$date = time();

		$rssItemObject = new RSSItem($this->_logger, $this->_mySqlConnect->db);
		$rssItemObject->setUuid(UUID::getUUID());
		$rssItemObject->setFeed($this->_currentFeed);
		$rssItemObject->setMd5String($item->__toString());
		$rssItemObject->setTitle($item->get_title());
		$rssItemObject->setDate($item->get_date('U'));
		$rssItemObject->setPermalink($item->get_permalink());
		$rssItemObject->setDescription($item->get_description());
		$rssItemObject->setContent($item->get_content());
		$rssItemObject->setCreated($date);
		$rssItemObject->setModified($date);
		if ($rssItemObject->createItem() === FALSE) {
			$serializedItem = serialize($rssItemObject);
			$this->_logger->error('Item could not be created: ' . $serializedItem);
		} else {
			$this->_createdCount++;
		}
	}
}