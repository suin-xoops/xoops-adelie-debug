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

class AdelieDebug_Controller_File_Index extends AdelieDebug_Controller
{
	protected $mimetypes = array(
		'css'  => 'text/css',
		'js'   => 'application/x-javascript',
		'png'  => 'image/png',
		'jpg'  => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'gif'  => 'image/gif',
	);

	public function run()
	{
		$filename = '/File/'.$this->app->parameter('filename');

		if ( $this->app->fileExists($filename) === false )
		{
			throw new AdelieDebug_Exception_NotFoundException("File not found: $filename");
		}

		$extension = pathinfo($filename, PATHINFO_EXTENSION);
		$mimetype  = $this->mimetypes[$extension];
		$contents  = $this->app->fileGetContents($filename);

		header('Content-Type: '.$mimetype);
		echo $contents;
		die;
	}
}
