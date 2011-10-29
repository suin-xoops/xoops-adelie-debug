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

class AdelieDebug_Debug_Reporter
{
	protected $logger = null;

	public function __construct(AdelieDebug_Debug_Logger $logger)
	{
		$this->logger = $logger;
	}

	public function setUp()
	{
		// テンプレートメソッド
	}

	public function report()
	{
		echo '<pre style="text-align:left;">';
		var_dump($this->logger->getLogs());
		echo '</pre>';
	}
}
