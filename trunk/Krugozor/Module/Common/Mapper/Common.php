<?php
/**
 * ��������� ������ Mapper_Abstract ������� � ������� ������
 * �������� ������������ ��� ������� �������.
 */
class Module_Common_Mapper_Common extends Mapper_Abstract
{
    /**
     * @see parent::createModelObject()
     * @param void
     * @return Base_Model
     */
    public function createNew()
    {
        return parent::createModel();
    }

    /**
     * ������� ������ ������ �� ������ ������ $data, �������
     * �������� �������� ���� Cover_Array.
     *
     * @param object Cover_Array
     * @param array $mustbe ������ ��� �������, ������� ������ ����������� � ����������� �������
     *              �, �� ����, ������ �������������� � $data. �.�. ��� ��������� ������� ������
     *              �� POST-�������. ���� � $data ��� �������� ����� ������������� (������� ������),
     *              �� ��������, ������������� � $mustbe ������ ���� ��������� ������� �������� ���������
     *              �� ���������, ����������� � Base_Model::$model_attributes
     *              ������ ����������: $mustbe �� ���������� ������������ �������, �.�. ���� ��
     *              ������ �� POST-������� array('name' => '...', 'age' => '...') �������� � ������
     *              ������ �������� name, �� ��� ��� ������ ������ $mustbe ��� array('name') -
     *              � ������ ����� ��-������ �������� �������� age, �.�. ��� �������� ������
     *              ������ � ����������� � Base_Model::$model_attributes.
     *              ��� ���������� ������������ ������������� ������� ����������
     *              ���� ������� �� �� ������� $data.
     * @return object
     */
    public function createFromCover(Cover_Array $data, array $mustbe=array())
    {
        $object = new $this->model_class_name();
        $object->setMapperManager($this->manager);

        foreach ($data as $key => $value)
        {
            if ($key = $object->getMethodNameByKeyWithPrefix($key, 'set'))
            {
                $object->$key($value);
            }
        }

        if ($mustbe)
        {
	        // ����� ��������� ��������, �� �� ��������� �� �����-���� �������� �� �������
	        $mustbe = array_diff($mustbe, array_keys($data->getData()));

	        foreach ($mustbe as $key)
	        {
	            if ($method_name = $object->getMethodNameByKeyWithPrefix($key, 'set'))
	            {
	                $params = $object->getMapItem($key);

	                $value = isset($params['default_value'])
	                         ? $params['default_value']
	                         : null;

	                $object->$method_name($value);
	            }
	        }
        }

        return $object;
    }

    /**
     * @see parent::findModelByParams()
     * @param array
     * @return object
     */
    public function findByParams(array $params=array())
    {
        return parent::findModelByParams($params);
    }

    /**
     * ������� ������ �� ��������� ��������� $objId.
     * $objId ����� ���� ���� �������� �� ��������� id,
     * ���� �������� ���������.
     * ����� ���, ��� ��������� SQL-������ � ����,
     * ����� ������� ���� ������ � ������ ID �
     * ��������� �������� self::$collection.
     *
     * @param object|int
     * @return object
     */
    public function findById($objId)
    {
        $id = is_object($objId) ? $objId->getId() : $objId;

        if (!$id)
        {
            return $this->createNew();
        }

        if (!isset(self::$collection[$this->getModuleName()][$this->getModelName()][$id]))
        {
            $object = parent::findModelByParams
            (
                array('where' => array('id = ?i' => array( $id )))
            );

            if ($object->getId())
            {
                return self::$collection[$this->getModuleName()][$this->getModelName()][$id] = $object;
            }
            else
            {
                return $object;
            }
        }

        return self::$collection[$this->getModuleName()][$this->getModelName()][$id];
    }

    /**
     * ����� ������� ������ �� ��������� SQL �������.
     *
     * @param $sql SQL ������
     * @return object

    public function findBySql($sql='')
    {
        $res = $this->db->query($sql);

        if (is_object($res) && $res->getNumRows())
        {
            $object = parent::createModelFromArray($res->fetch_assoc());

            self::$collection[$this->getModuleName()][$this->getModelName()][$object->getId()] = $object;

            return $object;
        }

        return $this->createNew();
    }*/

    /**
     * @see parent::findModelList()
     * @param array ��������� �������
     * @return Cover_Array
     */
    public function findList(array $params=array())
    {
        return parent::findModelList($params);
    }

    /**
     * �������� �������� FOUND_ROWS()
     *
     * @param void
     * @return int
     * @todo: ��� ���� ��� ����� �����?
     */
    public function getFoundRows()
    {
        return $this->db->query('SELECT FOUND_ROWS()')->getOne();
    }

    /**
     * ��������� ������ � ��.
     *
     * @param object
     * @return void
     */
    public function save(Base_Model $object)
    {
        parent::saveModel($object);
    }

    /**
     * ������� ������(�) �� ������� �������� ������� ���������� $params.
     *
     * @param array
     * @return int ���������� ��������� �����
     */
    public function deleteByParams(array $params=array())
    {
        parent::createQueryDelete($params);

        return $this->db->getAffectedRows();
    }

