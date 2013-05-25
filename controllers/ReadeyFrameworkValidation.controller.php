<?php

/**
 * MobileAPIValidation.class.php
 * Description:
 *
 */

class ReadeyFrameworkValidation
{
	private $_validate;
	private $_logger;
	private $_errorCode = '';
	private $_errors = array();
	private $_friendlyError = '';
	private $_errorCount = 0;

	public function __construct($logger)
	{
		//setup for log entries
		$this->_logger = $logger;

		$this->_validate = new FrameworkValidation();
	}

	public function validateAPICommon()
	{
		$_REQUEST['key'] = $this->_validate->sanitizeAPIKey($_REQUEST['key']);
		if (isset($_REQUEST['uuid'])) $_REQUEST['uuid'] = $this->_validate->sanitizeUUID($_REQUEST['uuid']);
		if (isset($_REQUEST['appVersion'])) $_REQUEST['appVersion'] = $this->_validate->sanitizeFloat($_REQUEST['appVersion']);
		if (isset($_REQUEST['device'])) $_REQUEST['device'] = $this->_validate->sanitizeTextWithSpace($_REQUEST['device']);
		if (isset($_REQUEST['machine'])) $_REQUEST['machine'] = $this->_validate->sanitizeMachineName($_REQUEST['machine']);
		if (isset($_REQUEST['osVersion'])) $_REQUEST['osVersion'] = $this->_validate->sanitizeFloat($_REQUEST['osVersion']);
		$this->validateKey(TRUE);
		$this->validateUUID(FALSE);
		$this->validateAppVersion(FALSE);
		$this->validateDevice(FALSE);
		$this->validateMachine(FALSE);
		$this->validateOSVersion(FALSE);
	}

	public function validateGetItems()
	{
		$_REQUEST['category'] = $this->_validate->sanitizeUUID($_REQUEST['category']);
		if (isset($_REQUEST['page'])) $_REQUEST['page'] = $this->_validate->sanitizeInteger($_REQUEST['page']);
		$this->validateCategory(TRUE);
		$this->validatePage(FALSE);
	}

	public function validateReadLog()
	{
		$_REQUEST['words'] = $this->_validate->sanitizeInteger($_REQUEST['words']);
		$_REQUEST['speed'] = $this->_validate->sanitizeFloat($_REQUEST['speed']);
		$_REQUEST['rssItemUuid'] = $this->_validate->sanitizeUUID($_REQUEST['rssItemUuid']);
		$this->validateWords(TRUE);
		$this->validateSpeed(TRUE);
		$this->validateRssItemUuid(TRUE);
	}

	public function validateFeedback()
	{
		if (isset($_REQUEST['feedbackType'])) $_REQUEST['feedbackType'] = $this->_validate->sanitizeTextWithSpace($_REQUEST['feedbackType']);
		if (isset($_REQUEST['description'])) $_REQUEST['description'] = $this->_validate->sanitizeSentence($_REQUEST['description']);
		if (isset($_REQUEST['email'])) $_REQUEST['email'] = $this->_validate->sanitizeEmail($_REQUEST['email']);
		$this->validateFeedbackType(FALSE);
		$this->validateDescription(FALSE);
		$this->validateEmail(FALSE);
	}

	private function validateKey($required) {
		if (isset($_REQUEST['key']) && !empty($_REQUEST['key'])) {
			$this->_logger->debug('Checking API Key: ' . $_REQUEST['key']);
			$returnError = $this->_validate->validateAPIKey($_REQUEST['key']);
			if (!empty($returnError)) $this->reportVariableErrors('invalid', 'API Key', $returnError);
		} else if ($required === TRUE) {
			$this->reportVariableErrors('missing', 'API Key', '');
		}
	}

	private function validateUUID($required) {
		if (isset($_REQUEST['uuid']) && !empty($_REQUEST['uuid'])) {
			$this->_logger->debug('Checking uuid: ' . $_REQUEST['uuid']);
			$returnError = $this->_validate->validateUUID($_REQUEST['uuid']);
			if (!empty($returnError)) $this->reportVariableErrors('invalid', 'uuid', $returnError);
		} else if ($required === TRUE) {
			$this->reportVariableErrors('missing', 'uuid', '');
		}
	}

	private function validateAppVersion($required) {
		if (isset($_REQUEST['appVersion']) && !empty($_REQUEST['appVersion'])) {
			$this->_logger->debug('Checking appVersion: ' . $_REQUEST['appVersion']);
			$returnError = $this->_validate->validateVersionNumber($_REQUEST['appVersion']);
			if (!empty($returnError)) $this->reportVariableErrors('invalid', 'appVersion', $returnError);
		} else if ($required === TRUE) {
			$this->reportVariableErrors('missing', 'appVersion', '');
		}
	}

