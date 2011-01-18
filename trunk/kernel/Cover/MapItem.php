<?php
class Cover_MapItem extends Cover_Array
{
    /**
     * ���������� ������� �������� �������� ������ type
     *
     * @param void
     * @return string|null
     */
    public function getType()
    {
        if (!isset($this->data['type']))
        {
            trigger_error('������� �������� �������� ������ type �� ����������', E_USER_WARNING);

            return null;
        }

        return $this->data['type'];
    }

    /**
     * ���������� ������� �������� �������� ������ db_element
     *
     * @param void
     * @return string
     */
    public function getDbElement()
    {
        if (!isset($this->data['db_element']))
        {
            trigger_error('������� �������� �������� ������ db_element �� ����������', E_USER_WARNING);

            return null;
        }

        return $this->data['db_element'];
    }

    /**
     * ���������� ������� �������� �������� ������ db_field_name
     *
     * @param void
     * @return string
     */
    public function getFieldName()
    {
        if (!isset($this->data['db_field_name']))
        {
            trigger_error('������� �������� �������� ������ db_field_name �� ����������', E_USER_WARNING);

            return null;
        }

        return $this->data['db_field_name'];
    }

    /**
     * ���������� ������� �������� �������� ������ default_value
     *
     * @param void
     * @return mixed
     */
    public function getDefaultValue()
    {
        if (!isset($this->data['default_value']))
        {
            trigger_error('������� �������� �������� ������ default_value �� ����������', E_USER_WARNING);

            return null;
        }

        return $this->data['default_value'];
    }
}
?>