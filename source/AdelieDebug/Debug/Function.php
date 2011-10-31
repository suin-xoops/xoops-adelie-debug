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
	$args = func_get_args();
	array_unshift($args, 1);
	call_user_func_array(array('AdelieDebug_Debug_Dump', 'dumpbt'), $args);
}

function atrace()
{
	AdelieDebug_Debug_Trace::trace(1);
}