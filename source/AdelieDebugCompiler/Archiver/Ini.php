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

class AdelieDebugCompiler_Archiver_Ini extends AdelieDebugCompiler_Archiver_Plugin
{
	public function getContents()
	{
		$contents = parse_ini_file($this->path, true);
		$contents = '<?php return '.var_export($contents, true).';';
		$contents = $this->_minimize($contents);
		$contents = $this->_removePhpTag($contents);
		return $contents;
	}

	protected function _minimize($contents)
	{
		$compresser = new AdelieDebugCompiler_Compresser($this->app);
		return $compresser->compressPHP($contents);
	}

	protected function _removePhpTag($contents)
	{
		return trim(substr($contents, 5)); // this removes '<?php'
	}
}
