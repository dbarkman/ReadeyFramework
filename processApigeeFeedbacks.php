<?php

session_start();

require_once dirname(__FILE__) . '/includes/includes.php';

$ptwr = new processApigeeFeedbacks();
$ptwr->getApigeeFeedbacks();

class processApigeeFeedbacks
{
	private $_logger;
	private $_mySqlConnect;

	public function __construct()
	{
		$container = new Container();

		$this->_logger = $container->getLogger();

		$this->_mySqlConnect = $container->getMySqlConnect();
	}

	public function getApigeeFeedbacks()
	{
		$feedbackObject = new Feedback($this->_logger, $this->_mySqlConnect->db);
		$feedbackObject->getNewestFeedback();

		$apigee = new Apigee($this->_logger);
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
					SendNotifications::sendNewFeedbackNotification($message);
				}
			}
		}
	}
}