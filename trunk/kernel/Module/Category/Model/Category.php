<?php
class Module_Category_Model_Category extends Base_Model
{
    protected static $db_field_prefix = 'category';

    // Дерево подкатегорий данного узла.
    protected $tree;

    // Вложенность (отступ) от нулевого узла
    protected $indent;

    protected static $model_attributes = array
    (
        'id' => array('db_element'=>FALSE,
                      'default_value'=>0,
                      'validators' => array(
                          'Common/Decimal' => array('unsigned' => false),
                      )
                     ),

        'pid' => array('db_element'=>TRUE,
                       'db_field_name'=>'pid',
                       'default_value'=>0,
                       'validators' => array(
                           'Common/EmptyNull' => array(),
                           'Common/Decimal' => array('unsigned' => false),
                       )
                      ),

        'active' => array('db_element'=>TRUE,
                          'db_field_name'=>'category_active',
                          'default_value'=>1,
                          'validators' => array(
                              'Common/EmptyNull' => array(),
                              'Common/Decimal' => array('unsigned' => true),
                              'Common/IntRange' => array('min' => 0, 'max' => 1),
                          )
                         ),

        'name' => array('db_element'=>TRUE,
                        'db_field_name'=>'category_name',
                        'default_value'=>NULL,
                        'validators' => array(
                            'Common/EmptyNull' => array(),
                            'Common/StringLength' => array('start'=>0, 'stop' => Module_Common_Validator_StringLength::VARCHAR_MAX_LENGTH),
                        )
                       ),

        'alias' => array('db_element'=>TRUE,
                         'db_field_name'=>'category_alias',
                         'default_value'=>NULL,
                         'validators' => array(
                             'Common/StringLength' => array('start'=>0, 'stop' => Module_Common_Validator_StringLength::VARCHAR_MAX_LENGTH),
                         )
                        ),

        'url' => array('db_element'=>TRUE,
                       'db_field_name'=>'category_url',
                       'default_value'=>NULL,
                       'validators' => array(

                       )
                      ),

        'description' => array('db_element'=>TRUE,
                               'db_field_name'=>'category_description',
                               'default_value'=>NULL,
                               'validators' => array(
                                   'Common/StringLength' => array('start'=>0, 'stop' => 3000),
                               )
                              ),

        'keywords' => array('db_element'=>TRUE,
                            'db_field_name'=>'category_keywords',
                            'default_value'=>NULL,
                            'validators' => array(
                                'Common/StringLength' => array('start'=>0, 'stop' => 3000),
                            )
                           )
    );

    public function __construct()
    {
        $this->tree = new Cover_Array();
    }

    public function getTree()
    {
        return $this->tree;
    }

    public function setTree(Cover_Array $tree)
    {
        $this->tree = $tree;
    }

    public function setIndent($indent)
    {
        $this->indent = $indent;
    }

    public function getIndent()
    {
        return $this->indent;
    }

    /**
     * Создает URL-адрес на основании массива $tree
     * который является деревом-путём.
     *
     * @param array
     */
    public function setUrlFromTreePath(Cover_Array $tree)
    {
        $aliases = self::getAliasesFromTree($tree);

        // Поскольку levels берутся из базы, то последний элемент -
        // alias данной категории может отличаться от текущего $this->alias.
        // Перезаписываем последний элемент думуды на актуальный $this->alias
        if ($this->alias)
        {
            $aliases[count($aliases)-1] = $this->alias;
        }

        $this->setUrl('/'.implode('/', $aliases).'/');
    }

    protected function _setAlias($alias)
    {
        return Base_Translit::UrlTranslit($alias);
    }

    /**
     * Возвращает массив, состоящий из алиасов узлов дерева-пути.
     *
     * @param Cover_Array
     * @return array
     */
    private static function getAliasesFromTree(Cover_Array $tree)
    {
        if (!$tree instanceof Cover_Array || !$tree->count())
        {
            return false;
        }

        $aliases = array();

        foreach ($tree as $category)
        {
            $aliases[] = $category->getAlias();

            if ($category->getTree() && $category->getTree()->count())
            {
                $aliases = array_merge($aliases, self::getAliasesFromTree($category->getTree()));
            }
        }

        return $aliases;
    }

    /**
     * Извлекает из каждого элемента дерева значение с помощью метода
     * $method_name и помещает его в результирующий массив-список.
     * Подразумевается, что в каждом элементе дерева есть метод $method_name.
     *
     * @param Cover_Array $tree дерево объектов, из которых необходимо получать значение
     * @param string $method_name имя get-метода получения свойства объекта
     * @return array
     */
    public static function getElementsInTree($tree, $method_name)
    {
        $data = array();

        foreach ($tree as $element)
        {
            $data[] = $element->$method_name();

            if ($element->getTree() && $element->getTree()->count())
            {
                $data = array_merge($data, self::getElementsInTree($element->getTree(), $method_name));
            }
        }

        return $data;
    }
}
?>