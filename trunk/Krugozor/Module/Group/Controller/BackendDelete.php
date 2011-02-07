<?php
class Module_Group_Controller_BackendDelete extends Module_Group_Controller_BackendCommon
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            $redirect = new Base_Redirect($this->getDb());
            return $redirect->setMessage('forbidden_access')->setType('alert')
                            ->setRedirectUrl(array('admin', 'group'))->run();
        }

        if ($result = $this->checkIdOnValid())
        {
            return $result;
        }

        if (empty($this->getRequest()->getRequest()->id))
        {
            $redirect = new Base_Redirect($this->getDb());
            $redirect->setType('alert');
            $redirect->setMessage('id_group_not_exists');
            $redirect->setRedirectUrl(array('admin','group'));
            return $redirect->run();
        }

        $this->getMapper('Group/Group')->delete($this->group);

        $redirect = new Base_Redirect($this->getDb());
        $redirect->setMessage('group_delete');
        $redirect->addParam('group_name', Helper_Format::hsc($this->group->getName()));
        $redirect->setRedirectUrl($this->getRequest()->getRequest('referer'));
        return $redirect->run();
    }
}