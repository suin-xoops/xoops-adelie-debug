<?php

class AdelieDebug_Preload extends XCube_ActionFilter
{
	protected $debugger = null;

	public function preFilter()
	{
		$this->_bootstrap();
		$this->_setUp();
		$this->_registerEventListeners();
	}

	public function topAccessEventHandler()
	{
		if ( $this->_isAdelieDebugPage() === false )
		{
			return;
		}

		$application = new AdelieDebug_Application();
		$application->setUp();
		$application->run();
		$result = $application->getResult();
		echo $result;
		die;
	}

	public function setupDebugEventHandler($instance, $debugMode)
	{
		$instance = new AdelieDebug_Debug_XoopsDebugger($this->debugger->logger);
		$this->debugger->enableErrorReporting(); // Legacy_Controller::_setupDebugger() で error_reproting = 0 にされちゃってるので必要
	}

	protected function _bootstrap()
	{
		// TODO >> コンパイル時はこのメソッド不要
		require_once dirname(__FILE__).'/AdelieDebug/Core/ClassLoader.php';
		$classLoader = new AdelieDebug_Core_ClassLoader();
		$classLoader->setIncludePath(dirname(__FILE__));
		$classLoader->register();
	}

	protected function _setUp()
	{
		$this->debugger = new AdelieDebug_Debug_Main();
		$this->debugger->run();
	}

	protected function _registerEventListeners()
	{
		$this->mRoot->mDelegateManager->add('Legacypage.Top.Access', array($this, 'topAccessEventHandler'), 0);
		$this->mController->mSetupDebugger->add(array($this, 'setupDebugEventHandler'), 99999);
	}

	protected function _isAdelieDebugPage()
	{
		return ( strpos($_SERVER['REQUEST_URI'], 'index.php/debug') !== false );
	}
}

