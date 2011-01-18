<?php
class Module_Ajax_Controller_Region extends Module_Ajax_Controller_Common
{
    public function run()
    {
        parent::common();

        $this->getResponse()->setHeader('Content-type', 'text/plain; charset=windows-1251');

        $this->getView('JsonList');

        $this->getView()->jdata = self::getMapper('User/Region')->getArrayListForHtmlSelect(
            $this->getRequest()->getRequest('id')
        );

        return $this->getView();
    }
}
?>