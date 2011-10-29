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

class AdelieDebug_Debug_Reporter_Html extends AdelieDebug_Debug_Reporter
{
	protected $reportable = null;

	protected $hasContent = false;

	public function setUp()
	{
		$this->reportable = new AdelieDebug_Debug_Reporter_Html_Reportable();
	}

	public function report()
	{
		if ( $this->reportable->isReportable() === false )
		{
			return;
		}

		$this->_flushObContents();
		$this->_printContents();
	}

	protected function _flushObContents()
	{
		$contents = '';

		while ( ob_get_level() > 0 )
		{
			$contents .= ob_get_clean();
			$this->hasContent = true;
		}

		echo $contents;
	}

	protected function _printContents()
	{
		// TODO >> メモリ足りないときは素でvar_dump()する
		$application = new AdelieDebug_Application();
		$application->setPathinfo('/debug/report');
		$application->setParameter('logger', $this->logger);
		$application->setParameter('via', __CLASS__);
		$application->setUp();
		$application->run();
		$result = $application->getResult();
		echo $result;
	}
}
