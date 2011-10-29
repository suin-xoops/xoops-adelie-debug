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

class AdelieDebug_Core_Request
{
	public function __construct()
	{
	}

	public function isPost()
	{
		return ( $_SERVER['REQUEST_METHOD'] === 'POST' );
	}

	public function isGet()
	{
		return ( $_SERVER['REQUEST_METHOD'] === 'GET' );
	}

	public function isSSL()
	{
		return ( isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] === 'on' );
	}

	/**
	 *
	 * @notice The X-Requested-With is sent by the Ajax functions of most major Frameworks but not all.
	 * @see http://stackoverflow.com/questions/2579254/php-does-serverhttp-x-requested-with-exist-or-not
	 */
	public function isXHR()
	{
		return ( isset($_SERVER['HTTP_X_REQUESTED_WITH']) === true and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest' );
	}

	public function isCLI()
	{
		return ( PHP_SAPI === 'cli' );
	}

	public function get($name, $default = null)
	{
		if ( isset($_GET[$name]) )
		{
			return $_GET[$name];
		}

		return $default;
	}

	public function post($name, $default = null)
	{
		if ( isset($_POST[$name]) )
		{
			return $_POST[$name];
		}

		return $default;
	}

	public function cookie($name, $default = null)
	{
		if ( isset($_COOKIE[$name]) )
		{
			return $_COOKIE[$name];
		}

		return $default;
	}

	public function getScheme()
	{
		if ( $this->isSSL() === true )
		{
			return 'https';
		}

		return 'http';
	}

	public function getUrl()
	{
		return $this->getScheme().'://'.$this->getHost().$this->getRequestUri();
	}

	public function getRequestUri()
	{
		return $_SERVER['REQUEST_URI'];
	}

	public function getScriptName()
	{
		return $_SERVER['SCRIPT_NAME'];
	}

	public function getRemoteAddr()
	{
		if ( empty($_SERVER['HTTP_X_FORWARDED_FOR']) === false )
		{
			return $_SERVER['HTTP_X_FORWARDED_FOR']; // for reverse proxy
		}

		return $_SERVER['REMOTE_ADDR'];
	}

	public function getHost()
	{
		if ( empty($_SERVER['HTTP_X_FORWARDED_HOST']) === false )
		{
			return $_SERVER['HTTP_X_FORWARDED_HOST']; // for reverse proxy
		}

		return $_SERVER['HTTP_HOST'];
	}

	public function getServerName()
	{
		if ( empty($_SERVER['HTTP_X_FORWARDED_SERVER']) === false )
		{
			return $_SERVER['HTTP_X_FORWARDED_SERVER']; // for reverse proxy
		}

		return $_SERVER['SERVER_NAME'];
	}

	public function getSiteUrl()
	{
		return $this->getScheme().'://'.$this->getHost().$this->getSiteBaseUrl();
	}

	public function getSiteBaseUrl()
	{
		return rtrim(dirname($this->getScriptName()), '/');
	}

	public function getBaseUrl()
	{
		$scriptName = $this->getScriptName();
		$requestUri = $this->getRequestUri();

		if ( strpos($requestUri, $scriptName) === 0 )
		{
			return $scriptName;
		}
		elseif ( strpos($requestUri, dirname($scriptName)) === 0 )
		{
			return rtrim(dirname($scriptName), '/');
		}

		return '';
	}

	public function getPathInfo()
	{
		$baseUrl    = $this->getBaseUrl();
		$requestUri = $this->getRequestUri();

		$queryStringPosition = strpos($requestUri, '?');

		if ( $queryStringPosition !== false )
		{
			$requestUri = substr($requestUri, 0, $queryStringPosition);
		}

		$baseUrlLength = strlen($baseUrl);
		$pathInfo = substr($requestUri, $baseUrlLength);
		$pathInfo = strval($pathInfo);

		return $pathInfo;
	}

	public function getAcceptLanguages()
	{
		if ( array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER) === false )
		{
			return array();
		}

		$languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
		$acceptLanguages = array();

		foreach ( $languages as $language )
		{
			$tokens = explode(';q=', $language);

			if ( count($tokens) === 2 )
			{
				$langcode = reset($tokens);
				$priority = floatval(next($tokens));
			}
			else
			{
				$langcode = reset($tokens);
				$priority = 1;
			}

			$acceptLanguages[$langcode] = $priority;
		}

		arsort($acceptLanguages);

		return $acceptLanguages;
	}
}
