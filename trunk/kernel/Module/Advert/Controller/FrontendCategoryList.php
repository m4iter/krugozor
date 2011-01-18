<?php
class Module_Advert_Controller_FrontendCategoryList extends Module_Advert_Controller_FrontendCommon
{
    public function run()
    {
        parent::common();

        $category = self::getMapper('Category/Category')->findByUrl($this->getRequest()->getRequest('category_url'));

        if (!$category->getId())
        {
            $redirect = new Base_Redirect();
            $redirect->setHeader('action_failed');
            $redirect->setType('alert');
            $redirect->setMessage('element_does_not_exist');
            $redirect->setRedirectUrl('/categories/');
            return $redirect->run();
        }

        $this->init();

        $navigation = new Base_Navigation(10, 100);

        // Получаем дерево объектов от корневой категории до категории $category->getId()
        $path_to_category = self::getMapper('Category/Category')->loadPath($category->getId());

        // получаем имена всех категорий для html-тега title
        $cat_names = Module_Category_Model_Category::getElementsInTree($path_to_category, 'getName');

        $this->getView()->getHelper('Html_Title')->add($cat_names);

        // получаем всех потомков уровня $category->getId()
        $params['what'] = 'id, pid, category_name, category_url';
        $subcategories = self::getMapper('Category/Category')->loadLevel($category->getId(), $params);

        $pids = Module_Category_Model_Category::getElementsInTree($subcategories, 'getId');

        $pids[] = $category->getId();

        $params = array(
            // Дабы не использовать для выборки исключительно по одному идентификатору конструкцию IN()...
            'where' => count($pids) > 1
                ? array('`'.Module_Advert_Model_Advert::getMapItem('category')->getFieldName().'` IN ('.implode(',', $pids).')' => array())
                : array('`'.Module_Advert_Model_Advert::getMapItem('category')->getFieldName().'` = ?i' => array($pids[0])),
            'limit' => array('start' => $navigation->getStartLimit(), 'stop' => $navigation->getStopLimit())
        );

        if ($this->getRequest()->getGet('submit'))
        {
            $id_city = $this->getRequest()->getGet('id_city', 'decimal');

            if ($id_city) {
                $this->getResponse()->setcookie('id_city', $id_city, time()+60*60*24*365, '/');
            } else {
                $this->getResponse()->setcookie('id_city', '', time()-60*60*24*14, '/');
            }

            $id_region = $this->getRequest()->getGet('id_region', 'decimal');

            if ($id_region) {
                $this->getResponse()->setcookie('id_region', $id_region, time()+60*60*24*365, '/');
            } else {
                $this->getResponse()->setcookie('id_region', '', time()-60*60*24*14, '/');
            }

            $id_country = $this->getRequest()->getGet('id_country', 'decimal');

            if ($id_country) {
                $this->getResponse()->setcookie('id_country', $id_country, time()+60*60*24*365, '/');
            } else {
                $this->getResponse()->setcookie('id_country', '', time()-60*60*24*14, '/');
            }

            $type = $this->getRequest()->getGet('type', 'string');

            if ($type) {
                $this->getResponse()->setcookie('type', $type, time()+60*60*24*365, '/');
            } else {
                $this->getResponse()->setcookie('type', '', time()-60*60*24*14, '/');
            }

            $this->getResponse()->sendCookie();
            $redirect = new Base_Redirect();
            $redirect->setMessage('filter_action');
            $redirect->setRedirectUrl($this->getRequest()->getRequest()->getUri());
            return $redirect->run();
        }

        $get_search = ($this->getRequest()->getGet('id_city', 'decimal')
                      || $this->getRequest()->getGet('id_region', 'decimal')
                      || $this->getRequest()->getGet('id_country', 'decimal')
                      || $this->getRequest()->getGet('type', 'string'));

        $this->getView()->filter_search = (
            $this->getRequest()->getCookie('id_city', 'decimal')
            || $this->getRequest()->getCookie('id_region', 'decimal')
            || $this->getRequest()->getCookie('id_country', 'decimal')
            || $this->getRequest()->getCookie('type', 'string')
        );

        $id_city = $get_search
                   ? $this->getRequest()->getGet('id_city', 'decimal')
                   : $this->getRequest()->getCookie('id_city', 'decimal');

        if ($id_city)
        {
            $params['where']['AND `'.Module_Advert_Model_Advert::getMapItem('place_city')->getFieldName().'` = ?i'] = array($id_city);
        }

        $id_region = $get_search
                     ? $this->getRequest()->getGet('id_region', 'decimal')
                     : $this->getRequest()->getCookie('id_region', 'decimal');

        if ($id_region)
        {
            $params['where']['AND `'.Module_Advert_Model_Advert::getMapItem('place_region')->getFieldName().'` = ?i'] = array($id_region);
        }

        $id_country = $get_search
                      ? $this->getRequest()->getGet('id_country', 'decimal')
                      : $this->getRequest()->getCookie('id_country', 'decimal');

        if ($id_country)
        {
            $params['where']['AND `'.Module_Advert_Model_Advert::getMapItem('place_country')->getFieldName().'` = ?i'] = array($id_country);
        }

        $type = $get_search
                ? $this->getRequest()->getGet('type', 'string')
                : $this->getRequest()->getCookie('type', 'string');

        if ($type)
        {
            $params['where']['AND `'.Module_Advert_Model_Advert::getMapItem('type')->getFieldName().'` = "?s"'] = array($type);
        }

        if ($this->getResponse()->getCookie())
        {
            $this->getResponse()->sendCookie();
        }

        $this->getView()->adverts = self::getMapper('Advert/Advert')->findListForCatalog($params);

        // Подставляем название выбранного города в title
        if ($id_city && $this->getView()->adverts->count())
        {
            $index = $this->getView()->getHelper('Html_Title')->getCountElements()-1;

            $new_title = $this->getView()->getHelper('Html_Title')->getByIndex($index).
                         $this->getView()->lang['in_city'].
                         $this->getView()->adverts->item(0)->user_city->getNameRu();
            $this->getView()->getHelper('Html_Title')->deleteByIndex($index);
            $this->getView()->getHelper('Html_Title')->add($new_title);
        }
        // Подставляем название выбранного региона в title
        else if ($id_region && $this->getView()->adverts->count())
        {
            $index = $this->getView()->getHelper('Html_Title')->getCountElements()-1;

            $new_title = $this->getView()->getHelper('Html_Title')->getByIndex($index).
                         $this->getView()->lang['in_region'].
                         $this->getView()->adverts->item(0)->user_region->getNameRu();
            $this->getView()->getHelper('Html_Title')->deleteByIndex($index);
            $this->getView()->getHelper('Html_Title')->add($new_title);
        }

        $navigation->setCount(self::getMapper('Advert/Advert')->getFoundRows());

        $this->getView()->navigation = $navigation;

        $this->getView()->current_user = $this->getCurrentUser();
        $this->getView()->category = $category;
        $this->getView()->category_url = $this->getRequest()->getRequest('category_url');
        $this->getView()->subcategories = $subcategories;
        $this->getView()->path_to_category = $path_to_category;

        $this->getView()->type = $type;
        $this->getView()->id_city = $id_city;
        $this->getView()->id_region = $id_region;
        $this->getView()->id_country = $id_country;

        return $this->getView();
    }
}
?>