	private function validateDevice($required) {
		if (isset($_REQUEST['device']) && !empty($_REQUEST['device'])) {
			$this->_logger->debug('Checking device: ' . $_REQUEST['device']);
			$returnError = $this->_validate->validateTextWithSpace($_REQUEST['device']);
			if (!empty($returnError)) $this->reportVariableErrors('invalid', 'device', $returnError);
		} else if ($required === TRUE) {
			$this->reportVariableErrors('missing', 'device', '');
		}
	}

	private function validateMachine($required) {
		if (isset($_REQUEST['machine']) && !empty($_REQUEST['machine'])) {
			$this->_logger->debug('Checking machine: ' . $_REQUEST['machine']);
			$returnError = $this->_validate->validateMachineName($_REQUEST['machine']);
			if (!empty($returnError)) $this->reportVariableErrors('invalid', 'machine', $returnError);
		} else if ($required === TRUE) {
			$this->reportVariableErrors('missing', 'machine', '');
		}
	}

	private function validateOSVersion($required) {
		if (isset($_REQUEST['osVersion']) && !empty($_REQUEST['osVersion'])) {
			$this->_logger->debug('Checking osVersion: ' . $_REQUEST['osVersion']);
			$returnError = $this->_validate->validateVersionNumber($_REQUEST['osVersion']);
			if (!empty($returnError)) $this->reportVariableErrors('invalid', 'osVersion', $returnError);
		} else if ($required === TRUE) {
			$this->reportVariableErrors('missing', 'osVersion', '');
		}
	}

	private function validateCategory($required) {
		if (isset($_REQUEST['category']) && !empty($_REQUEST['category'])) {
			$this->_logger->debug('Checking category: ' . $_REQUEST['category']);
			$returnError = $this->_validate->validateUUID($_REQUEST['category']);
			if (!empty($returnError)) $this->reportVariableErrors('invalid', 'category', $returnError);
		} else if ($required === TRUE) {
			$this->reportVariableErrors('missing', 'category', '');
		}
	}

	private function validatePage($required) {
		if (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) {
			$this->_logger->debug('Checking page: ' . $_REQUEST['page']);
			$returnError = $this->_validate->validateInteger($_REQUEST['page']);
			if (!empty($returnError)) $this->reportVariableErrors('invalid', 'page', $returnError);
		} else if ($required === TRUE) {
			$this->reportVariableErrors('missing', 'page', '');
		}
	}

	private function validateWords($required) {
		if ((isset($_REQUEST['words']) && !empty($_REQUEST['words'])) || $_REQUEST['words'] == 0) {
			$this->_logger->debug('Checking words: ' . $_REQUEST['words']);
			$returnError = $this->_validate->validateInteger($_REQUEST['words']);
			if (!empty($returnError)) $this->reportVariableErrors('invalid', 'words', $returnError);
		} else if ($required === TRUE) {
			$this->reportVariableErrors('missing', 'words', '');
		}
	}

	private function validateSpeed($required) {
		if ((isset($_REQUEST['speed']) && !empty($_REQUEST['speed'])) || $_REQUEST['speed'] == 0) {
			$this->_logger->debug('Checking speed: ' . $_REQUEST['speed']);
			$returnError = $this->_validate->validateFloat($_REQUEST['speed']);
			if (!empty($returnError)) $this->reportVariableErrors('invalid', 'speed', $returnError);
		} else if ($required === TRUE) {
			$this->reportVariableErrors('missing', 'speed', '');
		}
	}

	private function validateRssItemUuid($required) {
		if (isset($_REQUEST['rssItemUuid']) && !empty($_REQUEST['rssItemUuid'])) {
			$this->_logger->debug('Checking rssItemUuid: ' . $_REQUEST['rssItemUuid']);
			$returnError = $this->_validate->validateUUID($_REQUEST['rssItemUuid']);
			if (!empty($returnError)) $this->reportVariableErrors('invalid', 'rssItemUuid', $returnError);
		} else if ($required === TRUE) {
			$this->reportVariableErrors('missing', 'rssItemUuid', '');
		}
	}

	private function validateFeedbackType($required) {
		if (isset($_REQUEST['feedbackType']) && !empty($_REQUEST['feedbackType'])) {
			$this->_logger->debug('Checking feedback type: ' . $_REQUEST['feedbackType']);
			$returnError = $this->_validate->validateTextWithSpace($_REQUEST['feedbackType']);
			if (!empty($returnError)) $this->reportVariableErrors('invalid', 'feedbackType', $returnError);
		} else if ($required === TRUE) {
			$this->reportVariableErrors('missing', 'feedbackType', '');
		}
	}

