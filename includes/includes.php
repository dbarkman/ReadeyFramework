<?php

/**
 * includes.php
 * Description:
 *
 */

require_once dirname(__FILE__) . '/../config/config.php';
require_once dirname(__FILE__) . '/../config/dbConfig.php';

spl_autoload_register(function ($class) {
	require_once dirname(__FILE__) . '/../classes/' . $class . '.class.php';
});
