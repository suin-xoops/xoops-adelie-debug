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

abstract class AdelieDebug_Core_Controller
{
	/**
	 * @AdelieDebug_Core_Application
	 */
	protected $app = null;

	/**
	 * 結果
	 */
	protected $result = null;

	/**
	 * ページタイトル
	 */
	protected $pageTitle    = '';
	protected $pageSubtitle = '';

	/**
	 * View関係
	 */
	protected $output = array();
	protected $controllerTemplate = '';
	protected $actionTemplate     = '';
	protected $themeTemplate      = 'Main';

	public function __construct(AdelieDebug_Core_Application $app)
	{
		$this->app = $app;
		$this->controllerTemplate = $this->app->parameter('Controller');
		$this->actionTemplate     = $this->app->parameter('Action');
	}

	/**
	 * 初期化用テンプレートメソッド
	 * 
	 * @access public
	 * @return void
	 */
	public function setUp()
	{
	}

	/**
	 * メイン処理。実装してね.
	 * 
	 * @access public
	 * @abstract
	 * @return void
	 */
	abstract public function run();

	/**
	 * 結果を返す.
	 * 
	 * @access public
	 * @return mixed
	 */
	public function getResult()
	{
		return $this->result;
	}

	/**
	 * 描画結果をresultに代入する.
	 * 
	 * @access protected
	 * @return void
	 */
	protected function _render()
	{
		$content      = $this->_renderTemplate();
		$this->result = $this->_renderTheme(array('content' => $content));
	}

	protected function _renderTemplate()
	{
		$renderClass = $this->app->config('render.class');
		$render = new $renderClass($this->app);
		$values = $this->_getTemplateValues();
		$values = array_merge($values, $this->output);
		$render->setValues($values);
		$render->setTemplate($this->_getTemplate());
		return $render->render();
	}

	protected function _getTemplate()
	{
		return 'Template/'.$this->controllerTemplate.'/'.$this->actionTemplate;
	}

	protected function _getThemeValues(array $_values)
	{
		$values = $this->_getTemplateValues();
		$values = array_merge($values, $_values);
		return $values;
	}

	protected function _getTemplateValues()
	{
		return array(
			'app'          => $this->app,
			'baseUrl'      => $this->app->request->getBaseUrl(),
			'siteUrl'      => $this->app->request->getSiteUrl(),
			'siteBaseUrl'  => $this->app->request->getSiteBaseUrl(),
			'pageTitle'    => $this->pageTitle,
			'pageSubtitle' => $this->pageSubtitle,
		);
	}

	protected function _renderTheme($content)
	{
		$renderClass = $this->app->config('render.class');
		$values = $this->_getThemeValues($content);
		$render = new $renderClass($this->app);
		$render->setValues($values);
		$render->setTemplate($this->_getTheme());
		return $render->render();
	}

	protected function _getTheme()
	{
		return 'Theme/'.$this->app->config('theme.name').'/'.$this->themeTemplate;
	}
}
