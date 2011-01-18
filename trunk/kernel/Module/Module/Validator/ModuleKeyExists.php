<?php
class Module_Module_Validator_ModuleKeyExists extends Validator_Abstract
{
    /**
     * Идентификатор проверяемого модуля
     *
     * @var int
     */
    private $id_module;

    public function __construct($value, $id_module=null, $_break=TRUE, $ERROR_KEY='MODULE_KEY_EXISTS')
    {
        parent::init($value, $_break, $ERROR_KEY);

        $this->id_module = $id_module;
    }

    public function validate()
    {
        $Module_Module_Mapper_Module = new Module_Module_Mapper_Module();

        $params = array
        (
            'where' => array('module_key = "?s"' => array($this->value)),
            'what' => 'id',
        );

        if ($this->id_module !== null)
        {
            $params['where']['AND id <> ?i'] = array($this->id_module);
        }

        if ($Module_Module_Mapper_Module->findByParams($params)->id)
        {
            $this->error = array($this->ERROR_KEY, array('module_key' => $this->value));

            return false;
        }

        return true;
    }
}
?>