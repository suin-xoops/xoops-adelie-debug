<?php

class AdelieDebug_Debug_XoopsLogger_TP extends AdelieDebug_Debug_XoopsLogger
{
	public static function instance()
	{
		static $instance = null;

		if ( $instance === null)
		{
			$instance = new self();
		}

		return $instance;
	}
}
