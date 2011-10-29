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

abstract class AdelieDebug_Core_Render
{
	protected $app      = null;
	protected $values   = array();
	protected $template = '';

	public function __construct(AdelieDebug_Core_Application $app)
	{
		$this->app = $app;
	}

	public function setValues(array $values)
	{
		$this->values = $values;
	}

	public function getValues()
	{
		return $this->values;
	}

	public function setTemplate($template)
	{
		$this->template = $template;
	}

	public function getTemplate()
	{
		return $this->template;
	}

	public function render()
	{
		return '';
	}
}
