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

abstract class AdelieDebug_Core_Application
{
	/**
	 * Objects
	 */
	protected $request = null;
	protected $router  = null;

	/**
	 * Variables
	 */
	protected $config = null;
	protected $parameters = array();
	protected $result  = null;

	public function __construct(array $config = array())
	{
		$this->config = $config;
	}

	public function __get($name)
	{
		return $this->$name;
	}

	public function __isset($name)
	{
		return isset($this->$name);
	}

	/**
	 * setUp function.
	 * 
	 * @access public
	 * @return void
	 */
	public function setUp()
	{
		$this->_setUpConstant();
		$this->_setUpConfig();
		$this->_setUpRequest();
		$this->_setUpRouter();
		$this->_setUpRoutes();
	}

	/**
	 * run function.
	 * 
	 * @access public
	 * @return void
	 */
	public function run()
	{
		try
		{
			$this->_resolve();
			$this->_runController();
		}
		catch ( AdelieDebug_Exception_NotFoundException $e )
		{
			$this->_runExceptionController($e, 'not_found');
		}
		catch ( Exception $e )
		{
			$this->_runExceptionController($e, 'internal_server_error');
		}
	}

	/**
	 * getResult function.
	 * 
	 * @access public
	 * @return string
	 */
	public function getResult()
	{
		return $this->result;
	}

	/**
	 * isDebug function.
	 * 
	 * @access public
	 * @return bool
	 */
	public function isDebug()
	{
		return ( defined('ADELIE_DEBUG_DEBUG') === true and ADELIE_DEBUG_DEBUG === true );
	}

	/**
	 * config function.
	 * 
	 * @access public
	 * @param string $name
	 * @return mixed
	 */
	public function config($name)
	{
		return $this->config[$name];
	}

	/**
	 * parameter function.
	 * 
	 * @access public
	 * @param string $name
	 * @param mixed $default (default: null)
	 * @return mixed
	 */
	public function parameter($name, $default = null)
	{
		if ( array_key_exists($name, $this->parameters) === true )
		{
			return $this->parameters[$name];
		}

		return $default;
	}

	/**
	 * setParameter function.
	 * 
	 * @access public
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function setParameter($name, $value)
	{
		$this->parameters[$name] = $value;
	}

	protected function _setUpConstant()
	{
		defined('ADELIE_DEBUG_DIR') or define('ADELIE_DEBUG_DIR', dirname(dirname(__FILE__)));
	}

	protected function _setUpConfig()
	{
		$filenameProd  = ADELIE_DEBUG_DIR.'/Config/Config.ini';
		$filenameDebug = ADELIE_DEBUG_DIR.'/Config/ConfigDebug.ini';
		$config = AdelieDebug_Core_IniParser::parseFile($filenameProd);

		if ( $this->isDebug() === true and file_exists($filenameDebug) === true )
		{
			$configDebug = AdelieDebug_Core_IniParser::parseFile($filenameDebug);
			$config      = array_merge($config, $configDebug);
		}

		$this->config = $config;
	}

	protected function _setUpRequest()
	{
		$this->request = new AdelieDebug_Core_Request();
	}

	protected function _setUpRouter()
	{
		$this->router = new AdelieDebug_Core_Router();
	}

	protected function _setUpRoutes()
	{
		$filename = ADELIE_DEBUG_DIR.'/Config/Route.ini';
		$routes   = AdelieDebug_Core_IniParser::parseFile($filename);
		$this->router->setRoutes($routes);
	}

	protected function _resolve()
	{
		$pathinfo   = $this->request->getPathinfo();
		$parameters = $this->router->resolve($pathinfo);

		if ( $parameters === false )
		{
			throw new AdelieDebug_Exception_NotFoundException('Route not found: '.$pathinfo);
		}

		$this->parameters = array_merge($this->parameters, $parameters);
	}

	protected function _runController()
	{
		$this->parameters['Controller'] = AdelieDebug_Core_Inflector::pascalize($this->parameters['controller']);
		$this->parameters['Action']     = AdelieDebug_Core_Inflector::pascalize($this->parameters['action']);

		$controllerClass  = $this->_getControllerClass($this->parameters['Controller'], $this->parameters['Action']);
		$controller = new $controllerClass($this);
		$controller->setUp();
		$controller->run();
		$this->result = $controller->getResult();
	}

	protected function _getControllerClass($controller, $action)
	{
		$controllerClass = 'AdelieDebug_Controller_'.$controller.'_'.$action;

		if ( class_exists($controllerClass) === false )
		{
			throw new AdelieDebug_Exception_NotFoundException('Class not found: '.$controllerClass);
		} 

		return $controllerClass;
	}

	protected function _runExceptionController(Exception $exception, $action = 'default')
	{
		$this->parameters = array(
			'controller' => 'error',
			'action'     => $action,
			'exception'  => $exception,
		);

		$this->_runController();
	}
}
