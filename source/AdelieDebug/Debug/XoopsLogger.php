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
		$this->queries[] = array('sql' => $sql, 'error' => $error, 'errno' => $errno);

		if ( $error )
		{
			$trace = AdelieDebug_Debug_Trace::trace(2, true);
			$this->logger->addSqlError($sql, $error, $trace);
		}
		else
		{
			$this->logger->addSql($sql);
		}
	}

	public function clearQuery()
	{
		$this->logger->addMessage('clearQuery() was commanded. But this request was rejected by AdelieDebug.');
	}

	protected function _importQueryLogs()
	{
		foreach ( $this->queries as $query )
		{
			if ( $query['error'] )
			{
				$this->logger->addSqlError($query['sql'], $query['error']);
			}
			else
			{
				$this->logger->addSql($query['sql']);
			}
		}
	}
}
