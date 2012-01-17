<?php

class AdelieDebug_Controller_Console_LogList extends AdelieDebug_Controller
{
	public function run()
	{
		define('ADELIE_DEBUG_DISABLE_CONSOLE_LOG', true);

		$files = glob(XOOPS_CACHE_PATH.'/AdelieDebugLog*.json');
		
		if ( is_array($files) === false )
		{
			// TODO >> error
		}

		sort($files);

		$list = array();

		foreach ( $files as $file )
		{
			
		}

	}
}
