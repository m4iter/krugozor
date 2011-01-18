<?php
class Module_User_Validator_UserIdExists extends Validator_Abstract
{
    private $user_mapper;

    public function __construct($value, $_break=TRUE, $ERROR_KEY='USER_WITH_ID_NOT_EXISTS')
    {
        parent::init($value, $_break, $ERROR_KEY);
    }

    public function validate()
    {
        $this->user_mapper = new Module_User_Mapper_User();

        $params = array
        (
            'where' => array('id = ?i' => array($this->value)),
            'what' => 'id',
        );

        if (!$this->user_mapper->findByParams($params)->id)
        {
            $this->error = array($this->ERROR_KEY, array('id' => $this->value));

            return false;
        }

        return true;
    }
}
?>