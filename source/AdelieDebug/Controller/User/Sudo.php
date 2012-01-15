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

class AdelieDebug_Controller_User_Sudo extends AdelieDebug_Controller
{
	public function run()
	{
		if ( $this->app->request->isPost() === true )
		{
			$this->_substitute();
		}
	
		$this->output['nowUser'] = $this->_getNowUser();
		$this->output['users']   = $this->_getUsers();
		$this->_render();
	}

	protected function _substitute()
	{
		if ( isset($_POST['uid'][0]) === true )
		{
			$this->_substituteByUserId();
		}
		elseif ( isset($_POST['uname'][0]) === true )
		{
			$this->_substituteByUserName();
		}
	}

	protected function _substituteByUserId()
	{
		$userId = (int) $_POST['uid'];
		
		$userHandler =& xoops_getmodulehandler('users', 'user');
		$criteria =& new CriteriaCompo();
		$criteria->add(new Criteria('uid', $userId));
		$userModels =& $userHandler->getObjects($criteria);
		
		if ( count($userModels) !== 1 )
		{
			throw new RuntimeException("ユーザが見つかりません。");
		}
		
		if ( $userModels[0]->get('level') == 0 )
		{
			throw new RuntimeException("未承認のユーザです。");
		}

		$handler =& xoops_gethandler('user');
		$user =& $handler->get($userModels[0]->get('uid'));

		$this->_createSession($user);
	}
	
	protected function _substituteByUserName()
	{
		$userName = $_POST['uname'];

		$userHandler =& xoops_getmodulehandler('users', 'user');
		$criteria =& new CriteriaCompo();
		$criteria->add(new Criteria('uname', $userName));
		$userModels =& $userHandler->getObjects($criteria);

		if ( count($userModels) !== 1 )
		{
			throw new RuntimeException("ユーザが見つかりません。");
		}
		
		if ( $userModels[0]->get('level') == 0 )
		{
			throw new RuntimeException("未承認のユーザです。");
		}

		$handler =& xoops_gethandler('user');
		$user =& $handler->get($userModels[0]->get('uid'));

		$this->_createSession($user);
	}

	protected function _createSession($xoopsUser)
	{
		$_SESSION = array();
		$_SESSION['xoopsUserId']     = $xoopsUser->get('uid');
		$_SESSION['xoopsUserGroups'] = $xoopsUser->getGroups();
		$url = $this->app->request->getUrl();
		header('Location: '.$url);
		die;
	}

	protected function _getNowUser()
	{
		global $xoopsUser;
		
		if ( is_object($xoopsUser) === false )
		{
			return false;
		}
		
		$nowUser = array();
		$nowUser['uid']   = $xoopsUser->get('uid');
		$nowUser['name']  = $xoopsUser->get('name');
		$nowUser['uname'] = $xoopsUser->get('uname');

		return $nowUser;
	}

	protected function _getUsers()
	{
		$db = Database::getInstance();
		$query = "SELECT uid, uname, name FROM %s WHERE level > 0 ORDER BY uid ASC";
		$query = sprintf($query, $db->prefix('users'));

		$result = $db->query($query);

		$users = array();
		
		while ( $user = $db->fetchArray($result) )
		{
			$users[] = $user;
		}
		
		return $users;
	}
}
