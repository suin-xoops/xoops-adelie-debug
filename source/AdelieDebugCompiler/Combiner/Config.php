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

class AdelieDebugCompiler_Combiner_Config extends AdelieDebugCompiler_Combiner
{
	protected $targetExtensions = array('ini');

	public function run()
	{
		$configFiles = $this->_list($this->targetDir.'/Config', $this->targetExtensions);
		
		$configs = array();
		
		foreach ( $configFiles as $configFile )
		{
			$name     = pathinfo($configFile, PATHINFO_FILENAME);
			$contents = parse_ini_file($configFile, true);
			$configs[$name] = $contents;
		}

		$configs = var_export($configs, true);
		$class = sprintf('class AdelieDebug_Compiled_Config { public $configs = %s; }', $configs);
		
		if ( $this->app->syntaxChecker->checkCode($class) === false )
		{
			throw new RuntimeException("Syntax error: ".$this->app->syntaxChecker->getErrorMessage());
		}

		$this->contents = $class;
	}
}
