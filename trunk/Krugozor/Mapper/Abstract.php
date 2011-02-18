<?php
/**
 * Базовый мэппер.
 *
 * @abstract
 */
abstract class Mapper_Abstract
{
    /**
     * Объект класса БД.
     *
     * @access protected
     * @var object
     */
    protected $db;

    /**
     * Имя таблицы модели.
     *
     * @access protected
     * @var string
     * @todo: вынести в модель?
     */
    protected $db_table_name;

    /**
     * Имя класса модели.
     *
     * @access protected
     * @var string
     * @todo: сделать по аналогии с $model_name и $module_name, т.е. вычислять из get_class($this)?
     */
    protected $model_class_name;

    /**
     * Имя модели, связанной с экземпляром текущего объекта меппера.
     * Заполняется по требования в методе $this->getModelName()
     *
     * @access protected
     * @var string
     */
    protected $model_name;

    /**
     * Имя модуля, связанного с экземпляром текущего объекта меппера.
     * Заполняется по требования в методе $this->getModuleName()
     *
     * @access protected
     * @var string
     */
    protected $module_name;

    /**
     * Кэш-коллекция объектов уже полученных из БД.
     * Коллекция представляет собой многомерный массив вида
     * $collection[Module_Name][Model_Name][ID_object] = object
     *
     * @access protected
     * @var array
     * @static
     */
    protected static $collection = array();

    /**
     * Мэнеджер мэпперов.
     *
     * @var Mapper_Manager
     */
    protected $manager;

    /**
     * Инициализация каких-то действий наследника.
     * Данный метод предопределяется в классе-наследнике, а вызывается
     * в конструкторе данного базового класса.
     *
     * @abstract
     * @param void
     * @return void
     */
    abstract protected function init();

    /**
     * @param Db_Mysql_Base $db
     * @param Mapper_Manager $manager
     */
    public function __construct(Db_Mysql_Base $db, Mapper_Manager $manager)
    {
        $this->db = $db;
        $this->manager = $manager;
        $this->init();
    }

    /**
     * Возвращает имя модели.
     *
     * @access public
     * @param void
     * @return string
     */
    public function getModelName()
    {
        if (null === $this->model_name)
        {
            list(,,,$this->model_name) = explode('_', get_class($this));
        }

        return $this->model_name;
    }

    /**
     * Возвращает имя модуля моделя.
     *
     * @access public
     * @param void
     * @return string
     */
    public function getModuleName()
    {
        if (null === $this->module_name)
        {
            list(,$this->module_name) = explode('_', get_class($this));
        }

        return $this->module_name;
    }

    /**
     * Возвращает имя таблицы БД.
     *
     * @access public
     * @param void
     * @return string
     */
    public function getTableName()
    {
        return $this->db_table_name;
    }

    /**
     * Возвращает коллекцию self::$collection
     *
     * @access public
     * @param void
     * @return array коллецкия объектов
     * @static
     */
    public static function getCollection()
    {
        return self::$collection;
    }

    /**
     * Удаляет элемент коллекци self::$collection.
     *
     * @param string $module_name имя модуля
     * @param string $model_name имя модели
     * @param int $id ID объекта
     * @return void
     */
    public static function unsetCollectionElement($module_name, $model_name, $id)
    {
        unset(self::$collection[$module_name][$model_name][$id]);
    }

	/*********************************************************************************
	*       П О Р А Ж Д А Ю Щ И Е    М Е Т О Д Ы
	**********************************************************************************/

    /**
     * Создает пустой объект модели на основе карты
     * опций аттрибутов модели Base_Model::model_attributes.
     * Значениями аттрибутов объекта становятся значения по умолчанию,
     * определенные в карте опций модели под индексами 'default_value'.
     * Если значения по умолчанию не заданы в карте модели, то свойства
     * задаются со значением null.
     *
     * @access protected
     * @param void
     * @return object
     * @final
     */
    protected final function createModel()
    {
        $object = new $this->model_class_name();
        $object->setMapperManager($this->manager);

        foreach ($object->getMaps() as $key => $params)
        {
            if ($method_name = $object->getMethodNameByKeyWithPrefix($key, 'set'))
            {
	            $object->$method_name(
	               isset($params['default_value']) ? $params['default_value'] : null
	            );
            }
        }

        return $object;
    }

