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

abstract class AdelieDebug_Controller_Error extends AdelieDebug_Controller
{
	protected $statusCode    = 500;
	protected $statusMessage = "500 Internal Server Error";

	public function run()
	{
		header('HTTP', true, $this->statusCode);
		$this->output['statusCode']    = $this->statusCode;
		$this->output['statusMessage'] = $this->statusMessage;
		$this->output['exception']     = $this->app->parameter('exception');
		$this->_render();
	}
}
