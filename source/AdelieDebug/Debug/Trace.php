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

class AdelieDebug_Debug_Trace
{
	protected static $logger = null;

	public static function setLogger(AdelieDebug_Debug_Logger $logger)
	{
		self::$logger = $logger;
	}

	public function trace($minus = 0)
	{
		ob_start();
		debug_print_backtrace();
		$trace = ob_get_clean();

		for ( $i = 0; $i < $minus; $i ++ )
		{
			$trace = preg_replace("/.*\n#1/s", '#1', $trace);
			$trace = preg_replace ('/^#(\d+)/me', '\'#\' . ($1 - 1)', $trace);
		}
		
		self::$logger->addTrace($trace);
	}
}
