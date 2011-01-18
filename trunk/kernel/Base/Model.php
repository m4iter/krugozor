<?php
abstract class Base_Model extends Cover_Abstract_Simple
{
    /**
     * ����� ��������� ������.
     * ������������� � �������� ������� � ���� �������� ���������� ����������:
     *
     * 'first_name' => array('db_element' => true,
     *                       'db_field_name' => 'user_first_name',
     *                       'default_value' => null,
     *                       'validators' => array(
     *                           'Common/Decimal' => array('unsigned' => false), ...)
     *                       )
     *
     * ���������� �������� � �� ��������� ��������:
     *
     * type          ��� ������ ��������. ��� ����������� ������ ��� �������, �� ��������� �����,
     *               ��������, ��� �����, ��� ������ Module_Common_Type_Datetime,
     *               Module_Common_Type_Email � �.�. ���� ��� �� ������, ������, ��� ������.
     * db_element    [true|false] ������ �� ��� �������� ������������ � ��.
     *               � ����������� ������� ��� �������� ��������������� � true. ���������� ����������
     *               �����-�� "���������������" �������� �������, ������� ��������� ����� � ��������
     *               ������ ������, �� ���������� � �� �� �����.
     *               ��������, �������� ID Primary Key ��� ������ ������� ����� �������� false ��
     *               ������� ����, ��� ������� �� ������� � �������, � �������� ���� ����������
     *               �� ������, �.�. ���������� �������� "���������������" � ������ ������������.
     * db_field_name ��� ���� ������� ������, ������������� � ������ ��������� ������.
     * default_value �������� �������� �� ���������, ��� ��������������� �������.
     *               ������ �������� ����� �� ������ �� ��������� DEFAULT SQL-�������� ������� ������.
     * validators    ������ �����������, ������� ������ ���� ��������� � �������� ���
     *               ���������� ��� ��������.
     *
     * @var array
     */
    protected static $model_attributes = array();

    /**
     * ������� ���� ����� �������.
     * ����������� ��� ���� ������ �������, �� ����������� ���� id,
     * ��������� � ����������� ����������, ����������, � ����� ������� ��������� ����.
     * ��������: user_name (������� user), group_type (������� group) � �.�.
     *
     * @var string
     */
    protected static $db_field_prefix;

    /**
     * ����������� ������ ��������� �� ������� ��������� �������.
     * ����������� �����������, ����������� �� ����������� ��� ���������� �������
     * ��������, �� ��������������� ��������� � ����� ������ �����������.
     * ��. �������� self::$model_attributes
     *
     * @var array
     */
    protected $validate_errors = array();

    /**
     * �������� ������������� ������ ������� ������� (���� "���_��������" => "��������")
     * � ���������� �����, �������� ����������� set-������, ����� ������� ��������
     * ������������� �������. ���� � ������ �������� ����, ��� �������� �� �������
     * � ������� ��������� ���������� ������ $this->model_attributes, �� �����
     * ������������ ����� ��������������� ��� ������ �����-���� ������.
     *
     * @todo: ����� �� �������� ������ � ������ ������������ ������� ������������ ��������?
     * @param array
     * @return void
     */
    public function setData($data)
    {
        foreach ($data as $key => $value)
        {
            $method = $this->getMethodNameByKeyWithPrefix($key, 'set');

            if (is_string($method) && $method != '')
            {
                $this->$method($value);
            }
        }
    }

    /**
     * ���������� ������� ���� ����� �������.
     *
     * @param void
     * @return string
     */
    public function getDbFieldPrefix()
    {
        return static::$db_field_prefix;
    }

    /**
     * ���������� ����� ��������� ����� ������ ��� �������� $key,
     * ���� �������� ����������, � null � �������� ������.
     *
     * @param string
     * @return Cover_MapItem|null
     */
    public static function getMapItem($key)
    {
        return isset(static::$model_attributes[$key])
               ? new Cover_MapItem(static::$model_attributes[$key])
               : null;
    }

    /**
     * ���������� ������ ����� ���������� ������.
     *
     * @param void
     * @return array
     */
    public function getMaps()
    {
        return static::$model_attributes;
    }

