<?php
class Module_Module_Model_Controller extends Base_Model
{
    protected $module;

    protected static $db_field_prefix = 'controller';

    protected static $model_attributes = array
    (
        'id' => array('db_element'=>FALSE,
                      'default_value'=>0,
                      'validators' => array(
                          'Common/Decimal' => array('unsigned' => true),
                      )
                     ),

        'id_module' => array('db_element'=>TRUE,
                             'db_field_name' => 'controller_id_module',
		                     'validators' => array(
                                 'Common/Empty' => array(),
		                         'Common/Decimal' => array('unsigned' => true),
		                     )
		                    ),

        'name' => array('db_element'=>TRUE,
                        'db_field_name'=>'controller_name',
                        'validators' => array(
                            'Common/EmptyNull' => array(),
                            'Common/StringLength' => array('start'=>0, 'stop' => Module_Common_Validator_StringLength::VARCHAR_MAX_LENGTH),
                        )
                       ),

        'key' => array('db_element'=>TRUE,
                       'db_field_name'=>'controller_key',
                       'validators' => array(
                           'Common/EmptyNull' => array(),
                           'Common/CharPassword' => array(),
                           'Common/StringLength' => array('start'=>0, 'stop' => 150),
                       )
                      ),
    );

    public function getModule()
    {
        if ($this->module === null)
        {
            $mapper = new Module_Module_Mapper_Module();
            $this->module = $mapper->findById($this->getIdModule());
        }

        return $this->module;
    }
}
?>
