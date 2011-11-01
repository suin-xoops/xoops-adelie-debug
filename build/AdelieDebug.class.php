<?php 
define('ADELIE_DEBUG_BUILD', true);
define('ADELIE_DEBUG_BUILD_TIME', 1320147288);

/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class AdelieDebug_Archive_ClassLoader
{
	protected $includePaths       = array();
	protected $namespaceSeparator = '_';
	protected $fileExtension      = '.php';

	public function setIncludePath($includePath)
	{
		if ( in_array($includePath, $this->includePaths) === false )
		{
			$this->includePaths[] = $includePath;
		}

		return $this;
	}

	public function getIncludePath()
	{
		return $this->includePaths;
	}

	public function setNamespaceSeparator($namespaceSeparator)
	{
		$this->namespaceSeparator = $namespaceSeparator;
		return $this;
	}

	public function getNamespaceSeparator()
	{
		return $this->namespaceSeparator;
	}

	public function setFileExtension($fileExtension)
	{
		$this->fileExtension = $fileExtension;
		return $this;
	}

	public function getFileExtension()
	{
		return $this->fileExtension;
	}

	public function register()
	{
		spl_autoload_register(array($this, 'loadClass'));
		return $this;
	}

	public function unregister()
	{
		spl_autoload_unregister(array($this, 'loadClass'));
		return $this;
	}

	public function loadClass($className)
	{
		if ( class_exists($className, false) === true )
		{
			return;
		}

		if ( interface_exists($className, false) === true )
		{
			return;
		}

		if ( function_exists('trait_exists') === true and trait_exists($className, false) === true )
		{
			return;
		}

		if ( preg_match('/[a-zA-Z0-9_\\\]/', $className) == false )
		{
			throw new InvalidArgumentException('Invalid class name was given: '.$className);
		}

		$classFile = str_replace($this->namespaceSeparator, DIRECTORY_SEPARATOR, $className);
		$classFile = $classFile.$this->fileExtension;

		foreach ( $this->includePaths as $includePath )
		{
			if ( $includePath === ':eval:' )
			{
				$classFile = '/'.$classFile;
			
				if ( array_key_exists($classFile, AdelieDebug_Archive::$archive) === true )
				{
					eval(AdelieDebug_Archive::$archive[$classFile]);
					return true;
				}
			}
			else
			{
				$classPath = $includePath.DIRECTORY_SEPARATOR.$classFile;

				if ( file_exists($classPath) === true )
				{
					require $classPath;
					return true;
				}
			}
		}

		return false;
	}
}


class AdelieDebug_Preload extends XCube_ActionFilter
{
	protected $debugger = null;

	public function preFilter()
	{
		$this->_bootstrap();
		$this->_setUp();
		$this->_registerEventListeners();
	}

	public function topAccessEventHandler()
	{
		if ( $this->_isAdelieDebugPage() === false )
		{
			return;
		}

		$application = new AdelieDebug_Application();
		$application->setUp();
		$application->run();
		$result = $application->getResult();
		echo $result;
		die;
	}

	public function setupDebugEventHandler($instance, $debugMode)
	{
		$instance = new AdelieDebug_Debug_XoopsDebugger($this->debugger->logger);
		$this->debugger->enableErrorReporting(); // Legacy_Controller::_setupDebugger() で error_reproting = 0 にされちゃってるので必要
	}

	protected function _bootstrap()
	{
		if ( defined('ADELIE_DEBUG_BUILD') === true )
		{
			$classLoader = new AdelieDebug_Archive_ClassLoader();
			$classLoader->setIncludePath(':eval:');
			$classLoader->register();
		}
		else
		{
			require_once dirname(__FILE__).'/Core/ClassLoader.php';
			$classLoader = new AdelieDebug_Core_ClassLoader();
			$classLoader->setIncludePath(dirname(dirname(__FILE__)));
			$classLoader->register();
		}
	}

	protected function _setUp()
	{
		$this->debugger = new AdelieDebug_Debug_Main();
		$this->debugger->run();
	}

	protected function _registerEventListeners()
	{
		$this->mRoot->mDelegateManager->add('Legacypage.Top.Access', array($this, 'topAccessEventHandler'), 0);
		$this->mController->mSetupDebugger->add(array($this, 'setupDebugEventHandler'), 99999);
	}

	protected function _isAdelieDebugPage()
	{
		return ( strpos($_SERVER['REQUEST_URI'], 'index.php/debug') !== false );
	}
}

