<?php
class Module_Module_Model_Module extends Base_Model
{
    protected static $db_field_prefix = 'module';

    protected static $model_attributes = array
    (
        'id' => array('db_element'=>FALSE,
                      'default_value'=>0,
                      'validators' => array(
                          'Common/Decimal' => array('unsigned' => true),
                      )
                     ),

        'name' => array('db_element'=>TRUE,
                        'db_field_name'=>'module_name',
		                'validators' => array(
		                    'Common/EmptyNull' => array(),
                            'Common/StringLength' => array('start'=>0, 'stop' => 50),
		                )
		               ),

        'key' => array('db_element'=>TRUE,
                       'db_field_name'=>'module_key',
                       'validators' => array(
                           'Common/EmptyNull' => array(),
		                   'Common/CharPassword'=> array(),
		                   'Common/StringLength' => array('start'=>0, 'stop' => 30),
                       )
                      ),
    );

    /**
     * @var Cover_Array
     */
    protected $controllers;

    /**
     * Возвращает объектный массив Cover_Array, содержащий
     * все контроллеры, принадлежащие данному модулю.
     * Контроллеры запрашиваются из СУБД только в том случае, если
     * объектный массив $this->controllers ещё не был инициализирован.
     *
     * @param void
     * @return Cover_Array
     */
    public function getControllers()
    {
        if ($this->controllers === null)
        {
            foreach ($this->mapperManager
                          ->getMapper('Module/Controller')
                          ->findControllersListByModuleId($this->getId()) as $controller)
            {
                $this->setController($controller);
            }
        }

        return $this->controllers;
    }

    /**
     * Добавляет в коллекцию контроллеров новый контроллер.
     *
     * @param Module_Module_Model_Controller $controller
     * @return Module_Module_Model_Module
     */
    public function setController(Module_Module_Model_Controller $controller)
    {
        if ($this->controllers === null)
        {
            $this->controllers = new Cover_Array();
        }

        $this->controllers->append($controller);

        return $this;
    }
}