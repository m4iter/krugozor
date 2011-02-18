<?php
class Module_User_Validator_UserPasswordsCompare extends Validator_Abstract
{
    /**
     * Проверяемый пароль № 1
     *
     * @var string
     */
    private $password_1;

    /**
     * Проверяемый пароль № 2
     *
     * @var string
     */
    private $password_2;

    /**
     * @param string $password_1 Строка пароля №1
     * @param string $password_2 Строка пароля №2
     * @param bool $_break
     * @param string $ERROR_KEY
     */
    public function __construct($password_1, $password_2, $_break=TRUE, $ERROR_KEY='INCORRECT_PASSWORDS')
    {
        parent::init(NULL, $_break, $ERROR_KEY);

        $this->password_1 = (string)$password_1;
        $this->password_2 = (string)$password_2;
    }

    /**
     * @see Krugozor/Validator/Validator_Abstract#validate()
     */
    public function validate()
    {
        return $this->password_1 === $this->password_2;
    }
}