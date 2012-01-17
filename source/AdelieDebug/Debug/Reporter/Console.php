<?php

class AdelieDebug_Debug_Reporter_Console extends AdelieDebug_Debug_Reporter
{
	public function report()
	{
		if ( defined('ADELIE_DEBUG_DISABLE_CONSOLE_LOG') === true )
		{
			return;
		}

		if ( isset($_SESSION) === false )
		{
			$_SESSION = array();
		}

		$sentHeaders = headers_list();
		$requests = array(
			'$_GET'     => $_GET, 
			'$_POST'    => $_POST, 
			'$_SESSION' => $_SESSION,
			'$_COOKIE'  => $_COOKIE,
			'$_FILES'   => $_FILES,
			'$_SERVER'  => $_SERVER,
		);

		$request = new AdelieDebug_Core_Request();

		$data = array(
			'time'         => time(),
			'url'          => $request->getUrl(),
			'errorSummary' => $this->logger->getErrorSummary(),
			'timeline'     => $this->logger->getLogs(),
			'sentHeaders'  => $sentHeaders,
			'requests'     => $requests,
		);

		$filename = XOOPS_CACHE_PATH.'/AdelieDebugLog_'.date('YmdHis').'_'.uniqid().'.json';
		file_put_contents($filename, json_encode($data));
	}
}
