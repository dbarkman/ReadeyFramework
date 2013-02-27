<?php

session_start();

require_once dirname(__FILE__) . '/includes/includes.php';

$ptwr = new processTotalWordsRead();
$ptwr->getTotalWordsRead();

class processTotalWordsRead
{
	private $_logger;
	private $_db;

	public function __construct()
	{
		$this->_logger = Singleton::getInstance('Logger');
		$this->_logger->debug('');

		$mySqlConnect = Singleton::getInstance('MySQLConnect');
		$this->_db = $mySqlConnect->db;
	}

	public function getTotalWordsRead()
	{
		$readLogObject = new ReadLog();
		$readLogObject->getNewestReadLog();

		$apigee = new Apigee();
		$readLogs = $apigee->getReadLogs('newer', $readLogObject->getCreated());

		if (isset($readLogs->action) && $readLogs->action === 'get') {
			$this->_logger->debug('ReadLogs Retrieved: ' . $readLogs->count);

			$readLogArray = $readLogs->entities;

			$count = 0;
			foreach ($readLogArray as $readLog) {
				$readLogObject->setUuid($readLog->uuid);
				$readLogObject->setCreated($readLog->created);
				$readLogObject->setModified($readLog->modified);
				$readLogObject->setUser($readLog->user);
				$readLogObject->setWords($readLog->words);
				$readLogObject->setSpeed(round($readLog->speed, 3));
				if ($readLogObject->createReadLog() === TRUE) $count++;
			}
			if ($count > 0) $this->_logger->info('ReadLogs Created: ' . $count);
		}
	}
}