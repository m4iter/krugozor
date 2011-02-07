<?php
class Cover_MapItem extends Cover_Array
{
    /**
     * ¬озвращает элемент описани€ свойства модели type
     *
     * @param void
     * @return string|null
     */
    public function getType()
    {
        if (!isset($this->data['type']))
        {
            trigger_error('Ёлемент описани€ свойства модели type не существует', E_USER_WARNING);

            return null;
        }

        return $this->data['type'];
    }

    /**
     * ¬озвращает элемент описани€ свойства модели db_element
     *
     * @param void
     * @return string
     */
    public function getDbElement()
    {
        if (!isset($this->data['db_element']))
        {
            trigger_error('Ёлемент описани€ свойства модели db_element не существует', E_USER_WARNING);

            return null;
        }

        return $this->data['db_element'];
    }

    /**
     * ¬озвращает элемент описани€ свойства модели db_field_name
     *
     * @param void
     * @return string
     */
    public function getFieldName()
    {
        if (!isset($this->data['db_field_name']))
        {
            trigger_error('Ёлемент описани€ свойства модели db_field_name не существует', E_USER_WARNING);

            return null;
        }

        return $this->data['db_field_name'];
    }

    /**
     * ¬озвращает элемент описани€ свойства модели default_value
     *
     * @param void
     * @return mixed
     */
    public function getDefaultValue()
    {
        if (!isset($this->data['default_value']))
        {
            trigger_error('Ёлемент описани€ свойства модели default_value не существует', E_USER_WARNING);

            return null;
        }

        return $this->data['default_value'];
    }
}
?>