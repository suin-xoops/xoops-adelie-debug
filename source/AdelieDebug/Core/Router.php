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

class AdelieDebug_Core_Router
{
	protected $routes = array();

	public function compile(array $definition)
	{
		$route        = $definition['route'];
		$placeholders = $definition['placeholders'];
		unset($definition['route'], $definition['placeholders']);

		$prefix = '/'.trim($route['prefix'], '/');

		$routes = array();

		foreach ( $definition as $url => $properties )
		{
			$url = $prefix.$url;
			$patterns   = $this->_getPatterns($properties);
			$patterns   = array_merge($placeholders, $patterns);
			$parameters = $this->_getParameters($properties);
			$tokens     = $this->_tokenizeUrl($url);
			$parsedUrl  = $this->_parseUrl($tokens, $patterns);
			$url        = $this->_compileUrl($parsedUrl);
			$routes[] = array(
				'url'        => $url,
				'parameters' => $parameters,
			);
		}

		return $routes;
	}

	public function setRoutes(array $routes, $withCompile = true)
	{
		if ( $withCompile === true )
		{
			$routes = $this->compile($routes);
		}

		$this->routes = $routes;
	}

	public function getRoutes()
	{
		return $this->routes;
	}

	public function resolve($pathInfo)
	{
		$pathInfo = '/'.trim($pathInfo, '/');

		foreach ( $this->routes as $route )
		{
			if ( preg_match('#^'.$route['url'].'$#', $pathInfo, $matches) )
			{
				return array_merge($route['parameters'], $matches);
			}
		}

		return false;
	}

	protected function _getPatterns(array $properties)
	{
		$patterns = array();
	
		foreach ( $properties as $key => $value )
		{
			// for example, ':id', ':year', ':user_name'
			if ( strpos($key, ':') === 0 )
			{
				$patterns[$key] = $value;
			}
		}
	
		return $patterns;
	}

	protected function _getParameters(array $properties)
	{
		$parameters = array();
	
		foreach ( $properties as $key => $value )
		{
			// for example, ':id', ':year', ':user_name' will be excluded
			if ( strpos($key, ':') === false )
			{
				$parameters[$key] = $value;
			}
		}
	
		return $parameters;
	}

	protected function _tokenizeUrl($url)
	{
		$url = trim($url, '/');
		$tokens = explode('/', $url);
		return $tokens;
	}

	protected function _parseUrl(array $tokens, array $patterns = array())
	{
		foreach ( $tokens as $index => $token )
		{
			if ( strpos($token, ':') !== 0 )
			{
				continue; // not be modified.
			}
	
			if ( isset($patterns[$token]) === false )
			{
				continue;
			}
	
			$regex = $patterns[$token];
			$name  = substr($token, 1); // ex, ':id' becomes 'id'
			$tokens[$index] = '(?P<'.$name.'>'.$regex.')';
		}
	
		return $tokens;
	}

	protected function _compileUrl(array $parsedUrl)
	{
		return '/'.implode('/', $parsedUrl);
	}
}
