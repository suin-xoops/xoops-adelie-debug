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

class AdelieDebugCompiler_Compresser
{
	protected $app = null;

	public function __construct($app)
	{
		$this->app = $app;
	}

	public function compressPHP($contents)
	{
		$filename = $this->app->temporaryFileManager->create($contents);
		$contents = php_strip_whitespace($filename);
		return $contents;
	}
}
