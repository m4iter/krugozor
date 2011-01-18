<?php
abstract class Base_Model extends Cover_Abstract_Simple
{
    /**
     * Карта атрибутов модели.
     * Перечисляются в дочерних классах в виде массивов следующего содержания:
     *
     * 'first_name' => array('db_element' => true,
     *                       'db_field_name' => 'user_first_name',
     *                       'default_value' => null,
     *                       'validators' => array(
     *                           'Common/Decimal' => array('unsigned' => false), ...)
     *                       )
     *
     * Допустимые свойства и их возможные значения:
     *
     * type          Тип данных свойства. Тип указывается только для сложных, не скалярных типов,
     *               например, для таких, как объект Module_Common_Type_Datetime,
     *               Module_Common_Type_Email и т.д. Если тип не указан, значит, это скаляр.
     * db_element    [true|false] должно ли это свойство записываться в БД.
     *               В большинстве случаев это свойство устанавливается в true. Исключения составляют
     *               какие-то "вспомогательные" свойства объекта, которые допустимо иметь в качестве
     *               членов класса, но записывать в БД не нужно.
     *               Например, свойство ID Primary Key для каждой таблицы имеет значение false по
     *               причине того, что никогда не пишется в таблицу, а является лишь указателем
     *               на запись, т.е. фактически является "вспомогательным" в данной терминологии.
     * db_field_name Имя поля таблицы данных, ассоциируемое с данным свойством класса.
     * default_value Значение свойства по умолчанию, при инстанцировании объекта.
     *               Данный параметр никак не связан со значением DEFAULT SQL-описания таблицы данных.
     * validators    Массив валидаторов, которые должны быть применены к свойству при
     *               присвоении ему значения.
     *
     * @var array
     */
    protected static $model_attributes = array();

    /**
     * Префикс имен полей таблицы.
     * Исторически все поля таблиц моделей, за исключением поля id,
     * именуются с однотипными префиксами, означающим, к какой таблице относится поле.
     * Например: user_name (таблица user), group_type (таблица group) и т.д.
     *
     * @var string
     */
    protected static $db_field_prefix;

    /**
     * Многомерный массив сообщений об ошибках валидации свойств.
     * Заполняется сообщениями, посупающими из валидаторов при присвоении объекту
     * значений, не удовлетворяющих описанным в карте модели валидаторам.
     * См. описание self::$model_attributes
     *
     * @var array
     */
    protected $validate_errors = array();

    /**
     * Приимает потенциальный массив свойств объекта (вида "имя_свойства" => "значение")
     * и анализируя ключи, вызывает виртуальные set-методы, через которые значения
     * присваиваются объекту. Если в объект подается ключ, имя которого не найдено
     * в массиве известных аттрибутов модели $this->model_attributes, то такое
     * присваивание будет проигнарировано без вывода каких-либо ошибок.
     *
     * @todo: нужно ли выводить ошибки в случае присваивания объекту неизвестного свойства?
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
     * Возвращает префикс имен полей таблицы.
     *
     * @param void
     * @return string
     */
    public function getDbFieldPrefix()
    {
        return static::$db_field_prefix;
    }

    /**
     * Возвращает опции аттрибута карты модели под индексом $key,
     * если аттрибут существует, и null в обратном случае.
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
     * Возвращает массив опций аттрибутов модели.
     *
     * @param void
     * @return array
     */
    public function getMaps()
    {
        return static::$model_attributes;
    }

