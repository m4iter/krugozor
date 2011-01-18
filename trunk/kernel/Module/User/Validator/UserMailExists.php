<?php
// ѕроверка на пользовател€ с таким мылом.
class Module_User_Validator_UserMailExists extends Validator_Abstract
{
    private $id_user;

    public function __construct($value, $id_user=null, $_break=TRUE, $ERROR_KEY='USER_MAIL_EXISTS')
    {
        parent::init($value, $_break, $ERROR_KEY);
        $this->id_user = $id_user;
    }

    public function validate()
    {
        $this->user_mapper = new Module_User_Mapper_User();

        $params = array
        (
            'where' => array('user_mail = "?s"' => array($this->value)),
            'what' => 'id',
        );

        if ($this->id_user !== null)
        {
            $params['where']['AND id <> ?i'] = array($this->id_user);
        }

        if ($this->user_mapper->findByParams($params)->id)
        {
            $this->error = array($this->ERROR_KEY, array('user_mail' => $this->value));

            return false;
        }

        return true;
    }
}
?>