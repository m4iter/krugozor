<?php
abstract class Module_Common_Controller_Common extends Base_Controller
{
    /**
     * Объект текущего пользователя.
     *
     * @access protected
     * @var Module_User_Model_User
     */
    protected $current_user;

    /**
     * Правила доступа ко всем контроллерам системы
     * для пользователя текущей группы.
     *
     * @access protected
     * @var array
     */
    protected $rules;

    /**
     * @see parent::initViewVars()
     */
    protected function initViewVars()
    {
        $this->getView()->err = new Cover_Array();

        $this->getView()->_module_name = $this->getRequest()->getRequest()->getModuleName()->getUriStyle();
        $this->getView()->_controller_name = $this->getRequest()->getRequest()->getControllerName()->getUriStyle();

        $this->getView()->http_host = 'http://'.$_SERVER['HTTP_HOST'];

        $this->getView()->request_uri = Helper_Format::stripNotifQS($_SERVER['REQUEST_URI']);
        $this->getView()->urlencode_request_uri = urlencode($this->getView()->request_uri);
        $this->getView()->hsc_request_uri = Helper_Format::hsc($this->getView()->request_uri);

        $this->getView()->full_request_uri = 'http://'.$_SERVER['HTTP_HOST'].Helper_Format::stripNotifQS($_SERVER['REQUEST_URI']);
        $this->getView()->urlencode_full_request_uri = urlencode($this->getView()->full_request_uri);
        $this->getView()->hsc_full_request_uri = Helper_Format::hsc($this->getView()->full_request_uri);

        $this->getView()->path = Base_Registry::getInstance()->path['http'];
    }

    protected function common()
    {
        $this->getResponse()->setHeader('Content-type', 'text/html; charset=windows-1251')->
        setHeader('Content-Language', Base_Registry::getInstance()->config->lang)->
        setHeader('Expires', 'Mon, 26 Jul 2008 05:00:00 GMT')->
        setHeader('Last-Modified', gmdate("D, d M Y H:i:s")." GMT")->
        setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')->
        setHeader('Pragma', 'no-cache');
    }

    /**
     * Загружает текущего пользователя во
     * внутреннее представление контроллера.
     *
     * @access protected
     * @param void
     * @return void
     */
    protected function loadCurrentUser()
    {
        if (!empty($this->getRequest()->getCookie()->auth_id) &&
            !empty($this->getRequest()->getCookie()->auth_hash))
        {
            $this->current_user = self::getMapper('User/User')->findByLoginHash(
                $this->getRequest()->getCookie('auth_id'),
                $this->getRequest()->getCookie('auth_hash'),
                Base_Registry::getInstance()->config['user_cookie_salt']
            );

            // Если у пользователя невалидные cookie, убиваем их.
            if (!is_object($this->current_user) || $this->current_user->getId() == 0)
            {
                $this->destroyCurrentUser();
            }
            else
            {
                self::getMapper('User/User')->updateActualInfo($this->current_user);

                $this->current_user->setPassword(null);
            }
        } // гость
        else
        {
            $this->current_user = self::getMapper('User/User')->findById(-1);
        }
    }

    /**
     * Уничтожает сеанс текущего пользователя.
     *
     * @access protected
     * @param void
     * @return void
     */
    protected function destroyCurrentUser()
    {
        $time = time()-60*60*24*31;

        $this->getResponse()->setcookie('auth_id', '', $time, '/');
        $this->getResponse()->setcookie('auth_hash', '', $time, '/');

        $redirect = new Base_Redirect();
        $redirect->setHidden(1);
        $redirect->setRedirectUrl($_SERVER['REQUEST_URI']);
        return $redirect->run();
    }

    /**
     * Возвращает объект текущего пользователя.
     * Если объект ещё не создан, создает его.
     *
     * @access protected
     * @param void
     * @return Module_User_Model_User объект пользователя
     */
    protected function getCurrentUser()
    {
        if (null === $this->current_user)
        {
            $this->loadCurrentUser();
        }

        return $this->current_user;
    }

    /**
     * Проверяет доступ текущего пользователя к
     * контроллеру $controller_key модуля $module_key.
     * Возвращает TRUE, если доступ разрешён и FALSE в противном случае.
     *
     * @access protected
     * @param string $module_key
     * @param string $controller_key
     * @return boolean
     */
    protected function checkAccess($module_key=null, $controller_key=null)
    {
        if (!$this->rules)
        {
            $access = new Base_Access();
            $this->rules = $access->getGroupRulesByIdWithControllerNames($this->getCurrentUser()->getGroup());
        }

        $module_key = $module_key
                      ? $module_key
                      : $this->getRequest()->getRequest()->getModuleName()->getCamelCaseStyle();

        $controller_key = $controller_key
                          ? $controller_key
                          : $this->getRequest()->getRequest()->getControllerName()->getCamelCaseStyle();

        return isset($this->rules[$module_key][$controller_key])
               ? $this->rules[$module_key][$controller_key] &&
                 ($this->getCurrentUser()->isGuest()
                  ? 1
                  : $this->getCurrentUser()->getActive()
                 )
               : 0;
    }

    /**
     * Возвращает "виртуальный" путь для текущего контроллера.
     * Если данный метод вызовет контроллер Module_User_Controller_BackendEdit,
     * то метод вернет строку "User/BackendEdit".
     * Данный метод применяется для облегчения написания виртуальных путей к
     * файлам интернационализации.
     *
     * @param void
     * @return void
     */
    protected function getVirtualControllerPath()
    {
        return $this->getRequest()->getRequest()->getModuleName()->getCamelCaseStyle() . '/' .
               $this->getRequest()->getRequest()->getControllerName()->getCamelCaseStyle();
    }
}
?>