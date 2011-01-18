<?php
class Module_Common_Validator_Captcha extends Validator_Abstract
{
    private $request_value;
    private $session_value;

    public function __construct($request_value, $session_value, $_break=TRUE, $ERROR_KEY='BAD_CAPTCHA')
    {
        parent::init(null, $_break, $ERROR_KEY);

        $this->request_value = (string)$request_value;
        $this->session_value = (string)$session_value;
    }

    public function validate()
    {
        return (!empty($this->request_value) &&
                !empty($this->session_value) &&
                $this->session_value === $this->request_value);
    }
}
?>