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

if ( class_exists('Smarty') === false )
{
	if ( file_exists(XOOPS_ROOT_PATH.'/class/smarty/Smarty.class.php') === true )
	{
		// XOOPS Cube Legacy 2.1
		require_once XOOPS_ROOT_PATH.'/class/smarty/Smarty.class.php';
	}
	elseif ( file_exists(XOOPS_TRUST_PATH.'/libs/smarty/Smarty.class.php') === true )
	{
		// XOOPS Cube Legacy 2.2
		require_once XOOPS_TRUST_PATH.'/libs/smarty/Smarty.class.php';
	}
	else
	{
		// TOKYOPen
		require_once XOOPS_TRUST_PATH.'/vendor/smarty/Smarty.class.php';
	}
}

class AdelieDebug_Library_Smarty extends Smarty
{
	public function __construct()
	{
		parent::__construct();

		$this->compile_id         = null;
		$this->_canUpdateFromFile = true;
		$this->compile_check      = true;
		$this->compile_dir        = XOOPS_COMPILE_PATH;
		$this->left_delimiter     = '<{';
		$this->right_delimiter    = '}>';
		$this->force_compile      = false;
	}
}
