<?php
class Module_Advert_Controller_View extends Module_Common_Controller_Common
{
    public function run()
    {
        parent::common();

        $advert_data = $this->getMapper('Advert/Advert')->findByIdUrl(
            $this->getRequest()->getRequest('id'), $this->getRequest()->getRequest('category_url')
        );

        if (!$advert_data['advert']->getId())
        {
            $redirect = new Base_Redirect($this->getDb());
            $redirect->setType('alert');
            $redirect->setMessage('advert_does_not_exist');
            $redirect->setRedirectUrl($this->getCurrentUser()->isGuest()
                                      ? array('categories')
                                      : array('my', 'adverts'));
            return $redirect->run();
        }

        foreach ($advert_data as $key => $object)
        {
            $this->getView()->$key = $object;
        }

        // ѕолучаем дерево объектов от корневой категории до категории $category->getId()
        $path_to_category = $this->getMapper('Category/Category')->loadPath($this->getView()->category->getId());

        $cat_names = Module_Category_Model_Category::getElementsInTree($path_to_category, 'getName');

        $this->getView()->loadI18n('Common/FrontendGeneral', $this->getVirtualControllerPath());

        $this->getView()->getHelper('Html_Title')->add($this->getView()->lang['title']);
        $this->getView()->getHelper('Html_Title')->add($cat_names);
        $this->getView()->getHelper('Html_Title')->add($this->getView()->advert->getHeader());

        $this->getView()->current_user = $this->getCurrentUser();
        $this->getView()->category_url = $this->getRequest()->getRequest('category_url');
        $this->getView()->path_to_category = $path_to_category;

        // похожие объ€влени€
        $this->getView()->similar_adverts = $this->getMapper('Advert/Advert')->finfSimilarAdverts($advert_data['advert'], $advert_data['user']);

        // ≈сли пользователь скрыл объ€вление, то уведомл€ем об этом
        if (!$this->getView()->advert->getActive())
        {
            $redirect = new Base_Redirect($this->getDb());
            $redirect->setType('warning');

            if ($this->getView()->advert->getIdUser() == $this->getCurrentUser()->getId())
            {
                $redirect->setMessage('advert_close_for_author');
                $redirect->addParam('advert_header', $this->getView()->advert->getHeader());
            }
            else
            {
                $redirect->setMessage('advert_close_for_user');
            }

            $this->getView()->setRedirect($redirect);
        }
        // иначе показ объ€влени€ увеличиваем на 1
        else
        {
            if (!$this->getCurrentUser()->isGuest() && $advert_data['advert']->getIdUser() != $this->getCurrentUser()->getId() OR
                 $this->getCurrentUser()->isGuest())
            {
                $this->getMapper('Advert/Advert')->incrementViewCount($advert_data['advert']);
            }
        }

        return $this->getView();
    }
}