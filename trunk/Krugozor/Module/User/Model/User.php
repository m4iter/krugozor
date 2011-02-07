<?php
class Module_User_Model_User extends Base_Model
{
    protected static $db_field_prefix = 'user';

    protected static $model_attributes = array
    (
        'id' => array('db_element'=>false,
                      'default_value' => 0,
                      'validators' => array(
                          'Common/Decimal' => array('unsigned' => false),
                      )
                     ),

        'active' => array('db_element'=>true,
                          'db_field_name' => 'user_active',
                          'default_value' => 1,
	                      'validators' => array(
                              'Common/EmptyNull' => array(),
	                          'Common/Decimal' => array('unsigned' => true),
                              'Common/IntRange' => array('min' => 0, 'max' => 1),
	                      )
	                     ),

        'group'  => array('db_element'=>true,
                          'db_field_name' => 'user_group',
                          'default_value'=> 2, // 2 - ID группы Пользователи
	                      'validators' => array(
	                          'Common/Empty' => array(),
                              'Common/Decimal' => array('unsigned' => true),
                          )
                         ),

        'login'  => array('db_element'=>true,
                          'db_field_name' => 'user_login',
                          'validators' => array(
                              'Common/EmptyNull' => array(),
                              'Common/StringLength' => array(
                                  'start' => 0,
                                  'stop' => Module_Common_Validator_StringLength::VARCHAR_MAX_LENGTH),
                              'Common/CharPassword' => array(),
                          )
                         ),

        'mail'   => array('type' => 'Module_Common_Type_Email',
                          'db_element' => true,
                          'default_value' => null,
                          'db_field_name' => 'user_mail',
                          'validators' => array(
                              'Common/StringLength' => array(
                                  'start' => 0,
                                  'stop' => Module_Common_Validator_StringLength::VARCHAR_MAX_LENGTH),
                              'Common/Email' => array(),
                          )
                         ),

        'password' => array('db_element'=>true,
                            'db_field_name' => 'user_password',
	                        'validators' => array(
	                            'Common/CharPassword' => array(),
	                        )
	                       ),

        'regdate'  => array('type' => 'Module_Common_Type_Datetime',
                            'db_element'=>true,
                            'db_field_name' => 'user_regdate',
                            'default_value' => 'now',
	                        'validators' => array(

                            )
                           ),

        'visitdate' => array('type' => 'Module_Common_Type_Datetime',
                             'db_element'=>true,
                             'db_field_name' => 'user_visitdate',
                             'default_value'=>null,
                             'validators' => array(

                             )
                            ),

        'ip'     => array('db_element'=>true,
                          'db_field_name' => 'user_ip',
                          'default_value'=>null,
                          'validators' => array(
                              //'Common/FixedStringLength' => array(15),
                          )
                         ),

        'first_name' => array('db_element'=>true,
                              'db_field_name' => 'user_first_name',
                              'default_value'=>null,
	                          'validators' => array(
	                              'Common/StringLength' => array('start' => 0, 'stop' => 30),
	                          )
	                         ),

        'last_name' => array('db_element'=>true,
                             'db_field_name' => 'user_last_name',
                             'default_value'=>null,
                             'validators' => array(
                                 'Common/StringLength' => array('start' => 0, 'stop' => 30),
                             )
                            ),

        'age' => array('type' => 'Module_Common_Type_Datetime',
                       'db_element'=>true,
                       'db_field_name' => 'user_age',
                       'default_value'=>null,
                       'validators' => array(

                       )
                      ),

        'sex' => array('type' => 'Module_User_Type_Sex',
                       'db_element' => true,
                       'db_field_name' => 'user_sex',
                       'default_value' => null,
                       'validators' => array(
                           'Common/StringLength' => array('start' => 1, 'stop' => 1),
                           'Common/VarEnum' => array('enum' => array('M', 'F')),
                       )
                      ),

        'city' => array('db_element'=>true,
                        'db_field_name' => 'user_city',
                        'default_value' => 0,
                        'validators' => array(
                            'Common/Decimal' => array('unsigned' => true),
                        )
                       ),

        'region' => array('db_element'=>true,
                          'db_field_name' => 'user_region',
                          'default_value' => 0,
                          'validators' => array(
                              'Common/Decimal' => array('unsigned' => true),
                          )
                         ),

        'country' => array('db_element'=>true,
                           'db_field_name' => 'user_country',
                           'default_value' => 0,
                           'validators' => array(
                               'Common/Decimal' => array('unsigned' => true),
                           )
                          ),

        'phone' => array('db_element'=>true,
                         'db_field_name' => 'user_phone',
                         'default_value'=>null,
                         'validators' => array(
                             'Common/StringLength' => array(
                                 'start'=> 0,
                                 'stop' => Module_Common_Validator_StringLength::VARCHAR_MAX_LENGTH),
                         )
                        ),

        'icq' => array('db_element'=>true,
                       'db_field_name' => 'user_icq',
                       'default_value'=>null,
                       'validators' => array(
                           'Common/Decimal' => array('unsigned' => true),
                           'Common/IntRange' => array(
                               'min' => 10000,
                               'max' => Module_Common_Validator_IntRange::PHP_MAX_INT_32)
                       )
                      ),

        'url' => array('db_element'=>true,
                       'db_field_name' => 'user_url',
                       'default_value' => null,
                       'validators' => array(
                           'Common/StringLength' => array(
                               'start' => 0,
                               'stop' => Module_Common_Validator_StringLength::VARCHAR_MAX_LENGTH),
                           'Common/Url' => array()
                       )
                      ),
    );

