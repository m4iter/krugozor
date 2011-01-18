<?php
abstract class Base_Mapper
{
    /**
     * ������ ������ ��.
     * ���������������� � �������� ������ Module_Common_Mapper_Common
     *
     * @access protected
     * @var object
     */
    protected $db;

    /**
     * ��� ������� ������.
     *
     * @access protected
     * @var string
     */
    protected $db_table_name;

    /**
     * ��� ������ ������.
     *
     * @access protected
     * @var string
     */
    protected $model_class_name;

    /**
     * ��� ������, ��������� � ����������� �������� ������� �������.
     * ����������� �� ���������� � ������ $this->getModelName()
     *
     * @access protected
     * @var string
     */
    protected $model_name;

    /**
     * ��� ������, ���������� � ����������� �������� ������� �������.
     * ����������� �� ���������� � ������ $this->getModuleName()
     *
     * @access protected
     * @var string
     */
    protected $module_name;

    /**
     * ���-��������� �������� ��� ���������� �� ��.
     * ��������� ������������ ����� ����������� ������ ����
     * $collection[Module_Name][Model_Name][ID_object] = object
     *
     * @access protected
     * @var array
     * @static
     */
    protected static $collection = array();

    public function __construct(){}

    /**
     * ���������� ��� ������.
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
     * ���������� ��� ������ ������.
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
     * ���������� ��� ������� ��.
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
     * ���������� ��������� self::$collection
     *
     * @access public
     * @param void
     * @return array ��������� ��������
     * @static
     */
    public static function getCollection()
    {
        return self::$collection;
    }

    /**
     * ������� ������� �������� self::$collection.
     *
     * @param string $module_name ��� ������
     * @param string $model_name ��� ������
     * @param int $id ID �������
     * @return void
     */
    public static function unsetCollectionElement($module_name, $model_name, $id)
    {
        unset(self::$collection[$module_name][$model_name][$id]);
    }

	/*********************************************************************************
	*       � � � � � � � � � � �    � � � � � �
	**********************************************************************************/

    /**
     * ������� ������ ������ ������ �� ������ �����
     * ����� ���������� ������ Base_Model::model_attributes.
     * ���������� ���������� ������� ���������� �������� �� ���������,
     * ������������ � ����� ����� ������.
     * ���� �������� �� ��������� �� ������ � ����� ������, �� ��������
     * �������� �� ��������� null.
     *
     * @access protected
     * @param void
     * @return object
     * @final
     */
    protected final function createModel()
    {
        $object = new $this->model_class_name();

        foreach ($object->getMaps() as $key => $params)
        {
            if ($method_name = $object->getMethodNameByKeyWithPrefix($key, 'set'))
            {
                $value = isset($params['default_value'])
                         ? $params['default_value']
                         : null;

	            $object->$method_name($value);
            }
        }

        return $object;
    }

    /**
     * ������� �������� ������, �������� ��� ������� $data.
     * ������ $data ������������ ����� ��������� ������ �������
     * ���� �� ������� ������.
     *
     * @access protected
     * @param array
     * @return object
     * @final
     */
    protected final function createModelFromArray(array $data)
    {
        $object = new $this->model_class_name();

        // SQL-������ ������ ���������, ������ �������.
        if ($data)
        {
            foreach ($data as $key => $value)
            {
                if ($method_name = $object->getMethodNameByKeyWithPrefix($key, 'set'))
                {
                    $object->$method_name($value);
                }
            }
        }
        // ������ �� �������.
        // ������� ������ ������ � ������������ ���������� id = 0.
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
     * ��������� ��������� ���������� SQL-������� � ���������� ������
     * �������� �������, �������� �� ������ ���������� �������.
     * �������� ����� ��� ��������� ������ �������� �������� ������� �� ������ JOIN.
     *
     * @access protected
     * @param  resource $result
     * @return Cover_Array
     */
    protected final function result2objects($result)
    {
        if (!is_resource($result))
        {
            return false;
        }

        $fields = array();

        $data   = new Cover_Array();

        $count_fields = mysql_num_fields($result);

        for ($i = 0; $i < $count_fields; ++$i)
        {
            $fields[] = mysql_fetch_field($result);
        }

        while ($row = mysql_fetch_row($result))
        {
            $count_row = count($row);

            for ($i=0; $i<$count_row; $i++)
            {
                $temp[$fields[$i]->table][$fields[$i]->name] = $row[$i];
            }

            $data[] = $temp;
        }

        $count_data = count($data);

        for ($i=0; $i<$count_data; $i++)
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

                $current_model_class_name = 'Module_'.$module_name.'_Model_'.$model_name;
                $current_model = new $current_model_class_name();
                $current_model->setData($props);
                $data[$i][$table_name] = $current_model;

                self::$collection[$module_name][$model_name][$current_model->getId()] = $current_model;
            }
        }

