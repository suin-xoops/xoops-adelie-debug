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

class AdelieDebugCompiler_TemporaryFileManager
{
	protected $files = array();

	public function __construct()
	{
	}

	public function __destruct()
	{
		foreach ( $this->files as $file )
		{
			unlink($file);
		}
	}

	public function create($contents = '')
	{
		$tempFile = tempnam(sys_get_temp_dir(), 'adc');

		if ( file_put_contents($tempFile, $contents) === false )
		{
			throw new RuntimeException("Failed to create temp file: $tempFile");
		}

		$this->files[] = $tempFile;

		return $tempFile;
	}
}
