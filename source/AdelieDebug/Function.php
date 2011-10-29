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

define('ADELIE_DEBUG_FUNCTION_LOADED', true);

function adump()
{
	ob_start();
	$args = func_get_args();
	array_unshift($args, 1);
	call_user_func_array(array('AdelieDebug_Debug', 'dumpbt'), $args);
	$content = ob_get_clean();

	if ( ob_get_level() > 1 )
	{
		// バッファが効いていないと、echoできないのでプロセス終了時に
		AdelieDebug_Shutdown::$supendedDumps[] = $content;
	}
	else
	{
		echo $content;
	}
}

function amark($name = '')
{
	AdelieDebug_Debug::markQuery($name);
}