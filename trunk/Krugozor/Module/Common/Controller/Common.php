<?php
abstract class Module_Common_Controller_Common extends Base_Controller
{
    /**
     * ������ �������� ������������.
     *
     * @var Module_User_Model_User
     */
    private $current_user;

    /**
     * ������� �� ���� �������/������������ �������
     * ��� ������������ ������� ������.
     *
     * @var array
     */
    private $current_user_accesses;

    /**
     * @see parent::initViewVars()
     */
    protected function initViewVars()
    {
        $this->getView()->err = new Cover_Array();

        $this->getView()->_module_name = $this->getRequest()->getRequest()->getModuleName()->getUriStyle();
        $this->getView()->_controller_name = $this->getRequest()->getRequest()->getControllerName()->getUriStyle();

        $this->getView()->http_host = 'http://' . $_SERVER['HTTP_HOST'];

        $this->getView()->request_uri = Helper_Format::stripNotifQS($_SERVER['REQUEST_URI']);
        $this->getView()->urlencode_request_uri = urlencode($this->getView()->request_uri);
        $this->getView()->hsc_request_uri = Helper_Format::hsc($this->getView()->request_uri);

        $this->getView()->full_request_uri = 'http://' . $_SERVER['HTTP_HOST'] .
                                             Helper_Format::stripNotifQS($_SERVER['REQUEST_URI']);
        $this->getView()->urlencode_full_request_uri = urlencode($this->getView()->full_request_uri);
        $this->getView()->hsc_full_request_uri = Helper_Format::hsc($this->getView()->full_request_uri);
    }

    /**
     * ����� ��� ���� ������������ �����.
     *
     * @param void
     * @return void
     */
    protected function common()
    {
        $this->getResponse()->setHeader('Content-type', 'text/html; charset=windows-1251')
                            ->setHeader('Content-Language', Base_Registry::getInstance()->config['lang'])
                            ->setHeader('Expires', 'Mon, 26 Jul 2008 05:00:00 GMT')
                            ->setHeader('Last-Modified', gmdate("D, d M Y H:i:s") . " GMT")
                            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
                            ->setHeader('Pragma', 'no-cache');
    }

    /**
     * ���������� ����� (COOKIE) �������� ������������.
     *
     * @param void
     * @return void
     */
    protected function destroyCurrentUser()
    {
        $time = time() - 60*60*24*31;

        $this->getResponse()->setCookie('auth_id', '', $time, '/');
        $this->getResponse()->setCookie('auth_hash', '', $time, '/');
    }

    /**
     * ���������� ������ �������� ������������.
     *
     * @param void
     * @return Module_User_Model_User
     */
    protected function getCurrentUser()
    {
        if ($this->current_user === null)
        {
            $this->loadCurrentUser();
        }

        return $this->current_user;
    }

    /**
     * ��������� ������ �������� ������������ �
     * ����������� $controller_key ������ $module_key.
     * ���������� TRUE, ���� ������ �������� � FALSE � ��������� ������.
     *
     * @param string $module_key
     * @param string $controller_key
     * @return boolean
     */
    protected function checkAccess($module_key=null, $controller_key=null)
    {
        if ($this->current_user_accesses === null)
        {
            $this->current_user_accesses = $this->getMapper('Group/Access')->getGroupAccessByIdWithControllerNames(
                                                    $this->getCurrentUser()->getGroup()
                                                  );
        }

        $module_key     = $module_key ?: $this->getRequest()->getRequest()->getModuleName()->getCamelCaseStyle();
        $controller_key = $controller_key ?: $this->getRequest()->getRequest()->getControllerName()->getCamelCaseStyle();

        return isset($this->current_user_accesses[$module_key][$controller_key])
               ? $this->current_user_accesses[$module_key][$controller_key] &&
                 ($this->getCurrentUser()->isGuest() ? true : $this->getCurrentUser()->getActive())
               : false;
    }

    /**
     * ���������� "�����������" ���� ��� �������� �����������.
     * ���� ������ ����� ������� ���������� Module_User_Controller_BackendEdit,
     * �� ����� ������ ������ "User/BackendEdit".
     * ������ ����� ����������� ��� ���������� ��������� ����������� ����� �
     * ������ �������������������.
     *
     * @param void
     * @return string
     */
    protected function getVirtualControllerPath()
    {
        return $this->getRequest()->getRequest()->getModuleName()->getCamelCaseStyle() . '/' .
               $this->getRequest()->getRequest()->getControllerName()->getCamelCaseStyle();
    }

    /**
     * ��������� �������� ������������ �� ���������� ������������� �����������
     * �� ��������� ��������� �� COOKIEs.
     *
     * @param void
     * @return void
     */
    private function loadCurrentUser()
    {
        if (!empty($this->getRequest()->getCookie()->auth_id) &&
            !empty($this->getRequest()->getCookie()->auth_hash))
        {
            $this->current_user = $this->getMapper('User/User')->findById(
                $this->getRequest()->getCookie('auth_id')
            );

            if (is_object($this->current_user) &&
                md5($this->current_user->getLogin() . $this->current_user->getPassword())
                === $this->getRequest()->getCookie('auth_hash')
               )
            {
                $this->getMapper('User/User')->updateActualInfo($this->current_user);

                return;
            }
            else
            {
                $this->destroyCurrentUser();
            }
        }

        $this->current_user = $this->getMapper('User/User')->findById(-1);
    }
}