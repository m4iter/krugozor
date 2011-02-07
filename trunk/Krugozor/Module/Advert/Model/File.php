<?php
class Module_Advert_Model_File extends Base_Model
{
    protected static $db_field_prefix = 'file';

    protected static $model_attributes = array
    (
        'id' => array('db_element'=>FALSE,
                      'default_value' => 0,
                      'validators' => array(
                          'Common/Decimal' => array('unsigned' => true),
                      )
                     ),

        'id_element' => array('db_element'=>TRUE,
                              'db_field_name'=>'file_id_element',
                              'default_value' => null,
                              'validators' => array(
		                          'Common/Decimal' => array('unsigned' => true),
		                      )
                        ),

        'active' => array('db_element'=>TRUE,
                          'db_field_name'=>'file_active',
                          'default_value'=>1,
                          'validators' => array(
                              'Common/EmptyNull' => array(),
                              'Common/Decimal' => array('unsigned' => true),
                              'Common/IntRange' => array('min' => 0, 'max' => 1),
                          )
                         ),

        'size' => array('db_element'=>TRUE,
                        'db_field_name'=>'file_size',
                        'default_value' => 0,
                        'validators' => array(
                            'Common/Decimal' => array('unsigned' => true),
                        )
                       ),

        'ext'  => array('db_element'=>TRUE,
                        'db_field_name'=>'file_ext',
                        'default_value'=>null,
                        'validators' => array(
                            'Common/StringLength' => array('start'=> 0, 'stop' => 10),
                        )
                       ),

        'name' => array('db_element'=>TRUE,
                        'db_field_name'=>'file_name',
                        'default_value'=>NULL,
                        'validators' => array(
                            'Common/Empty' => array(),
                            'Common/StringLength' => array('start'=> 0, 'stop' => Module_Common_Validator_StringLength::VARCHAR_MAX_LENGTH),
                        )
                       ),

        'dir'  => array('db_element'=>TRUE,
                        'db_field_name'=>'file_dir',
                        'default_value'=>null,
                        'validators' => array(
                            'Common/StringLength' => array('start'=> 0, 'stop' => Module_Common_Validator_StringLength::VARCHAR_MAX_LENGTH),
                        )
                       ),

        'mime_type' => array('db_element'=>TRUE,
                        'db_field_name'=>'file_mime_type',
                        'default_value'=>NULL,
                        'validators' => array(
                            'Common/Empty' => array(),
                            'Common/StringLength' => array('start'=> 0, 'stop' => Module_Common_Validator_StringLength::VARCHAR_MAX_LENGTH),
                        )
                       ),

        'file_date' => array('type'=>'Module_Common_Type_Datetime',
                             'db_element'=>TRUE,
                             'db_field_name'=>'file_create_date',
                             'default_value'=>'now'),
    );
}
?>