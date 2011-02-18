<?php
/**
 * ������ �������, ���������.
 * ������� ���������� ������� ��� ����� ������� ������ � �������-�������� ���������� ������
 * � ������ OO-��������������� PHP-����������.
 */
abstract class Cover_Abstract_Array extends Cover_Abstract_Simple
implements IteratorAggregate, Countable, ArrayAccess, Serializable
{
    public function __set($key, $value)
    {
        $this->data[$key] = is_array($value) ? new $this($value) : $value;
    }

    public function __toString()
    {
        return ''; // $this->data ? strval(current($this->data)) : '';
    }

    /**
     * ���������� ���������� Countable
     *
     * @access public
     * @param void
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * ���������� ���������� IteratorAggregate
     *
     * @access public
     * @param void
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }

    /**
     * ���������� ������� ��������� � �������� �������� � �������� ����������.
     * ������ parent::__get, �� ������������ ��� �������� ��������.
     *
     * @access public
     * @param mixed
     * @return mixed
     */
    public function item($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * ������������ ���� ������� � ������ �������.
     *
     * @access public
     * @param mixed
     * @return object
     */
    public function prepend($value)
    {
        array_unshift($this->data, self::array2cover($value));

        return $this;
    }

    /**
     * ������������ ���� ������� � ����� �������.
     *
     * @access public
     * @param mixed
     * @return object
     */
    public function append($value)
    {
        array_push($this->data, self::array2cover($value));

        return $this;
    }

    /**
     * ���������� ��������� ������� �������.
     *
     * @param void
     * @return mixed
     */
    public function getLast()
    {
        $last = end($this->data);
        reset($this->data);
        return $last;
    }

    /**
     * ���������� ������ ������� �������.
     *
     * @param void
     * @return mixed
     */
    public function getFirst()
    {
        $last = end($this->data);
        reset($this->data);
        return $last;
    }

    /**
     * ���������� ������ ������� ��� ������.
     *
     * @access public
     * @param void
     * @return array
     */
    public function getDataAsArray()
    {
        return self::object2array($this->data);
    }

    /**
     * ���������� ������ ���������� ArrayAccess.
     *
     * @param int|string|null $key ���� ��������
     * @param mixed $value �������� ��������
     * @return Cover_Array
     */
    public function offsetSet($key, $value)
    {
        // ��� ���������� ������ �������� ������� ���� $var[] = 'element';
        if ($key === null)
        {
            $u = &$this->data[];
        }
        else
        {
            $u = &$this->data[$key];
        }

        $u = self::array2cover($value);
    }

    /**
     * ���������� ������ ���������� ArrayAccess.
     *
     * @param int|string ���� ��������
     * @return boolean
     */
    public function offsetExists($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * ���������� ������ ���������� ArrayAccess.
     *
     * @param int|string ���� ��������
     * @return void
     */
    public function offsetUnset($key)
    {
        if (isset($this->data[$key]))
        {
            unset($this->data[$key]);
        }
    }

    /**
     * ���������� ������ ���������� ArrayAccess.
     * ���������� �� ��������� ArrayObject ���, ��� � ������ ����������
     * ����������� �������� �� ���������� ������, � ������� � ���������
     * ��� �������, � ���������, �������� $key ���������� ������ ������
     * �������� ������.
     *
     * @param int|string ���� ��������
     * @return mixed
     */
    public function offsetGet($key)
    {
        if (isset($this->data[$key]))
        {
            return $this->data[$key];
        }
        else
        {
            return $this->data[$key] = new Cover_Array();
        }
    }

    /**
     * ���������� ������ ���������� Serializable.
     *
     * @return array
     */
    public function serialize()
    {
        return serialize($this->data);
    }

    /**
     * ���������� ������ ���������� Serializable.
     *
     * @param array $data
     * @return Cover_Array
     */
    public function unserialize($data)
    {
        $this->setData(unserialize($data));

        return $this;
    }

    /**
     * ����������� ��� �������� ������� $in � �������, ���� ��������
     * �����-���� ��������� ������ ����� ������� ���� Cover_Array.
     *
     * @access protected
     * @param array
     * @return array
     */
    protected static function object2array(array $in)
    {
        foreach ($in as $key => $value)
        {
            $in[$key] = (is_object($value) && $value instanceof self)
                        ? $in[$key] = self::object2array($value->getData())
                        : $value;
        }

        return $in;
    }

    /**
     * ���������� ������ Cover_Array, ���� ����������
     * � ����� ��������� �������� ������.
     *
     * @access protected
     * @param mixed
     * @return mixed
     */
    protected static function array2cover($value)
    {
        return is_array($value) ? new Cover_Array($value) : $value;
    }
}