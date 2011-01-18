<?php
class Module_Category_Controller_FrontendCategoriesList extends Module_Common_Controller_Common
{
    public function run()
    {
        parent::common();

        $this->getView()->loadI18n('Common/FrontendGeneral', $this->getVirtualControllerPath());

        $this->getView()->getHelper('Html_Title')->add($this->getView()->lang['title']);

        $this->getView()->current_user = $this->getCurrentUser();

        $this->getView()->categories = self::getMapper('Category/Category')->findCategoriesForIndex();

        return $this->getView();
    }
}
?>