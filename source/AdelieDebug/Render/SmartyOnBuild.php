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
		if ( array_key_exists($this->tempalte, AdelieDebug_Build_Template::$templates) === false )
		{
			throw new RuntimeException("Template not found: ".$this->tempalte);
		}

		return 'string:'.AdelieDebug_Build_Template::$templates[$this->tempalte];
	}
}
