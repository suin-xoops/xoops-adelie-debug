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

class AdelieDebug_Debug_ErrorHandler
{
	protected $errorTypes = array (
		E_ERROR              => 'ERROR',
		E_WARNING            => 'WARNING',
		E_PARSE              => 'PARSING ERROR',
		E_NOTICE             => 'NOTICE',
		E_CORE_ERROR         => 'CORE ERROR',
		E_CORE_WARNING       => 'CORE WARNING',
		E_COMPILE_ERROR      => 'COMPILE ERROR',
		E_COMPILE_WARNING    => 'COMPILE WARNING',
		E_USER_ERROR         => 'USER ERROR',
		E_USER_WARNING       => 'USER WARNING',
		E_USER_NOTICE        => 'USER NOTICE',
		E_STRICT             => 'STRICT NOTICE',
		E_RECOVERABLE_ERROR  => 'RECOVERABLE ERROR',
	);

	protected $logger = null;

	public function __construct(AdelieDebug_Debug_Logger $logger)
	{
		$this->logger = $logger;
		$this->_setUpErrorTypes();
	}

	public function register()
	{
		set_error_handler(array($this, 'callback'));
	}

	public function callback($level, $message, $file, $line)
	{
		if ( ( $level & error_reporting() ) != $level )
		{
			return true;
		}

		ob_start();
		debug_print_backtrace();
		$trace = ob_get_clean();
		$trace = preg_replace("/.*\n#1/s", '#1', $trace);
		$trace = preg_replace ('/^#(\d+)/me', '\'#\' . ($1 - 1)', $trace); 

		$this->_add($level, $message, $file, $line, $trace);

		return true;
	}

	protected function _setUpErrorTypes()
	{
		if ( version_compare(PHP_VERSION, '5.2', '>=') === true )
		{
			$this->errorTypes[E_RECOVERABLE_ERROR] = 'RECOVERABLE ERROR';
		}

		if ( version_compare(PHP_VERSION, '5.3', '>=') === true )
		{
			$this->errorTypes[E_DEPRECATED]      = 'DEPRECATED';
			$this->errorTypes[E_USER_DEPRECATED] = 'USER_DEPRECATED';
		}
	}

	protected function _getFormatedError(array $error, $format = "{type}: {message} in {file} on line {line}")
	{
		$message = str_replace('{type}', $error['type'], $format);
		$message = str_replace('{message}', $error['message'], $message);
		$message = str_replace('{file}', $error['file'], $message);
		$message = str_replace('{line}', $error['line'], $message);

		return $message;
	}

	protected function _add($level, $message, $file, $line, $trace)
	{
		$error = array(
			'type'    => $this->_getType($level),
			'level'   => $level,
			'message' => $message,
			'file'    => $file,
			'line'    => $line,
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
		
		return 'UNKNOWN ERROR('.$level.')';
	}
}
