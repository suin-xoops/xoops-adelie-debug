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

class AdelieDebugCompiler_Archiver
{
	protected $app = null;

	protected $archive = array();

	public function __construct(AdelieDebugCompiler_Application $app)
	{
		$this->app = $app;
	}

	public function add($path)
	{
		$name = substr($path, strlen(dirname($this->app->targetDir)));
		$this->archive[$name] = $this->_getContents($path);
	}

	public function archive()
	{
		$template = file_get_contents($this->app->dir.'/Resource/Archive.php');
		$archive  = var_export($this->archive, true);
		$template = str_replace('{archive}', $archive, $template);
		return $template;
	}

	protected function _getContents($path)
	{
		$plugin = $this->_getPluginClass($path);

		if ( class_exists($plugin) === true )
		{
			$plugin = new $plugin($this->app);
		}
		else
		{
			$plugin = new AdelieDebugCompiler_Archiver_Default($this->app);
		}

		$plugin->setPath($path);
		return $plugin->getContents();
	}

	protected function _getPluginClass($path)
	{
		$extension = pathinfo($path, PATHINFO_EXTENSION);
		$extension = strtolower($extension);
		$extension = ucfirst($extension);
		return sprintf('AdelieDebugCompiler_Archiver_%s', $extension);
	}
}