    /**
     * Создает доменный объект из массива $data.
     * Обычно массив $data представляют собой результат выборки из СУБД.
     *
     * @access protected
     * @param array
     * @return object
     * @final
     */
    protected final function createModelFromArray(array $data)
    {
        $object = new $this->model_class_name();
        $object->setMapperManager($this->manager);

        // SQL-запрос вернул результат, запись найдена.
        if ($data)
        {
            $object->setData($data);
        }
        // Запись не найдена.
        // Создаем пустой объект с единственным параметром id = 0.
        else
        {
	        if ($map = $object->getMapItem('id') &&
	            $method_name = $object->getMethodNameByKeyWithPrefix('id', 'set'))
	        {
	            $object->$method_name(0);
	        }
        }

        return $object;
    }

    /**
     * Принимает результат выполнения SQL-запроса и возвращает массив
     * объектов моделей, созданых на основе результата выборки.
     * Основной метод для получения списка объектов на основе JOIN-запроса.
     *
     * @access protected
     * @param  DB_Mysql_Statement $statement
     * @return Cover_Array|false
     * @todo: стоит ли создать type hint DB_Mysql_Statement?
     */
    protected final function result2objects(DB_Mysql_Statement $statement)
    {
        if (!is_object($statement) || !$statement instanceof DB_Mysql_Statement)
        {
            return false;
        }

        $fields = array();

        $data = new Cover_Array();

        $count_fields = mysql_num_fields($statement->getResult());

        for ($i = 0; $i < $count_fields; ++$i)
        {
            $fields[] = mysql_fetch_field($statement->getResult());
        }

        while ($row = mysql_fetch_row($statement->getResult()))
        {
            $count_row = count($row);

            for ($i=0; $i < $count_row; $i++)
            {
                $temp[$fields[$i]->table][$fields[$i]->name] = $row[$i];
            }

            $data[] = $temp;
        }

        $count_data = count($data);

        for ($i=0; $i < $count_data; $i++)
        {
            foreach ($data[$i] as $table_name => $props)
            {
                $temps = explode('_', $table_name, 2);

                if (count($temps) > 1)
                {
                    $module = $temps[0];
                    $model = $temps[1];
                }
                else
                {
                    $module = $temps[0];
                    $model = $temps[0];
                }

                $module_name = ucfirst($module);
                $model_name = ucfirst($model);

                $current_model_class_name = 'Module_' . $module_name . '_Model_' . $model_name;
                $current_model = new $current_model_class_name();
                $current_model->setMapperManager($this->manager);
                $current_model->setData($props);
                $data[$i][$table_name] = $current_model;

                self::$collection[$module_name][$model_name][$current_model->getId()] = $current_model;
            }
        }

        return $data;
    }

	/*********************************************************************************
	*       М Е Т О Д Ы    В Ы Б О Р К И
	**********************************************************************************/

    /**
     * Исполняет SELECT-запрос и возвращает объект результата DB_Mysql_Statement.
     *
     * @access protected
     * @param array
     * @return DB_Mysql_Statement
     * @final
     */
    protected final function createQuerySelect($params)
    {
        $params = self::makeSqlFromParams($params);

        $sql = 'SELECT' . $params['what'] . 'FROM `' . $this->db_table_name . '`' .
               $params['join'] . $params['where'] . $params['order'] . $params['limit'];

        array_unshift($params['args'], $sql);

        return call_user_func_array(array($this->db, 'query'), $params['args']);
    }

    /**
     * Возвращает доменный объект на основании параметров $params.
     *
     * @access protected
     * @param array
     * @return Base_Model
     * @final
     * @todo: м.б. добавить сюда доп. параметр, запрещающий кэширование объекта модели?
     */
    protected final function findModelByParams(array $params)
    {
        $res = $this->createQuerySelect($params);

        $object = $this->createModelFromArray(is_object($res) && $res->getNumRows()
                                              ? $res->fetch_assoc()
                                              : array()
                                             );

        if ($object->getId())
        {
            self::$collection[$this->getModuleName()][$this->getModelName()][$object->getId()] = $object;
        }

        return $object;
    }

    /**
     * Возвращает объект Cover_Array, содержащий список объектов
     * выбранных согласно массиву параметров $params.
     *
     * @access protected
     * @param array параметры выборки
     * @return Cover_Array
     * @final
     */
    protected final function findModelList($params = array())
    {
        $data = new Cover_Array();

        $res = $this->createQuerySelect($params);

        if (is_object($res) && $res->getNumRows() > 0)
        {
            while ($row = $res->fetch_assoc())
            {
                $object = $this->createModelFromArray($row);

                self::$collection[$this->getModuleName()][$this->getModelName()][$object->id] = $object;

                $data->append($object);
            }
        }

        return $data;
    }

	/*********************************************************************************
	*       Д Е Й С Т В И Я    с   О Б Ъ Е К Т А М И
	**********************************************************************************/

