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

class AdelieDebug_Application extends AdelieDebug_Core_Application
{
	protected $pathinfo = null;

	/**
	 * setUp function.
	 * 
	 * @access public
	 * @return void
	 */
	public function setUp()
	{
		parent::setUp();
	}

	public function setPathinfo($pathinfo)
	{
		$this->pathinfo = $pathinfo;
	}

	protected function _resolve()
	{
		if ( $this->pathinfo === null )
		{
			$this->pathinfo = $this->request->getPathinfo();
		}

		$parameters = $this->router->resolve($this->pathinfo);

		if ( $parameters === false )
		{
			throw new AdelieDebug_Exception_NotFoundException('Route not found: '.$this->pathinfo);
		}

		$this->parameters = array_merge($this->parameters, $parameters);
	}
}