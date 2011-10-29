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

class AdelieDebug_Render_Smarty extends AdelieDebug_Render
{
	public function render()
	{
		$template = ADELIE_DEBUG_DIR.'/'.$this->template.'.tpl';

		if ( file_exists($template) === false )
		{
			throw new RuntimeException("Template not found: ".$template);
		}

		$values = $this->getValues();
		$smarty = $this->_getSmarty();
		$smarty->assign($values);
		$result = $smarty->fetch($template);
		return $result;
	}

	protected function _getSmarty()
	{
		return new AdelieDebug_Library_Smarty();
	}
}
