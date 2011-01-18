<?php
class Module_Common_Validator_Numeric extends Validator_Abstract
{
    public function __construct($value, $_break=TRUE, $ERROR_KEY='INVALID_NUMERIC')
    {
        parent::init($value, $_break, $ERROR_KEY);
    }

    public function validate()
    {
        if (Base_String::isEmpty($this->value)) {
            return true;
        }

        return is_numeric($this->value);
    }
}
?>