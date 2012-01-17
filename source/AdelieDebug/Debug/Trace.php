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

	public static function trace($minus = 0, $return = false)
	{
		// debug_print_backtrace()がメモリオーバーになる可能性があるので、例外のトレース機能を使う
		$exception = new Exception();
		$trace = $exception->getTraceAsString();

		for ( $i = 0; $i < $minus; $i ++ )
		{
			$trace = preg_replace("/.*\n#1([^\d])/s", '#1$1', $trace);
			$trace = preg_replace ('/^#(\d+)/me', '\'#\' . ($1 - 1)', $trace);
		}

		if ( $return === true )
		{
			return $trace;
		}
		else
		{
			self::$logger->addTrace($trace);
		}
	}

	public static function getCalled($level = 0, $html = true)
	{
		$level = $level + 1;
		$trace = array(
			'file' => 'Unknown file',
			'line' => 0,
		);
		
		$traces = debug_backtrace();

		if ( isset($traces[$level]) === true )
		{
			$trace = array_merge($trace, $traces[$level]);
		}
		
		$called = sprintf("Called in %s on line %s", $trace['file'], $trace['line']);

		if ( $html === true )
		{
			$called = '<div style="font-size:10px; background:#ddd;text-align:left;">'.$called."</div>";
		}

		return $called;
	}
}
