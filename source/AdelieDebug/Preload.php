<?php

class AdelieDebug_Preload extends XCube_ActionFilter
{
	/** @var AdelieDebug_Debug_Main */
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

	public function setupDebugEventHandler(&$instance, $debugMode)
	{
		$instance = new AdelieDebug_Debug_XoopsDebugger($this->debugger->logger);

		if ( $debugMode === XOOPS_DEBUG_SMARTY )
		{
			$instance->enableDebugRenderSystem();
		}

		$this->debugger->enableErrorReporting(); // Legacy_Controller::_setupDebugger() で error_reproting = 0 にされちゃってるので必要
	}

	public function addOutputFilterToXoopsTpl(Smarty $xoopsTpl)
	{
		if ( method_exists($xoopsTpl, 'registerFilter') === true )
		{
			$xoopsTpl->registerFilter('output', array($this, 'filterSmartyOutput'));
		}
		else
		{
			$xoopsTpl->register_outputfilter(array($this, 'filterSmartyOutput'));
		}
	}

	protected function _bootstrap()
	{
		if ( defined('ADELIE_DEBUG_BUILD') === true )
		{
			$classLoader = new AdelieDebug_Archive_ClassLoader();
			$classLoader->setIncludePath(':eval:');
			$classLoader->register();
		}
		else
		{
			require_once dirname(__FILE__).'/Core/ClassLoader.php';
			$classLoader = new AdelieDebug_Core_ClassLoader();
			$classLoader->setIncludePath(dirname(dirname(__FILE__)));
			$classLoader->register();
		}
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
		$this->mRoot->mDelegateManager->add('XoopsTpl.New', array($this, 'addOutputFilterToXoopsTpl'));
	}

	protected function _isAdelieDebugPage()
	{
		return ( strpos($_SERVER['REQUEST_URI'], 'index.php/debug') !== false );
	}

	public function filterSmartyOutput($output, $xoopsTpl)
	{
		$steps = debug_backtrace();

		foreach ( $steps as $step )
		{
			if ( $step['function'] === 'fetch' and $step['class'] === 'Smarty' and isset($step['args'][0]) === true )
			{
				$this->debugger->logger->addView(sprintf('Render template: %s', $step['args'][0]));
				return $output;
			}
		}

		// 上のループで見つからなかった場合
		$this->debugger->logger->addView('Render a template, but template name is unknown...');

		return $output;
	}
}
