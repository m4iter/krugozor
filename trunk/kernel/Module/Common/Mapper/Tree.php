<?php
class Module_Common_Mapper_Tree extends Module_Common_Mapper_Common
{
    /**
     * Загружает все дерево в соответствии с массивом $params.
     *
     * @param array массив параметро выборки
     * @return Cover_Array
     */
    public function loadTree($params=array())
    {
        return $this->medium2objectTree( $this->findMediumTypeArray($params) );
    }

    /**
     * Загружает всех потомков уровня с id = $pid.
     * Данный метод НЕ загружает все дерево целиком, а только потомков с указаным ID.
     *
     * @param int $pid
     */
    public function loadLevel($pid, $params=array())
    {
        if (empty($pid) || !Base_Numeric::is_decimal($pid))
        {
            return false;
        }

        if (!isset($params['where']))
        {
            $params['where'] = array();
        }

        Base_Array::array_unshift_assoc($params['where'], '`pid` = ?i', array($pid));

        $params = self::makeSqlFromParams($params);

        $sql = 'SELECT '.$params['what'].
               ' FROM `'.$this->db_table_name.'` '.
               $params['where'].
               ' ORDER BY `order` DESC';

        array_unshift($params['args'], $sql);

        $res = call_user_func_array(array($this->db, 'query'), $params['args']);

        if (!$res)
        {
            return false;
        }

        $tree = new Cover_Array();

        while ($row = $res->fetch_assoc())
        {
            $object = parent::createModelFromArray($row);

            if ($object->id)
            {
                self::$collection[$this->getModuleName()][$this->getModelName()][$object->id] = $object;
            }

            $tree->append($object);
        }

        return $tree;
    }

    /**
     * Загружает поддерево полностью, до последнего элемента.
     *
     * @param int $id
     * @return Cover_Array
     */
    public function loadSubtree($id)
    {
        if (!Base_Numeric::is_decimal($id) || !$id)
        {
            return false;
        }

        $res = $this->db->query('SELECT * FROM `'.$this->db_table_name.'` WHERE `pid` = ?i ORDER BY `order` DESC', $id);

        if (!$res)
        {
            return new Cover_Array();
        }

        $subtree = new Cover_Array();

        while ($row = $res->fetch_assoc())
        {
            $object = parent::createModelFromArray($row);

            $object->setTree($this->loadSubtree($object->getId()));

            $subtree->append($object);

            if ($object->id)
            {
                self::$collection[$this->getModuleName()][$this->getModelName()][$object->id] = $object;
            }
        }

        return $subtree;
    }

    /**
     * Загружает путь (поддерево от начала дерева)
     * к указанной вершине (включая вершину).
     *
     * @param $id
     */
    public function loadPath($id)
    {
        if (empty($id) || !Base_Numeric::is_decimal($id, true))
        {
            return false;
        }

        $tree = new Cover_Array();

        while ($id)
        {
            $object = parent::findById($id);

            if (!$object->getId())
            {
                return false;
            }

            $object->setTree($tree);

            $tree = new Cover_Array();
            $tree->append($object);

            $id = $object->getPid();
        }

        return $tree;
    }

    /**
     * На основании таблицы деревьев возвращает многомерный массив вида:
     *
     * [0] => Array
     *  (
     *      [0] => Array
     *          (
     *              [id] => 121
     *              [pid] => 0
     *              [category_name] => Недвижимость
     *              [...] => ...
     *          )
     * [50] => Array
     *  (
     *      [0] => Array
     *          (
     *              [id] => 82
     *              [pid] => 50
     *              [category_name] => Женская одежда
     *              [...] => ...
     *
     * где каждый элемент массива является элементом массива
     * с ключом равным его parent id.
     *
     * @param array $params
     * @return array
     */
    protected function findMediumTypeArray(array $params=array())
    {
        $params = self::makeSqlFromParams($params);

        $sql = 'SELECT '.$params['what'].' FROM `'.$this->db_table_name.'`'.
               $params['where'].
               $params['order'];

        array_unshift($params['args'], $sql);

        $res = call_user_func_array(array($this->db, 'query'), $params['args']);

        $data = array();

        while ($temp = $res->fetch_assoc())
        {
            if (!isset($data[$temp['pid']]))
            {
                $data[$temp['pid']] = array();
            }

            $data[$temp['pid']][] = $temp;
        }

        return $data;
    }

    /**
     * Создает дерево объектов из многомерного массива,
     * возвращённого методом $this->findMediumTypeArray()
     *
     * @param array $data массив
     * @param int $k идентификатор элемента
     * @param int $indent отступ для форматирования
     * @return Cover_Array
     */
    protected function medium2objectTree($data, $k=0, $indent=0)
    {
        if (empty($data[$k]))
        {
            return new Cover_Array();
        }

        $indent++;

        $tree = new Cover_Array();

        foreach ($data[$k] as $category_data)
        {
            $object = parent::createModelFromArray($category_data);

            $object->setTree($this->medium2objectTree($data, $category_data['id'], $indent));

            $object->setIndent($indent);

            $tree->append($object);
        }

        return $tree;
    }
}
?>