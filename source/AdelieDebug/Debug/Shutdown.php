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

class AdelieDebug_Debug_Shutdown
{
	protected $reporter = null;

	public function __construct(AdelieDebug_Debug_Reporter $reporter)
	{
		$this->reproter = $reporter;
	}

	public function register()
	{
		register_shutdown_function(array($this, 'report'));
	}

	public function report()
	{
		$this->reproter->report();
	}
}
