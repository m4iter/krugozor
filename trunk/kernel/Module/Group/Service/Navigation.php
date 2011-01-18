<?php
class Module_Group_Service_Navigation extends Module_Common_Service_Navigation
{
    protected $sort_vars = array
    (
        'field_name' => 'id',
        'sort_order' => 'DESC',
    );

    protected $sort_cols_values = array
    (
        'id' => 'id',
        'name' => 'group_name',
        'active' => 'group_active',
    );

    public function getList()
    {
        $params = array
        (
            'order' => array($this->getRealSortFieldName() => $this->getRealSortOrder()),
            'limit' => array('start' => $this->navigation->getStartLimit(),
                             'stop'  => $this->navigation->getStopLimit()),
        );

        $list = $this->mapper->findList($params);

        $this->navigation->setCount($this->mapper->getFoundRows());

        return $list;
    }
}
?>