	private function validateDescription($required) {
		if (isset($_REQUEST['description']) && !empty($_REQUEST['description'])) {
			$this->_logger->debug('Checking description: ' . $_REQUEST['description']);
			$returnError = $this->_validate->validateSentence($_REQUEST['description']);
			if (!empty($returnError)) $this->reportVariableErrors('invalid', 'description', $returnError);
		} else if ($required === TRUE) {
			$this->reportVariableErrors('missing', 'description', '');
		}
	}

	private function validateEmail($required) {
		if (isset($_REQUEST['email']) && !empty($_REQUEST['email'])) {
			$this->_logger->debug('Checking email: ' . $_REQUEST['email']);
			$returnError = $this->_validate->validateEmail($_REQUEST['email']);
			if (!empty($returnError)) $this->reportVariableErrors('invalid', 'email', $returnError);
		} else if ($required === TRUE) {
			$this->reportVariableErrors('missing', 'email', '');
		}
	}

	private function validateUsername($required) {
		if (isset($_REQUEST['username']) && !empty($_REQUEST['username'])) {
			$this->_logger->debug('Checking username: ' . $_REQUEST['username']);
			$returnError = $this->_validate->validateUsername($_REQUEST['username']);
			if (!empty($returnError)) $this->reportVariableErrors('invalid', 'username', $returnError);
		} else if ($required === TRUE) {
			$this->reportVariableErrors('missing', 'username', '');
		}
	}

	private function validatePassword($required) {
		if (isset($_REQUEST['password']) && !empty($_REQUEST['password'])) {
			$this->_logger->debug('Checking password: ********');
			$returnError = $this->_validate->validatePassword($_REQUEST['password']);
			if (!empty($returnError)) $this->reportVariableErrors('invalid', 'password', $returnError);
		} else if ($required === TRUE) {
			$this->reportVariableErrors('missing', 'password', '');
		}
	}

	private function validateDate($required) {
		if (isset($_REQUEST['date']) && !empty($_REQUEST['date'])) {
			$this->_logger->debug('Checking date: ' . $_REQUEST['date']);
			$returnError = $this->_validate->validateDate($_REQUEST['date']);
			if (!empty($returnError)) $this->reportVariableErrors('invalid', 'date', $returnError);
		} else if ($required === TRUE) {
			$this->reportVariableErrors('missing', 'date', '');
		}
	}

	private function validateFirstName($required) {
		if (isset($_REQUEST['first_name' ]) && !empty($_REQUEST['first_name'])) {
			$this->_logger->debug('Checking first name: ' . $_REQUEST['first_name']);
			$returnError = $this->_validate->validateName(rtrim($_REQUEST['first_name']));
			if (!empty($returnError)) $this->reportVariableErrors('invalid', 'first_name', $returnError);
		} else if ($required === TRUE) {
			$this->reportVariableErrors('missing', 'first_name', '');
		}
	}

	private function validateLastName($required) {
		if (isset($_REQUEST['last_name' ]) && !empty($_REQUEST['last_name'])) {
			$this->_logger->debug('Checking last name: ' . $_REQUEST['last_name']);
			$returnError = $this->_validate->validateName($_REQUEST['last_name']);
			if (!empty($returnError)) $this->reportVariableErrors('invalid', 'last_name', $returnError);
		} else if ($required === TRUE) {
			$this->reportVariableErrors('missing', 'last_name', '');
		}
	}

	private function validateCost($required) {
		if (isset($_REQUEST['cost' ]) && !empty($_REQUEST['cost'])) {
			$this->_logger->debug('Checking cost: ' . $_REQUEST['cost']);
			$returnError = $this->_validate->validateMoney(trim($_REQUEST['cost']));
			if (!empty($returnError)) $this->reportVariableErrors('invalid', 'cost', $returnError);
		} else if ($required === TRUE) {
			$this->reportVariableErrors('missing', 'cost', '');
		}
	}

	private function reportVariableErrors($type, $variable, $returnError) {
		if ($type === 'invalid') {
			$this->_errorCode = 'invalidParameter';
			$this->_errors[] = $returnError . ' value for ' . $variable . ' (' . $_REQUEST[$variable] . ')';
			$this->_friendlyError = 'Invalid value or format for one of the submitted parameters.';
			$this->_errorCount++;
		} else if ($type === 'missing') {
			$this->_errorCode = 'missingParameter';
			$this->_errors[] = 'Required parameter ' . $variable . ' is missing from request.';
			$this->_friendlyError = 'A required parameter is missing from this request.';
			$this->_errorCount++;
		}
	}

	public function getErrorCode() {
		return $this->_errorCode;
	}

	public function getErrors() {
		return $this->_errors;
	}

	public function getFriendlyError() {
		return $this->_friendlyError;
	}

	public function getErrorCount() {
		return $this->_errorCount;
	}
}
