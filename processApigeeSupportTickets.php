<?php

session_start();

require_once dirname(__FILE__) . '/includes/includes.php';

$ptwr = new processApigeeSupportTickets();
$ptwr->getApigeeSupportTickets();

class processApigeeSupportTickets
{
	private $_logger;
	private $_mySqlConnect;

	public function __construct()
	{
		$container = new Container();

		$this->_logger = $container->getLogger();

		$this->_mySqlConnect = $container->getMySqlConnect();
	}

	public function getApigeeSupportTickets()
	{
		$supportTicketObject = new SupportTicket($this->_logger, $this->_mySqlConnect->db);
		$supportTicketObject->getNewestSupportTicket();

		$apigee = new Apigee($this->_logger);
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
					SendNotifications::sendNewSupportTicketNotification($message);
				}
			}
		}
	}
}