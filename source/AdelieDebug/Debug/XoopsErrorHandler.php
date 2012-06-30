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

$reflectionMethod = new ReflectionMethod('XoopsErrorHandler', 'getInstance');

if ( $reflectionMethod->isStatic() === true )
{
	class AdelieDebug_Debug_XoopsErrorHandler extends XoopsErrorHandler
	{
		public function __construct()
		{
			// 親のコンストラクタの処理を封じる
		}

		public static function getInstance()
		{
			static $instance = null;

			if ( $instance === null )
			{
				$instance = new self();
			}

			return $instance;
		}
	}
}
else
{
	class AdelieDebug_Debug_XoopsErrorHandler extends XoopsErrorHandler
	{
		public function __construct()
		{
			// 親のコンストラクタの処理を封じる
		}

		public function getInstance()
		{
			static $instance = null;

			if ( $instance === null )
			{
				$instance = new self();
			}

			return $instance;
		}
	}
}