    /**
     * Сохраняет объект в БД.
     *
     * @access protected
     * @param Base_Model $object
     * @return Base_Model
     * @final
     */
    protected final function saveModel(Base_Model $object)
    {
        $args = array();

        if ($object->getId())
        {
            $sql = 'UPDATE `' . $this->db_table_name . '` SET ';
        }
        else
        {
            $sql = 'INSERT INTO `' . $this->db_table_name . '` SET ';
        }

        foreach ($object->getData() as $key => $value)
        {
            $options = $object::getMapItem($key);

            if (!isset($options['db_element']) || !$options['db_element'])
            {
                continue;
            }

            if (is_object($value))
            {
                // Объект Module_Common_Type_Datetime обрабатываем "особо" в виду его сложности
                // и фактической пригодности для нескольких видов полей таблицы.
                if ($value instanceof Module_Common_Type_Datetime)
                {
                    $fields_info = $this->db->getListFields($this->db_table_name);

                    $db_field_name = $object->getDbFieldPrefix() . '_' . $key;

                    if (!empty($fields_info[$db_field_name]))
                    {
                        switch ($fields_info[$db_field_name]->type)
                        {
                            case 'datetime':
                                $sql .= '`' . $options['db_field_name'] . '` = "?s", ';
                                $args[] = $value->format('Y-m-d H:i:s');
                                break;

                            case 'date':
                                $sql .= '`' . $options['db_field_name'] . '` = "?s", ';
                                $args[] = $value->format('Y-m-d');
                                break;

                            case 'int':
                                $sql .= '`' . $options['db_field_name'] . '` = ?i, ';
                                $args[] = $value->getTimestamp();
                                break;
                        }
                    }
                }
                else if ($value instanceof $options['type'] && method_exists($value, 'getValue'))
                {
                    $value = $value->getValue();

                    goto value_is_scalar;
                }
            }
            else
            {
                value_is_scalar:

                // Пустые строки в базу не пишем, вместо них пишем NULL
                if ($value === null || $value === '')
                {
                    $sql .= '`' . $options['db_field_name'] . '` = NULL, ';
                }
                else
                {
                    $sql .= '`' . $options['db_field_name'] . '` = "?s", ';

                    $args[] = $value;
                }
            }
        }

        $sql = rtrim($sql, ', ');

        if ($object->getId())
        {
            $args[] = $object->getId();

            $sql = $sql . ' WHERE id = ?i';

            array_unshift($args, $sql);

            call_user_func_array(array($this->db, 'query'), $args);
        }
        else
        {
            array_unshift($args, $sql);

            call_user_func_array(array($this->db, 'query'), $args);

            $object->setId($this->db->getInsertId());
        }

        return $object;
    }

    /**
     * Исполняет DELETE-запрос и возвращает объект результата DB_Mysql_Statement.
     *
     * @access protected
     * @param array $params
     * @return DB_Mysql_Statement
     * @final
     */
    protected final function createQueryDelete(array $params)
    {
        $params = self::makeSqlFromParams($params);

        $sql = 'DELETE FROM `' . $this->db_table_name . '`' . $params['where'] . $params['limit'];

        array_unshift($params['args'], $sql);

        return call_user_func_array(array($this->db, 'query'), $params['args']);
    }

