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

class AdelieDebugCompiler_Combiner_Template extends AdelieDebugCompiler_Combiner
{
	protected $targetExtensions = array('tpl');

	public function run()
	{
		$templateDir   = $this->targetDir;
		$templateFiles = $this->_find($templateDir, $this->targetExtensions);

		$start = strlen($this->targetDir) + 1;

		$templates = array();

		foreach ( $templateFiles as $templateFile )
		{
			$contents = file_get_contents($templateFile);
			
			if ( $contents === false )
			{
				throw new RuntimeException("Failed to read file: $templateFile");
			}
			
			$name = substr($templateFile, $start, -4);
			$templates[$name] = $contents;
		}

		$templates = var_export($templates, true);
		$class = sprintf('class AdelieDebug_Build_Template { public static $sources = %s; }', $templates);
		
		if ( $this->app->syntaxChecker->checkCode($class) === false )
		{
			throw new RuntimeException("Syntax error: ".$this->app->syntaxChecker->getErrorMessage());
		}
		
		$this->contents = $class;
	}
}
