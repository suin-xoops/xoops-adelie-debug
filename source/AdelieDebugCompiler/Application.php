<?php
/**
 * A_simple_description_for_this_script.
 *
 * @package    AdelieDebugCompiler
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2011 Suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class AdelieDebugCompiler_Application
{
	protected $dir = '';

	protected $config = array(
		'phpCommand'    => 'php',
		'preloadClass'  => 'AdelieDebug',
		'yuicompressor' => 'java -jar ~/bin/yuicompressor-2.4.6.jar',
	);

	protected $temporaryFileManager = null;
	protected $syntaxChecker = null;

	protected $source = '';

	public function __construct(array $config = array())
	{
		$this->config = array_merge($this->config, $config);
	}

	public function __get($name)
	{
		return $this->$name;
	}

	public function setUp()
	{
		$this->_setUpPaths();
		$this->_setUpTemporaryFileManager();
		$this->_setUpSyntaxChecker();
	}

	public function run()
	{
		try
		{
			$this->_initializeSource();
			$this->_addConstants();
			$this->_addClassLoader();
			$this->_addPreloadClass();
			$this->_addArchive();
			$this->_finalize();
			$this->_minimize();
			$this->_addDocComent();
			$this->_outputFile();
		}
		catch ( Exception $e )
		{
			echo $e;
			die(1);
		}
	}

	public function getResult()
	{
	}

	public function config($name)
	{
		return $this->config[$name];
	}

	protected function _setUpPaths()
	{
		$this->dir       = dirname(__FILE__);
		$this->targetDir = dirname(dirname(__FILE__)).'/AdelieDebug';
	}

	protected function _setUpTemporaryFileManager()
	{
		$this->temporaryFileManager = new AdelieDebugCompiler_TemporaryFileManager();
	}

	protected function _setUpSyntaxChecker()
	{
		$this->syntaxChecker = new AdelieDebugCompiler_SyntaxChecker($this);
	}

	protected function _initializeSource()
	{
		$this->source = "<?php ";
	}

	protected function _addConstants()
	{
		$now    = time();
		$constants  = "define('ADELIE_DEBUG_BUILD', true);\n";
		$constants .= "define('ADELIE_DEBUG_BUILD_TIME', $now);\n";
		$this->source .= $constants;
	}

	protected function _addClassLoader()
	{
		$classLoader = file_get_contents($this->dir.'/Resource/ClassLoader.php');
		$classLoader = substr($classLoader, 5);
		$this->source .= $classLoader;
	}

	protected function _addPreloadClass()
	{
		$preloadSource = file_get_contents($this->targetDir.'/Preload.php');
		$preloadSource = substr($preloadSource, 5);
		$this->source .= $preloadSource;
		$preloadClass = $this->config('preloadClass');

		if ( $preloadClass !== 'AdelieDebug_Preload' )
		{
			$this->source .= sprintf(file_get_contents($this->dir.'/Resource/Preload.php'), $preloadClass);
		}
	}

	protected function _addArchive()
	{
		$files = AdelieDebugCompiler_Finder::find($this->targetDir);

		$archiver = new AdelieDebugCompiler_Archiver($this);

		foreach ( $files as $file )
		{
			$archiver->add($file);
		}

		$this->source .= $archiver->archive();
	}

	protected function _finalize()
	{

	}

	protected function _minimize()
	{
		$compresser = new AdelieDebugCompiler_Compresser($this);
		$this->source = $compresser->compressPHP($this->source);
	}

	protected function _outputFile()
	{
		$preloadClass = $this->config('preloadClass');

		$filename = 'build/'.$preloadClass.'.class.php';
		$writtenBytes = file_put_contents($filename, $this->source);
		
		if ( $writtenBytes === false )
		{
			throw new RuntimeException("Failed to write file: $filename");
		}
	}

	protected function _addDocComent()
	{
		$docComment = $this->_getDocComment();
		$this->source = "<?php\n".$docComment.substr($this->source, 6);
	}

	protected function _getDocComment()
	{
		$docComment  = "/**\n";
		$docComment .= " * AdelieDebug - Powerful Debugger for XOOPS Cube Legacy & TOKYOPen\n";
		$docComment .= " * Copyright 2011-".date('Y')." Suin\n";
		$docComment .= " *\n";
		$docComment .= " * AdelieDebug is distributed under the terms of the GPL2 license\n";
		$docComment .= " * For more information visit https://github.com/suin/xoops-adelie-debug\n";
		$docComment .= " * Do not remove this copyright message\n";
		$docComment .= " */\n";
		return $docComment;
	}
}
