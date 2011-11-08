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

abstract class AdelieDebug_Controller extends AdelieDebug_Core_Controller
{
	protected function _getTemplateValues()
	{
		return array_merge(parent::_getTemplateValues(), array(
			'xoopsUrl' => XOOPS_URL,
		));
	}
}
