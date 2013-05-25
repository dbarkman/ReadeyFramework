<?php

session_start();

require_once dirname(__FILE__) . '/includes/includes.php';

echo time() . PHP_EOL;
echo UUID::getUUID() . PHP_EOL;
