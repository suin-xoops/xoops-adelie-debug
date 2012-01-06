<?php
/**
 * AdelieDebug\Debug\Synopsys class.
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2012 Suin
 * @license    MIT License
 */

class AdelieDebug_Debug_Synopsys
{
	protected static $logger = null;

	public static function setLogger(AdelieDebug_Debug_Logger $logger)
	{
		self::$logger = $logger;
	}

	public function synopsys($object, $highlight = true, $minus = 1)
	{
		if ( is_object($object) === false and class_exists($object) === false and interface_exists($object) === false )
		{
			call_user_func(array('AdelieDebug_Debug_Dump', 'dumpbt'), $minus, $object);
		}
		else
		{
			$documentizer = new AdelieDebug_Debug_ClassDocumentizer($object);
			$document = $documentizer->documentize();
	
			if ( $highlight === true )
			{
				$document = highlight_string('<?php '.$document, true);
				$document = str_replace('&lt;?php&nbsp;', '', $document);
			}
	
			$document = AdelieDebug_Debug_Trace::getCalled($minus - 1).$document;
	
			self::$logger->addSynopsys($document);
		}
	}
}
