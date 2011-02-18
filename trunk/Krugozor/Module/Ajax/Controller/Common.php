<?php
abstract class Module_Ajax_Controller_Common extends Module_Common_Controller_Common
{
    public function common()
    {
        $this->default_view_class_name = 'Module_Ajax_View_Default';

        $this->view_bebug_info = FALSE;

        $this->getResponse()->clearHeaders();

        parent::common();
    }
}