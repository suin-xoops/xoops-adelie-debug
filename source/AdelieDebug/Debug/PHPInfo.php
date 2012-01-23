<?php

class AdelieDebug_Debug_PHPInfo
{
	// TODO >> export to configuration file.
	protected $config = array(
		'memory'   => array(
			'memory_peak_usage' => array('memoryPeakUsage'),
			'memory_limit'      => array('ini', 'memory_limit'),
		),
		'encoding' => array(
			'output_buffering'              => array('iniOnOff', 'output_buffering'),
			'default_charset'               => array('ini', 'default_charset'),
			'mbstring.language'             => array('ini', 'mbstring.language'),
			'mbstring.encoding_translation' => array('iniOnOff', 'mbstring.encoding_translation'),
			'mbstring.http_input'           => array('ini', 'mbstring.http_input'),
			'mbstring.http_output'          => array('ini', 'mbstring.http_output'),
			'mbstring.internal_encoding'    => array('ini', 'mbstring.internal_encoding'),
			'mbstring.substitute_character' => array('ini', 'mbstring.substitute_character'),
			'mbstring.detect_order'         => array('ini', 'mbstring.detect_order'),
		),
	);

	public function summary()
	{
		$summary = array();

		foreach ( $this->config as $categoryName => $info )
		{
			$summary[$categoryName] = array();

			foreach ( $info as $name => $callback )
			{
				$summary[$categoryName][$name] = $this->_get($callback);
			}
		}

		return $summary;
	}

	protected function _get(array $callback)
	{
		$method = '_'.array_shift($callback);

		if ( method_exists($this, $method) === false )
		{
			throw new RuntimeException(__CLASS__.': method does not exit: '.$method);
		}

		return call_user_func_array(array($this, $method), $callback);
	}

	protected function _memoryPeakUsage()
	{
		return AdelieDebug_TextFormat::bytes(memory_get_peak_usage());
	}

	protected function _ini($name)
	{
		return ini_get($name);
	}

	protected function _iniOnOff($name)
	{
		if ( ini_get($name) )
		{
			return 'On';
		}
		else
		{
			return 'Off';
		}
	}
}
