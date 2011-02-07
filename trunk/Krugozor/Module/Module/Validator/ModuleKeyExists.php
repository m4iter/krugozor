<?php
class Module_Module_Validator_ModuleKeyExists extends Validator_Abstract
{
    /**
     * @param Module_Module_Model_Module $value объект модуля
     * @param Mapper_Abstract $mapper
     * @param boolean $_break
     * @param string $ERROR_KEY
     */
    public function __construct(Module_Module_Model_Module $value, Mapper_Abstract $mapper, $_break=TRUE, $ERROR_KEY='MODULE_KEY_EXISTS')
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
            'where' => array('module_key = "?s"' => array($this->value->getKey())),
            'what' => 'id',
        );

        if ($this->value->getId() !== null)
        {
            $params['where']['AND id <> ?i'] = array($this->value->getId());
        }

        if ($this->mapper->findByParams($params)->getId())
        {
            $this->error = array($this->ERROR_KEY, array('module_key' => $this->value->getKey()));

            return false;
        }

        return true;
    }
}