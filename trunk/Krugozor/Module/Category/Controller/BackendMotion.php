<?php
class Module_Category_Controller_BackendMotion extends Module_Category_Controller_BackendCommon
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            $redirect = new Base_Redirect($this->getDb());
            $redirect->setMessage('forbidden_access');
            $redirect->setType('alert');
            $redirect->setRedirectUrl(array('admin', 'category'));
            return $redirect->run();
        }

        if ($result = $this->checkIdOnValid())
        {
            return $result;
        }

        if (!isset($this->getRequest()->getRequest()->id))
        {
            $redirect = new Base_Redirect($this->getDb());
            $redirect->setType('alert');
            $redirect->setMessage('element_not_exists');
            $redirect->setRedirectUrl(array('admin', 'category'));
            return $redirect->run();
        }

        $redirect = new Base_Redirect($this->getDb());

        switch ($this->getRequest()->getRequest('tomotion'))
        {
            case 'up':
                $this->getMapper('Category/Category')->motionUp(
                    $this->category, array('pid', $this->getRequest()->getRequest()->pid)
                );
                $redirect->setMessage('element_motion_up');
                break;
            case 'down':
                $this->getMapper('Category/Category')->motionDown(
                    $this->category, array('pid',$this->getRequest()->getRequest()->pid)
                );
                $redirect->setMessage('element_motion_down');
                break;
            default:
                $redirect->setType('alert');
                $redirect->setMessage('unknown_tomotion');
                break;
        }

        $redirect->setRedirectUrl('/admin/category/');
        return $redirect->run();
    }
}