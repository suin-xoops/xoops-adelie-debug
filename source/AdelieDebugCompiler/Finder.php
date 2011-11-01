<?php
/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebugCompiler
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class AdelieDebugCompiler_Finder
{
	public static function find($dir)
	{
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::SELF_FIRST);
		return self::_filter($files);
	}

	public static function ls($dir)
	{
		$files = new DirectoryIterator($dir);
		return self::_filter($files);
	}

	protected static function _filter(Traversable $files)
	{
		$_files = array();

		foreach ( $files as $k => $file )
		{
			$filename = $file->getPathname();
			$extension = pathinfo($filename, PATHINFO_EXTENSION);

			if ( $file->isFile() === false )
			{
				continue;
			}

			$_files[] = $filename;
		}
		
		return $_files;
	}
}
