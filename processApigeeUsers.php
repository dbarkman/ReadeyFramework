<?php

session_start();

require_once dirname(__FILE__) . '/includes/includes.php';

$ptwr = new processApigeeUsers();
$ptwr->getApigeeUsers();

class processApigeeUsers
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

	public function getApigeeUsers()
	{
		$userObject = new User();
		$userObject->getNewestUser();

		$apigee = new Apigee();
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
					SendEmail::sendNewUserNotification($message);
				}
			}
		}
	}
}