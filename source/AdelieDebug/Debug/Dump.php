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

class AdelieDebug_Debug_Dump
{
	protected static $logger = null;

	public static function setLogger(AdelieDebug_Debug_Logger $logger)
	{
		self::$logger = $logger;
	}

	public static function dump()
	{
		$called = AdelieDebug_Debug_Trace::getCalled(0);
		$values = func_get_args();
		$result = self::_dump_html($called, $values);
		self::$logger->addDump($result);
	}

	public static function dumpbt($level = 0)
	{
		$level  = $level + 1;
		$called = AdelieDebug_Debug_Trace::getCalled($level);
		$values = func_get_args();
		array_shift($values);
		$result =  self::_dump_html($called, $values);
		self::$logger->addDump($result);
	}

	protected static function _dump_html($called, $values)
	{
		ob_start();
		echo '<pre style="border:1px dotted #000; font-size:12px; color:#000; background:#fff; font-family:"Times New Roman",Georgia,Serif;">';
		echo $called;
		echo '<div style="text-align:left;">';
		array_map('var_dump', $values);
		echo '</div>';
		echo '</pre>';
		return ob_get_clean();
	}
}
