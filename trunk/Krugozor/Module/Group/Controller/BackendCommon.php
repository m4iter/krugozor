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
                return $this->createNotification()
                            ->setType('alert')
                            ->setMessage('bad_id_group')
                            ->setRedirectUrl(array('admin','group'))
                            ->run();
            }

            $this->group = $this->getMapper('Group/Group')->findById(
                $this->getRequest()->getRequest('id')
            );

            if (!$this->group->getId())
            {
                return $this->createNotification()
                            ->setType('alert')
                            ->setMessage('group_does_not_exist')
                            ->addParam('id', $this->getRequest()->getRequest('id'))
                            ->setRedirectUrl(array('admin', 'group'))
                            ->run();
            }
        }
    }

    protected function init()
    {
        $this->getView()->loadI18n('Common/BackendGeneral', $this->getVirtualControllerPath());

        $this->getView()->getHelper('Html_Title')->add($this->getView()->lang['title']);
    }
}