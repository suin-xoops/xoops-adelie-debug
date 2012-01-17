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

class AdelieDebug_Debug_Logger
{
	const TYPE_UNKOWN    = 1;
	const TYPE_PHP_ERROR = 2;
	const TYPE_DUMP      = 4;
	const TYPE_SQL       = 8;
	const TYPE_SQL_ERROR = 16;
	const TYPE_SQL_MARK  = 32;
	const TYPE_TRACE     = 64;
	const TYPE_MESSAGE   = 128;
	const TYPE_SYNOPSYS  = 256;
	const TYPE_TRIGGER_DELEGATE = 512;

	protected $typeNames = array(
		self::TYPE_UNKOWN    => 'UNKNOWN',
		self::TYPE_PHP_ERROR => 'PHP ERROR',
		self::TYPE_DUMP      => 'DUMP',
		self::TYPE_SQL       => 'SQL',
		self::TYPE_SQL_ERROR => 'SQL ERROR',
		self::TYPE_SQL_MARK  => 'SQL MARK',
		self::TYPE_TRACE     => 'TRACE',
		self::TYPE_MESSAGE   => 'MESSAGE',
		self::TYPE_SYNOPSYS  => 'SYNOPSYS',
		self::TYPE_TRIGGER_DELEGATE => 'DELEGATE',
	);

	protected $id = 0;

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

	/**
	 * エラーの概要を返す.
	 * @return array
	 */
	public function getErrorSummary()
	{
		$errors = array();

		foreach ( $this->logs as $log )
		{
			if ( $log['isError'] === false )
			{
				continue;
			}

			$typeName = $log['typeName'];
			$errors[$typeName][] = $log['id'];
		}
		
		return $errors;
	}

	public function add($message, $type = self::TYPE_UNKOWN, $isError = false, $info = '')
	{
		$now = microtime(true) - $this->initTime;
	
		$this->logs[] = array(
			'id'       => $this->_incrementId(),
			'type'     => $type,
			'typeName' => $this->typeNames[$type],
			'message'  => $message,
			'time'     => $now,
			'ms'       => round( $now * 1000 ),
			'isError'  => $isError,
			'info'     => $info,
		);
	}

	public function addPhpError($error, $trace = '')
	{
		$this->add($error, self::TYPE_PHP_ERROR, true, $trace);
	}

	public function addDump($message)
	{
		$this->add($message, self::TYPE_DUMP);
	}

	public function addSql($message, $info = '')
	{
		$this->add($message, 8, false, $info); // Database Sessionでエラーになるため定数を使わない。
	}

	public function addSqlError($message, $error, $trace)
	{
		$this->add($message, self::TYPE_SQL_ERROR, true, $error."\n".$trace);
	}

	public function addSqlMark($message)
	{
		$this->add($message, self::TYPE_SQL_MARK);
	}

	public function addTrace($message)
	{
		$this->add($message, self::TYPE_TRACE);
	}

	public function addMessage($message)
	{
		$this->add($message, self::TYPE_MESSAGE);
	}

	public function addSynopsys($synopsys)
	{
		$this->add($synopsys, self::TYPE_SYNOPSYS);
	}

	public function addTriggerDelegate($delegateName)
	{
		$message = sprintf("triggered %s", $delegateName);
		$this->add($message, self::TYPE_TRIGGER_DELEGATE);
	}

	protected function _incrementId()
	{
		$this->id += 1;
		return $this->id;
	}
}
