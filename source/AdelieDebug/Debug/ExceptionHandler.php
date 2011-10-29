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

class AdelieDebug_Debug_ExceptionHandler
{
	protected $logger = null;

	public function __construct(AdelieDebug_Debug_Logger $logger)
	{
		$this->logger = $logger;
	}

	public function register()
	{
		set_exception_handler(array($this, 'catchException'));
	}

	public function catchException(Exception $exception)
	{
		$this->logger->addPhpError(strval($exception));
	}
}
