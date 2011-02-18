<?php
class Module_Group_Model_Group extends Base_Model
{
    protected static $db_field_prefix = 'group';

    protected static $model_attributes = array
    (
        'id' => array('db_element' => FALSE,
                      'default_value' => 0,
                      'validators' => array(
                          'Common/Decimal' => array('unsigned' => true),
                      )
                     ),

        'name' => array('db_element' => TRUE,
                        'db_field_name' => 'group_name',
                        'validators' => array(
                            'Common/EmptyNull' => array(),
                        )
                       ),

        'active' => array('db_element' => TRUE,
                          'default_value' => 1,
                          'db_field_name' => 'group_active',
                          'validators' => array(
                               'Common/EmptyNull' => array(),
                               'Common/Decimal' => array('unsigned' => true),
                               'Common/IntRange' => array('min' => 0, 'max' => 1),
                          )
		                 ),

        'alias' => array('db_element' => TRUE,
                         'db_field_name' => 'group_alias',
		                 'validators' => array(
                             'Common/EmptyNull' => array(),
		                     'Common/CharPassword' => array(),
		                 )
		                ),
    );

    /**
     * Коллекция объектов доступа группы к контроллерам системы.
     *
     * @var Cover_Array
     */
    protected $accesses;

    /**
     * Возвращает коллецию объектов доступа.
     *
     * @param void
     * @return Cover_Array
     */
    public function getAccesses()
    {
        if ($this->accesses === null)
        {
            $this->accesses = new Cover_Array();

            foreach ($this->mapperManager
                          ->getMapper('Group/Access')
                          ->findListByGroupId($this->getId()) as $access)
            {
                $this->setAccess($access);
            }
        }

        return $this->accesses;
    }

    /**
     * Добавляет в коллекцию новый объект доступа.
     *
     * @param Module_Group_Model_Access
     * @return Module_Group_Model_Group
     */
    public function setAccess(Module_Group_Model_Access $access)
    {
        if ($this->accesses === null)
        {
            $this->accesses = new Cover_Array();
        }

        $this->accesses->append($access);

        return $this;
    }

    /**
     * Ищет в коллекции $this->accesses объект доступа, относящийся к объекту
     * контроллера $controller и в случае нахождения его, возвращает.
     * Если объект доступа не найден, возвращается null.
     *
     * @param Module_Module_Model_Controller
     * @return Module_Group_Model_Access|null
     */
    public function getAcccessForController(Module_Module_Model_Controller $controller)
    {
        foreach ($this->getAccesses() as $access)
        {
            if ($access->getIdController() == $controller->getId())
            {
                return $access->getAccess();
            }
        }

        return null;
    }
}