<?php

require_once dirname(__FILE__).'/source/AdelieDebug/Core/ClassLoader.php';

if ( file_exists('compile.config.php') === true )
{
	$config = require 'compile_config.php';
}
else
{
	$config = require 'compile_config.dist.php';
}

$classLoader = new AdelieDebug_Core_ClassLoader();
$classLoader->setIncludePath(dirname(__FILE__).'/source');
$classLoader->register();

$application = new AdelieDebugCompiler_Application($config);
$application->setUp();
$application->run();
$result = $application->getResult();

