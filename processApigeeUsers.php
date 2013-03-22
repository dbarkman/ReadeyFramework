<?php

session_start();

require_once dirname(__FILE__) . '/includes/includes.php';

$ptwr = new processApigeeUsers();
$ptwr->getApigeeUsers();

class processApigeeUsers
{
	private $_logger;
	private $_mySqlConnect;

	public function __construct()
	{
		$container = new Container();

		$this->_logger = $container->getLogger();

		$this->_mySqlConnect = $container->getMySqlConnect();
	}

	public function getApigeeUsers()
	{
		$userObject = new User($this->_logger, $this->_mySqlConnect->db);
		$userObject->getNewestUser();

		$apigee = new Apigee($this->_logger);
		if ($apigee->getAuthToken() === TRUE) {
			$users = $apigee->getUsers('newer', $userObject->getCreated());

			if (isset($users->action) && $users->action === 'get') {
				$this->_logger->debug('Users Retrieved: ' . $users->count);

				$userArray = $users->entities;

				$count = 0;
				foreach ($userArray as $user) {
					$userObject->setUuid($user->uuid);
					$userObject->setCreated($user->created);
					$userObject->setModified($user->modified);
					$userObject->setName($user->name);
					$userObject->setEmail($user->email);
					$userObject->setUsername($user->username);
					if ($userObject->createUser() === TRUE) $count++;
				}
				if ($count > 0) {
					$message = 'Users Created: ' . $count;
					$this->_logger->info($message);
					SendNotifications::sendNewUserNotification($message);
				}
			}
		}
	}
}