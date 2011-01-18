<?php
class Module_Common_Validator_EmptyNull extends Validator_Abstract
{
    public function __construct($value, $_break=TRUE, $ERROR_KEY='EMPTY_VALUE')
    {
        parent::init($value, $_break, $ERROR_KEY);
    }

    public function validate()
    {
        return !Base_String::isEmpty($this->value);
    }
}
?>