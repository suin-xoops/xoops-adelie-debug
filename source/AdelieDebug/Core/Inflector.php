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

class AdelieDebug_Core_Inflector
{
	public static function camelize($string)
	{
		$string = self::pascalize($string);
		$string[0] = strtolower($string[0]);
		return $string;
	}

	public static function pascalize($string)
	{
		$string = strtolower($string);
		$string = str_replace('_', ' ', $string);
		$string = ucwords($string);
		$string = str_replace(' ', '', $string);
		return $string;
	}

	public static function snakeCase($string)
	{
		$string = preg_replace('/([A-Z])/', '_$1', $string);
		$string = strtolower($string);
		return ltrim($string, '_');
	}

	public static function snakeCaseUpper($string)
	{
		$string = self::snakeCase($string);
		$string = strtoupper($string);
		return $string;
	}

	public static function snakeCaseLower($string)
	{
		$string = self::snakeCase($string);
		$string = strtolower($string);
		return $string;
	}

	public static function humanize($string)
	{
		$string = strtr($string, '_', ' ');
		$string = strtr($string, '.', ' ');
		$string = preg_replace('/([A-Z])/', ' $1', $string);
		$string = ucwords($string);
		return $string;
	}
}
