<?php

session_start();

require_once dirname(__FILE__) . '/includes/includes.php';

$ptwr = new processApigeeFeedbacks();
$ptwr->getApigeeFeedbacks();

class processApigeeFeedbacks
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

	public function getApigeeFeedbacks()
	{
		$feedbackObject = new Feedback();
		$feedbackObject->getNewestFeedback();

		$apigee = new Apigee();
		if ($apigee->getAuthToken() === TRUE) {
			$feedbacks = $apigee->getFeedbacks('newer', $feedbackObject->getCreated());

			if (isset($feedbacks->action) && $feedbacks->action === 'get') {
				$this->_logger->debug('Feedbacks Retrieved: ' . $feedbacks->count);

				$feedbackArray = $feedbacks->entities;

				$count = 0;
				foreach ($feedbackArray as $feedback) {
					$feedbackObject->setUuid($feedback->uuid);
					$feedbackObject->setCreated($feedback->created);
					$feedbackObject->setModified($feedback->modified);
					$feedbackObject->setUser($feedback->user);
					$feedbackObject->setEmail($feedback->email);
					$feedbackObject->setFeedbackType($feedback->feedbackType);
					$feedbackObject->setDescription($feedback->description);
					if ($feedbackObject->createFeedback() === TRUE) $count++;
				}
				if ($count > 0) {
					$message = 'Feedbacks Created: ' . $count;
					$this->_logger->info($message);
					SendEmail::sendNewFeedbackNotification($message);
				}
			}
		}
	}
}