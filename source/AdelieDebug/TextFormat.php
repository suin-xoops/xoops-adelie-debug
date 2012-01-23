<?php

class AdelieDebug_TextFormat
{
	/**
	 * バイト数をフォーマットする
	 * @param integer $bytes
	 * @param integer $precision
	 * @param array $units
	 */
	public static function bytes($bytes, $precision = 2, array $units = null)
	{
		if ( abs($bytes) < 1024 )
		{
			$precision = 0;
		}
	
		if ( is_array($units) === false )
		{
			$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		}
	
		if ( $bytes < 0 )
		{
			$sign = '-';
			$bytes = abs($bytes);
		}
		else
		{
			$sign = '';
		}
	
		$exp   = floor(log($bytes) / log(1024));
		$unit  = $units[$exp];
		$bytes = $bytes / pow(1024, floor($exp));
		$bytes = sprintf('%.'.$precision.'f', $bytes);
		return $sign.$bytes.' '.$unit;
	}
}
