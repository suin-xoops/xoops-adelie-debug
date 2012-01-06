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

class AdelieDebug_Debug_Reporter_Html_Reportable
{
	public function __construct()
	{
	}

	public function isReportable()
	{
		if ( $this->isCli() === true )
		{
			return false;
		}

		if ( $this->isHtmlContent() === false )
		{
			return false;
		}

		if ( $this->isXMLHttpRequest() === true )
		{
			return false;
		}

		if ( defined('ADELIE_DEBUG_DISABLE') === true )
		{
			return false;
		}

		return true;
	}

	public function isCli()
	{
		return ( PHP_SAPI === 'cli' );
	}

	public function isHtmlContent()
	{
		$headers = headers_list();

		foreach ( $headers as $header )
		{
			$header = trim($header);
			
			if ( preg_match('#content-type:#i', $header) > 0 and preg_match('#content-type:\s*text/html#i', $header) == 0 )
			{
				return false;
			}
		}

		return true;
	}

	public function isXMLHttpRequest()
	{
		if ( isset($_SERVER['HTTP_X_REQUESTED_WITH']) === false )
		{
			return false;
		}

		if ( $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest' )
		{
			return true;
		}

		return false;
	}
}
