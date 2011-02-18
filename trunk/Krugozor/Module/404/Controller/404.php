<?php
class Module_404_Controller_404 extends Module_Common_Controller_Common
{
    public function run()
    {
        $this->getResponse()->setHeader404();

        $this->view_bebug_info = FALSE;

        $this->getView()->loadI18n('404/404');

        $this->getView()->getHelper('Html_Title')->add(
            $this->getView()->lang['document_not_found_header']
        );

        return $this->getView();
    }
}