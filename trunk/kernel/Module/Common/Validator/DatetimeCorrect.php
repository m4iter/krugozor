<?php
class Module_Common_Validator_DatetimeCorrect extends Validator_Abstract
{
    public function __construct($value, $_break=TRUE, $ERROR_KEY='INVALID_DATETIME')
    {
        parent::init($value, $_break, $ERROR_KEY);
    }

    public function validate()
    {
        if (preg_match("/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/", $this->value, $matches))
        {
            if (checkdate($matches[2], $matches[3], $matches[1])) {
                return true;
            }
        }

        return false;
    }
}
?>