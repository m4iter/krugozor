<?php
class Module_Module_Service_Navigation extends Module_Common_Service_Navigation
{
    protected $sort_vars = array
    (
        'field_name' => 'name',
        'sort_order' => 'DESC',
    );

    protected $sort_cols_values = array
    (
        'id' => 'module.id',
        'name' => 'module.module_name',
        'key' => 'module.module_key',
    );

    public function getList()
    {
        $params = array
        (
            'order' => array($this->getRealSortFieldName() => $this->getRealSortOrder()),
            //'limit' => array('start' => $this->navigation->getStartLimit(),
            //                 'stop'  => $this->navigation->getStopLimit()),
        );

        $list = $this->mapper->findModulesWithControllers($params);

        //$this->navigation->setCount($this->mapper->getFoundRows());

        return $list;
    }
}
?>