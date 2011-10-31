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

class AdelieDebugCompiler_Combiner_PHP extends AdelieDebugCompiler_Combiner
{
	protected $targetExtensions = array('php');
	protected $targetFiles = array();
	protected $dummyClasses = array(
		'Legacy_AbstractDebugger',
		'XoopsErrorHandler',
		'XoopsLogger',
		'Smarty',
		'XCube_ActionFilter',
	);

	protected $targetClasses = array();
	protected $targetClassFiles = array();

	public function run()
	{
		// クラスは継承順に陳列しないといけないので、
		// 一旦クラスを読み込み、クラスの継承関係を判別してから
		// スーパークラスから順に結合していく。
		$this->_setUpTargetFiles();
		$this->_defineDummyClasses();
		$this->_getTargetClasses();
		$this->_getTargetClassFiles();
		$this->_sortTargetFiles();
		$this->_combine();
		$this->_checkSyntax();
	}

	protected function _setUpTargetFiles()
	{
		$this->targetFiles = $this->_find($this->targetDir, $this->targetExtensions);
	}

	protected function _defineDummyClasses()
	{
		foreach ( $this->dummyClasses as $dummyClass )
		{
			eval("class $dummyClass {}");
		}
	}

	protected function _getTargetClasses()
	{
		$knownClasses = get_declared_classes();
		
		foreach ( $this->targetFiles as $targetFile )
		{
			require_once $targetFile;
		}
		
		$this->targetClasses = array_diff(get_declared_classes(), $knownClasses);
	}

	protected function _getTargetClassFiles()
	{
		$filenames = array();
	
		foreach ( $this->targetClasses as $targetClass )
		{
			$reflectionClass = new ReflectionClass($targetClass);
			$filenames[] = $reflectionClass->getFilename();
		}

		$filenames = array_unique($filenames);
		$this->targetClassFiles = $filenames;
	}

	protected function _sortTargetFiles()
	{
		$this->targetFiles = array_merge($this->targetClassFiles, $this->targetFiles);
		$this->targetFiles = array_unique($this->targetFiles); // 先勝ち
	}

	protected function _combine()
	{
		foreach ( $this->targetFiles as $targetFile )
		{
			$contents = file_get_contents($targetFile);
			
			if ( $contents === false )
			{
				throw new RuntimeException("Failed to read file: $targetFile");
			}
			
			$contents = substr($contents, 5); // this removes '<?php'
			$this->contents .= $contents;
		}
	}

	protected function _checkSyntax()
	{
		$result = $this->app->syntaxChecker->checkCode($this->contents);

		if ( $result === false )
		{
			throw new RuntimeException($this->app->syntaxChecker->getErrorMessage());
		}
	}
}
