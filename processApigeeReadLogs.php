<?php

session_start();

require_once dirname(__FILE__) . '/includes/includes.php';

$ptwr = new processApigeeReadLogs();
$ptwr->getApigeeReadLogs();

class processApigeeReadLogs
{
	private $_logger;
	private $_mySqlConnect;

	public function __construct()
	{
		$container = new Container();

		$this->_logger = $container->getLogger();

		$this->_mySqlConnect = $container->getMySqlConnect();
	}

	public function getApigeeReadLogs()
	{
		$readLogObject = new ReadLog($this->_logger, $this->_mySqlConnect->db);
		$readLogObject->getNewestReadLog();

		$apigee = new Apigee($this->_logger);
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
			if ($count > 0) {
				$message = 'ReadLogs Created: ' . $count;
				$this->_logger->info($message);
				SendNotifications::sendNewWordLogNotification($message);
			}
		}
	}
}