    /**
     * true, если пользователь принадлежит к группе "пользователи"
     *
     * @var boolean
     */
    protected $is_user;

    /**
     * true, если пользователь принадлежит к группе "администраторы"
     *
     * @var boolean
     */
    protected $is_administrator;

    public function isGuest()
    {
        // У гостей ID модели User всегда меньше 0 (-1)
        // поэтому лезть в базу не обязательно
        return $this->getId() < 0;
    }

    public function isUser()
    {
        if ($this->is_user === null)
        {
            $this->is_user = $this->getGroup() == $this->mapperManager->getMapper('Group/Group')
                                                       ->findGroupByAlias('user')
                                                       ->getId();
        }

        return $this->is_user;
    }

    public function isAdministrator()
    {
        if ($this->is_administrator === null)
        {
            $this->is_administrator = $this->getGroup() == $this->mapperManager->getMapper('Group/Group')
                                                                ->findGroupByAlias('administrator')
                                                                ->getId();
        }

        return $this->is_administrator;
    }

    public function getFullName()
    {
        return $this->first_name . ($this->last_name ? ' ' . $this->last_name : '');
    }

    public function getFullNameOrLogin()
    {
        return $this->getFullName() ? $this->getFullName() : $this->getLogin();
    }

    public function getAgeDay()
    {
        if ($this->age && $this->age instanceof Module_Common_Type_Datetime)
        {
            return $this->age->format('j');
        }

        return null;
    }

    public function getAgeMonth()
    {
        if ($this->age && $this->age instanceof Module_Common_Type_Datetime)
        {
            return $this->age->format('n');
        }

        return null;
    }

    public function getAgeYear()
    {
        if ($this->age && $this->age instanceof Module_Common_Type_Datetime)
        {
            return $this->age->format('Y');
        }

        return null;
    }

    /**
     * @see parent::setId()
     */
    public function setId($id)
    {
        if (!empty($this->data['id']) && $this->data['id'] != -1 && $this->data['id'] != $id)
        {
            throw new LogicException('Нельзя переопределить значение ID объекта модели ' . get_class($this));
        }

        $this->id = $id;

        return true;
    }

    protected function _setUrl($url)
    {
        return $url === 'http://' ? null : $url;
    }

    protected function _setPassword($password)
    {
        if ($password === null || $password === '')
        {
            return null;
        }

        return md5($password);
    }
}