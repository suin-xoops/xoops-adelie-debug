<?php
/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class AdelieDebug_Render_SmartyOnBuild extends AdelieDebug_Render_Smarty
{
	protected function _getTempalte()
	{
		$template = '/AdelieDebug/'.$this->template.'.tpl';
	
		if ( array_key_exists($template, AdelieDebug_Archive::$archive) === false )
		{
			throw new RuntimeException("Template not found: ".$template);
		}

		$filename = XOOPS_CACHE_PATH.'/'.trim(strtr($template, '/', '_'), '_');

		if ( file_exists($filename) === false or filemtime($filename) < ADELIE_DEBUG_BUILD_TIME )
		{
			file_put_contents($filename, AdelieDebug_Archive::$archive[$template]);
		}

		return $filename;
	}
}
