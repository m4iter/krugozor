<?php
class Module_Common_Validator_Email extends Validator_Abstract
{
    /**
     * @param string $value
     * @param boolean $_break
     * @param string $ERROR_KEY
     */
    public function __construct($value, $_break=TRUE, $ERROR_KEY='INVALID_STRING_EMAIL')
    {
        parent::init($value, $_break, $ERROR_KEY);
    }

    /**
     * @see Krugozor/Validator/Validator_Abstract#validate()
     */
    public function validate()
    {
        if (Base_String::isEmpty($this->value)) {
            return true;
        }

        return self::isCorrectEmail($this->value);
    }

    /**
     * Проверяет строку на соответствие email адресу
     * возвращает true в случае если ввод корректен,
     * false в противном случае.
     *
     * @param string
     * @return bool
     * @static
     */
    public static function isCorrectEmail($in)
    {
    	return preg_match("~^([a-z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,4}$~i", $in);
    }
}