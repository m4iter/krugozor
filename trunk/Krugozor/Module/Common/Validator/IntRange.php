<?php
class Module_Common_Validator_IntRange extends Validator_Abstract
{
    /**
     * Минимальная величина диапазона.
     *
     * @var int
     */
    private $min;

    /**
     * Максимальная величина диапазона.
     *
     * @var int
     */
    private $max;

    const ZERO = 0;
    const PHP_MAX_INT_32 = 2147483647;

    /**
     * @param int $value
     * @param int $min
     * @param int $max
     * @param boolean $_break
     * @param string $ERROR_KEY
     */
    public function __construct($value, $min=null, $max=null, $_break=TRUE, $ERROR_KEY='INVALID_INT_RANGE')
    {
        parent::init($value, $_break, $ERROR_KEY);

        $this->setMin($min);
        $this->setMax($max);
    }

    /**
     * @param int $min
     * @return Module_Common_Validator_IntRange
     */
    public function setMin($min)
    {
        $this->min = $min;

        return $this;
    }

    /**
     * @param int $max
     * @return Module_Common_Validator_IntRange
     */
    public function setMax($max)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * @see Krugozor/Validator/Validator_Abstract#validate()
     */
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