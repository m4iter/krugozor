<?php
class Module_Common_Validator_VarEnum extends Validator_Abstract
{
    public function __construct($value, $enum=array(), $_break=TRUE, $ERROR_KEY='INCORRECT_VALUE')
    {
        parent::init($value, $_break, $ERROR_KEY);

        $this->setEnum($enum);
    }

    public function setEnum($enum)
    {
        $this->enum = (array)$enum;
    }

    public function validate()
    {
        if (Base_String::isEmpty($this->value)) {
            return true;
        }

        return in_array($this->value, $this->enum);
    }
}
?>