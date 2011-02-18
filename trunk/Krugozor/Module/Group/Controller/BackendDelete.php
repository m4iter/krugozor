<?php
class Module_Group_Controller_BackendDelete extends Module_Group_Controller_BackendCommon
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            return $this->createNotification()
                        ->setMessage('forbidden_access')
                        ->setType('alert')
                        ->setRedirectUrl(array('admin', 'group'))
                        ->run();
        }

        if ($result = $this->checkIdOnValid())
        {
            return $result;
        }

        if (empty($this->getRequest()->getRequest()->id))
        {
            return $this->createNotification()
                        ->setType('alert')
                        ->setMessage('id_group_not_exists')
                        ->setRedirectUrl(array('admin','group'))
                        ->run();
        }

        $this->getMapper('Group/Group')->delete($this->group);

        return $this->createNotification()
                    ->setMessage('group_delete')
                    ->addParam('group_name', Helper_Format::hsc($this->group->getName()))
                    ->setRedirectUrl($this->getRequest()->getRequest('referer'))
                    ->run();
    }
}