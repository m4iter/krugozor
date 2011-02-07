<?php
class Module_Index_Controller_Index extends Module_Common_Controller_Common
{
    public function run()
    {
        parent::common();

        $this->getView()->loadI18n('Common/FrontendGeneral', $this->getVirtualControllerPath());
        $this->getView()->getHelper('Html_Title')->add($this->getView()->lang['title']);

        $this->getView()->categories = $this->getMapper('Category/Category')->findCategoriesForIndex();
        $this->getView()->current_user = $this->getCurrentUser();
        $this->getView()->adverts = $this->getMapper('Advert/Advert')->findListForIndex(5);

        return $this->getView();
    }
}