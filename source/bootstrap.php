<?php

require_once dirname(__FILE__).'/AdelieDebug/Core/ClassLoader.php';
$classLoader = new AdelieDebug_Core_ClassLoader();
$classLoader->setIncludePath(dirname(__FILE__));
$classLoader->register();

$application = new AdelieDebug_Application();
$application->run();
$result = $application->getResult();
echo $result;
