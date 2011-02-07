<?php
class Module_User_Validator_UserIdExists extends Validator_Abstract
{
    /**
     * @param int $value ID пользовател€
     * @param Mapper_Abstract $mapper
     * @param bool $_break
     * @param string $ERROR_KEY
     * @todo: может сюда передавать именно объект пользовател€, а не только его int ID?
     */
    public function __construct($value, Mapper_Abstract $mapper, $_break=TRUE, $ERROR_KEY='USER_WITH_ID_NOT_EXISTS')
    {
        parent::init($value, $_break, $ERROR_KEY);

        $this->mapper = $mapper;
    }

    /**
     * ¬озвращает false (факт ошибки), если пользователь с указанным ID не найден.
     *
     * @see Krugozor/Validator/Validator_Abstract#validate()
     */
    public function validate()
    {
        $params = array
        (
            'where' => array('id = ?i' => array($this->value)),
            'what' => 'id',
        );

        if (!$this->mapper->findByParams($params)->getId())
        {
            $this->error = array($this->ERROR_KEY, array('id' => $this->value));

            return false;
        }

        return true;
    }
}