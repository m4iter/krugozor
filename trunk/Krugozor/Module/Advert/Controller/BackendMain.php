<?php
class Module_Advert_Controller_BackendMain extends Module_Advert_Controller_BackendCommon
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            return $this->createNotification()
                        ->setMessage('forbidden_access')
                        ->setType('alert')
                        ->setRedirectUrl('/admin/')
                        ->run();
        }

        $this->init();

        $sort_cols_values = array
        (
            'id' => 'advert.id',
            'header' => 'advert_header',
            'category' => 'category.category_name',
            'active' => 'advert_active',
            'user_name' => 'user.user_first_name',
            'view_count' => 'advert_view_count',
        );

        $real_order_field_name = ($this->getRequest()->getRequest('field_name') &&
                                  isset($sort_cols_values[$this->getRequest()->getRequest('field_name')]))
                                 ? $sort_cols_values[$this->getRequest()->getRequest('field_name')]
                                 : $sort_cols_values['id'];

        $real_order_type = ($this->getRequest()->getRequest('sort_order') &&
                            in_array($this->getRequest()->getRequest('sort_order'), array('ASC', 'DESC')))
                           ? $this->getRequest()->getRequest('sort_order')
                           : 'DESC';

        $sql_where_string = $sql_where_args = array();

        if ($this->getRequest()->getRequest('id_category') &&
            Base_Numeric::is_decimal($this->getRequest()->getRequest('id_category')))
        {
            $sql_where_string[] = '`advert`.`advert_category` = ?i';
            $sql_where_args[] = $this->getRequest()->getRequest('id_category');
        }

        if ($this->getRequest()->getRequest('id_user') &&
            Base_Numeric::is_decimal($this->getRequest()->getRequest('id_user')))
        {
            $sql_where_string[] = '`advert`.`advert_id_user` = ?i';
            $sql_where_args[] = $this->getRequest()->getRequest('id_user');
        }

        $navigation = new Base_Navigation(10, 100);
        $start_limit = $navigation->getStartLimit();
        $stop_limit = $navigation->getStopLimit();

        $params = array
        (
            'where' => ($sql_where_string && $sql_where_args)
                       ? array(implode(' AND ', $sql_where_string) => $sql_where_args)
                       : '',
            'order' => array($real_order_field_name => $real_order_type),
            'limit' => array('start' => $start_limit, 'stop' => $stop_limit),
        );

        $this->getView()->adverts = $this->getMapper('Advert/Advert')->findListForBackend($params);

        $navigation->setCount($this->getMapper('Advert/Advert')->getFoundRows());

        $this->getView()->navigation = $navigation;

        $this->getView()->field_name = $this->getRequest()->getRequest('field_name') ?: 'id';
        $this->getView()->sort_order = $real_order_type;
        $this->getView()->id_category = $this->getRequest()->getRequest('id_category');
        $this->getView()->id_user = $this->getRequest()->getRequest('id_user');

        return $this->getView();
    }
}