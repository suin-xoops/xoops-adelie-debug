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

class AdelieDebug_Debug_XoopsDebugger extends Legacy_AbstractDebugger
{
	protected $logger = null;
	protected $isDebugRenderSystem = false;

	public function __construct(AdelieDebug_Debug_Logger $logger)
	{
		$this->logger = $logger;
	}

	public function enableDebugRenderSystem()
	{
		$this->isDebugRenderSystem = true;
	}

	public function prepare()
	{
		$GLOBALS['xoopsErrorHandler'] =& AdelieDebug_Debug_XoopsErrorHandler::getInstance();
		$GLOBALS['xoopsErrorHandler']->activate(false);

		$xoopsLogger = AdelieDebug_Debug_XoopsLogger::getInstance();
		$xoopsLogger->setLogger($this->logger);
		$xoopsLogger->importParent();
		$GLOBALS['xoopsLogger'] =& $xoopsLogger;
		$root = XCube_Root::getSingleton();
		$root->mController->mLogger = $xoopsLogger;
		$root->mController->mDB->setLogger($xoopsLogger);
	}

	public function isDebugRenderSystem()
	{
		return $this->isDebugRenderSystem;
	}
}
