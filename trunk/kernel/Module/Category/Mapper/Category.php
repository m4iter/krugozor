<?php
class Module_Category_Mapper_Category extends Module_Common_Mapper_Tree
{
    public function __construct()
    {
        parent::__construct();

        $this->db_table_name = 'category';

        $this->model_class_name = 'Module_Category_Model_Category';
    }

    public function loadLevel($pid, $params=array())
    {
        $params['where']['AND `category_active` = 1'] = array();

        return parent::loadLevel($pid, $params);
    }

    /**
     * Находит категорию по URL
     *
     * @param string $url
     */
    public function findByUrl($url)
    {
        $params = array
        (
            'where' => array(Module_Category_Model_Category::getMapItem('url')->getFieldName().' = "?s"' => array($url)),
        );

        $obj = parent::findModelByParams($params);

        return $obj;
    }

    /**
     * Сохраняет объект Категории и обновляет поле
     * сортировки order.
     *
     * @access public
     * @param Module_Category_Model_Category
     * @return void
     */
    public function save(Base_Model $object)
    {
        $id = $object->getId();

        $new_object = empty($id);

        if ($new_object)
        {
            parent::saveModel($object);
            parent::updateOrderField($object);

            $object->setUrlFromTreePath($this->loadPath($object->id) );
            parent::saveModel($object);
        }
        else
        {
            $object->setUrlFromTreePath($this->loadPath($object->id));
            parent::saveModel($object);

            // получаем подчинённые узлы
            $tree = $this->loadSubtree($object->id);
            // изменяем их URL-адреса
            $tree = $this->changeTreeUrls($tree, $object->url);
            // сохраняем подчинённые
            $this->saveTree($tree);
        }
    }

    /**
     * Сохраняет дерево категорий.
     *
     * @param Cover_Array дерево категорий
     */
    public function saveTree(Cover_Array $tree)
    {
        if (!$tree->count())
        {
            return false;
        }

        foreach ($tree as $category)
        {
            parent::saveModel($category);

	        if ($category->getTree() && $category->getTree()->count())
	        {
	            $this->saveTree($category->getTree());
	        }
        }

        return true;
    }

    /**
     * Возвращает дерево категорий по параметру $active
     *
     * @access public
     * @param int $active 1 или 0 - активность категории
     * @return Cover_Array
     */
    public function findCategoriesByActive($active=null)
    {
        $params = array
        (
            'order' => array('order' => 'DESC')
        );

        if (Base_Numeric::is_decimal($active))
        {
            $params['where'] = array('category_active = ?i' => array($active));
        }

        return $this->loadTree($params);
    }

    /**
     * Возвращает дерево категорий по параметру $active
     * и уровня 0.
     *
     * @access public
     * @param int $active 1 или 0 - активность категории
     * @return Cover_Array
     */
    public function findCategoriesForIndex()
    {
        $params['order'] = array('order' => 'DESC');
        $params['what'] = 'id, pid, category_name, category_url';
        $params['where'] = array('category_active = 1' => array());
        $params['where'] = array('pid = 0' => array());

        return $this->loadTree($params);
    }

    /**
     * Изменяет URL адреса дерева, добавляя поочередно
     * к каждому следующему узлу префикс, состоящий из предыдущего URL.
     * В качестве начального URL передается строка $url.
     *
     * @param Cover_Array $tree дерево категорий
     * @param $url префикс URL для всех URL адресов
     */
    private function changeTreeUrls(Cover_Array $tree, $url)
    {
        if (!$tree->count())
        {
            return new Cover_Array();
        }

        foreach ($tree as $key => $category)
        {
            $tree->item($key)->setUrl($url.$tree->item($key)->getAlias().'/');

            $tree->item($key)->setTree($this->changeTreeUrls($tree->item($key)->getTree(), $tree->item($key)->getUrl()));
        }

        return $tree;
    }
}
?>