<?php
/**
 * @todo: переименовать класс
 */
class Module_Common_Validator_DatetimeCorrect extends Validator_Abstract
{
    /**
     * @param string $value
     * @param boolean $_break
     * @param string $ERROR_KEY
     */
    public function __construct($value, $_break=TRUE, $ERROR_KEY='INVALID_DATETIME')
    {
        parent::init($value, $_break, $ERROR_KEY);
    }

    /**
     * Возвращает true, если дата в виде строки $value соответствует шаблону
     * ГГГГ-ММ-ДД ЧЧ:MM:CC, false в обратном случае.
     *
     * @see Krugozor/Validator/Validator_Abstract#validate()
     */
    public function validate()
    {
        if (preg_match("/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/", $this->value, $matches))
        {
            return checkdate($matches[2], $matches[3], $matches[1]);
        }

        return false;
    }
}