	/**
	 * Метод формирования SQL запросов из массива параметров params.
	 * Массив параметров представляет собой ассоциативный массив, где ключи являются
	 * условиями и элементами SQL запроса, а значения - данные для подстановки в SQL.
	 *
	 * 1. 'where'.
	 * 'where' может быть массивом вида
	 * $params['where'] = array
	 * (
	 *     'id = ?' => array(23),
	 *     'id = ? AND foo = "?" AND foo2 LIKE "?!" ' => array(23, 'hellow', 'world')
	 * );
	 * или строкой вида
	 * $params['where'] = 'id = 5';
	 * 'where' может быть не определён, тогда where-условие не используется.
	 *
	 * 2. 'what'
	 * 'what' может являться строкой вида
	 * $params['what'] = 'name, value';
	 * 'what' может быть не определён, тогда what-условие по умолчанию становится как *.
	 *
	 * 3. 'limit'
	 * 'limit' может является массивом вида
	 * $params['what'] = array('start' => int [, 'stop' => int]);
	 * 'limit' может быть не определён, тогда limit-условие не используется.
	 *
	 * 4. 'order'
	 * 'order' может является массивом вида
	 * $params['order'] = array
	 * (
	 *     'col' => 'ASC|DESC' [, 'col2' => 'ASC|DESC']
	 * )
	 * где 'col' - столбец, по которому производится сортировка
	 *     'ASC|DESC' - один из двух методов сортировки
	 * 'order' может быть не определён, тогда order-условие не используется.
	 *
	 * 5. 'group'
	 * 'group' может является массивом вида
	 * $params['group'] = array
	 * (
	 *     'col' => 'ASC|DESC' [, 'col2' => 'ASC|DESC']
	 * )
	 * где 'col' - столбец, по которому производится группировка
	 *     'ASC|DESC' - один из двух методов сортировки
	 *
	 * @param array
	 * @return array
	 */
	protected static function makeSqlFromParams($params)
	{
	    // Аргументы для подстановки в маркеры SQL запроса.
	    // Фактически, это константные данные SQL-запроса.
	    $sql_store = array('args' => array(),
	                       'where' => '',
	                       'join' => '',
	                       'what' => ' * ',
	                       'limit' => '',
	                       'order' => '',
	                       'group' => ''
	                       );

	    // where-условие
        if (!empty($params['where']))
        {
            $where_sql = '';

            if (is_array($params['where']))
            {
                foreach ($params['where'] as $sql_key => $args_value)
                {
                    foreach ($args_value as $value)
                    {
                        $sql_store['args'][] = is_object($value) ? $value->getValue() : $value;
                    }

                    $where_sql .= ' '.$sql_key.' ';
                }
            }
            else
            {
                $where_sql = trim($params['where']);
            }

            $sql_store['where'] = $where_sql !== null && $where_sql !== '' ? ' WHERE '.$where_sql : '';
        }

        // join
	    if (!empty($params['join']))
	    {
	        $join_array = array();

	        foreach ($params['join'] as $join)
	        {
	            $join_array[] = ' '.$join[0].' `'.$join[1].'` ON '.$join[2].' ';
	        }

	        $sql_store['join'] = implode('', $join_array);
	    }

	    // what
	    if (!empty($params['what']))
	    {
            $what_sql = trim($params['what']);

            $sql_store['what'] = $what_sql !== '' ? ' '.$what_sql.' ' : $sql_store['what'];
	    }

	    // limit
	    if (!empty($params['limit']) && is_array($params['limit']))
	    {
	        $sql_store['limit'] = isset($params['limit']['start']) && Base_Numeric::is_decimal($params['limit']['start'], true)
	                              ? ' LIMIT '.$params['limit']['start'].
	                                (isset($params['limit']['stop']) && Base_Numeric::is_decimal($params['limit']['stop'], true)
	                                 ? ', '.$params['limit']['stop']
	                                 : ''
	                                )
	                              : '';
	    }

	    // order
	    if (!empty($params['order']))
	    {
            $order_sql = '';

	        foreach ($params['order'] as $field => $method)
	        {
	            // Определяем, что из себя представляет order-параметр:
	            // Если order имеет вид типа table.col, то преобразуем этот параметр в `table`.`col`,
	            // если order имеет вид типа col, то преобразуем к виду `col`.
	            $temp = explode('.', $field);

	            if (count($temp) > 1)
	            {
	                $field = '`'.$temp[0].'`.`'.$temp[1].'`';
	            }
	            else
	            {
	                $field = '`'.$field.'`';
	            }

	            $order_sql .= $field.' '.$method.', ';
	        }

	        $order_sql = rtrim($order_sql, ', ');

	        $sql_store['order'] = ' ORDER BY '.$order_sql;
	    }

	    // group
	    // todo: сделать возможность группировать по полям вида таблица.поле как в order?
	    if (isset($params['group']))
	    {
	        $group_sql = '';

	        foreach ($params['group'] as $field => $method)
	        {
	            $group_sql .= '`'.$field.'` '.$method.', ';
	        }

	        $group_sql = rtrim($group_sql, ', ');

	        $sql_store['group'] = ' GROUP BY '.$group_sql;
	    }

	    return $sql_store;
	}
}


    /**
     * Возвращает объект модели $model_name модуля $module_name
     * с идентификатором $id из коллекции объектов $collection
     *
     * @access public
     * @param string $module_name имя модуля
     * @param string $model_name имя модели
     * @param int id ID запрашиваемого объекта
     * @return object
     * @static
     * зачем это?

    public static function getCollectionObjectById($module_name, $model_name, $id)
    {
        $module_name = ucfirst($module_name);
        $model_name = ucfirst($model_name);

        if (!isset(self::$collection[$module_name][$model_name][$id]))
        {
            $mapper_name = 'Module_'.$module_name.'_Mapper_'.$model_name;

            $mapper = new $mapper_name();
            $object = $mapper->findById($id);

            if ($object->id)
            {
                return self::$collection[$module_name][$model_name][$id] = $object;
            }
            else
            {
                return $object;
            }
        }

        return self::$collection[$module_name][$model_name][$id];
    }*/
?>