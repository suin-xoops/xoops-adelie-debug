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

class AdelieDebug_Core_IniParser
{
	public static function parseFile($filename, $processSection = true, $mode = null)
	{
		$arguments = array($filename, true);

		if ( version_compare(PHP_VERSION, '5.3', '<') and $mode !== null )
		{
			$arguments[] = $mode; // PHP5.3以上
		}

		return call_user_func_array('parse_ini_file', $arguments);
	}
}