        return $data;
    }

	/*********************************************************************************
	*       � � � � � �    � � � � � � �
	**********************************************************************************/

    /**
     * ���������� _������_����������_ �� �������.
     *
     * @access protected
     * @param array
     * @return object
     * @final
     */
    protected final function getResultForSqlSelect($params)
    {
        $params = self::makeSqlFromParams($params);

        $sql = 'SELECT'.
                   $params['what'].
               'FROM `'.$this->db_table_name.'`'.
               $params['join'].
               $params['where'].
               $params['order'].
               $params['limit'];

        array_unshift($params['args'], $sql);

        return call_user_func_array(array($this->db, 'query'), $params['args']);
    }

    /**
     * ���������� �������� ������ � ������ ���������� $params.
     *
     * @access protected
     * @param array
     * @return object
     * @final
     */
    protected final function findModelByParams(array $params)
    {
        $res = $this->getResultForSqlSelect($params);

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
     * ���������� ������ Cover_Array, ���������� ������ ��������
     * ��������� �������� ������� ���������� $params.
     *
     * @access protected
     * @param array ��������� �������
     * @return object Cover_Array
     * @final
     */
    protected final function findModelList($params = array())
    {
        $data = new Cover_Array();

        $res = $this->getResultForSqlSelect($params);

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
	*       � � � � � � � �    �   � � � � � � � � �
	**********************************************************************************/

    /**
     * ��������� ������ � ��.
     *
     * @access protected
     * @param object $object
     * @return void
     * @final
     */
    protected final function saveModel($object)
    {
        $args = array();

        if ($object->id)
        {
            $sql = 'UPDATE `'.$this->db_table_name.'` SET ';
        }
        else
        {
            $sql = 'INSERT INTO `'.$this->db_table_name.'` SET ';
        }

        foreach ($object->getData() as $key => $value)
        {
            $options = $object::getMapItem($key);

            if (!$options['db_element'])
            {
                continue;
            }

            if (is_object($value))
            {
                // ������ Module_Common_Type_Datetime ������������ "�����" � ���� ��� ���������
                // � ����������� ����������� ��� ���������� ����� ����� �������.
                if ($value instanceof Module_Common_Type_Datetime)
                {
                    $fields_info = $this->db->getListFields($this->db_table_name);

                    $db_field_name = $object->getDbFieldPrefix().'_'.$key;

                    if (!empty($fields_info[$db_field_name]))
                    {
                        switch ($fields_info[$db_field_name]->type)
                        {
                            case 'datetime':
                                $sql .= '`'.$options['db_field_name'].'` = "?s", ';
                                $args[] = $value->format('Y-m-d H:i:s');
                                break;

                            case 'date':
                                $sql .= '`'.$options['db_field_name'].'` = "?s", ';
                                $args[] = $value->format('Y-m-d');
                                break;

                            case 'int':
                                $sql .= '`'.$options['db_field_name'].'` = ?i, ';
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

                // ������ ������ � ���� �� �����, ������ ��� ����� NULL
                if ($value === null || $value === '')
                {
                    $sql .= '`'.$options['db_field_name'].'` = NULL, ';
                }
                else
                {
                    $sql .= '`'.$options['db_field_name'].'` = "?s", ';

                    $args[] = $value;
                }
            }
        }

        $sql = rtrim($sql, ', ');

        if ($object->id)
        {
            $args[] = $object->id;

            $sql = $sql.' WHERE id = ?i';

            array_unshift($args, $sql);

            call_user_func_array(array($this->db, 'query'), $args);
        }
        else
        {
            array_unshift($args, $sql);

            call_user_func_array(array($this->db, 'query'), $args);

            $object->id = $this->db->getInsertId();
        }

        return $object;
    }

    /**
     * ���������� _������_����������_ �� ��������.
     *
     * @access protected
     * @param array $params
     * @return mixed
     * @final
     */
    protected final function getResultForSqlDelete(array $params)
    {
        $params = self::makeSqlFromParams($params);

        $sql = 'DELETE FROM
                    `'.$this->db_table_name.'`'.
               $params['where'].
               $params['limit'];

        array_unshift($params['args'], $sql);

        return call_user_func_array(array($this->db, 'query'), $params['args']);
    }

	/**
	 * ����� ������������ SQL �������� �� ������� ���������� params.
	 * ������ ���������� ������������ ����� ������������� ������, ��� ����� ��������
	 * ��������� � ���������� SQL �������, � �������� - ������ ��� ����������� � SQL.
	 *
	 * 1. 'where'.
	 * 'where' ����� ���� �������� ����
	 * $params['where'] = array
	 * (
	 *     'id = ?' => array(23),
	 *     'id = ? AND foo = "?" AND foo2 LIKE "?!" ' => array(23, 'hellow', 'world')
	 * );
	 * ��� ������� ����
	 * $params['where'] = 'id = 5';
	 * 'where' ����� ���� �� ��������, ����� where-������� �� ������������.
	 *
	 * 2. 'what'
	 * 'what' ����� �������� ������� ����
	 * $params['what'] = 'name, value';
	 * 'what' ����� ���� �� ��������, ����� what-������� �� ��������� ���������� ��� *.
	 *
	 * 3. 'limit'
	 * 'limit' ����� �������� �������� ����
	 * $params['what'] = array('start' => int [, 'stop' => int]);
	 * 'limit' ����� ���� �� ��������, ����� limit-������� �� ������������.
	 *
	 * 4. 'order'
	 * 'order' ����� �������� �������� ����
	 * $params['order'] = array
	 * (
	 *     'col' => 'ASC|DESC' [, 'col2' => 'ASC|DESC']
	 * )
	 * ��� 'col' - �������, �� �������� ������������ ����������
	 *     'ASC|DESC' - ���� �� ���� ������� ����������
	 * 'order' ����� ���� �� ��������, ����� order-������� �� ������������.
	 *
	 * 5. 'group'
	 * 'group' ����� �������� �������� ����
	 * $params['group'] = array
	 * (
	 *     'col' => 'ASC|DESC' [, 'col2' => 'ASC|DESC']
	 * )
	 * ��� 'col' - �������, �� �������� ������������ �����������
	 *     'ASC|DESC' - ���� �� ���� ������� ����������
	 *
	 * @param array
	 * @return array
	 */
	protected static function makeSqlFromParams($params)
	{
	    // ��������� ��� ����������� � ������� SQL �������.
	    // ����������, ��� ����������� ������ SQL-�������.
	    $sql_store = array('args' => array(),
	                       'where' => '',
	                       'join' => '',
	                       'what' => ' * ',
	                       'limit' => '',
	                       'order' => '',
	                       'group' => ''
	                       );

	    // where-�������
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
	            // ����������, ��� �� ���� ������������ order-��������:
	            // ���� order ����� ��� ���� table.col, �� ����������� ���� �������� � `table`.`col`,
	            // ���� order ����� ��� ���� col, �� ����������� � ���� `col`.
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
	    // todo: ������� ����������� ������������ �� ����� ���� �������.���� ��� � order?
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
     * ���������� ������ ������ $model_name ������ $module_name
     * � ��������������� $id �� ��������� �������� $collection
     *
     * @access public
     * @param string $module_name ��� ������
     * @param string $model_name ��� ������
     * @param int id ID �������������� �������
     * @return object
     * @static
     * ����� ���?

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