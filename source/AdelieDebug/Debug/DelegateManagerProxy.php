<?php

class AdelieDebug_Debug_DelegateManagerProxy extends XCube_DelegateManager
{
	protected $logger = null;

	public function __construct(XCube_DelegateManager $delegateManager, AdelieDebug_Debug_Logger $logger)
	{
		$this->_mCallbacks = $delegateManager->_mCallbacks;
		$this->_mCallbackParameters = $delegateManager->_mCallbackParameters;
		$this->_mDelegates = $delegateManager->_mDelegates;
		$this->logger = $logger;
	}

	public function register($name, &$delegate)
	{
		$observer = array(new AdelieDebug_Debug_EventObserver($name, $this->logger), 'invoke');
		$this->add($name, $observer, -9999);
		return parent::register($name, $delegate);
	}
}
