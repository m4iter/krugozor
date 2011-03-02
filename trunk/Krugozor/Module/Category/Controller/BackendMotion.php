<?php
class Module_Category_Controller_BackendMotion extends Module_Category_Controller_BackendCommon
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            return $this->createNotification()
                        ->setMessage('forbidden_access')
                        ->setType('alert')
                        ->setRedirectUrl(array('admin', 'category'))
                        ->run();
        }

        if ($result = $this->checkIdOnValid())
        {
            return $result;
        }

        if (!isset($this->getRequest()->getRequest()->id))
        {
            return $this->createNotification()
                        ->setType('alert')
                        ->setMessage('element_not_exists')
                        ->setRedirectUrl(array('admin', 'category'))
                        ->run();
        }

        $redirect = $this->createNotification();

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

        return $redirect->setRedirectUrl('/admin/category/')->run();
    }
}