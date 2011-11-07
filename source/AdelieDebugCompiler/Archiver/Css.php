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

class AdelieDebugCompiler_Archiver_Css extends AdelieDebugCompiler_Archiver_Plugin
{
	public function getContents()
	{
		$contents = $this->_minimize();
		return $contents;
	}

	protected function _minimize()
	{
		$yuicompressor = $this->app->config('yuicompressor');
		$command = sprintf('%s %s 2>&1', $yuicompressor, $this->path);
		exec($command, $output, $result);

		$output = implode("\n", $output);

		if ( $result > 0 )
		{
			throw new RuntimeException("YUI Compressor Error: ".$output);
		}
		
		return $output;
	}
}
