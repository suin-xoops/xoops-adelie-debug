<?php

class AdelieDebug_Debug_EventObserver
{
	protected $name   = '';
	protected $logger = null;

	public function __construct($name, AdelieDebug_Debug_Logger $logger)
	{
		$this->name   = $name;
		$this->logger = $logger;
	}

	public function invoke()
	{
		$this->logger->addTriggerDelegate($this->name);
	}
}
