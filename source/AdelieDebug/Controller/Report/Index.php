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

class AdelieDebug_Controller_Report_Index extends AdelieDebug_Controller
{
	protected $logger = null;

	public function setUp()
	{
		$this->_setUpLogger();
	}

	public function run()
	{
		$this->_checkAccess();

		if ( isset($_SESSION) === false )
		{
			$_SESSION = array();
		}

		$this->output['sentHeaders'] = headers_list();
		$this->output['requests'] = array(
			'$_GET'     => $_GET, 
			'$_POST'    => $_POST, 
			'$_SESSION' => $_SESSION,
			'$_COOKIE'  => $_COOKIE,
			'$_FILES'   => $_FILES,
			'$_SERVER'  => $_SERVER,
		);
		$this->output['logs'] = $this->_getLogs();
		$this->output['errorSummary'] = $this->logger->getErrorSummary();
		$this->output['css'] = $this->app->fileGetContents('/File/css/report.css');

		$phpinfo = new AdelieDebug_Debug_PHPInfo();
		$this->output['phpInfo'] = $phpinfo->summary();
		
		$this->_render();
	}

	protected function _setUpLogger()
	{
		$this->logger = $this->app->parameter('logger');
	}

	protected function _checkAccess()
	{
		if ( $this->app->parameter('via') === null )
		{
			throw new RuntimeException("予期せぬアクセス方法です。");
		}
	}

	protected function _renderTheme($content)
	{
		return $content['content'];
	}

	protected function _getLogs()
	{
		$logs = $this->logger->getLogs();

		$last = end($logs);
		$lastTime = $last['time'];

		foreach ( $logs as $key => $log )
		{
			if ( isset($logs[$key + 1]['time']) === true )
			{
				$time = $logs[$key + 1]['time'];
			}
			else
			{
				$time = $log['time'];
			}
		
			$timePer  = ( $time / $lastTime ) * 100;
			$timeRate = ( $time * 800 ) /  $lastTime;
			$logs[$key]['timePer']  = $timePer;
			$logs[$key]['timeRate'] = $timeRate;
		}

		return $logs;
	}
}
