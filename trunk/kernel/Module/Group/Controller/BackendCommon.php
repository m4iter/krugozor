<?php
abstract class Module_Group_Controller_BackendCommon extends Module_Common_Controller_Common
{
    protected $group;

    protected function checkIdOnValid()
    {
        if ($id = $this->getRequest()->getRequest('id'))
        {
            if (!Base_Numeric::is_decimal($id))
            {
                $redirect = new Base_Redirect();
                $redirect->setType('alert');
                $redirect->setMessage('bad_id_group');
                $redirect->setRedirectUrl(array('admin','group'));
                return $redirect->run();
            }

            $this->group = self::getMapper('Group/Group')->findById($this->getRequest()->getRequest('id'));

            if (!$this->group->getId())
            {
                $redirect = new Base_Redirect();
                $redirect->setType('alert');
                $redirect->setMessage('group_does_not_exist');
                $redirect->addParam('id', $this->getRequest()->getRequest('id'));
                $redirect->setRedirectUrl(array('admin', 'group'));
                return $redirect->run();
            }
        }
    }

    protected function init()
    {
        $this->getView()->loadI18n('Common/BackendGeneral', $this->getVirtualControllerPath());

        $this->getView()->getHelper('Html_Title')->add($this->getView()->lang['title']);
    }
}