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

class AdelieDebugCompiler_SyntaxChecker
{
	protected $app = null;

	protected $errorMessage = '';

	public function __construct(AdelieDebugCompiler_Application $app)
	{
		$this->app = $app;
	}

	public function checkCode($contents, $prefixPhpTag = true)
	{
		if ( $prefixPhpTag === true )
		{
			$contents = '<?php '.$contents;
		}

		$filename = $this->app->temporaryFileManager->create($contents);
		return $this->checkFile($filename);
	}

	public function checkFile($filename)
	{
		$phpCommand = $this->app->config('phpCommand');
		$command    = sprintf('%s -l %s 2>&1', $phpCommand, $filename);
		exec($command, $output, $result);

		if ( $result > 0 )
		{
			$this->errorMessage = implode("\n", $output);
			return false;
		}
		
		return true;
	}

	public function getErrorMessage()
	{
		return $this->errorMessage;
	}
}
