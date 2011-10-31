<?php

require_once dirname(__FILE__).'/source/AdelieDebug/Core/ClassLoader.php';

$classLoader = new AdelieDebug_Core_ClassLoader();
$classLoader->setIncludePath(dirname(__FILE__).'/source');
$classLoader->register();

$application = new AdelieDebugCompiler_Application();
$application->setUp();
$application->run();
$result = $application->getResult();

