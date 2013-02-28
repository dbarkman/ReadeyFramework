<?php

session_start();

require_once dirname(__FILE__) . '/includes/includes.php';

$ptwr = new processApigeeSupportTickets();
$ptwr->getApigeeSupportTickets();

class processApigeeSupportTickets
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

	public function getApigeeSupportTickets()
	{
		$supportTicketObject = new SupportTicket();
		$supportTicketObject->getNewestSupportTicket();

		$apigee = new Apigee();
		if ($apigee->getAuthToken() === TRUE) {
			$supportTickets = $apigee->getSupportTickets('newer', $supportTicketObject->getCreated());

			if (isset($supportTickets->action) && $supportTickets->action === 'get') {
				$this->_logger->debug('Support Tickets Retrieved: ' . $supportTickets->count);

				$supportTicketArray = $supportTickets->entities;

				$count = 0;
				foreach ($supportTicketArray as $supportTicket) {
					$supportTicketObject->setUuid($supportTicket->uuid);
					$supportTicketObject->setCreated($supportTicket->created);
					$supportTicketObject->setModified($supportTicket->modified);
					$supportTicketObject->setName($supportTicket->name);
					$supportTicketObject->setEmail($supportTicket->email);
					$supportTicketObject->setUsersName($supportTicket->usersName);
					$supportTicketObject->setComment($supportTicket->comment);
					if ($supportTicketObject->createSupportTicket() === TRUE) $count++;
				}
				if ($count > 0) {
					$message = 'Support Tickets Created: ' . $count;
					$this->_logger->info($message);
					SendEmail::sendNewSupportTicketNotification($message);
				}
			}
		}
	}
}