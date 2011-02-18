<?php
class Module_Advert_Model_Advert extends Base_Model
{
    protected static $db_field_prefix = 'advert';

    protected static $model_attributes = array
    (
        'id' => array('db_element' => false,
                      'default_value' => 0,
                      'validators' => array(
                          'Common/Decimal' => array('unsigned' => false),
                      )
                     ),

        'id_user' => array('db_element' => true,
                           'db_field_name' => 'advert_id_user',
                           'default_value'=>-1,
                           'validators' => array(
                               'Common/EmptyNull' => array(),
                               'Common/Decimal' => array('unsigned' => false),
                           )
                          ),

        'active' => array('db_element' => true,
                          'db_field_name' => 'advert_active',
                          'default_value' => 1,
                          'validators' => array(
                              'Common/EmptyNull' => array(),
                              'Common/Decimal' => array('unsigned' => true),
                              'Common/IntRange' => array('min' => 0, 'max' => 1),
                          )
                         ),

        'type' => array('type' => 'Module_Advert_Type_AdvertType',
                        'db_element' => true,
                        'db_field_name' => 'advert_type',
                        'default_value' => 'sale',
                        'validators' => array(
                            'Common/EmptyNull' => array(),
                            'Common/VarEnum' => array('enum' => array('sale', 'buy')),
                        )
                       ),

        'category' => array('db_element' => true,
                            'db_field_name' => 'advert_category',
                            'default_value' => NULL,
                            'validators' => array(
                                'Common/Empty' => array(),
                                'Common/Decimal' => array('unsigned' => true),
                            )
                           ),

        'header' => array('db_element' => true,
                          'db_field_name' => 'advert_header',
                          'default_value' => NULL,
                          'validators' => array(
                              'Common/EmptyNull' => array(),
                              'Common/StringLength' => array('start'=> 0, 'stop' => Module_Common_Validator_StringLength::VARCHAR_MAX_LENGTH),
                          )
                         ),

        'text' => array('db_element' => true,
                        'db_field_name' => 'advert_text',
                        'default_value' => NULL,
                        'validators' => array(
                            'Common/EmptyNull' => array(),
                        )
                       ),

        'price' => array('db_element' => true,
                         'db_field_name' => 'advert_price',
                         'default_value'=> NULL,
                         'validators' => array(
                             'Common/Decimal' => array('unsigned' => true),
                         )
                        ),

        'price_type' => array('type' => 'Module_Advert_Type_PriceType',
                              'db_element' => true,
                              'db_field_name' => 'advert_price_type',
                              'default_value' => 'rur',
                              'validators' => array(
                                  'Common/EmptyNull' => array(),
                                  'Common/VarEnum' => array('enum' => array('rur', 'eur', 'usd')),
                              )
                             ),

        'email' => array('db_element' => true,
                         'db_field_name' => 'advert_email',
                         'default_value' => NULL,
                         'validators' => array(
                             'Common/StringLength' => array('start'=> 0, 'stop' => Module_Common_Validator_StringLength::VARCHAR_MAX_LENGTH),
                             'Common/Email' => array(),
                         )
                        ),

        'phone' => array('db_element' => true,
                         'db_field_name' => 'advert_phone',
                         'default_value' => NULL,
                         'validators' => array(
                             'Common/StringLength' => array('start'=> 0, 'stop' => Module_Common_Validator_StringLength::VARCHAR_MAX_LENGTH),
                         )
                        ),

        'icq' => array('db_element' => true,
                       'db_field_name' => 'advert_icq',
                       'default_value' => NULL,
                       'validators' => array(
                           'Common/Decimal' => array('unsigned' => true),
                           'Common/IntRange' => array('min' => 10000, 'max' => Module_Common_Validator_IntRange::PHP_MAX_INT_32)
                       )
                      ),

        'url' => array('db_element' => true,
                       'db_field_name' => 'advert_url',
                       'default_value' => NULL,
                       'validators' => array(
                           'Common/StringLength' => array('start'=> 0, 'stop' => Module_Common_Validator_StringLength::VARCHAR_MAX_LENGTH),
                           'Common/Url' => array(),
                       )
                      ),

        'user_name' => array('db_element' => true,
                             'db_field_name' => 'advert_user_name',
                             'default_value' => NULL,
	                         'validators' => array(
	                             'Common/StringLength' => array('start'=> 0, 'stop' => Module_Common_Validator_StringLength::VARCHAR_MAX_LENGTH),
	                         )
	                        ),

        'main_email' => array('db_element' => true,
                              'db_field_name' => 'advert_main_email',
                              'default_value' => 1,
	                          'validators' => array(
	                              'Common/EmptyNull' => array(),
	                              'Common/Decimal' => array('unsigned' => true),
	                              'Common/IntRange' => array('min' => 0, 'max' => 1),
	                          )
	                         ),

        'main_phone' => array('db_element' => true,
                              'db_field_name' => 'advert_main_phone',
                              'default_value' => 1,
                              'validators' => array(
                                  'Common/EmptyNull' => array(),
                                  'Common/Decimal' => array('unsigned' => true),
                                  'Common/IntRange' => array('min' => 0, 'max' => 1),
                              )
                             ),

        'main_icq' => array('db_element' => true,
                            'db_field_name' => 'advert_main_icq',
                            'default_value' => 1,
                            'validators' => array(
                                  'Common/EmptyNull' => array(),
                                  'Common/Decimal' => array('unsigned' => true),
                                  'Common/IntRange' => array('min' => 0, 'max' => 1),
                            )
                           ),

        'main_url' => array('db_element' => true,
                            'db_field_name' => 'advert_main_url',
                            'default_value' => 1,
                            'validators' => array(
                                'Common/EmptyNull' => array(),
                                'Common/Decimal' => array('unsigned' => true),
                                'Common/IntRange' => array('min' => 0, 'max' => 1),
                            )
                           ),

       'main_user_name' => array('db_element' => true,
                              'db_field_name' => 'advert_main_user_name',
                              'default_value' => 1,
                              'validators' => array(
                                  'Common/EmptyNull' => array(),
                                  'Common/Decimal' => array('unsigned' => true),
                                  'Common/IntRange' => array('min' => 0, 'max' => 1),
                              )
                             ),

       'place_country' => array('db_element' => true,
                                'db_field_name' => 'advert_place_country',
                                'default_value' => 0,
	                            'validators' => array(
	                                'Common/Decimal' => array('unsigned' => true),
	                            )
	                           ),

       'place_region' => array('db_element' => true,
                               'db_field_name' => 'advert_place_region',
                               'default_value' => 0,
	                           'validators' => array(
	                               'Common/Decimal' => array('unsigned' => true),
	                           )
	                          ),

       'place_city' => array('db_element' => true,
                             'db_field_name' => 'advert_place_city',
                             'default_value' => 0,
	                         'validators' => array(
	                             'Common/Decimal' => array('unsigned' => true),
	                         )
	                        ),

       'create_date' => array('type' => 'Module_Common_Type_Datetime',
                                'db_element' => true,
                                'db_field_name' => 'advert_create_date',
                                'default_value' => 'now'),

       'edit_date' => array('type' => 'Module_Common_Type_Datetime',
                             'db_element' => true,
                             'db_field_name' => 'advert_edit_date',
                             'default_value' => null),

       'view_count' => array('db_element' => true,
	                         'db_field_name' => 'advert_view_count',
	                         'default_value' => 0,
	                         'validators' => array(
                                 'Common/Decimal' => array('unsigned' => true),
                             )
	                        )
    );

    public function _setUrl($url)
    {
        return $url === 'http://' ? NULL : $url;
    }

    /**
     * Инвертирует активность объявления.
     *
     * @param void
     * @return Module_Advert_Model_Advert
     */
    public function invertActive()
    {
    	$this->setActive($this->getActive() ? 0 : 1);

    	return $this;
    }

    /**
     * Возвращает объект DateInterval, указывающий сколько осталось до-
     * или уже прошло времени после- времени create_date + $hour часов.
     *
     * @access public
     * @param int $hour колчество часов
     * @return DateInterval
     */
    public function getExpireRestrictionUpdateCreateDate($hour=1)
    {
        $interval = new DateInterval('P0Y0DT'.$hour.'H0M');
        $t_date = clone $this->getCreateDate();
        $t_date->add($interval);

        $now = new Module_Common_Type_Datetime();
        return $now->diff($t_date);
    }

    /**
     * Устанавливает свойство create_date в значение
     * текущего времени - 1 секунда.
     *
     * @access public
     * @param void
     * @return void
     */
    public function setCurrentCreateDateDiffSecond()
    {
        $now = new Module_Common_Type_Datetime();
        $now->setTimestamp(time()-1);
        $this->setCreateDate($now);
    }
}
?>