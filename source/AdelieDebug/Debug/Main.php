<?php
/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class AdelieDebug_Debug_Main
{
	protected $logger           = null;
	protected $errorHandler     = null;
	protected $exceptionHandler = null;
	protected $reporter         = null;
	protected $shutdown         = null;

	public function __construct()
	{
		// なんもしない
	}

	public function __isset($name)
	{
		return isset($this->$name);
	}

	public function __get($name)
	{
		return $this->$name;
	}

	public function run()
	{
		$this->enableErrorReporting();
		$this->_setUp();
	}

	public function enableErrorReporting()
	{
/*		if ( version_compare(PHP_VERSION, '5.3', '>=') === true )
		{
			error_reporting(E_ALL ^ E_DEPRECATED); // TODO >> 設定可能に
		}
		else
		{
*/
			error_reporting(E_ALL); // TODO >> 設定可能に
//		}

		ini_set('log_errors', true);
		ini_set('display_errors', true);
	}

	protected function _setUp()
	{
		$this->_setUpLogger();
		$this->_setUpErrorHandler();
		$this->_setUpExceptionHandler();
		$this->_setUpReporter(); // シャットダウン時にReporterをnewすると既にメモリが足りなくなっている可能性があるため予めメモリを確保しておく
		$this->_setUpShutdown();
		$this->_setUpFunctions();
	}

	protected function _setUpLogger()
	{
		$this->logger = new AdelieDebug_Debug_Logger();
	}

	protected function _setUpErrorHandler()
	{
		$this->errorHandler = new AdelieDebug_Debug_ErrorHandler($this->logger);
		$this->errorHandler->register();
	}

	protected function _setUpExceptionHandler()
	{
		$this->exceptionHandler = new AdelieDebug_Debug_ExceptionHandler($this->logger);
		$this->exceptionHandler->register();
	}

	protected function _setUpReporter()
	{
		$this->reporter = new AdelieDebug_Debug_Reporter_Html($this->logger); // TODO >> リポータの種類を設定できるようにする
		$this->reporter->setUp();
	}

	protected function _setUpShutdown()
	{
		$this->shutdown = new AdelieDebug_Debug_Shutdown($this->reporter);
		$this->shutdown->register();
	}

	protected function _setUpFunctions()
	{
		AdelieDebug_Debug_Dump::setLogger($this->logger);
		AdelieDebug_Debug_Trace::setLogger($this->logger);
		AdelieDebug_Debug_Synopsys::setLogger($this->logger);
		$this->_loadFunctions();
	}

	protected function _loadFunctions()
	{
		if ( defined('ADELIE_DEBUG_BUILD') === true )
		{
			eval(AdelieDebug_Archive::$archive['/AdelieDebug/Debug/Function.php']);
		}
		else
		{
			require_once dirname(__FILE__).'/Function.php';
		}
	}
}
