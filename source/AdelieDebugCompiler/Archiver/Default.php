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

class AdelieDebugCompiler_Archiver_Default extends AdelieDebugCompiler_Archiver_Plugin
{
	public function getContents()
	{
		$contents = $this->_getContents();
		return $contents;
	}
}
