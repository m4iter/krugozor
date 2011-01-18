<?php
class Module_User_Model_Country extends Base_Model
{
    protected static $db_field_prefix = 'country';

    protected static $model_attributes = array
    (
        'id' => array('db_element'=>FALSE,
                      'default_value'=>0),

        'name_ru' => array('db_element'=>TRUE,
                           'db_field_name'=>'country_name_ru'),

        'name_en' => array('db_element'=>TRUE,
                           'db_field_name'=>'country_name_en'),
    );
}
?>