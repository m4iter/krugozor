<?php
class Module_User_Validator_UserLoginExists extends Validator_Abstract
{
    /**
     * @param Module_User_Model_User $value объект пользователя
     * @param Mapper_Abstract $mapper
     * @param bool $_break
     * @param string $ERROR_KEY
     */
    public function __construct($value, Mapper_Abstract $mapper, $_break=TRUE, $ERROR_KEY='USER_LOGIN_EXISTS')
    {
        parent::init($value, $_break, $ERROR_KEY);

        $this->mapper = $mapper;
    }

    /**
     * @see Krugozor/Validator/Validator_Abstract#validate()
     */
    public function validate()
    {
        $params = array
        (
            'where' => array('user_login = "?s"' => array($this->value->getLogin())),
            'what' => 'id',
        );

        if ($this->value->getId() !== null)
        {
            $params['where']['AND id <> ?i'] = array($this->value->getId());
        }

        if ($this->mapper->findByParams($params)->getId())
        {
            $this->error = array($this->ERROR_KEY, array('user_login' => $this->value->getLogin()));

            return false;
        }

        return true;
    }
}