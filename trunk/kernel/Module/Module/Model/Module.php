<?php
class Module_Module_Model_Module extends Base_Model
{
    protected static $db_field_prefix = 'module';

    protected $controllers;

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

    public function __construct()
    {
        $this->controllers = new Cover_Array();
    }

    public function getControllers()
    {
        return $this->controllers;
    }

    public function setControllers(Cover_Array $controllers)
    {
        $this->controllers = $controllers;
    }

    public function loadControllers()
    {
        if (!$this->controllers->count() && $this->id)
        {
            $controller_mapper = new Module_Module_Mapper_Controller();

            $params = array
            (
                'where' => array('controller_id_module = ?i' => array($this->id)),
                'order' => array('controller_name' => 'ASC')
            );

            foreach ($controller_mapper->findList($params) as $controller)
            {
                $this->controllers->append($controller);
            }
        }

        return $this;
    }
}
?>