    /**
     * �������� ��� set- ��� get- ������ ��� �������� ������� � ������ $property_name.
     * ��� �������� $property_name ����� ���������� � ���������� $this->db_field_prefix
     * ��� ��� ��, �.�. ������ � ������� ��������:
     *
     * ->getMethodNameByKeyWithPrefix('user_name', 'set');
     * ->getMethodNameByKeyWithPrefix('name', 'set');
     *
     * ��������� ���������� ��������� - ��� ������ setUserName()
     *
     * @param string $property_name ��� �������� �������
     * @param string $action set|get �������� ������
     * @return string ��� get- ��� set- ������
     */
    public function getMethodNameByKeyWithPrefix($property_name, $action = 'set')
    {
        $key = preg_replace('~^'.static::$db_field_prefix.'_([a-z0-9_]+)$~', '$1', $property_name);

        if (isset(static::$model_attributes[$key]))
        {
	        $args = explode('_', $key);

	        $count = count($args);

	        for ($i=0; $i<$count; $i++)
	        {
	            $args[$i][0] = strtoupper($args[$i][0]);
	        }

	        $key = implode('', $args);

	        if (!in_array($action, array('set', 'get')))
	        {
	            trigger_error('������ ������������ action <b>' . $action .
	                          '</b> � ��������� ������ ������ <b>' . __METHOD__ . '</b>', E_USER_WARNING);

	            return null;
            }

	        return $action . $key;
        }

        return null;
    }

    /**
    * ������������� �������� $value ��� �������� $key ������.
    *
    * @param string $key ��� �������� �������
    * @param string mixed �������� �������� �������
    * @return Base_Model
    */
    public function __set($key, $value)
    {
        // ������ �������� � ������ ���
        if (!isset(static::$model_attributes[$key]))
        {
            trigger_error('�������� ' . $key . ' �� ����������� ������ ' . get_class($this), E_USER_WARNING);

            return $this;
        }

        // � ����� �������� ������� ������ ������� ����������,
        // �������� ���������� ������������ �������� $value.

        // ������ ��������� ����� ������, ���� ���������.
        // ��������� � ������ ����� ���� �������������� ��������.
        // ��������� �������, ��� ������ � ��������� ������� ������ ����,
        // ����������� � ���� �������.
        if (isset(static::$model_attributes[$key]['validators']))
        {
            // ���� � ������� ������ ��� ���������� ���������� �� ��������� ����������
            // ������� ��������, �� ��� ���������� ���������� �������, �.�. ���� ���������� ������
            // �������� � ������ ���������� �� ������� ��� �� ���������.
            if (isset($this->validate_errors[$key]))
            {
                unset($this->validate_errors[$key]);
            }

            foreach (static::$model_attributes[$key]['validators'] as $validator_path => $params)
            {
                list($module_name, $validator_name) = explode('/', $validator_path);

                $validator_class_name = 'Module_' . $module_name . '_Validator_' . $validator_name;

                if (class_exists($validator_class_name))
                {
                    // $value ����� ���� ���� �������� - ����������� ����� ������, ���� ��������.
                    $value = is_object($value) &&
                             $value instanceof Module_Common_Type_Interface &&
                             method_exists($value, 'getValue')
                                ? $value->getValue()
                                : $value;

                    $validator = new $validator_class_name($value);

                    foreach ($params as $validator_criteria => $criteria_value)
                    {
                        // ����� ��� ������ {'set'.$validator_criteria} ������ ��������������
                        // � ��������� ����������.
                        $method = 'set' . $validator_criteria;

                        if (method_exists($validator, $method))
                        {
                            $validator->$method($criteria_value);
                        }
                        else
                        {
                            trigger_error('����� ������������ ������ ����������: ' .
                                          $validator_class_name . '::' . $method, E_USER_WARNING);
                        }
                    }

                    // �������� ������ ���������, �������� �� � ����� ���������.
                    if (!$validator->validate())
                    {
                        $this->validate_errors[$key][] = $validator->getError();
                    }
                }
            }
        }

        $this->setValueWithTransformation($key, $value);

        return $this;
    }