    /**
     * Получает имя set- или get- метода для свойства объекта с именем $property_name.
     * Имя свойства $property_name может подаваться с приставкой $this->db_field_prefix
     * или без неё, т.е. методы с разными вызовами:
     *
     * ->getMethodNameByKeyWithPrefix('user_name', 'set');
     * ->getMethodNameByKeyWithPrefix('name', 'set');
     *
     * возвратят одинаковый результат - имя метода setUserName()
     *
     * @param string $property_name имя свойства объекта
     * @param string $action set|get действие метода
     * @return string имя get- или set- метода
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
	            trigger_error('Указан некорректный action <b>' . $action .
	                          '</b> в контексте вызова метода <b>' . __METHOD__ . '</b>', E_USER_WARNING);

	            return null;
            }

	        return $action . $key;
        }

        return null;
    }

    /**
    * Устанавливает значение $value для свойства $key объека.
    *
    * @param string $key имя свойства объекта
    * @param string mixed значение свойства объекта
    * @return Base_Model
    */
    public function __set($key, $value)
    {
        // Такого свойства в модели нет
        if (!isset(static::$model_attributes[$key]))
        {
            trigger_error('Свойство ' . $key . ' не принадлежит модели ' . get_class($this), E_USER_WARNING);

            return $this;
        }

        // В карте описания свойств модели указаны валидаторы,
        // которыми необходимо валидировать значение $value.

        // Модель принимает любые данные, даже ошибочные.
        // Валидация в модели носит лишь уведомительный характер.
        // Принимать решение, что делать с ошибочной моделью должен слой,
        // оперирующий с этой моделью.
        if (isset(static::$model_attributes[$key]['validators']))
        {
            // Если в объекте модели уже содержится информация об ошибочном заполнении
            // данного свойства, то эту информацию необходимо удалить, т.к. идет присвоение нового
            // значения и старая информация об ошибках уже не актуальна.
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
                    // $value может быть либо объектом - собственным типом данных, либо скаляром.
                    $value = is_object($value) &&
                             $value instanceof Module_Common_Type_Interface &&
                             method_exists($value, 'getValue')
                                ? $value->getValue()
                                : $value;

                    $validator = new $validator_class_name($value);

                    foreach ($params as $validator_criteria => $criteria_value)
                    {
                        // Метод под именем {'set'.$validator_criteria} должен присутствовать
                        // в вызванном валидаторе.
                        $method = 'set' . $validator_criteria;

                        if (method_exists($validator, $method))
                        {
                            $validator->$method($criteria_value);
                        }
                        else
                        {
                            trigger_error('Вызов неизвестного метода валидатора: ' .
                                          $validator_class_name . '::' . $method, E_USER_WARNING);
                        }
                    }

                    // Возникли ошибки валидации, помещаем их в общее хранилище.
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
     * Получение и установка свойств объекта через вызов магического
     * метода вида:
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
                'Вызов неизвестного метода ' . get_class($this) . '::' . $method_name
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

                // Смотрим, имеется ли в классе явно объявленный set-метод (с префиксом "_") для
                // данного свойства и имеются ли ошибки валидации.
                // Если метод явно объявлен, а ошибок валидации нет, то применяем метод
                // для текущего состояния свойства.
                if (method_exists($this, $explicit_method) && !$has_errors)
                {
                   $this->data[$property_name] = $this->$explicit_method($this->$property_name);
                }

                return $has_errors;
        }
    }

    /**
     * Возвращает ошибки валидации модели.
     *
     * @param void
     * @return array
     */
    public function getValidateErrors()
    {
        return $this->validate_errors;
    }

    /**
     * Возвращает ошибки валидации свойства $key
     *
     * @param string имя свойства
     * @return array информация об ошибке
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
     * Явный метод setId(), предупреждающий затирание
     * явно существующего ID текущего объекта.
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
                'Нельзя переопределить значение ID объекта модели ' . get_class($this)
            );
        }

        $this->id = $id;

        return true;
    }

    /**
     * Устанавливает для свойства объекта $key значение $value
     * в соответствии с картой описания свойств static::$model_attributes[$key].
     *
     * @param $key имя свойства объекта
     * @param $value значение свойства объекта
     * @return void
     * @throws RuntimeException
     */
    private function setValueWithTransformation($key, $value)
    {
        // Тип свойства не указан в карте описания свойств модели.
        // Значит, работаем со скалярным типом данных и присваеваем
        // "как есть" значение $value свойству $key.
        if (!isset(static::$model_attributes[$key]['type']))
        {
            $this->data[$key] = $value;
        }
        else
        {
            // Если $value - объект, производный от указанного в карте
            // описания свойств модели, то никаких преобразований с $value не делаем.
            if (is_object($value) && $value instanceof static::$model_attributes[$key]['type'])
            {
                $this->data[$key] = $value;
            }
            // Если $value - скалярное значение, значит, его необходимо
            // преобразовать в указанный в карте модели объект.
            // Для этого значение $value необходимо передать в конструктор
            // указанного в карте описания свойств модели класса.
            else
            {
                if (!class_exists(static::$model_attributes[$key]['type']))
                {
                	throw new RuntimeException(
                        'Не найден класс типа <b>' . static::$model_attributes[$key]['type'] . '</b>'
                    );
                }

                // Если в объекте типа будет выброшено исключение UnexpectedValueException,
                // значит, объект создавать не нужно, а свойству модели $key необходимо
                // присвоить значение null.
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