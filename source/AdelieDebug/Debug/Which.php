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

class AdelieDebug_Debug_Which
{
	public function __construct()
	{
	}

	public function which($variable)
	{
		$type = gettype($variable);
		$method = 'which'.ucfirst($type);

		if ( method_exists($this, $method) === true )
		{
			$result = $this->$method($variable);
		}
		else
		{
			$result = false;
		}

		if ( $result === false )
		{
			$result = "Not found: ".$type;
		}

		$result = 'Which: '.$result;

		return $result;
	}

	public function whichObject($object)
	{
		return $this->whichClass(get_class($object));
	}

	public function whichString($string)
	{
		if ( preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $string) > 0 )
		{
			$found = array();
		
			if ( class_exists($string) === true )
			{
				$found[] = $this->whichClass($string);
			}

			if ( function_exists($string) === true )
			{
				$found[] = $this->whichFunction($string);
			}
			
			if ( defined($string) === true )
			{
				// 定数
			}
			
			$found = implode("\n", $found);
			
			return $found;
		}
	
		return false;
	}

	public function whichClass($class)
	{
		$reflectionClass = new ReflectionClass($class);
		$filename = $reflectionClass->getFileName();
		$line     = $reflectionClass->getStartLine();

		if ( $filename === false )
		{
			$filename = 'unknown';
		}

		if ( $line === false )
		{
			$line = '0';
		}

		return sprintf("Class '%s' defined in %s at line %s", $class, $filename, $line);
	}

	public function whichFunction($function)
	{
		$reflectionFunction = new ReflectionFunction($function);
		$filename = $reflectionFunction->getFileName();
		$line     = $reflectionFunction->getStartLine();

		if ( $filename === false )
		{
			$filename = 'unknown';
		}

		if ( $line === false )
		{
			$line = '0';
		}

		return sprintf("Function '%s' defined in %s at line %s", $function, $filename, $line);
	}
}
