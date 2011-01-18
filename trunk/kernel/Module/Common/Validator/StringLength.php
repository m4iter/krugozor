<?php
class Module_Common_Validator_StringLength extends Validator_Abstract
{
    /**
     * Минимальная длинна строки.
     *
     * @var int
     */
    private $start;

    /**
     * Максимальная длинна строки.
     *
     * @var int
     */
    private $stop;

    const VARCHAR_MAX_LENGTH = 255;

    public function __construct($value, $start=0, $stop=255, $_break=TRUE, $ERROR_KEY='INVALID_STRING_LENGTH')
    {
        parent::init((string)$value, $_break, $ERROR_KEY);

        $this->setStart($start);
        $this->setStop($stop);
    }

    public function setStart($start)
    {
        $this->start = $start;
    }

    public function setStop($stop)
    {
        $this->stop = $stop;
    }

    public function validate()
    {
        if (Base_String::isEmpty($this->value)) {
            return true;
        }

        $len = strlen($this->value);

        if(!($len >= $this->start && $len <= $this->stop))
        {
            $this->error = array($this->ERROR_KEY, array('start' => $this->start, 'stop' => $this->stop));

            return false;
        }

        return true;
    }
}
?>