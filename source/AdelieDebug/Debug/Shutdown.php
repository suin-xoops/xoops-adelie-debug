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

class AdelieDebug_Debug_Shutdown
{
	protected $callbacks = array();

	public function add($callback)
	{
		$this->callbacks[] = $callback;
	}

	public function register()
	{
		register_shutdown_function(array($this, 'report'));
	}

	public function report()
	{	
		foreach ( $this->callbacks as $callback )
		{
			call_user_func($callback);
		}
	}
}