    /**
     * ������� ���� ������ �� ������� �������� $objId.
     * $objId ����� ���� ���� �������� �� ��������� ID,
     * ���� �������� ���������.
     *
     * @param object|int
     * @return int ���������� ��������� �����
     */
    public function deleteById($objId)
    {
        $params = array('where' => array('id = ?i' => array( is_object($objId) ? $objId->id : $objId) ),
                        'limit' => array('start' => 1));

        parent::createQueryDelete($params);

        return $this->db->getAffectedRows();
    }

    /***
     * ������ ��� ������ � "�����" ������ � ������ �����.
     *
     * ��������: ����� ���������� ������ ������� �������� ���� id (autoincrement)
     * ����������� ������ � ����������� � ���� order_id.
     * ��� ������� ������ "�����" �� ������� ������ -
     * 1. ���� ������������ ���������� �������� order_id �� ������ �������� (�������� ������)
     * 2. ����� order_id �������� ������ �� ��������� (0)
     * 3. ����� order_id ������� ������ �� order_id �������� ������
     * 4. ����� order_id �������� ������ �� order_id ������� ������
     */

    /**
     * ��������� ������ � �������� �� ���� ������� ����.
     * ��������� �����, �����, ���� ��������� � ���,
     * ��� � ������� ���� ���� `order` ��������������� ��� ����������.
     *
     * @param Base_Model
     * @return void
     */
    public function motionUp(Base_Model $object, array $category=array())
    {
        $sql_category = '';

        if ($category)
        {
            list($field, $value) = $category;
            $sql_category = ' AND `' . $field.'` = ' . $value;
        }

        $res = $this->db->query('SELECT
                                    `id`,
                                    `order`
                                FROM
                                    `' . $this->db_table_name . '`
                                WHERE
                                    `order` >
                                    (
                                        SELECT
                                            `order`
                                        FROM
                                            `' . $this->db_table_name . '`
                                        WHERE
                                            `id` = ?i
                                        ' . $sql_category . '
                                    )
                                ' . $sql_category . '
                                ORDER BY `order` ASC
                                LIMIT 0, 1', $object->getId());

        list($down_id, $new_order) = $res->fetch_row();

        if ($down_id && $new_order)
        {
            $res = $this->db->query('SELECT
                                        `order`
                                    FROM
                                        `' . $this->db_table_name . '`
                                    WHERE
                                        `id` = ?i ' . $sql_category, $object->getId());

            $down_order = $res->getOne();

            $this->db->query('UPDATE
                                 `' . $this->db_table_name . '`
                             SET
                                 `order` = ?i
                             WHERE
                                 `id` = ?i' . $sql_category, $down_order, $down_id);

            $this->db->query('UPDATE
                                 `' . $this->db_table_name . '`
                             SET
                                 `order` = ?i
                             WHERE
                                 `id` = ?i' . $sql_category, $new_order, $object->getId());
        }
    }

    /**
     * �������� ������ � �������� �� ���� ������� ����.
     * ��������� �����, �����, ���� ��������� � ���,
     * ��� � ������� ���� ���� `order` ��������������� ��� ����������.
     *
     * @param Base_Model
     * @return void
     */
    public function motionDown(Base_Model $object, array $category=array())
    {
        $sql_category = '';

        if ($category)
        {
            list($field, $value) = $category;
            $sql_category = ' AND `' . $field.'` = ' . $value;
        }

        $res = $this->db->query('SELECT
                                    `id`,
                                    `order`
                                FROM
                                    `' . $this->db_table_name . '`
                                WHERE
                                    `order` <
                                    (
                                        SELECT
                                            `order`
                                        FROM
                                            `' . $this->db_table_name . '`
                                        WHERE
                                            `id` = ?i
                                       )
                                ' . $sql_category . '
                                ORDER BY
                                    `order` DESC
                                LIMIT 0, 1', $object->getId());

        list($up_id, $new_order) = $res->fetch_row();

        if ($up_id && $new_order)
        {
            $res = $this->db->query('SELECT
                                        `order`
                                    FROM
                                        `' . $this->db_table_name . '`
                                    WHERE
                                        `id` = ?i' . $sql_category, $object->getId());

            $up_order = $res->getOne();

            $this->db->query('UPDATE
                                 `' . $this->db_table_name . '`
                             SET
                                 `order` = ?i
                             WHERE
                                 `id` = ?i' . $sql_category, $up_order, $up_id);

            $this->db->query('UPDATE
                                 `' . $this->db_table_name . '`
                             SET
                                 `order` = ?i
                             WHERE
                                 `id` = ?i' . $sql_category, $new_order, $object->getId());
        }
    }

    /**
     * ��������� ���� `order` ������� $this->db_table_name
     * �� ID ������ ��� ����������� ������.
     * ���������� ����� ����� ������ save.
     *
     * @param Base_Model
     * @return Base_Model
     */
    protected function updateOrderField(Base_Model $object)
    {
        $fields_db = $this->db->getListFields($this->db_table_name);

        if (!empty($fields_db['order']))
        {
            $this->db->query('UPDATE
                                  `' . $this->db_table_name . '`
                              SET
                                  `order` = ?i
                              WHERE
                                  `id` = ?i', $object->getId(), $object->getId());
        }

        return $object;
    }

    protected function init(){}
}