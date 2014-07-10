<?php

class AdelieDebug_Debug_XoopsLogger_XCL extends AdelieDebug_Debug_XoopsLogger
{
	public static function &instance()
	{
		static $instance = null;

		if ( $instance === null)
		{
			$instance = new self();
		}

		return $instance;
	}
}