    /**
     * ��������� � ��������� ������� ������� ����� ����� �����������
     * ������ ����:
     *
     * $model->(get|set)Propertyname($prop);
     *
     * @see __call
     */
    public function __call($method_name, $argument)
    {
        $args = preg_split('/(?<=\w)(?=[A-Z])/', $method_name);

        $action = array_shift($args);

        $property_name = strtolower(implode('_', $args));

        if (!isset(static::$model_attributes[$property_name]))
        {
        	throw new BadMethodCallException(
                '����� ������������ ������ ' . get_class($this) . '::' . $method_name
            );
        }

        switch ($action)
        {
            case 'get':
                return $this->$property_name;

            case 'set':
                $this->$property_name = $argument[0];

                $has_errors = isset($this->validate_errors[$property_name]);

                $explicit_method = '_'.$method_name;

                // �������, ������� �� � ������ ���� ����������� set-����� (� ��������� "_") ���
                // ������� �������� � ������� �� ������ ���������.
                // ���� ����� ���� ��������, � ������ ��������� ���, �� ��������� �����
                // ��� �������� ��������� ��������.
                if (method_exists($this, $explicit_method) && !$has_errors)
                {
                   $this->data[$property_name] = $this->$explicit_method($this->$property_name);
                }

                return $has_errors;
        }
    }

    /**
     * ���������� ������ ��������� ������.
     *
     * @param void
     * @return array
     */
    public function getValidateErrors()
    {
        return $this->validate_errors;
    }

    /**
     * ���������� ������ ��������� �������� $key
     *
     * @param string ��� ��������
     * @return array ���������� �� ������
     */
    public function getValidateErrorsByKey($key)
    {
        if (isset($this->validate_errors[$key]))
        {
            return $this->validate_errors[$key];
        }

        return false;
    }

    /**
     * ����� ����� setId(), ��������������� ���������
     * ���� ������������� ID �������� �������.
     *
     * @param int $id
     * @return boolean
     * @throws LogicException
     */
    public function setId($id)
    {
        if (!empty($this->data['id']) && $this->data['id'] != $id)
        {
            throw new LogicException(
                '������ �������������� �������� ID ������� ������ ' . get_class($this)
            );
        }

        $this->id = $id;

        return true;
    }

    /**
     * ������������� ��� �������� ������� $key �������� $value
     * � ������������ � ������ �������� ������� static::$model_attributes[$key].
     *
     * @param $key ��� �������� �������
     * @param $value �������� �������� �������
     * @return void
     * @throws RuntimeException
     */
    private function setValueWithTransformation($key, $value)
    {
        // ��� �������� �� ������ � ����� �������� ������� ������.
        // ������, �������� �� ��������� ����� ������ � �����������
        // "��� ����" �������� $value �������� $key.
        if (!isset(static::$model_attributes[$key]['type']))
        {
            $this->data[$key] = $value;
        }
        else
        {
            // ���� $value - ������, ����������� �� ���������� � �����
            // �������� ������� ������, �� ������� �������������� � $value �� ������.
            if (is_object($value) && $value instanceof static::$model_attributes[$key]['type'])
            {
                $this->data[$key] = $value;
            }
            // ���� $value - ��������� ��������, ������, ��� ����������
            // ������������� � ��������� � ����� ������ ������.
            // ��� ����� �������� $value ���������� �������� � �����������
            // ���������� � ����� �������� ������� ������ ������.
            else
            {
                if (!class_exists(static::$model_attributes[$key]['type']))
                {
                	throw new RuntimeException(
                        '�� ������ ����� ���� <b>' . static::$model_attributes[$key]['type'] . '</b>'
                    );
                }

                // ���� � ������� ���� ����� ��������� ���������� UnexpectedValueException,
                // ������, ������ ��������� �� �����, � �������� ������ $key ����������
                // ��������� �������� null.
                try
                {
                    $this->data[$key] = new static::$model_attributes[$key]['type']($value);
                }
                catch (UnexpectedValueException $e)
                {
                    $this->data[$key] = null;
                }
            }
        }
    }
}