class AdelieDebug extends AdelieDebug_Preload
{
}
class AdelieDebug_Archive
{
	public static $archive = array (
  '/AdelieDebug/Application.php' => '/**
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

	/**
	 * setPathinfo function.
	 * 
	 * @access public
	 * @param string $pathinfo
	 * @return void
	 */
	public function setPathinfo($pathinfo)
	{
		$this->pathinfo = $pathinfo;
	}

	/**
	 * isBuild function.
	 * 
	 * @access public
	 * @return bool
	 */
	public function isBuild()
	{
		return defined(\'ADELIE_DEBUG_BUILD\');
	}

	protected function _setUpConfig()
	{
		if ( $this->isBuild() === false )
		{
			parent::_setUpConfig();
			return;
		}

		$this->config = eval(AdelieDebug_Archive::$archive[\'/AdelieDebug/Config/Config.ini\']);
		$this->config[\'render.class\'] = $this->config[\'render.class\'].\'OnBuild\';
	}

	protected function _setUpRoutes()
	{
		if ( $this->isBuild() === false )
		{
			parent::_setUpRoutes();
			return;
		}

		$routes = eval(AdelieDebug_Archive::$archive[\'/AdelieDebug/Config/Route.ini\']);
		$this->router->setRoutes($routes);
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
			throw new AdelieDebug_Exception_NotFoundException(\'Route not found: \'.$this->pathinfo);
		}

		$this->parameters = array_merge($this->parameters, $parameters);
	}
}',
  '/AdelieDebug/Config/Config.ini' => 'return array ( \'render.class\' => \'AdelieDebug_Render_Smarty\', \'theme.name\' => \'AdelieDebug\', );',
  '/AdelieDebug/Config/Route.ini' => 'return array ( \'route\' => array ( \'prefix\' => \'/debug\', ), \'placeholders\' => array ( \':controller\' => \'[a-z0-9_]+\', \':action\' => \'[a-z0-9_]+\', \':year\' => \'[0-9]{4}\', \':month\' => \'[0-9]{2}\', \':day\' => \'[0-9]{2}\', \':id\' => \'[0-9]+\', ), \'/\' => array ( \'controller\' => \'top\', \'action\' => \'index\', ), \'/:controller\' => array ( \'action\' => \'index\', ), \'/:controller/:action\' => array ( ), );',
  '/AdelieDebug/Controller/Error/InternalServerError.php' => '/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class AdelieDebug_Controller_Error_InternalServerError extends AdelieDebug_Controller_Error
{
	protected $statusCode    = 500;
	protected $statusMessage = "500 Internal Server Error";
}',
  '/AdelieDebug/Controller/Error/NotFound.php' => '/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class AdelieDebug_Controller_Error_NotFound extends AdelieDebug_Controller_Error
{
	protected $statusCode    = 404;
	protected $statusMessage = "404 Not Found";
}',
  '/AdelieDebug/Controller/Error.php' => '/**
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
		header(\'HTTP\', true, $this->statusCode);
		$this->output[\'statusCode\']    = $this->statusCode;
		$this->output[\'statusMessage\'] = $this->statusMessage;
		$this->output[\'exception\']     = $this->app->parameter(\'exception\');
		$this->_render();
	}
}',
  '/AdelieDebug/Controller/Report/Index.php' => '/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class AdelieDebug_Controller_Report_Index extends AdelieDebug_Controller
{
	protected $logger = null;

	public function setUp()
	{
		$this->_setUpLogger();
	}

	public function run()
	{
		$this->_checkAccess();
		
		$this->output[\'sentHeaders\'] = headers_list();
		$this->output[\'requests\'] = array(
			\'$_GET\'     => $_GET, 
			\'$_POST\'    => $_POST, 
			\'$_SESSION\' => $_SESSION,
			\'$_COOKIE\'  => $_COOKIE,
			\'$_FILES\'   => $_FILES,
			\'$_SERVER\'  => $_SERVER,
		);
		$this->output[\'logs\'] = $this->logger->getLogs();

		$this->_render();
	}

	protected function _setUpLogger()
	{
		$this->logger = $this->app->parameter(\'logger\');
	}

	protected function _checkAccess()
	{
		if ( $this->app->parameter(\'via\') === null )
		{
//			throw new RuntimeException("予期せぬアクセス方法です。");
		}
	}
}',
  '/AdelieDebug/Controller/Top/Index.php' => '/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class AdelieDebug_Controller_Top_Index extends AdelieDebug_Controller
{
	public function run()
	{
		$this->_render();
	}
}',
  '/AdelieDebug/Controller.php' => '/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

abstract class AdelieDebug_Controller extends AdelieDebug_Core_Controller
{

}',
  '/AdelieDebug/Core/Application.php' => '/**
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
			$this->_runExceptionController($e, \'not_found\');
		}
		catch ( Exception $e )
		{
			$this->_runExceptionController($e, \'internal_server_error\');
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
		return ( defined(\'ADELIE_DEBUG_DEBUG\') === true and ADELIE_DEBUG_DEBUG === true );
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
		defined(\'ADELIE_DEBUG_DIR\') or define(\'ADELIE_DEBUG_DIR\', dirname(dirname(__FILE__)));
	}

	protected function _setUpConfig()
	{
		$filenameProd  = ADELIE_DEBUG_DIR.\'/Config/Config.ini\';
		$filenameDebug = ADELIE_DEBUG_DIR.\'/Config/ConfigDebug.ini\';
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
		$filename = ADELIE_DEBUG_DIR.\'/Config/Route.ini\';
		$routes   = AdelieDebug_Core_IniParser::parseFile($filename);
		$this->router->setRoutes($routes);
	}

	protected function _resolve()
	{
		$pathinfo   = $this->request->getPathinfo();
		$parameters = $this->router->resolve($pathinfo);

		if ( $parameters === false )
		{
			throw new AdelieDebug_Exception_NotFoundException(\'Route not found: \'.$pathinfo);
		}

		$this->parameters = array_merge($this->parameters, $parameters);
	}

	protected function _runController()
	{
		$this->parameters[\'Controller\'] = AdelieDebug_Core_Inflector::pascalize($this->parameters[\'controller\']);
		$this->parameters[\'Action\']     = AdelieDebug_Core_Inflector::pascalize($this->parameters[\'action\']);

		$controllerClass  = $this->_getControllerClass($this->parameters[\'Controller\'], $this->parameters[\'Action\']);
		$controller = new $controllerClass($this);
		$controller->setUp();
		$controller->run();
		$this->result = $controller->getResult();
	}

	protected function _getControllerClass($controller, $action)
	{
		$controllerClass = \'AdelieDebug_Controller_\'.$controller.\'_\'.$action;

		if ( class_exists($controllerClass) === false )
		{
			throw new AdelieDebug_Exception_NotFoundException(\'Class not found: \'.$controllerClass);
		} 

		return $controllerClass;
	}

	protected function _runExceptionController(Exception $exception, $action = \'default\')
	{
		$this->parameters = array(
			\'controller\' => \'error\',
			\'action\'     => $action,
			\'exception\'  => $exception,
		);

		$this->_runController();
	}
}',
  '/AdelieDebug/Core/ClassLoader.php' => '/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class AdelieDebug_Core_ClassLoader
{
	protected $includePaths       = array();
	protected $namespaceSeparator = \'_\';
	protected $fileExtension      = \'.php\';

	public function setIncludePath($includePath)
	{
		if ( in_array($includePath, $this->includePaths) === false )
		{
			$this->includePaths[] = $includePath;
		}

		return $this;
	}

	public function getIncludePath()
	{
		return $this->includePaths;
	}

	public function setNamespaceSeparator($namespaceSeparator)
	{
		$this->namespaceSeparator = $namespaceSeparator;
		return $this;
	}

	public function getNamespaceSeparator()
	{
		return $this->namespaceSeparator;
	}

	public function setFileExtension($fileExtension)
	{
		$this->fileExtension = $fileExtension;
		return $this;
	}

	public function getFileExtension()
	{
		return $this->fileExtension;
	}

	public function register()
	{
		spl_autoload_register(array($this, \'loadClass\'));
		return $this;
	}

	public function unregister()
	{
		spl_autoload_unregister(array($this, \'loadClass\'));
		return $this;
	}

	public function loadClass($className)
	{
		if ( class_exists($className, false) === true )
		{
			return;
		}

		if ( interface_exists($className, false) === true )
		{
			return;
		}

		if ( function_exists(\'trait_exists\') === true and trait_exists($className, false) === true )
		{
			return;
		}

		if ( preg_match(\'/[a-zA-Z0-9_\\\\\\]/\', $className) == false )
		{
			throw new InvalidArgumentException(\'Invalid class name was given: \'.$className);
		}

		$classFile = str_replace($this->namespaceSeparator, DIRECTORY_SEPARATOR, $className);
		$classFile = $classFile.$this->fileExtension;

		foreach ( $this->includePaths as $includePath )
		{
			$classPath = $includePath.DIRECTORY_SEPARATOR.$classFile;
	
			if ( file_exists($classPath) === true )
			{
				require $classPath;
				return true;
			}
		}

		return false;
	}
}',
  '/AdelieDebug/Core/Controller.php' => '/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

abstract class AdelieDebug_Core_Controller
{
	/**
	 * @AdelieDebug_Core_Application
	 */
	protected $app = null;

	/**
	 * 結果
	 */
	protected $result = null;

	/**
	 * ページタイトル
	 */
	protected $pageTitle    = \'\';
	protected $pageSubtitle = \'\';

	/**
	 * View関係
	 */
	protected $output = array();
	protected $controllerTemplate = \'\';
	protected $actionTemplate     = \'\';
	protected $themeTemplate      = \'Main\';

	public function __construct(AdelieDebug_Core_Application $app)
	{
		$this->app = $app;
		$this->controllerTemplate = $this->app->parameter(\'Controller\');
		$this->actionTemplate     = $this->app->parameter(\'Action\');
	}

	/**
	 * 初期化用テンプレートメソッド
	 * 
	 * @access public
	 * @return void
	 */
	public function setUp()
	{
	}

	/**
	 * メイン処理。実装してね.
	 * 
	 * @access public
	 * @abstract
	 * @return void
	 */
	abstract public function run();

	/**
	 * 結果を返す.
	 * 
	 * @access public
	 * @return mixed
	 */
	public function getResult()
	{
		return $this->result;
	}

	/**
	 * 描画結果をresultに代入する.
	 * 
	 * @access protected
	 * @return void
	 */
	protected function _render()
	{
		$content      = $this->_renderTemplate();
		$this->result = $this->_renderTheme(array(\'content\' => $content));
	}

	protected function _renderTemplate()
	{
		$renderClass = $this->app->config(\'render.class\');
		$render = new $renderClass($this->app);
		$values = $this->_getTemplateValues();
		$values = array_merge($values, $this->output);
		$render->setValues($values);
		$render->setTemplate($this->_getTemplate());
		return $render->render();
	}

	protected function _getTemplate()
	{
		return \'Template/\'.$this->controllerTemplate.\'/\'.$this->actionTemplate;
	}

	protected function _getThemeValues(array $_values)
	{
		$values = $this->_getTemplateValues();
		$values = array_merge($values, $_values);
		return $values;
	}

	protected function _getTemplateValues()
	{
		return array(
			\'app\'          => $this->app,
			\'baseUrl\'      => $this->app->request->getBaseUrl(),
			\'siteUrl\'      => $this->app->request->getSiteUrl(),
			\'siteBaseUrl\'  => $this->app->request->getSiteBaseUrl(),
			\'pageTitle\'    => $this->pageTitle,
			\'pageSubtitle\' => $this->pageSubtitle,
		);
	}

	protected function _renderTheme($content)
	{
		$renderClass = $this->app->config(\'render.class\');
		$values = $this->_getThemeValues($content);
		$render = new $renderClass($this->app);
		$render->setValues($values);
		$render->setTemplate($this->_getTheme());
		return $render->render();
	}

	protected function _getTheme()
	{
		return \'Theme/\'.$this->app->config(\'theme.name\').\'/\'.$this->themeTemplate;
	}
}',
  '/AdelieDebug/Core/Inflector.php' => '/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class AdelieDebug_Core_Inflector
{
	public static function camelize($string)
	{
		$string = self::pascalize($string);
		$string[0] = strtolower($string[0]);
		return $string;
	}

	public static function pascalize($string)
	{
		$string = strtolower($string);
		$string = str_replace(\'_\', \' \', $string);
		$string = ucwords($string);
		$string = str_replace(\' \', \'\', $string);
		return $string;
	}

	public static function snakeCase($string)
	{
		$string = preg_replace(\'/([A-Z])/\', \'_$1\', $string);
		$string = strtolower($string);
		return ltrim($string, \'_\');
	}

	public static function snakeCaseUpper($string)
	{
		$string = self::snakeCase($string);
		$string = strtoupper($string);
		return $string;
	}

	public static function snakeCaseLower($string)
	{
		$string = self::snakeCase($string);
		$string = strtolower($string);
		return $string;
	}
}',
  '/AdelieDebug/Core/IniParser.php' => '/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class AdelieDebug_Core_IniParser
{
	public static function parseFile($filename, $processSection = true, $mode = null)
	{
		$arguments = array($filename, true);

		if ( version_compare(PHP_VERSION, \'5.3\', \'<\') and $mode !== null )
		{
			$arguments[] = $mode; // PHP5.3以上
		}

		return call_user_func_array(\'parse_ini_file\', $arguments);
	}
}',
  '/AdelieDebug/Core/Render.php' => '/**
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
	protected $template = \'\';

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
		return \'\';
	}
}',
  '/AdelieDebug/Core/Request.php' => '/**
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
		return ( $_SERVER[\'REQUEST_METHOD\'] === \'POST\' );
	}

	public function isGet()
	{
		return ( $_SERVER[\'REQUEST_METHOD\'] === \'GET\' );
	}

	public function isSSL()
	{
		return ( isset($_SERVER[\'HTTPS\']) and $_SERVER[\'HTTPS\'] === \'on\' );
	}

	/**
	 *
	 * @notice The X-Requested-With is sent by the Ajax functions of most major Frameworks but not all.
	 * @see http://stackoverflow.com/questions/2579254/php-does-serverhttp-x-requested-with-exist-or-not
	 */
	public function isXHR()
	{
		return ( isset($_SERVER[\'HTTP_X_REQUESTED_WITH\']) === true and strtolower($_SERVER[\'HTTP_X_REQUESTED_WITH\']) === \'xmlhttprequest\' );
	}

	public function isCLI()
	{
		return ( PHP_SAPI === \'cli\' );
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
			return \'https\';
		}

		return \'http\';
	}

	public function getUrl()
	{
		return $this->getScheme().\'://\'.$this->getHost().$this->getRequestUri();
	}

	public function getRequestUri()
	{
		return $_SERVER[\'REQUEST_URI\'];
	}

	public function getScriptName()
	{
		return $_SERVER[\'SCRIPT_NAME\'];
	}

	public function getRemoteAddr()
	{
		if ( empty($_SERVER[\'HTTP_X_FORWARDED_FOR\']) === false )
		{
			return $_SERVER[\'HTTP_X_FORWARDED_FOR\']; // for reverse proxy
		}

		return $_SERVER[\'REMOTE_ADDR\'];
	}

	public function getHost()
	{
		if ( empty($_SERVER[\'HTTP_X_FORWARDED_HOST\']) === false )
		{
			return $_SERVER[\'HTTP_X_FORWARDED_HOST\']; // for reverse proxy
		}

		return $_SERVER[\'HTTP_HOST\'];
	}

	public function getServerName()
	{
		if ( empty($_SERVER[\'HTTP_X_FORWARDED_SERVER\']) === false )
		{
			return $_SERVER[\'HTTP_X_FORWARDED_SERVER\']; // for reverse proxy
		}

		return $_SERVER[\'SERVER_NAME\'];
	}

	public function getSiteUrl()
	{
		return $this->getScheme().\'://\'.$this->getHost().$this->getSiteBaseUrl();
	}

	public function getSiteBaseUrl()
	{
		return rtrim(dirname($this->getScriptName()), \'/\');
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
			return rtrim(dirname($scriptName), \'/\');
		}

		return \'\';
	}

	public function getPathInfo()
	{
		$baseUrl    = $this->getBaseUrl();
		$requestUri = $this->getRequestUri();

		$queryStringPosition = strpos($requestUri, \'?\');

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
		if ( array_key_exists(\'HTTP_ACCEPT_LANGUAGE\', $_SERVER) === false )
		{
			return array();
		}

		$languages = explode(\',\', $_SERVER[\'HTTP_ACCEPT_LANGUAGE\']);
		$acceptLanguages = array();

		foreach ( $languages as $language )
		{
			$tokens = explode(\';q=\', $language);

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
}',
  '/AdelieDebug/Core/Router.php' => '/**
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
		$route        = $definition[\'route\'];
		$placeholders = $definition[\'placeholders\'];
		unset($definition[\'route\'], $definition[\'placeholders\']);

		$prefix = \'/\'.trim($route[\'prefix\'], \'/\');

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
				\'url\'        => $url,
				\'parameters\' => $parameters,
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
		$pathInfo = \'/\'.trim($pathInfo, \'/\');

		foreach ( $this->routes as $route )
		{
			if ( preg_match(\'#^\'.$route[\'url\'].\'$#\', $pathInfo, $matches) )
			{
				return array_merge($route[\'parameters\'], $matches);
			}
		}

		return false;
	}

	protected function _getPatterns(array $properties)
	{
		$patterns = array();
	
		foreach ( $properties as $key => $value )
		{
			// for example, \':id\', \':year\', \':user_name\'
			if ( strpos($key, \':\') === 0 )
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
			// for example, \':id\', \':year\', \':user_name\' will be excluded
			if ( strpos($key, \':\') === false )
			{
				$parameters[$key] = $value;
			}
		}
	
		return $parameters;
	}

	protected function _tokenizeUrl($url)
	{
		$url = trim($url, \'/\');
		$tokens = explode(\'/\', $url);
		return $tokens;
	}

	protected function _parseUrl(array $tokens, array $patterns = array())
	{
		foreach ( $tokens as $index => $token )
		{
			if ( strpos($token, \':\') !== 0 )
			{
				continue; // not be modified.
			}
	
			if ( isset($patterns[$token]) === false )
			{
				continue;
			}
	
			$regex = $patterns[$token];
			$name  = substr($token, 1); // ex, \':id\' becomes \'id\'
			$tokens[$index] = \'(?P<\'.$name.\'>\'.$regex.\')\';
		}
	
		return $tokens;
	}

	protected function _compileUrl(array $parsedUrl)
	{
		return \'/\'.implode(\'/\', $parsedUrl);
	}
}',
  '/AdelieDebug/Debug/Dump.php' => '/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class AdelieDebug_Debug_Dump
{
	protected static $logger = null;

	public static function setLogger(AdelieDebug_Debug_Logger $logger)
	{
		self::$logger = $logger;
	}

	public static function dump()
	{
		$called = self::_getCalled(0, false);
		$values = func_get_args();
		$result = self::_dump_html($called, $values);
		self::$logger->addDump($result);
	}

	public static function dumpbt($level = 0)
	{
		$level  = $level + 1;
		$called = self::_getCalled($level, false);
		$values = func_get_args();
		array_shift($values);
		$result =  self::_dump_html($called, $values);
		self::$logger->addDump($result);
	}

	protected static function _getCalled($level = 0, $isDump = true)
	{
		$level = $level + 1;
		$trace = array(
			\'file\' => \'Unknown file\',
			\'line\' => 0,
		);
		
		$traces = debug_backtrace();

		if ( isset($traces[$level]) === true )
		{
			$trace = array_merge($trace, $traces[$level]);
		}
		
		$called = sprintf("Called in %s on line %s", $trace[\'file\'], $trace[\'line\']);

		return $called;
	}

	protected static function _dump_html($called, $values)
	{
		ob_start();
		echo \'<pre style="border:1px dotted #000; font-size:12px; color:#000; background:#fff; font-family:"Times New Roman",Georgia,Serif;">\';
		echo \'<div style="font-size:10px; background:#ddd;text-align:left;">\'.$called."</div>";
		echo \'<div style="text-align:left;">\';
		array_map(\'var_dump\', $values);
		echo \'</div>\';
		echo \'</pre>\';
		return ob_get_clean();
	}
}',
  '/AdelieDebug/Debug/ErrorHandler.php' => '/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class AdelieDebug_Debug_ErrorHandler
{
	protected $errorTypes = array (
		E_ERROR              => \'ERROR\',
		E_WARNING            => \'WARNING\',
		E_PARSE              => \'PARSING ERROR\',
		E_NOTICE             => \'NOTICE\',
		E_CORE_ERROR         => \'CORE ERROR\',
		E_CORE_WARNING       => \'CORE WARNING\',
		E_COMPILE_ERROR      => \'COMPILE ERROR\',
		E_COMPILE_WARNING    => \'COMPILE WARNING\',
		E_USER_ERROR         => \'USER ERROR\',
		E_USER_WARNING       => \'USER WARNING\',
		E_USER_NOTICE        => \'USER NOTICE\',
		E_STRICT             => \'STRICT NOTICE\',
		E_RECOVERABLE_ERROR  => \'RECOVERABLE ERROR\',
	);

	protected $logger = null;

	public function __construct(AdelieDebug_Debug_Logger $logger)
	{
		$this->logger = $logger;
		$this->_setUpErrorTypes();
	}

	public function register()
	{
		set_error_handler(array($this, \'callback\'));
	}

	public function callback($level, $message, $file, $line)
	{
		if ( ( $level & error_reporting() ) != $level )
		{
			return true;
		}

		$trace = $this->_backtrace(3);

		$this->_add($level, $message, $file, $line, $trace);

		return true;
	}

	protected function _backtrace($ignore = 1)
	{
		$output = \'\';
		$backtrace = debug_backtrace();
		$index = 0;

		for ( $i = 0; $i < $ignore; $i ++ )
		{
			array_shift($backtrace);
		}

		foreach ( $backtrace as $index => $bt )
		{
			$args = \'\';

			if ( isset($bt[\'class\']) === true )
			{
				$function = $bt[\'class\'].$bt[\'type\'].$bt[\'function\'];
			}
			else
			{
				$function = $bt[\'function\'];
			}
	
			if ( isset($bt[\'line\']) === true )
			{
				$line = $bt[\'line\'];
			}
			else
			{
				$line = \'?\';
			}
	
			if ( isset($bt[\'file\']) === true )
			{
				$file = $bt[\'file\'];
			}
			else
			{
				$file = \'?\';
			}

			$output .= sprintf(\'#%u %s(%s) called at [%s:%s]\'."\\n", $index, $function, $args, $file, $line);
			$index += 1;
		}
	
		return $output;
	}

	protected function _setUpErrorTypes()
	{
		if ( version_compare(PHP_VERSION, \'5.2\', \'>=\') === true )
		{
			$this->errorTypes[E_RECOVERABLE_ERROR] = \'RECOVERABLE ERROR\';
		}

		if ( version_compare(PHP_VERSION, \'5.3\', \'>=\') === true )
		{
			$this->errorTypes[E_DEPRECATED]      = \'DEPRECATED\';
			$this->errorTypes[E_USER_DEPRECATED] = \'USER_DEPRECATED\';
		}
	}

	protected function _getFormatedError(array $error, $format = "{type}: {message} in {file} on line {line}")
	{
		$message = str_replace(\'{type}\', $error[\'type\'], $format);
		$message = str_replace(\'{message}\', $error[\'message\'], $message);
		$message = str_replace(\'{file}\', $error[\'file\'], $message);
		$message = str_replace(\'{line}\', $error[\'line\'], $message);

		return $message;
	}

	protected function _add($level, $message, $file, $line, $trace)
	{
		$error = array(
			\'type\'    => $this->_getType($level),
			\'level\'   => $level,
			\'message\' => $message,
			\'file\'    => $file,
			\'line\'    => $line,
		);

		$message = $this->_getFormatedError($error);
		$this->logger->addPhpError($message, $trace);
	}
	
	protected function _getType($level)
	{
		if ( isset($this->errorTypes[$level]) === true )
		{
			return $this->errorTypes[$level];
		}
		
		return \'UNKNOWN ERROR(\'.$level.\')\';
	}
}',
  '/AdelieDebug/Debug/ExceptionHandler.php' => '/**
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
		set_exception_handler(array($this, \'catchException\'));
	}

	public function catchException(Exception $exception)
	{
		$this->logger->addPhpError(strval($exception));
	}
}',
  '/AdelieDebug/Debug/Function.php' => '/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

function adump()
{
	$args = func_get_args();
	array_unshift($args, 1);
	call_user_func_array(array(\'AdelieDebug_Debug_Dump\', \'dumpbt\'), $args);
}

function atrace()
{
	AdelieDebug_Debug_Trace::trace(1);
}',
  '/AdelieDebug/Debug/Logger.php' => '/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class AdelieDebug_Debug_Logger
{
	const TYPE_UNKOWN    = 1;
	const TYPE_PHP_ERROR = 2;
	const TYPE_DUMP      = 4;
	const TYPE_SQL       = 8;
	const TYPE_SQL_ERROR = 16;
	const TYPE_SQL_MARK  = 32;
	const TYPE_TRACE     = 64;

	protected static $typeNames = array(
		self::TYPE_UNKOWN    => \'UNKNOWN\',
		self::TYPE_PHP_ERROR => \'PHP ERROR\',
		self::TYPE_DUMP      => \'DUMP\',
		self::TYPE_SQL       => \'SQL\',
		self::TYPE_SQL_ERROR => \'SQL ERROR\',
		self::TYPE_SQL_MARK  => \'SQL MARK\',
		self::TYPE_TRACE     => \'TRACE\',
	);

	protected $logs = array();

	protected $initTime = 0;

	public function __construct()
	{
		$this->initTime = microtime(true);
	}

	public function getLogs()
	{
		return $this->logs;
	}

	public function add($message, $type = self::TYPE_UNKOWN, $isError = false, $info = \'\')
	{
		$now = microtime(true) - $this->initTime;
	
		$this->logs[] = array(
			\'type\'     => $type,
			\'typeName\' => self::$typeNames[$type],
			\'message\'  => $message,
			\'time\'     => $now,
			\'ms\'       => round( $now * 1000 ),
			\'isError\'  => $isError,
			\'info\'     => $info,
		);
	}

	public function addPhpError($error, $trace = \'\')
	{
		$this->add($error, self::TYPE_PHP_ERROR, true, $trace);
	}

	public function addDump($message)
	{
		$this->add($message, self::TYPE_DUMP);
	}

	public function addSql($message, $info = \'\')
	{
		$this->add($message, self::TYPE_SQL, false, $info);
	}

	public function addSqlError($message, $error)
	{
		$this->add($message, self::TYPE_SQL_ERROR, true, $error);
	}

	public function addSqlMark($message)
	{
		$this->add($message, self::TYPE_SQL_MARK);
	}

	public function addTrace($message)
	{
		$this->add($message, self::TYPE_TRACE);
	}
}',
  '/AdelieDebug/Debug/Main.php' => '/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class AdelieDebug_Debug_Main
{
	protected $logger           = null;
	protected $errorHandler     = null;
	protected $exceptionHandler = null;
	protected $reporter         = null;
	protected $shutdown         = null;

	public function __construct()
	{
		// なんもしない
	}

	public function __isset($name)
	{
		return isset($this->$name);
	}

	public function __get($name)
	{
		return $this->$name;
	}

	public function run()
	{
		$this->enableErrorReporting();
		$this->_setUp();
	}

	public function enableErrorReporting()
	{
		if ( version_compare(PHP_VERSION, \'5.3\', \'>=\') === true )
		{
			error_reporting(E_ALL ^ E_DEPRECATED); // TODO >> 設定可能に
		}
		else
		{
			error_reporting(E_ALL); // TODO >> 設定可能に
		}

		ini_set(\'log_errors\', true);
		ini_set(\'display_errors\', true);
	}

	protected function _setUp()
	{
		$this->_setUpLogger();
		$this->_setUpErrorHandler();
		$this->_setUpExceptionHandler();
		$this->_setUpReporter(); // シャットダウン時にReporterをnewすると既にメモリが足りなくなっている可能性があるため予めメモリを確保しておく
		$this->_setUpShutdown();
		$this->_setUpFunctions();
	}

	protected function _setUpLogger()
	{
		$this->logger = new AdelieDebug_Debug_Logger();
	}

	protected function _setUpErrorHandler()
	{
		$this->errorHandler = new AdelieDebug_Debug_ErrorHandler($this->logger);
		$this->errorHandler->register();
	}

	protected function _setUpExceptionHandler()
	{
		$this->exceptionHandler = new AdelieDebug_Debug_ExceptionHandler($this->logger);
		$this->exceptionHandler->register();
	}

	protected function _setUpReporter()
	{
		$this->reporter = new AdelieDebug_Debug_Reporter_Html($this->logger); // TODO >> リポータの種類を設定できるようにする
		$this->reporter->setUp();
	}

	protected function _setUpShutdown()
	{
		$this->shutdown = new AdelieDebug_Debug_Shutdown($this->reporter);
		$this->shutdown->register();
	}

	protected function _setUpFunctions()
	{
		AdelieDebug_Debug_Dump::setLogger($this->logger);
		AdelieDebug_Debug_Trace::setLogger($this->logger);
		$this->_loadFunctions();
	}

	protected function _loadFunctions()
	{
		if ( defined(\'ADELIE_DEBUG_BUILD\') === true )
		{
			eval(AdelieDebug_Archive::$archive[\'/AdelieDebug/Debug/Function.php\']);
		}
		else
		{
			require_once dirname(__FILE__).\'/Function.php\';
		}
	}
}',
  '/AdelieDebug/Debug/Reporter/Html/Reportable.php' => '/**
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
		
		return true;
	}

	public function isCli()
	{
		return ( PHP_SAPI === \'cli\' );
	}

	public function isHtmlContent()
	{
		$headers = headers_list();

		foreach ( $headers as $header )
		{
			$header = trim($header);
			
			if ( preg_match(\'#content-type:#i\', $header) > 0 and preg_match(\'#content-type:\\s*text/html#i\', $header) == 0 )
			{
				return false;
			}
		}

		return true;
	}

	public function isXMLHttpRequest()
	{
		if ( isset($_SERVER[\'HTTP_X_REQUESTED_WITH\']) === false )
		{
			return false;
		}

		if ( $_SERVER[\'HTTP_X_REQUESTED_WITH\'] === \'XMLHttpRequest\' )
		{
			return true;
		}

		return false;
	}
}',
  '/AdelieDebug/Debug/Reporter/Html.php' => '/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class AdelieDebug_Debug_Reporter_Html extends AdelieDebug_Debug_Reporter
{
	protected $reportable = null;

	protected $hasContent = false;

	public function setUp()
	{
		$this->reportable = new AdelieDebug_Debug_Reporter_Html_Reportable();
	}

	public function report()
	{
		if ( $this->reportable->isReportable() === false )
		{
			return;
		}

		$this->_flushObContents();
		$this->_printContents();
	}

	protected function _flushObContents()
	{
		$contents = \'\';

		while ( ob_get_level() > 0 )
		{
			$contents .= ob_get_clean();
			$this->hasContent = true;
		}

		echo $contents;
	}

	protected function _printContents()
	{
		// TODO >> メモリ足りないときは素でvar_dump()する
		$application = new AdelieDebug_Application();
		$application->setPathinfo(\'/debug/report\');
		$application->setParameter(\'logger\', $this->logger);
		$application->setParameter(\'via\', __CLASS__);
		$application->setUp();
		$application->run();
		$result = $application->getResult();
		echo $result;
	}
}',
  '/AdelieDebug/Debug/Reporter.php' => '/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class AdelieDebug_Debug_Reporter
{
	protected $logger = null;

	public function __construct(AdelieDebug_Debug_Logger $logger)
	{
		$this->logger = $logger;
	}

	public function setUp()
	{
		// テンプレートメソッド
	}

	public function report()
	{
		echo \'<pre style="text-align:left;">\';
		var_dump($this->logger->getLogs());
		echo \'</pre>\';
	}
}',
  '/AdelieDebug/Debug/Shutdown.php' => '/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class AdelieDebug_Debug_Shutdown
{
	protected $reporter = null;

	public function __construct(AdelieDebug_Debug_Reporter $reporter)
	{
		$this->reproter = $reporter;
	}

	public function register()
	{
		register_shutdown_function(array($this, \'report\'));
	}

	public function report()
	{
		$this->reproter->report();
	}
}',
  '/AdelieDebug/Debug/Trace.php' => '/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class AdelieDebug_Debug_Trace
{
	protected static $logger = null;

	public static function setLogger(AdelieDebug_Debug_Logger $logger)
	{
		self::$logger = $logger;
	}

	public function trace($minus = 0)
	{
		ob_start();
		debug_print_backtrace();
		$trace = ob_get_clean();

		for ( $i = 0; $i < $minus; $i ++ )
		{
			$trace = preg_replace("/.*\\n#1/s", \'#1\', $trace);
			$trace = preg_replace (\'/^#(\\d+)/me\', \'\\\'#\\\' . ($1 - 1)\', $trace);
		}
		
		self::$logger->addTrace($trace);
	}
}',
  '/AdelieDebug/Debug/XoopsDebugger.php' => '/**
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

	public function __construct(AdelieDebug_Debug_Logger $logger)
	{
		$this->logger = $logger;
	}

	public function prepare()
	{
		$GLOBALS[\'xoopsErrorHandler\'] =& AdelieDebug_Debug_XoopsErrorHandler::getInstance();
		$GLOBALS[\'xoopsErrorHandler\']->activate(false);

		$xoopsLogger = AdelieDebug_Debug_XoopsLogger::instance();
		$xoopsLogger->setLogger($this->logger);
		$xoopsLogger->importParent();
		$GLOBALS[\'xoopsLogger\'] =& $xoopsLogger;
		$root = XCube_Root::getSingleton();
		$root->mController->mLogger = $xoopsLogger;
		$root->mController->mDB->setLogger($xoopsLogger);
	}
}',
  '/AdelieDebug/Debug/XoopsErrorHandler.php' => '/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class AdelieDebug_Debug_XoopsErrorHandler extends XoopsErrorHandler
{
	public function __construct()
	{
		// 親のコンストラクタの処理を封じる
	}

	public function getInstance()
	{
		static $instance = null;

		if ( $instance === null )
		{
			$instance = new self();
		}

		return $instance;
	}
}',
  '/AdelieDebug/Debug/XoopsLogger.php' => '/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class AdelieDebug_Debug_XoopsLogger extends XoopsLogger
{
	protected $logger = null;

	public function __construct()
	{
	}

	public function instance()
	{
		static $instance = null;

		if ( $instance === null)
		{
			$instance = new self();
		}

		return $instance;
	}

	public function setLogger(AdelieDebug_Debug_Logger $logger)
	{
		$this->logger = $logger;
	}

	public function importParent()
	{
		$logger = parent::instance();

		foreach ( $logger as $k => $v )
		{
			$this->$k = $v;
		}
		
		$this->_importQueryLogs();
	}

	/**
	 * log a database query
	 *
	 * @param   string  $sql    SQL string
	 * @param   string  $error  error message (if any)
	 * @param   int     $errno  error number (if any)
	 */
	public function addQuery($sql, $error = null, $errno = null)
	{
		$this->queries[] = array(\'sql\' => $sql, \'error\' => $error, \'errno\' => $errno);

		if ( $error )
		{
			$this->logger->addSqlError($sql, $error);
		}
		else
		{
			$this->logger->addSql($sql);
		}
	}

	protected function _importQueryLogs()
	{
		foreach ( $this->queries as $query )
		{
			if ( $query[\'error\'] )
			{
				$this->logger->addSqlError($query[\'sql\'], $query[\'error\']);
			}
			else
			{
				$this->logger->addSql($query[\'sql\']);
			}
		}
	}
}',
  '/AdelieDebug/Exception/NotFoundException.php' => '/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class AdelieDebug_Exception_NotFoundException extends RuntimeException
{
}',
  '/AdelieDebug/Library/Smarty.php' => '/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class_exists(\'Smarty\') or require_once XOOPS_ROOT_PATH.\'/class/smarty/Smarty.class.php\';

class AdelieDebug_Library_Smarty extends Smarty
{
	public function __construct()
	{
		parent::__construct();

		$this->compile_id         = null;
		$this->_canUpdateFromFile = true;
		$this->compile_check      = true;
		$this->compile_dir        = XOOPS_COMPILE_PATH;
		$this->left_delimiter     = \'<{\';
		$this->right_delimiter    = \'}>\';
		$this->force_compile      = false;
	}
}',
  '/AdelieDebug/Preload.php' => 'class AdelieDebug_Preload extends XCube_ActionFilter
{
	protected $debugger = null;

	public function preFilter()
	{
		$this->_bootstrap();
		$this->_setUp();
		$this->_registerEventListeners();
	}

	public function topAccessEventHandler()
	{
		if ( $this->_isAdelieDebugPage() === false )
		{
			return;
		}

		$application = new AdelieDebug_Application();
		$application->setUp();
		$application->run();
		$result = $application->getResult();
		echo $result;
		die;
	}

	public function setupDebugEventHandler($instance, $debugMode)
	{
		$instance = new AdelieDebug_Debug_XoopsDebugger($this->debugger->logger);
		$this->debugger->enableErrorReporting(); // Legacy_Controller::_setupDebugger() で error_reproting = 0 にされちゃってるので必要
	}

	protected function _bootstrap()
	{
		if ( defined(\'ADELIE_DEBUG_BUILD\') === true )
		{
			$classLoader = new AdelieDebug_Archive_ClassLoader();
			$classLoader->setIncludePath(\':eval:\');
			$classLoader->register();
		}
		else
		{
			require_once dirname(__FILE__).\'/Core/ClassLoader.php\';
			$classLoader = new AdelieDebug_Core_ClassLoader();
			$classLoader->setIncludePath(dirname(dirname(__FILE__)));
			$classLoader->register();
		}
	}

	protected function _setUp()
	{
		$this->debugger = new AdelieDebug_Debug_Main();
		$this->debugger->run();
	}

	protected function _registerEventListeners()
	{
		$this->mRoot->mDelegateManager->add(\'Legacypage.Top.Access\', array($this, \'topAccessEventHandler\'), 0);
		$this->mController->mSetupDebugger->add(array($this, \'setupDebugEventHandler\'), 99999);
	}

	protected function _isAdelieDebugPage()
	{
		return ( strpos($_SERVER[\'REQUEST_URI\'], \'index.php/debug\') !== false );
	}
}',
  '/AdelieDebug/Render/Smarty.php' => '/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class AdelieDebug_Render_Smarty extends AdelieDebug_Render
{
	public function render()
	{
		$template = $this->_getTempalte();
		$values = $this->getValues();
		$smarty = $this->_getSmarty();
		$smarty->assign($values);
		$result = $smarty->fetch($template);
		return $result;
	}

	protected function _getTempalte()
	{
		$template = ADELIE_DEBUG_DIR.\'/\'.$this->template.\'.tpl\';

		if ( file_exists($template) === false )
		{
			throw new RuntimeException("Template not found: ".$template);
		}
		
		return $template;
	}

	protected function _getSmarty()
	{
		return new AdelieDebug_Library_Smarty();
	}
}',
  '/AdelieDebug/Render/SmartyOnBuild.php' => '/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class AdelieDebug_Render_SmartyOnBuild extends AdelieDebug_Render_Smarty
{
	protected function _getTempalte()
	{
		$template = \'/AdelieDebug/\'.$this->template.\'.tpl\';
	
		if ( array_key_exists($template, AdelieDebug_Archive::$archive) === false )
		{
			throw new RuntimeException("Template not found: ".$template);
		}

		$filename = XOOPS_CACHE_PATH.\'/\'.trim(strtr($template, \'/\', \'_\'), \'_\');

		if ( file_exists($filename) === false or filemtime($filename) < ADELIE_DEBUG_BUILD_TIME )
		{
			file_put_contents($filename, AdelieDebug_Archive::$archive[$template]);
		}

		return $filename;
	}
}',
  '/AdelieDebug/Render.php' => '/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

abstract class AdelieDebug_Render extends AdelieDebug_Core_Render
{
}',
  '/AdelieDebug/Template/Error/InternalServerError.tpl' => '<h1><{$statusMessage}></h1>
<pre><{$exception}></pre>
',
  '/AdelieDebug/Template/Error/NotFound.tpl' => '<h1><{$statusMessage}></h1>
<pre><{$exception}></pre>
',
  '/AdelieDebug/Template/Report/Index.tpl' => '<style>
.adelieDebug {
	background: #fff;
	text-align: left;
	}

.adelieDebug p.h1 {
	font-size: 30px;
	font-weight: bold;
	}

.adelieDebug p.h2 {
	font-size: 20px;
	font-weight: bold;
	}

.adelieDebug p.h3 {
	font-size: 16px;
	font-weight: bold;
	}

.adelieDebug p {
	font-size: 15px;
	}

.adelieDebug pre {
	border:none; 
	text-align:left; 
	font-size: 15px; 
	font-family: Consolas,monospace;
	padding: 2px; 
	margin: 1px 0; 
	overflow: auto;
	white-space: -moz-pre-wrap; /* Mozilla */
	white-space: -pre-wrap;     /* Opera 4-6 */
	white-space: -o-pre-wrap;   /* Opera 7 */
	white-space: pre-wrap;      /* CSS3 */
	word-wrap: break-word;      /* IE 5.5+ */
	}

.adelieDebug pre.console {
	background-color: #ECECEC;
	border: 1px solid #ccc;
	}

.adelieDebug pre.SQL,
.adelieDebug pre.PHP {
	cursor: pointer;
}

.adelieDebug pre.info {
	background: #DFF2BF;
	color: #4F8A10;
	}

.adelieDebug pre.OVERLAP {
	background: #FEEFB3;
	color: #9F6000;
	}

.adelieDebug pre.MARK {
	color: #00529B;
	background-color: #BDE5F8;
	}

.adelieDebug pre.ERROR {
	background: #FFBABA;
	color: #D8000C;
	}

.adelieDebug table.data {
	border: 1px solid #eee;
	border-collapse: collapse;
	font-family: Consolas,monospace;
	width: 100%;
	}

.adelieDebug table.data th {
	border-top: 1px solid #E2F4FA;
	border-right: none;
	border-bottom: 1px solid #E2F4FA;
	border-left: none;
	padding: 2px;
	background-color: #333;
	color: #fff;
	font-weight: normal;
	font-size: 15px; 
	text-align: left;
	}

.adelieDebug table.data td {
	border-top: 1px solid #fff;
	border-right: 1px solid #ccc;
	border-bottom: 1px solid #ccc;
	border-left: 1px solid #fff;
	padding: 2px;
	font-size: 15px; 
	}
.adelieDebug table.data td:first-child {
	width: 200px;
	}

.adelieDebug table.data thead tr {
	border-top: 1px solid #E2F4FA;
	}

.adelieDebug table.data tbody tr {
	border-bottom: 1px solid #ccc;
	background-color: white;
	}

.adelieDebug table.data tbody tr:nth-child(even) {
	background-color: #ECECEC;
	}

.adelieDebug table.data tbody tr:hover {
	background-color: #ccc;
	}

.adelieDebug pre.expanded {
	height: auto;
	overflow-y: auto;
	cursor: pointer;
	}

.adelieDebug pre.shortened {
	height: 1em;
	overflow-y: hidden;
	cursor: pointer;
	}

.adelieDebug span.expandMore {
	float: left;
	}
</style>
<div class="adelieDebug">
	<p class="h1">Adelie Debug</p>
	<p class="h2">タイムライン</p>
	<div id="adelieDebugPhpErrors">
		<table class="data">
			<tr>
				<th>ms</th>
				<th>Type</th>
				<th>Message</th>
			</tr>
		<{foreach from=$logs item="log"}>
			<tr>
				<td style="width: 10px;"><{$log.ms}></td>
				<td><{$log.typeName}></td>
				<td>
					<{if $log.typeName == \'DUMP\'}>
						<{$log.message}>
					<{else}>
						<pre class="info <{$log.typeName}>"><{$log.message|escape}></pre>
						<{if $log.info}>
							<pre><{$log.info|escape}></pre>
						<{/if}>
					<{/if}>
				</td>
			</tr>
		<{/foreach}>
		</table>
	</div>

	<p class="h2">送信済ヘッダ</p>
	<div id="adelieDebugSentHeaders">
		<{strip}>
		<pre class="console">
			<{foreach from=$sentHeaders item="header"}>
				<{$header}><br />
			<{/foreach}>
		</pre>
		<{/strip}>
	</div>
	<p class="h2">リクエスト</p>
	<div id="adelieDebugRequest">
		<{foreach from=$requests key="name" item="request"}>
			<p class="h3"><{$name}></p>
			<{if $request}>
				<table class="data">
					<tr>
						<th>キー</th>
						<th>値</th>
					</tr>
					<{foreach from=$request key="key" item="value"}>
						<tr>
							<td><{$key}></td>
							<td><{$value}></td>
						</tr>
					<{/foreach}>
				</table>
			<{else}>
				<p>セットされている変数はありません。</p>
			<{/if}>
		<{/foreach}>
	</div>
</div>',
  '/AdelieDebug/Template/Top/Index.tpl' => '<h1>AdelieDebug</h1>',
  '/AdelieDebug/Theme/AdelieDebug/Main.tpl' => '<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8" />
		<title>AdelieDebug</title>
	</head>
	<body>
		<{$content}>
	</body>
</html>
',
);
}
