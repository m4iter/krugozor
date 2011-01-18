<?php
class Module_Common_Validator_IntRange extends Validator_Abstract
{
    private $min;
    private $max;

    const ZERO = 0;
    const PHP_MAX_INT_32 = 2147483647;

    public function __construct($value, $min=null, $max=null, $_break=TRUE, $ERROR_KEY='INVALID_INT_RANGE')
    {
        parent::init($value, $_break, $ERROR_KEY);

        $this->setMin($min);
        $this->setMax($max);
    }

    public function setMin($min)
    {
        $this->min = $min;
    }

    public function setMax($max)
    {
        $this->max = $max;
    }

    public function validate()
    {
        if (Base_String::isEmpty($this->value)) {
            return true;
        }

        if ($this->min !== NULL && $this->max !== NULL)
        {
            if ($this->value < $this->min || $this->value > $this->max)
            {
                $this->error = array($this->ERROR_KEY, array('min' => $this->min, 'max' => $this->max));

                return false;
            }

            return true;
        }
    }
}
?>