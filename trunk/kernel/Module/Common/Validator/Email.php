<?php
class Module_Common_Validator_Email extends Validator_Abstract
{
    public function __construct($value, $_break=TRUE, $ERROR_KEY='INVALID_STRING_EMAIL')
    {
        parent::init($value, $_break, $ERROR_KEY);
    }

    public function validate()
    {
        if (Base_String::isEmpty($this->value)) {
            return true;
        }

        return self::is_correct_email($this->value);
    }

    /**
     * ѕровер€ет строку на соответствие email адресу
     * возвращает true в случае если ввод корректен,
     * false в противном случае.
     *
     * @access public
     * @param string провер€ема€ строка
     * @return bool
     * @static
     */
    public static function is_correct_email($in)
    {
    	return preg_match("~^([a-z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,4}$~i", $in);
    }
}
?>