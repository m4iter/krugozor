<?php
class Module_User_Service_Navigation extends Module_Common_Service_Navigation
{
    protected $sort_vars = array
    (
        'field_name' => 'id',
        'sort_order' => 'DESC',
    );

    protected $sort_cols_values = array
    (
        'id' => 'user.id',
        'first_name' => 'user.user_first_name',
        'ip' => 'user.user_ip',
    );

    public function getList()
    {/*
        // Поля текстовые, по которым проходит LIKE-поиск.
        // Данные значения подставляются в SQL-запрос на выборку.
        $text_search_cols = array
        (
            'id',
            'user_first_name',
            'user_last_name',
            'user_mail',
            'user_url',
            'user_icq',
            'user_ip',
        );

        $sql_where_string = array();
        $sql_where_args = array();

        // Выборка по городам
        $territory = array
        (
            'city' => array('user_city', 'id'),
            'region' => array('user_region', 'id'),
            'country' => array('user_country', 'id')
        );

        $sql_join_string = array();

        foreach ($territory as $table => $fields)
        {
            $this->getView()->$fields[1] = 0;

            if ($id = $this->getRequest()->getRequest($fields[0]))
            {
                $this->getView()->$fields[1] = $id;
                $sql_where_string[] = 'user.'.$fields[0].' = ?i';
                $sql_where_args[] = $id;
            }
        }

        if (($user_active = $this->getRequest()->getRequest('user_active', 'string')) !== '')
        {
            $sql_where_string[] = 'user_active = ?i';
            $sql_where_args[] = $user_active;
        }

        if ($this->getRequest()->getRequest('search') != '')
        {
            $this->getRequest()->getRequest()->search = urldecode($this->getRequest()->getRequest()->item('search'));

            if ($this->getRequest()->getRequest()->item('col') != 'id_user')
            {
                if ($this->getRequest()->getRequest()->item('col') == 'all')
                {
                    $sql_where_string[] = 'CONCAT_WS(",", '.
                                          implode(', ', $text_search_cols).
                                          ') LIKE "%?S%"';
                    $sql_where_args[] = $this->getRequest()->getRequest()->item('search');
                }
                else
                {
                    $sql_where_string[] = $this->getRequest()->getRequest()->item('col').' LIKE "%?S%"';
                    $sql_where_args[] = $this->getRequest()->getRequest()->item('search');
                }
            }
            else if ($this->getRequest()->getRequest()->item('col')=='id_user'
                     && $id_user = $this->getRequest()->getRequest('search'))
            {
                $sql_where_string[] = '`user`.`id` = ?i';
                $sql_where_args[] = $id_user;
            }
        }*/

        $params = array
        (
            //'where' => ($sql_where_string && $sql_where_args) ?
            //            array(implode(' AND ', $sql_where_string) => $sql_where_args)
            //            : '',
            'order' => array($this->getRealSortFieldName() => $this->getRealSortOrder()),
            'limit' => array('start' => $this->navigation->getStartLimit(),
                             'stop'  => $this->navigation->getStopLimit()),
        );

        $list = $this->mapper->getUsersListWithResidence($params);

        $this->navigation->setCount($this->mapper->getFoundRows());

        return $list;
    }
}