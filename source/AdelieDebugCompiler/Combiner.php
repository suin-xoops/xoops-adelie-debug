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

abstract class AdelieDebugCompiler_Combiner
{
	protected $app = null;
	protected $targetDir = '';
	protected $contents = '';

	public function __construct(AdelieDebugCompiler_Application $app)
	{
		$this->app       = $app;
		$this->targetDir = $app->targetDir;
	}

	/**
	 * run function.
	 * 
	 * @access public
	 * @abstract
	 * @return void
	 */
	abstract public function run();

	/**
	 * getContents function.
	 * 
	 * @access public
	 * @abstract
	 * @return string
	 */
	public function getContents()
	{
		return $this->contents;
	}

	protected function _find($dir, array $extensions = array())
	{
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::SELF_FIRST);
		return $this->_filter($files, $extensions);
	}

	protected function _list($dir, array $extensions = array())
	{
		$files = new DirectoryIterator($dir);
		return $this->_filter($files, $extensions);
	}

	protected function _filter(Traversable $files, array $extensions = array())
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
			
			if ( count($extensions) > 0 and in_array($extension, $extensions) === false )
			{
				continue;
			}

			$_files[] = $filename;
		}
		
		return $_files;
	}
}
