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

abstract class AdelieDebugCompiler_Archiver_Plugin
{
	protected $app = null;
	protected $path = '';

	public function __construct(AdelieDebugCompiler_Application $app)
	{
		$this->app = $app;
	}

	public function setPath($path)
	{
		$this->path = $path;
	}

	abstract public function getContents();

	protected function _getContents()
	{
		$contents = file_get_contents($this->path);
		
		if ( $contents === false )
		{
			throw new RuntimeException("Failed to read file: {$this->path}");
		}

		return $contents;
	}
}
